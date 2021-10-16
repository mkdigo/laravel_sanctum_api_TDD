<?php

namespace App\Helpers;

use Illuminate\Validation\Validator;

class Response {
  public static function validatorErrorsToMessage(Validator $validator)
  {
    $errors = $validator->errors()->messages();
    $message = '';

    foreach($errors as $error) {
      $message .= $error[0] . ' ';
    }

    $message = trim($message);

    return response()->json([
      'success' => false,
      'message' => $message,
    ], 400);
  }

  public static function notFound()
  {
    return response()->json([
      'success' => false,
      'message' => 'Not Found.'
    ], 404);
  }

  public static function unauthorized()
  {
    return response()->json([
      'success' => false,
      'message' => 'Unauthorized.'
    ], 401);
  }
}
