<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    use HasFactory;

    protected $casts = [
        'start_time' => 'datetime',
        'stop_time' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function (Parking $parking){
            $parking->start_time = now();
        });

        static::addGlobalScope('ownParking', function (Builder $builder){
            if ($userId = auth()->id()){
                $builder->where('user_id', $userId);
            }
        });
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
