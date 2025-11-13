<?php

namespace ProcessMaker\ScriptHelpers;

/**
 * Helper class to list ProcessRequestTokens (Tasks) in script tasks
 * Uses ProcessMaker API instead of Eloquent models
 */
class TaskLister
{
    /**
     * Get all tasks
     *
     * @param array $filters Optional filters (status, user_id, process_id, etc.)
     * @param int $perPage Number of items per page
     * @param int $page Page number
     * @return array
     */
    public static function all(array $filters = [], int $perPage = 10, int $page = 1)
    {
        $params = ApiClient::buildQueryParams($filters, $perPage, $page);
        return ApiClient::get('/tasks', $params);
    }

    /**
     * Get a single task by ID
     *
     * @param string|int $id Task ID
     * @return array|null
     */
    public static function find($id)
    {
        try {
            return ApiClient::get("/tasks/{$id}");
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get active tasks
     *
     * @param array $filters Additional filters
     * @param int $perPage
     * @return array
     */
    public static function active(array $filters = [], int $perPage = 10)
    {
        return self::all(array_merge($filters, ['status' => 'ACTIVE']), $perPage);
    }

    /**
     * Get completed tasks
     *
     * @param array $filters Additional filters
     * @param int $perPage
     * @return array
     */
    public static function completed(array $filters = [], int $perPage = 10)
    {
        return self::all(array_merge($filters, ['status' => 'COMPLETED']), $perPage);
    }

    /**
     * Get tasks by user ID
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
     * Get tasks by process request ID
     *
     * @param string|int $requestId Process Request ID
     * @param int $perPage
     * @return array
     */
    public static function byRequest($requestId, int $perPage = 10)
    {
        return self::all(['process_request_id' => $requestId], $perPage);
    }

    /**
     * Get overdue tasks
     *
     * @param array $filters Additional filters
     * @param int $perPage
     * @return array
     */
    public static function overdue(array $filters = [], int $perPage = 10)
    {
        // Note: Overdue filter might need to be handled differently in API
        // This is a simplified version
        return self::all(array_merge($filters, ['overdue' => true]), $perPage);
    }

    /**
     * Get count of tasks
     *
     * @param array $filters Optional filters
     * @return int
     */
    public static function count(array $filters = []): int
    {
        // Get first page with per_page=1 to get total count from meta
        $params = ApiClient::buildQueryParams($filters, 1, 1);
        $result = ApiClient::get('/tasks', $params);
        
        // If result has meta with total, return it
        if (isset($result['meta']['total'])) {
            return (int) $result['meta']['total'];
        }
        
        // Otherwise, get all and count
        $all = self::all($filters, 0);
        return is_array($all) ? count($all) : 0;
    }
}
