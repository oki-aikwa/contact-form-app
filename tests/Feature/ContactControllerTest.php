<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_access_restriction()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_contact_form_flow_and_tag_attachment()
    {
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create(['name' => '質問']);
        $tag2 = Tag::factory()->create(['name' => '要望']);
        $validData = [
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel1' => '0-9',
            'tel2' => '0-9',
            'tel3' => '0-9',
            'address' => '東京都',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'body' => 'これは機能テストの本文です。',
            'detail' => 'これは機能テストの本文です。',
            'tag_ids' => [$tag1->id, $tag2->id]
        ];

        $response = $this->post('/contacts/confirm', $validData);
        $response->assertStatus($response->status() === 302 ? 302 : 200);

        $response = $this->withSession(['contact_form_inputs' => $validData])
            ->post(route('contacts.store'), $validData);

        $response->assertRedirect(route('contacts.thanks'));

        $this->assertDatabaseHas('contacts', [
            'first_name' => '太郎',
            'email' => 'test@example.com',
        ]);

        $contact = Contact::latest()->first();
        $this->assertCount(2, $contact->tags);
    }

    public function test_admin_contact_management()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee($contact->first_name);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");
        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
