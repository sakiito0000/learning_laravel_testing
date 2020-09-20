<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 予約モデル
 */
class Reservation extends Model
{
    protected $fillable = [
        'lesson_id',
        'user_id',
    ];
}
