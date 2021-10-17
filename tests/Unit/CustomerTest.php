<?php

namespace Tests\Unit;

use App\Models\Customer;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
  public function test_check_if_user_columns_is_correct()
  {
    $user = new Customer;

    $expected = [
      'name',
      'email',
      'password',
      'cellphone',
      'zipcode',
      'address',
    ];

    $arrayCompared = array_diff($expected, $user->getFillable());

    $this->assertEquals(0, count($arrayCompared));
  }
}
