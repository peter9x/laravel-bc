<?php

namespace Mupy\BusinessCentral;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Mupy\BusinessCentral\EndPoint\ApiEndPoint;
use InvalidArgumentException;
use RuntimeException;

class ApiClient
{
    private Client $http;
    private string $tenant;
    private string $clientId;
    private string $clientSecret;
    private ?string $database = null;
    private ?string $company = null;

    public function __construct(array $connection, string $apiBaseUrl)
    {
        $requiredKeys = ['tenant_id', 'client_id', 'client_secret', 'environment'];

        foreach ($requiredKeys as $key) {
            if (empty($connection[$key])) {
                throw new InvalidArgumentException("Connection array must include '{$key}'.");
            }
        }

        $this->tenant = $connection['tenant_id'];
        $this->clientId = $connection['client_id'];
        $this->clientSecret = $connection['client_secret'];
        $this->database = $connection['environment'];
        $this->company = $connection['company_id'] ?? null;

        $this->http = new Client([
            'base_uri' => rtrim($apiBaseUrl, '/') . '/',
            'timeout' => 10.0,
        ]);
    }

    /**
     * Return a clone of the client using a specific company.
     */
    public function useCompany(string $companyId): self
    {
        if (empty($companyId)) {
            throw new InvalidArgumentException("Company ID cannot be empty.");
        }

        $clone = clone $this;
        $clone->company = $companyId;

        return $clone;
    }

    /**
     * Set the database to use.
     */
    public function selectDB(string $database): self
    {
        if (empty($database)) {
            throw new InvalidArgumentException("Database cannot be empty.");
        }

        $this->database = $database;
        return $this;
    }

    /**
     * Retrieve a cached access token for the tenant/client combination.
     */
    public function getBearer(): string
    {
        $cacheKey = "bc_access_token_{$this->tenant}_{$this->clientId}";

        return Cache::remember($cacheKey, 3500, function () {
            $tokenUrl = "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token";

            $response = $this->http->post($tokenUrl, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'https://api.businesscentral.dynamics.com/.default',
                ],
            ]);

            $data = json_decode((string)$response->getBody(), true);

            if (empty($data['access_token'])) {
                $errorMsg = $data['error_description'] ?? 'Unknown error while fetching access token';
                throw new RuntimeException("Failed to obtain access token: {$errorMsg}");
            }

            return $data['access_token'];
        });
    }

    /**
     * Perform a GET request to a Business Central API endpoint.
     */
    public function getRequest(ApiEndPoint $target, array $query = []): ApiResponse
    {
        if (empty($this->database)) {
            throw new RuntimeException("Database must be selected before making requests.");
        }

        $endpoint = $this->buildEndpoint($target);

        $response = $this->http->get($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getBearer(),
                'Accept' => 'application/json',
            ],
            'query' => $query,
        ]);

        return new ApiResponse($response);
    }

    public function get(string $target, mixed $filters = null, $full = false): ApiResponse
    {
        if (class_exists($target)) {
            $endpoint = new $target();
            if ($full === false) {
                $endpoint->select($endpoint::$select);
            }
        } else {
            $endpoint = ApiEndPoint::static($target);
        }
        if ($filters && is_array($filters)) {
            $endpoint->addFilters($filters);
        }
        return $this->getRequest($endpoint);
    }

    /**
     * Build the full API endpoint URL.
     */
    private function buildEndpoint(ApiEndPoint $target): string
    {
        $path = sprintf(
            "%s/api/%s/%s/%s%s%s",
            $this->database,
            $target->publisher(),
            $target->group(),
            $target->version(),
            $this->company ? "/companies({$this->company})" : '',
            "/{$target->getPath()}"
        );

        return $path;
    }
}
