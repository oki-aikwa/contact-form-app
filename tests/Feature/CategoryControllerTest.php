<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_and_contact_relation()
    {
        $category = Category::factory()->create();

        $contact1 = Contact::factory()->create(['category_id' => $category->id]);
        $contact2 = Contact::factory()->create(['category_id' => $category->id]);

        $this->assertCount(2, $category->contacts);
        $this->assertTrue($category->contacts->contains($contact1));
        $this->assertTrue($category->contacts->contains($contact2));
    }
}
