<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{
    /**
     * List artikel dengan filter
     */
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('mood_tag')) {
            $query->where('mood_tag', $request->mood_tag);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Ambil category yang tersedia
        $categories = Article::distinct()->pluck('category');
        $moodTags = Article::distinct()->pluck('mood_tag');

        $articles = $query->latest()->paginate(10);

        return $this->successResponse([
            'available_categories' => $categories,
            'available_mood_tags' => $moodTags,
            'articles' => $articles
        ], 'Daftar artikel');
    }

    /**
     * Detail artikel
     */
   public function show($id)
{
    $article = Article::where('article_id', $id)->first();

    if (!$article) {
        return $this->notFoundResponse('Artikel tidak ditemukan');
    }

    $related = Article::where('mood_tag', $article->mood_tag)
                ->where('article_id', '!=', $id)
                ->take(3)
                ->get();

    return $this->successResponse([
        'article' => $article,
        'related_articles' => $related
    ], 'Detail artikel');
}

    /**
     * Artikel berdasarkan mood
     */
    public function byMood($moodTag)
    {
        $articles = Article::where('mood_tag', $moodTag)->get();

        if ($articles->isEmpty()) {
            return $this->notFoundResponse('Tidak ada artikel untuk mood ini');
        }

        return $this->successResponse($articles, "Artikel untuk mood: {$moodTag}");
    }
}