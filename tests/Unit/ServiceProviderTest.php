<?php

namespace Tests\Unit;

use Mupy\BusinessCentral\BusinessCentralServiceProvider;
use Mupy\BusinessCentral\Facades\BusinessCentral;
use Orchestra\Testbench\TestCase;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [BusinessCentralServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'BusinessCentral' => BusinessCentral::class,
        ];
    }

    /** @test */
    public function it_registers_the_business_central_client()
    {
        $client = BusinessCentral::getClient();
        $this->assertNotNull($client);
    }
}
