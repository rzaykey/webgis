<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceUptime extends Model
{
    use HasFactory;

    protected $fillable = ['location_id', 'status', 'checked_at'];

    // Relasi ke model Location
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
