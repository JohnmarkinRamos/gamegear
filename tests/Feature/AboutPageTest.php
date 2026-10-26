<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_guest_can_view_about_page_and_find_about_navigation(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk()
            ->assertViewIs('about')
            ->assertSee('Made for the')
            ->assertSee('Better gear.')
            ->assertSee('Meet Momo, our little pixel cat.')
            ->assertSee('cozy-gaming club')
            ->assertSee('About Us')
            ->assertSee('nav-momo-card')
            ->assertSee('Momo’s nook');
    }
}
