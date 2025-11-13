<?php

namespace ProcessMaker\ScriptHelpers;

/**
 * Helper class to list Users in script tasks
 * Uses ProcessMaker API instead of Eloquent models
 */
class UserLister
{
    /**
     * Get all users
     *
     * @param array $filters Optional filters (status, group_id, etc.)
     * @param int $perPage Number of items per page
     * @param int $page Page number
     * @return array
     */
    public static function all(array $filters = [], int $perPage = 10, int $page = 1)
    {
        $params = ApiClient::buildQueryParams($filters, $perPage, $page);
        return ApiClient::get('/users', $params);
    }

    /**
     * Get a single user by ID
     *
     * @param string|int $id User ID
     * @return array|null
     */
    public static function find($id)
    {
        try {
            return ApiClient::get("/users/{$id}");
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get user by username
     *
     * @param string $username Username
     * @return array|null
     */
    public static function byUsername(string $username)
    {
        $users = self::all(['username' => $username], 1);
        
        if (isset($users['data']) && is_array($users['data']) && count($users['data']) > 0) {
            return $users['data'][0];
        }
        
        return null;
    }

    /**
     * Get user by email
     *
     * @param string $email Email address
     * @return array|null
     */
    public static function byEmail(string $email)
    {
        $users = self::all(['email' => $email], 1);
        
        if (isset($users['data']) && is_array($users['data']) && count($users['data']) > 0) {
            return $users['data'][0];
        }
        
        return null;
    }

    /**
     * Get active users
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
     * Get users by group ID
     *
     * @param string|int $groupId Group ID
     * @param int $perPage
     * @return array
     */
    public static function byGroup($groupId, int $perPage = 10)
    {
        return self::all(['group_id' => $groupId], $perPage);
    }

    /**
     * Search users by name or username
     *
     * @param string $search Search term
     * @param int $perPage
     * @return array
     */
    public static function search(string $search, int $perPage = 10)
    {
        // Use filter parameter for search
        return self::all(['filter' => $search], $perPage);
    }

    /**
     * Get count of users
     *
     * @param array $filters Optional filters
     * @return int
     */
    public static function count(array $filters = []): int
    {
        // Get first page with per_page=1 to get total count from meta
        $params = ApiClient::buildQueryParams($filters, 1, 1);
        $result = ApiClient::get('/users', $params);
        
        // If result has meta with total, return it
        if (isset($result['meta']['total'])) {
            return (int) $result['meta']['total'];
        }
        
        // Otherwise, get all and count
        $all = self::all($filters, 0);
        return is_array($all) ? count($all) : 0;
    }
}
