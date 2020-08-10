<?php

namespace Tests\Unit\Models;

use App\Models\VacancyLevel;
use PHPUnit\Framework\TestCase;

/**
 * 空き状況階層テスト
 */
class VacancyLevelTest extends TestCase
{
    /**
     * 空き状況の印を返す
     *
     * @param int    $remainingCount 残り数
     * @param string $expectedMark   予想の印
     * @dataProvider dataMark
     * @return void
     */
    public function testMark(int $remainingCount, string $expectedMark)
    {
        $level = new VacancyLevel($remainingCount);
        $this->assertSame($expectedMark, $level->mark());
    }

    public function dataMark(): array
    {
        return [
            '空きなし' => [
                'remainingCount' => 0,
                'expectedMark'   => '×',
            ],
            '残りわずか' => [
                'remainingCount' => 4,
                'expectedMark'   => '△',
            ],
            '空き十分' => [
                'remainingCount' => 5,
                'expectedMark'   => '◎',
            ],
        ];
    }
}
