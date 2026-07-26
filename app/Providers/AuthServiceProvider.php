<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function ($user)
        {
            return $user->hakAkses == '1';
        });

        Gate::define('isKasir', function ($user)
        {
            return $user->hakAkses == '2';
        });

        Gate::define('isGudang', function ($user)
        {
            return $user->hakAkses == '3';
        });

        Gate::define('isAdminKasir', function ($user)
        {
            return $user->hakAkses == '1' || $user->hakAkses == '2';
        });

        Gate::define('isAdminGudang', function ($user)
        {
            return $user->hakAkses == '1' || $user->hakAkses == '3';
        });

        // Teknisi maintenance (scan barcode, input perawatan barang)
        Gate::define('isTeknisi', function ($user)
        {
            return $user->hakAkses == '4';
        });

        // Akses maintenance: admin, gudang, atau teknisi
        Gate::define('isAdminGudangTeknisi', function ($user)
        {
            return in_array($user->hakAkses, ['1', '3', '4']);
        });

        // Gates untuk approval usulan pembelian
        Gate::define('canApproveLevel1', function ($user) {
            return $user->approval_level == 1 || $user->hakAkses == '1';
        });

        Gate::define('canApproveLevel2', function ($user) {
            return $user->approval_level == 2 || $user->hakAkses == '1';
        });

        Gate::define('canApproveLevel3', function ($user) {
            return $user->approval_level == 3 || $user->hakAkses == '1';
        });

        Gate::define('canApproveLevel4', function ($user) {
            return $user->approval_level == 4 || $user->hakAkses == '1';
        });

        Gate::define('isApprover', function ($user) {
            return !empty($user->approval_level) || $user->hakAkses == '1';
        });
    }
}
