<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use App\Services\BusinessMergeService;
use App\Services\BusinessNormalizer;
use App\Services\DuplicateDetectionService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DataVaultWorkflowTest extends TestCase
{
    use DatabaseMigrations;

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_open_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Operational dashboard', false);
    }

    public function test_duplicate_detection_and_merge_workflow(): void
    {
        $normalizer = app(BusinessNormalizer::class);

        $first = Business::query()->create($normalizer->prepareBusinessPayload([
            'business_name' => 'Honeybee Digital',
            'area' => 'Sector 18',
            'city' => 'Noida',
            'mobile_no' => '9876543210',
            'category' => 'Marketing',
            'sub_category' => 'Agency',
            'address' => 'Tower A',
        ]));

        $second = Business::query()->create($normalizer->prepareBusinessPayload([
            'business_name' => 'Honeybee Digital ',
            'area' => 'Sector-18',
            'city' => 'NOIDA',
            'mobile_no' => '9911223344',
            'category' => 'Marketing',
            'sub_category' => 'SEO',
            'address' => 'Tower A',
        ]));

        app(DuplicateDetectionService::class)->refreshFlags();

        $this->assertFalse($first->fresh()->is_duplicate);
        $this->assertTrue($second->fresh()->is_duplicate);

        $master = app(BusinessMergeService::class)->merge([$first->id, $second->id], $first->id);

        $this->assertFalse($master->fresh()->is_duplicate);
        $this->assertStringContainsString('9876543210', $master->fresh()->mobile_no);
        $this->assertStringContainsString('9911223344', $master->fresh()->mobile_no);
        $this->assertSoftDeleted('businesses', [
            'id' => $second->id,
            'merged_into' => $first->id,
        ]);
    }
}
