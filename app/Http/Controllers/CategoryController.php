<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController
{
    /**
     * カテゴリ一覧の表示（GET /categories）
     */
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    /**
     * カテゴリ作成画面へのアクセスがあった場合、お問い合わせフォームにリダイレクトしてエラーを回避
     */
    public function create()
    {
        return redirect()->route('contacts.index');
    }

    /**
     * 新しいカテゴリの保存（POST /categories）
     */
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
