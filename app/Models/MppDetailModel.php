<?php
namespace App\Models;
use CodeIgniter\Model;

class MppDetailModel extends Model
{
    protected $table      = 'yp_plan__trans_mpp_detail'; // 
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'mpp_header_id', 'category', // Contract, Permanent, Outsourcing
        'jan', 'feb', 'mar', 'apr', 'may', 'jun', 
        'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'total'
    ];
    // Tambahkan method khusus untuk join dengan master UMK / Salary 
}