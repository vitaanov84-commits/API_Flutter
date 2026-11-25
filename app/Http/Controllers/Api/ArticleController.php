<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // READ: semua artikel milik user yang login
    public function index(Request $request)
    {
        $articles = $request->user()->articles()->latest()->get();

        return response()->json(['data' => $articles]);
    }

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'title'   => $validated['title'],
            'content' => $validated['content'],
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = $request->user()->articles()->create($data);

        return response()->json(['data' => $article], 201);
    }

    // READ: satu artikel
    public function show(Request $request, $id)
    {
        $article = $request->user()->articles()->findOrFail($id);

        return response()->json(['data' => $article]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $article = $request->user()->articles()->findOrFail($id);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        $data = [
            'title'   => $validated['title'],
            'content' => $validated['content'],
        ];

        if ($request->hasFile('image')) {
            // Foto baru: buang yang lama, simpan yang baru
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        } elseif ($request->boolean('remove_image')) {
            // User minta foto dihapus
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = null;
        }

        $article->update($data);

        return response()->json(['data' => $article]);
    }

    // DELETE
    public function destroy(Request $request, $id)
    {
        $article = $request->user()->articles()->findOrFail($id);

        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return response()->json(['message' => 'Artikel dihapus']);
    }
}