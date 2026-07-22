<?php

namespace Mupy\BusinessCentral\HealthChecks;

use Mupy\BusinessCentral\BusinessCentralClient;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Throwable;

class BusinessCentralCheck extends Check
{
    /** @var array<int, string>|null */
    protected ?array $connections = null;

    /**
     * Limit the check to specific connection names instead of every connection
     * configured in `config('businesscentral.connections')`.
     *
     * @param  array<int, string>  $connections
     */
    public function connections(array $connections): static
    {
        $this->connections = $connections;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        $connections = $this->connections ?? array_keys(config('businesscentral.connections', []));

        if (empty($connections)) {
            return $result->failed('No Business Central connections are configured.');
        }

        $client = app(BusinessCentralClient::class);
        $errors = [];

        foreach ($connections as $connection) {
            try {
                $client->getClient($connection)->getBearer();
            } catch (Throwable $exception) {
                $errors[$connection] = $exception->getMessage();
            }
        }

        if (empty($errors)) {
            return $result->ok(sprintf(
                'Successfully authenticated with Business Central (%s).',
                implode(', ', $connections)
            ));
        }

        $summary = collect($errors)
            ->map(fn (string $message, string $connection) => "{$connection}: {$message}")
            ->implode(' | ');

        return $result->failed("Business Central authentication failed - {$summary}");
    }
}
