<?php

namespace ProcessMaker\ScriptHelpers;

use ProcessMaker\Models\ProcessRequestToken;

/**
 * Helper class to list ProcessRequestTokens (Tasks) in script tasks
 */
class TaskLister
{
    /**
     * Get all tasks
     *
     * @param array $filters Optional filters (status, user_id, process_id, etc.)
     * @param int $perPage Number of items per page
     * @param int $page Page number
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function all(array $filters = [], int $perPage = 10, int $page = 1)
    {
        $query = ProcessRequestToken::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['process_id'])) {
            $query->where('process_id', $filters['process_id']);
        }

        if (isset($filters['process_request_id'])) {
            $query->where('process_request_id', $filters['process_request_id']);
        }

        if (isset($filters['element_type'])) {
            $query->where('element_type', $filters['element_type']);
        }

        if (isset($filters['element_name'])) {
            $query->where('element_name', 'like', '%' . $filters['element_name'] . '%');
        }

        if (isset($filters['due_from'])) {
            $query->where('due_at', '>=', $filters['due_from']);
        }

        if (isset($filters['due_to'])) {
            $query->where('due_at', '<=', $filters['due_to']);
        }

        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->where('due_at', '<', now())
                  ->where('status', 'ACTIVE');
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
     * Get a single task by ID
     *
     * @param string|int $id Task ID
     * @return ProcessRequestToken|null
     */
    public static function find($id)
    {
        return ProcessRequestToken::find($id);
    }

    /**
     * Get active tasks
     *
     * @param array $filters Additional filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function overdue(array $filters = [], int $perPage = 10)
    {
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
        $query = ProcessRequestToken::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['process_id'])) {
            $query->where('process_id', $filters['process_id']);
        }

        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->where('due_at', '<', now())
                  ->where('status', 'ACTIVE');
        }

        return $query->count();
    }
}

