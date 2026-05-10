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
              <h1 class="page-header-title">Settings</h1>
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->
        </div>
        <!-- End Page Header -->
        
        <div class="row">
          <div class="col-lg-3">
            <!-- Navbar -->
            <div class="navbar-expand-lg navbar-vertical mb-3 mb-lg-5">
              <!-- Navbar Toggle -->
              <!-- Navbar Toggle -->
              <div class="d-grid">
                <button type="button" class="navbar-toggler btn btn-white mb-3" data-bs-toggle="collapse" data-bs-target="#navbarVerticalNavMenu" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu">
                  <span class="d-flex justify-content-between align-items-center">
                    <span class="text-dark">Menu</span>
        
                    <span class="navbar-toggler-default">
                      <i class="bi-list"></i>
                    </span>
        
                    <span class="navbar-toggler-toggled">
                      <i class="bi-x"></i>
                    </span>
                  </span>
                </button>
              </div>
              <!-- End Navbar Toggle -->
              <!-- End Navbar Toggle -->
        
              <!-- Navbar Collapse -->
              <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                <ul id="navbarSettings" class="js-sticky-block js-scrollspy card card-navbar-nav nav nav-tabs nav-lg nav-vertical" data-hs-sticky-block-options='{
                        "parentSelector": "#navbarVerticalNavMenu",
                        "targetSelector": "#header",
                        "breakpoint": "lg",
                        "startPoint": "#navbarVerticalNavMenu",
                        "endPoint": "#stickyBlockEndPoint",
                        "stickyOffsetTop": 20
                      }'>
                  <li class="nav-item">
                    <a class="nav-link active" href="#content">
                      <i class="bi-person nav-icon"></i> Basic information
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#emailSection">
                      <i class="bi-at nav-icon"></i> Email
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#passwordSection">
                      <i class="bi-key nav-icon"></i> Password
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#recentDevicesSection">
                      <i class="bi-phone nav-icon"></i> Recent devices
                    </a>
                  </li>
                </ul>
              </div>
              <!-- End Navbar Collapse -->
            </div>
            <!-- End Navbar -->
          </div>
        
          <div class="col-lg-9">
            <div class="d-grid gap-3 gap-lg-5">
              <!-- Card -->
              <div class="card">
                <div class="card-header">
                  <h2 class="card-title h4">Basic information</h2>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <!-- Form -->
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="fullname" class="col-sm-3 col-form-label form-label">Full name</label>
        
                      <div class="col-sm-9">
                        <div class="input-group input-group-sm-vertical">
                          <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Your full name" aria-label="Your full name" value="<?php echo $global_user_response['response'][0]['name']?>">
                        </div>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="username" class="col-sm-3 col-form-label form-label">Username</label>
        
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="username" id="username" placeholder="Your username" aria-label="Your username" value="<?php echo $global_user_response['response'][0]['username']?>">
                      </div>
                    </div>
                    <!-- End Form -->
        
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="emailaddress" class="col-sm-3 col-form-label form-label">Email Address</label>
        
                      <div class="col-sm-9">
                        <input type="email" class="form-control" name="emailaddress" id="emailaddress" placeholder="Email Address" aria-label="Emai Addressl" value="<?php echo $global_user_response['response'][0]['email']?>" readonly>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <span class="response-savebasicinfo"></span>
                    
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary btn-savebasicinfo" onclick="savebasicinfo()">Save changes</button>
                    </div>
                  <!-- End Form -->
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
        
              <!-- Card -->
              <div id="emailSection" class="card">
                <div class="card-header">
                  <h4 class="card-title">Email</h4>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <p>Your current email address is <span class="fw-semibold"><?php echo $global_user_response['response'][0]['email']?></span></p>
        
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="basicemail" class="col-sm-3 col-form-label form-label">New email address</label>
        
                      <div class="col-sm-9">
                        <input type="email" class="form-control" name="basicemail" id="basicemail" placeholder="Enter new email address" aria-label="Enter new email address">
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <span class="response-savebasicemail"></span>
        
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary btn-savebasicemail" onclick="savebasicemail()">Save changes</button>
                    </div>
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
        
              <!-- Card -->
              <div id="passwordSection" class="card">
                <div class="card-header">
                  <h4 class="card-title">Change your password</h4>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <!-- Form -->
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="currentpassword" class="col-sm-3 col-form-label form-label">Current password</label>
        
                      <div class="col-sm-9">
                        <input type="password" class="form-control" name="currentpassword" id="currentpassword" placeholder="Enter current password" aria-label="Enter current password">
                      </div>
                    </div>
                    <!-- End Form -->
        
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="newPassword" class="col-sm-3 col-form-label form-label">New password</label>
        
                      <div class="col-sm-9">
                        <input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="Enter new password" aria-label="Enter new password">
                      </div>
                    </div>
                    <!-- End Form -->
        
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="confirmpassword" class="col-sm-3 col-form-label form-label">Confirm new password</label>
        
                      <div class="col-sm-9">
                        <div class="mb-3">
                          <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="Confirm your new password" aria-label="Confirm your new password">
                        </div>
        
                        <h5>Password requirements:</h5>
        
                        <p class="fs-6 mb-2">Ensure that these requirements are met:</p>
        
                        <ul class="fs-6">
                          <li>Minimum 8 characters long - the more, the better</li>
                          <li>At least one lowercase character</li>
                          <li>At least one uppercase character</li>
                          <li>At least one number, symbol, or whitespace character</li>
                        </ul>
                      </div>
                    </div>
                    <!-- End Form -->
                    <span class="response-savenewpassword"></span>
                    
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary btn-savenewpassword" onclick="savenewpassword()">Save Changes</button>
                    </div>
                  <!-- End Form -->
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
        
              <!-- Card -->
              <div id="recentDevicesSection" class="card">
                <div class="card-header">
                  <h4 class="card-title">Recent devices</h4>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <p class="card-text">View and manage devices where you're currently logged in.</p>
                </div>
                <!-- End Body -->
        
                <!-- Table -->
                <div class="table-responsive">
                  <table class="table table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                      <tr>
                        <th>Browser</th>
                        <th>Device</th>
                        <th>IP</th>
                        <th>Most recent activity</th>
                      </tr>
                    </thead>
        
                    <tbody>
                        <?php
                            $global_data_count = json_decode(getData($db_prefix.'browser_log', ' ORDER BY 1 DESC LIMIT 10'), true);
                            foreach($global_data_count['response'] as $data){
                        ?>
                              <tr>
                                <td class="align-items-center"><?php echo $data['browser'];?> on <?php echo $data['device'];?></td>
                                <td><i class="bi-laptop fs-3 me-2"></i> <?php echo $data['device'];?> <?php if($data['cookie'] == getCookie('pp_admin')){echo '<span class="badge bg-soft-success text-success ms-1">Current</span>';}?></td>
                                <td><?php echo $data['ip'];?></td>
                                <td><?php echo timeAgo($data['created_at']);?></td>
                              </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                  </table>
                </div>
                <!-- End Table -->
              </div>
              <!-- End Card -->
        
        
        
            </div>
            <!-- Sticky Block End Point -->
            <div id="stickyBlockEndPoint"></div>
          </div>
        </div>
        <!-- End Row -->
        
        <script>
          function savenewpassword() {
              var currentpassword = document.querySelector("#currentpassword").value;
              var newPassword = document.querySelector("#newPassword").value;
              var confirmpassword = document.querySelector("#confirmpassword").value;
              
              document.querySelector(".btn-savenewpassword").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_newpassword", "currentpassword": currentpassword, "newPassword": newPassword, "confirmpassword": confirmpassword},
                    success: function (data) {
                        document.querySelector(".btn-savenewpassword").innerHTML = 'Save changes';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-savenewpassword").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            document.querySelector(".response-savenewpassword").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
          }
        
          function savebasicemail() {
              var basicemail = document.querySelector("#basicemail").value;

              document.querySelector(".btn-savebasicemail").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_basicemail", "email": basicemail},
                    success: function (data) {
                        document.querySelector(".btn-savebasicemail").innerHTML = 'Save changes';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-savebasicemail").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            document.querySelector(".response-savebasicemail").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
          }
        
          function savebasicinfo() {
              var fullname = document.querySelector("#fullname").value;
              var username = document.querySelector("#username").value;
              
              document.querySelector(".btn-savebasicinfo").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_basicinfo", "fullname": fullname , "username": username },
                    success: function (data) {
                        document.querySelector(".btn-savebasicinfo").innerHTML = 'Save changes';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-savebasicinfo").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            document.querySelector(".response-savebasicinfo").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
          }
        </script>
<?php
    }
?>