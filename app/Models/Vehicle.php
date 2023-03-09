<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('ownVehicle', function (Builder $builder){
          if ($userId = auth()->id()) {
              $builder->where('user_id', $userId);
          }
      });

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
