<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * タグ管理機能（Tag CRUD）のテスト
     */
    public function test_tag_crud_management()
    {
        $user = User::factory()->create();

        // ◯ web.phpに一覧(GET /admin/tags)がないため、既存の編集画面(GET)へのアクセスで認証チェックを行います
        $tagForView = Tag::factory()->create();
        $response = $this->actingAs($user)->get("/admin/tags/{$tagForView->id}/edit");
        $response->assertStatus(200);

        // 新規登録の検証 (POST /admin/tags)
        $tagData = ['name' => '新規タグ'];
        $response = $this->actingAs($user)->post('/admin/tags', $tagData);
        $this->assertDatabaseHas('tags', ['name' => '新規タグ']);

        $tag = Tag::where('name', '新規タグ')->first();

        // 更新の検証 (PUT /admin/tags/{tag})
        $updateData = ['name' => '更新済みタグ'];
        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", $updateData);
        $this->assertDatabaseHas('tags', ['name' => '更新済みタグ']);

        // 削除の検証 (DELETE /admin/tags/{tag})
        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
