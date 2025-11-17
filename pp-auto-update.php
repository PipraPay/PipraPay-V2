<?php
    if (file_exists(__DIR__."/pp-config.php")) {
        if (file_exists(__DIR__.'/maintenance.lock')) {
            if (file_exists(__DIR__.'/pp-include/pp-maintenance.php')) {
               include(__DIR__."/pp-include/pp-maintenance.php");
            }else{
                die('System is under maintenance. Please try again later.');
            }
            exit();
        }else{
            if (file_exists(__DIR__.'/pp-include/pp-controller.php')) {
                include(__DIR__."/pp-include/pp-controller.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            if (file_exists(__DIR__.'/pp-include/pp-model.php')) {
                include(__DIR__."/pp-include/pp-model.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if(isset($_POST['mh-piprapay-auto-update'])){
                
            }else{
                if($global_user_login == false){
?>
                    <script>
                        location.href="https://<?php echo $_SERVER['HTTP_HOST']?>/admin/login";
                    </script>
<?php
                    exit();
                }
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
    
    if (isset($_POST['auto-update'])) {
        if ($mode == "demo") {
            echo json_encode(["status" => "false", "message" => "Error: Demo mode is active"]);
            exit();
        }
    
        $remote_update_url = 'updates/version.json';
        $local_version_file = 'version.json';
        $maintenance_file = 'maintenance.lock';
        $update_zip = 'update.zip';
        $update_sql_path = 'update.sql';
    
        file_put_contents($maintenance_file, '1');
    
        $remote_data = json_decode(file_get_contents($remote_update_url), true);
        if (!$remote_data || empty($remote_data['latest_version']) || empty($remote_data['releases'][0]['download_url'])) {
            unlink($maintenance_file);
            echo json_encode(["status" => "false", "message" => "Error: Invalid update info"]);
            exit();
        }
    
        $local_data = json_decode(file_get_contents($local_version_file), true);
        $current_version = $local_data['version'] ?? '0.0.0';
        $new_version = $remote_data['latest_version'];
    
        if (version_compare($new_version, $current_version, '<=')) {
            unlink($maintenance_file);
            echo json_encode(["status" => "false", "message" => "Already up to date"]);
            exit();
        }
    
        file_put_contents($update_zip, file_get_contents($remote_data['releases'][0]['download_url']));
        if (!file_exists($update_zip)) {
            unlink($maintenance_file);
            echo json_encode(["status" => "false", "message" => "Download failed"]);
            exit();
        }
    
        $zip = new ZipArchive;
        $tempPath = __DIR__ . '/temp_update_' . time();
        mkdir($tempPath);
    
        if ($zip->open($update_zip) === TRUE) {
            $zip->extractTo($tempPath);
            $zip->close();
            unlink($update_zip);
    
            // Detect the first folder inside the extracted ZIP (e.g. update-1.0.1)
            $subdirs = array_filter(glob($tempPath . '/*'), 'is_dir');
            $realSource = count($subdirs) === 1 ? $subdirs[0] : $tempPath;
    
            // Move files to root
            $dirIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($realSource, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
    
            foreach ($dirIterator as $item) {
                $destPath = __DIR__ . '/' . $dirIterator->getSubPathName();
                if ($item->isDir()) {
                    @mkdir($destPath, 0755, true);
                } else {
                    copy($item, $destPath);
                }
            }
    
            // Clean up
            function deleteDir($dir) {
                foreach (scandir($dir) as $item) {
                    if ($item == '.' || $item == '..') continue;
                    $path = $dir . DIRECTORY_SEPARATOR . $item;
                    is_dir($path) ? deleteDir($path) : unlink($path);
                }
                rmdir($dir);
            }
            deleteDir($tempPath);
    
        } else {
            unlink($maintenance_file);
            echo json_encode(["status" => "false", "message" => "Failed to unzip"]);
            exit();
        }
    
        // SQL update
        if (file_exists($update_sql_path)) {
            $conn = new mysqli();

            if (isset($db_ssl) && $db_ssl === 'true') {
                // Use CA path if provided and file exists, otherwise use default SSL settings
                if (isset($db_ca_path) && !empty($db_ca_path) && file_exists($db_ca_path)) {
                    mysqli_ssl_set($conn, null, null, $db_ca_path, null, null);
                } else {
                    mysqli_ssl_set($conn, null, null, null, null, null);
                }
                $conn->real_connect($db_host, $db_user, $db_pass, $db_name, null, null, MYSQLI_CLIENT_SSL);
            } else {
                $conn->real_connect($db_host, $db_user, $db_pass, $db_name);
            }

            if ($conn->connect_error) {
                unlink($maintenance_file);
                echo json_encode(["status" => "false", "message" => "DB connection failed"]);
                exit();
            }
    
            $sql = file_get_contents($update_sql_path);
            if ($db_prefix) {
                $sql = str_replace('__PREFIX__', $db_prefix, $sql);
            }
    
            if ($conn->multi_query($sql)) {
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->more_results() && $conn->next_result());
            } else {
                unlink($maintenance_file);
                echo json_encode(["status" => "false", "message" => "SQL error: " . $conn->error]);
                exit();
            }
            $conn->close();
            unlink($update_sql_path);
        }
    
        // Update version file
        $localData = file_exists($local_version_file) ? json_decode(file_get_contents($local_version_file), true) : [];
        $localData['version'] = $new_version;
        foreach ($remote_data['releases'] as $release) {
            if ($release['version'] === $new_version) {
                $localData['release_date'] = $release['release_date'] ?? null;
                $localData['changelog'] = $release['changelog'] ?? [];
                break;
            }
        }
        file_put_contents($local_version_file, json_encode($localData, JSON_PRETTY_PRINT));
    
        unlink($maintenance_file);
    
        echo json_encode(["status" => "true", "message" => "Update complete: $new_version"]);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PipraPay - Auto Update</title>
    <link rel="icon" type="image/x-icon" href="https://cdn.piprapay.com/media/favicon.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link href="https://<?php echo $_SERVER['HTTP_HOST']?>/pp-external/assets/style-auto-update.css?v=1" rel="stylesheet">
</head>
<body>
    <div class="d-flex align-items-center justify-content-center h-100 p-3">
        <div class="update-container">
            <div class="update-icon">
                <i class="fa fa-sync-alt"></i>
            </div>
            <h1 class="display-5 fw-bold mb-3">Website Update Available</h1>
            <p class="lead mb-4">We're upgrading to a better version with new features and improvements</p>
            
            <div class="version-comparison">
                <?php
                    $versionFile = 'version.json';
                    
                    if (file_exists($versionFile)) {
                        $versionData = json_decode(file_get_contents($versionFile), true);
                    
                        $version = $versionData['version'] ?? 'N/A';
                        $changelog = $versionData['changelog'][0] ?? null;
                    
                        if ($changelog) {
                            echo '<div class="version-card current-version">';
                            echo '    <div class="version-label">Current Version</div>';
                            echo '    <div class="version-number">v' . htmlspecialchars($version) . '</div>';
                            echo '    <ul class="version-features">';
                            foreach ($changelog['details'] as $feature) {
                                echo '        <li>' . htmlspecialchars($feature) . '</li>';
                            }
                            echo '    </ul>';
                            echo '</div>';
                        } else {
                            echo '<div class="version-card current-version">';
                            echo '    <div class="version-label">Current Version</div>';
                            echo '    <div class="version-number">v' . htmlspecialchars($version) . '</div>';
                            echo '    <p>No changelog available.</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="version-card current-version">';
                        echo '    <div class="version-label">Current Version</div>';
                        echo '    <div class="version-number">Unknown</div>';
                        echo '    <p>version.json not found.</p>';
                        echo '</div>';
                    }
                ?>
                
                <div class="d-flex align-items-center">
                    <div class="arrow-animation">
                        <i class="fa fa-long-arrow-right"></i>
                        <i class="fa fa-long-arrow-down"></i>
                    </div>
                </div>
                
                <?php
                    // Paths
                    $currentVersionPath = 'version.json';
                    $remoteVersionUrl = 'updates/version.json'; // 🔁 Replace with your actual URL
                    
                    // Function to safely fetch remote JSON
                    function getRemoteJson($url) {
                        $json = @file_get_contents($url);
                        return $json ? json_decode($json, true) : null;
                    }
                    
                    // Step 1: Load current version
                    $currentData = file_exists($currentVersionPath) ? json_decode(file_get_contents($currentVersionPath), true) : null;
                    $currentVersion = $currentData['version'] ?? '0.0.0';
                    
                    // Step 2: Load latest version from remote
                    $remoteData = getRemoteJson($remoteVersionUrl);
                    $latestVersion = $remoteData['latest_version'] ?? null;
                    
                    if ($latestVersion && version_compare($latestVersion, $currentVersion, '>')) {
                        // Step 3: Find latest release info
                        $release = null;
                        foreach ($remoteData['releases'] as $r) {
                            if ($r['version'] === $latestVersion) {
                                $release = $r;
                                break;
                            }
                        }
                    
                        // Step 4: Output new version HTML
                        if ($release) {
                            echo '<div class="version-card new-version">';
                            echo '    <div class="version-label">New Version</div>';
                            echo '    <div class="version-number">v' . htmlspecialchars($release['version']) . '</div>';
                            echo '    <ul class="version-features">';
                            foreach ($release['changelog'][0]['details'] as $feature) {
                                echo '        <li>' . htmlspecialchars($feature) . '</li>';
                            }
                            echo '    </ul>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="version-card new-version">';
                        echo '    <div class="version-label">New Version</div>';
                        echo '    <div class="version-number">Up-to-date</div>';
                        echo '    <p>No new version available.</p>';
                        echo '</div>';
                    }
                ?>
            </div>
            
            <span class="update-response"></span>
            
            <button class="btn btn-update">
                <i class="fa fa-download me-2"></i> Prepare for Update
            </button>
            
            <div class="downtime-notice mt-4">
                <i class="fa fa-exclamation-triangle me-2"></i>
                The website will be unavailable for approximately 30 minutes during the update process
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    
    <script>
        document.querySelector('.btn-update').addEventListener('click', function() {
            document.querySelector(".btn-update").innerHTML = '<i class="fa fa-circle-o-notch fa-spin" style="font-size:18px"></i>';
            
            $.ajax
            ({
                type: "POST",
                url: "https://<?php echo $_SERVER['HTTP_HOST']?>/pp-auto-update",
                data: { "auto-update": "start" },
                success: function (data) {
                    document.querySelector(".btn-update").innerHTML = '<i class="fa fa-download me-2"></i> Prepare for Update';
                    
                    var dedata = JSON.parse(data);
                    
                    if(dedata.status == "false"){
                        document.querySelector(".update-response").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                    }else{
                        document.querySelector(".update-response").innerHTML = '<div class="alert alert-success" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                    }
                }
            });
        });
    </script>
</body>
</html>