<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Refresh the database and run migrations
        $this->artisan('migrate:fresh');

        // Create a user for authentication
       $this->user = User::factory()->create();
       $this->actingAs($this->user, 'sanctum');
    }

    public function test_get_products(): void
    {
        $this->withoutExceptionHandling();

        Product::factory()->count(10)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                    'description',
                    'stock',
                    'image',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
        ]);

        $response->assertJsonCount(10, 'data');
    }

    public function test_get_product_by_id()
    {
        $this->withoutExceptionHandling();

        $product = Product::factory()->create();

        $response = $this->getJson('/api/products/' . $product->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'price',
            'description',
            'stock',
            'image',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_insert_product_with_validation()
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'description' => 'This is a test product.',
            'stock' => 10,
            'image' => UploadedFile::fake()->image('product.jpg'),
        ]);

        $response->assertStatus(201); // Should return 201 for creation
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'price',
                'description',
                'stock',
                'image',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'description' => 'This is a test product.',
            'stock' => 10,
        ]);
    }

    public function test_insert_product_validation_errors()
    {
        $response = $this->postJson('/api/products', [
            'name' => '',
            'price' => -10,
            'stock' => -5,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'price', 'stock']);
    }

    public function test_update_product_with_validation()
    {
        $this->withoutExceptionHandling();

        $product = Product::factory()->create();

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => 'Updated Product',
            'price' => 99.99,
            'description' => 'This is an updated product.',
            'stock' => 10,
            'image' => UploadedFile::fake()->image('product.jpg'),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'price',
                'description',
                'stock',
                'image',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Updated Product',
            'price' => 99.99,
            'description' => 'This is an updated product.',
            'stock' => 10,
        ]);
    }

    public function test_update_product_validation_errors()
    {
        $product = Product::factory()->create();

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => '',
            'price' => -10,
            'stock' => -5,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'price', 'stock']);
    }

    public function test_delete_product_with_id()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
