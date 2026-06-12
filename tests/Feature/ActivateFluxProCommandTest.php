<?php

test('flux pro activation requires credentials', function () {
    config([
        'services.flux.username' => '',
        'services.flux.license_key' => '',
    ]);

    $this->artisan('app:activate-flux-pro')
        ->expectsOutputToContain('Set FLUX_USERNAME and FLUX_LICENSE_KEY')
        ->assertFailed();
});

test('flux pro activation can validate configured credentials', function () {
    config([
        'services.flux.username' => 'flux@example.com',
        'services.flux.license_key' => 'test-license-key',
    ]);

    $this->artisan('app:activate-flux-pro --dry-run')
        ->expectsOutputToContain('Flux Pro credentials are configured.')
        ->assertSuccessful();
});
