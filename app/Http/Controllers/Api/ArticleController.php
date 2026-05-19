<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * GET: Daftar artikel (bisa filter by category & mood_tag)
     */
    public function index(Request $request)
    {
        $query = Article::query();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by mood tag
        if ($request->has('mood_tag')) {
            $query->where('mood_tag', $request->mood_tag);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'Daftar artikel',
            'data' => $articles
        ], 200);
    }

    /**
     * GET: Detail artikel
     */
    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => false,
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $article
        ], 200);
    }
}