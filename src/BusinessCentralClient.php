<?php

namespace Mupy\BusinessCentral;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class BusinessCentralClient
{
    protected $config;
    protected $http;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->http = new Client([
            'base_uri' => $this->config['api_url'] . $this->config['environment'] . '/' . $this->config['tenant_id'] . '/',
        ]);
    }

    protected function getAccessToken()
    {
        return Cache::remember('bc_access_token', 3500, function () {
            $response = $this->http->post('https://login.microsoftonline.com/' . $this->config['tenant_id'] . '/oauth2/v2.0/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'scope' => 'https://api.businesscentral.dynamics.com/.default',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'];
        });
    }

    public function get($endpoint, $query = [])
    {
        $response = $this->http->get($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Accept' => 'application/json',
            ],
            'query' => $query,
        ]);

        return json_decode($response->getBody(), true);
    }

    // Example method
    public function getCustomers()
    {
        $company = $this->config['company_id'];
        return $this->get("companies({$company})/customers");
    }
}
