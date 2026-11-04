<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{

    public function index(IndexContactRequest $request): AnonymousResourceCollection
    {
        $query = Contact::query();
        if ($request->filled('keyword')) {
            $query->where('content', 'like', '%' . $request->keyword . '%');
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        $contacts = $query->paginate($request->get('per_page', 10));
        return ContactResource::collection($contacts);
    }

    public function show(Contact $contact): ContactResource
    {
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (array_key_exists('detail', $validated)) {
            $validated['body'] = $validated['detail'];
            unset($validated['detail']);
        }
        $contact = Contact::create($validated);

        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
            $contact->load('tags');
        }

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }
    public function update(UpdateContactRequest $request, Contact $contact): ContactResource
    {
        $validated = $request->validated();
        if (array_key_exists('detail', $validated)) {
            $validated['body'] = $validated['detail'];
            unset($validated['detail']);
        }

        $contact->update($validated);

        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        }

        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();
        return response()->json(null, 204);
    }
}
