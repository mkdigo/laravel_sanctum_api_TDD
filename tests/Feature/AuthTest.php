<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
  public function test_user_get_token_errors()
  {
    $response = $this->json('POST', '/api/auth/login', []);

    $response
      ->assertStatus(400)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereAllType([
          'success' => 'boolean',
          'message' => 'string'
        ])->where('success', false)
      );
  }

  public function test_user_get_token_unauthorized()
  {
    $user = User::factory()->create([
      'password' => Hash::make('123'),
    ]);

    $response = $this->json('POST', '/api/auth/login', [
      'email' => $user->email,
      'password' => '1234',
      'device' => 'web',
    ]);

    $response
    ->assertStatus(401)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereAllType([
          'success' => 'boolean',
          'message' => 'string'
        ])->where('success', false)
      );
  }

  public function test_user_get_token()
  {
    $user = User::factory()->create([
      'password' => Hash::make('123'),
    ]);

    $response = $this->json('POST', '/api/auth/login', [
      'email' => $user->email,
      'password' => '123',
      'device' => 'web',
    ]);

    $response
    ->assertStatus(200)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereAllType([
          'success' => 'boolean',
          'token' => 'string'
        ])->where('success', true)
      );
  }

  public function test_user_delete_token()
  {
    Sanctum::actingAs(
      User::factory()->create()
    );

    $response = $this->get('/api/auth/logout',[
      'Accept' => 'application/json',
    ]);

    $response->assertStatus(200)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereAllType([
          'success' => 'boolean',
          'message' => 'string'
        ])->where('success', true)
      );
  }

  private function routeTest($method, $route)
  {
    $response = $this->withHeaders([
      'Accept' => 'application/json'
    ])->$method($route);
    $response->assertStatus(401);
  }

  public function test_privates_routes()
  {
    $this->routeTest('get', '/api/auth/logout');

    $this->routeTest('get', '/api/users');
    $this->routeTest('get', '/api/users/1');
    $this->routeTest('put', '/api/users/1');
    $this->routeTest('delete', '/api/users/1');
  }
}
