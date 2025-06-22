<?php
// Path: app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionProgress;
use App\Models\ProjectTailor;
use App\Models\TailorProgress;
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

    // Casting untuk tipe numerik
    protected $casts = [
        'price_per_piece' => 'decimal:2',
        'profit'          => 'decimal:2',
        'quantity'        => 'integer',
    ];

    // Constants untuk status
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Relasi ke ProductionProgress (hasMany)
     * → dipakai untuk progress internal proyek (bisa juga investasi langsung)
     */
    public function productionProgress()
{
    return $this->hasMany(ProductionProgress::class, 'project_id');
}

    /**
     * Relasi assignments penjahit (ProjectTailor pivot)
     */
    public function assignments()
    {
        return $this->hasMany(ProjectTailor::class, 'project_id');
    }

    /**
     * Relasi ke semua investasi (hasMany)
     */
    public function investments()
    {
        return $this->hasMany(Investment::class, 'project_id');
    }

    /**
     * Relasi ke INVESTASI yang sudah di-approve
     */
    public function approvedInvestments()
    {
        return $this->hasMany(Investment::class, 'project_id')
                    ->where('approved', true);
    }

    /**
     * Alias relasi untuk kemudahan: memanggil produksi progress
     * (investment → produksi nyata) via hasManyThrough
     *
     * Model akhir: TailorProgress
     * Perantara : ProjectTailor (assignment)
     */
     public function progress()
{
    return $this->hasManyThrough(
        TailorProgress::class,    // model akhir
        ProjectTailor::class,     // pivot assignment
        'project_id',             // FK di project_tailor
        'assignment_id',          // FK di tailor_progresses
        'id',                     // PK project
        'id'                      // PK project_tailor
    );
}
}
