<?php
namespace App\Models;
use CodeIgniter\Model;

class NewHeadAccountModel extends Model
{
    protected $table      = 'yp_plan__new_head_account'; // 
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'cost_center_id', 'period_id', 'status', 'created_by', 'updated_at'
    ];
}