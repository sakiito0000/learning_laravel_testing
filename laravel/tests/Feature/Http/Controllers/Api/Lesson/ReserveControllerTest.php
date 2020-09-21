<?php

namespace Tests\Feature\Http\Controllers\Api\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\Factories\Traits\CreateUser;
use Tests\TestCase;

/**
 * SPA想定の機能テスト
 */
class ReserveControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreateUser;


    public function testInvoke_正常()
    {
        $lesson = factory(Lesson::class)->create();
        $user = $this->createUser();
        $this->actingAs($user, 'api');

        $response = $this->postJson("/api/lessons/{$lesson->id}/reserve");
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'user_id'   => $user->id,
            'lesson_id' => $lesson->id
        ]);

        $this->assertDatabaseHas('reservations', [
            'user_id'   => $user->id,
            'lesson_id' => $lesson->id
        ]);
    }

    public function testInvoke_異常()
    {
        $lesson = factory(Lesson::class)->create(['capacity' => 1]);
        $anotherUser = $this->createUser();
        $lesson->reservations()->save(factory(Reservation::class)->make([
            'user_id'   => $anotherUser->id,
            'lesson_id' => $lesson->id
        ]));
        $user = $this->createUser();
        $this->actingAs($user, 'api');

        $response = $this->postJson("/api/lessons/{$lesson->id}/reserve");
        $response->assertStatus(Response::HTTP_CONFLICT);

        $response->assertJsonStructure(['error']);
        $error = $response->json('error');
        $this->assertStringContainsString('予約できません。', $error);

        $this->assertDatabaseMissing('reservations', [
            'lesson_id' => $lesson->id,
            'user_id'   => $user->id
        ]);
    }
}
