<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; // 追加
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category; // 追加

class ContactControllerTest extends TestCase
{
    use RefreshDatabase; // 追加（これによって no such table エラーが消えます）

    /**
     * ContactController で使用するリクエストバリデーションの検証
     */
    public function test_contact_controller_validation_rules()
    {
        // データベースに存在するカテゴリを用意する
        $category = Category::factory()->create();

        $request = new StoreContactRequest();

        // 正常系: 画像のカラム名に合わせた正しい入力データ
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
            'category_id' => $category->id, // 作成したカテゴリIDを指定
            'detail' => 'お問い合わせの本文内容です。',
        ];
        $this->assertTrue(Validator::make($validData, $request->rules())->passes());

        // 異常系: 姓（first_name）が欠落している場合
        $invalidNameData = $validData;
        unset($invalidNameData['first_name']);
        $this->assertFalse(Validator::make($invalidNameData, $request->rules())->passes());

        // 異常系: 分割された電話番号（tel2）が欠落している場合
        $invalidTelData = $validData;
        unset($invalidTelData['tel2']);
        $this->assertFalse(Validator::make($invalidTelData, $request->rules())->passes());
    }
}
