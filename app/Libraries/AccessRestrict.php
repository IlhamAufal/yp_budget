<?php

namespace App\Libraries;

use Config\Database;

class AccessRestrict
{
    protected $db;
    protected $table = 'gw_sm__access_restrict';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Check if a URL & Year combination is being accessed by another user
     *
     * @param string $userId
     * @param string $url
     * @param int $year
     * @return array ['allowed' => bool, 'accessed_by' => string|null]
     */
    public function check(string $userId, string $url, int $year): array
    {
        // Clean expired sessions (older than 15 minutes)
        $timeoutLimit = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        try {
            $this->db->table($this->table)
                ->where('updated_at <', $timeoutLimit)
                ->delete();

            // Check if another user is accessing this URL for the same working year
            $existing = $this->db->table($this->table)
                ->where('url', $url)
                ->where('year', $year)
                ->where('user_id !=', $userId)
                ->get()
                ->getRowArray();

            if ($existing) {
                return [
                    'allowed'     => false,
                    'accessed_by' => $existing['user_id'] ?? 'User lain',
                ];
            }

            // Register or update access for current user
            $userAccess = $this->db->table($this->table)
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            if ($userAccess) {
                $this->db->table($this->table)
                    ->where('user_id', $userId)
                    ->update([
                        'url'        => $url,
                        'year'       => $year,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                $this->db->table($this->table)->insert([
                    'user_id'    => $userId,
                    'url'        => $url,
                    'year'       => $year,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {
            // Fail safe if table does not exist yet
            log_message('error', 'AccessRestrict error: ' . $e->getMessage());
        }

        return [
            'allowed'     => true,
            'accessed_by' => null,
        ];
    }

    /**
     * Release access restriction for a user
     */
    public function release(string $userId): void
    {
        try {
            $this->db->table($this->table)
                ->where('user_id', $userId)
                ->delete();
        } catch (\Throwable $e) {
            log_message('error', 'AccessRestrict release error: ' . $e->getMessage());
        }
    }
}
