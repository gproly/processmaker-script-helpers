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
     * @param int $perPage Number of items per page (0 for all)
     * @param int $page Page number
     * @return array
     */
    public static function all(array $filters = [], int $perPage = 10, int $page = 1)
    {
        //$params = ApiClient::buildQueryParams($filters, $perPage, $page);
        return ["hello world from RequestLister"]; //ApiClient::get('/requests', $params);
    }
}
