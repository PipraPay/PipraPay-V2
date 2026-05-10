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
              <h1 class="page-header-title">Api Settings</h1>
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
                  <h2 class="card-title h4">API Access Credentials</h2>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <!-- Form -->
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-12">
                            <label for="sitename" class="col-sm-12 col-form-label form-label">API Key</label>
                            <div class="input-group">
                                 <input type="text" class="form-control" name="api-key" id="api-key" placeholder="Enter api key" aria-label="Enter api key" value="<?php if($global_setting_response['response'][0]['api_key'] !== "--"){echo $global_setting_response['response'][0]['api_key'];}?>" readonly>
                                 <span class="input-group-text" style="cursor:pointer" onclick="copyToClipboard('api-key')"><i class="bi bi-clipboard"></i></span>
                                 <span class="input-group-text btn-arrow-repeat" style="cursor:pointer" onclick="generateapi()"><i class="bi bi-arrow-repeat"></i></span>
                            </div>
                            <div class="text-secondary mt-2">Your secret API key for authentication</div>
                      </div>
                    </div>
                    <!-- End Form -->
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
        

              <!-- Card -->
              <div class="card">
                <div class="card-header">
                  <h2 class="card-title h4">API Endpoints</h2>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <!-- Form -->
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-6">
                            <label for="sitename" class="col-sm-12 col-form-label form-label">Base URL</label>
                            <div class="input-group">
                                 <input type="text" class="form-control" name="base-url" id="base-url" placeholder="Enter base url" aria-label="Enter base url" value="<?php echo PP_URL; ?>/api" readonly>
                                 <span class="input-group-text" style="cursor:pointer" onclick="copyToClipboard('base-url')"><i class="bi bi-clipboard"></i></span>
                            </div>
                            <div class="text-secondary mt-2">Root API endpoint</div>
                      </div>
                      
                      <div class="col-sm-6">
                            <label for="sitename" class="col-sm-12 col-form-label form-label">API Endpoint</label>
                            <div class="input-group">
                                 <input type="text" class="form-control" name="checkout-integration" id="checkout-integration" placeholder="Enter checkout integration" aria-label="Enter checkout integration" value="<?php echo PP_URL; ?>/api/create-charge" readonly>
                                 <span class="input-group-text" style="cursor:pointer" onclick="copyToClipboard('checkout-integration')"><i class="bi bi-clipboard"></i></span>
                            </div>
                            <div class="text-secondary mt-2">Standard checkout integration</div>
                      </div>
                      
                      <div class="col-sm-6">
                            <label for="sitename" class="col-sm-12 col-form-label form-label">Verify Payment Endpoint</label>
                            <div class="input-group">
                                 <input type="text" class="form-control" name="verify-payment" id="verify-payment" placeholder="Enter verify payment" aria-label="Enter verify payment" value="<?php echo PP_URL; ?>/api/verify-payments" readonly>
                                 <span class="input-group-text" style="cursor:pointer" onclick="copyToClipboard('verify-payment')"><i class="bi bi-clipboard"></i></span>
                            </div>
                            <div class="text-secondary mt-2">Use this to verify payment status</div>
                      </div>
                    </div>
                    <!-- End Form -->
                </div>
                <!-- End Body -->
              </div>
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
        
            function generateapi(){
              document.querySelector(".btn-arrow-repeat").innerHTML = '<div class="spinner-border text-primary spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_generate_api"},
                    success: function (data) {
                        document.querySelector(".btn-arrow-repeat").innerHTML = '<i class="bi bi-arrow-repeat"></i>';
                        
                        var dedata = JSON.parse(data);
                        
                        document.querySelector("#api-key").value = dedata.api;
                    }
                });
            }
        </script>
<?php
    }
?>