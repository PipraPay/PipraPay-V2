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
        <!-- Page Header -->
        <div class="page-header">
          <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
              <h1 class="page-header-title">Cron Job Settings</h1>
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->
        </div>
        <!-- End Page Header -->
        
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="d-grid gap-3 gap-lg-5">
              <!-- Card -->
              <div class="card">
                <div class="card-header">
                  <h2 class="card-title h4">Cron Job</h2>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <!-- Form -->
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-12">
                            <label for="sitename" class="col-sm-12 col-form-label form-label">Cron Job Command</label>
                            <div class="input-group">
                                 <input type="text" class="form-control" name="cron-job-command" id="cron-job-command" placeholder="Enter cron job command" aria-label="Enter cron job command" value="curl -s <?php echo PP_URL; ?>/?cron >/dev/null 2>&1" readonly>
                                 <span class="input-group-text" style="cursor:pointer" onclick="copyToClipboard('cron-job-command')"><i class="bi bi-clipboard"></i></span>
                            </div>
                            <div class="text-secondary mt-2">Set to run every 10 minutes</div>
                      </div>
                    </div>
                    <!-- End Form -->
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
        
              <!-- End Card -->
            <div id="stickyBlockEndPoint"></div>
          </div>
        </div>
        <!-- End Row -->
        
        <script> 
          function copyToClipboard(inputs) {
            const input = document.getElementById(inputs);
            input.select();
            input.setSelectionRange(0, 99999); 
        
            navigator.clipboard.writeText(input.value)
              .then(() => {

              });
          }
        </script>
<?php
    }
?>