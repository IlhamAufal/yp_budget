<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model Channel — tabel `gw_plan__master_channel`.
 */
class ChannelModel extends Model
{
    protected $table            = 'gw_plan__master_channel';
    protected $primaryKey       = 'id_channel';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'channel_name',
        'channel_code',
    ];

    /**
     * Ambil semua channel dari database.
     */
    public function getAll(): array
    {
        return $this->builder()
            ->orderBy('id_channel', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil channel domestic dengan mengecualikan channel EXPORT.
     */
    public function getDomesticChannels(): array
    {
        return $this->builder()
            ->where('channel_code !=', 'EXPORT')
            ->orderBy('id_channel', 'ASC')
            ->get()
            ->getResultArray();
    }
}
