<?php

namespace Tests\Feature\Http\Controllers\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\Factories\Traits\CreateUser;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    use CreateUser;
    use RefreshDatabase;

    public function testInvoke()
    {
        $lesson = factory(Lesson::class)->create();
        $user   = $this->createUser();
        $this->actingAs($user);

        $response = $this->post("/lessons/{$lesson->id}/reserve");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect("lessons/{$lesson->id}");
    }

    public function testInvoke_異常系()
    {
        // 残り許容量が１枠のみのレッスン(他ユーザーが予約済み)を作成
        $lesson = factory(Lesson::class)->create(['capacity' => 1]);
        $anotherUser = $this->createUser();
        $lesson->reservations()->save(factory(Reservation::class)->make([
            'user_id'   => $anotherUser->id,
            'lesson_id' => $lesson->id
        ]));

        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->from("/lessons/{$lesson->id}")
            ->post("/lessons/{$lesson->id}/reserve");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect("lessons/{$lesson->id}");

        $response->assertSessionHasErrors();
        // メッセージの中身まで確認したい場合は以下の2行も追加
        $error = session('errors')->first();
        $this->assertStringContainsString('予約できません。', $error);

        $this->assertDatabaseMissing('reservations', [
            'lesson_id' => $lesson->id,
            'user_id'   => $user->id
        ]);
    }
}
