<?php

namespace Tests\Unit;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ImportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ImportService $service;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service  = new ImportService();
        $this->supplier = Supplier::factory()->create();
    }

    // ── scan() ─────────────────────────────────────────────────────────────

    /** @test */
    public function scan_returns_import_token()
    {
        $report = $this->service->scan($this->supplier->id, ['layups' => []]);

        $this->assertArrayHasKey('import_token', $report);
        $this->assertNotEmpty($report['import_token']);
    }

    /** @test */
    public function scan_stores_data_in_cache_with_token()
    {
        $data   = ['layups' => [['name' => 'Test', 'layers' => []]]];
        $report = $this->service->scan($this->supplier->id, $data);

        $cached = Cache::get("import_data_{$report['import_token']}");
        $this->assertNotNull($cached);
        $this->assertEquals($data, $cached);
    }

    /** @test */
    public function scan_detects_new_layups()
    {
        $data = ['layups' => [
            ['name' => 'Brand New Layup', 'layers' => []],
        ]];

        $report = $this->service->scan($this->supplier->id, $data);

        $this->assertCount(1, $report['layups']['new']);
        $this->assertEquals('Brand New Layup', $report['layups']['new'][0]['name']);
        $this->assertFalse($report['summary']['has_conflicts']);
    }

    /** @test */
    public function scan_detects_existing_layup_as_match()
    {
        CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'Existing Layup']);

        $data = ['layups' => [
            ['name' => 'Existing Layup', 'layers' => []],
        ]];

        $report = $this->service->scan($this->supplier->id, $data);

        $this->assertNotEmpty($report['layups']['matches']);
        $this->assertEmpty($report['layups']['new']);
    }

    /** @test */
    public function scan_detects_layer_conflict_when_specs_differ()
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'CLT-5']);
        CltLayer::factory()->create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 20,
            'width'       => 100,
            'angle'       => 0,
        ]);

        $data = ['layups' => [['name' => 'CLT-5', 'layers' => [
            ['layer_order' => 1, 'thickness' => 30, 'width' => 100, 'angle' => 0],
        ]]]];

        $report = $this->service->scan($this->supplier->id, $data);

        $this->assertTrue($report['summary']['has_conflicts']);
        $this->assertCount(1, $report['layers']['conflicts']);
        $this->assertEquals(20.0, $report['layers']['conflicts'][0]['existing']['thickness']);
        $this->assertEquals(30.0, $report['layers']['conflicts'][0]['incoming']['thickness']);
    }

    /** @test */
    public function scan_does_not_flag_conflict_when_specs_are_identical()
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'CLT-5']);
        CltLayer::factory()->create([
            'layup_id'    => $layup->id,
            'layer_order' => 1,
            'thickness'   => 20,
            'width'       => 100,
            'angle'       => 0,
        ]);

        $data = ['layups' => [['name' => 'CLT-5', 'layers' => [
            ['layer_order' => 1, 'thickness' => 20, 'width' => 100, 'angle' => 0],
        ]]]];

        $report = $this->service->scan($this->supplier->id, $data);

        $this->assertFalse($report['summary']['has_conflicts']);
        $this->assertEmpty($report['layers']['conflicts']);
    }

    /** @test */
    public function scan_counts_totals_correctly()
    {
        $data = ['layups' => [
            ['name' => 'L1', 'layers' => [
                ['layer_order' => 1, 'thickness' => 10, 'width' => 50, 'angle' => 0],
                ['layer_order' => 2, 'thickness' => 15, 'width' => 60, 'angle' => 90],
            ]],
            ['name' => 'L2', 'layers' => [
                ['layer_order' => 1, 'thickness' => 20, 'width' => 80, 'angle' => 45],
            ]],
        ]];

        $report = $this->service->scan($this->supplier->id, $data);

        $this->assertEquals(2, $report['summary']['total_layups']);
        $this->assertEquals(3, $report['summary']['total_layers']);
    }

    // ── execute() ──────────────────────────────────────────────────────────

    /** @test */
    public function execute_creates_new_layups_and_layers()
    {
        $data = ['layups' => [['name' => 'New CLT', 'layers' => [
            ['layer_order' => 1, 'thickness' => 20, 'width' => 100, 'angle' => 0],
        ]]]];

        $report  = $this->service->scan($this->supplier->id, $data);
        $summary = $this->service->execute($this->supplier->id, $report['import_token'], 'skip');

        $this->assertEquals(1, $summary['layups_created']);
        $this->assertEquals(1, $summary['layers_created']);
        $this->assertDatabaseHas('clt_layups', ['name' => 'New CLT']);
        $this->assertDatabaseHas('clt_layers', ['layer_order' => 1, 'thickness' => 20]);
    }

    /** @test */
    public function execute_with_overwrite_updates_conflicting_layer()
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'CLT-X']);
        $layer = CltLayer::factory()->create([
            'layup_id' => $layup->id, 'layer_order' => 1,
            'thickness' => 20, 'width' => 100, 'angle' => 0,
        ]);

        $data   = ['layups' => [['name' => 'CLT-X', 'layers' => [
            ['layer_order' => 1, 'thickness' => 99, 'width' => 100, 'angle' => 0],
        ]]]];
        $report  = $this->service->scan($this->supplier->id, $data);
        $summary = $this->service->execute($this->supplier->id, $report['import_token'], 'overwrite');

        $this->assertEquals(1, $summary['layers_updated']);
        $this->assertDatabaseHas('clt_layers', ['id' => $layer->id, 'thickness' => 99]);
    }

    /** @test */
    public function execute_with_skip_keeps_existing_layer_unchanged()
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id, 'name' => 'CLT-Y']);
        $layer = CltLayer::factory()->create([
            'layup_id' => $layup->id, 'layer_order' => 1,
            'thickness' => 20, 'width' => 100, 'angle' => 0,
        ]);

        $data   = ['layups' => [['name' => 'CLT-Y', 'layers' => [
            ['layer_order' => 1, 'thickness' => 50, 'width' => 100, 'angle' => 0],
        ]]]];
        $report  = $this->service->scan($this->supplier->id, $data);
        $summary = $this->service->execute($this->supplier->id, $report['import_token'], 'skip');

        $this->assertEquals(1, $summary['layers_skipped']);
        $this->assertDatabaseHas('clt_layers', ['id' => $layer->id, 'thickness' => 20]);
    }

    /** @test */
    public function execute_throws_exception_for_invalid_token()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Import data expired or token invalid.');

        $this->service->execute($this->supplier->id, 'invalid-token-xyz', 'skip');
    }

    /** @test */
    public function execute_invalidates_cache_token_after_use()
    {
        $data   = ['layups' => []];
        $report = $this->service->scan($this->supplier->id, $data);
        $token  = $report['import_token'];

        $this->service->execute($this->supplier->id, $token, 'skip');

        $this->assertNull(Cache::get("import_data_{$token}"));
    }
}
