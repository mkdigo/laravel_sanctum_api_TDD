<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\CustomerSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
  private $data = [
    'name' => 'Test',
    'email' => 'test@mail.com',
    'cellphone' => '090-9441-0000',
    'zipcode' => '444-1234',
    'address' => 'Soma address here',
    'password' => '12345678',
 ];

  private function expectedCustomer($customer)
  {
    $expectedCustomer = [
      'id',
      'name',
      'email',
      'cellphone',
      'zipcode',
      'address',
      'created_at',
      'updated_at',
    ];

    $whereAllTypes = [
      'customer.id' => 'integer',
      'customer.name' => 'string',
      'customer.email' => 'string',
      'customer.cellphone' => 'string',
      'customer.zipcode' => 'string',
      'customer.address' => 'string',
      'customer.created_at' => 'string',
      'customer.updated_at' => 'string',
    ];

    $customer = array_keys($customer);

    $arrayCompared1 = array_diff($customer, $expectedCustomer);
    $arrayCompared2 = array_diff($expectedCustomer, $customer);

    $this->assertEquals(0, count($arrayCompared1) + count($arrayCompared2));

    return [$expectedCustomer, $whereAllTypes];
  }

  public function test_list_customers()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(CustomerSeeder::class);

    $response = $this->get('/api/customers', [
      'Accept' => 'application/json',
    ]);

    [$expectedCustomer] = $this->expectedCustomer($response->json()['customers'][0]);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'customers' => [
          '*' => $expectedCustomer,
        ]
      ]);
  }

  public function test_customer_create()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $response = $this->json('POST', '/api/customers', $this->data, ['Accept' => 'application/json']);

    [$expectedCustomer, $whereAllTypes] = $this->expectedCustomer($response->json()['customer']);

    $response->assertStatus(201)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereType('success', 'boolean')
          ->whereAllType($whereAllTypes)
          ->where('success', true)
    );
  }

  public function test_customer_update()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(CustomerSeeder::class);

    $response = $this->json('PUT', '/api/customers/1', $this->data, ['Accept' => 'application/json']);

    [$expectedCustomer, $whereAllTypes] = $this->expectedCustomer($response->json()['customer']);

    $response->assertStatus(200)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereType('success', 'boolean')
          ->whereAllType($whereAllTypes)
          ->where('success', true)
    );
  }

  public function test_customer_show()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(CustomerSeeder::class);

    $response = $this->json('GET', '/api/customers/1', ['Accept' => 'application/json']);

    [$expectedCustomer, $whereAllTypes] = $this->expectedCustomer($response->json()['customer']);

    $response->assertStatus(200)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereType('success', 'boolean')
          ->whereAllType($whereAllTypes)
          ->where('success', true)
    );
  }

  public function  test_customer_delete()
  {
    Sanctum::actingAs(
      User::factory()->create(['is_admin' => true])
    );

    $this->seed(CustomerSeeder::class);

    $response = $this->json('DELETE', '/api/admin/customers/1', ['Accept' => 'application/json']);

    $response->assertStatus(200)
      ->assertExactJson([
        'success' => true,
      ]);
  }

  public function  test_check_if_non_admin_users_cannot_delete_customers()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(CustomerSeeder::class);

    $response = $this->json('DELETE', '/api/admin/customers/1', ['Accept' => 'application/json']);

    $response->assertStatus(401);
  }
}
