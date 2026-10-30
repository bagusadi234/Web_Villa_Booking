<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_application_routes_are_defined(): void
    {
        $this->assertTrue(
            file_exists(base_path('routes/web.php'))
        );
    }
}
