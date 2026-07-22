<?php

namespace Tests\Feature;

use Mupy\BusinessCentral\BusinessCentralServiceProvider;
use Orchestra\Testbench\TestCase;

class BusinessCentralClientTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [BusinessCentralServiceProvider::class];
    }

    /** @test */
    public function it_work()
    {
        $this->assertTrue(true);
    }
}
