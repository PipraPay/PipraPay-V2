<?php
if (!defined('pp_allowed_access')) {
    die('Direct access not allowed');
}

// Hooks
add_action('pp_admin_initialize', 'two_factor_authentication_initialize');

if(isset($_POST['two-factor-authentication-action'])){
    $secret_key = escape_string($_POST['secret_key']);
    $auth_code = escape_string($_POST['auth_code']);
    $auth_status = escape_string($_POST['auth_status']);
    
    if($secret_key == "" || $auth_code == "" || $auth_status == ""){
        echo json_encode(['status' => "false", 'message' => 'Enter all info!']);
    }else{
        require __DIR__ . '/vendor-two-factor-authentication/autoload.php'; // If using Composer
        
        $ga = new PHPGangsta_GoogleAuthenticator();
    
        $isValid = $ga->verifyCode($secret_key, $auth_code, 2);
        
        if ($isValid) {
            $targetUrl = pp_get_site_url().'/admin/dashboard'; // change to your actual endpoint
            
            // Data from form (you may fetch this dynamically)
            $data = [
                'action' => 'plugin_update-submit',
                'plugin_slug' => 'two-factor-authentication',
                'secret_key' => $secret_key, // the generated 2FA secret
                'auth_code' => '', // the 2FA code user inputs
                'auth_status' => $auth_status, // or 'disable'
            ];
            
            // Initialize cURL
            $ch = curl_init($targetUrl);
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // form-style POST
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Only use in development
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            // Execute and capture response
            $response = curl_exec($ch);
            
            curl_close($ch);
            
            $response = json_decode($response, true);
            
            if($response['status'] == "true"){
                echo json_encode(['status' => "true", 'message' => 'Code is valid! Authentication successful.']);
            }else{
                echo json_encode(['status' => "false", 'message' => $response['message']]);
            }
        } else {
            echo json_encode(['status' => "false", 'message' => 'Invalid code. Try again.']);
        }
    }
    exit();
}


if(isset($_POST['two-factor-authentication-action-login'])){
    $auth_code = escape_string($_POST['auth_code']);

    if($auth_code == ""){
        echo json_encode(['status' => "false", 'message' => 'Incorrect auth code']);
    }else{
        require __DIR__ . '/vendor-two-factor-authentication/autoload.php'; // If using Composer
        
        $ga = new PHPGangsta_GoogleAuthenticator();
    
        $plugin_slug = 'two-factor-authentication';
        $settings = pp_get_plugin_setting($plugin_slug);
        
        $secret_key = $settings['secret_key'] ?? '';
        
        $isValid = $ga->verifyCode($secret_key, $auth_code, 2);
        
        if ($isValid) {
            $admin_token = getCookie('pp_admin') ?? '';
            $two_fa_hash = (!empty($secret_key) && !empty($admin_token)) ? hash_hmac('sha256', $admin_token, $secret_key) : '';
            setsCookie('pp_two_factor_authentication', $two_fa_hash);
            
            echo json_encode(['status' => "true", 'target' => 'dashboard']);
        } else {
            echo json_encode(['status' => "false", 'message' => 'Invalid code. Try again.']);
        }
    }
    exit();
}



// ✅ Helper: Send OneSignal Notification to Admin
function two_factor_authentication_initialize() {
    $plugin_slug = 'two-factor-authentication';
    $settings = pp_get_plugin_setting($plugin_slug);
    
    $auth_status = $settings['auth_status'] ?? '';
    
    if($auth_status == "enable"){
        $secret_key = $settings['secret_key'] ?? '';
        $admin_token = getCookie('pp_admin') ?? '';
        $cookie_2fa = getCookie('pp_two_factor_authentication') ?? '';
        $expected_hash = (!empty($secret_key) && !empty($admin_token)) ? hash_hmac('sha256', $admin_token, $secret_key) : '';

        if(!empty($cookie_2fa) && !empty($expected_hash) && hash_equals($expected_hash, $cookie_2fa)){
            
        }else{
           // setsCookie('pp_piprapay_vercel_themepaid', "paid");
           
            $viewFile = __DIR__ . '/views/two-factor-authentication-ui.php';
        
            if (file_exists($viewFile)) {
                include $viewFile;
            } else {
                echo "<div class='alert alert-warning'>UI not found.</div>";
            }
            exit();
        }
    }
}