<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Helper {
  public static function userValidator(Request $request, $method = 'store', $user = null)
  {
    $data = $request->only('name', 'email', 'password');

    $rules = [];

    if($method === 'store') {
      $rules = [
        'name' => 'required|string|max:191',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|max:191',
      ];
    } else {
      $rules = [
        'name' => 'required|string|max:191',
        'email' => [
          'required',
          'email',
          Rule::unique('users')->ignore($user->id),
        ],
        'password' => 'nullable|string|max:191',
      ];
    }

    $validator = Validator::make($data, $rules);

    return [$data, $validator];
  }
}
