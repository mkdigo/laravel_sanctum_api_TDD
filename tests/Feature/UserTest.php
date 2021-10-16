<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
  private $data = [
    'name' => 'name',
    'email' => 'email@email.com',
    'password' => '123',
  ];

  private function expectedUser($user)
  {
    $expectedUser = [
      'id',
      'name',
      'email',
      'updated_at',
      'created_at',
    ];
    $whereAllType = [
      'user.id' => 'integer',
      'user.name' => 'string',
      'user.email' => 'string',
      'user.updated_at' => 'string',
      'user.created_at' => 'string',
    ];

    $user = array_keys($user);
    $arrayCompared_1 = array_diff($expectedUser, $user);
    $arrayCompared_2 = array_diff($user, $expectedUser);

    $this->assertEquals(0, count($arrayCompared_1) + count($arrayCompared_2));

    return $whereAllType;
  }

  /** @test */
  public function test_create_new_user()
  {
    $response = $this->json('POST', '/api/users', $this->data);

    $whereAllType = $this->expectedUser($response->json()['user']);

    $response->assertStatus(201)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereType('success', 'boolean')
          ->whereAllType($whereAllType)
          ->where('success', true)
      );
  }

  public function test_check_if_two_users_cannot_use_the_same_email()
  {
    User::factory()->create([
      'email' => 'email@email.com',
    ]);

    $response = $this->json('POST', '/api/users', $this->data);

    $response->assertStatus(400)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereAllType([
          'success' => 'boolean',
          'message' => 'string'
        ])->where('success', false)
      );
  }

  public function test_user_update()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(UserSeeder::class);

    $response = $this->json('PUT', '/api/users/1', $this->data);

    $whereAllType = $this->expectedUser($response->json()['user']);

    $response->assertStatus(200)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereType('success', 'boolean')
          ->whereAllType($whereAllType)
          ->where('success', true)
      );
  }

  public function test_check_if_user_cannot_update_others_users()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(UserSeeder::class);

    $response = $this->json('PUT', '/api/users/2', $this->data);

    $response->assertStatus(401);
  }

  public function test_user_show()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(UserSeeder::class);

    $response = $this->json('GET', '/api/users/1');

    $whereAllType = $this->expectedUser($response->json()['user']);

    $response->assertStatus(200)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereType('success', 'boolean')
          ->whereAllType($whereAllType)
          ->where('success', true)
      );
  }

  public function test_user_delete()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(UserSeeder::class);

    $response = $this->json('DELETE', '/api/users/1');

    $response->assertStatus(200)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereType('success', 'boolean')
          ->where('success', true)
      );
  }

  public function test_check_if_user_cannot_delete_others_users()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(UserSeeder::class);

    $response = $this->json('DELETE', '/api/users/2');

    $response->assertStatus(401);
  }

  public function test_list_users()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $this->seed(UserSeeder::class);

    $response = $this->get('/api/users', [
      'Accept' => 'application/json',
    ]);

    $whereAllType = $this->expectedUser($response->json()['users'][0]);

    $this->assertTrue($response->json()['success']);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'success',
        'users' => [
          '*' => [
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
          ]
        ]
      ]);
  }
}
