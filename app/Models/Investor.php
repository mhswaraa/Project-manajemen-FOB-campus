<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    protected $primaryKey = 'investor_id';
    protected $fillable = ['user_id','name','email','phone','amount','deadline'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
