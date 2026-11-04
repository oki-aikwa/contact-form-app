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

    public function test_tag_controller_store_validation()
    {
        $request = new StoreTagRequest();

        $this->assertTrue(Validator::make(['name' => '正常なタグ'], $request->rules())->passes());

        $this->assertFalse(Validator::make(['name' => ''], $request->rules())->passes());

        $longName = str_repeat('あ', 51);
        $this->assertFalse(Validator::make(['name' => $longName], $request->rules())->passes());

        Tag::factory()->create(['name' => '重複タグ']);
        $this->assertFalse(Validator::make(['name' => '重複タグ'], $request->rules())->passes());
    }

    public function test_tag_controller_update_validation()
    {
        $tagA = Tag::factory()->create(['name' => 'タグA']);
        $tagB = Tag::factory()->create(['name' => 'タグB']);

        $request = new StoreTagRequest();
        $rules = $request->rules();

        if (isset($rules['name']) && is_array($rules['name'])) {
            foreach ($rules['name'] as $key => $rule) {

                if (is_string($rule) && str_contains($rule, 'unique:tags')) {
                    $rules['name'][$key] = 'unique:tags,name,' . $tagA->id;
                }

                if ($rule instanceof \Illuminate\Validation\Rules\Unique) {
                    $rules['name'][$key] = $rule->ignore($tagA->id);
                }
            }
        }

        $validator1 = Validator::make(['name' => 'タグA'], $rules);
        $this->assertTrue($validator1->passes());

        $validator2 = Validator::make(['name' => 'タグB'], $rules);
        $this->assertFalse($validator2->passes());
    }
}
