<?php

use App\Models\User;

describe('Admin User Management', function () {
    describe('Promote Writer to Admin', function () {
        it('allows super admin to promote writer to admin', function () {
            $superAdmin = User::factory()->create(['role' => 'super_admin']);
            $writer = User::factory()->create(['role' => 'writer']);

            $response = $this->actingAs($superAdmin)
                ->post(route('admin.promote', $writer));

            $response->assertRedirect();
            $response->assertSessionHas('success', $writer->name . ' promoted to admin.');

            $writer->refresh();
            expect($writer->role)->toBe('admin');
        });

        it('prevents normal admin from promoting writer', function () {
            $admin = User::factory()->create(['role' => 'admin']);
            $writer = User::factory()->create(['role' => 'writer']);

            $response = $this->actingAs($admin)
                ->post(route('admin.promote', $writer));

            $response->assertStatus(403);

            $writer->refresh();
            expect($writer->role)->toBe('writer');
        });

        it('shows promoted user in admin list and removes from writer list', function () {
            $superAdmin = User::factory()->create(['role' => 'super_admin']);
            $writer = User::factory()->create(['role' => 'writer']);

            // Before promotion
            $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));
            $response->assertViewHas('writers');
            $response->assertViewHas('admins');
            
            expect($response->viewData('writers')->pluck('id'))->toContain($writer->id);
            expect($response->viewData('admins')->pluck('id'))->not->toContain($writer->id);

            // Promote
            $this->actingAs($superAdmin)->post(route('admin.promote', $writer));

            // After promotion
            $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));
            expect($response->viewData('writers')->pluck('id'))->not->toContain($writer->id);
            expect($response->viewData('admins')->pluck('id'))->toContain($writer->id);
        });
    });

    describe('Demote Admin to Writer', function () {
        it('allows super admin to demote admin to writer', function () {
            $superAdmin = User::factory()->create(['role' => 'super_admin']);
            $admin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($superAdmin)
                ->post(route('admin.demote', $admin));

            $response->assertRedirect();
            $response->assertSessionHas('success', $admin->name . ' demoted to writer.');

            $admin->refresh();
            expect($admin->role)->toBe('writer');
        });

        it('prevents normal admin from demoting other admin', function () {
            $admin1 = User::factory()->create(['role' => 'admin']);
            $admin2 = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($admin1)
                ->post(route('admin.demote', $admin2));

            $response->assertStatus(403);

            $admin2->refresh();
            expect($admin2->role)->toBe('admin');
        });

        it('prevents super admin from demoting themselves', function () {
            $superAdmin = User::factory()->create(['role' => 'super_admin']);

            $response = $this->actingAs($superAdmin)
                ->post(route('admin.demote', $superAdmin));

            $response->assertRedirect();
            $response->assertSessionHas('error', 'You cannot demote yourself.');

            $superAdmin->refresh();
            expect($superAdmin->role)->toBe('super_admin');
        });

        it('prevents super admin from demoting other super admins', function () {
            $superAdmin1 = User::factory()->create(['role' => 'super_admin']);
            $superAdmin2 = User::factory()->create(['role' => 'super_admin']);

            $response = $this->actingAs($superAdmin1)
                ->post(route('admin.demote', $superAdmin2));

            $response->assertRedirect();
            $response->assertSessionHas('error', 'Super Admin users cannot be demoted.');

            $superAdmin2->refresh();
            expect($superAdmin2->role)->toBe('super_admin');
        });

        it('shows demoted user in writer list and removes from admin list', function () {
            $superAdmin = User::factory()->create(['role' => 'super_admin']);
            $admin = User::factory()->create(['role' => 'admin']);

            // Before demotion
            $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));
            expect($response->viewData('admins')->pluck('id'))->toContain($admin->id);
            expect($response->viewData('writers')->pluck('id'))->not->toContain($admin->id);

            // Demote
            $this->actingAs($superAdmin)->post(route('admin.demote', $admin));

            // After demotion
            $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));
            expect($response->viewData('admins')->pluck('id'))->not->toContain($admin->id);
            expect($response->viewData('writers')->pluck('id'))->toContain($admin->id);
        });
    });

    describe('Dashboard Visibility', function () {
        it('shows admin management section only to super admin', function () {
            $superAdmin = User::factory()->create(['role' => 'super_admin']);
            $admin = User::factory()->create(['role' => 'admin']);
            $writer = User::factory()->create(['role' => 'writer']);

            // Super Admin sees admin and writer lists
            $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));
            $response->assertViewHas('admins');
            $response->assertViewHas('writers');
            expect($response->viewData('admins')->count())->toBeGreaterThan(0);

            // Normal Admin sees writer list but not admin list (empty)
            $response = $this->actingAs($admin)->get(route('admin.dashboard'));
            $response->assertViewHas('writers');
            $response->assertViewHas('admins');
            expect($response->viewData('admins')->count())->toBe(0);

            // Writer cannot see these sections (gets empty collections)
            $response = $this->actingAs($writer)->get(route('admin.dashboard'));
            expect($response->viewData('writers')->count())->toBe(0);
        });

        it('shows current super admin in admin list', function () {
            $superAdmin = User::factory()->create(['role' => 'super_admin']);
            $otherAdmin = User::factory()->create(['role' => 'admin']);

            $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));
            
            $response->assertViewHas('admins');
            $admins = $response->viewData('admins');
            
            // Other admin should be in the list
            expect($admins->pluck('id'))->toContain($otherAdmin->id);
        });
    });
});

