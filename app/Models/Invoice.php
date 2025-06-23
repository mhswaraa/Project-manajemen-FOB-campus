<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number', // <-- INI ADALAH PERBAIKANNYA
        'tailor_id',
        'issue_date',
        'total_amount',
        'status',
        'payment_date',
        'receipt_path',
        'processed_by_user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'payment_date' => 'date',
    ];

    /**
     * Get the tailor that owns the invoice.
     */
    public function tailor()
    {
        return $this->belongsTo(Tailor::class, 'tailor_id', 'tailor_id');
    }

    /**
     * Get the progress items associated with the invoice.
     */
    public function progressItems()
    {
        return $this->hasMany(TailorProgress::class);
    }

    /**
     * Get the user who processed the payment.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}
