<?php

class ServiceAppointment
{
    public $id;
    public $booking_code;
    public $customer_name;
    public $customer_phone;
    public $customer_email;
    public $vehicle_no;
    public $vehicle_brand_id;
    public $vehicle_model_id;
    public $service_type_ids;
    public $preferred_date;
    public $preferred_time;
    public $notes;
    public $status;
    public $created_at;
    public $updated_at;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    public function __construct($id = null)
    {
        if ($id) {
            $query = "SELECT * FROM `service_appointment` WHERE `id` = " . (int) $id;
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
        
        $this->booking_code = $this->generateBookingCode();
        $this->status = self::STATUS_PENDING;
        
        $query = "INSERT INTO `service_appointment` (
            `booking_code`, `customer_name`, `customer_phone`, `customer_email`,
            `vehicle_no`, `vehicle_brand_id`, `vehicle_model_id`, `service_type_ids`,
            `preferred_date`, `preferred_time`, `notes`, `status`, `created_at`
        ) VALUES (
            '$this->booking_code', '$this->customer_name', '$this->customer_phone', '$this->customer_email',
            '$this->vehicle_no', '$this->vehicle_brand_id', '$this->vehicle_model_id', '$this->service_type_ids',
            '$this->preferred_date', '$this->preferred_time', '$this->notes', '$this->status', NOW()
        )";

        $result = $db->readQuery($query);

        if ($result) {
            return mysqli_insert_id($db->DB_CON);
        }
        return false;
    }

    public function update()
    {
        $db = Database::getInstance();
        
        $query = "UPDATE `service_appointment` SET 
            `customer_name` = '$this->customer_name',
            `customer_phone` = '$this->customer_phone',
            `customer_email` = '$this->customer_email',
            `vehicle_no` = '$this->vehicle_no',
            `vehicle_brand_id` = '$this->vehicle_brand_id',
            `vehicle_model_id` = '$this->vehicle_model_id',
            `service_type_ids` = '$this->service_type_ids',
            `preferred_date` = '$this->preferred_date',
            `preferred_time` = '$this->preferred_time',
            `notes` = '$this->notes',
            `updated_at` = NOW()
            WHERE `id` = '$this->id'";

        return $db->readQuery($query);
    }

    public function updateStatus($status)
    {
        $db = Database::getInstance();
        
        $query = "UPDATE `service_appointment` SET 
            `status` = '$status',
            `updated_at` = NOW()
            WHERE `id` = '$this->id'";

        return $db->readQuery($query);
    }

    public function delete()
    {
        $db = Database::getInstance();
        $query = "DELETE FROM `service_appointment` WHERE `id` = '$this->id'";
        return $db->readQuery($query);
    }

    public function all($status = null)
    {
        $db = Database::getInstance();
        $where = "WHERE 1=1";
        
        if ($status) {
            $where .= " AND status = '$status'";
        }
        
        $query = "SELECT sa.*, vb.name as brand_name, vm.name as model_name 
                  FROM `service_appointment` sa 
                  LEFT JOIN `vehicle_brand` vb ON sa.vehicle_brand_id = vb.id 
                  LEFT JOIN `vehicle_model` vm ON sa.vehicle_model_id = vm.id 
                  $where ORDER BY sa.preferred_date ASC, sa.preferred_time ASC";
        
        $result = $db->readQuery($query);
        $array_res = [];
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($array_res, $row);
        }
        
        return $array_res;
    }

    public function getLastID()
    {
        $query = "SELECT id FROM `service_appointment` ORDER BY `id` DESC LIMIT 1";
        $db = Database::getInstance();
        $result = mysqli_fetch_array($db->readQuery($query));
        return $result['id'] ?? 0;
    }

    private function generateBookingCode()
    {
        return 'APT' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    }

    public static function getByBookingCode($bookingCode)
    {
        $db = Database::getInstance();
        $bookingCode = $db->escapeString($bookingCode);
        
        $query = "SELECT sa.*, vb.name as brand_name, vm.name as model_name 
                  FROM `service_appointment` sa 
                  LEFT JOIN `vehicle_brand` vb ON sa.vehicle_brand_id = vb.id 
                  LEFT JOIN `vehicle_model` vm ON sa.vehicle_model_id = vm.id 
                  WHERE sa.booking_code = '$bookingCode'";
        
        $result = mysqli_fetch_assoc($db->readQuery($query));
        return $result;
    }

    public function getServiceTypes()
    {
        if (!$this->service_type_ids) return [];
        
        $db = Database::getInstance();
        $ids = json_decode($this->service_type_ids, true);
        
        if (!$ids || !is_array($ids)) return [];
        
        $idsList = implode(',', array_map('intval', $ids));
        $query = "SELECT * FROM `service_type` WHERE `id` IN ($idsList)";
        
        $result = $db->readQuery($query);
        $services = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $services[] = $row;
        }
        
        return $services;
    }

    public function getTotalEstimate()
    {
        $services = $this->getServiceTypes();
        $total = 0;
        
        foreach ($services as $service) {
            $total += (float) $service['price'];
        }
        
        return $total;
    }

    public function fetchForDataTable($request)
    {
        $db = Database::getInstance();

        $start = isset($request['start']) ? (int)$request['start'] : 0;
        $length = isset($request['length']) ? (int)$request['length'] : 100;
        $search = $request['search']['value'] ?? '';
        $status = $request['status'] ?? '';
        $date = $request['date'] ?? '';

        $where = "WHERE 1=1";

        if (!empty($search)) {
            $where .= " AND (sa.booking_code LIKE '%$search%' OR sa.customer_name LIKE '%$search%' OR sa.vehicle_no LIKE '%$search%' OR sa.customer_phone LIKE '%$search%')";
        }

        if (!empty($status)) {
            $where .= " AND sa.status = '$status'";
        }

        if (!empty($date)) {
            $where .= " AND sa.preferred_date = '$date'";
        }

        $totalSql = "SELECT COUNT(*) as cnt FROM service_appointment sa";
        $totalQuery = $db->readQuery($totalSql);
        $totalRow = mysqli_fetch_assoc($totalQuery);
        $totalData = $totalRow['cnt'];

        $filteredSql = "SELECT COUNT(*) as cnt FROM service_appointment sa $where";
        $filteredQuery = $db->readQuery($filteredSql);
        $filteredRow = mysqli_fetch_assoc($filteredQuery);
        $filteredData = $filteredRow['cnt'];

        $sql = "SELECT sa.*, vb.name as brand_name, vm.name as model_name 
                FROM service_appointment sa 
                LEFT JOIN vehicle_brand vb ON sa.vehicle_brand_id = vb.id 
                LEFT JOIN vehicle_model vm ON sa.vehicle_model_id = vm.id 
                $where ORDER BY sa.preferred_date ASC, sa.preferred_time ASC LIMIT $start, $length";
        
        $dataQuery = $db->readQuery($sql);
        $data = [];

        while ($row = mysqli_fetch_assoc($dataQuery)) {
            $statusBadge = [
                'pending' => ['class' => 'warning', 'text' => 'Pending'],
                'confirmed' => ['class' => 'info', 'text' => 'Confirmed'],
                'cancelled' => ['class' => 'danger', 'text' => 'Cancelled'],
                'completed' => ['class' => 'success', 'text' => 'Completed']
            ];
            
            $badge = $statusBadge[$row['status']] ?? ['class' => 'secondary', 'text' => 'Unknown'];
            
            $nestedData = [
                "id" => $row['id'],
                "booking_code" => $row['booking_code'],
                "customer_name" => $row['customer_name'],
                "customer_phone" => $row['customer_phone'],
                "vehicle_no" => $row['vehicle_no'],
                "brand_name" => $row['brand_name'],
                "model_name" => $row['model_name'],
                "preferred_date" => date('Y-m-d', strtotime($row['preferred_date'])),
                "preferred_time" => date('h:i A', strtotime($row['preferred_time'])),
                "status" => $row['status'],
                "status_badge" => $badge,
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
