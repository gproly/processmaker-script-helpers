<?php

namespace ProcessMaker\ScriptHelpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Base API client for making HTTP requests to ProcessMaker API
 */
class ApiClient
{
    private static $client = null;
    private static $apiToken = null;
    private static $apiHost = null;

    /**
     * Get the HTTP client instance
     *
     * @return Client
     */
    private static function getClient()
    {
        if (self::$client === null) {
            self::$apiToken = getenv('API_TOKEN');
            self::$apiHost = getenv('API_HOST');
            
            if (!self::$apiHost) {
                throw new \RuntimeException('API_HOST environment variable is not set');
            }
            
            if (!self::$apiToken) {
                throw new \RuntimeException('API_TOKEN environment variable is not set');
            }

            self::$client = new Client([
                'base_uri' => self::$apiHost,
                'verify' => getenv('API_SSL_VERIFY') !== '0',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . self::$apiToken
                ]
            ]);
        }

        return self::$client;
    }

    /**
     * Make a GET request
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public static function get($endpoint, array $params = [])
    {
        try {
            $client = self::getClient();
            $response = $client->get($endpoint, ['query' => $params]);
            $body = json_decode($response->getBody()->getContents(), true);
            
            // ProcessMaker API wraps responses in 'data' key
            return $body['data'] ?? $body;
        } catch (GuzzleException $e) {
            throw new \Exception('API request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Make a POST request
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public static function post($endpoint, array $data = [])
    {
        try {
            $client = self::getClient();
            $response = $client->post($endpoint, ['json' => $data]);
            $body = json_decode($response->getBody()->getContents(), true);
            
            return $body['data'] ?? $body;
        } catch (GuzzleException $e) {
            throw new \Exception('API request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Build query parameters from filters
     *
     * @param array $filters
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public static function buildQueryParams(array $filters = [], $perPage = 10, $page = 1)
    {
        $params = [];
        
        // Add pagination
        if ($perPage > 0) {
            $params['per_page'] = $perPage;
            $params['page'] = $page;
        }
        
        // Add filters
        foreach ($filters as $key => $value) {
            if ($key === 'order_by' || $key === 'order_direction') {
                $params[$key] = $value;
            } elseif ($value !== null && $value !== '') {
                $params['filter'][$key] = $value;
            }
        }
        
        return $params;
    }
}

