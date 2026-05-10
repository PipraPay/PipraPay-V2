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
            location.href="../install/";
        </script>
<?php
        exit();
    }
    
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }
    
    if(isset($_GET['name'])){
        $invoice_id = escape_string($_GET['name']);
        
        if($invoice_id == ""){
            $error_title = "Invoice Not Found";
            $error_description = "The invoice you're trying to access doesn't exist or may have been removed. Please verify the link or contact support for assistance.";
            
            include(__DIR__."/../error.php");
            
            exit();
        }else{
            $invoice_details = pp_get_invoice($invoice_id);
            $setting = pp_get_settings();
            
            if($invoice_details['status'] == true){
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                    $rawData = file_get_contents("php://input");
                    $data = json_decode($rawData, true);

        
                    $received_api_key = getAuthorizationHeader();
                    
                    if ($received_api_key !== $setting['response'][0]['api_key']) {
                        http_response_code(401); 
                        echo json_encode(["status" => false, "message" => "Unauthorized request. Invalid API key.".$received_api_key]);
                        exit;
                    }
                
                    $pp_id = $data['pp_id'] ?? '';
                    $customer_name = $data['customer_name'] ?? '';
                    $customer_email_mobile = $data['customer_email_mobile'] ?? '';
                    $payment_method = $data['payment_method'] ?? '';
                    $amount = $data['amount'] ?? 0;
                    $fee = $data['fee'] ?? 0;
                    $refund_amount = $data['refund_amount'] ?? 0;
                    $total = $data['total'] ?? 0;
                    $currency = $data['currency'] ?? '';
                    $status = $data['status'] ?? '';
                    $date = $data['date'] ?? '';
                    
                    $metadata = $data['metadata'] ?? [];
                    $invoiceid = $metadata['invoiceid'] ?? '';
                    
                
                    $url = PP_URL . '/api/verify-payments';
                    $apiKey = $setting['response'][0]['api_key'];
                    $data = [
                        'pp_id' => $pp_id
                    ];
                    
                    $ch = curl_init($url);
                    
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'accept: application/json',
                        'content-type: application/json',
                        'mh-piprapay-api-key: ' . $apiKey
                    ]);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    
                    $response = curl_exec($ch);
                    curl_close($ch);
                    
                    $data = json_decode($response, true);
                    
                    if($data['status'] == false){
                        http_response_code(401); 
                        echo json_encode(["status" => false, "message" => "Invalid Transaction"]);
                    }else{
                        $pp_id = $data['pp_id'] ?? '';
                        $customer_name = $data['customer_name'] ?? '';
                        $customer_email_mobile = $data['customer_email_mobile'] ?? '';
                        $payment_method = $data['payment_method'] ?? '';
                        $amount = $data['amount'] ?? 0;
                        $fee = $data['fee'] ?? 0;
                        $refund_amount = $data['refund_amount'] ?? 0;
                        $total = $data['total'] ?? 0;
                        $currency = $data['currency'] ?? '';
                        $status = $data['status'] ?? '';
                        $date = $data['date'] ?? '';
                        
                        $metadata = $data['metadata'] ?? [];
                        $invoiceid = $metadata['invoiceid'] ?? '';
                        
                        if($status == "completed"){
                            $columns = ['i_status'];
                            $values = ['paid'];
                            $condition = "i_id = '".$invoiceid."'"; 
                            
                            updateData($db_prefix."invoice", $columns, $values, $condition);
                            
                            if (function_exists('pp_invoice_ipn')) {
                                pp_trigger_hook('pp_invoice_ipn', $invoiceid);
                            }
                        }else{
                            if($status == "refunded"){
                                $columns = ['i_status'];
                                $values = ['refunded'];
                                $condition = "i_id = '".$invoiceid."'"; 
                                
                                updateData($db_prefix."invoice", $columns, $values, $condition);
                                
                                if (function_exists('pp_invoice_ipn')) {
                                    pp_trigger_hook('pp_invoice_ipn', $invoiceid);
                                }
                            }
                        }
                        
                        http_response_code(200);
                    }
                    exit;
                }
                
                theme_include('invoice', $setting['response'][0]['invoice_theme'], $setting['response'][0]['invoice_theme'].'-invoice-class.php', $invoice_id, 'invoice_id');
                
                if (function_exists('pp_trigger_hook')) {
                    pp_trigger_hook('pp_invoice_initialize');
                }
            }else{
                $error_title = "Invoice Inactive";
                $error_description = "This invoice is no longer active and cannot be processed. Please contact support or request a new invoice if needed.";
                
                include(__DIR__."/../error.php");
                
                exit();
            }
        }
    }else{
        $error_title = "Invoice Not Found";
        $error_description = "The invoice you're trying to access doesn't exist or may have been removed. Please verify the link or contact support for assistance.";
        
        include(__DIR__."/../error.php");
        
        exit();
    }