<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionProgress; // import model ProductionProgress

class Project extends Model
{
    // Tambahkan field yang boleh di‑mass assignment
    protected $fillable = [
        'name',
        'budget',
        'deadline',
        'status',
        'image',      // jika field image ikut di‑mass assign
        // tambahkan kolom lain bila ada, misal 'category'
    ];

    // (Opsional) constants status
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public function productionProgress()
{
    return $this->hasMany(ProductionProgress::class, 'project_id');
}
}