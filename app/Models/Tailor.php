<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tailor extends Model
{
    use HasFactory;

    protected $table = 'penjahits';
    protected $primaryKey = 'tailor_id';

    protected $fillable = [
        'user_id',
        'address',
        'phone',
        'email',
        'status',
        'gdrive_link', // <-- Tambahkan ini
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function assignments()
    {
        return $this->hasMany(ProjectTailor::class, 'tailor_id', 'tailor_id');
    }

    public function progress()
    {
        return $this->hasManyThrough(TailorProgress::class, ProjectTailor::class, 'tailor_id', 'assignment_id', 'tailor_id', 'id');
    }
    
    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'specialization_tailor', 'tailor_id', 'specialization_id');
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class, 'tailor_id', 'tailor_id');
    }

    /**
     * Relasi ke semua invoice yang dimiliki oleh penjahit ini.
     * INI ADALAH PERBAIKANNYA.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'tailor_id', 'tailor_id');
    }
}
