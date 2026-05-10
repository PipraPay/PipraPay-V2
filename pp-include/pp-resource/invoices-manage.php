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

            $response_invoice_checker = json_decode(getData($db_prefix.'invoice','WHERE id= "'.$ref.'"'),true);
            if($response_invoice_checker['status'] == true){
                
            }else{
?>
                <script>
                    load_content('Invoices','invoices','nav-btn-invoices');
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
              <h1 class="page-header-title"><?php if(isset($response_invoice_checker['response'][0]['i_id'])){ echo "Update"; }else{ echo "Create"; }?> Invoice</h1>
            </div>
            
            <?php 
               if(isset($response_invoice_checker['response'][0]['i_id'])){
            ?>
              <div class="col-auto">
                <a class="btn btn-primary btn-copy-topclip-board<?php echo $response_invoice_checker['response'][0]['i_id']?>" onclick="copytoclipboard('<?php echo $response_invoice_checker['response'][0]['i_id']?>')">
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
            <input type="hidden" name="action" value="pp_invoice_manage">
            <input type="hidden" name="i_id" value="<?php if(isset($response_invoice_checker['response'][0]['i_id'])){ echo $response_invoice_checker['response'][0]['i_id'];}?>">
            
            <div class="container-fluid">
                <div class="row g-4">
                  <!-- Left Column (8/12 width on lg screens) -->
                  <div class="col-12 col-lg-8">
                    <!-- Customer Section -->
                    <div class="card mb-4">
                      <div class="card-body">
                        <div class="row g-3">
                          <!-- Customer Select -->
                          <div class="col-12">
                            <div class="mb-3">
                              <label for="data.customer_id" class="form-label">
                                Customer<sup class="text-danger">*</sup>
                              </label>
                              
                              <span class="d-flex">
                                  <select class="js-select form-select" name="invoice_customer">
                                      <option value="--">Select Customer</option>
                                      
                                    <?php
                                        $load = json_decode(getData($db_prefix.'customer', ''), true);
                                        foreach($load['response'] as $in){
                                    ?>
                                            <option value="<?php echo $in['c_id']?>" <?php if(isset($response_invoice_checker['response'][0]['c_id'])){if($response_invoice_checker['response'][0]['c_id'] == $in['c_id']){echo "selected";}}?>><?php echo $in['c_name']?> <?php echo $in['c_email_mobile']?></option>
                                    <?php
                                        }
                                    ?>
                                  </select>
                                  <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" title="Create" data-bs-toggle="modal" data-bs-target="#customer-add">
                                      <i class="bi bi-plus"></i>
                                    </button>
                                  </div>
                              </span>
                            </div>
                          </div>
                          
                          <!-- Currency -->
                          <div class="col-12 col-md-6">
                            <div class="mb-3">
                              <label for="data.currency" class="form-label">
                                Currency<sup class="text-danger">*</sup>
                              </label>
                                <select class="js-select form-select" id="invoice_currency" name="invoice_currency" onchange="invoice_currencys()">
                                    <option value="--" data-option-template='<span class="d-flex align-items-center"><span class="text-truncate">Select Currency</span></span>' data-symbol="-" data-rate="0">Select Currency</option>
                                    
                                    <?php
                                        $load = json_decode(getData($db_prefix.'currency', ''), true);
                                        foreach($load['response'] as $in){
                                    ?>
                                            <option value="<?php echo $in['currency_code']?>" data-option-template='<span class="d-flex align-items-center"><span class="text-truncate"><?php echo $in['currency_name']?> - <?php echo $in['currency_code']?></span></span>' data-symbol="<?php echo $in['currency_code']?>" data-rate="<?php echo $in['currency_rate']?>" <?php if(isset($response_invoice_checker['response'][0]['i_currency'])){if($response_invoice_checker['response'][0]['i_currency'] == $in['currency_code']){echo "selected";}}?>><?php echo $in['currency_name']?> <?php echo $in['currency_code']?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                          </div>
                          
                          <!-- Due Date -->
                          <div class="col-12 col-md-6">
                            <div class="mb-3">
                              <label for="data.due_date" class="form-label">Due Date</label>
                              <input type="text" class="js-flatpickr form-control flatpickr-custom" name="invoice_due" placeholder="Select dates" value="<?php if(isset($response_invoice_checker['response'][0]['i_due_date'])){ echo $response_invoice_checker['response'][0]['i_due_date'];}else{ echo "dd/mm/yyyy";}?>" data-hs-flatpickr-options='{"dateFormat": "d/m/Y"}'>
                            </div>
                          </div>
                          
                          <!-- Status -->
                          <div class="col-12 col-md-6">
                            <div class="mb-3">
                              <label for="data.status" class="form-label">
                                Status<sup class="text-danger">*</sup>
                              </label>
                              <select class="js-select form-select" name="invoice_status">
                                <option value="paid" <?php if(isset($response_invoice_checker['response'][0]['i_status'])){ if($response_invoice_checker['response'][0]['i_status'] == "paid"){echo "selected";}}?>>Paid</option>
                                <option value="unpaid" <?php if(isset($response_invoice_checker['response'][0]['i_status'])){ if($response_invoice_checker['response'][0]['i_status'] == "unpaid"){echo "selected";}}?>>Unpaid</option>
                                <option value="canceled" <?php if(isset($response_invoice_checker['response'][0]['i_status'])){ if($response_invoice_checker['response'][0]['i_status'] == "canceled"){echo "selected";}}?>>Canceled</option>
                                <option value="refunded" <?php if(isset($response_invoice_checker['response'][0]['i_status'])){ if($response_invoice_checker['response'][0]['i_status'] == "refunded"){echo "selected";}}?>>Refunded</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Invoice Items Section -->
                    <div class="card mb-4">
                      <div class="card-body">
                        <div class="mb-3">
                          <h5 class="card-title">Invoice Items</h5>
                          
                          <div class="invoice-items-list">
                                <?php
                                    if(isset($response_invoice_checker['response'][0]['i_id'])){
                                        $load = json_decode(getData($db_prefix.'invoice_items', 'WHERE i_id="'.$response_invoice_checker['response'][0]['i_id'].'"'), true);
                                        foreach($load['response'] as $in){
                                            $rand_id = rand();
                                ?>
                                            <div class="card" id="row_<?php echo $rand_id?>" style="margin-top:20px;">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                  <h6 class="mb-0">Item</h6>
                                                  <div>
                                                    <button class="btn btn-sm btn-outline-danger me-2" type="button" title="Delete" onclick="removeRow('<?php echo $rand_id?>')">
                                                      <i class="bi bi-trash"></i>
                                                    </button>
                                                  </div>
                                                </div>
                                                <div class="card-body">
                                                  <div class="row g-3">
                                                    <!-- Description -->
                                                    <div class="col-12">
                                                      <div class="mb-3">
                                                        <label for="item.description" class="form-label">
                                                          Description<sup class="text-danger">*</sup>
                                                        </label>
                                                        <input type="text" class="form-control" name="invoice-items-description[]" value="<?php echo str_replace("\\", "", $in['description'])?>">
                                                      </div>
                                                    </div>
                                                    
                                                    <!-- Quantity -->
                                                    <div class="col-12 col-md-6">
                                                      <div class="mb-3">
                                                        <label for="item.quantity" class="form-label">
                                                          Quantity<sup class="text-danger">*</sup>
                                                        </label>
                                                        <input type="number" class="form-control" name="invoice-items-quantity[]" value="<?php echo str_replace("\\", "", $in['quantity'])?>">
                                                      </div>
                                                    </div>
                                                    
                                                    <!-- Amount -->
                                                    <div class="col-12 col-md-6">
                                                      <div class="mb-3">
                                                        <label for="item.amount" class="form-label">
                                                          Amount<sup class="text-danger">*</sup>
                                                        </label>
                                                        <div class="input-group">
                                                          <span class="input-group-text invoice-items-currency"></span>
                                                          <input type="number" class="form-control" name="invoice-items-amount[]" value="<?php echo str_replace("\\", "", $in['amount'])?>">
                                                        </div>
                                                      </div>
                                                    </div>
                                                    
                                                    <!-- Discount -->
                                                    <div class="col-12 col-md-6">
                                                      <div class="mb-3">
                                                        <label for="item.discount" class="form-label">Discount</label>
                                                        <div class="input-group">
                                                          <span class="input-group-text invoice-items-currency"></span>
                                                          <input type="number" class="form-control" name="invoice-items-discount[]" value="<?php echo str_replace("\\", "", $in['discount'])?>">
                                                        </div>
                                                      </div>
                                                    </div>
                                                    
                                                    <!-- VAT -->
                                                    <div class="col-12 col-md-6">
                                                      <div class="mb-3">
                                                        <label for="item.vat" class="form-label">VAT</label>
                                                        <div class="input-group">
                                                          <span class="input-group-text">%</span>
                                                          <input type="number" class="form-control" name="invoice-items-vat[]" value="<?php echo str_replace("\\", "", $in['vat'])?>">
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                            </div>
                                <?php
                                        }
                                    }
                                ?>
                          </div>

                          <!-- Add Item Button -->
                          <div class="text-center" style="margin-top:20px;">
                            <button class="btn btn-primary" type="button" onclick="addinputrow()">
                              Add to Invoice Items
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Right Column (4/12 width on lg screens) -->
                  <div class="col-12 col-lg-4">
                    <!-- Notes Section -->
                    <div class="card mb-4">
                      <div class="card-body">
                        <div class="mb-3">
                          <label for="data.notes" class="form-label">Notes</label>
                          <textarea class="form-control" name="invoice_notes" rows="5"><?php if(isset($response_invoice_checker['response'][0]['i_note'])){ echo $response_invoice_checker['response'][0]['i_note'];}?></textarea>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Totals Section -->
                    <div class="card mb-4">
                      <div class="card-header">
                        <h5 class="mb-0">Totals</h5>
                      </div>
                      <div class="card-body">
                        <!-- Shipping -->
                        <div class="mb-3">
                          <label for="data.shipping_amount" class="form-label">Shipping</label>
                          <div class="input-group">
                            <span class="input-group-text invoice-items-currency"></span>
                            <input type="number" class="form-control" name="invoice_shipping" value="<?php if(isset($response_invoice_checker['response'][0]['i_amount_shipping'])){ echo $response_invoice_checker['response'][0]['i_amount_shipping'];}else{ echo 0;}?>">
                          </div>
                        </div>
                        
                        <!-- VAT -->
                        <div class="mb-3">
                          <label for="data.vat" class="form-label">VAT</label>
                          <div class="input-group">
                            <span class="input-group-text invoice-items-currency"></span>
                            <input type="number" class="form-control" name="invoice_vat" readonly>
                          </div>
                        </div>
                        
                        <!-- Discount -->
                        <div class="mb-3">
                          <label for="data.discount" class="form-label">Discount</label>
                          <div class="input-group">
                            <span class="input-group-text invoice-items-currency"></span>
                            <input type="number" class="form-control" name="invoice_discount" readonly>
                          </div>
                        </div>
                        
                        <!-- Total Amount -->
                        <div class="mb-3">
                          <label for="data.subtotal" class="form-label">Total Amount</label>
                          <div class="input-group">
                            <span class="input-group-text invoice-items-currency"></span>
                            <input type="text" class="form-control" name="invoice_amount" readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <span class="response-invoice"></span>
                
                <!-- Form Actions -->
                <div class="d-flex justify-content-between mt-4">
                  <div>
                    <button type="submit" class="btn btn-primary me-2 btn-primary-add">
                      <?php if(isset($response_invoice_checker['response'][0]['i_id'])){ echo "Update"; }else{ echo "Create"; }?>
                    </button>
                  </div>
                </div>
            </div>
        </form>
        
        
        <div class="modal fade" id="customer-add" tabindex="-1" role="dialog" aria-labelledby="customer-add" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="customer-add">Create customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="input-group mb-3">
                  <input type="text" class="form-control" id="c-full-name" placeholder="Enter full name" aria-label="Enter full name">
                </div>

                <div class="input-group mb-3">
                  <input type="text" class="form-control" id="c-email-mobile" placeholder="Enter email or mobile" aria-label="Enter email or mobile">
                </div>
                
                 <span class="response-model"></span>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-model-currency-rate-saved" onclick="addnew()">Add New</button>
              </div>
            </div>
          </div>
        </div>
        
        
        <script> 
            <?php 
               if(isset($response_invoice_checker['response'][0]['i_id'])){
            ?>
                    function copytoclipboard(text) {
                        if (!navigator.clipboard) {
                            // Fallback for older browsers
                            const textarea = document.createElement("textarea");
                            textarea.value = '<?php echo PP_URL; ?>/invoice/'+text;
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand("copy");
                            document.body.removeChild(textarea);
                        } else {
                            navigator.clipboard.writeText('<?php echo PP_URL; ?>/invoice/'+text).then(function() {
                                document.querySelector(".btn-copy-topclip-board"+text).innerHTML = '<i class="bi-clipboard-check me-1"></i> Copied';
                            });
                        }
                    }
            <?php 
               }
            ?>
        
            function invoice_currencys() {
                // Get the select element
                const selectElement = document.getElementById('invoice_currency');
                
                // Get the selected value (currency_code)
                const selectedCurrencyCode = selectElement.value;
                
                // Get the selected option element
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                
                // You can also access other data attributes if needed
                const currencySymbol = selectedOption.getAttribute('data-symbol');
                const currencyRate = selectedOption.getAttribute('data-rate');
                
                document.querySelectorAll('.invoice-items-currency').forEach(element => {
                  element.textContent = selectedCurrencyCode;
                });
            }
            
            invoice_currencys();
        
            function addnew(){
              var c_name = document.querySelector("#c-full-name").value;
              var c_email_mobile = document.querySelector("#c-email-mobile").value;
              
              document.querySelector(".btn-model-currency-rate-saved").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_addcustomer", "c_name": c_name, "c_email_mobile": c_email_mobile},
                    success: function (data) {
                        document.querySelector(".btn-model-currency-rate-saved").innerHTML = 'Add New';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-model").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            $('#customer-add').modal('hide');
    
                            load_content('Invoices','invoices-manage','nav-btn-invoices');
                            document.querySelector(".response-model").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
            }
                    
            // Function to calculate invoice totals
            function calculateInvoiceTotals() {
              // Get all item rows
              const itemRows = document.querySelectorAll('.invoice-items-list .card');
              
              let subtotal = 0;
              let totalVat = 0;
              let totalDiscount = 0;
            
              // Loop through each item row
              itemRows.forEach(row => {
                const quantity = parseFloat(row.querySelector('[name="invoice-items-quantity[]"]').value) || 0;
                const amount = parseFloat(row.querySelector('[name="invoice-items-amount[]"]').value) || 0;
                const discount = parseFloat(row.querySelector('[name="invoice-items-discount[]"]').value) || 0;
                const vatPercentage = parseFloat(row.querySelector('[name="invoice-items-vat[]"]').value) || 0;
                
                // Calculate item subtotal
                const itemSubtotal = quantity * amount;
                
                // Calculate item discount amount
                const itemDiscount = Math.min(discount, itemSubtotal); // Ensure discount doesn't exceed subtotal
                totalDiscount += itemDiscount;
                
                // Calculate item VAT amount (on the discounted amount)
                const itemAmountAfterDiscount = itemSubtotal - itemDiscount;
                const itemVat = itemAmountAfterDiscount * (vatPercentage / 100);
                totalVat += itemVat;
                
                // Add to subtotal
                subtotal += itemSubtotal;
              });
            
              // Get shipping cost
              const shipping = parseFloat(document.querySelector('[name="invoice_shipping"]').value) || 0;
              
              // Calculate final total
              const totalAmount = subtotal - totalDiscount + totalVat + shipping;
              
              // Update the totals section
              document.querySelector('[name="invoice_vat"]').value = totalVat.toFixed(2);
              document.querySelector('[name="invoice_discount"]').value = totalDiscount.toFixed(2);
              document.querySelector('[name="invoice_amount"]').value = totalAmount.toFixed(2);
            }
            
            // Add event listeners to all input fields that affect calculations
            function setupCalculationListeners() {
              // Listen to changes in item fields
              document.querySelectorAll('.invoice-items-list [name^="invoice-items-"]').forEach(input => {
                input.addEventListener('input', calculateInvoiceTotals);
              });
              
              // Listen to changes in shipping
              document.querySelector('[name="invoice_shipping"]').addEventListener('input', calculateInvoiceTotals);
              
              // Calculate initially
              calculateInvoiceTotals();
            }
            
            // Call this function after adding new rows or on page load
            setupCalculationListeners();
            
            // Modify your addinputrow function to include the event listener setup
            function addinputrow() {
                const row_id = 'id-' + Math.random().toString(36).substr(2, 9);
            
                const container = document.querySelector(".invoice-items-list");
              
                const div = document.createElement("div");
                div.className = "card"; 
                div.id = `row_${row_id}`;
                div.style.marginTop = "20px";
                
                div.innerHTML = `
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h6 class="mb-0">Item</h6>
                      <div>
                        <button class="btn btn-sm btn-outline-danger me-2" type="button" title="Delete" onclick="removeRow('${row_id}')">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <!-- Description -->
                        <div class="col-12">
                          <div class="mb-3">
                            <label for="item.description" class="form-label">
                              Description<sup class="text-danger">*</sup>
                            </label>
                            <input type="text" class="form-control" name="invoice-items-description[]">
                          </div>
                        </div>
                        
                        <!-- Quantity -->
                        <div class="col-12 col-md-6">
                          <div class="mb-3">
                            <label for="item.quantity" class="form-label">
                              Quantity<sup class="text-danger">*</sup>
                            </label>
                            <input type="number" class="form-control" name="invoice-items-quantity[]">
                          </div>
                        </div>
                        
                        <!-- Amount -->
                        <div class="col-12 col-md-6">
                          <div class="mb-3">
                            <label for="item.amount" class="form-label">
                              Amount<sup class="text-danger">*</sup>
                            </label>
                            <div class="input-group">
                              <span class="input-group-text invoice-items-currency"></span>
                              <input type="number" class="form-control" name="invoice-items-amount[]">
                            </div>
                          </div>
                        </div>
                        
                        <!-- Discount -->
                        <div class="col-12 col-md-6">
                          <div class="mb-3">
                            <label for="item.discount" class="form-label">Discount</label>
                            <div class="input-group">
                              <span class="input-group-text invoice-items-currency"></span>
                              <input type="number" class="form-control" name="invoice-items-discount[]">
                            </div>
                          </div>
                        </div>
                        
                        <!-- VAT -->
                        <div class="col-12 col-md-6">
                          <div class="mb-3">
                            <label for="item.vat" class="form-label">VAT</label>
                            <div class="input-group">
                              <span class="input-group-text">%</span>
                              <input type="number" class="form-control" name="invoice-items-vat[]">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                `;
            
                container.appendChild(div);
                
                // Set up event listeners for the new row
                div.querySelectorAll('[name^="invoice-items-"]').forEach(input => {
                  input.addEventListener('input', calculateInvoiceTotals);
                });
                
                invoice_currencys();
            }
                        
            function removeRow(row_id) {
                document.getElementById(`row_${row_id}`).remove();
                invoice_currencys();
                calculateInvoiceTotals();
            }
            
        
        
        
           function initialize(){
               HSCore.components.HSTomSelect.init('.js-select');
               HSCore.components.HSFlatpickr.init('.js-flatpickr')
           }
           initialize();
                   
           document.getElementById('invoice_currency').addEventListener('change', function () {
                const selectedValue = this.value;
                const selectedOption = document.querySelector(`#invoice_currency option[value="${selectedValue}"]`);

                //document.querySelector("#payment-link-amount-currency").innerHTML = selectedOption.dataset.symbol;
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
            		    console.log(data);
                        $(".btn-primary-add").prop("disabled", false);
                        $(".btn-primary-add").html('<?php if(isset($response_invoice_checker['response'][0]['i_id'])){ echo "Update"; }else{ echo "Create"; }?>');
    
                        var response = JSON.parse(data);
    
                        if(response.status == "false"){
                            $(".response-invoice").html('<div class="alert alert-danger" style=" margin-top: 20px; margin-bottom: -1px; " role="alert">'+response.message+'</div>');
                        }else{
                            $(".response-invoice").html('<div class="alert alert-primary" style=" margin-top: 20px; margin-bottom: -1px; " role="alert">'+response.message+'</div>');
                        }
            		}
            	);
            });
        </script>
<?php
    }
?>