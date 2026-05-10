<?php
    if (file_exists(__DIR__."/../../pp-config.php")) {
        if (file_exists(__DIR__.'/../../maintenance.lock')) {
            if (file_exists(__DIR__.'/../../pp-include/pp-maintenance.php')) {

            }else{
                die('System is under maintenance. Please try again later.');
            }
            exit();
        }else{
            if (file_exists(__DIR__.'/../../pp-include/pp-controller.php')) {
                if (file_exists(__DIR__.'/../../pp-include/pp-view.php')) {
    
                }else{
                    echo 'System is under maintenance. Please try again later.';
                    exit();
                }
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
            
            if (file_exists(__DIR__.'/../../pp-include/pp-model.php')) {
                include(__DIR__."/../../pp-include/pp-model.php");
            }else{
                echo 'System is under maintenance. Please try again later.';
                exit();
            }
        }
    }else{
        echo 'System is under maintenance. Please try again later.';
        exit();
    }
    
    if (!defined('pp_allowed_access')) {
        die('Direct access not allowed');
    }
    
    if(isset($global_user_login) && $global_user_login == true){
?>
    <style>
        .plugin-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .plugin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        .plugin-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            font-size: 1.5rem;
        }
        .plugin-icon img{
            height: 40px;
        }
        .status-badge {
            font-size: 0.65rem;
            letter-spacing: 0.5px;
            padding: 3px 6px;
        }
        .nav-pills .nav-link.active {

        }
        .nav-pills .nav-link {
            color: #495057;
            font-weight: 500;
        }
        .tab-content {
            background: #fff;
            border-radius: 0 0.5rem 0.5rem 0.5rem;
        }
        .search-box {
            position: relative;
        }
        .search-box .form-control {
            padding-left: 2.5rem;
            border-radius: 2rem;
        }
        .search-box .bi {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .plugin-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>

        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h4 mb-0">
                                <i class="bi bi-plug me-2 text-primary"></i>Theme Manager
                            </h1>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add-new-plugins">
                                <i class="bi bi-plus-lg me-1"></i> Add New
                            </button>
                        </div>
                        
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-gateway-tab" data-bs-toggle="pill" data-bs-target="#pills-gateway" type="button" role="tab">
                                    <i class="bi bi-check-circle me-1"></i> Installed (<span class="gateway-count">0</span>)
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <span class="response-action"></span>
                    
                    <div class="card-body pt-0">
                        <div class="tab-content" id="pills-tabContent">
                            <!-- Installed Plugins Tab -->
                            <div class="tab-pane fade show active" id="pills-gateway" role="tabpanel">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="search-box">
                                            <i class="bi bi-search"></i>
                                            <input type="text" class="form-control" id="gatewaysSearch" onkeyup="filterGateways(currentFilter)"placeholder="Search installed gateway themes...">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="bi bi-funnel me-1"></i> Filter
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#" onclick="filterGateways('all')">All Themes</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#" onclick="filterGateways('active')">Active Only</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="filterGateways('inactive')">Inactive Only</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-4">
                                    <?php
                                        $baseDir = __DIR__.'/../../pp-content/themes/';
                                        
                                        $mainFolders = array_filter(scandir($baseDir), function($item) use ($baseDir) {
                                            return $item !== '.' && $item !== '..' && is_dir($baseDir . DIRECTORY_SEPARATOR . $item);
                                        });
                                        
                                        $foundThemes = false;
                                        
                                        $installed_count = 0;
                                        
                                        foreach ($mainFolders as $mainFolder) {
                                            $themeBasePath = $baseDir . DIRECTORY_SEPARATOR . $mainFolder;
                                        
                                            $themeFolders = array_filter(scandir($themeBasePath), function($item) use ($themeBasePath) {
                                                return $item !== '.' && $item !== '..' && is_dir($themeBasePath . DIRECTORY_SEPARATOR . $item);
                                            });
                            
                                            foreach ($themeFolders as $themeFolder) {
                                                $mainLocation = $themeBasePath . DIRECTORY_SEPARATOR . $themeFolder. DIRECTORY_SEPARATOR;
                                                
                                                $mainFile = $themeBasePath . DIRECTORY_SEPARATOR . $themeFolder . DIRECTORY_SEPARATOR . $themeFolder . '-class.php';
                                        
                                                if (file_exists($mainFile)) {
                                                    $foundThemes = true;
                                                    $themeInfo = parseThemeHeader($mainFile);

                                                    $plugin_rand = rand();
                                                    
                                                    $installed_count = $installed_count+1;
                                    ?>                                                    
                                                    <!-- Plugin Card 1 -->
                                                    <div class="col-md-6 col-lg-4 plugin-items <?php if($global_setting_response['response'][0]['gateway_theme'] == $themeFolder){echo "active";}else{echo "inactive";}?>plugins">
                                                        <div class="plugin-card card h-100 border">
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-start mb-3">
                                                                    <div class="plugin-icon bg-primary bg-opacity-10 text-primary me-3">
                                                                        <img src="<?php echo PP_URL; ?>/pp-content/themes/<?php echo $mainFolder.'/'.$themeFolder?>/assets/icon.png" alt="Image Description">
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h5 class="mb-1"><?php echo htmlspecialchars($themeInfo['Theme Name'] ?? '')?></h5>
                                                                        <p class="text-primary small mb-2" onclick="location.href='<?php echo htmlspecialchars($themeInfo['Author URI'] ?? '')?>'" style="cursor: pointer;">v<?php echo htmlspecialchars($themeInfo['Version'] ?? '')?> by <?php echo htmlspecialchars($themeInfo['Author'] ?? '')?></p>
                                                                        <?php
                                                                           if($global_setting_response['response'][0]['gateway_theme'] == $themeFolder){
                                                                        ?>
                                                                                <span class="badge bg-success status-badge me-1">Active</span>
                                                                        <?php
                                                                           }else{
                                                                        ?>
                                                                                <span class="badge bg-danger status-badge me-1">Inactive</span>
                                                                        <?php
                                                                           }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <p class="small text-muted mb-3"><?php echo htmlspecialchars($themeInfo['Description'] ?? '')?></p>
                                                                
                                                                <div class="plugin-actions d-flex justify-content-between border-top pt-3">
                                                                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#themeDetailsModal<?php echo $plugin_rand?>">
                                                                        <i class="bi bi-info-circle me-1"></i> Details
                                                                    </button>
                                                                
                                                                    <?php
                                                                        if($global_setting_response['response'][0]['gateway_theme'] !== $themeFolder){
                                                                    ?>
                                                                            <button class="btn btn-outline-success btn-sm btn-activate<?php echo $plugin_rand?>" onclick="pluginhit('btn-activate<?php echo $plugin_rand?>', 'activate', '<?php echo $mainFolder?>', '<?php echo $themeFolder?>', 'themeDetailsModal<?php echo $plugin_rand?>')">
                                                                                <i class="bi bi-power me-1"></i> Activate
                                                                            </button>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Plugin Details Modal -->
                                                    <div class="modal fade" id="themeDetailsModal<?php echo $plugin_rand?>" tabindex="-1" aria-labelledby="themeDetailsModalLabel<?php echo $plugin_rand?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="themeDetailsModalLabel<?php echo $plugin_rand?>">Theme Details</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <h4><?php echo htmlspecialchars($themeInfo['Theme Name'] ?? '')?></h4>
                                                                            
                                                                            <?php
                                                                                if (file_exists($mainLocation.'readme.txt')) {
                                                                                    $content = file_get_contents($mainLocation.'readme.txt');
                                                                                    $sections = parse_readme_sections($content);
                                                                                    
                                                                                    $pluginHeader = parse_readme_header($content);
                                                                                    
                                                                                    $tags = [];
                                                                                    if (!empty($pluginHeader['tags'])) {
                                                                                        $tags = array_map('trim', explode(',', $pluginHeader['tags']));
                                                                                    }
                                                                            ?>
                                                                                    <p class="text-muted"><?php echo htmlspecialchars($themeInfo['Description'] ?? '')?></p>
                                                                                    
                                                                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                                                                        <?php foreach ($tags as $tag): ?>
                                                                                            <span class="badge bg-primary"><?php echo htmlspecialchars($tag); ?></span>
                                                                                        <?php endforeach; ?>
                                                                                    </div>
                                                                                    
                                                                                    <div class="mb-4">
                                                                                        <h5>Description</h5>
                                                                                        <p><?php echo nl2br(htmlspecialchars($sections['description'] ?? 'N/A')); ?></p>
                                                                                        
                                                                                        <h5 class="mt-4">Changelog</h5>
                                                                                        <?php
                                                                                            $changelogData = parse_readme_changelog($sections['changelog'] ?? '');                                                                      
                                                                                        ?>
                                                                                        <div class="accordion" id="changelogAccordion">
                                                                                            <?php
                                                                                            $index = 0;
                                                                                            foreach ($changelogData as $version => $items):
                                                                                                $isFirst = ($index === 0);
                                                                                                $collapseId = 'changelog' . $index;
                                                                                            ?>
                                                                                                <div class="accordion-item">
                                                                                                    <h2 class="accordion-header">
                                                                                                        <button class="accordion-button <?php echo $isFirst ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>">
                                                                                                            Version <?php echo htmlspecialchars($version); ?><?php echo $isFirst ? ' (Current)' : ''; ?>
                                                                                                        </button>
                                                                                                    </h2>
                                                                                                    <div id="<?php echo $collapseId; ?>" class="accordion-collapse collapse <?php echo $isFirst ? 'show' : ''; ?>" data-bs-parent="#changelogAccordion">
                                                                                                        <div class="accordion-body small">
                                                                                                            <ul>
                                                                                                                <?php foreach ($items as $item): ?>
                                                                                                                    <li><?php echo htmlspecialchars($item); ?></li>
                                                                                                                <?php endforeach; ?>
                                                                                                            </ul>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php
                                                                                                $index++;
                                                                                            endforeach;
                                                                                            ?>
                                                                                        </div>
                                                                                    </div>
                                                                            <?php
                                                                                }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <?php
                                                                        if($global_setting_response['response'][0]['gateway_theme'] !== $themeFolder){
                                                                    ?>
                                                                            <button type="button" class="btn btn-primary btn-activate<?php echo $plugin_rand?>-popup" onclick="pluginhit('btn-activate<?php echo $plugin_rand?>-popup', 'activate', '<?php echo $mainFolder?>', '<?php echo $themeFolder?>', 'themeDetailsModal<?php echo $plugin_rand?>')">Activate</button>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                    <?php
                                                }
                                            }
                                        }
                                        
                                        if (!$foundThemes) {
                                    ?>
                                            <div class="col-md-12 col-lg-12">
                                                <div class="alert alert-danger"><i class="bi bi-info-circle-fill me-2"></i> No themes available</div>
                                            </div>
                                    <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
                                                                                
    <script>
        function initialize(){
            document.querySelector(".gateway-count").innerHTML = "<?php echo $installed_count?>";
            
            let currentFilter = "all"; // Track current type globally
        }
        initialize();
        
        function filterGateways(type) {
          currentFilter = type;
          const search = document.getElementById('gatewaysSearch').value.toLowerCase();
        
          document.querySelectorAll('.plugin-items').forEach(el => {
            const name = el.querySelector('h5')?.textContent.toLowerCase() || '';
        
            const matchesType = (type === "all") || el.classList.contains(type + "plugins");
            const matchesSearch = name.includes(search);
        
            if (matchesType && matchesSearch) {
              el.style.display = 'block';
            } else {
              el.style.display = 'none';
            }
          });
        }
                        
        function pluginhit(btn, type, mainfolder, themesfolder, model){
            var btn_value = document.querySelector("."+btn).innerHTML;
            
            $("."+btn).html('<div class="spinner-border spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>');
            $("."+btn).prop("disabled", true);
            
            $.ajax
            ({
                type: "POST",
                url: "<?php echo PP_URL; ?>/admin/appearance-themes",
                data: { "action": "pp_appearance_themes_manager", "type": type, "mainfolder": mainfolder, "themesfolder": themesfolder},
                success: function (data) {
                    console.log(data);
                    $("."+btn).prop("disabled", false);
                    $("."+btn).html(btn_value);
                    
                    $('#'+model).modal('hide');
                    
                    var dedata = JSON.parse(data);
                    
                    if(dedata.status == "false"){
                        document.querySelector(".response-action").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                    }else{
                        $('#'+model).modal('hide');

                        load_content('Appearance','appearance-themes','nav-btn-appearance');
                    }
                }
            });
        }
    </script>                                                                            
<?php
    }
?>