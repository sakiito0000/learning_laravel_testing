<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Model;

/**
 * 空き状況階層モデル
 */
class VacancyLevel extends Model
{
    // 残り数
    private $remainingCount;

    // 印とCSSラベル文字のセット
    const MARKS = [
        'empty'  => '×',
        'few'    => '△',
        'enough' => '◎'
    ];

    public function __construct(int $remainingCount)
    {
        $this->remainingCount = $remainingCount;
    }

    /**
     * 残り数から空き状況の印を判定して返す
     *
     * @return string
     * @throws DomainExection
     */
    public function mark(): string
    {
        $slug = $this->slug();
        assert(isset(self::MARKS[$slug]), new DomainException('invalid slug value'));
        return self::MARKS[$slug];
    }

    /**
     * CSSラベル文字を返します。
     *
     * @return string
     */
    public function slug(): string
    {
        if ($this->remainingCount === 0) {
            return 'empty';
        } elseif ($this->remainingCount < 5) {
            return 'few';
        }
        return 'enough';
    }
}
