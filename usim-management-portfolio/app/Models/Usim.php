<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usim extends Model
{
    use HasFactory;

    const STATUS_CONTRACT = '사용중';
    const STATUS_SUSPENDED = '일시정지';
    const STATUS_CANCELED = '해지';

    const STATUSES = [
        self::STATUS_CONTRACT,
        self::STATUS_SUSPENDED,
        self::STATUS_CANCELED,
    ];

    protected $fillable = [
        'usim_number',
        'phone_number',
        'carrier',
        'site',
        'customer_id',
        'device_id',
        'status',
        'contract_date',
        'suspended_date',
        'canceled_date',
        'memo',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'suspended_date' => 'date',
        'canceled_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(UsimStatusHistory::class)->latest('changed_date')->latest('id');
    }
}
