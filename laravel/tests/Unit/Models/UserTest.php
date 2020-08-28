<?php

namespace Tests\Unit\Models;

use App\Models\Lesson;
use App\Models\User;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * ユーザーモデルテスト
 */
class UserTest extends TestCase
{

    /**
     * 予約可能か
     *
     * @param string  $plan             プラン名
     * @param integer $remainingCount   レッスンの空き数
     * @param integer $reservationCount 現在までのユーザーの予約数
     * @param boolean $canReserve       予約可能か？
     * @dataProvider  dataCanReserve
     */
    public function testCanReserve(string $plan, int $remainingCount, int $reservationCount, bool $canReserve)
    {
        /** @var User $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('reservationCountThisMonth')->andReturn($reservationCount);
        $user->plan = $plan;

        /** @var Lesson $lesson */
        $lesson = Mockery::mock(Lesson::class);
        $lesson->shouldReceive('remainingCount')->andReturn($remainingCount);

        $this->assertSame($user->canReserve($lesson), $canReserve);
    }

    public function dataCanReserve()
    {
        return [
            '予約可:レギュラー,空きあり,月の上限以下' => [
                'plan'             => 'reglar',
                'remainingCount'   => 1,
                'reservationCount' => 4,
                'canReservice'     => true
            ],
            '予約不可:レギュラー,空きあり,月の上限以上' => [
                'plan'             => 'reglar',
                'remainingCount'   => 1,
                'reservationCount' => 5,
                'canReservice'     => false
            ],
            '予約不可:レギュラー,空きなし' => [
                'plan'             => 'reglar',
                'remainingCount'   => 0,
                'reservationCount' => 4,
                'canReservice'     => false
            ],
            '予約可:ゴールド,空きあり' => [
                'plan'             => 'gold',
                'remainingCount'   => 1,
                'reservationCount' => 5,
                'canReservice'     => true
            ],
            '予約不可:ゴールド,空きなし' => [
                'plan'             => 'gold',
                'remainingCount'   => 0,
                'reservationCount' => 4,
                'canReservice'     => false
            ],
        ];
    }
}
