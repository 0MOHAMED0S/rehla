<?php

namespace App\Http\Controllers\Admin\ContentCreator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Articles\StoreArticleRequest;
use App\Http\Requests\Articles\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $articles = Article::latest()
                ->paginate(10, [
                    'id',
                    'title',
                    'slug',
                    'text',
                    'image',
                    'author_name',
                    'status',
                    'created_at'
                ]);

            return response()->json([
                'status'   => true,
                'message'  => 'تم جلب المقالات بنجاح',
                'articles' => $articles->through(function ($article) {
                    return [
                        'id'          => $article->id,
                        'title'       => $article->title,
                        'slug'        => $article->slug,
                        'text'        => $article->text,
                        'image'       => $article->image,
                        'status'      => $article->status,
                        'created_at'  => $article->created_at->toDateTimeString(),
                        'author_name' => $article->author_name,
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ ما: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('articles', 'public');
                $data['image'] = $path;
            }

            $data['user_id'] = Auth::id();
            if ($request->filled('slug')) {
                $slug = Str::slug($request->input('slug'));
            } else {
                $slug = Str::slug($request->input('title'));
            }

            $originalSlug = $slug;
            $counter = 1;
            while (Article::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;

            $data['author_name'] = $request->input('author_name');
            $data['status'] = $request->input('status', true);
            $article = Article::create($data);

            return response()->json([
                'status'  => true,
                'message' => 'تم إنشاء المقال بنجاح',
                'article' => [
                    'id'          => $article->id,
                    'title'       => $article->title,
                    'slug'        => $article->slug,
                    'text'        => $article->text,
                    'image'       => $article->image,
                    'status'      => $article->status,
                    'author_name' => $article->author_name,
                    'created_at'  => $article->created_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في إنشاء المقال',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


public function show(Article $article): JsonResponse
{
    try {
        return response()->json([
            'status'  => true,
            'message' => 'تم جلب المقال بنجاح',
            'article' => [
                'id'          => $article->id,
                'title'       => $article->title,
                'slug'        => $article->slug,
                'text'        => $article->text,
                'image'       => $article->image,
                'status'      => $article->status,
                'author_name' => $article->author_name,
                'created_at'  => $article->created_at,
            ],
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'حدث خطأ ما: ' . $e->getMessage(),
        ], 500);
    }
}



    public function update(UpdateArticleRequest $request, $id): JsonResponse
    {
        try {
            $article = Article::find($id);

            if (! $article) {
                return response()->json([
                    'status'  => 404,
                    'message' => 'المقال غير موجود'
                ], 404);
            }

            $data = [];

            if ($request->hasFile('image')) {
                if (!empty($article->image) && Storage::disk('public')->exists($article->image)) {
                    Storage::disk('public')->delete($article->image);
                }
                $data['image'] = $request->file('image')->store('articles', 'public');
            } else {
                $data['image'] = $article->image;
            }

            if ($request->filled('title')) {
                $data['title'] = $request->title;
            }

            if ($request->filled('slug')) {
                $slug = Str::slug($request->slug);
            } elseif ($request->filled('title')) {
                $slug = Str::slug($request->title);
            } else {
                $slug = $article->slug;
            }

            if ($slug !== $article->slug) {
                $originalSlug = $slug;
                $counter = 1;
                while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter++;
                }
            }
            $data['slug'] = $slug;

            if ($request->filled('text')) {
                $data['text'] = $request->text;
            }
            if ($request->filled('author_name')) {
                $data['author_name'] = $request->author_name;
            }
            if ($request->filled('status')) {
                $data['status'] = $request->status;
            }

            $article->update($data);

            return response()->json([
                'status'  => 200,
                'message' => 'تم تحديث المقال بنجاح',
                'article' => $article
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'فشل في تحديث المقال',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    public function destroy($id): JsonResponse
    {
        try {
            $article = Article::find($id);

            if (!$article) {
                return response()->json([
                    'status'  => false,
                    'message' => 'المقال غير موجود'
                ], 404);
            }

            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }

            $article->delete();

            return response()->json([
                'status'  => true,
                'message' => 'تم حذف المقال بنجاح'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في حذف المقال',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
