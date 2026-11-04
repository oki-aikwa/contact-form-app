<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\IndexContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{

    public function index(IndexContactRequest $request)
    {
        $query = $this->buildSearchQuery($request);


        $contacts = $query->paginate(7)->withQueryString();
        $categories = Category::all();
        $tags = Tag::all();
        $genderLabels = [
            1 => '男性',
            2 => '女性',
            3 => 'その他'
        ];

        return view('admin.index', compact('contacts', 'tags', 'genderLabels', 'categories'));
    }


    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', compact('contact'));
    }
    public function destroy(Contact $contact)
    {
        $contact->tags()->detach();
        $contact->delete();

        return redirect()->route('admin.index');
    }

    public function export(IndexContactRequest $request)
    {
        $query = $this->buildSearchQuery($request);
        $contacts = $query->orderBy('created_at', 'desc')->get();

        $response = new StreamedResponse(function () use ($contacts) {
            $stream = fopen('php://output', 'w');
            fwrite($stream, pack('C*', 0xEF, 0xBB, 0xBF));
            fputcsv($stream, ['id', '氏名', '性別', 'メールアドレス', '電話番号', '住所', '建物名', 'お問い合わせの種類', 'お問い合わせ内容', '作成日時']);
            $genderMap = [1 => '男性', 2 => '女性', 3 => 'その他'];
            foreach ($contacts as $contact) {
                fputcsv($stream, [
                    $contact->id,
                    $contact->last_name . ' ' . $contact->first_name,
                    $genderMap[$contact->gender] ?? 'その他',
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category->name ?? '',
                    $contact->tags->name ?? '',
                    $contact->detail,
                    $contact->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($stream);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="contacts_' . date('YmdHis') . '.csv"');

        return $response;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
    private function buildSearchQuery(IndexContactRequest $request)
    {
        $query = Contact::query()->with(['category', 'tags']);

        if ($request->filled('Keyword')) {
            $keyword = $request->Keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gender') && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        return $query;
    }
}
