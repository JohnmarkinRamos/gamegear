<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('gamegear:make-admin {username} {--revoke}', function (): int {
    $user = User::where('username', Str::lower($this->argument('username')))->first();
    if (! $user) {
        $this->error('No account found for that username.');

        return self::FAILURE;
    }

    $isAdmin = ! $this->option('revoke');
    $user->forceFill(['is_admin' => $isAdmin])->save();
    $this->info($isAdmin ? "{$user->username} can now access the admin panel." : "Admin access removed from {$user->username}.");

    return self::SUCCESS;
})->purpose('Grant or revoke admin access for an existing user account');

Artisan::command('gamegear:create-admin {username=gearadmin}', function (): int {
    $username = Str::lower($this->argument('username'));
    if (User::where('username', $username)->exists()) {
        $this->error('That username already exists. Choose another admin username.');

        return self::FAILURE;
    }

    $password = bin2hex(random_bytes(20));
    $user = User::create(['name' => 'GameGear Administrator', 'username' => $username, 'password' => $password]);
    $user->forceFill(['is_admin' => true])->save();

    $this->info('Dedicated admin account created. Save this password; it is shown only once.');
    $this->line("Username: {$username}");
    $this->line("Temporary password: {$password}");

    return self::SUCCESS;
})->purpose('Create a dedicated admin account with a one-time password');

