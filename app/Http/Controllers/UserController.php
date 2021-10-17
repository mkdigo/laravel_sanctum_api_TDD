<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use App\Helpers\Response;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function index()
  {
    try {
      $users = User::orderBy('name')->get();

      return response()->json([
        'success' => true,
        'users' => UserResource::collection($users),
      ]);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function store(Request $request)
  {
    try {
      [$data, $validator] = Helper::userValidator($request);

      if($validator->fails()) {
        return Response::validatorErrorsToMessage($validator);
      }

      $data['password'] = Hash::make($data['password']);

      $user = User::create($data);

      return response()->json([
        'success' => true,
        'user' => new UserResource($user),
      ], 201);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function show($id)
  {
    try {
      $user = User::findOrFail($id);

      return response()->json([
        'success' => true,
        'user' => new UserResource($user),
      ]);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $user = User::findOrFail($id);

      if(auth('sanctum')->user()->id !== $user->id) return Response::unauthorized();

      [$data, $validator] = Helper::userValidator($request, 'update', $user);

      if($validator->fails()) return Response::validatorErrorsToMessage($validator);

      $user->update($data);

      return response()->json([
        'success' => true,
        'user' => new UserResource($user),
      ]);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function toggleAdminAccess($id)
  {
    try {
      $user = User::findOrFail($id);
      $user->is_admin = !$user->is_admin;
      $user->update();

      return response()->json([
        'success' => true,
      ]);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function destroy($id)
  {
    try {
      $user = User::findOrFail($id);

      $user->delete();

      return response()->json([
        'success' => true,
      ]);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }
}
