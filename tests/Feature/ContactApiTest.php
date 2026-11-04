<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase; // テストごとにデータベースの状態をリセットする

    /**
     * 1. お問い合わせ一覧APIのテスト
     * 仕様：JSON形式での一覧が返り、検索・ページネーションが機能し、不正な値は422が返ること。
     */
    public function test_index_returns_paginated_contacts_and_filters(): void
    {
        // テスト用データ（カテゴリと、それに紐づくお問い合わせ）を作成
        $category = Category::factory()->create();
        Contact::factory()->count(15)->create([
            'category_id' => $category->id,
            'gender' => 1,
            'body' => '特定のキーワードを含むお問い合わせ内容です。'
        ]);

        // 【正常系】一覧取得とページネーションの検証
        $response = $this->getJson('/api/v1/contacts');
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['current_page', 'last_page']]);

        // 【正常系】フィルタリング（検索機能）の検証
        $searchResponse = $this->getJson("/api/v1/contacts?keyword=特定&gender=1&category_id={$category->id}");
        $searchResponse->assertStatus(200);
        $this->assertNotEmpty($searchResponse->json('data'));

        // 【異常系】不正な値によるバリデーションエラー（422）の検証
        $invalidResponse = $this->getJson('/api/v1/contacts?category_id=99999'); // 存在しないID
        $invalidResponse->assertStatus(422);
    }

    /**
     * 2. お問い合わせ詳細APIのテスト
     * 仕様：JSON形式で詳細が返り、存在しないIDでは404エラー（JSON）が返ること。
     */
    public function test_show_returns_contact_detail_or_404(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        // 【正常系】詳細データの検証
        $response = $this->getJson("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'gender', 'category', 'tags']
            ]);

        // 【異常系】存在しないIDでの404検証
        $response404 = $this->getJson('/api/v1/contacts/99999');
        $response404->assertStatus(404);
    }

    /**
     * 3. お問い合わせ作成APIのテスト
     * 仕様：レコードが作成され201が返り、バリデーションエラー時は422が返ること。
     */
    public function test_store_creates_record_or_returns_422(): void
    {
        $category = Category::factory()->create();

        $validData = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都新宿区',
            'gender' => 1,
            'category_id' => $category->id,
            'detail' => 'テストのお問い合わせ内容です。'
        ];

        // 【正常系】作成成功（201）の検証
        $response = $this->postJson('/api/v1/contacts', $validData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('contacts', ['first_name' => '山田']);

        // 【異常系】必須項目漏れによるバリデーションエラー（422）の検証
        $invalidData = array_merge($validData, ['first_name' => '']); // 名前を空にする
        $response422 = $this->postJson('/api/v1/contacts', $invalidData);
        $response422->assertStatus(422);
    }

    /**
     * 4. お問い合わせ更新APIのテスト
     * 仕様：レコードが更新され200が返り、存在しないIDは404、バリデーションエラー時は422が返ること。
     */
    public function test_update_modifies_record_or_returns_errors(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        $updateData = [
            'detail' => '新しく更新した内容です。'
        ];

        // 【正常系】更新成功（200）の検証
        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'body' => '新しく更新した内容です。']);

        // 【異常系】存在しないIDでの404検証
        $response404 = $this->putJson('/api/v1/contacts/99999', $updateData);
        $response404->assertStatus(404);

        // 【異常系】不正な値（例：存在しないカテゴリID）による422検証
        $invalidData = ['category_id' => 99999];
        $response422 = $this->putJson("/api/v1/contacts/{$contact->id}", $invalidData);
        $response422->assertStatus(422);
    }

    /**
     * 5. お問い合わせ削除APIのテスト
     * 仕様：レコードが削除され204が返り、存在しないIDは404が返ること。
     */
    public function test_destroy_deletes_record_or_returns_404(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        // 【正常系】削除成功（204）の検証
        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(204);

        // データベースから消えている（または論理削除されている）ことを確認
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);

        // 【異常系】存在しないIDでの404検証
        $response404 = $this->deleteJson('/api/v1/contacts/99999');
        $response404->assertStatus(404);
    }
}
