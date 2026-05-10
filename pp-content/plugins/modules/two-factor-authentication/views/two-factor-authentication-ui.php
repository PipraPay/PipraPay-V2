<?php
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
    <title>Two-factor authentication - PipraPay</title>
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
                <p class="text-muted">Please enter auth code to continue</p>
            </div>

            <!-- Login Form -->
            <div class="login-form" id="loginForm">
                <div class="form-group">
                    <label for="auth_code">Auth Code</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" id="auth_code" class="form-control" placeholder="Enter your auth code" required>
                    </div>
                </div>

                <span class="login-response"></span>

                <button type="submit" class="btn btn-login" id="loginButton"><span id="loginText">Verify</span></button>
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
        document.querySelector('#loginButton').addEventListener('click', function() {
            var auth_code = document.querySelector("#auth_code").value;

            if(auth_code == ""){
                document.querySelector(".login-response").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> Incorrect auth code</div>';
            }else{
                document.querySelector("#loginButton").innerHTML = '<i class="fa fa-circle-o-notch fa-spin" style="font-size:18px"></i>';
            
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "two-factor-authentication-action-login": "proceed", "auth_code": auth_code },
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