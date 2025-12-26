<?php

class VehicleService
{
    public $id;
    public $code;
    public $tracking_code;
    public $customer_id;
    public $customer_name;
    public $customer_address;
    public $customer_phone;
    public $vehicle_no;
    public $vehicle_brand_id;
    public $vehicle_model_id;
    public $status;
    public $current_stage;
    public $expected_completion;
    public $remarks;
    public $created_by;
    public $created_at;
    public $updated_at;
    public $completed_at;

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELIVERED = 'delivered';

    const STAGES = [
        1 => ['name' => 'Vehicle Received', 'icon' => 'uil-car', 'color' => '#3b82f6'],
        2 => ['name' => 'Inspection', 'icon' => 'uil-search', 'color' => '#8b5cf6'],
        3 => ['name' => 'Service Started', 'icon' => 'uil-wrench', 'color' => '#f59e0b'],
        4 => ['name' => 'In Progress', 'icon' => 'uil-cog', 'color' => '#ec4899'],
        5 => ['name' => 'Quality Check', 'icon' => 'uil-check-circle', 'color' => '#10b981'],
        6 => ['name' => 'Ready for Pickup', 'icon' => 'uil-truck', 'color' => '#06b6d4'],
        7 => ['name' => 'Delivered', 'icon' => 'uil-smile', 'color' => '#22c55e']
    ];

    public function __construct($id = null)
    {
        if ($id) {
            $query = "SELECT * FROM `vehicle_service` WHERE `id` = " . (int) $id;
            $db = Database::getInstance();
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                foreach ($result as $key => $value) {
                    if (property_exists($this, $key)) {
                        $this->$key = $value;
                    }
                }
            }
        }
    }

    public function create()
    {
        $db = Database::getInstance();
        
        $this->tracking_code = $this->generateTrackingCode();
        $this->status = self::STATUS_PENDING;
        $this->current_stage = 1;
        
        $query = "INSERT INTO `vehicle_service` (
            `code`, `tracking_code`, `customer_id`, `customer_name`, `customer_address`, 
            `customer_phone`, `vehicle_no`, `vehicle_brand_id`, `vehicle_model_id`, 
            `status`, `current_stage`, `expected_completion`, `remarks`, `created_by`, `created_at`
        ) VALUES (
            '$this->code', '$this->tracking_code', '$this->customer_id', '$this->customer_name', 
            '$this->customer_address', '$this->customer_phone', '$this->vehicle_no', 
            '$this->vehicle_brand_id', '$this->vehicle_model_id', '$this->status', 
            '$this->current_stage', '$this->expected_completion', '$this->remarks', 
            '$this->created_by', NOW()
        )";

        $result = $db->readQuery($query);

        if ($result) {
            $insertId = mysqli_insert_id($db->DB_CON);
            $this->logStageChange($insertId, 1, 'Vehicle received at service center');
            return $insertId;
        }
        return false;
    }

    public function update()
    {
        $db = Database::getInstance();
        
        $query = "UPDATE `vehicle_service` SET 
            `customer_name` = '$this->customer_name',
            `customer_address` = '$this->customer_address',
            `customer_phone` = '$this->customer_phone',
            `vehicle_no` = '$this->vehicle_no',
            `vehicle_brand_id` = '$this->vehicle_brand_id',
            `vehicle_model_id` = '$this->vehicle_model_id',
            `expected_completion` = '$this->expected_completion',
            `remarks` = '$this->remarks',
            `updated_at` = NOW()
            WHERE `id` = '$this->id'";

        return $db->readQuery($query);
    }

    public function updateStage($stage, $notes = '')
    {
        $db = Database::getInstance();
        
        $status = self::STATUS_IN_PROGRESS;
        $completedAt = 'NULL';
        
        if ($stage >= 7) {
            $status = self::STATUS_DELIVERED;
            $completedAt = 'NOW()';
        } elseif ($stage >= 6) {
            $status = self::STATUS_COMPLETED;
        }
        
        $query = "UPDATE `vehicle_service` SET 
            `current_stage` = '$stage',
            `status` = '$status',
            `updated_at` = NOW(),
            `completed_at` = $completedAt
            WHERE `id` = '$this->id'";

        $result = $db->readQuery($query);
        
        if ($result) {
            $this->logStageChange($this->id, $stage, $notes);
        }
        
        return $result;
    }

    private function logStageChange($serviceId, $stage, $notes)
    {
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'] ?? 0;
        
        $query = "INSERT INTO `vehicle_service_logs` 
            (`service_id`, `stage`, `notes`, `created_by`, `created_at`)
            VALUES ('$serviceId', '$stage', '$notes', '$userId', NOW())";
        
        $db->readQuery($query);
    }

    public function getServiceLogs()
    {
        $db = Database::getInstance();
        $query = "SELECT vsl.*, u.name as user_name 
                  FROM `vehicle_service_logs` vsl 
                  LEFT JOIN `user` u ON vsl.created_by = u.id 
                  WHERE vsl.service_id = '$this->id' 
                  ORDER BY vsl.created_at ASC";
        
        $result = $db->readQuery($query);
        $logs = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }
        
        return $logs;
    }

    public function delete()
    {
        $db = Database::getInstance();
        $query = "DELETE FROM `vehicle_service` WHERE `id` = '$this->id'";
        return $db->readQuery($query);
    }

    public function all($status = null)
    {
        $db = Database::getInstance();
        $where = "WHERE 1=1";
        
        if ($status) {
            $where .= " AND status = '$status'";
        }
        
        $query = "SELECT vs.*, vb.name as brand_name, vm.name as model_name 
                  FROM `vehicle_service` vs 
                  LEFT JOIN `vehicle_brand` vb ON vs.vehicle_brand_id = vb.id 
                  LEFT JOIN `vehicle_model` vm ON vs.vehicle_model_id = vm.id 
                  $where ORDER BY vs.created_at DESC";
        
        $result = $db->readQuery($query);
        $array_res = [];
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($array_res, $row);
        }
        
        return $array_res;
    }

    public function getLastID()
    {
        $query = "SELECT id FROM `vehicle_service` ORDER BY `id` DESC LIMIT 1";
        $db = Database::getInstance();
        $result = mysqli_fetch_array($db->readQuery($query));
        return $result['id'] ?? 0;
    }

    private function generateTrackingCode()
    {
        return 'TRK' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    }

    public static function getByTrackingCode($trackingCode)
    {
        $db = Database::getInstance();
        $trackingCode = $db->escapeString($trackingCode);
        
        $query = "SELECT vs.*, vb.name as brand_name, vm.name as model_name 
                  FROM `vehicle_service` vs 
                  LEFT JOIN `vehicle_brand` vb ON vs.vehicle_brand_id = vb.id 
                  LEFT JOIN `vehicle_model` vm ON vs.vehicle_model_id = vm.id 
                  WHERE vs.tracking_code = '$trackingCode'";
        
        $result = mysqli_fetch_assoc($db->readQuery($query));
        return $result;
    }

    public function getServiceJobs()
    {
        $db = Database::getInstance();
        $query = "SELECT vsj.*, st.name as service_name, st.price as service_price 
                  FROM `vehicle_service_jobs` vsj 
                  LEFT JOIN `service_type` st ON vsj.service_type_id = st.id 
                  WHERE vsj.service_id = '$this->id'";
        
        $result = $db->readQuery($query);
        $jobs = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $jobs[] = $row;
        }
        
        return $jobs;
    }

    public function addServiceJob($serviceTypeId, $price, $notes = '')
    {
        $db = Database::getInstance();
        
        $query = "INSERT INTO `vehicle_service_jobs` 
            (`service_id`, `service_type_id`, `price`, `notes`, `created_at`)
            VALUES ('$this->id', '$serviceTypeId', '$price', '$notes', NOW())";
        
        return $db->readQuery($query);
    }

    public function removeServiceJob($jobId)
    {
        $db = Database::getInstance();
        $query = "DELETE FROM `vehicle_service_jobs` WHERE `id` = '$jobId' AND `service_id` = '$this->id'";
        return $db->readQuery($query);
    }

    public function getTotalAmount()
    {
        $db = Database::getInstance();
        $query = "SELECT SUM(price) as total FROM `vehicle_service_jobs` WHERE `service_id` = '$this->id'";
        $result = mysqli_fetch_assoc($db->readQuery($query));
        return $result['total'] ?? 0;
    }

    public function fetchForDataTable($request)
    {
        $db = Database::getInstance();

        $start = isset($request['start']) ? (int)$request['start'] : 0;
        $length = isset($request['length']) ? (int)$request['length'] : 100;
        $search = $request['search']['value'] ?? '';
        $status = $request['status'] ?? '';

        $where = "WHERE 1=1";
        
        // Exclude delivered services (stage 7) from tracking page
        $where .= " AND vs.current_stage < 7 AND vs.status != 'delivered'";

        if (!empty($search)) {
            $where .= " AND (vs.code LIKE '%$search%' OR vs.tracking_code LIKE '%$search%' OR vs.customer_name LIKE '%$search%' OR vs.vehicle_no LIKE '%$search%')";
        }

        if (!empty($status)) {
            $where .= " AND vs.status = '$status'";
        }

        $totalSql = "SELECT COUNT(*) as cnt FROM vehicle_service vs";
        $totalQuery = $db->readQuery($totalSql);
        $totalRow = mysqli_fetch_assoc($totalQuery);
        $totalData = $totalRow['cnt'];

        $filteredSql = "SELECT COUNT(*) as cnt FROM vehicle_service vs $where";
        $filteredQuery = $db->readQuery($filteredSql);
        $filteredRow = mysqli_fetch_assoc($filteredQuery);
        $filteredData = $filteredRow['cnt'];

        $sql = "SELECT vs.*, vb.name as brand_name, vm.name as model_name 
                FROM vehicle_service vs 
                LEFT JOIN vehicle_brand vb ON vs.vehicle_brand_id = vb.id 
                LEFT JOIN vehicle_model vm ON vs.vehicle_model_id = vm.id 
                $where ORDER BY vs.created_at DESC LIMIT $start, $length";
        
        $dataQuery = $db->readQuery($sql);
        $data = [];

        while ($row = mysqli_fetch_assoc($dataQuery)) {
            $stageName = self::STAGES[$row['current_stage']]['name'] ?? 'Unknown';
            $stageColor = self::STAGES[$row['current_stage']]['color'] ?? '#6b7280';
            
            $nestedData = [
                "id" => $row['id'],
                "code" => $row['code'],
                "tracking_code" => $row['tracking_code'],
                "customer_name" => $row['customer_name'],
                "vehicle_no" => $row['vehicle_no'],
                "brand_name" => $row['brand_name'],
                "model_name" => $row['model_name'],
                "status" => $row['status'],
                "current_stage" => $row['current_stage'],
                "stage_name" => $stageName,
                "stage_color" => $stageColor,
                "expected_completion" => $row['expected_completion'],
                "created_at" => date('Y-m-d H:i', strtotime($row['created_at']))
            ];

            $data[] = $nestedData;
        }

        return [
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($filteredData),
            "data" => $data
        ];
    }
}

?>
