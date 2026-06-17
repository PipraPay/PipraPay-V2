<?php
    if (file_exists(__DIR__."/../pp-config.php")) {
?>
        <script>
            location.href="https://<?php echo $_SERVER['HTTP_HOST']?>";
        </script>
<?php
        exit();
    }
    
    $phpVersion = PHP_VERSION;
    $requirements = [
        [
            'name'  => "PHP Version 7.4.x to 8.3.x ($phpVersion detected)",
            'check' => version_compare($phpVersion, '7.4.0', '>=') && version_compare($phpVersion, '8.4.0', '<')
        ],
        [
            'name' => "cURL Extension",
            'check' => function_exists('curl_version')
        ],
        [
            'name' => "MySQLi Extension",
            'check' => function_exists('mysqli_connect')
        ],
        [
            'name' => "GD Library",
            'check' => extension_loaded('gd')
        ],
        [
            'name' => "Fileinfo Extension",
            'check' => extension_loaded('fileinfo')
        ],
        [
            'name' => "Imagick",
            'check' => extension_loaded('imagick')
        ],
        [
            'name' => 'ZipArchive',
            'check' => class_exists('ZipArchive')
        ],
    ];
        
    $folders = [
        'invoice' => is_writable('../invoice'),
        'payment' => is_writable('../payment'),
        'admin'   => is_writable('../admin'),
        'pp-include'   => is_writable('../pp-include'),
    ];
    
    $is_requirment_fill = true;
    
    if (isset($_POST['install'])) {
        $datahost = $_POST['datahost'];
        $dataport = $_POST['dataport'];
        $dbName   = $_POST['dbName'];
        $dbPrefix = $_POST['dbPrefix'];
        $dbUser   = $_POST['dbUser'];
        $dbPass   = $_POST['dbPass'];
        $dbSSL    = isset($_POST['dbSSL']) ? 'true' : 'false';
        $dbCAPath = isset($_POST['dbCAPath']) ? trim($_POST['dbCAPath']) : '';

        $adminName = $_POST['adminName'];
        $adminEmail = $_POST['adminEmail'];
        $adminUser = $_POST['adminUser'];
        $adminPass = $_POST['adminPass'];
    
        // Validate
        if (empty($datahost) || empty($dataport) || empty($dbName) || empty($dbPrefix) || empty($dbUser)) {
            echo json_encode(["status" => "false", "message" => "Error: All database connection fields are required."]);
            exit();
        }
    
        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "false", "message" => "Error: Invalid administrator email address."]);
            exit();
        }
    
        // Connect to DB
        $conn = new mysqli();

        if ($dbSSL === 'true') {
            // Use CA path if provided, otherwise use default SSL settings
            if (!empty($dbCAPath) && file_exists($dbCAPath)) {
                mysqli_ssl_set($conn, null, null, $dbCAPath, null, null);
            } else {
                mysqli_ssl_set($conn, null, null, null, null, null);
            }
            $conn->real_connect($datahost, $dbUser, $dbPass, $dbName, $dataport, null, MYSQLI_CLIENT_SSL);
        } else {
            $conn->real_connect($datahost, $dbUser, $dbPass, $dbName, $dataport);
        }

        if ($conn->connect_error) {
            $errorMessage = "Error: Database connection failed.";
            if ($dbSSL === 'true' && strpos($conn->connect_error, 'insecure transport') !== false) {
                $errorMessage .= " Try disabling SSL if your database doesn't require secure connections.";
            } elseif ($dbSSL === 'false' && strpos($conn->connect_error, 'insecure transport') !== false) {
                $errorMessage .= " This database requires SSL connections. Please enable the SSL option.";
            }
            echo json_encode(["status" => "false", "message" => $errorMessage]);
            exit();
        }
    
        // Save config.php
        $configContent = "<?php
    \$db_host = '$datahost';
    \$db_user = '$dbUser';
    \$db_pass = '$dbPass';
    \$db_name = '$dbName';
    \$db_prefix = '$dbPrefix';
    \$db_ssl = '" . addslashes($dbSSL) . "';
    \$db_ca_path = '" . addslashes($dbCAPath) . "';
    \$mode = 'live';
    \$password_reset = 'off';
?>";
    
        file_put_contents('../pp-config.php', $configContent);
    
        // Load and import SQL with prefix
        $sqlUrl = "database.sql";
        $sql = file_get_contents($sqlUrl);
        if ($sql === false) {
            echo json_encode(["status" => "false", "message" => "Error: Failed to fetch SQL file."]);
            exit();
        }
    
        // Replace __PREFIX__ with actual prefix
        $sql = str_replace("__PREFIX__", $dbPrefix, $sql);
    
        // Execute all queries
        $queries = array_filter(array_map('trim', explode(";", $sql)));
        foreach ($queries as $query) {
            if (!empty($query)) {
                $conn->query($query);
            }
        }
    
        $sqlUrlCurrency = "currency.sql";
        $sqlContentCurrency = file_get_contents($sqlUrlCurrency);
        
        if ($sqlContentCurrency === false) {
            echo json_encode(["status" => "false", "message" => "Error: Failed to fetch SQL file."]);
            exit();
        }
        
        // Replace old table name with your real one
        $sqlContentCurrency = str_replace("INSERT INTO `currency`", "INSERT INTO `{$dbPrefix}currency`", $sqlContentCurrency);
        
        // Run the modified SQL
        if ($conn->multi_query($sqlContentCurrency)) {
            do {
                $conn->store_result();
            } while ($conn->more_results() && $conn->next_result());
        } else {
            echo json_encode(["status" => "false", "message" => "Error: Failed to fetch SQL file."]);
            exit();
        }

        $sqlUrlTimezone = "timezone.sql";
        $sqlContentTimezone = file_get_contents($sqlUrlTimezone);
        
        if ($sqlContentTimezone === false) {
            echo json_encode(["status" => "false", "message" => "Error: Failed to fetch SQL file."]);
            exit();
        }
        
        // Replace old table name with your real one
        $sqlContentTimezone = str_replace("INSERT INTO `timezone`", "INSERT INTO `{$dbPrefix}timezone`", $sqlContentTimezone);
        
        // Run the modified SQL
        if ($conn->multi_query($sqlContentTimezone)) {
            do {
                $conn->store_result();
            } while ($conn->more_results() && $conn->next_result());
        } else {
            echo json_encode(["status" => "false", "message" => "Error: Failed to fetch SQL file."]);
            exit();
        }

        // Insert admin user
        $hashedPass = password_hash($adminPass, PASSWORD_BCRYPT);
        $insertAdmin = "INSERT INTO `{$dbPrefix}admins` (name, email, username, password) VALUES 
                       ('" . $conn->real_escape_string($adminName) . "', 
                        '" . $conn->real_escape_string($adminEmail) . "', 
                        '" . $conn->real_escape_string($adminUser) . "', 
                        '" . $conn->real_escape_string($hashedPass) . "')";
        $conn->query($insertAdmin);
            
        $insertSettings = "INSERT INTO `{$dbPrefix}settings` (site_name) VALUES ('--')";
        $conn->query($insertSettings);
                   
        echo json_encode(["status" => "true", "message" => "Installation completed successfully."]);
        exit();
    }
    
    if (isset($_POST['test'])) {
        $datahost = $_POST['datahost'];
        $dataport = $_POST['dataport'];
        $dbName   = $_POST['dbName'];
        $dbPrefix = $_POST['dbPrefix'];
        $dbUser   = $_POST['dbUser'];
        $dbPass   = $_POST['dbPass'];
        $dbSSL    = isset($_POST['dbSSL']) ? 'true' : 'false';
        $dbCAPath = isset($_POST['dbCAPath']) ? trim($_POST['dbCAPath']) : '';

        $conn = new mysqli();

        if ($dbSSL === 'true') {
            // Use CA path if provided, otherwise use default SSL settings
            if (!empty($dbCAPath) && file_exists($dbCAPath)) {
                mysqli_ssl_set($conn, null, null, $dbCAPath, null, null);
            } else {
                mysqli_ssl_set($conn, null, null, null, null, null);
            }
            $conn->real_connect($datahost, $dbUser, $dbPass, $dbName, $dataport, null, MYSQLI_CLIENT_SSL);
        } else {
            $conn->real_connect($datahost, $dbUser, $dbPass, $dbName, $dataport);
        }

        if ($conn->connect_error) {
            $errorMessage = "Connection failed: " . $conn->connect_error;
            if ($dbSSL === 'true' && strpos($conn->connect_error, 'insecure transport') !== false) {
                $errorMessage .= " Try disabling SSL if your database doesn't require secure connections.";
            } elseif ($dbSSL === 'false' && strpos($conn->connect_error, 'insecure transport') !== false) {
                $errorMessage .= " This database requires SSL connections. Please enable the SSL option.";
            }
            echo json_encode([
                "status" => "false",
                "message" => $errorMessage
            ]);
        } else {
            echo json_encode([
                "status" => "true",
                "message" => "Database connection successful!"
            ]);
            $conn->close();
        }
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PipraPay - Script Installer</title>
    <link rel="icon" type="image/x-icon" href="https://cdn.piprapay.com/media/favicon.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link href="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/style-install.css" rel="stylesheet">
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1 class="installer-title"><i class="fa fa-cog me-2"></i> PipraPay Script Installation</h1>
        </div>
        
        <div class="installer-body">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Requirements</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Database</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Administrator</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Finish</div>
                </div>
            </div>
            
            <!-- Step Contents -->
            <div class="step-content active" data-step-content="1">
                <h3 class="mb-4">System Requirements Check</h3>
                <p class="text-muted mb-4">Before proceeding with the installation, please ensure your server meets the following requirements:</p>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">PHP Requirements</h5>
                    </div>
                    <div class="card-body">
                        <ul class="requirements-list">
                            <?php foreach ($requirements as $req): ?>
                                <li>
                                    <?php if ($req['check']): ?>
                                        <i class="fa fa-check-circle requirement-met me-2"></i>
                                    <?php else: ?>
                                        <?php $is_requirment_fill = false;?>
                                        <i class="fa fa-times-circle requirement-not-met me-2"></i>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($req['name']) ?> (<?= $req['check'] ? 'Enabled' : 'Not Enabled' ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">File Permissions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="requirements-list">
                            <?php foreach ($folders as $name => $isWritable): ?>
                                <li>
                                    <?php if ($isWritable): ?>
                                        <i class="fa fa-check-circle requirement-met me-2"></i>
                                        /<?= $name ?> (Writable)
                                    <?php else: ?>
                                        <?php $is_requirment_fill = false;?>
                                        <i class="fa fa-times-circle requirement-not-met me-2"></i>
                                        /<?= $name ?> (Not Writable)
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <?php
                    if($is_requirment_fill == false){
                ?>
                        <div class="alert alert-warning mt-4">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            Please fix all requirements marked in red before proceeding with the installation.
                        </div>
                <?php
                    }else{
                ?>
                        <div class="alert alert-success mt-4">
                            <i class="fa fa-check-circle me-2"></i>
                            All requirements are met. You can proceed with the installation.
                        </div>
                <?php
                    }
                ?>
            </div>
            
            <div class="step-content" data-step-content="2">
                <h3 class="mb-4">Database Configuration</h3>
                <p class="text-muted mb-4">Please provide your database connection details:</p>
                
                <div class="database-type-selector">
                    <div class="database-card selected" data-host="localhost" data-port="3306">
                        <div class="database-icon">
                            <i class="fa fa-database"></i>
                        </div>
                        <h5>MySQL</h5>
                        <p class="text-muted small">Recommended for most installations</p>
                    </div>
                    <!--<div class="database-card">
                        <div class="database-icon">
                            <i class="fa fa-server"></i>
                        </div>
                        <h5>PostgreSQL</h5>
                        <p class="text-muted small">For advanced users</p>
                    </div>
                    <div class="database-card">
                        <div class="database-icon">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <h5>SQLite</h5>
                        <p class="text-muted small">For testing purposes</p>
                    </div>!-->
                </div>
                
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="dbHost" class="form-label">Database Host</label>
                                <input type="text" class="form-control" id="dbHost" value="localhost" required>
                                <div class="form-text">Usually 'localhost' or '127.0.0.1'</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="dbPort" class="form-label">Database Port</label>
                                <input type="text" class="form-control" id="dbPort" value="3306" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="dbName" class="form-label">Database Name</label>
                                <input type="text" class="form-control" id="dbName" required>
                                <div class="form-text">The database must already exist</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="dbPrefix" class="form-label">Table Prefix</label>
                                <input type="text" class="form-control" id="dbPrefix" value="pp_">
                                <div class="form-text">Optional prefix for database tables</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="dbUser" class="form-label">Database Username</label>
                                <input type="text" class="form-control" id="dbUser" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dbPass" class="form-label">Database Password</label>
                                <input type="password" class="form-control" id="dbPass">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="dbSSL">
                                <label class="form-check-label" for="dbSSL">
                                    <strong>Enable SSL Connection</strong>
                                    <div class="form-text">Required for TiDB Serverless and other secure database clusters</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3" id="caPathRow" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="dbCAPath" class="form-label">CA Certificate Path</label>
                                <input type="text" class="form-control" id="dbCAPath" placeholder="/etc/ssl/cert.pem">
                                <div class="form-text">Path to CA certificate file (e.g., /etc/ssl/cert.pem on macOS). Leave empty to use default certificates.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn btn-light" id="testBtn">
                        Test <i class="fa fa-refresh ms-2"></i>
                    </div>
                    
                    <span class="db-connection-response"></span>
                </form>
            </div>
            
            <div class="step-content" data-step-content="3">
                <h3 class="mb-4">Administrator Account Setup</h3>
                <p class="text-muted mb-4">Create your administrator account to manage the application:</p>
                
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="adminName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="adminName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="adminEmail" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="adminEmail" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="adminUser" class="form-label">Username</label>
                                <input type="text" class="form-control" id="adminUser" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="adminPass" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="adminPass" required>
                                    <button class="btn btn-outline-secondary" type="button" id="showPass">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength mt-2">
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">Password strength: Weak</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <span class="admin-connection-response">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            Remember these credentials. You'll need them to access the admin panel.
                        </div>
                    </span>
                </form>
            </div>
            
            <div class="step-content" data-step-content="4">
                <div class="success-message">
                    <div class="success-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h3 class="mb-3">Installation Complete!</h3>
                    <p class="text-muted mb-4">Your application has been successfully installed and is ready to use.</p>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Installation Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="summary-item">
                                <span class="summary-item-label">Admin URL:</span>
                                <span>https://<?php echo $_SERVER['HTTP_HOST']?>/admin/</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-item-label">Database Tables Created:</span>
                                <span>24</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-item-label">Administrator Username:</span>
                                <span class="administrator-username"></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-item-label">Administrator Email:</span>
                                <span class="administrator-email"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle me-2"></i>
                        <strong>Important:</strong> For security reasons, please delete the <code>/install</code> directory.
                    </div>
                    
                    <a href="https://<?php echo $_SERVER['HTTP_HOST']?>/admin/" class="btn btn-primary btn-lg">
                        <i class="fa fa-rocket me-2"></i> Launch Application
                    </a>
                </div>
            </div>
        </div>
        
        <div class="installer-footer">
            <button class="btn btn-secondary" id="prevBtn" disabled>
                <i class="fa fa-arrow-left me-2"></i> Previous
            </button>
            
            <?php
                if($is_requirment_fill == false){
            ?>
                    <button class="btn btn-warning" onclick="location.href='https://<?php echo $_SERVER['HTTP_HOST']?>/install/'">
                        Check Again <i class="fa fa-refresh ms-2"></i>
                    </button>
            <?php
                }else{
            ?>
                    <button class="btn btn-primary" id="nextBtn">
                        Next <i class="fa fa-arrow-right ms-2"></i>
                    </button>
            <?php
                }
            ?>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            // Toggle CA path field when SSL checkbox changes
            $('#dbSSL').change(function() {
                if ($(this).is(':checked')) {
                    $('#caPathRow').show();
                } else {
                    $('#caPathRow').hide();
                }
            });

            $('#testBtn').click(function (e) { 
                document.querySelector("#testBtn").innerHTML = '<i class="fa fa-circle-o-notch fa-spin" style="font-size:18px"></i>';

                var datahost =document.querySelector("#dbHost").value;
                var dataport =document.querySelector("#dbPort").value;
                var dbName =document.querySelector("#dbName").value;
                var dbPrefix =document.querySelector("#dbPrefix").value;
                var dbUser =document.querySelector("#dbUser").value;
                var dbPass =document.querySelector("#dbPass").value;
                var dbSSL = document.querySelector("#dbSSL").checked;
                var dbCAPath = document.querySelector("#dbCAPath").value;

                $.ajax
                ({
                    type: "POST",
                    url: "https://<?php echo $_SERVER['HTTP_HOST']?>/install/",
                    data: { "test": "test", "datahost": datahost, "dataport": dataport, "dbName": dbName, "dbPrefix": dbPrefix, "dbUser": dbUser, "dbPass": dbPass, "dbSSL": dbSSL, "dbCAPath": dbCAPath },
                    success: function (data) {
                        document.querySelector("#testBtn").innerHTML = 'Test <i class="fa fa-refresh ms-2"></i>';
                        
                        try {
                            var dedata = JSON.parse(data);
                            if (dedata.status === "false") {
                                $(".db-connection-response").html('<div class="alert alert-danger">' + dedata.message + '</div>');
                            } else {
                                $(".db-connection-response").html('<div class="alert alert-success">' + dedata.message + '</div>');
                            }
                        } catch (e) {
                            console.error("Invalid JSON response:", data);
                            $(".db-connection-response").html('<div class="alert alert-danger">'+data+'</div>');
                        }
                    }
                });
            });
        });
    
        const passwordInput = document.getElementById('adminPass');
        const progressBar = document.querySelector('.progress-bar');
        const strengthText = document.querySelector('.password-strength small');
    
        const showPassBtn = document.getElementById('showPass');
        showPassBtn.addEventListener('click', () => {
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
            showPassBtn.innerHTML = passwordInput.type === 'password'
                ? '<i class="fa fa-eye"></i>'
                : '<i class="fa fa-eye-slash"></i>';
        });
    
        passwordInput.addEventListener('input', function () {
            const val = passwordInput.value;
            let strength = 0;
    
            if (val.length > 5) strength += 1;
            if (val.length > 8) strength += 1;
            if (/[A-Z]/.test(val)) strength += 1;
            if (/[0-9]/.test(val)) strength += 1;
            if (/[^A-Za-z0-9]/.test(val)) strength += 1;
    
            let strengthLabel = '';
            let barWidth = 0;
            let barColor = 'bg-danger';
    
            switch (strength) {
                case 0:
                case 1:
                    strengthLabel = 'Very Weak';
                    barWidth = 20;
                    barColor = 'bg-danger';
                    break;
                case 2:
                    strengthLabel = 'Weak';
                    barWidth = 40;
                    barColor = 'bg-warning';
                    break;
                case 3:
                    strengthLabel = 'Moderate';
                    barWidth = 60;
                    barColor = 'bg-info';
                    break;
                case 4:
                    strengthLabel = 'Strong';
                    barWidth = 80;
                    barColor = 'bg-primary';
                    break;
                case 5:
                    strengthLabel = 'Very Strong';
                    barWidth = 100;
                    barColor = 'bg-success';
                    break;
            }
    
            progressBar.style.width = barWidth + '%';
            progressBar.className = 'progress-bar ' + barColor;
            strengthText.textContent = 'Password strength: ' + strengthLabel;
        });
    </script>

    <script>
        // Simple step navigation (for demo purposes)
        document.addEventListener('DOMContentLoaded', function() {
            const steps = document.querySelectorAll('.step');
            const stepContents = document.querySelectorAll('.step-content');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            let currentStep = 1;
            
            // Database type selection
            const databaseCards = document.querySelectorAll('.database-card');
            databaseCards.forEach(card => {
                card.addEventListener('click', function() {
                    databaseCards.forEach(c => c.classList.remove('selected'));

                    this.classList.add('selected');
                    
                    const datahost = this.getAttribute('data-host');
                    const dataport = this.getAttribute('data-port');
                    
                    document.querySelector("#dbHost").value = datahost;
                    document.querySelector("#dbPort").value = dataport;
                });
            });
            
            // Password visibility toggle
            const showPassBtn = document.getElementById('showPass');
            const adminPass = document.getElementById('adminPass');
            showPassBtn.addEventListener('click', function() {
                const type = adminPass.getAttribute('type') === 'password' ? 'text' : 'password';
                adminPass.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fa fa-eye"></i>' : '<i class="fa fa-eye-slash"></i>';
            });
            
            // Step navigation
            function updateSteps() {
                steps.forEach((step, index) => {
                    const stepNum = parseInt(step.getAttribute('data-step'));
                    
                    if (stepNum < currentStep) {
                        step.classList.remove('active');
                        step.classList.add('completed');
                    } else if (stepNum === currentStep) {
                        step.classList.add('active');
                        step.classList.remove('completed');
                    } else {
                        step.classList.remove('active', 'completed');
                    }
                });
                
                stepContents.forEach(content => {
                    const contentStep = parseInt(content.getAttribute('data-step-content'));
                    content.classList.toggle('active', contentStep === currentStep);
                });
                
                prevBtn.disabled = currentStep === 1;
                nextBtn.innerHTML = currentStep === steps.length ? 'Finish <i class="fa fa-check ms-2"></i>' : 
                                    'Next <i class="fa fa-arrow-right ms-2"></i>';
            }
            
            nextBtn.addEventListener('click', function() {
                if (currentStep < steps.length) {
                    if(currentStep == 2){
                        var datahost =document.querySelector("#dbHost").value;
                        var dataport =document.querySelector("#dbPort").value;
                        var dbName =document.querySelector("#dbName").value;
                        var dbPrefix =document.querySelector("#dbPrefix").value;
                        var dbUser =document.querySelector("#dbUser").value;
                        var dbPass =document.querySelector("#dbPass").value;
                        
                        if(datahost == "" || dataport == "" || dbName == "" || dbPrefix == "" || dbUser == "" || dbPass == ""){
                            document.querySelector(".db-connection-response").innerHTML = '<div class="alert alert-danger"> <i class="fa fa-info-circle me-2"></i> Error: All database connection fields (host, username, database name) are required.</div>';
                        }else{
                            document.querySelector(".db-connection-response").innerHTML = '';
                            currentStep++;
                            updateSteps();
                        }
                    }else{
                        if(currentStep == 3){
                            var datahost =document.querySelector("#dbHost").value;
                            var dataport =document.querySelector("#dbPort").value;
                            var dbName =document.querySelector("#dbName").value;
                            var dbPrefix =document.querySelector("#dbPrefix").value;
                            var dbUser =document.querySelector("#dbUser").value;
                            var dbPass =document.querySelector("#dbPass").value;
                            var dbSSL = document.querySelector("#dbSSL").checked;
                            var dbCAPath = document.querySelector("#dbCAPath").value;

                            var adminName =document.querySelector("#adminName").value;
                            var adminEmail =document.querySelector("#adminEmail").value;
                            var adminUser =document.querySelector("#adminUser").value;
                            var adminPass =document.querySelector("#adminPass").value;

                            if(adminName == "" || adminEmail == "" || adminUser == "" || adminPass == ""){
                                document.querySelector(".admin-connection-response").innerHTML = '<div class="alert alert-danger"> <i class="fa fa-info-circle me-2"></i> Error: All administrator credentials are required to complete the installation.</div>';
                            }else{
                                document.querySelector("#nextBtn").innerHTML = '<i class="fa fa-circle-o-notch fa-spin" style="font-size:18px"></i>';

                                $.ajax
                                ({
                                    type: "POST",
                                    url: "https://<?php echo $_SERVER['HTTP_HOST']?>/install/",
                                    data: { "install": "start", "datahost": datahost, "dataport": dataport, "dbName": dbName, "dbPrefix": dbPrefix, "dbUser": dbUser, "dbPass": dbPass, "dbSSL": dbSSL, "dbCAPath": dbCAPath, "adminName": adminName, "adminEmail": adminEmail, "adminUser": adminUser, "adminPass": adminPass },
                                    success: function (data) {
                                        document.querySelector("#nextBtn").innerHTML = 'Next <i class="fa fa-arrow-right ms-2"></i>';
                                        
                                        var dedata = JSON.parse(data);
                                        
                                        if(dedata.status == "false"){
                                            document.querySelector(".admin-connection-response").innerHTML = '<div class="alert alert-danger"> <i class="fa fa-info-circle me-2"></i> Error: '+dedata.message+'</div>';
                                        }else{
                                            document.querySelector(".administrator-email").innerHTML = adminEmail;
                                            document.querySelector(".administrator-username").innerHTML = adminUser;
                                            
                                            document.querySelector(".admin-connection-response").innerHTML = '';
                                            
                                            currentStep++;
                                            updateSteps();
                                        }
                                    }
                                });
                            }
                        }else{
                            currentStep++;
                            updateSteps();
                        }
                    }
                } else {
                    location.href="https://<?php echo $_SERVER['HTTP_HOST']?>/admin/";
                }
            });
            
            prevBtn.addEventListener('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateSteps();
                }
            });
            
            // Initialize
            updateSteps();
        });
    </script>
</body>
</html>