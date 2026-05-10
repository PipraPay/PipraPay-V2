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
              <h1 class="page-header-title">General Settings</h1>
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
                    <a class="nav-link active" href="#general">
                      <i class="bi bi-border-right nav-icon"></i> General
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#colorscheme">
                      <i class="bi bi-palette nav-icon"></i> Color Scheme
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#branding">
                      <i class="bi bi-brush nav-icon"></i> Branding
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#business-details">
                      <i class="bi bi-shop-window nav-icon"></i> Business Details
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#contact-social">
                      <i class="bi bi-alexa nav-icon"></i> Contact & Social
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
                  <h2 class="card-title h4">General</h2>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <!-- Form -->
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="sitename" class="col-sm-3 col-form-label form-label">Site Name</label>
        
                      <div class="col-sm-9">
                        <div class="input-group input-group-sm-vertical">
                          <input type="text" class="form-control" name="sitename" id="sitename" placeholder="Enter site name" aria-label="Enter site name" value="<?php if($global_setting_response['response'][0]['site_name'] !== "--"){echo $global_setting_response['response'][0]['site_name'];}?>">
                        </div>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="default_timezone" class="col-sm-3 col-form-label form-label">Default Timezone</label>
        
                      <div class="col-sm-9">
                          <div class="tom-select-custom">
                                <select class="js-select form-select" name="default_timezone" id="default_timezone">
                                    <?php
                                        $load = json_decode(getData($db_prefix.'timezone', ''), true);
                                        foreach($load['response'] as $in){
                                    ?>
                                            <option value="<?php echo $in['timezone']?>" data-option-template='<span class="d-flex align-items-center"><span class="text-truncate"><?php echo $in['timezone']?></span></span>' <?php if($global_setting_response['response'][0]['default_timezone'] == $in['timezone']){echo "selected";}?>><?php echo $in['timezone']?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                          </div>
                      </div>
                    </div>
                    <!-- End Form -->
        
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="default_currency" class="col-sm-3 col-form-label form-label">Default Currency</label>
        
                      <div class="col-sm-9">
                          <div class="tom-select-custom">
                                <select class="js-select form-select" name="default_currency" id="default_currency">
                                    <?php
                                        $load = json_decode(getData($db_prefix.'currency', ''), true);
                                        foreach($load['response'] as $in){
                                    ?>
                                            <option value="<?php echo $in['currency_code']?>" data-option-template='<span class="d-flex align-items-center"><span class="text-truncate"><?php echo $in['currency_name']?> - <?php echo $in['currency_code']?></span></span>' <?php if($global_setting_response['response'][0]['default_currency'] == $in['currency_code']){echo "selected";}?> data-symbol="<?php echo $in['currency_symbol']?>" data-rate="<?php echo $in['currency_rate']?>"><?php echo $in['currency_name']?> <?php echo $in['currency_code']?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                          </div>
                      </div>
                    </div>
                    <!-- End Form -->
        
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="currency_symbol" class="col-sm-3 col-form-label form-label">Currency Symbol</label>
        
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="currency_symbol" id="currency_symbol" placeholder="Enter currency symbol" aria-label="Enter currency symbol" value="<?php if($global_setting_response['response'][0]['currency_symbol'] !== "--"){echo $global_setting_response['response'][0]['currency_symbol'];}?>" readonly>
                      </div>
                    </div>
                    <!-- End Form -->

                    <!-- Form -->
                    <div class="row mb-4">
                      <label class="col-sm-3 col-form-label form-label">Currency Rate</label>
        
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="currency_rate" id="currency_rate" placeholder="Enter currency rate" aria-label="Enter currency rate" value="0" readonly>
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
              <div id="colorscheme" class="card">
                <div class="card-header">
                  <h4 class="card-title">Color Scheme</h4>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-4">
                          <label for="global_text_color" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Global Text Color</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="global_text_color" id="global_text_color" value="<?php if($global_setting_response['response'][0]['global_text_color'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['global_text_color'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-4">
                          <label for="primary_button_color" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Primary Button Color</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="primary_button_color" id="primary_button_color" value="<?php if($global_setting_response['response'][0]['primary_button_color'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['primary_button_color'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-4">
                          <label for="button_text_color" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Button Text Color</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="button_text_color" id="button_text_color" value="<?php if($global_setting_response['response'][0]['button_text_color'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['button_text_color'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->

                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-4">
                          <label for="button_hover_color" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Button Hover Color</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="button_hover_color" id="button_hover_color" value="<?php if($global_setting_response['response'][0]['button_hover_color'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['button_hover_color'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-4">
                          <label for="button_hover_text_color" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Button Hover Text Color</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="button_hover_text_color" id="button_hover_text_color" value="<?php if($global_setting_response['response'][0]['button_hover_text_color'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['button_hover_text_color'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-4">
                          <label for="navigation_background" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Navigation Background</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="navigation_background" id="navigation_background" value="<?php if($global_setting_response['response'][0]['navigation_background'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['navigation_background'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-4">
                          <label for="navigation_text_color" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Navigation Text Color</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="navigation_text_color" id="navigation_text_color" value="<?php if($global_setting_response['response'][0]['navigation_text_color'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['navigation_text_color'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-4">
                          <label for="active_tab_color" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Active Tab Color</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="active_tab_color" id="active_tab_color" value="<?php if($global_setting_response['response'][0]['active_tab_color'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['active_tab_color'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-4">
                          <label for="active_tab_text_color" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Active Tab Text Color</label>

                            <div class="input-group mb-3">
                                <input type="color" class="form-control form-control-color" name="active_tab_text_color" id="active_tab_text_color" value="<?php if($global_setting_response['response'][0]['active_tab_text_color'] == "--"){echo "#FFFFFF";}else{echo $global_setting_response['response'][0]['active_tab_text_color'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <span class="response-pp_savecolorscheme"></span>
        
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-danger btn-pp_resetcolorscheme" onclick="pp_resetcolorscheme()">Reset</button>
                        <button type="submit" class="btn btn-primary btn-pp_savecolorscheme" onclick="pp_savecolorscheme()">Save changes</button>
                    </div>
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
        
              <!-- Card -->
              <div id="branding" class="card">
                <div class="card-header">
                  <h4 class="card-title">Branding</h4>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                  <!-- Form -->
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="branding_favicon" class="col-sm-3 col-form-label form-label">Favicon (150X15)</label>
        
                      <div class="col-sm-9">
                            <input type="file" class="form-control" name="branding_favicon" id="branding_favicon" onchange="previewbranding_favicon(event)">
                            <div style="margin-bottom:10px;"></div>
                            
                            <i>N.B: Supported formats: JPG, PNG, JPEG, GIF, WEBP</i><br>
                            
                            <span class="branding_favicon-preview"></span>
                      </div>
                    </div>
                    <!-- End Form -->
        
                    <!-- Form -->
                    <div class="row mb-4">
                      <label for="branding_logo" class="col-sm-3 col-form-label form-label">Logo (166X60)</label>
        
                      <div class="col-sm-9">
                            <input type="file" class="form-control" name="branding_logo" id="branding_logo" onchange="previewbranding_logo(event)">
                            <div style="margin-bottom:10px;"></div>
                            
                            <i>N.B: Supported formats: JPG, PNG, JPEG, GIF, WEBP</i><br>
                            
                            <span class="branding_logo-preview"></span>
                      </div>
                    </div>
                    <!-- End Form -->
        
                    <span class="response-branding_logo"></span>
                    
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary btn-branding_logo">Save Changes</button>
                    </div>
                  <!-- End Form -->
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
        
              <!-- Card -->
              <div id="business-details" class="card">
                <div class="card-header">
                  <h4 class="card-title">Business Details</h4>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-6">
                          <label for="business_details_street_address" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Street Address</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="business_details_street_address" id="business_details_street_address" value="<?php if($global_setting_response['response'][0]['street_address'] !== "--"){echo $global_setting_response['response'][0]['street_address'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-6">
                          <label for="business_details_city_town" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">City/Town</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="business_details_city_town" id="business_details_city_town" value="<?php if($global_setting_response['response'][0]['city_town'] !== "--"){echo $global_setting_response['response'][0]['city_town'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->

                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-6">
                          <label for="business_details_postal_code" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Postal/ZIP Code</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="business_details_postal_code" id="business_details_postal_code" value="<?php if($global_setting_response['response'][0]['postal_zip_code'] !== "--"){echo $global_setting_response['response'][0]['postal_zip_code'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-6">
                          <label for="business_details_country" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Country</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="business_details_country" id="business_details_country" value="<?php if($global_setting_response['response'][0]['country'] !== "--"){echo $global_setting_response['response'][0]['country'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <span class="response-business_details"></span>
                    
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary btn-business_details" onclick="savebusiness_details()">Save Changes</button>
                    </div>
                  <!-- End Form -->
                </div>
                <!-- End Body -->
    
            </div>
            <!-- Sticky Block End Point -->

              <!-- Card -->
              <div id="contact-social" class="card">
                <div class="card-header">
                  <h4 class="card-title">Support Contact Information</h4>
                </div>
        
                <!-- Body -->
                <div class="card-body">
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-6">
                          <label for="support_contact_phone_mobile" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Support Phone Number</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="support_contact_phone_mobile" id="support_contact_phone_mobile" value="<?php if($global_setting_response['response'][0]['support_phone_number'] !== "--"){echo $global_setting_response['response'][0]['support_phone_number'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-6">
                          <label for="support_contact_email_addrss" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Support Email Address</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="support_contact_email_addrss" id="support_contact_email_addrss" value="<?php if($global_setting_response['response'][0]['support_email_address'] !== "--"){echo $global_setting_response['response'][0]['support_email_address'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->

                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-6">
                          <label for="support_contact_support_website" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Support Website</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="support_contact_support_website" id="support_contact_support_website" value="<?php if($global_setting_response['response'][0]['support_website'] !== "--"){echo $global_setting_response['response'][0]['support_website'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-6">
                          <label for="support_contact_facebok_page" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Facebook Page</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="support_contact_facebok_page" id="support_contact_facebok_page" value="<?php if($global_setting_response['response'][0]['facebook_page'] !== "--"){echo $global_setting_response['response'][0]['facebook_page'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-6">
                          <label for="support_contact_messenger" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Facebook Messenger</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="support_contact_messenger" id="support_contact_messenger" value="<?php if($global_setting_response['response'][0]['facebook_messenger'] !== "--"){echo $global_setting_response['response'][0]['facebook_messenger'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-6">
                          <label for="support_contact_whatsapp" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">WhatsApp Number</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="support_contact_whatsapp" id="support_contact_whatsapp" value="<?php if($global_setting_response['response'][0]['whatsapp_number'] !== "--"){echo $global_setting_response['response'][0]['whatsapp_number'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <!-- Form -->
                    <div class="row mb-4">
                      <div class="col-sm-6">
                          <label for="support_contact_telegram" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">Telegram</label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="support_contact_telegram" id="support_contact_telegram" value="<?php if($global_setting_response['response'][0]['telegram'] !== "--"){echo $global_setting_response['response'][0]['telegram'];}?>">
                            </div>
                      </div>
                      
                      <div class="col-sm-6">
                          <label for="support_contact_youtube" class="col-sm-3 col-form-label form-label" style=" width: 100%; ">YouTube Channel </label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="support_contact_youtube" id="support_contact_youtube" value="<?php if($global_setting_response['response'][0]['youtube_channel'] !== "--"){echo $global_setting_response['response'][0]['youtube_channel'];}?>">
                            </div>
                      </div>
                    </div>
                    <!-- End Form -->
                    
                    <span class="response-support_contact"></span>
                    
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary btn-support_contact" onclick="savesupport_contact()">Save Changes</button>
                    </div>
                  <!-- End Form -->
                </div>
                <!-- End Body -->
    
            </div>
            <!-- Sticky Block End Point -->
            
            
            <div id="stickyBlockEndPoint"></div>
          </div>
        </div>
        <!-- End Row -->
        
        <script> 
           function initialize(){
               HSCore.components.HSTomSelect.init('.js-select');
           }
           initialize();
                   
           document.getElementById('default_currency').addEventListener('change', function () {
                const selectedValue = this.value;
                const selectedOption = document.querySelector(`#default_currency option[value="${selectedValue}"]`);

                document.querySelector("#currency_symbol").value = selectedOption.dataset.symbol;
                document.querySelector("#currency_rate").value = selectedOption.dataset.rate;
           });
           

            function savesupport_contact(){
              var support_contact_phone_mobile = document.querySelector("#support_contact_phone_mobile").value;
              var support_contact_email_addrss = document.querySelector("#support_contact_email_addrss").value;
              var support_contact_support_website = document.querySelector("#support_contact_support_website").value;
              var support_contact_facebok_page = document.querySelector("#support_contact_facebok_page").value;

              var support_contact_messenger = document.querySelector("#support_contact_messenger").value;
              var support_contact_whatsapp = document.querySelector("#support_contact_whatsapp").value;
              var support_contact_telegram = document.querySelector("#support_contact_telegram").value;
              var support_contact_youtube = document.querySelector("#support_contact_youtube").value;
              
              document.querySelector(".btn-support_contact").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_savesupport_contact", "support_contact_phone_mobile": support_contact_phone_mobile, "support_contact_email_addrss": support_contact_email_addrss, "support_contact_support_website": support_contact_support_website, "support_contact_facebok_page": support_contact_facebok_page, "support_contact_messenger": support_contact_messenger, "support_contact_whatsapp": support_contact_whatsapp, "support_contact_telegram": support_contact_telegram, "support_contact_youtube": support_contact_youtube},
                    success: function (data) {
                        document.querySelector(".btn-support_contact").innerHTML = 'Save changes';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-support_contact").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            document.querySelector(".response-support_contact").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
            }
          


            $(document).ready(function () {
                $('.btn-branding_logo').click(function (e) {
                    e.preventDefault();
            
                    $(".btn-branding_logo").html('<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>');
                    $(".btn-branding_logo").prop("disabled", true);
            
                    var formData = new FormData();
                    formData.append('action', 'pp_branding');
                    formData.append('branding_favicon', $('#branding_favicon')[0].files[0]);
                    formData.append('branding_logo', $('#branding_logo')[0].files[0]);
            
                    $.ajax({
                        type: "POST",
                        url: "<?php echo PP_URL; ?>/admin/dashboard",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            console.log(data);
                            $(".btn-branding_logo").prop("disabled", false);
                            $(".btn-branding_logo").html('Save changes');
                
                            var dedata = JSON.parse(data);
                            
                            if(dedata.status == "false"){
                                document.querySelector(".response-branding_logo").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                            }else{
                                document.querySelector(".response-branding_logo").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                            }
                        }
                    });
                });
            });


            function load_branding_logo(){
                $('.branding_logo-preview').html('<img src="<?php if(isset($global_setting_response['response'][0]['logo'])){if($global_setting_response['response'][0]['logo'] == "--"){echo 'https://cdn.piprapay.com/media/logo.png';}else{echo $global_setting_response['response'][0]['logo'];};}else{echo 'https://cdn.piprapay.com/media/logo.png';}?>" style=" max-width: 100%; max-height: 60px; margin-top: 10px; ">');
            }
            load_branding_logo();
            
            function load_branding_favicon(){
                $('.branding_favicon-preview').html('<img src="<?php if(isset($global_setting_response['response'][0]['favicon'])){if($global_setting_response['response'][0]['favicon'] == "--"){echo 'https://cdn.piprapay.com/media/favicon.png';}else{echo $global_setting_response['response'][0]['favicon'];};}else{echo 'https://cdn.piprapay.com/media/favicon.png';}?>" style="margin-top: 10px;max-width: 100px;max-height: 100px;">');
            }
            load_branding_favicon();

            function previewbranding_logo(event) {
                const file = event.target.files[0];
                const reader = new FileReader();
    
                reader.onload = function() {
                    $('.branding_logo-preview').html('<img src="'+reader.result+'" style=" max-width: 100%; max-height: 60px; margin-top: 10px; ">');
                }
    
                if (file) {
                    reader.readAsDataURL(file);
                }
            }
            
            function previewbranding_favicon(event) {
                const file = event.target.files[0];
                const reader = new FileReader();
    
                reader.onload = function() {
                    $('.branding_favicon-preview').html('<img src="'+reader.result+'" style="margin-top: 10px;max-width: 100px;max-height: 100px;">');
                }
    
                if (file) {
                    reader.readAsDataURL(file);
                }
            }
            
            function savebusiness_details(){
              var business_details_street_address = document.querySelector("#business_details_street_address").value;
              var business_details_city_town = document.querySelector("#business_details_city_town").value;
              var business_details_postal_code = document.querySelector("#business_details_postal_code").value;
              var business_details_country = document.querySelector("#business_details_country").value;

              document.querySelector(".btn-business_details").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_savebusiness_details", "business_details_street_address": business_details_street_address, "business_details_city_town": business_details_city_town, "business_details_postal_code": business_details_postal_code, "business_details_country": business_details_country},
                    success: function (data) {
                        document.querySelector(".btn-business_details").innerHTML = 'Save changes';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-business_details").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            document.querySelector(".response-business_details").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
            }
          
          function pp_resetcolorscheme() {
              document.querySelector(".btn-pp_resetcolorscheme").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_resetcolorscheme"},
                    success: function (data) {
                        document.querySelector(".btn-pp_resetcolorscheme").innerHTML = 'Reset';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-pp_savecolorscheme").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            document.querySelector(".response-pp_savecolorscheme").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
          }
        
          function pp_savecolorscheme() {
              var global_text_color = document.querySelector("#global_text_color").value;
              var primary_button_color = document.querySelector("#primary_button_color").value;
              var button_text_color = document.querySelector("#button_text_color").value;
              var button_hover_color = document.querySelector("#button_hover_color").value;
              var button_hover_text_color = document.querySelector("#button_hover_text_color").value;
              var navigation_background = document.querySelector("#navigation_background").value;
              var navigation_text_color = document.querySelector("#navigation_text_color").value;
              var active_tab_color = document.querySelector("#active_tab_color").value;
              var active_tab_text_color = document.querySelector("#active_tab_text_color").value;

              document.querySelector(".btn-pp_savecolorscheme").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_savecolorscheme", "global_text_color": global_text_color, "primary_button_color": primary_button_color, "button_text_color": button_text_color, "button_hover_color": button_hover_color, "button_hover_text_color": button_hover_text_color, "navigation_background": navigation_background, "navigation_text_color": navigation_text_color, "active_tab_color": active_tab_color, "active_tab_text_color": active_tab_text_color},
                    success: function (data) {
                        document.querySelector(".btn-pp_savecolorscheme").innerHTML = 'Save changes';
                        
                        var dedata = JSON.parse(data);
                        
                        if(dedata.status == "false"){
                            document.querySelector(".response-pp_savecolorscheme").innerHTML = '<div class="alert alert-danger" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }else{
                            document.querySelector(".response-pp_savecolorscheme").innerHTML = '<div class="alert alert-primary" style="margin-top:10px;margin-bottom:10px"> <i class="fa fa-info-circle me-2"></i> '+dedata.message+'</div>';
                        }
                    }
                });
          }
        
          function savebasicinfo() {
              var sitename = document.querySelector("#sitename").value;
              var default_timezone = document.querySelector("#default_timezone").value;
              var default_currency = document.querySelector("#default_currency").value;
              var currency_symbol = document.querySelector("#currency_symbol").value;
              var currency_rate = document.querySelector("#currency_rate").value;
              
              document.querySelector(".btn-savebasicinfo").innerHTML = '<div class="spinner-border text-light spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';
              
                $.ajax
                ({
                    type: "POST",
                    url: "<?php echo PP_URL; ?>/admin/dashboard",
                    data: { "action": "pp_systembasicinfo", "sitename": sitename , "default_timezone": default_timezone, "default_currency": default_currency , "currency_symbol": currency_symbol, "currency_rate": currency_rate },
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