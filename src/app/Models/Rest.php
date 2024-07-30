<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_id',
        'start_rest',
        'end_rest',
        'resttime'
    ];

    public function stamp()
    {
        return $this->belongsTo(Time::class);
    }
}
