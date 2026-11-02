<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController
{

    public function index()
    {
        $tags = Tag::all();
        return view('tags.index', compact('tags'));
    }


    public function create()
    {
        return redirect()->route('contacts.index');
    }


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
