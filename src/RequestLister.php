<?php

namespace ProcessMaker\ScriptHelpers;

use ProcessMaker\Models\ProcessRequest;

/**
 * Helper class to list ProcessRequests in script tasks
 */
class RequestLister
{
    /**
     * Get all requests
     *
     * @param array $filters Optional filters (status, process_id, user_id, etc.)
     * @param int $perPage Number of items per page
     * @param int $page Page number
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function all(array $filters = [], int $perPage = 10, int $page = 1)
    {
        $query = ProcessRequest::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['process_id'])) {
            $query->where('process_id', $filters['process_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['case_number'])) {
            $query->where('case_number', $filters['case_number']);
        }

        if (isset($filters['created_from'])) {
            $query->where('created_at', '>=', $filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        // Paginate or get all
        if ($perPage > 0) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }

    /**
     * Get a single request by ID
     *
     * @param string|int $id Request ID
     * @return ProcessRequest|null
     */
    public static function find($id)
    {
        return ProcessRequest::find($id);
    }

    /**
     * Get requests by status
     *
     * @param string $status Status (ACTIVE, COMPLETED, etc.)
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
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
        $query = ProcessRequest::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['process_id'])) {
            $query->where('process_id', $filters['process_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->count();
    }
}

