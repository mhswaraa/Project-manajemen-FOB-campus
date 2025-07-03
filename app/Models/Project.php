<?php
// Path: app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // --- AWAL PERUBAHAN: Menambahkan konstanta status ---
    // Konstanta untuk alur kerja proyek
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    
    // Konstanta lama yang mungkin masih dipakai di beberapa controller
    // Ini akan memetakan nilai lama ke nilai baru untuk konsistensi
    public const STATUS_ACTIVE   = 'in_progress';
    public const STATUS_INACTIVE = 'completed'; // Diubah sesuai permintaan
    // --- AKHIR PERUBAHAN ---

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'description',
        'nominal_proyek',
        'quantity',
        'deadline',
        'price_per_piece',
        'material_cost',
        'wage_per_piece',
        'profit',
        'convection_profit',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deadline' => 'datetime',
        'price_per_piece' => 'float',
        'material_cost' => 'float',
        'profit' => 'float',
        'convection_profit' => 'float',
        'wage_per_piece' => 'float',
        'nominal_proyek' => 'float'
    ];

    /**
     * Relasi ke semua investasi.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class, 'project_id');
    }

    /**
     * Relasi ke investasi yang sudah disetujui.
     */
    public function approvedInvestments()
    {
        return $this->hasMany(Investment::class, 'project_id')
                    ->where('approved', true);
    }

    /**
     * Relasi ke assignments penjahit (ProjectTailor).
     */
    public function assignments()
    {
        return $this->hasMany(ProjectTailor::class, 'project_id');
    }

    /**
     * Relasi untuk mendapatkan semua progress penjahit melalui assignments.
     */
    public function progress()
    {
        return $this->hasManyThrough(
            TailorProgress::class,    // Model akhir yang ingin diakses
            ProjectTailor::class,     // Model perantara
            'project_id',             // Foreign key di tabel perantara (project_tailor)
            'assignment_id',          // Foreign key di tabel akhir (tailor_progress)
            'id',                     // Local key di tabel awal (projects)
            'id'                      // Local key di tabel perantara (project_tailor)
        );
    }
}
