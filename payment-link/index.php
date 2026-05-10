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
        $payment_link_id = escape_string($_GET['name']);
        
        if($payment_link_id == ""){
            $error_title = "Payment Link Invalid or Expired";
            $error_description = "The payment link you're trying to use is either incorrect or has expired. Please check the link or request a new one to proceed.";
            
            include(__DIR__."/../error.php");
            
            exit();
        }else{
            $payment_link_details = pp_get_payment_link($payment_link_id);
            $setting = pp_get_settings();
            
            if($payment_link_details['status'] == true){
                $expiry = DateTime::createFromFormat('d/m/Y', $payment_link_details['response'][0]['pl_expiry_date']);
                $now = new DateTime();
                
                if ($expiry < $now) {
                    $error_title = "Payment Link Expired";
                    $error_description = "This payment link is no longer valid. Please request a new one to complete your transaction.";
                    
                    include(__DIR__."/../error.php");
                    
                    exit();
                }else{
                    theme_include('invoice', $setting['response'][0]['invoice_theme'], $setting['response'][0]['invoice_theme'].'-payment-link-class.php', $payment_link_id);
                    
                    if (function_exists('pp_trigger_hook')) {
                        pp_trigger_hook('pp_payment_link_initialize');
                    }
                }
            }else{
                $error_title = "Payment Link Invalid or Expired";
                $error_description = "The payment link you're trying to use is either incorrect or has expired. Please check the link or request a new one to proceed.";
                
                include(__DIR__."/../error.php");
                
                exit();
            }
        }
    }else{
        $error_title = "Payment Link Invalid or Expired";
        $error_description = "The payment link you're trying to use is either incorrect or has expired. Please check the link or request a new one to proceed.";
        
        include(__DIR__."/../error.php");
        
        exit();
    }