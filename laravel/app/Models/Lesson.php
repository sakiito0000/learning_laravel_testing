<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * レッスンモデル
 */
class Lesson extends Model
{
    /**
     * リレーション
     */

     // 予約
     public function reservations()
     {
         return $this->hasMany(Reservation::class);
     }

    /**
     * アクセサ
     */

    public function getVacancyLevelAttribute(): VacancyLevel
    {
        return new VacancyLevel($this->remainingCount());
    }

    /**
     * レッスンの予約残り枠数を取得する
     */
    private function remainingCount(): int
    {
        // レッスンのキャパシティ - 予約確定数 = 予約残り枠数
        $reservationCount = $this->reservations()->count();
        return $this->capacity - $reservationCount;
    }
}
