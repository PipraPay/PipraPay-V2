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
                <h1 class="page-header-title">Currency Settings</h1>
              </div>
              <!-- End Col -->
            </div>
            <!-- End Row -->
          </div>
          <!-- End Page Header -->
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
                  <th scope="col" class="table-column-pe-0">SL</th>
                  <th>Currency Code</th>
                  <th>Currency Name</th>
                  <th>Currency Symbol</th>
                  <th>Currency Rate</th>
                  <th>Last Update</th>
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
              
      <script>
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
                    <td valign="top" colspan="7" class="dataTables_empty">
                        <div class="text-center p-4">
                              <div class="spinner-border text-primary" role="status"> <span class="visually-hidden">Loading...</span> </div>
                        </div>
                    </td>
                </tr>
            `);
            
          $.ajax({
            type: "POST",
            url: "/admin/dashboard",
            data: { action: "pp_currency_list", page: page, search: search },
            success: function (data) {
                tbody.empty();
                
              const dedata = JSON.parse(data);
        
              if (dedata.total === 0) {
                tbody.html(`
                    <tr class="odd">
                        <td valign="top" colspan="7" class="dataTables_empty">
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
                const dateText = (!tx.created_at || tx.created_at === "--" ||
                                    isNaN(new Date(tx.created_at).getTime()))
                                    ? "--"
                                    : new Date(tx.created_at).toLocaleDateString();
                  tbody.append(`
                    <tr>
                      <td>${tx.id || "--"}</td>
                      <td>${tx.currency_code || "--"}</td>
                      <td>${tx.currency_name || "--"}</td>
                      <td>${tx.currency_symbol || "--"}</td>
                      <td>${tx.currency_rate || "--"}</td>
                      <td>${dateText}</td>
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
                                    <h5 class="modal-title" id="model${tx.id || "--"}currency-setting">Currency Settings</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="input-group mb-3">
                                      <input type="text" class="form-control" placeholder="Currency Name" aria-label="Currency Name" value="${tx.currency_name || "--"}" readonly>
                                    </div>
                                    
                                    <div class="input-group mb-3">
                                      <span class="input-group-text" id="basic-addon1">${tx.currency_symbol || "--"}</span>
                                      <input type="text" class="form-control currency-rate${tx.id || "--"}" placeholder="Currency Rate" aria-label="Currency Rate" value="${tx.currency_rate || "0"}">
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
        
        function savechanges(modelid){
          var currency_rate = document.querySelector(".currency-rate"+modelid).value;
          
          document.querySelector(".btn-model-currency-rate-saved"+modelid).innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
          
            $.ajax
            ({
                type: "POST",
                url: "<?php echo PP_URL; ?>/admin/dashboard",
                data: { "action": "pp_currencysetting", "currency_id": modelid, "currency_rate": currency_rate},
                success: function (data) {
                    document.querySelector(".btn-model-currency-rate-saved"+modelid).innerHTML = 'Save changes';
                    
                    var dedata = JSON.parse(data);
                    
                    if(dedata.status == "false"){
                        document.querySelector(".response-model"+modelid).innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                    }else{
                        document.querySelector(".response-model"+modelid).innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                    }
                }
            });
        }
      </script>
<?php
    }
?>