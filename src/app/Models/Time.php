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
        'break_in',
        'break_out',
        'breaktime',
        'worktime',
        'date'
    ];

    protected $casts = [
        'start_work' => 'datetime:H:i:s',
        'end_work' => 'datetime:H:i:s',
        'date' => 'datetime:Y-m-d'
    ];

    // public function scopeDateSearch($query, $date)
    // {
    //     if (!empty($date)) {
    //         $query->where('date', $date);
    //     }
    // }

    public function time()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
