<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers\Helper;
use App\Models\Customer;
use App\Helpers\Response;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
  public function index()
  {
    try {
      $customers = Customer::orderBy('name')->get();

      return response()->json([
        'success' => true,
        'customers' => CustomerResource::collection($customers),
      ]);
    } catch(Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function show($id)
  {
    try {
      $customer = Customer::findOrFail($id);

      return response()->json([
        'success' => true,
        'customer' => new CustomerResource($customer),
      ]);
    } catch(Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function store(Request $request)
  {
    try {
      [$data, $validator] = Helper::customerValidator($request);

      if($validator->fails()) return Response::validatorErrorsToMessage($validator);

      $customer = Customer::create($data);

      return response()->json([
        'success' => true,
        'customer' => new CustomerResource($customer),
      ], 201);
    } catch(Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function update(Request $request, $id)
  {
    try {
      $customer = Customer::findOrFail($id);

      [$data, $validator] = Helper::customerValidator($request, 'update', $customer);

      if($validator->fails()) return Response::validatorErrorsToMessage($validator);

      $customer->update($data);

      return response()->json([
        'success' => true,
        'customer' => new CustomerResource($customer),
      ], 200);
    } catch(Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function destroy($id)
  {
    try {
      $customer = Customer::findOrFail($id);

      return response()->json([
        'success' => true,
      ]);
    } catch(Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }
}
