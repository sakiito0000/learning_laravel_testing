<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 空き状況階層モデル
 */
class VacancyLevel extends Model
{
    // 残り数
    private $remainingCount;

    public function __construct(int $remainingCount)
    {
        $this->remainingCount = $remainingCount;
    }

    /**
     * 残り数から空き状況の印を判定して返す
     *
     * @return string
     */
    public function mark(): string
    {
        if ($this->remainingCount === 0) {
            return '×';
        } elseif ($this->remainingCount < 5) {
            return '△';
        }
        return '◎';
    }
}
