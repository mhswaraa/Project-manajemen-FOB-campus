<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TailorProgress extends Model
{
    use HasFactory;

    protected $table = 'tailor_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assignment_id',
        'date',
        'quantity_done',
        'notes',
        'invoice_id',
        
        // AWAL PERUBAHAN: Tambahkan semua kolom QC di sini
        'status',
        'accepted_qty',
        'rejected_qty',
        'qc_notes',
        'qc_checked_at',
        'qc_admin_id',
        // AKHIR PERUBAHAN
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'qc_checked_at' => 'datetime', // Tambahkan cast untuk kolom datetime
    ];

    /**
     * Get the assignment that the progress belongs to.
     */
    public function assignment()
    {
        return $this->belongsTo(ProjectTailor::class, 'assignment_id');
    }
    
    /**
     * Get the invoice associated with the progress.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the admin who performed the QC check.
     */
    public function qcAdmin()
    {
        return $this->belongsTo(User::class, 'qc_admin_id');
    }
}
