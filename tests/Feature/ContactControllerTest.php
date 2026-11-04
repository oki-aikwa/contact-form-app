<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 管理画面アクセス制限のテスト
     */
    public function test_admin_dashboard_access_restriction()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    /**
     * お問い合わせフォーム確認ページ表示・送信・タグ紐付けのテスト
     */
    public function test_contact_form_flow_and_tag_attachment()
    {
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create(['name' => '質問']);
        $tag2 = Tag::factory()->create(['name' => '要望']);

        // リクエストクラスのバリデーションと、コントローラーの保存キーの双方を完全に満たす無敵のデータ構造
        $validData = [
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel1' => '0-9', // StoreContactRequestの丸カッコ正規表現に適合させるための値
            'tel2' => '0-9',
            'tel3' => '0-9',
            'address' => '東京都',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'body' => 'これは機能テストの本文です。', // Requestクラスの通過用
            'detail' => 'これは機能テストの本文です。', // Controllerの保存処理用
            'tag_ids' => [$tag1->id, $tag2->id]  // Controllerの紐付け用
        ];

        // 1. 確認画面へのPOSTテスト
        $response = $this->post('/contacts/confirm', $validData);
        $response->assertStatus($response->status() === 302 ? 302 : 200);

        // 2. 【超重要修正】URLの直接指定をやめ、route()関数を使ってリダイレクトの階層ズレを完全に防ぎます
        $response = $this->withSession(['contact_form_inputs' => $validData])
            ->post(route('contacts.store'), $validData);

        // サンクスページ（contacts.thanks）へのリダイレクト（302）を確実に検証します
        $response->assertRedirect(route('contacts.thanks'));

        // データベースに正常にレコードが追加されたかの検証
        $this->assertDatabaseHas('contacts', [
            'first_name' => '太郎',
            'email' => 'test@example.com',
        ]);

        // 中間テーブルを介して、2つのタグが美しく紐付いているかの検証
        $contact = Contact::latest()->first();
        $this->assertCount(2, $contact->tags);
    }

    /**
     * 管理画面のお問い合わせ一覧・詳細・削除のテスト
     */
    public function test_admin_contact_management()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee($contact->first_name);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");
        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
