<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * カテゴリー関係（1対多リレーション）のテスト
     */
    public function test_category_and_contact_relation()
    {
        $category = Category::factory()->create();

        $contact1 = Contact::factory()->create(['category_id' => $category->id]);
        $contact2 = Contact::factory()->create(['category_id' => $category->id]);

        // 1つのカテゴリから、紐づく複数の問い合わせ（hasMany）が正しく取得できることの検証
        $this->assertCount(2, $category->contacts);
        $this->assertTrue($category->contacts->contains($contact1));
        $this->assertTrue($category->contacts->contains($contact2));
    }
}
