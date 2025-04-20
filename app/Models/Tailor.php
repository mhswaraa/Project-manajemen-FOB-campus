<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tailor extends Model
{
    protected $table = 'penjahits';
    protected $primaryKey = 'tailor_id';
    protected $fillable = ['user_id','address','phone','email','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
