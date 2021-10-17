<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Response;
use Illuminate\Http\Request;

class UserIsAdmin
{
  /**
  * Handle an incoming request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \Closure  $next
  * @return mixed
  */
  public function handle(Request $request, Closure $next)
  {
    if(!auth('sanctum')->user()->is_admin) return Response::unauthorized();
    return $next($request);
  }
}
