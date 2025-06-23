<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function tailors()
    {
        return $this->belongsToMany(Tailor::class, 'specialization_tailor', 'specialization_id', 'tailor_id');
    }
}