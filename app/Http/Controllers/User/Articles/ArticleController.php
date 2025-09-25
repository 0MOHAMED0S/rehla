<?php

namespace App\Http\Controllers\User\Articles;

use App\Http\Controllers\Controller;
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
            $articles = Article::where('status', 1)
                ->latest()
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
    public function show($slug): JsonResponse
    {
        try {
            $article = Article::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (! $article) {
                return response()->json([
                    'status'  => false,
                    'message' => 'المقال غير موجود أو غير منشور',
                ], 404);
            }

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
                'message' => 'حدث خطأ ما أثناء جلب المقال',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
