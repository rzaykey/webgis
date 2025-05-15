<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeviceUptime;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_id',
        'ip_address',
        'latitude',
        'longitude',
    ];

    // Relasi ke DeviceUptime
    public function deviceUptimes()
    {
        return $this->hasMany(DeviceUptime::class, 'location_id');
    }
    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
