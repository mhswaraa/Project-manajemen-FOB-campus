<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;
    protected $fillable = ['tailor_id', 'image_path', 'caption'];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class, 'tailor_id', 'tailor_id');
    }
}