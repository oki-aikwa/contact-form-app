<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController
{
    /**
     * タグ一覧の表示（GET /tags）
     */
    public function index()
    {
        $tags = Tag::all();
        return view('tags.index', compact('tags'));
    }

    /**
     * タグ作成画面へのアクセスがあった場合、お問い合わせフォームにリダイレクトしてエラーを回避
     */
    public function create()
    {
        return redirect()->route('contacts.index');
    }

    /**
     * 新しいタグの保存（POST /tags）
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tags,name'],
        ]);

        Tag::create([
            'name' => $request->name,
        ]);

        return redirect()->route('contacts.index')->with('success', 'タグを追加しました');
    }
}
