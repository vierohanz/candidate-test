<?php

namespace Tests\Unit;

use App\Models\CltLayup;
use App\Models\Supplier;
use App\Repositories\EloquentSupplierRepository;
use App\Services\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SupplierService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SupplierService(new EloquentSupplierRepository());
    }

    // ── getAllSuppliers() ───────────────────────────────────────────────────

    /** @test */
    public function get_all_suppliers_returns_paginated_result()
    {
        Supplier::factory()->count(5)->create();

        $result = $this->service->getAllSuppliers(perPage: 3);

        $this->assertEquals(3, $result->perPage());
        $this->assertEquals(5, $result->total());
    }

    /** @test */
    public function get_all_suppliers_filters_by_search_term()
    {
        Supplier::factory()->create(['name' => 'PT Maju']);
        Supplier::factory()->create(['name' => 'CV Berkah']);

        $result = $this->service->getAllSuppliers(searchTerm: 'maju');

        $this->assertEquals(1, $result->total());
        $this->assertEquals('PT Maju', $result->items()[0]['name']);
    }

    // ── getSupplierWithLayups() ─────────────────────────────────────────────

    /** @test */
    public function get_supplier_with_layups_returns_supplier_and_relations()
    {
        $supplier = Supplier::factory()->create();
        CltLayup::factory()->count(2)->create(['supplier_id' => $supplier->id]);

        $result = $this->service->getSupplierWithLayups($supplier->id);

        $this->assertNotNull($result);
        $this->assertEquals($supplier->id, $result->id);
        $this->assertCount(2, $result->layups);
    }

    /** @test */
    public function get_supplier_with_layups_returns_null_for_nonexistent_id()
    {
        $result = $this->service->getSupplierWithLayups(9999);

        $this->assertNull($result);
    }

    // ── createSupplier() ───────────────────────────────────────────────────

    /** @test */
    public function create_supplier_persists_to_database()
    {
        $supplier = $this->service->createSupplier(['name' => 'PT Created']);

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertDatabaseHas('suppliers', ['name' => 'PT Created']);
    }

    // ── updateSupplier() ───────────────────────────────────────────────────

    /** @test */
    public function update_supplier_changes_name()
    {
        $supplier = Supplier::factory()->create(['name' => 'Before']);

        $result = $this->service->updateSupplier($supplier->id, ['name' => 'After']);

        $this->assertTrue($result);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'After']);
    }

    /** @test */
    public function update_supplier_returns_false_for_nonexistent_id()
    {
        $result = $this->service->updateSupplier(9999, ['name' => 'Ghost']);

        $this->assertFalse($result);
    }

    // ── deleteSupplier() ───────────────────────────────────────────────────

    /** @test */
    public function delete_supplier_soft_deletes_record()
    {
        $supplier = Supplier::factory()->create();

        $result = $this->service->deleteSupplier($supplier->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    /** @test */
    public function delete_supplier_returns_false_for_nonexistent_id()
    {
        $result = $this->service->deleteSupplier(9999);

        $this->assertFalse($result);
    }

    // ── exportSupplier() ───────────────────────────────────────────────────

    /** @test */
    public function export_supplier_returns_structured_array()
    {
        $supplier = Supplier::factory()->create();
        $layup    = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
        \App\Models\CltLayer::factory()->create([
            'layup_id' => $layup->id, 'layer_order' => 1,
            'thickness' => 20, 'width' => 100, 'angle' => 0,
        ]);

        $result = $this->service->exportSupplier($supplier->id);

        $this->assertArrayHasKey('layups', $result);
        $this->assertCount(1, $result['layups']);
        $this->assertEquals($layup->name, $result['layups'][0]['name']);
        $this->assertArrayHasKey('layers', $result['layups'][0]);
        $this->assertEquals(1, $result['layups'][0]['layers'][0]['layer_order']);
    }

    /** @test */
    public function export_supplier_throws_exception_for_nonexistent_id()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Supplier not found');

        $this->service->exportSupplier(9999);
    }

    // ── exportFilename() ───────────────────────────────────────────────────

    /** @test */
    public function export_filename_formats_correctly()
    {
        $supplier = Supplier::factory()->create(['name' => 'PT Maju Jaya']);

        $filename = $this->service->exportFilename($supplier);

        $this->assertEquals('CLT_Export_PT_Maju_Jaya.json', $filename);
    }
}
