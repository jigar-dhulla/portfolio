<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Render pages without a compiled Vite manifest, so tests do not depend on `npm run build`.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
