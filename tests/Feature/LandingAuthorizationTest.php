<?php

namespace Tests\Feature;

use App\Models\Landing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LandingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymous_requests_cannot_read_or_write_landings(): void
    {
        $this->getJson('/api/v1/landings')->assertUnauthorized();
        $this->postJson('/api/v1/landings', ['landingData' => []])->assertUnauthorized();
    }

    public function test_user_id_is_derived_from_authenticated_user_and_input_is_validated(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/landings', ['landingData' => 'invalid'])->assertUnprocessable();
        $this->postJson('/api/v1/landings', ['landingData' => ['title' => 'Demo'], 'user_id' => $other->id])
            ->assertCreated()->assertJsonPath('user_id', $owner->id);
        $this->assertDatabaseMissing('landings', ['user_id' => $other->id]);
    }

    public function test_cross_user_reads_and_mutations_are_forbidden(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $landing = new Landing();
        $landing->landingData = ['title' => 'Private'];
        $landing->user_id = $owner->id;
        $landing->save();

        Sanctum::actingAs($other);
        $this->getJson('/api/v1/landings')->assertOk()->assertExactJson([]);
        $this->getJson("/api/v1/landings/{$landing->id}")->assertForbidden();
        $this->getJson("/api/v1/landings/user/{$owner->id}")->assertForbidden();
        $this->putJson("/api/v1/landings/{$landing->id}", ['landingData' => ['title' => 'Changed']])->assertForbidden();
        $this->deleteJson("/api/v1/landings/{$landing->id}")->assertForbidden();
        $this->assertDatabaseHas('landings', ['id' => $landing->id, 'user_id' => $owner->id]);
    }

    public function test_owner_can_update_and_delete_own_landing(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $id = $this->postJson('/api/v1/landings', ['landingData' => ['title' => 'Original']])
            ->assertCreated()->json('id');

        $this->getJson("/api/v1/landings/{$id}")->assertOk();
        $this->putJson("/api/v1/landings/{$id}", ['landingData' => ['title' => 'Updated'], 'user_id' => 999])
            ->assertOk()->assertJsonPath('user_id', $owner->id);
        $this->deleteJson("/api/v1/landings/{$id}")->assertNoContent();
        $this->getJson("/api/v1/landings/{$id}")->assertNotFound();
    }

    public function test_invalid_resource_id_and_malformed_json_are_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/landings/999999')->assertNotFound();
        $this->putJson('/api/v1/landings/999999', ['landingData' => ['title' => 'No']])->assertNotFound();
        $this->call('POST', '/api/v1/landings', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], '{malformed')->assertUnprocessable();
    }

    public function test_component_writes_require_login_and_validate_payload(): void
    {
        $this->postJson('/api/v1/components', ['name' => 'X'])->assertUnauthorized();
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/v1/components', ['name' => 'X'])->assertUnprocessable();
        $this->postJson('/api/v1/components', ['name' => 'Hero', 'type' => 'header', 'componentData' => ['text' => 'Demo']])
            ->assertCreated();
    }
}
