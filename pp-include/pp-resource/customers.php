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
            <div class="row align-items-center">
              <div class="col">
                <h1 class="page-header-title">Customers</h1>
              </div>
              <!-- End Col -->
              <div class="col-auto">
                <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modelcurrency-setting">
                  <i class="bi-plus-circle me-1"></i> New Customers
                </a>
              </div>
            </div>
            <!-- End Row -->
          </div>
          <!-- End Page Header -->
          

          <div class="row justify-content-end mb-3 bulk-manage-tab" style="display: none">
            <div class="col-lg">
              <!-- Datatable Info -->
              <div id="datatableCounterInfo" style="">
                <div class="d-sm-flex justify-content-lg-end align-items-sm-center">
                  <span class="d-block d-sm-inline-block fs-5 me-3 mb-2 mb-sm-0">
                    <span id="bulk-manage-tab-counter">20</span>
                    Selected
                  </span>
                  <a class="btn btn-outline-danger btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-delete" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-delete', 'delete')">
                    <i class="bi-trash"></i> Delete
                  </a>
                  <a class="btn btn-primary btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-active" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-active', 'active')">
                    <i class="bi bi-person-check"></i> Active
                  </a>
                  <a class="btn btn-danger btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-inactive" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-inactive', 'inactive')">
                    <i class="bi bi-person-dash-fill"></i> Inactive
                  </a>
                </div>
              </div>
              
              <span class="response-bulk-action"></span>
            </div>
          </div>
          
          
          
          
      <!-- Card -->
      <div class="card">
        <!-- Header -->
        <div class="card-header card-header-content-md-between">
          <div class="mb-2 mb-md-0">
            <form>
              <!-- Search -->
              <div class="input-group input-group-merge input-group-flush">
                <div class="input-group-prepend input-group-text">
                  <i class="bi-search"></i>
                </div>
                <input id="datatableSearch" type="search" class="form-control" placeholder="Search" aria-label="Search" onkeyup = "loadDataTable()">
              </div>
              <!-- End Search -->
            </form>
          </div>

        </div>
        <!-- End Header -->


        <!-- Table -->
        <div class="table-responsive datatable-custom">
          <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
            <thead class="thead-light">
              <tr>
                  <th scope="col" class="table-column-pe-0"><input type="checkbox" id="select-all" class="form-check-input"></th>
                  <th>Name</th>
                  <th>Email or Mobile</th>
                  <th>Status</th>
                  <th>Actions</th>
              </tr>
            </thead>
            <tbody id="datatable">
                
            </tbody>
          </table>
        </div>
        <!-- End Table -->

        <!-- Footer -->
        <div class="card-footer">
          <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
            <div class="col-sm mb-2 mb-sm-0">
              <div class="d-flex justify-content-center justify-content-sm-start align-items-center">
                <span class="me-2">Showing:</span> <div class="tom-select-custom" id="showing-result"></div> <span class="text-secondary me-2" style="margin-left:8px;">of</span> <span id="total-result"></span>
              </div>
            </div>
            <!-- End Col -->

            <div class="col-sm-auto">
              <div class="d-flex justify-content-center justify-content-sm-end">
                <nav id="datatablePagination" aria-label="Activity pagination">
                    <div class="dataTables_paginate" id="datatable_paginate">
                        <ul id="datatable_pagination" class="pagination datatable-custom-pagination">
                            <li class="paginate_item page-item" id="prev-page">
                                <a class="paginate_button previous page-link" href="javascript:void(0)">
                                    <span aria-hidden="true">Prev</span>
                                </a>
                            </li>
                            <span id="page-numbers" style=" display: flex; "></span>
                            <li class="paginate_item page-item" id="next-page">
                                <a class="paginate_button next page-link" href="javascript:void(0)">
                                    <span aria-hidden="true">Next</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
              </div>
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->
        </div>
        <!-- End Footer -->
      </div>
      <!-- End Card -->
              
        <div class="modal fade" id="modelcurrency-setting" tabindex="-1" role="dialog" aria-labelledby="modelcurrency-setting" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modelcurrency-setting">Create customer</h5>
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
        $("#select-all").click(function(){
            $(".select-box").prop('checked', this.checked);
            
            var selectedCount = $(".select-box:checked").length;
            
            document.querySelector("#bulk-manage-tab-counter").innerHTML = selectedCount;
            
            if(selectedCount === 0){
                document.querySelector(".bulk-manage-tab").style.display = "none";
            }else{
                document.querySelector(".bulk-manage-tab").style.display = "flex";
            }
        });
        
        $(document).on('click', '.select-box', function() {
            var selectedCount = $(".select-box:checked").length;
            
            document.querySelector("#bulk-manage-tab-counter").innerHTML = selectedCount;
            
            if(selectedCount === 0){
                document.querySelector(".bulk-manage-tab").style.display = "none";
            }else{
                document.querySelector(".bulk-manage-tab").style.display = "flex";
            }
        });
                
        function initialize_system_currency_setting() {
            new HSFormSearch('.js-form-search')
    
            HSBsDropdown.init()
    
            HSCore.components.HSTomSelect.init('.js-select')
    
            new HsNavScroller('.js-nav-scroller')
    
            HSCore.components.HSDropzone.init('.js-dropzone')
        }
        initialize_system_currency_setting();
        
        function loadDataTable(page = 1) {
            var search = document.querySelector("#datatableSearch").value;
            
            const tbody = $("#datatable");
            tbody.empty();
    
             tbody.html(`              
                <tr class="odd">
                    <td valign="top" colspan="5" class="dataTables_empty">
                        <div class="text-center p-4">
                              <div class="spinner-border text-primary" role="status"> <span class="visually-hidden">Loading...</span> </div>
                        </div>
                    </td>
                </tr>
            `);
            
          $.ajax({
            type: "POST",
            url: "/admin/dashboard",
            data: { action: "pp_customer_list", page: page, search: search },
            success: function (data) {
                tbody.empty();
                
              const dedata = JSON.parse(data);
        
              if (dedata.total === 0) {
                tbody.html(`
                    <tr class="odd">
                        <td valign="top" colspan="5" class="dataTables_empty">
                            <div class="text-center p-4">
                                  <img class="mb-3" src="<?php echo PP_URL; ?>/pp-external/assets/admin/assets/svg/illustrations/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="default">
                                  <img class="mb-3" src="<?php echo PP_URL; ?>/pp-external/assets/admin/assets/svg/illustrations-light/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="dark">
                                  <p class="mb-0">No data to show</p>
                            </div>
                        </td>
                    </tr>
                `);
                totalPages = 1;
              } else {
                totalPages = dedata.totalPages;
                dedata.data.forEach(tx => {
                  tbody.append(`
                    <tr>
                      <td><input type="checkbox" class="form-check-input select-box" value="${tx.id || "--"}"></td>
                      <td>${tx.c_name || "--"}</td>
                      <td>${tx.c_email_mobile || "--"}</td>
                      <td>${tx.c_status === 'active' ? '<span class="badge bg-primary">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td>
                        <td>
                          <div class="btn-group" role="group">
                            <a class="btn btn-white btn-sm" data-bs-toggle="modal" data-bs-target="#model${tx.id || "--"}currency-setting">
                              <i class="bi-pencil-fill me-1"></i> Edit
                            </a>
                          </div>
                          
                            <div class="modal fade" id="model${tx.id || "--"}currency-setting" tabindex="-1" role="dialog" aria-labelledby="model${tx.id || "--"}currency-setting" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="model${tx.id || "--"}currency-setting">Edit customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="input-group mb-3">
                                      <input type="text" class="form-control" id="c-full-name${tx.id || "--"}" placeholder="Enter full name" aria-label="Enter full name" value="${tx.c_name || "--"}">
                                    </div>
                    
                                    <div class="input-group mb-3">
                                      <input type="text" class="form-control" id="c-email-mobile${tx.id || "--"}" placeholder="Enter email or mobile" aria-label="Enter email or mobile" value="${tx.c_email_mobile || "--"}">
                                    </div>

                                    <div class="input-group mb-3">
                                        <select class="form-select" id="c-status${tx.id || "--"}">
                                          <option value="active" ${tx.c_status === 'active' ? 'selected' : ''}>Active</option>
                                          <option value="inactive" ${tx.c_status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                        </select>
                                    </div>
                                    
                                    <span class="response-model${tx.id || "--"}"></span>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary btn-model-currency-rate-saved${tx.id || "--"}" onclick="savechanges(${tx.id || "--"})">Save changes</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </td>
                    </tr>
                  `);
                });
                totalPages = dedata.totalPages;
                currentPage = dedata.currentPage;
                
                document.querySelector("#total-result").innerHTML = dedata.total;
                document.querySelector("#showing-result").innerHTML = dedata.showing;
                
                renderPagination();
              }
            }
          });
        }
        
        loadDataTable();
        
        function renderPagination() {
            const pageNumbers = document.getElementById("page-numbers");
            pageNumbers.innerHTML = "";
        
            // Show up to 5 pages for simplicity
            const start = Math.max(1, currentPage - 2);
            const end = Math.min(totalPages, currentPage + 2);
        
            for (let i = start; i <= end; i++) {
                const li = document.createElement("li");
                li.className = `paginate_item page-item ${i === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="paginate_button page-link" href="javascript:void(0)">${i}</a>`;
                li.addEventListener("click", () => loadDataTable(i));
                pageNumbers.appendChild(li);
            }
        
            // Enable/disable prev/next
            document.getElementById("prev-page").classList.toggle("disabled", currentPage === 1);
            document.getElementById("next-page").classList.toggle("disabled", currentPage === totalPages);
        }
        
        document.getElementById("prev-page").addEventListener("click", () => {
            if (currentPage > 1) loadDataTable(currentPage - 1);
        });
        
        document.getElementById("next-page").addEventListener("click", () => {
            if (currentPage < totalPages) loadDataTable(currentPage + 1);
        });
        
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
                        $('#modelcurrency-setting').modal('hide');

                        load_content('Customers','customers','nav-btn-customers');
                        document.querySelector(".response-model").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                    }
                }
            });
        }
        
        function savechanges(modelid){
          var c_name = document.querySelector("#c-full-name"+modelid).value;
          var c_email_mobile = document.querySelector("#c-email-mobile"+modelid).value;
          var status = document.querySelector("#c-status"+modelid).value;
          
          document.querySelector(".btn-model-currency-rate-saved"+modelid).innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
          
            $.ajax
            ({
                type: "POST",
                url: "<?php echo PP_URL; ?>/admin/dashboard",
                data: { "action": "pp_editcustomer", "id": modelid, "c_name": c_name, "c_email_mobile": c_email_mobile, "status": status},
                success: function (data) {
                    document.querySelector(".btn-model-currency-rate-saved"+modelid).innerHTML = 'Add New';
                    
                    var dedata = JSON.parse(data);
                    
                    if(dedata.status == "false"){
                        document.querySelector(".response-model"+modelid).innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                    }else{
                        $('#model'+modelid+'currency-setting').modal('hide');

                        load_content('Customers','customers','nav-btn-customers');
                        document.querySelector(".response-model"+modelid).innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                    }
                }
            });
        }

        function bulk_action(bclass, action){
            var btn_value = document.querySelector("."+bclass).innerHTML;
            
            $("."+bclass).html('<div class="spinner-border spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>');
            $("."+bclass).prop("disabled", true);

            var ids = [];
            $(".select-box:checked").each(function(){
                ids.push($(this).val());
            });
        
            var selectedCount = ids.length;
        
            if (selectedCount === 0) {
                $(".response-bulk-action").html('<div class="alert alert-danger" style="margin-top: 20px; margin-bottom: -1px;" role="alert">Please select at least one record to delete.</div>');
            }else{
                var formData = new FormData();
                formData.append('action', 'pp_bulk_action_customer');
                formData.append('action_name', action);
                formData.append('ids', ids);
                
                $.ajax({
                    type: "POST",
                    url: "/admin/customers",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        console.log(data);
                        $("."+bclass).prop("disabled", false);
                        $("."+bclass).html(btn_value);
        
                        var response = JSON.parse(data);
    
                        if (response.status === "false") {
                            $(".response-bulk-action").html('<div class="alert alert-danger" style="margin-top: 20px; margin-bottom: -1px;" role="alert">'+response.message+'</div>');
                        } else {
                            $(".response-bulk-action").html('');
                            load_content('Customers','customers','nav-btn-customers');
                        }
                    }
                });
            }
        }
      </script>
<?php
    }
?>