<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Lesson;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * レッスンコントロールクラスフューチャーテスト
 */
class LessonControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Undocumented function
     *
     * @param int    $capacity
     * @param int    $reservationCount
     * @param string $expectedVacancyLevelMark
     * @param string $button
     * @dataProvider dataReservationCount
     * @return void
     */
    public function testShow(
        int $capacity,
        int $reservationCount,
        string $expectedVacancyLevelMark,
        string $button
    ) {
        $lesson = factory(Lesson::class)->create(['name' => '楽しいヨガレッスン', 'capacity' => $capacity]);

        // 既存予約数分予約モデルを登録する
        for ($i = 0; $i < $reservationCount; $i++) {
            $user = factory(User::class)->create();
            $lesson->reservations()->save(factory(Reservation::class)->make(['user_id' => $user]));
        }

        //ログインユーザー設定
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->get("/lessons/{$lesson->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($lesson->name);
        $response->assertSee("空き状況:{$expectedVacancyLevelMark}");
        $response->assertSee($button, false);

    }

    /**
     * 予約数テスト用データ
     *
     * @return array
     * 
     * 'capacity' 　　　　　        レッスンの受入枠
     * 'reservationCount'         予約確定数
     * 'expectedVacancyLevelMark' 空き状況階層の印
     * 'button'                   予約ボタン
     */
    public function dataReservationCount(): array
    {
        $reserveButton = '<button class="btn btn-primary">このレッスンを予約する</button>';
        $span          = '<span class="btn btn-primary disabled">予約できません</span>';

        return [
            '空きなし' => [
                'capacity'                 => 1,
                'reservationCount'         => 1,
                'expectedVacancyLevelMark' => '×',
                'button'                   => $span
            ],
            '残りわずか' => [
                'capacity'                 => 6,
                'reservationCount'         => 2,
                'expectedVacancyLevelMark' => '△',
                'button'                   => $reserveButton
            ],
            '空き十分' => [
                'capacity'                 => 6,
                'reservationCount'         => 1,
                'expectedVacancyLevelMark' => '◎',
                'button'                   => $reserveButton
            ],
        ];
    }
}
