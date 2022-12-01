<?php

namespace App\Service\Api;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NbpApiService
{
    private string $apiUrl;

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiUrl = 'http://api.nbp.pl/api';
    }

    public function get(string $endpoint): array
    {
        $response = $this->client->request('GET', $this->apiUrl . $endpoint, [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        return $response->toArray();
    }

    public function getTableExchangeRates(string $tableName): array
    {
        return $this->get('/exchangerates/tables/' . $tableName);
    }

}