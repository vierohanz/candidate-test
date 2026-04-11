<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    // ── Index ──────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_paginated_suppliers()
    {
        Supplier::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/suppliers');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [['id', 'name']],
                'metadata' => ['current_page', 'total_row', 'total_page'],
            ]);
    }

    /** @test */
    public function it_filters_suppliers_by_search_query()
    {
        Supplier::factory()->create(['name' => 'PT Maju Jaya']);
        Supplier::factory()->create(['name' => 'CV Berkah']);

        $response = $this->getJson('/api/v1/suppliers?q=maju');

        $response->assertStatus(200)
            ->assertJsonPath('metadata.total_row', 1)
            ->assertJsonFragment(['name' => 'PT Maju Jaya']);
    }

    // ── Store ──────────────────────────────────────────────────────────────

    /** @test */
    public function it_creates_a_supplier()
    {
        $response = $this->postJson('/api/v1/suppliers', ['name' => 'PT New Supplier']);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('suppliers', ['name' => 'PT New Supplier']);
    }

    /** @test */
    public function it_rejects_duplicate_supplier_name()
    {
        Supplier::factory()->create(['name' => 'PT Duplikat']);

        $response = $this->postJson('/api/v1/suppliers', ['name' => 'PT Duplikat']);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_requires_name_to_create_supplier()
    {
        $response = $this->postJson('/api/v1/suppliers', []);

        $response->assertStatus(422);
    }

    // ── Show ───────────────────────────────────────────────────────────────

    /** @test */
    public function it_returns_supplier_with_layups()
    {
        $supplier = Supplier::factory()->create();
        CltLayup::factory()->count(2)->create(['supplier_id' => $supplier->id]);

        $response = $this->getJson("/api/v1/suppliers/{$supplier->id}/show");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $supplier->id)
            ->assertJsonCount(2, 'data.layups');
    }

    /** @test */
    public function it_returns_404_for_nonexistent_supplier()
    {
        $response = $this->getJson('/api/v1/suppliers/9999/show');

        $response->assertStatus(404);
    }

    // ── Update ─────────────────────────────────────────────────────────────

    /** @test */
    public function it_updates_a_supplier()
    {
        $supplier = Supplier::factory()->create(['name' => 'Old Name']);

        $response = $this->patchJson("/api/v1/suppliers/{$supplier->id}/update", ['name' => 'New Name']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'New Name']);
    }

    /** @test */
    public function it_returns_404_when_updating_nonexistent_supplier()
    {
        $response = $this->patchJson('/api/v1/suppliers/9999/update', ['name' => 'Ghost']);

        $response->assertStatus(404);
    }

    // ── Destroy ────────────────────────────────────────────────────────────

    /** @test */
    public function it_deletes_a_supplier_and_cascades_to_layups()
    {
        $supplier = Supplier::factory()->create();
        $layup    = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
        CltLayer::factory()->create(['layup_id' => $layup->id]);

        $response = $this->deleteJson("/api/v1/suppliers/{$supplier->id}/delete");

        $response->assertStatus(200);
        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
        $this->assertSoftDeleted('clt_layups', ['id' => $layup->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_supplier()
    {
        $response = $this->deleteJson('/api/v1/suppliers/9999/delete');

        $response->assertStatus(404);
    }

    // ── Export ─────────────────────────────────────────────────────────────

    /** @test */
    public function it_exports_supplier_with_full_hierarchy()
    {
        $supplier = Supplier::factory()->create(['name' => 'PT Export']);
        $layup    = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
        CltLayer::factory()->create(['layup_id' => $layup->id, 'layer_order' => 1]);

        $response = $this->get("/api/v1/suppliers/{$supplier->id}/export");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('layups', $data);
        $this->assertCount(1, $data['layups']);
        $this->assertArrayHasKey('layers', $data['layups'][0]);
    }
}
