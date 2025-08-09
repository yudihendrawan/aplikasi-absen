<?php

use App\Exports\AttendancesExport;
use App\Exports\LeavesExport;
use App\Exports\SchedulesExport;
use App\Exports\StoresExport;
use App\Exports\UsersExport;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');


Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::middleware(['auth'])->group(function () {

    Route::middleware('role:admin')->group(function () {
        Route::get('/leaves/export', function (Request $request) {
            return Excel::download(new LeavesExport($request), 'izin_sales.xlsx');
        })->name('leaves.export');

        Route::get('/users/export', function (Request $request) {
            return Excel::download(new UsersExport($request), 'data_users.xlsx');
        })->name('users.export');

        Route::get('/attendances/export', function (Request $request) {
            return Excel::download(new AttendancesExport($request), 'data_attendances.xlsx');
        })->name('attendances.export');

        Route::get('/stores/export', function (Request $request) {
            return Excel::download(new StoresExport($request), 'data_stores.xlsx');
        })->name('stores.export');

        Route::get('/schedules/export', function (Request $request) {
            return Excel::download(new SchedulesExport($request), 'data_schedules.xlsx');
        })->name('schedules.export');


        Route::patch('/leaves/approve/{leave}', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::patch('/leaves/reject/{leave}', [LeaveController::class, 'reject'])->name('leaves.reject');


        Route::resource('users', UserController::class);
        Route::resource('stores', StoreController::class);
    });

    Route::resource('schedules', ScheduleController::class);
    Route::resource('leaves', LeaveController::class);
    Route::resource('attendances', AttendanceController::class);

    // web.php
    Route::get('/attendances/create-presence/{visit}', [AttendanceController::class, 'createPresence'])->name('attendances.createPresence');
    Route::get('/attendances/calendar', [AttendanceController::class, 'calendar'])->name('attendances.calendar');

    Route::redirect('settings', 'settings/profile');
    Route::get('/schedules/{schedule}/visits', [ScheduleController::class, 'showVisits']);

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__ . '/auth.php';
