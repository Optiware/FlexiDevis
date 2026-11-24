<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Les pages se rendent sans les fichiers compilés par Vite (pas de npm run build en test).
        $this->withoutVite();
    }
}
