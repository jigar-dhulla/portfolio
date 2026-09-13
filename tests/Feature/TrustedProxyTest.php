<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_forwarded_https_request_generates_https_urls(): void
    {
        $response = $this->withHeaders(['X-Forwarded-Proto' => 'https'])
            ->get(route('login'))
            ->assertOk();

        // The form action is what matters: an http:// action on an https:// page
        // is mixed active content, and browsers refuse to submit a password to it.
        $this->assertStringContainsString('action="https://', $response->getContent());
    }

    public function test_an_unforwarded_request_is_left_alone(): void
    {
        $response = $this->get(route('login'))->assertOk();

        $this->assertStringContainsString('action="http://', $response->getContent());
    }
}
