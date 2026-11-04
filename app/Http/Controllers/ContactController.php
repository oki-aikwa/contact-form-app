<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController
{

    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $validated['tel'] = $validated['tel1'] . '-' . $validated['tel2'] . '-' . $validated['tel3'];

        $category = Category::find($validated['category_id']);

        if ($category) {
            $category->content = $category->name;
        }

        $tags = isset($validated['tag_ids']) ? Tag::whereIn('id', $validated['tag_ids'])->get() : collect();


        $request->session()->put('contact_form_inputs', $validated);

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->session()->get('contact_form_inputs');

        if ($request->has('back') || !$validated) {
            return redirect()->route('contacts.index')->withInput($validated);
        }
        DB::transaction(function () use ($validated) {
            Contact::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'email' => $validated['email'],
                'tel1' => $validated['tel1'],
                'tel2' => $validated['tel2'],
                'tel3' => $validated['tel3'],
                'address' => $validated['address'],
                'building' => $validated['building'],
                'category_id' => $validated['category_id'],
                'body' => $validated['detail'],
            ]);
            if (isset($validated['tag_ids'])) {
                $contact = Contact::latest()->first();
                $contact->tags()->attach($validated['tag_ids']);
            }
        });
        $request->session()->forget('contact_form_inputs');
        return redirect()->route('contacts.thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}
