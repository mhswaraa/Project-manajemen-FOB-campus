<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'profit_amount',
        'payment_date',
        'receipt_path',
        'notes',
        'processed_by_user_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}
