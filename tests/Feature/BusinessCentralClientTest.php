<?php

namespace Tests\Feature;

use Orchestra\Testbench\TestCase;

class BusinessCentralClientTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [\Mupy\BusinessCentral\BusinessCentralServiceProvider::class];
    }

    /** @test */
    public function it_work()
    {
        $this->assertTrue(true);
    }
}
