<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  public function getToken(Request $request)
  {
    $data = $request->only('email', 'password', 'device');
    $validator = Validator::make($data, [
      'email' => 'required|email',
      'password' => 'required|string',
      'device' => 'required|string',
    ]);

    if($validator->fails()) {
      return Response::validatorErrorsToMessage($validator);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return Response::unauthorized();
    }

    $user->tokens()->delete();

    $token = $user->createToken($request->device)->plainTextToken;

    return response()->json([
      'success' => true,
      'token' => $token,
    ]);
  }

  public function deleteToken()
  {
    auth('sanctum')->user()->tokens()->delete();

    return response()->json([
      'success' => true,
      'message' => 'Logged out successfully.'
    ]);
  }
}
