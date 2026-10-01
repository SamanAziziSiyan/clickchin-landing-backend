<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ComponentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymous_access_and_invalid_payload_are_rejected(): void
    {
        $this->getJson('/api/v1/components')->assertUnauthorized();
        $this->postJson('/api/v1/components', ['name' => 'Hero'])->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/v1/components', ['name' => 'Hero'])->assertUnprocessable();
        $this->call('POST', '/api/v1/components', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], '{malformed')->assertUnprocessable();
    }

    public function test_component_is_owned_by_authenticated_creator(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($owner);
        $id = $this->postJson('/api/v1/components', [
            'name' => 'Hero', 'type' => 'header', 'componentData' => ['text' => 'Private'], 'user_id' => $other->id,
        ])->assertCreated()->assertJsonPath('user_id', $owner->id)->json('ID');

        $this->getJson('/api/v1/components')->assertOk()->assertJsonCount(1);
        $this->putJson("/api/v1/components/{$id}", ['name' => 'Updated', 'user_id' => $other->id])
            ->assertOk()->assertJsonPath('user_id', $owner->id);

        Sanctum::actingAs($other);
        $this->getJson('/api/v1/components')->assertOk()->assertExactJson([]);
        $this->getJson("/api/v1/components/{$id}")->assertForbidden();
        $this->putJson("/api/v1/components/{$id}", ['name' => 'Stolen'])->assertForbidden();
        $this->deleteJson("/api/v1/components/{$id}")->assertForbidden();

        Sanctum::actingAs($owner);
        $this->deleteJson("/api/v1/components/{$id}")->assertNoContent();
        $this->getJson("/api/v1/components/{$id}")->assertNotFound();
        $this->getJson('/api/v1/components/999999')->assertNotFound();
    }
}
