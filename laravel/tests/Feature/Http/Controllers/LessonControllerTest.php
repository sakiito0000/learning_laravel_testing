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
     * @dataProvider dataReservationCount
     * @return void
     */
    public function testShow(int $capacity, int $reservationCount, string $expectedVacancyLevelMark)
    {
        $lesson = factory(Lesson::class)->create(['name' => '楽しいヨガレッスン', 'capacity' => $capacity]);

        for ($i = 0; $i < $reservationCount; $i++) {
            $user = factory(User::class)->create();
            $lesson->reservations()->save(factory(Reservation::class)->make(['user_id' => $user]));
        }
        $response = $this->get("/lessons/{$lesson->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($lesson->name);
        $response->assertSee("空き状況:{$expectedVacancyLevelMark}");
    }

    /**
     * 予約数テスト用データ
     *
     * @return array
     * 
     * 'capacity' 　　　　　       レッスンの受入枠
     * 'reservationCount'         予約確定数
     * 'expectedVacancyLevelMark' 空き状況階層の印
     */
    public function dataReservationCount(): array
    {
        return [
            '空きなし' => [
                'capacity'                 => 1,
                'reservationCount'         => 1,
                'expectedVacancyLevelMark' => '×'
            ],
            '残りわずか' => [
                'capacity'                 => 6,
                'reservationCount'         => 2,
                'expectedVacancyLevelMark' => '△'
            ],
            '空き十分' => [
                'capacity'                 => 6,
                'reservationCount'         => 1,
                'expectedVacancyLevelMark' => '◎'
            ],
        ];
    }
}
