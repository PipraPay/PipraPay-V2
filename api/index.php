<?php
    if (file_exists(__DIR__."/../pp-config.php")) {
        if (file_exists(__DIR__.'/../maintenance.lock')) {
            if (file_exists(__DIR__.'/../pp-include/pp-maintenance.php')) {
               include(__DIR__."/../pp-include/pp-maintenance.php");
            }else{
                die('System is under maintenance. Please try again later.');
            }
            exit();
        }else{
            if (file_exists(__DIR__.'/../pp-include/pp-controller.php')) {
                include(__DIR__."/../pp-include/pp-controller.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../pp-include/pp-model.php')) {
                include(__DIR__."/../pp-include/pp-model.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../pp-include/pp-view.php')) {
                include(__DIR__."/../pp-include/pp-view.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
        }
    }else{
?>
        <script>
            location.href="https://<?php echo $_SERVER['HTTP_HOST']?>/install/";
        </script>
<?php
        exit();
    }
    
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }
    
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
        
    if(isset($_GET['name'])){
        $endpoint_type = escape_string($_GET['name']);
        
        if($endpoint_type == "create-charge") {
            $received_api_key = getAuthorizationHeader();
        
            if (empty($received_api_key) || !hash_equals($global_setting_response['response'][0]['api_key'], $received_api_key)) {
                http_response_code(401); 
                echo json_encode(["status" => false, "message" => "Unauthorized request. Invalid API key."]);
                exit;
            }
        
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
        
            // Required field validation
            $required_fields = ['full_name', 'email_mobile', 'amount', 'metadata', 'redirect_url', 'cancel_url', 'webhook_url', 'return_type', 'currency'];
            $missing_fields = [];
        
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    $missing_fields[] = $field;
                }
            }
        
            if (!empty($missing_fields)) {
                http_response_code(400); 
                echo json_encode(["status" => false, "message" => "Missing required field(s): " . implode(', ', $missing_fields)]);
                exit;
            }
        
            // Proceed if all fields are present
            $full_name = escape_string($data['full_name']);
            $email_mobile = escape_string($data['email_mobile']);
            $amount = safeNumber($data['amount']);
            if ($amount <= 0) {
                http_response_code(400); 
                echo json_encode(["status" => false, "message" => "Amount must be greater than zero."]);
                exit;
            }

            $meta_data = json_encode($data['metadata']);
            $redirect_url = escape_string($data['redirect_url']);
            $cancel_url = escape_string($data['cancel_url']);
            $webhook_url = escape_string($data['webhook_url']);
            $return_type = escape_string($data['return_type']);
            $currency = escape_string($data['currency']);
        
            $c_id = isset($data['c_id']) ? escape_string($data['c_id']) : '--';
            $product_name = isset($data['product_name']) ? escape_string($data['product_name']) : '--';
            $product_description = isset($data['product_description']) ? escape_string($data['product_description']) : '--';
            $product_meta = isset($data['product_meta']) ? json_encode($data['product_meta']) : '--';
        
            $pp_id = time() . mt_rand(10000, 99999);
        
            $columns = ['pp_id', 'c_id', 'c_name', 'c_email_mobile', 'transaction_amount', 'transaction_fee', 'transaction_refund_amount', 'transaction_currency', 'transaction_redirect_url', 'transaction_return_type', 'transaction_cancel_url', 'transaction_webhook_url', 'transaction_metadata', 'transaction_status', 'transaction_product_name', 'transaction_product_description', 'transaction_product_meta', 'created_at'];
            $values = [$pp_id, $c_id, $full_name, $email_mobile, safeNumber($amount), 0, 0, $currency, $redirect_url, $return_type, $cancel_url, $webhook_url, $meta_data, 'initialize', $product_name, $product_description, $product_meta, getCurrentDatetime('Y-m-d H:i:s')];
        
            insertData($db_prefix.'transaction', $columns, $values);
        
            http_response_code(200); 
            echo json_encode(["status" => true, "pp_id" => $pp_id, "pp_url" => "https://".$_SERVER['HTTP_HOST']."/payment/".$pp_id]);
        }
        
        if($endpoint_type == "verify-payments"){
            $received_api_key = getAuthorizationHeader();
            
            if (empty($received_api_key) || !hash_equals($global_setting_response['response'][0]['api_key'], $received_api_key)) {
                http_response_code(401); 
                echo json_encode(["status" => false, "message" => "Unauthorized request. Invalid API key."]);
                exit;
            }
            
            // Get JSON raw input
            $json = file_get_contents("php://input");
            $data = json_decode($json, true);
            
            // Validate required data
            if (!empty($data['pp_id'])) {
                $pp_id = escape_string($data['pp_id']);
                
                $transaction_details = pp_get_transation($pp_id);
                
                if($transaction_details['status'] == true){
                    if($transaction_details['response'][0]['transaction_status'] !== "initialize"){
                        http_response_code(200); 
                        
                        $meta = json_decode($transaction_details['response'][0]['transaction_metadata'], true) ?? [];
                        
                        $payload = [
                            'pp_id' => $transaction_details['response'][0]['pp_id'],
                            'customer_name' => $transaction_details['response'][0]['c_name'],
                            'customer_email_mobile' => $transaction_details['response'][0]['c_email_mobile'],
                            'payment_method' => $transaction_details['response'][0]['payment_method'],
                            'amount' => safeNumber($transaction_details['response'][0]['transaction_amount']),
                            'fee' => safeNumber($transaction_details['response'][0]['transaction_fee']),
                            'refund_amount' => safeNumber($transaction_details['response'][0]['transaction_refund_amount']),
                            'total' => safeNumber($transaction_details['response'][0]['transaction_amount']) + safeNumber($transaction_details['response'][0]['transaction_fee']) - safeNumber($transaction_details['response'][0]['transaction_refund_amount']),
                            'currency' => $transaction_details['response'][0]['transaction_currency'],
                            'metadata' => $meta,
                            'sender_number' => $transaction_details['response'][0]['payment_sender_number'],
                            'transaction_id' => $transaction_details['response'][0]['payment_verify_id'],
                            'status' => $transaction_details['response'][0]['transaction_status'],
                            'date' => $transaction_details['response'][0]['created_at']
                        ];
                        
                        echo json_encode($payload);
                    }else{
                        http_response_code(400); 
                        echo json_encode(["status" => false, "message" => "Invalid Transaction"]);
                    }
                }else{
                    http_response_code(400); 
                    echo json_encode(["status" => false, "message" => "Invalid Transaction"]);
                }
            } else {
                http_response_code(400); 
                echo json_encode(["status" => false, "message" => "Missing required fields"]);
            }
        }
    }else{
        echo 'System is under maintenance. Please try again later.';
        exit();
    }