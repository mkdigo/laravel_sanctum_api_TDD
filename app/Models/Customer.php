<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
  * The attributes that are mass assignable.
  *
  * @var string[]
  */
  protected $fillable = [
    'name',
    'email',
    'password',
    'cellphone',
    'zipcode',
    'address',
  ];

  /**
  * The attributes that should be hidden for serialization.
  *
  * @var array
  */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
  * The attributes that should be cast.
  *
  * @var array
  */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];
}
