<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_name',
        'serial_number',
        'memo',
    ];

    public function usim()
    {
        return $this->hasOne(Usim::class);
    }
}
