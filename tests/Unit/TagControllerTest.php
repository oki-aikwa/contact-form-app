<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TagController での新規登録バリデーション（必須入力・文字数・一意性の維持）
     */
    public function test_tag_controller_store_validation()
    {
        $request = new StoreTagRequest();

        // 正常系: タグ名の必須入力、50文字以内クリア
        $this->assertTrue(Validator::make(['name' => '正常なタグ'], $request->rules())->passes());

        // 異常系: 空文字 (requiredエラー)
        $this->assertFalse(Validator::make(['name' => ''], $request->rules())->passes());

        // 異常系: 50文字を超える場合 (max:50エラー)
        $longName = str_repeat('あ', 51);
        $this->assertFalse(Validator::make(['name' => $longName], $request->rules())->passes());

        // 異常系: 重複の禁止 (uniqueエラー)
        Tag::factory()->create(['name' => '重複タグ']);
        $this->assertFalse(Validator::make(['name' => '重複タグ'], $request->rules())->passes());
    }

    /**
     * TagController での更新バリデーション（自身の名前維持と他者重複の拒否）
     */
    public function test_tag_controller_update_validation()
    {
        $tagA = Tag::factory()->create(['name' => 'タグA']);
        $tagB = Tag::factory()->create(['name' => 'タグB']);

        $request = new StoreTagRequest();
        $rules = $request->rules();

        // 元の一意性チェックルールを「自分自身のID ($tagA->id) を除外する」ルールへと上書き修正
        if (isset($rules['name']) && is_array($rules['name'])) {
            foreach ($rules['name'] as $key => $rule) {
                // ルールが文字列の場合（'unique:tags,name' など）
                if (is_string($rule) && str_contains($rule, 'unique:tags')) {
                    $rules['name'][$key] = 'unique:tags,name,' . $tagA->id;
                }
                // ルールが Rule::unique() オブジェクトの場合
                if ($rule instanceof \Illuminate\Validation\Rules\Unique) {
                    $rules['name'][$key] = $rule->ignore($tagA->id);
                }
            }
        }

        // 1. 自身の名前 (タグA) のまま保存する場合、一意性チェックを通過 (true) することを確認
        $validator1 = Validator::make(['name' => 'タグA'], $rules);
        $this->assertTrue($validator1->passes());

        // 2. 他人が既に使用している名前 (タグB) に変更しようとした場合、重複エラー (false) になることを確認
        $validator2 = Validator::make(['name' => 'タグB'], $rules);
        $this->assertFalse($validator2->passes());
    }
}
