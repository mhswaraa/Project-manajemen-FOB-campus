<?php
// Path: app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionProgress;
use App\Models\ProjectTailor;
use App\Models\Investment;

class Project extends Model
{
    // Mass assignment
    protected $fillable = [
        'name',
        'price_per_piece',
        'quantity',
        'profit',
        'deadline',
        'status',
        'image',
    ];

    // Tipe casting
    protected $casts = [
        'price_per_piece' => 'decimal:2',
        'profit'          => 'decimal:2',
        'quantity'        => 'integer',
    ];

    // Status constants
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Relasi ke ProductionProgress (banyak)
     */
    public function productionProgress()
    {
        return $this->hasMany(ProductionProgress::class, 'project_id');
    }

    /**
     * Relasi ke pivot ProjectTailor (assignment penjahit)
     */
    public function assignments()
    {
        return $this->hasMany(ProjectTailor::class, 'project_id');
    }

    /**
     * Semua investasi (banyak)
     */
    public function investments()
    {
        return $this->hasMany(Investment::class, 'project_id');
    }

    /**
     * Hanya investasi yang sudah disetujui oleh admin
     */
    public function approvedInvestments()
    {
        return $this->hasMany(Investment::class, 'project_id')
                    ->where('approved', true);
    }
}
