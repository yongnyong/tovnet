<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsimStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'usim_id',
        'status',
        'changed_date',
        'changed_by',
        'memo',
    ];

    protected $casts = [
        'changed_date' => 'date',
    ];

    public function usim()
    {
        return $this->belongsTo(Usim::class);
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
