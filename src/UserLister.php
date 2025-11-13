<?php

namespace ProcessMaker\ScriptHelpers;

use ProcessMaker\Models\User;

/**
 * Helper class to list Users in script tasks
 */
class UserLister
{
    /**
     * Get all users
     *
     * @param array $filters Optional filters (status, group_id, etc.)
     * @param int $perPage Number of items per page
     * @param int $page Page number
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function all(array $filters = [], int $perPage = 10, int $page = 1)
    {
        $query = User::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['username'])) {
            $query->where('username', 'like', '%' . $filters['username'] . '%');
        }

        if (isset($filters['firstname'])) {
            $query->where('firstname', 'like', '%' . $filters['firstname'] . '%');
        }

        if (isset($filters['lastname'])) {
            $query->where('lastname', 'like', '%' . $filters['lastname'] . '%');
        }

        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (isset($filters['group_id'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->where('groups.id', $filters['group_id']);
            });
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'username';
        $orderDirection = $filters['order_direction'] ?? 'asc';
        $query->orderBy($orderBy, $orderDirection);

        // Paginate or get all
        if ($perPage > 0) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }

    /**
     * Get a single user by ID
     *
     * @param string|int $id User ID
     * @return User|null
     */
    public static function find($id)
    {
        return User::find($id);
    }

    /**
     * Get user by username
     *
     * @param string $username Username
     * @return User|null
     */
    public static function byUsername(string $username)
    {
        return User::where('username', $username)->first();
    }

    /**
     * Get user by email
     *
     * @param string $email Email address
     * @return User|null
     */
    public static function byEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get active users
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
     * Get users by group ID
     *
     * @param string|int $groupId Group ID
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public static function search(string $search, int $perPage = 10)
    {
        $query = User::query()
            ->where(function ($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                  ->orWhere('firstname', 'like', '%' . $search . '%')
                  ->orWhere('lastname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });

        if ($perPage > 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Get count of users
     *
     * @param array $filters Optional filters
     * @return int
     */
    public static function count(array $filters = []): int
    {
        $query = User::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['group_id'])) {
            $query->whereHas('groups', function ($q) use ($filters) {
                $q->where('groups.id', $filters['group_id']);
            });
        }

        return $query->count();
    }
}

