<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }
    public function create()
    {
        return redirect()->route('contacts.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:categories,name'],
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('contacts.index')->with('success', 'カテゴリを追加しました');
    }
}
