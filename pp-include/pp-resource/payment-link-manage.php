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
        if(isset($_GET['ref'])){
            $ref = escape_string($_GET['ref']);

            $response_payment_link_checker = json_decode(getData($db_prefix.'payment_link','WHERE id= "'.$ref.'"'),true);
            if($response_payment_link_checker['status'] == true){
                
            }else{
?>
                <script>
                    load_content('Payment Links','payment-link','nav-btn-payment-link');
                </script>
<?php
                exit();
            }
        }
?>
        <!-- Page Header -->
        <div class="page-header">
          <div class="row align-items-end">
            <div class="col-sm mb-2 mb-sm-0">
              <h1 class="page-header-title"><?php if(isset($response_payment_link_checker['response'][0]['pl_id'])){ echo "Manage"; }else{ echo "Create"; }?> Payment Link</h1>
            </div>
            <?php 
               if(isset($response_payment_link_checker['response'][0]['pl_id'])){
            ?>
              <div class="col-auto">
                <a class="btn btn-primary btn-copy-topclip-board<?php echo $response_payment_link_checker['response'][0]['pl_id']?>" onclick="copytoclipboard('<?php echo $response_payment_link_checker['response'][0]['pl_id']?>')">
                  <i class="bi-clipboard me-1"></i> Copy
                </a>
              </div>
            <?php 
               }
            ?>
          </div>
          <!-- End Row -->
        </div>
        <!-- End Page Header -->
        <form action="payment-link-manage" method="POST" class="payment-link-upload-form">
            <input type="hidden" name="action" value="pp_payment_links_manage">
            <input type="hidden" name="pl_id" value="<?php if(isset($response_payment_link_checker['response'][0]['pl_id'])){ echo $response_payment_link_checker['response'][0]['pl_id'];}?>">
            
            <div class="row justify-content-center">
              <div class="col-lg-9">
                <div class="d-grid gap-3 gap-lg-5">
                  <!-- Card -->
                  <span class="response-bulk-action"></span>
                  
                  <div class="card">
                    <div class="card-header">
                      <h2 class="card-title h4">Payment Link Details</h2>
                    </div>
                    <!-- Body -->
                    <div class="card-body">
                      <!-- Form -->
                        <!-- Form -->
                        <div class="row">
                          <div class="col-sm-4 mb-3">
                              <div class="col-md-12">
                                <label for="payment-link-product-name" class="form-label">Product name</label>
                                <input type="text" class="form-control" id="payment-link-product-name" name="payment-link-product-name" placeholder="Enter product name" required="" value="<?php if(isset($response_payment_link_checker['response'][0]['pl_name'])){ echo $response_payment_link_checker['response'][0]['pl_name'];}?>">
                              </div>
                          </div>
                          
                          <div class="col-sm-4 mb-3">
                              <div class="col-md-12">
                                <label for="payment-link-quantity" class="form-label">Quantity</label>
                                <input type="text" class="form-control" id="payment-link-quantity" name="payment-link-quantity" placeholder="Enter quantity" required="" value="<?php if(isset($response_payment_link_checker['response'][0]['pl_quantity'])){ echo $response_payment_link_checker['response'][0]['pl_quantity'];}?>">
                              </div>
                          </div>
                          
                          <div class="col-sm-12 mb-3">
                              <div class="col-md-12">
                                <label for="payment-link-product-description" class="form-label">Product description</label>
                                <textarea class="form-control" id="payment-link-product-description" name="payment-link-product-description" rows="4"><?php if(isset($response_payment_link_checker['response'][0]['pl_description'])){ echo str_replace("\\", "", $response_payment_link_checker['response'][0]['pl_description']);}?></textarea>
                              </div>
                          </div>
    
                          <div class="col-sm-4 mb-3">
                            <label class="form-label fw-medium text-dark">Currency</label>
    
                            <div class="input-group">
                                <select class="js-select form-select" id="payment-link-currency" name="payment-link-currency">
                                    <option value="-" data-option-template='<span class="d-flex align-items-center"><span class="text-truncate">Select Currency</span></span>' data-symbol="-" data-rate="0">Select Currency</option>
                                    
                                    <?php
                                        $load = json_decode(getData($db_prefix.'currency', ''), true);
                                        foreach($load['response'] as $in){
                                    ?>
                                            <option value="<?php echo $in['currency_code']?>" data-option-template='<span class="d-flex align-items-center"><span class="text-truncate"><?php echo $in['currency_name']?> - <?php echo $in['currency_code']?></span></span>' data-symbol="<?php echo $in['currency_code']?>" data-rate="<?php echo $in['currency_rate']?>" <?php if(isset($response_payment_link_checker['response'][0]['pl_currency'])){if($response_payment_link_checker['response'][0]['pl_currency'] == $in['currency_code']){echo "selected";}}?>><?php echo $in['currency_name']?> <?php echo $in['currency_code']?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                          </div>
                          
                          <div class="col-sm-4 mb-3">
                            <label class="form-label fw-medium text-dark">Amount</label>
    
                            <div class="input-group">
                                  <span class="input-group-text" id="payment-link-amount-currency"><?php if(isset($response_payment_link_checker['response'][0]['pl_currency'])){ echo $response_payment_link_checker['response'][0]['pl_currency'];}?></span>
                                  <input type="text" class="form-control" id="payment-link-amount" name="payment-link-amount" placeholder="Enter amount" aria-label="Enter amount" value="<?php if(isset($response_payment_link_checker['response'][0]['pl_amount'])){ echo $response_payment_link_checker['response'][0]['pl_amount'];}?>">
                            </div>
                          </div>
                          
                          <div class="col-sm-4 mb-3">
                            <label class="form-label fw-medium text-dark">Expire Date</label>
    
                            <div class="input-group">
                                  <input type="text" class="js-flatpickr form-control flatpickr-custom" name="payment-link-expiry" placeholder="Select dates" value="<?php if(isset($response_payment_link_checker['response'][0]['pl_expiry_date'])){ echo $response_payment_link_checker['response'][0]['pl_expiry_date'];}else{ echo "dd/mm/yyyy";}?>" data-hs-flatpickr-options='{"dateFormat": "d/m/Y"}'>
                            </div>
                          </div>
    
                          <div class="col-sm-4 mb-3">
                            <label class="form-label fw-medium text-dark">Status</label>
    
                            <div class="input-group">
                                <select class="form-select js-select" id="payment-link-status" name="payment-link-status">
                                  <option value="active" <?php if(isset($response_payment_link_checker['response'][0]['pl_status'])){if($response_payment_link_checker['response'][0]['pl_status'] == "active"){echo "selected";}}?>>Active</option>
                                  <option value="inactive" <?php if(isset($response_payment_link_checker['response'][0]['pl_status'])){if($response_payment_link_checker['response'][0]['pl_status'] == "inactive"){echo "selected";}}?>>Inactive</option>
                                </select>
                            </div>
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
                        <h2 class="card-title h4">User input fields</h2>
                    </div>
                    <!-- Body -->
                    <div class="card-body">
                        <div class="tab-content">
                          <div class="tab-pane fade show active user-input-fields" role="tabpanel">
                                <?php
                                    if(isset($response_payment_link_checker['response'][0]['pl_id'])){
                                        $load = json_decode(getData($db_prefix.'payment_link_input', 'WHERE pl_id="'.$response_payment_link_checker['response'][0]['pl_id'].'"'), true);
                                        foreach($load['response'] as $in){
                                            $randinput = rand();
                                ?>
                                            <div class="card" id="row_<?php echo $randinput?>"style="margin-top:20px;">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h2 class="card-title h4 m-0">Field</h2>
                                                    
                                                    <i class="bi bi-trash3" style="cursor:pointer; font-size:19px; color: #dc2626;" onclick="removeRow('<?php echo $randinput?>')"></i>
                                                </div>
                            
                                                <div class="card-body">
                                                    <div class="row">
                                                      <div class="col-sm-6 mb-3">
                                                        <label class="form-label fw-medium text-dark">Form Type</label>
                                                        
                                                        <select class="form-select js-select" id="payment-link-form-type" name="payment-link-input-field-type[]">
                                                          <option value="text" <?php if($in['pl_form_type'] == "text"){echo "selected";}?>>Text</option>
                                                          <option value="textarea" <?php if($in['pl_form_type'] == "textarea"){echo "selected";}?>>Textarea</option>
                                                          <option value="file" <?php if($in['pl_form_type'] == "file"){echo "selected";}?>>File</option>
                                                        </select>
                                                      </div>
                                                      
                                                      <div class="col-sm-6 mb-3">
                                                        <label class="form-label fw-medium text-dark">Field Name</label>
                                                        <input type="text" class="form-control" id="payment-link-field-name" name="payment-link-input-field-name[]" placeholder="Enter field name" aria-label="Enter field name" value="<?php echo  $in['pl_field_name'];?>">
                                                      </div>
                                                      
                                                      <div class="col-sm-6 mb-3">
                                                            <select class="form-select js-select" id="payment-link-form-field-type" name="payment-link-input-field-is-require[]">
                                                              <option value="required" <?php if($in['pl_is_require'] == "required"){echo "selected";}?>>Required</option>
                                                              <option value="optional" <?php if($in['pl_is_require'] == "optional"){echo "selected";}?>>Optional</option>
                                                            </select>
                                                      </div>
                                                    </div>
                                                </div>
                                            </div>
                                <?php
                                        }
                                    }
                                ?>     
                          </div>
                          
                          <center>
                              <button class="btn btn-primary mt-5" onclick="addinputrow()">Add new field</button>
                          </center>
                        </div>
                    </div>
                  </div>
                  
                  <span class="response"></span>
                  
                  <button class="btn btn-primary mt-1 btn-primary-add" style=" max-width: 130px; ">Save Changes</button>
                  <!-- End Card -->
                  
                <div id="stickyBlockEndPoint"></div>
              </div>
            </div>
            <!-- End Row -->
        </form>
        <script> 
            <?php 
               if(isset($response_payment_link_checker['response'][0]['pl_id'])){
            ?>
                    function copytoclipboard(text) {
                        if (!navigator.clipboard) {
                            // Fallback for older browsers
                            const textarea = document.createElement("textarea");
                            textarea.value = '<?php echo PP_URL; ?>/payment-link/'+text;
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand("copy");
                            document.body.removeChild(textarea);
                        } else {
                            navigator.clipboard.writeText('<?php echo PP_URL; ?>/payment-link/'+text).then(function() {
                                document.querySelector(".btn-copy-topclip-board"+text).innerHTML = '<i class="bi-clipboard-check me-1"></i> Copied';
                            });
                        }
                    }
            <?php 
               }
            ?>
        
        
        
            function addinputrow() {
                const row_id = 'id-' + Math.random().toString(36).substr(2, 9);

                const container = document.querySelector(".user-input-fields");
            
                const div = document.createElement("div");
                div.className = "card"; 
                div.id = `row_${row_id}`;
                div.style.marginTop = `20px`;
                
                div.innerHTML = `
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title h4 m-0">Field</h2>
                        
                        <i class="bi bi-trash3" style="cursor:pointer; font-size:19px; color: #dc2626;" onclick="removeRow('${row_id}')"></i>
                    </div>

                    <div class="card-body">
                        <div class="row">
                          <div class="col-sm-6 mb-3">
                            <label class="form-label fw-medium text-dark">Form Type</label>
                            
                            <select class="form-select js-select" id="payment-link-form-type" name="payment-link-input-field-type[]">
                              <option value="text">Text</option>
                              <option value="textarea">Textarea</option>
                              <option value="file">File</option>
                            </select>
                          </div>
                          
                          <div class="col-sm-6 mb-3">
                            <label class="form-label fw-medium text-dark">Field Name</label>
                            <input type="text" class="form-control" id="payment-link-field-name" name="payment-link-input-field-name[]" placeholder="Enter field name" aria-label="Enter field name">
                          </div>
                          
                          <div class="col-sm-6 mb-3">
                                <select class="form-select js-select" id="payment-link-form-field-type" name="payment-link-input-field-is-require[]">
                                  <option value="required">Required</option>
                                  <option value="optional">Optional</option>
                                </select>
                          </div>
                        </div>
                    </div>
                `;

                container.appendChild(div);
            }
            
            function removeRow(row_id) {
                document.getElementById(`row_${row_id}`).remove();
            }
            
        
        
        
           function initialize(){
               HSCore.components.HSTomSelect.init('.js-select');
               HSCore.components.HSFlatpickr.init('.js-flatpickr')
           }
           initialize();
                   
           document.getElementById('payment-link-currency').addEventListener('change', function () {
                const selectedValue = this.value;
                const selectedOption = document.querySelector(`#payment-link-currency option[value="${selectedValue}"]`);

                document.querySelector("#payment-link-amount-currency").innerHTML = selectedOption.dataset.symbol;
           });
           
            $('.payment-link-upload-form').submit(function(){
            	return false;
            });
             
            $('.btn-primary-add').click(function(){
                $(".btn-primary-add").html('<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>');
                $(".btn-primary-add").prop("disabled", true);
                
            	$.post(		
            		$('.payment-link-upload-form').attr('action'),
            		$('.payment-link-upload-form :input').serializeArray(),
            		function(data){
                        $(".btn-primary-add").prop("disabled", false);
                        $(".btn-primary-add").html('Save Changes');
    
                        var response = JSON.parse(data);
    
                        if(response.status == "false"){
                            $(".response").html('<div class="alert alert-danger" style=" margin-top: 20px; margin-bottom: -1px; " role="alert">'+response.message+'</div>');
                        }else{
                            $(".response").html('<div class="alert alert-primary" style=" margin-top: 20px; margin-bottom: -1px; " role="alert">'+response.message+'</div>');
                        }
            		}
            	);
            });
        </script>
<?php
    }
?>