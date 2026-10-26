<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthPageTest extends TestCase
{
    public function test_customer_login_uses_the_gamegear_split_auth_layout(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSee('auth-frame')
            ->assertSee('Welcome Back')
            ->assertSee('Build your')
            ->assertSee('perfect setup.')
            ->assertSee('momo-note-groom')
            ->assertSee('name="username"', false)
            ->assertSee('name="password"', false)
            ->assertSee(route('about'));
    }

    public function test_registration_and_admin_login_use_the_shared_promo_panel(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create Account')
            ->assertSee('FIND YOUR NEXT FAVORITE')
            ->assertSee('momo-note-groom')
            ->assertSee('name="password_confirmation"', false);

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Admin Sign In')
            ->assertSee('STORE MANAGEMENT')
            ->assertSee('name="username"', false);
    }
}
