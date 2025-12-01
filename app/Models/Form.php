<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
  protected $fillable = [
    'name',
    'json_schema',
  ];

  protected $casts = [
    'json_schema' => 'array',
  ];

  public function submissions()
  {
    return $this->hasMany(FormSubmission::class);
  }
}
