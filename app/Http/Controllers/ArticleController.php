<?php

namespace App\Http\Controllers;

use App\Http\Requests\Articles\ArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $articles = Article::with(['user:id,name'])
                ->latest()
                ->paginate(10, [ // 10 per page
                    'id',
                    'title',
                    'description',
                    'text',
                    'image',
                    'user_id',
                    'created_at'
                ]);

            return response()->json([
                'status'   => true,
                'message'  => 'Articles fetched successfully',
                'articles' => $articles->through(function ($article) {
                    return [
                        'id'          => $article->id,
                        'title'       => $article->title,
                        'description' => $article->description,
                        'text'        => $article->text,
                        'image'       => $article->image,
                        'created_at'  => $article->created_at,
                        'author_name' => $article->user->name,
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(ArticleRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('articles', 'public');
                $data['image'] = $path;
            }

            $data['user_id'] = Auth::id();

            $article = Article::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Article created successfully',
                'article' => $article->load('user')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create article',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show(Article $article): JsonResponse
    {
        try {
            $article->load(['user:id,name']);

            return response()->json([
                'status'  => true,
                'message' => 'Article fetched successfully',
                'article' => [
                    'id'          => $article->id,
                    'title'       => $article->title,
                    'description' => $article->description,
                    'text'        => $article->text,
                    'image'       => $article->image,
                    'created_at'  => $article->created_at,
                    'author_name' => $article->user->name,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function update(ArticleRequest $request, Article $article): JsonResponse
    {
        try {
            if ($article->user_id !== Auth::id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('articles', 'public');
                $data['image'] = $path;
            }

            $article->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Article updated successfully',
                'article' => $article->load('user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update article',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Article $article): JsonResponse
    {
        try {
            if ($article->user_id !== Auth::id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $article->delete();
            return response()->json([
                'status' => true,
                'message' => 'Article deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete article',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
