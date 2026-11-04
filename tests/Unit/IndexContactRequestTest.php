<?php

namespace Tests\Unit;

use App\Http\Requests\IndexContactRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーションルールの検証データプロバイダ
     */
    public static function validationProvider(): array
    {
        return [
            '正常な検索パラメータ' => [
                'data' => [
                    'Keyword' => 'テスト',      // 頭文字を大文字に修正
                    'gender' => '男性',         // stringルールに合わせて文字列に変更
                    'category_id' => 1,        // 存在するカテゴリID
                    'date' => '2026-11-04',    // Y-m-d 形式
                ],
                'expected' => true,
            ],
            '異常な日付フォーマット' => [
                'data' => [
                    'date' => '2026/11/04'     // Y-m-d形式ではないためエラーになる
                ],
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function test_index_contact_request_validation(array $data, bool $expected): void
    {
        // データベースにID=1のカテゴリデータをあらかじめ作成
        \App\Models\Category::factory()->create(['id' => 1]);

        $request = new IndexContactRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertEquals($expected, $validator->passes());
    }
}
