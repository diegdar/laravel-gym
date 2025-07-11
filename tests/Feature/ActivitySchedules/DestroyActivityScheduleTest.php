<?php
declare(strict_types=1);

namespace Tests\Feature\ActivitySchedules;

use App\Models\ActivitySchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestHelper;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class DestroyActivityScheduleTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    protected const PERMISSION = 'activity.schedules.destroy';
    // Routes
    protected const ROUTE_INDEX = 'activity.schedules.index';
    protected const ROUTE_DESTROY = 'activity.schedules.destroy';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function destroyActivityScheduleAs(string $roleName, int $activityScheduleId): TestResponse
    {
        return $this->actingAsRole($roleName)->delete(route(self::ROUTE_DESTROY, $activityScheduleId));
    }

    public function test_can_destroy_an_activity_schedule_as_authorized_user()
    {        
        foreach (
                $this->getAuthorizedRoles                (self::PERMISSION)       
                as $authorizedRole
            ) 
        {
            $activityScheduleToDestroy = ActivitySchedule::factory()->create();
            $response = $this->destroyActivityScheduleAs($authorizedRole, $activityScheduleToDestroy->id);
            $response->assertStatus(302)
                     ->assertRedirect(route(self::ROUTE_INDEX));

            $this->assertDatabaseMissing('activity_schedules', ['id' => $activityScheduleToDestroy->id]);
        }
    }

    public function test_cannot_destroy_an_activity_schedule_as_unauthorized_user()
    {        
        $activityScheduleToDestroy = ActivitySchedule::factory()->create();
        foreach (
            $this->getUnauthorizedRoles(self::PERMISSION) as $unauthorizedRole
        ) {
            $response = $this->destroyActivityScheduleAs($unauthorizedRole, $activityScheduleToDestroy->id);
            $response->assertStatus( 403);

            $this->assertDatabaseHas('activity_schedules', ['id' => $activityScheduleToDestroy->id]);
        }
    }    

}
