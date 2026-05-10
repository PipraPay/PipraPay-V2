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
            
            if($global_user_login == true){
?>
                <script>
                    location.href="<?php echo PP_URL; ?>/admin/dashboard";
                </script>
<?php
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PipraPay</title>
    <link rel="icon" type="image/x-icon" href="https://cdn.piprapay.com/media/favicon.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo PP_URL; ?>/pp-external/assets/style-login.css?v=1.4">
</head>
<body>
    <div class="login-container">
        <div class="login-wrapper">
            <!-- Logo Section -->
            <div class="logo-container">
                <div class="logo-circle">
                    <i class="fa fa-user"></i>
                </div>
                <h1>Welcome Back</h1>
                <p class="text-muted">Please enter your credentials to login</p>
            </div>

            <!-- Login Form -->
            <div class="login-form" id="loginForm">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" id="username" class="form-control" placeholder="Enter your username or email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" id="password" class="form-control" placeholder="Enter your password" required>
                        <button type="button" class="btn btn-eye" id="togglePassword">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    
                    <div class="form-check" style=" margin-top: -5px; ">
                        <a href="forgot" style=" text-decoration: none; color: #3BB77E; ">Forgot Password?</a>
                    </div>
                </div>
                
                <span class="login-response"></span>

                <button type="submit" class="btn btn-login" id="loginButton"><span id="loginText">Log In</span></button>
            </div>
        </div>

        <!-- Background Animation -->
        <div class="background-animation">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
        
            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fa fa-eye"></i>' : '<i class="fa fa-eye-slash"></i>';
            });

            // Check for remembered username
            const rememberedUsername = localStorage.getItem('rememberedUsername');
            if (rememberedUsername) {
                document.getElementById('username').value = rememberedUsername;
                document.getElementById('rememberMe').checked = true;
            }
        
            // Add animation to form inputs on focus
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    this.parentElement.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.05)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = '';
                    this.parentElement.style.boxShadow = '';
                });
            });
        });
               
        document.querySelector('#loginButton').addEventListener('click', function() {
            var username = document.querySelector("#username").value;
            var password = document.querySelector("#password").value;
            
            if(username == "" || password == ""){
                document.querySelector(".login-response").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> Incorrect credentials</div>';
            }else{
                document.querySelector("#loginButton").innerHTML = '<i class="fa fa-circle-o-notch fa-spin" style="font-size:18px"></i>';
            
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/login",
                    data: { "action": "login", "email_username": username, "password": password },
                    success: function (data) {
                        document.querySelector("#loginButton").innerHTML = '<span id="loginText">Log In</span>';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".login-response").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            location.href = dedata.target;
                            document.querySelector(".login-response").innerHTML = '';
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>