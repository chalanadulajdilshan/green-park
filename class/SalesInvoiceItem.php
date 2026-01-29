<?php

class SalesInvoiceItem
{
    public $id;
    public $invoice_id;
    public $item_code;
    public $service_item_code;
    public $item_name;
    public $quantity;
    public $cost;
    public $list_price;
    public $price;
    public $discount;
    public $total;
    public $vehicle_no;
    public $current_km;
    public $next_service_date;
    public $tax;
    public $service_item_id;
    public $created_at;
    public $commission;

    public function __construct($id = null)
    {
        if ($id) {
            $query = "SELECT  * 
                      FROM `sales_invoice_items` 
                      WHERE `id` = " . (int) $id;
            $db = Database::getInstance();
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                $this->id = $result['id'];
                $this->invoice_id = $result['invoice_id'];
                $this->item_code = $result['item_code'];
                $this->service_item_code = $result['service_item_code'];
                $this->item_name = $result['item_name'];
                $this->quantity = $result['quantity'];
                $this->discount = $result['discount'];
                $this->cost = $result['cost'];
                $this->list_price = $result['list_price'] ?? $result['price']; // Fallback for existing records
                $this->price = $result['price'];
                $this->total = $result['total'];
                $this->vehicle_no = $result['vehicle_no'] ?? '';
                $this->current_km = $result['current_km'] ?? '';
                $this->next_service_date = $result['next_service_date'] ?? '';
                $this->tax = $result['tax'] ?? 0;
                $this->service_item_id = $result['service_item_id'] ?? 0;
                $this->commission = $result['commission'] ?? 0;
                $this->created_at = $result['created_at'];
            }
        }
    }

    public function create()
    {


        $query = "INSERT INTO `sales_invoice_items` 
    (`invoice_id`, `item_code`, `service_item_code`, `service_item_id`, `item_name`,`cost`, `list_price`, `price`, `discount`,`tax`, `quantity`, `total`, `vehicle_no`, `current_km`, `next_service_date`, `commission`, `created_at`) 
    VALUES (
        '{$this->invoice_id}', 
        '{$this->item_code}', 
        '{$this->service_item_code}', 
        '{$this->service_item_id}', 
        '{$this->item_name}', 
        '{$this->cost}', 
        '{$this->list_price}', 
        '{$this->price}', 
        '{$this->discount}', 
        '{$this->tax}', 
        '{$this->quantity}', 
        '{$this->total}',
        '{$this->vehicle_no}',
        '{$this->current_km}',
        '{$this->next_service_date}',
        '{$this->commission}',
        NOW()
    )";



        $db = Database::getInstance();
        $result = $db->readQuery($query);

        if ($result) {
            return mysqli_insert_id($db->DB_CON);
        } else {
            return false;
        }
    }

    public function update()
    {
        $query = "UPDATE `sales_invoice_items` SET 
             
            `item_code` = '{$this->item_code}', 
            `service_item_code` = '{$this->service_item_code}', 
            `item_name` = '{$this->item_name}', 
            `price` = '{$this->price}', 
            `discount` = '{$this->discount}', 
            `tax` = '{$this->tax}', 
            `quantity` = '{$this->quantity}', 
            `total` = '{$this->total}' 
            WHERE `id` = '{$this->id}'";

        $db = Database::getInstance();
        $result = $db->readQuery($query);

        if ($result) {
            return $this->__construct($this->id);
        } else {
            return false;
        }
    }

    public function delete()
    {
        $query = "DELETE FROM `sales_invoice_items` WHERE `id` = '{$this->id}'";
        $db = Database::getInstance();
        return $db->readQuery($query);
    }

    public function getByInvoiceId($invoice_id)
    {
        $query = "SELECT * FROM `sales_invoice_items` WHERE `invoice_id` = '{$invoice_id}' ORDER BY `id` ASC";
        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array_res = [];

        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                array_push($array_res, $row);
            }
        }

        return $array_res;
    }


    
    // Get invoice items together with returned and available quantities
    public function getByInvoiceIdWithReturns($invoice_id)
    {
        $db = Database::getInstance();
        $invoice_id = (int)$invoice_id;

        $query = "
            SELECT 
                sii.*, 
                COALESCE(rt.returned_quantity, 0) AS returned_quantity,
                (sii.quantity - COALESCE(rt.returned_quantity, 0)) AS available_quantity,
                sii.price AS customer_price,
                sii.list_price AS dealer_price
            FROM `sales_invoice_items` sii
            LEFT JOIN (
                SELECT 
                    sri.item_id,
                    sr.invoice_id,
                    SUM(sri.quantity) AS returned_quantity
                FROM `sales_return_items` sri
                INNER JOIN `sales_return` sr ON sri.return_id = sr.id
                GROUP BY sri.item_id, sr.invoice_id
            ) rt ON sii.item_code = rt.item_id AND sii.invoice_id = rt.invoice_id
            WHERE sii.invoice_id = {$invoice_id}
            ORDER BY sii.id ASC";

        $result = $db->readQuery($query);
        $items = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = $row;
            }
        }

        return $items;
    }


    public function all()
    {
        $query = "SELECT  * 
                  FROM `sales_invoice_items` 
                  ORDER BY `id` DESC";
        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array_res = array();

        while ($row = mysqli_fetch_array($result)) {
            array_push($array_res, $row);
        }

        return $array_res;
    }

    public function getItemsByInvoiceId($invoice_id)
    {
        $query = "SELECT * 
                  FROM `sales_invoice_items` 
                  WHERE `invoice_id` = $invoice_id 
                  ORDER BY `id` DESC";
    
        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array_res = array();
    
        while ($row = mysqli_fetch_assoc($result)) {
            // safely load item master
            if (!empty($row['service_item_id']) && $row['service_item_id'] != 0) {
                // Pure service (SV/)
                $row['item_code_name'] = 'SV/' . str_pad($row['service_item_id'], 4, '0', STR_PAD_LEFT);
            } elseif (!empty($row['service_item_code']) && $row['service_item_code'] != 0) {
                // Service item (SI/)
                $service_item_master = new ServiceItem($row['service_item_code']);
                $row['item_code_name'] = $service_item_master->item_code ?? ('SI/' . str_pad($row['service_item_code'], 4, '0', STR_PAD_LEFT));
            } elseif (!empty($row['item_code']) && $row['item_code'] != 0) {
                // Regular item (TI/)
                $item_master = new ItemMaster($row['item_code']);
                if (!empty($item_master->code)) {
                    $row['item_code_name'] = $item_master->code;
                } else {
                    $row['item_code_name'] = 'TI/' . str_pad($row['item_code'], 4, '0', STR_PAD_LEFT);
                }
            } else {
                $row['item_code_name'] = '';
            }
            
            // Extract clean item name for display (remove ARN metadata)
            $row['display_name'] = $this->extractCleanItemName($row['item_name']);
            
            // Add vehicle no and current km to display name if they exist
            if (!empty($row['vehicle_no']) || !empty($row['current_km'])) {
                $vehicleInfo = ' [' . ($row['vehicle_no'] ?: 'N/A') . ' - ' . ($row['current_km'] ?: 'N/A') . ' KM]';
                $row['display_name'] .= $vehicleInfo;
            }
    
            $array_res[] = $row; // push AFTER adding new field
        }
    
        return $array_res;
    }
    
    // Helper method to extract clean item name without ARN metadata
    private function extractCleanItemName($itemName)
    {
        // Remove ARN metadata first
        if (strpos($itemName, '|ARN:') !== false) {
            $parts = explode('|ARN:', $itemName);
            $itemName = trim($parts[0]);
        }

        // Remove trailing location suffix like " L1" or "L1"
        $itemName = preg_replace('/\s*[A-Z]+\d+$/', '', $itemName);

        return trim($itemName);
    }
    



}
