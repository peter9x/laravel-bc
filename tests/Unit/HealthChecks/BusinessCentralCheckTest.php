<?php

namespace Tests\Unit\HealthChecks;

use Mockery;
use Mupy\BusinessCentral\ApiClient;
use Mupy\BusinessCentral\BusinessCentralClient;
use Mupy\BusinessCentral\BusinessCentralServiceProvider;
use Mupy\BusinessCentral\HealthChecks\BusinessCentralCheck;
use Orchestra\Testbench\TestCase;
use RuntimeException;
use Spatie\Health\Enums\Status;

class BusinessCentralCheckTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [BusinessCentralServiceProvider::class];
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function it_passes_when_every_configured_connection_authenticates()
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $apiClient->shouldReceive('getBearer')->once()->andReturn('a-token');

        $businessCentral = Mockery::mock(BusinessCentralClient::class);
        $businessCentral->shouldReceive('getClient')->with('default')->once()->andReturn($apiClient);
        $this->app->instance(BusinessCentralClient::class, $businessCentral);

        $result = (new BusinessCentralCheck)->run();

        $this->assertTrue($result->status->equals(Status::ok()));
    }

    /** @test */
    public function it_fails_when_a_connection_cannot_authenticate()
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $apiClient->shouldReceive('getBearer')->once()->andThrow(new RuntimeException('invalid credentials'));

        $businessCentral = Mockery::mock(BusinessCentralClient::class);
        $businessCentral->shouldReceive('getClient')->with('default')->once()->andReturn($apiClient);
        $this->app->instance(BusinessCentralClient::class, $businessCentral);

        $result = (new BusinessCentralCheck)->run();

        $this->assertTrue($result->status->equals(Status::failed()));
        $this->assertStringContainsString('invalid credentials', $result->notificationMessage);
    }

    /** @test */
    public function it_fails_when_no_connections_are_configured()
    {
        config(['businesscentral.connections' => []]);

        $result = (new BusinessCentralCheck)->run();

        $this->assertTrue($result->status->equals(Status::failed()));
    }

    /** @test */
    public function it_can_be_limited_to_specific_connections()
    {
        $apiClient = Mockery::mock(ApiClient::class);
        $apiClient->shouldReceive('getBearer')->once()->andReturn('a-token');

        $businessCentral = Mockery::mock(BusinessCentralClient::class);
        $businessCentral->shouldReceive('getClient')->with('secondary')->once()->andReturn($apiClient);
        $this->app->instance(BusinessCentralClient::class, $businessCentral);

        $result = (new BusinessCentralCheck)->connections(['secondary'])->run();

        $this->assertTrue($result->status->equals(Status::ok()));
    }
}
