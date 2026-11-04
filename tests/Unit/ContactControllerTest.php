<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_controller_validation_rules()
    {

        $category = Category::factory()->create();
        $request = new StoreContactRequest();
        $validData = [
            'first_name' => '太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel1' => '090',
            'tel2' => '1234',
            'tel3' => '5678',
            'address' => '東京都渋谷区...',
            'building' => 'テックビル 101',
            'category_id' => $category->id,
            'detail' => 'お問い合わせの本文内容です。',
        ];
        $this->assertTrue(Validator::make($validData, $request->rules())->passes());
        $invalidNameData = $validData;
        unset($invalidNameData['first_name']);
        $this->assertFalse(Validator::make($invalidNameData, $request->rules())->passes());

        $invalidTelData = $validData;
        unset($invalidTelData['tel2']);
        $this->assertFalse(Validator::make($invalidTelData, $request->rules())->passes());
    }
}
