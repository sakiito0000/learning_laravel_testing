<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * ユーザーがレッスンの予約可能か？
     *
     * @param integer $remainingCount    レッスンの残り予約枠
     * @param integer $reservationCount  ユーザーの現在までの予約数
     * @return bool
     */
    public function canReserve(int $remainingCount, int $reservationCount): bool
    {
        if ($remainingCount <= 0) {
            return false;
        }

        if ($this->plan === 'gold') {
            return true;
        }

        return $reservationCount < 5;
    }
}
