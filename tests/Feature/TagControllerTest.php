<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_crud_management()
    {
        $user = User::factory()->create();

        $tagForView = Tag::factory()->create();
        $response = $this->actingAs($user)->get("/admin/tags/{$tagForView->id}/edit");
        $response->assertStatus(200);

        $tagData = ['name' => '新規タグ'];
        $response = $this->actingAs($user)->post('/admin/tags', $tagData);
        $this->assertDatabaseHas('tags', ['name' => '新規タグ']);

        $tag = Tag::where('name', '新規タグ')->first();

        $updateData = ['name' => '更新済みタグ'];
        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", $updateData);
        $this->assertDatabaseHas('tags', ['name' => '更新済みタグ']);

        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
