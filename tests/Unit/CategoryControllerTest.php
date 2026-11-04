<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * CategoryController に関連するバリデーション：存在しないカテゴリIDの拒否
     */
    public function test_category_controller_invalid_id_is_rejected()
    {
        $request = new StoreContactRequest();

        $data = [
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都...',
            'category_id' => 99999, // 存在しないカテゴリID
            'body' => 'テスト本文',
        ];

        $this->assertFalse(Validator::make($data, $request->rules())->passes());
    }


    public function test_category_controller_relation_has_many_contacts()
    {
        $category = Category::factory()->create();
        Contact::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->contacts);
        $this->assertInstanceOf(Contact::class, $category->contacts->first());
    }
}
