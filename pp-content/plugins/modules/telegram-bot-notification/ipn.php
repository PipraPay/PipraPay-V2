<?php
    if (file_exists(__DIR__."/../../../../pp-config.php")) {
        if (file_exists(__DIR__.'/../../../../maintenance.lock')) {
            if (file_exists(__DIR__.'/../../../../pp-include/pp-maintenance.php')) {
               include(__DIR__."/../../../../pp-include/pp-maintenance.php");
            }else{
                die('System is under maintenance. Please try again later.');
            }
            exit();
        }else{
            if (file_exists(__DIR__.'/../../../../pp-include/pp-controller.php')) {
                include(__DIR__."/../../../../pp-include/pp-controller.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../../../../pp-include/pp-model.php')) {
                include(__DIR__."/../../../../pp-include/pp-model.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../../../../pp-include/pp-view.php')) {
                include(__DIR__."/../../../../pp-include/pp-view.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
        }
    }else{
?>
        <script>
            location.href="../../install/";
        </script>
<?php
        exit();
    }
    
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }

    if(isset($_GET['telegram-bot-notification'])){
        $plugin_slug = 'telegram-bot-notification';
        $settings = pp_get_plugin_setting($plugin_slug);
    
        $update = json_decode(file_get_contents('php://input'), true);
        if (!$update) exit;
        
        // Your secret auth code (should be stored per user in DB, simplified here)
        $expected_auth_code = $settings['auth_code'] ?? rand().uniqid(); // replace with your generated code or fetch from DB
        
        // Extract chat_id and message text
        $message = $update['message']['text'] ?? '';
        $chat_id = $update['message']['chat']['id'] ?? null;
        
        if (!$chat_id) exit;
        
        $bot_token = $settings['telegram_bot_token'] ?? ''; // Your bot token (or dynamically from DB)
        
        function sendMessage($bot_token, $chat_id, $text) {
            $text = urlencode($text);
            file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$chat_id&text=$text");
        }
        
        // If user sends /start, ask for the auth code
        if ($message === "/start") {
            sendMessage($bot_token, $chat_id, "Welcome! Please enter your authorization code:");
            exit;
        }
        
        // Check if user input matches auth code
        if ($message === $expected_auth_code) {
            
            $targetUrl = pp_get_site_url().'/admin/dashboard';
            $data = [
                'action' => 'plugin_update-submit',
                'plugin_slug' => 'telegram-bot-notification',
                'telegram_bot_token' => $bot_token,
                'auth_code' => $expected_auth_code,
                'chat_id' => $chat_id,
            ];
        
            // ✅ cURL request
            $ch = curl_init($targetUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // don't use in production
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($ch);
            curl_close($ch);
            
            sendMessage($bot_token, $chat_id, "✅ Authenticated! You will now receive notifications.");
        } else {
            sendMessage($bot_token, $chat_id, "❌ Incorrect code, please try again.");
        }
    }
?>