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
     * @dataProvider  dataCanReserve_正常
     */
    public function testCanReserve_正常 (
        string $plan,
        int    $remainingCount,
        int    $reservationCount
    ) {
        /** @var User $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('reservationCountThisMonth')->andReturn($reservationCount);
        $user->plan = $plan;

        /** @var Lesson $lesson */
        $lesson = Mockery::mock(Lesson::class);
        $lesson->shouldReceive('remainingCount')->andReturn($remainingCount);

        $user->canReserve($lesson);
        $this->assertTrue(true);
    }

    /**
     * 予約不可か
     *
     * @param string  $plan             プラン名
     * @param integer $remainingCount   レッスンの空き数
     * @param integer $reservationCount 現在までのユーザーの予約数
     * @param string  $errorMessage     例外エラーメッセージ
     * @dataProvider  dataCanReserve_エラー
     */
    public function testCanReserve_エラー (
        string $plan,
        int    $remainingCount,
        int    $reservationCount,
        string $errorMessage
    ) {
        /** @var User $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('reservationCountThisMonth')->andReturn($reservationCount);
        $user->plan = $plan;

        /** @var Lesson $lesson  */
        $lesson = Mockery::mock(Lesson::class);
        $lesson->shouldReceive('remainingCount')->andReturn($remainingCount);

        $this->expectErrorMessage($errorMessage);
        $user->canReserve($lesson);
    }

    public function dataCanReserve_正常()
    {
        return [
            '予約可:レギュラー,空きあり,月の上限以下' => [
                'plan'             => 'reglar',
                'remainingCount'   => 1,
                'reservationCount' => 4,
                'canReservice'     => true
            ],
            '予約可:ゴールド,空きあり' => [
                'plan'             => 'gold',
                'remainingCount'   => 1,
                'reservationCount' => 5,
                'canReservice'     => true
            ]
        ];
    }

    public function dataCanReserve_エラー()
    {
        return [
            '予約不可:レギュラー,空きあり,月の上限以上' => [
                'plan'             => 'reglar',
                'remainingCount'   => 1,
                'reservationCount' => 5,
                'errorMessage'     => '今月の予約がプランの上限に達しています。'
            ],
            '予約不可:レギュラー,空きなし' => [
                'plan'             => 'reglar',
                'remainingCount'   => 0,
                'reservationCount' => 4,
                'errorMessage'     => 'レッスンの予約可能上限に達しています。'
            ],
            '予約不可:ゴールド,空きなし' => [
                'plan'             => 'gold',
                'remainingCount'   => 0,
                'reservationCount' => 4,
                'errorMessage'     => 'レッスンの予約可能上限に達しています。'
            ],
        ];
    }
}
