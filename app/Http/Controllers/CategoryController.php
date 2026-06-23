<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use RealRashid\SweetAlert\Facades\Alert;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::withCount('posts')->orderBy('title')->get();
        return view('admin.pages.categorie.index', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100|unique:categories,title',
        ]);

        Category::create(['title' => $request->title]);

        Cache::forget('categories_list');

        Alert::toast('Catégorie créée avec succès', 'success');
        return back();
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:100',
        ]);

        $category = Category::whereSlug($slug)->firstOrFail();
        $category->update(['title' => $request->title]);

        Cache::forget('categories_list');

        Alert::toast('Catégorie modifiée avec succès', 'success');
        return back();
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->posts()->count() > 0) {
            Alert::toast('Impossible de supprimer : cette catégorie contient des articles.', 'warning');
            return back();
        }

        $category->delete();
        Cache::forget('categories_list');

        Alert::toast('Catégorie supprimée avec succès', 'success');
        return redirect()->route('category');
    }
}
