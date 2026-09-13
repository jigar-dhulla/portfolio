<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_the_portfolio_renders_every_section(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Backend engineer who loves to build');
        $response->assertSee('id="about"', false);
        $response->assertSee('id="experience"', false);
        $response->assertSee('id="projects"', false);
        $response->assertSee('id="open-source"', false);
        $response->assertSee('id="talks"', false);
        $response->assertSee('id="contact"', false);
    }

    public function test_the_availability_badge_is_hidden_until_it_is_configured(): void
    {
        config(['portfolio.availability' => null]);

        $this->get(route('home'))->assertDontSee('hero__badge', false);

        config(['portfolio.availability' => 'Open to new work']);

        $this->get(route('home'))->assertSee('Open to new work');
    }

    public function test_the_stat_row_can_be_switched_off(): void
    {
        config(['portfolio.show_stats' => false]);

        $this->get(route('home'))->assertDontSee('Years of experience');
    }
}
