<?php

namespace ProcessMaker\ScriptHelpers;

/**
 * Helper class to list ProcessRequests in script tasks
 * Uses ProcessMaker API instead of Eloquent models
 */
class RequestLister
{
    /**
     * Get all requests
     *
     * @param array $filters Optional filters (status, process_id, user_id, etc.)
     * @param int $perPage Number of items per page
     * @param int $page Page number
     * @return array
     */
    public static function all(array $filters = [], int $perPage = 10, int $page = 1)
    {
        $params = ApiClient::buildQueryParams($filters, $perPage, $page);
        return ApiClient::get('/requests', $params);
    }

    /**
     * Get a single request by ID
     *
     * @param string|int $id Request ID
     * @return array|null
     */
    public static function find($id)
    {
        try {
            return ApiClient::get("/requests/{$id}");
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get requests by status
     *
     * @param string $status Status (ACTIVE, COMPLETED, etc.)
     * @param int $perPage
     * @return array
     */
    public static function byStatus(string $status, int $perPage = 10)
    {
        return self::all(['status' => $status], $perPage);
    }

    /**
     * Get requests by process ID
     *
     * @param string|int $processId Process ID
     * @param int $perPage
     * @return array
     */
    public static function byProcess($processId, int $perPage = 10)
    {
        return self::all(['process_id' => $processId], $perPage);
    }

    /**
     * Get requests by user ID
     *
     * @param string|int $userId User ID
     * @param int $perPage
     * @return array
     */
    public static function byUser($userId, int $perPage = 10)
    {
        return self::all(['user_id' => $userId], $perPage);
    }

    /**
     * Get count of requests
     *
     * @param array $filters Optional filters
     * @return int
     */
    public static function count(array $filters = []): int
    {
        // Get first page with per_page=1 to get total count from meta
        $params = ApiClient::buildQueryParams($filters, 1, 1);
        $result = ApiClient::get('/requests', $params);
        
        // If result has meta with total, return it
        if (isset($result['meta']['total'])) {
            return (int) $result['meta']['total'];
        }
        
        // Otherwise, get all and count
        $all = self::all($filters, 0);
        return is_array($all) ? count($all) : 0;
    }
}
