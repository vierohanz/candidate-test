<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupTest extends TestCase
{
    use RefreshDatabase;

    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supplier = Supplier::factory()->create();
    }

    // ── Index ──────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_paginated_layups_under_supplier()
    {
        CltLayup::factory()->count(3)->create(['supplier_id' => $this->supplier->id]);

        $response = $this->getJson("/api/v1/layups/{$this->supplier->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('metadata.total_row', 3);
    }

    /** @test */
    public function it_returns_422_for_nonexistent_supplier_on_index()
    {
        $response = $this->getJson('/api/v1/layups/9999');

        $response->assertStatus(422);
    }

    /** @test */
    public function it_filters_layups_by_search_query()
    {
        CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Alpha Layout']);
        CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Beta Layout']);

        $response = $this->getJson("/api/v1/layups/{$this->supplier->id}?q=alpha");

        $response->assertStatus(200)
            ->assertJsonPath('metadata.total_row', 1)
            ->assertJsonFragment(['name' => 'Alpha Layout']);
    }

    // ── Store ──────────────────────────────────────────────────────────────

    /** @test */
    public function it_creates_a_layup_under_supplier()
    {
        $response = $this->postJson('/api/v1/layups', [
            'supplier_id' => $this->supplier->id,
            'name'        => '5-Layer Layout',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $this->supplier->id,
            'name'        => '5-Layer Layout',
        ]);
    }

    /** @test */
    public function it_rejects_duplicate_layup_name_under_same_supplier()
    {
        CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Duplicate']);

        $response = $this->postJson('/api/v1/layups', [
            'supplier_id' => $this->supplier->id,
            'name'        => 'Duplicate',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_allows_same_layup_name_under_different_suppliers()
    {
        $other = Supplier::factory()->create();
        CltLayup::factory()->create(['supplier_id' => $other->id, 'name' => 'Same Name']);

        $response = $this->postJson('/api/v1/layups', [
            'supplier_id' => $this->supplier->id,
            'name'        => 'Same Name',
        ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_requires_supplier_id_and_name_to_create_layup()
    {
        $response = $this->postJson('/api/v1/layups', []);

        $response->assertStatus(422);
    }

    // ── Show ───────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_layup_with_layers()
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
        CltLayer::factory()->count(3)->create(['layup_id' => $layup->id]);

        $response = $this->getJson("/api/v1/layups/{$layup->id}/show");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $layup->id)
            ->assertJsonCount(3, 'data.layers');
    }

    /** @test */
    public function it_returns_404_for_nonexistent_layup()
    {
        $response = $this->getJson('/api/v1/layups/9999/show');

        $response->assertStatus(404);
    }

    // ── Update ─────────────────────────────────────────────────────────────

    /** @test */
    public function it_updates_a_layup()
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Old']);

        $response = $this->patchJson("/api/v1/layups/{$layup->id}/update", ['name' => 'Updated']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('clt_layups', ['id' => $layup->id, 'name' => 'Updated']);
    }

    /** @test */
    public function it_returns_404_when_updating_nonexistent_layup()
    {
        $response = $this->patchJson('/api/v1/layups/9999/update', ['name' => 'Ghost']);

        $response->assertStatus(404);
    }

    // ── Destroy ────────────────────────────────────────────────────────────

    /** @test */
    public function it_deletes_a_layup_and_cascades_to_layers()
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
        $layer = CltLayer::factory()->create(['layup_id' => $layup->id]);

        $response = $this->deleteJson("/api/v1/layups/{$layup->id}/delete");

        $response->assertStatus(200);
        $this->assertSoftDeleted('clt_layups', ['id' => $layup->id]);
        $this->assertSoftDeleted('clt_layers', ['id' => $layer->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_layup()
    {
        $response = $this->deleteJson('/api/v1/layups/9999/delete');

        $response->assertStatus(404);
    }
}
