<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name', 
        'start_work', 
        'end_work',
        'month',
        'day',
        'break_in',
        'break_out',
        'worktime',
        'year'
    ];

    public function time()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
