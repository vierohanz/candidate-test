<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supplier = Supplier::create(['name' => 'Test Supplier']);
    }

    /** @test */
    public function it_can_auto_import_clean_data()
    {
        $payload = [
            'layups' => [
                [
                    'name' => 'New Layup',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 20,
                            'width' => 100,
                            'angle' => 0,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson("/api/v1/suppliers/{$this->supplier->id}/import/scan", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.auto_confirmed', true);

        $this->assertDatabaseHas('clt_layups', ['name' => 'New Layup']);
        $this->assertDatabaseHas('clt_layers', ['layer_order' => 1, 'thickness' => 20]);
    }

    /** @test */
    public function it_detects_value_conflicts_in_layers()
    {
        // 1. Setup existing data
        $layup = CltLayup::create(['supplier_id' => $this->supplier->id, 'name' => 'Manufacture']);
        CltLayer::create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20, // Existing is 20
            'width' => 100,
            'angle' => 0,
        ]);

        // 2. Import data with DIFFERENT thickness (25)
        $payload = [
            'layups' => [
                [
                    'name' => 'Manufacture',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 25, // Change to 25
                            'width' => 100,
                            'angle' => 0,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson("/api/v1/suppliers/{$this->supplier->id}/import/scan", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Conflicts detected. Review before confirming.')
            ->assertJsonFragment(['layer_order' => 1])
            ->assertJsonFragment(['thickness' => 20]) // Existing
            ->assertJsonFragment(['thickness' => 25]); // Incoming
    }

    /** @test */
    public function it_can_resolve_conflicts_with_overwrite_strategy()
    {
        // 1. Setup existing data (20mm)
        $layup = CltLayup::create(['supplier_id' => $this->supplier->id, 'name' => 'Manufacture']);
        $layer = CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 20, 'width' => 100, 'angle' => 0]);

        // 2. Scan to get token
        $payload = ['layups' => [['name' => 'Manufacture', 'layers' => [['layer_order' => 1, 'thickness' => 30, 'width' => 100, 'angle' => 0]]]]];
        $scanResponse = $this->postJson("/api/v1/suppliers/{$this->supplier->id}/import/scan", $payload);
        $token = $scanResponse->json('data.import_token');

        // 3. Confirm with OVERWRITE
        $confirmResponse = $this->postJson("/api/v1/suppliers/{$this->supplier->id}/import/confirm", [
            'import_token' => $token,
            'strategy' => 'overwrite',
        ]);

        $confirmResponse->assertStatus(200);
        $this->assertDatabaseHas('clt_layers', [
            'id' => $layer->id,
            'thickness' => 30, // Must be updated to 30
        ]);

        // 4. Token should be deleted from cache
        $this->assertNull(Cache::get("import_data_{$token}"));
    }

    /** @test */
    public function it_can_resolve_conflicts_with_skip_strategy()
    {
        // 1. Setup existing data (20mm)
        $layup = CltLayup::create(['supplier_id' => $this->supplier->id, 'name' => 'Manufacture']);
        $layer = CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 20, 'width' => 100, 'angle' => 0]);

        // 2. Scan to get token
        $payload = ['layups' => [['name' => 'Manufacture', 'layers' => [['layer_order' => 1, 'thickness' => 35, 'width' => 100, 'angle' => 0]]]]];
        $scanResponse = $this->postJson("/api/v1/suppliers/{$this->supplier->id}/import/scan", $payload);
        $token = $scanResponse->json('data.import_token');

        // 3. Confirm with SKIP
        $this->postJson("/api/v1/suppliers/{$this->supplier->id}/import/confirm", [
            'import_token' => $token,
            'strategy' => 'skip',
        ]);

        $this->assertDatabaseHas('clt_layers', [
            'id' => $layer->id,
            'thickness' => 20, // Must stay 20
        ]);
    }
}
