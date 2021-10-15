<?php

namespace Tests\Feature;

use Tests\TestCase;
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
  public function create_new_user()
  {
    $response = $this->json('POST', '/api/users', $this->data);

    $whereAllType = $this->expectedUser($response->json()['user']);

    $response->assertStatus(201)
      ->assertJson(fn (AssertableJson $json) =>
        $json->whereType('success', 'boolean')
          ->whereAllType($whereAllType)
      );
  }

  // //** @test */
  // public function update_user()
  // {
  //   $response = $this->json('POST', '/api/users/1', [
  //     'name' => 'name',
  //     'email' => 'email@email.com',
  //     'password' => '123',
  //   ]);

  //   $expectedUser = [
  //     'id',
  //     'name',
  //     'email',
  //     'updated_at',
  //     'created_at',
  //   ];
  //   $user = array_keys($response->json()['user']);
  //   $arrayCompared = array_diff($expectedUser, $user);

  //   $this->assertEquals(0, count($arrayCompared));

  //   $response->assertStatus(201)
  //     ->assertJson(fn (AssertableJson $json) =>
  //       $json->whereAllType([
  //         'success' => 'boolean',
  //         'user.id' => 'integer',
  //         'user.name' => 'string',
  //         'user.email' => 'string',
  //         'user.updated_at' => 'string',
  //         'user.created_at' => 'string',
  //       ])
  //     );
  // }
}
