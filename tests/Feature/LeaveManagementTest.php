<?php

use App\Models\LeaveRequest;
use App\Models\LeaveSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::firstOrCreate(
        ['email' => 'admin_test@familymart.com'],
        [
            'nip' => 'ADM999999',
            'name' => 'Admin Testing',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'account_status' => 'Aktif',
        ]
    );

    $this->employee = User::firstOrCreate(
        ['email' => 'employee_test@familymart.com'],
        [
            'nip' => 'EMP999999',
            'name' => 'Employee Testing',
            'role' => 'user',
            'password' => bcrypt('password'),
            'account_status' => 'Aktif',
        ]
    );

    LeaveSetting::updateOrCreate(
        ['id' => 1],
        ['max_annual_leave_days' => 12]
    );
});

test('unauthenticated users are redirected to login', function () {
    $this->get('/admin/leaves')->assertRedirect('/login');
    $this->get('/employee/leaves')->assertRedirect('/login');
});

test('employee cannot access admin leaves page', function () {
    $this->actingAs($this->employee)
        ->get('/admin/leaves')
        ->assertRedirect('/employee/dashboard');
});

test('admin can view leaves management page and metrics', function () {
    $response = $this->actingAs($this->admin)->get('/admin/leaves');

    $response->assertStatus(200);
    $response->assertSee('Kelola Perizinan Karyawan');
    $response->assertSee('Batas Cuti');
});

test('admin can update annual leave days setting', function () {
    $response = $this->actingAs($this->admin)
        ->put('/admin/leaves/settings', [
            'max_annual_leave_days' => 15,
        ]);

    $response->assertRedirect('/admin/leaves');
    $response->assertSessionHas('success');

    expect(LeaveSetting::getMaxDays())->toBe(15);
});

test('admin can approve a pending leave request', function () {
    $leave = LeaveRequest::create([
        'user_id' => $this->employee->id,
        'type' => 'CUTI',
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(6)->toDateString(),
        'duration_days' => 2,
        'reason' => 'Keperluan keluarga',
        'status' => 'PENDING',
    ]);

    $response = $this->actingAs($this->admin)
        ->post("/admin/leaves/{$leave->id}/approve");

    $response->assertRedirect('/admin/leaves');
    $response->assertSessionHas('success');

    $leave->refresh();
    expect($leave->status)->toBe('APPROVED');
    expect($leave->reviewed_by)->toBe($this->admin->id);
    expect($leave->reviewed_at)->not->toBeNull();
});

test('admin can reject a pending leave request with a reason', function () {
    $leave = LeaveRequest::create([
        'user_id' => $this->employee->id,
        'type' => 'IZIN',
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'duration_days' => 1,
        'reason' => 'Urusan mendadak',
        'status' => 'PENDING',
    ]);

    $response = $this->actingAs($this->admin)
        ->post("/admin/leaves/{$leave->id}/reject", [
            'reject_reason' => 'Mohon lampirkan dokumen pendukung.',
        ]);

    $response->assertRedirect('/admin/leaves');
    $response->assertSessionHas('success');

    $leave->refresh();
    expect($leave->status)->toBe('REJECTED');
    expect($leave->reject_reason)->toBe('Mohon lampirkan dokumen pendukung.');
});

test('employee can view their leave quota and history', function () {
    $response = $this->actingAs($this->employee)->get('/employee/leaves');

    $response->assertStatus(200);
    $response->assertSee('Pengajuan Perizinan');
    $response->assertSee('SISA CUTI TAHUNAN');
});

test('employee can submit a new leave request', function () {
    $response = $this->actingAs($this->employee)
        ->post('/employee/leaves', [
            'type' => 'IZIN',
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'reason' => 'Perpanjangan SIM di Samsat keliling',
        ]);

    $response->assertRedirect('/employee/leaves');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $this->employee->id,
        'type' => 'IZIN',
        'status' => 'PENDING',
        'reason' => 'Perpanjangan SIM di Samsat keliling',
    ]);
});

test('admin dashboard displays leave statistics and recent leaves section', function () {
    $response = $this->actingAs($this->admin)->get('/admin/dashboard');

    $response->assertStatus(200);
    $response->assertSee('PERIZINAN PENDING');
    $response->assertSee('SEDANG CUTI / IZIN HARI INI');
    $response->assertSee('Pengajuan Perizinan Terbaru');
});
