<?php

namespace Tests\Unit\Models;

use App\Models\User;
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
        $user       = new User();
        $user->plan = $plan;
        $this->assertSame($user->canReserve($remainingCount, $reservationCount), $canReserve);
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
