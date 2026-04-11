<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayerTest extends TestCase
{
    use RefreshDatabase;

    protected Supplier $supplier;
    protected CltLayup $layup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supplier = Supplier::factory()->create();
        $this->layup    = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
    }

    // ── Index ──────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_paginated_layers_under_layup()
    {
        CltLayer::factory()->count(3)->create(['layup_id' => $this->layup->id]);

        $response = $this->getJson("/api/v1/layers/{$this->supplier->id}/{$this->layup->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('metadata.total_row', 3);
    }

    /** @test */
    public function it_returns_422_for_invalid_supplier_or_layup_on_index()
    {
        $response = $this->getJson('/api/v1/layers/9999/8888');

        $response->assertStatus(422);
    }

    // ── Store ──────────────────────────────────────────────────────────────

    /** @test */
    public function it_creates_a_layer()
    {
        $response = $this->postJson('/api/v1/layers', [
            'layup_id'    => $this->layup->id,
            'layer_order' => 1,
            'thickness'   => 20.5,
            'width'       => 100.0,
            'angle'       => 0.0,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('clt_layers', [
            'layup_id'    => $this->layup->id,
            'layer_order' => 1,
        ]);
    }

    /** @test */
    public function it_rejects_duplicate_layer_order_in_same_layup()
    {
        CltLayer::factory()->create(['layup_id' => $this->layup->id, 'layer_order' => 1]);

        $response = $this->postJson('/api/v1/layers', [
            'layup_id'    => $this->layup->id,
            'layer_order' => 1,
            'thickness'   => 30.0,
            'width'       => 120.0,
            'angle'       => 45.0,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_allows_same_layer_order_in_different_layups()
    {
        $otherLayup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
        CltLayer::factory()->create(['layup_id' => $otherLayup->id, 'layer_order' => 1]);

        $response = $this->postJson('/api/v1/layers', [
            'layup_id'    => $this->layup->id,
            'layer_order' => 1,
            'thickness'   => 20.0,
            'width'       => 100.0,
            'angle'       => 0.0,
        ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_requires_all_fields_to_create_layer()
    {
        $response = $this->postJson('/api/v1/layers', []);

        $response->assertStatus(422);
    }

    // ── Show ───────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_layer_with_layup_context()
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $response = $this->getJson("/api/v1/layers/{$layer->id}/show");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $layer->id)
            ->assertJsonStructure(['data' => ['id', 'layer_order', 'thickness', 'width', 'angle', 'layup']]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_layer()
    {
        $response = $this->getJson('/api/v1/layers/9999/show');

        $response->assertStatus(404);
    }

    // ── Update ─────────────────────────────────────────────────────────────

    /** @test */
    public function it_updates_a_layer()
    {
        $layer = CltLayer::factory()->create([
            'layup_id'    => $this->layup->id,
            'layer_order' => 1,
            'thickness'   => 20.0,
            'width'       => 100.0,
            'angle'       => 0.0,
        ]);

        $response = $this->putJson("/api/v1/layers/{$layer->id}/update", [
            'layup_id'    => $this->layup->id,
            'layer_order' => 1,
            'thickness'   => 25.0,
            'width'       => 110.0,
            'angle'       => 45.0,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('clt_layers', ['id' => $layer->id, 'thickness' => 25.0]);
    }

    /** @test */
    public function it_returns_404_when_updating_nonexistent_layer()
    {
        $response = $this->putJson('/api/v1/layers/9999/update', [
            'layup_id'    => $this->layup->id,
            'layer_order' => 1,
            'thickness'   => 20.0,
            'width'       => 100.0,
            'angle'       => 0.0,
        ]);

        $response->assertStatus(404);
    }

    // ── Destroy ────────────────────────────────────────────────────────────

    /** @test */
    public function it_soft_deletes_a_layer()
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $response = $this->deleteJson("/api/v1/layers/{$layer->id}/delete");

        $response->assertStatus(200);
        $this->assertSoftDeleted('clt_layers', ['id' => $layer->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_layer()
    {
        $response = $this->deleteJson('/api/v1/layers/9999/delete');

        $response->assertStatus(404);
    }
}
