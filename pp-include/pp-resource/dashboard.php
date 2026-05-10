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
                <h1 class="page-header-title">Dashboard</h1>
              </div>
              <!-- End Col -->
        
              <div class="col-auto">
                <a class="btn btn-primary" href="<?php echo PP_URL; ?>/pp-auto-update">
                  <i class="bi-arrow-clockwise me-1"></i> Check Updates
                </a>
              </div>
              <!-- End Col -->
            </div>
            <!-- End Row -->
          </div>
          <!-- End Page Header -->
        
          <!-- Stats -->
          <div class="row">
            <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5" onclick="load_content('Transaction','transaction?status=all','nav-btn-transaction')">
              <!-- Card -->
              <a class="card card-hover-shadow h-100" href="javascript:void(0);">
                <div class="card-body">
                  <h6 class="card-subtitle">Total Transaction</h6>
        
                  <div class="row align-items-center gx-2 mb-1">
                    <div class="col-6">
                      <h2 class="card-title text-inherit">
                          <?php
                                $count = 0;
                                $global_data_count = json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status NOT IN ("initialize")'), true);
                                foreach($global_data_count['response'] as $data_count){
                                        $count = $count+1;
                                }
                                echo $count;
                          ?>
                      </h2>
                    </div>
                    <!-- End Col -->
        
                    <div class="col-6">
                      <!-- Chart -->
                      <div class="chartjs-custom" style="height: 3rem;">
                            <?php
                                $chartJson = getChart($db_prefix.'transaction', 'created_at', 'monthly');
                                $chartData = json_decode($chartJson, true);
                                
                                $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                $data = isset($chartData['data']) ? $chartData['data'] : [];
        
                                $labelsJson = json_encode($labels);
                                $dataJson = json_encode($data);
                            ?>
                            
                            <canvas class="js-chart" data-hs-chartjs-options='{
                              "type": "line",
                              "data": {
                                "labels": <?php echo $labelsJson; ?>,
                                "datasets": [{
                                  "data": <?php echo $dataJson; ?>,
                                  "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                                  "borderColor": "#3BB77E",
                                  "borderWidth": 2,
                                  "pointRadius": 0,
                                  "pointHoverRadius": 0
                                }]
                              },
                              "options": {
                                "scales": {
                                  "y": {
                                    "display": false
                                  },
                                  "x": {
                                    "display": false
                                  }
                                },
                                "hover": {
                                  "mode": "nearest",
                                  "intersect": false
                                },
                                "plugins": {
                                  "tooltip": {
                                    "postfix": "",
                                    "hasIndicator": true,
                                    "intersect": false
                                  }
                                }
                              }
                            }'>
                            </canvas>
                      </div>
                      <!-- End Chart -->
                    </div>
                    <!-- End Col -->
                  </div>
                  <!-- End Row -->
                  <?php
                      $stats = getDataChangeStats($db_prefix.'transaction', 'transaction_status != "initialize"', 'created_at', 'monthly');
                  ?>
                    <?php if (!empty($stats) && isset($stats['up'], $stats['percent'], $stats['previous'])): ?>
                      <span class="badge bg-soft-<?= $stats['up'] ? 'success' : 'danger' ?> text-<?= $stats['up'] ? 'success' : 'danger' ?>">
                        <i class="bi bi-graph-<?= $stats['up'] ? 'up' : 'down' ?>"></i> <?= abs(round($stats['percent'], 2)) ?>%
                      </span>
                      <span class="text-body fs-6 ms-1">from <?= number_format($stats['previous']) ?></span>
                    <?php else: ?>
                      <span class="text-muted">No data available</span>
                    <?php endif; ?>
        
                </div>
              </a>
              <!-- End Card -->
            </div>
        
            <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5" onclick="load_content('Transaction','transaction?status=pending','nav-btn-transaction')">
              <!-- Card -->
              <a class="card card-hover-shadow h-100" href="javascript:void(0);">
                <div class="card-body">
                  <h6 class="card-subtitle">Pending Transaction</h6>
        
                  <div class="row align-items-center gx-2 mb-1">
                    <div class="col-6">
                      <h2 class="card-title text-inherit">
                          <?php
                                $count = 0;
                                $global_data_count = json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "pending"'), true);
                                foreach($global_data_count['response'] as $data_count){
                                        $count = $count+1;
                                }
                                echo $count;
                          ?>
                      </h2>
                    </div>
                    <!-- End Col -->
        
                    <div class="col-6">
                      <!-- Chart -->
                      <div class="chartjs-custom" style="height: 3rem;">
                            <?php
                                $chartJson = getChart($db_prefix.'transaction', 'created_at', 'monthly', null, null, 'transaction_status = "pending"');
                                $chartData = json_decode($chartJson, true);
                                
                                $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                $data = isset($chartData['data']) ? $chartData['data'] : [];
        
                                $labelsJson = json_encode($labels);
                                $dataJson = json_encode($data);
                            ?>
                            
                            <canvas class="js-chart" data-hs-chartjs-options='{
                              "type": "line",
                              "data": {
                                "labels": <?php echo $labelsJson; ?>,
                                "datasets": [{
                                  "data": <?php echo $dataJson; ?>,
                                  "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                                  "borderColor": "#3BB77E",
                                  "borderWidth": 2,
                                  "pointRadius": 0,
                                  "pointHoverRadius": 0
                                }]
                              },
                              "options": {
                                "scales": {
                                  "y": {
                                    "display": false
                                  },
                                  "x": {
                                    "display": false
                                  }
                                },
                                "hover": {
                                  "mode": "nearest",
                                  "intersect": false
                                },
                                "plugins": {
                                  "tooltip": {
                                    "postfix": "",
                                    "hasIndicator": true,
                                    "intersect": false
                                  }
                                }
                              }
                            }'>
                            </canvas>
                      </div>
                      <!-- End Chart -->
                    </div>
                    <!-- End Col -->
                  </div>
                  <!-- End Row -->
        
                  <?php
                      $stats = getDataChangeStats($db_prefix.'transaction', 'transaction_status = "pending"', 'created_at', 'monthly');
                  ?>
                    <?php if (!empty($stats) && isset($stats['up'], $stats['percent'], $stats['previous'])): ?>
                      <span class="badge bg-soft-<?= $stats['up'] ? 'success' : 'danger' ?> text-<?= $stats['up'] ? 'success' : 'danger' ?>">
                        <i class="bi bi-graph-<?= $stats['up'] ? 'up' : 'down' ?>"></i> <?= abs(round($stats['percent'], 2)) ?>%
                      </span>
                      <span class="text-body fs-6 ms-1">from <?= number_format($stats['previous']) ?></span>
                    <?php else: ?>
                      <span class="text-muted">No data available</span>
                    <?php endif; ?>
                </div>
              </a>
              <!-- End Card -->
            </div>
        
            <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5" onclick="load_content('Invoices','invoices?status=unpaid','nav-btn-invoices')">
              <!-- Card -->
              <a class="card card-hover-shadow h-100" href="javascript:void(0);">
                <div class="card-body">
                  <h6 class="card-subtitle">Unpaid Invoice</h6>
        
                  <div class="row align-items-center gx-2 mb-1">
                    <div class="col-6">
                      <h2 class="card-title text-inherit">
                          <?php
                                $count = 0;
                                $global_data_count = json_decode(getData($db_prefix.'invoice', 'WHERE i_status = "unpaid"'), true);
                                foreach($global_data_count['response'] as $data_count){
                                        $count = $count+1;
                                }
                                echo $count;
                          ?>
                      </h2>
                    </div>
                    <!-- End Col -->
        
                    <div class="col-6">
                      <!-- Chart -->
                      <div class="chartjs-custom" style="height: 3rem;">
                            <?php
                                $chartJson = getChart($db_prefix.'invoice', 'created_at', 'monthly', null, null, 'i_status = "unpaid"');
                                $chartData = json_decode($chartJson, true);
                                
                                $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                $data = isset($chartData['data']) ? $chartData['data'] : [];
        
                                $labelsJson = json_encode($labels);
                                $dataJson = json_encode($data);
                            ?>
                            
                            <canvas class="js-chart" data-hs-chartjs-options='{
                              "type": "line",
                              "data": {
                                "labels": <?php echo $labelsJson; ?>,
                                "datasets": [{
                                  "data": <?php echo $dataJson; ?>,
                                  "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                                  "borderColor": "#3BB77E",
                                  "borderWidth": 2,
                                  "pointRadius": 0,
                                  "pointHoverRadius": 0
                                }]
                              },
                              "options": {
                                "scales": {
                                  "y": {
                                    "display": false
                                  },
                                  "x": {
                                    "display": false
                                  }
                                },
                                "hover": {
                                  "mode": "nearest",
                                  "intersect": false
                                },
                                "plugins": {
                                  "tooltip": {
                                    "postfix": "",
                                    "hasIndicator": true,
                                    "intersect": false
                                  }
                                }
                              }
                            }'>
                            </canvas>
                      </div>
                      <!-- End Chart -->
                    </div>
                    <!-- End Col -->
                  </div>
                  <!-- End Row -->
        
                  <?php
                      $stats = getDataChangeStats($db_prefix.'invoice', 'i_status = "unpaid"', 'created_at', 'monthly');
                  ?>
                    <?php if (!empty($stats) && isset($stats['up'], $stats['percent'], $stats['previous'])): ?>
                      <span class="badge bg-soft-<?= $stats['up'] ? 'success' : 'danger' ?> text-<?= $stats['up'] ? 'success' : 'danger' ?>">
                        <i class="bi bi-graph-<?= $stats['up'] ? 'up' : 'down' ?>"></i> <?= abs(round($stats['percent'], 2)) ?>%
                      </span>
                      <span class="text-body fs-6 ms-1">from <?= number_format($stats['previous']) ?></span>
                    <?php else: ?>
                      <span class="text-muted">No data available</span>
                    <?php endif; ?>
                </div>
              </a>
              <!-- End Card -->
            </div>
        
            <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5" onclick="load_content('Customers','customers','nav-btn-customers')">
              <!-- Card -->
              <a class="card card-hover-shadow h-100" href="javascript:void(0);">
                <div class="card-body">
                  <h6 class="card-subtitle">Total Customers</h6>
        
                  <div class="row align-items-center gx-2 mb-1">
                    <div class="col-6">
                      <h2 class="card-title text-inherit">
                          <?php
                                $count = 0;
                                $global_data_count = json_decode(getData($db_prefix.'customer', ''), true);
                                foreach($global_data_count['response'] as $data_count){
                                        $count = $count+1;
                                }
                                echo $count;
                          ?>
                      </h2>
                    </div>
                    <!-- End Col -->
        
                    <div class="col-6">
                      <!-- Chart -->
                      <div class="chartjs-custom" style="height: 3rem;">
                            <?php
                                $chartJson = getChart($db_prefix.'customer', 'created_at', 'yearly');
                                $chartData = json_decode($chartJson, true);
                                
                                $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                $data = isset($chartData['data']) ? $chartData['data'] : [];
        
                                $labelsJson = json_encode($labels);
                                $dataJson = json_encode($data);
                            ?>
                            
                            <canvas class="js-chart" data-hs-chartjs-options='{
                              "type": "line",
                              "data": {
                                "labels": <?php echo $labelsJson; ?>,
                                "datasets": [{
                                  "data": <?php echo $dataJson; ?>,
                                  "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                                  "borderColor": "#3BB77E",
                                  "borderWidth": 2,
                                  "pointRadius": 0,
                                  "pointHoverRadius": 0
                                }]
                              },
                              "options": {
                                "scales": {
                                  "y": {
                                    "display": false
                                  },
                                  "x": {
                                    "display": false
                                  }
                                },
                                "hover": {
                                  "mode": "nearest",
                                  "intersect": false
                                },
                                "plugins": {
                                  "tooltip": {
                                    "postfix": "",
                                    "hasIndicator": true,
                                    "intersect": false
                                  }
                                }
                              }
                            }'>
                            </canvas>
                      </div>
                      <!-- End Chart -->
                    </div>
                    <!-- End Col -->
                  </div>
                  <!-- End Row -->
        
                  <?php
                      $stats = getDataChangeStats($db_prefix.'customer', '', 'created_at', 'monthly');
                  ?>
                    <?php if (!empty($stats) && isset($stats['up'], $stats['percent'], $stats['previous'])): ?>
                      <span class="badge bg-soft-<?= $stats['up'] ? 'success' : 'danger' ?> text-<?= $stats['up'] ? 'success' : 'danger' ?>">
                        <i class="bi bi-graph-<?= $stats['up'] ? 'up' : 'down' ?>"></i> <?= abs(round($stats['percent'], 2)) ?>%
                      </span>
                      <span class="text-body fs-6 ms-1">from <?= number_format($stats['previous']) ?></span>
                    <?php else: ?>
                      <span class="text-muted">No data available</span>
                    <?php endif; ?>
                </div>
              </a>
              <!-- End Card -->
            </div>
          </div>
          <!-- End Stats -->
        
          <div class="row">
            <div class="col-lg-12 mb-3 mb-lg-5">
              <!-- Card -->
              <div class="card h-100">
                <!-- Header -->
                <div class="card-header card-header-content-sm-between">
                  <h4 class="card-header-title mb-2 mb-sm-0">Transaction Statistics</h4>
        
                  <ul class="nav nav-segment nav-fill" id="expensesTab" role="tablist">
                    <li class="nav-item" data-bs-toggle="chart-bar" data-datasets="today" data-trigger="click" data-action="toggle" role="presentation">
                      <a class="nav-link active" href="javascript:void(0)" data-bs-toggle="tab" aria-selected="true" role="tab">Today</a>
                    </li>
                    <li class="nav-item" data-bs-toggle="chart-bar" data-datasets="monthly" data-trigger="click" data-action="toggle" role="presentation">
                      <a class="nav-link" href="javascript:void(0)" data-bs-toggle="tab" aria-selected="false" tabindex="-1" role="tab">Monthly</a>
                    </li>
                    <li class="nav-item" data-bs-toggle="chart-bar" data-datasets="yearly" data-trigger="click" data-action="toggle" role="presentation">
                      <a class="nav-link" href="javascript:void(0)" data-bs-toggle="tab" aria-selected="false" tabindex="-1" role="tab">Yearly</a>
                    </li>
                  </ul>
                </div>
                <!-- End Header -->
        
                <!-- Body -->
                <div class="card-body">
                  <div class="row mb-4">
                    <div class="col-sm mb-2 mb-sm-0"></div>
                    <!-- End Col -->
        
                    <div class="col-sm-auto align-self-sm-end">
                      <div class="row fs-6 text-body">
                        <div class="col-auto">
                          <span class="legend-indicator bg-primary"></span> Completed
                        </div>
                        <!-- End Col -->
                        
                        <div class="col-auto">
                          <span class="legend-indicator bg-warning"></span> Pending
                        </div>
                        <!-- End Col -->
                        
                        <div class="col-auto">
                          <span class="legend-indicator bg-warning"></span> Refunded
                        </div>
                        <!-- End Col -->
                        
                        <div class="col-auto">
                          <span class="legend-indicator bg-danger"></span> Failed
                        </div>
                        <!-- End Col -->
                      </div>
                      <!-- End Row -->
                    </div>
                    <!-- End Col -->
                  </div>
                  <!-- End Row -->
        
                  <!-- Bar Chart -->
                  <div class="chartjs-custom">
                    <?php
                        $chartJson = getChart($db_prefix.'transaction', 'created_at', 'today', null, null, 'transaction_status = "completed"');
                        $chartData = json_decode($chartJson, true);
                        
                        $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                        $data = isset($chartData['data']) ? $chartData['data'] : [];
        
                        $labelsJson = json_encode($labels);
                        $dataJsoncompleted = json_encode($data);
                    ?>
        
                    <?php
                        $chartJson = getChart($db_prefix.'transaction', 'created_at', 'today', null, null, 'transaction_status = "pending"');
                        $chartData = json_decode($chartJson, true);
                        
                        $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                        $data = isset($chartData['data']) ? $chartData['data'] : [];
        
                        $dataJsonpending = json_encode($data);
                    ?>
                    
                    <?php
                        $chartJson = getChart($db_prefix.'transaction', 'created_at', 'today', null, null, 'transaction_status = "refunded"');
                        $chartData = json_decode($chartJson, true);
                        
                        $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                        $data = isset($chartData['data']) ? $chartData['data'] : [];
        
                        $dataJsonrefunded = json_encode($data);
                    ?>
                    
                    <?php
                        $chartJson = getChart($db_prefix.'transaction', 'created_at', 'today', null, null, 'transaction_status = "failed"');
                        $chartData = json_decode($chartJson, true);
                        
                        $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                        $data = isset($chartData['data']) ? $chartData['data'] : [];
        
                        $dataJsonfailed = json_encode($data);
                    ?>
                    
                    <canvas id="updatingBarChart" style="height: 20rem;" data-hs-chartjs-options='{
                              "type": "bar",
                              "data": {
                                "labels": <?php echo $labelsJson; ?>,
                                "datasets": [{
                                  "data": <?php echo $dataJsoncompleted?>,
                                  "backgroundColor": "#3BB77E",
                                  "hoverBackgroundColor": "#3BB77E",
                                  "borderColor": "#3BB77E",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonpending?>,
                                  "backgroundColor": "#f5ca99",
                                  "borderColor": "#f5ca99",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonrefunded?>,
                                  "backgroundColor": "#f5ca99",
                                  "borderColor": "#f5ca99",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonfailed?>,
                                  "backgroundColor": "#d63384",
                                  "borderColor": "#d63384",
                                  "maxBarThickness": "10"
                                }]
                              },
                              "options": {
                                "scales": {
                                  "y": {
                                    "grid": {
                                      "color": "#e7eaf3",
                                      "drawBorder": false,
                                      "zeroLineColor": "#e7eaf3"
                                    },
                                    "ticks": {
                                      "beginAtZero": true,
                                      "stepSize": 100,
                                      "fontSize": 12,
                                      "fontColor":  "#97a4af",
                                      "fontFamily": "Open Sans, sans-serif",
                                      "padding": 10,
                                      "postfix": ""
                                    }
                                  },
                                  "x": {
                                    "grid": {
                                      "display": false,
                                      "drawBorder": false
                                    },
                                    "ticks": {
                                      "fontSize": 12,
                                      "fontColor":  "#97a4af",
                                      "fontFamily": "Open Sans, sans-serif",
                                      "padding": 5
                                    },
                                    "categoryPercentage": 0.5,
                                    "maxBarThickness": "10"
                                  }
                                },
                                "cornerRadius": 2,
                                "plugins": {
                                  "tooltip": {
                                    "prefix": "",
                                    "hasIndicator": true,
                                    "mode": "index",
                                    "intersect": false
                                  }
                                },
                                "hover": {
                                  "mode": "nearest",
                                  "intersect": true
                                }
                              }
                            }'></canvas>
                  </div>
                  <!-- End Bar Chart -->
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->
        
          <div class="row mb-3">
            <div class="col-lg-6 mb-3 mb-lg-0">
              <!-- Card -->
              <div class="card h-100">
                <!-- Header -->
                <div class="card-header card-header-content-sm-between">
                  <h4 class="card-header-title mb-2 mb-sm-0">Transactions</h4>
                  
                  <ul class="nav nav-segment nav-fill" style="visibility: hidden;"><li class="nav-item"><a class="nav-link">Today</a></li></ul>
                </div>
                <!-- End Header -->
        
                <!-- Body -->
                <div class="card-body">
                  <!-- Chart -->
                  <?php
                        $completedcount = 0;
                        $global_data_count = json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "completed"'), true);
                        foreach($global_data_count['response'] as $data_count){
                                $completedcount = $completedcount+1;
                        }
                  ?>
                  <?php
                        $pendingcount = 0;
                        $global_data_count = json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "pending"'), true);
                        foreach($global_data_count['response'] as $data_count){
                                $pendingcount = $pendingcount+1;
                        }
                  ?>
                  <?php
                        $refundedcount = 0;
                        $global_data_count = json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "refunded"'), true);
                        foreach($global_data_count['response'] as $data_count){
                                $refundedcount = $refundedcount+1;
                        }
                  ?>
                  <?php
                        $failedcount = 0;
                        $global_data_count = json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "failed"'), true);
                        foreach($global_data_count['response'] as $data_count){
                                $failedcount = $failedcount+1;
                        }
                  ?>
                  
                  <div class="chartjs-custom mx-auto" style="height: 20rem;">
                    <canvas class="js-chart-datalabels" data-hs-chartjs-options='{
                      "type": "bubble",
                      "data": {
                        "datasets": [
                          {
                            "label": "Label 1",
                            "data": [
                              {"x": 50, "y": 65, "r": <?php echo $completedcount?>}
                            ],
                            "color": "#fff",
                            "backgroundColor": "#3BB77E",
                            "borderColor": "transparent"
                          },
                          {
                            "label": "Label 2",
                            "data": [
                              {"x": 46, "y": 42, "r": <?php echo $pendingcount?>}
                            ],
                            "color": "#fff",
                            "backgroundColor": "#f5ca99",
                            "borderColor": "transparent"
                          },
                          {
                            "label": "Label 3",
                            "data": [
                              {"x": 48, "y": 15, "r": <?php echo $refundedcount?>}
                            ],
                            "color": "#fff",
                            "backgroundColor": "#f5ca99",
                            "borderColor": "transparent"
                          },
                          {
                            "label": "Label 3",
                            "data": [
                              {"x": 55, "y": 2, "r": <?php echo $failedcount?>}
                            ],
                            "color": "#fff",
                            "backgroundColor": "#d63384",
                            "borderColor": "transparent"
                          }
                        ]
                      },
                      "options": {
                        "scales": {
                          "y": {
                            "grid": {
                              "display": false,
                              "drawBorder": false
                            },
                            "ticks": {
                              "display": false,
                              "max": 100,
                              "beginAtZero": true
                            }
                          },
                          "x": {
                          "grid": {
                              "display": false,
                              "drawBorder": false
                            },
                            "ticks": {
                              "display": false,
                              "max": 100,
                              "beginAtZero": true
                            }
                          }
                        },
                        "plugins": {
                          "tooltip": false
                        }
                      }
                    }'></canvas>
                  </div>
                  <!-- End Chart -->
        
                  <div class="row justify-content-center">
                    <div class="col-auto">
                      <span class="legend-indicator bg-primary"></span> Completed
                    </div>
                    <!-- End Col -->
                    
                    <div class="col-auto">
                      <span class="legend-indicator bg-warning"></span> Pending
                    </div>
                    <!-- End Col -->
                    
                    <div class="col-auto">
                      <span class="legend-indicator bg-warning"></span> Refunded
                    </div>
                    <!-- End Col -->
                    
                    <div class="col-auto">
                      <span class="legend-indicator bg-danger"></span> Failed
                    </div>
                    <!-- End Col -->
                  </div>
                  <!-- End Row -->
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
            </div>
        
            <div class="col-lg-6">
              <!-- Card -->
              <div class="card h-100">
                <!-- Header -->
                <div class="card-header card-header-content-between">
                  <h4 class="card-header-title">Reports overview</h4>
        
                  <!-- Dropdown -->
                  <div class="dropdown" style="visibility: hidden;">
                    <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm rounded-circle" id="reportsOverviewDropdown1" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi-three-dots-vertical"></i></button>
                  </div>
                  <!-- End Dropdown -->
                </div>
                <!-- End Header -->
                
                <?php
                    $total_report_overview = 0;
                    
                    $total_report_overview_complete = 0;
                    $global_cal= json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "completed"'), true);
                    foreach($global_cal['response'] as $cal){
                        $total_amount = $cal['transaction_amount']+$cal['transaction_fee'];
                        $net_amount = $total_amount-$cal['transaction_refund_amount'];
                        
                        $total_report_overview_complete += convertToDefault($net_amount, $cal['transaction_currency'], $global_setting_response['response'][0]['default_currency']);
                    }
                    
                    $total_report_overview_pending = 0;
                    $global_cal= json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "pending"'), true);
                    foreach($global_cal['response'] as $cal){
                        $total_amount = $cal['transaction_amount']+$cal['transaction_fee'];
                        $net_amount = $total_amount-$cal['transaction_refund_amount'];
                        
                        $total_report_overview_pending += convertToDefault($net_amount, $cal['transaction_currency'], $global_setting_response['response'][0]['default_currency']);
                    }
                    
                    $total_report_overview_refunded = 0;
                    $global_cal= json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "refunded"'), true);
                    foreach($global_cal['response'] as $cal){
                        $total_amount = $cal['transaction_amount']+$cal['transaction_fee'];
                        $net_amount = $total_amount-$cal['transaction_refund_amount'];
                        
                        $total_report_overview_refunded += convertToDefault($net_amount, $cal['transaction_currency'], $global_setting_response['response'][0]['default_currency']);
                    }
                    
                    $total_report_overview_failed = 0;
                    $global_cal= json_decode(getData($db_prefix.'transaction', 'WHERE transaction_status = "failed"'), true);
                    foreach($global_cal['response'] as $cal){
                        $total_amount = $cal['transaction_amount']+$cal['transaction_fee'];
                        $net_amount = $total_amount-$cal['transaction_refund_amount'];
                        
                        $total_report_overview_failed += convertToDefault($net_amount, $cal['transaction_currency'], $global_setting_response['response'][0]['default_currency']);
                    }
                    
                    $total_report_overview = $total_report_overview_complete+$total_report_overview_pending+$total_report_overview_refunded+$total_report_overview_failed;
                    
                    $total = $total_report_overview_complete 
                           + $total_report_overview_pending 
                           + $total_report_overview_refunded 
                           + $total_report_overview_failed;
                    
                    if ($total > 0) {
                        $percent_complete = round(($total_report_overview_complete / $total) * 100, 2);
                        $percent_pending = round(($total_report_overview_pending / $total) * 100, 2);
                        $percent_refunded = round(($total_report_overview_refunded / $total) * 100, 2);
                        $percent_failed = round(($total_report_overview_failed / $total) * 100, 2);
                    } else {
                        // All values are 0 — so all percentages are 0%
                        $percent_complete = 0;
                        $percent_pending = 0;
                        $percent_refunded = 0;
                        $percent_failed = 0;
                    }
                ?>
        
                <!-- Body -->
                <div class="card-body">
                  <span class="h1 d-block mb-4"><?php echo $global_setting_response['response'][0]['currency_symbol']?> <?php echo number_format($total_report_overview,2);?> <?php echo $global_setting_response['response'][0]['default_currency']?></span>
        
                  <!-- Progress -->
                  <div class="progress rounded-pill mb-2">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percent_complete?>%" aria-valuenow="<?php echo $percent_complete?>" aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top" title="Gross value"></div>
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $percent_pending?>%" aria-valuenow="<?php echo $percent_pending?>" aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top" title="Net volume from sales"></div>
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $percent_refunded?>%" aria-valuenow="<?php echo $percent_refunded?>" aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top" title="New volume from sales"></div>
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $percent_failed?>%" aria-valuenow="<?php echo $percent_failed?>" aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top" title="New volume from sales"></div>
                  </div>
        
                  <div class="d-flex justify-content-between mb-4">
                    <span>0%</span>
                    <span>100%</span>
                  </div>
                  <!-- End Progress -->
        
                  <!-- Table -->
                  <div class="table-responsive">
                    <table class="table table-lg table-nowrap card-table mb-0">
                      <tr>
                        <th scope="row">
                          <span class="legend-indicator bg-primary"></span>Completed
                        </th>
                        <td><?php echo $global_setting_response['response'][0]['currency_symbol'].' '.number_format($total_report_overview_complete, 2);?></td>
                      </tr>
        
                      <tr>
                        <th scope="row">
                          <span class="legend-indicator bg-warning"></span>Pending
                        </th>
                        <td><?php echo $global_setting_response['response'][0]['currency_symbol'].' '.number_format($total_report_overview_pending, 2);?></td>
                      </tr>
        
                      <tr>
                        <th scope="row">
                          <span class="legend-indicator bg-warning"></span>Refunded
                        </th>
                        <td><?php echo $global_setting_response['response'][0]['currency_symbol'].' '.number_format($total_report_overview_refunded, 2);?></td>
                      </tr>
        
                      <tr>
                        <th scope="row">
                          <span class="legend-indicator bg-danger"></span>Failed
                        </th>
                        <td><?php echo $global_setting_response['response'][0]['currency_symbol'].' '.number_format($total_report_overview_failed, 2);?></td>
                      </tr>
                    </table>
                  </div>
                  <!-- End Table -->
                </div>
                <!-- End Body -->
              </div>
              <!-- End Card -->
            </div>
          </div>
            
            
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
                  <a class="btn btn-primary btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-approved" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-approved', 'approved')">
                    <i class="bi-check-circle"></i> Approved
                  </a>
                  <a class="btn btn-success btn-sm mb-2 mb-sm-0 me-2 btn-bulk-action-send-ipn" href="javascript:void(0)" onclick="bulk_action('btn-bulk-action-send-ipn', 'send-ipn')">
                    <i class="bi-send"></i> Send IPN
                  </a>
                </div>
              </div>
              
              <span class="response-bulk-action"></span>
            </div>
          </div>
            
          <div class="card mb-lg-5">
            <div class="card-header">
              <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-md">
                  <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-header-title">Latest Transaction</h4>
                  </div>
                </div>
                <!-- End Col -->
        
                <div class="col-auto">
                  <!-- Filter -->
                  <div class="row align-items-sm-center">
                    <div class="col-sm-auto">
                      <div class="row align-items-center gx-0">
                        <div class="col">
                          <span class="text-secondary me-2">Status:</span>
                        </div>
                        <!-- End Col -->
        
                        <div class="col-auto">
                          <!-- Select -->
                          <div class="tom-select-custom tom-select-custom-end">
                            <select class="js-select form-select form-select-sm form-select-borderless transaction_status" data-hs-tom-select-options='{
                                      "searchInDropdown": false,
                                      "hideSearch": true,
                                      "dropdownWidth": "10rem"
                                    }' onchange="loadDataTable()">
                              <option value="all" selected>All</option>
                              <option value="completed">Completed</option>
                              <option value="pending">Pending</option>
                              <option value="refunded">Refunded</option>
                              <option value="failed">Failed</option>
                            </select>
                          </div>
                          <!-- End Select -->
                        </div>
                        <!-- End Col -->
                      </div>
                      <!-- End Row -->
                    </div>
                    <!-- End Col -->
        
                    <div class="col-md">
                      <form>
                        <!-- Search -->
                        <div class="input-group input-group-merge input-group-flush">
                          <div class="input-group-prepend input-group-text">
                            <i class="bi-search"></i>
                          </div>
                          <input id="datatableSearch" type="search" class="form-control" placeholder="Search" aria-label="Search" onkeyup="loadDataTable()">
                        </div>
                        <!-- End Search -->
                      </form>
                    </div>
                    <!-- End Col -->
                  </div>
                  <!-- End Filter -->
                </div>
                <!-- End Col -->
              </div>
              <!-- End Row -->
            </div>
            <!-- End Header -->
    
            <!-- Table -->
            <div class="table-responsive datatable-custom">
              <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                  <tr>
                      <th scope="col" class="table-column-pe-0"><input type="checkbox" id="select-all" class="form-check-input"></th>
                      <th>Customer</th>
                      <th>Gateway</th>
                      <th>Amount</th>
                      <th>Sender</th>
                      <th>Transaction Id</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Action</th>
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
                        formData.append('action', 'bulk_action_transaction');
                        formData.append('action_name', action);
                        formData.append('ids', ids);
                        
                        $.ajax({
                            type: "POST",
                            url: "dashboard",
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
                                    load_content('Dashboard','dashboard','nav-btn-dashboard');
                                }
                            }
                        });
                    }
                }

                function loadDataTable(page = 1) {
                    var search = document.querySelector("#datatableSearch").value;
                    var transaction_status = document.querySelector(".transaction_status").value;
                    
                    const tbody = $("#datatable");
                    tbody.empty();
            
                     tbody.html(`              
                        <tr class="odd">
                            <td valign="top" colspan="8" class="dataTables_empty">
                                <div class="text-center p-4">
                                      <div class="spinner-border text-primary" role="status"> <span class="visually-hidden">Loading...</span> </div>
                                </div>
                            </td>
                        </tr>
                    `);
                    
                  $.ajax({
                    type: "POST",
                    url: "/admin/dashboard",
                    data: { action: "pp_transaction", visibility: "limited", page: page, search: search, transaction_status: transaction_status },
                    success: function (data) {
                        tbody.empty();
                        
                      const dedata = JSON.parse(data);
                
                      if (dedata.total === 0) {
                        tbody.html(`
                            <tr class="odd">
                                <td valign="top" colspan="8" class="dataTables_empty">
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
                          let url = 'view-transaction?ref=' + tx.id;
                            
                          tbody.append(`
                            <tr>
                              <td><input type="checkbox" class="form-check-input select-box" value="${tx.id || "--"}"></td>
                              <td onclick="load_content('View Transaction','${url}','nav-btn-transaction')">${tx.c_name || "--"}</td>
                              <td onclick="load_content('View Transaction','${url}','nav-btn-transaction')">${tx.payment_method || "--"}</td>
                              <td onclick="load_content('View Transaction','${url}','nav-btn-transaction')">${tx.transaction_amount || "--"}</td>
                              <td onclick="load_content('View Transaction','${url}','nav-btn-transaction')">${tx.sender || "--"}</td>
                              <td onclick="load_content('View Transaction','${url}','nav-btn-transaction')">${tx.transaction_id || "--"}</td>
                              <td onclick="load_content('View Transaction','${url}','nav-btn-transaction')">${tx.created_at || "--"}</td>
                              <td onclick="load_content('View Transaction','${url}','nav-btn-transaction')">
                                  ${(() => {
                                    switch (tx.transaction_status) {
                                      case 'completed':
                                        return '<span class="badge bg-primary">Completed</span>';
                                      case 'pending':
                                        return '<span class="badge bg-warning text-dark">Pending</span>';
                                      case 'failed':
                                        return '<span class="badge bg-danger">Failed</span>';
                                      case 'refunded':
                                        return '<span class="badge bg-warning text-dark">Refunded</span>';
                                      default:
                                        return '<span class="badge bg-dark">Unknown</span>';
                                    }
                                  })()}
                              </td>
                              <td onclick="load_content('View Transaction','${url}','nav-btn-transaction')">
                                  <div class="btn-group" role="group">
                                    <a class="btn btn-white btn-sm">
                                      <i class="bi-eye me-1"></i> View
                                    </a>
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
          
          
          
          
          
                function initialize111() {
                    new HSFormSearch('.js-form-search')
            
                    HSBsDropdown.init()
        
            
                    new HsNavScroller('.js-nav-scroller')
            
                    HSCore.components.HSDropzone.init('.js-dropzone')

            
                    HSBsDropdown.init()
            
                    HSCore.components.HSChartJS.init('.js-chart')
            
                    HSCore.components.HSChartJS.init('#updatingBarChart')
                    const updatingBarChart = HSCore.components.HSChartJS.getItem('updatingBarChart')
            
                    document.querySelectorAll('[data-bs-toggle="chart-bar"]').forEach(item => {
                      item.addEventListener('click', e => {
                        let keyDataset = e.currentTarget.getAttribute('data-datasets')
            
                        const styles = HSCore.components.HSChartJS.getTheme('updatingBarChart', HSThemeAppearance.getAppearance())
            
                        if (keyDataset === 'today') {
                            <?php
                                $chartJson = getChart($db_prefix.'transaction', 'created_at', 'today', null, null, 'transaction_status = "completed"');
                                $chartData = json_decode($chartJson, true);
                                
                                $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                $data = isset($chartData['data']) ? $chartData['data'] : [];
                
                                $labelsJson = json_encode($labels);
                                $dataJsoncompleted = json_encode($data);
                            ?>
                
                            <?php
                                $chartJson = getChart($db_prefix.'transaction', 'created_at', 'today', null, null, 'transaction_status = "pending"');
                                $chartData = json_decode($chartJson, true);
                                
                                $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                $data = isset($chartData['data']) ? $chartData['data'] : [];
                
                                $dataJsonpending = json_encode($data);
                            ?>
                            
                            <?php
                                $chartJson = getChart($db_prefix.'transaction', 'created_at', 'today', null, null, 'transaction_status = "refunded"');
                                $chartData = json_decode($chartJson, true);
                                
                                $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                $data = isset($chartData['data']) ? $chartData['data'] : [];
                
                                $dataJsonrefunded = json_encode($data);
                            ?>
                            
                            <?php
                                $chartJson = getChart($db_prefix.'transaction', 'created_at', 'today', null, null, 'transaction_status = "failed"');
                                $chartData = json_decode($chartJson, true);
                                
                                $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                $data = isset($chartData['data']) ? $chartData['data'] : [];
                
                                $dataJsonfailed = json_encode($data);
                            ?>
                                
                          updatingBarChart.data.labels = <?php echo $labelsJson?>;
                          updatingBarChart.data.datasets = [
                            {
                              "data": <?php echo $dataJsoncompleted?>,
                              "backgroundColor": "#3BB77E",
                              "hoverBackgroundColor": "#3BB77E",
                              "borderColor": "#3BB77E",
                              "maxBarThickness": "10"
                            },
                            {
                              "data": <?php echo $dataJsonpending?>,
                              "backgroundColor": "#f5ca99",
                              "borderColor": "#f5ca99",
                              "maxBarThickness": "10"
                            },
                            {
                              "data": <?php echo $dataJsonrefunded?>,
                              "backgroundColor": "#f5ca99",
                              "borderColor": "#f5ca99",
                              "maxBarThickness": "10"
                            },
                            {
                              "data": <?php echo $dataJsonfailed?>,
                              "backgroundColor": "#d63384",
                              "borderColor": "#d63384",
                              "maxBarThickness": "10"
                            }
                          ];
                          updatingBarChart.update();
                        } else {
                            if (keyDataset === 'monthly') {
                                <?php
                                    $chartJson = getChart($db_prefix.'transaction', 'created_at', 'monthly', null, null, 'transaction_status = "completed"');
                                    $chartData = json_decode($chartJson, true);
                                    
                                    $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                    $data = isset($chartData['data']) ? $chartData['data'] : [];
                    
                                    $labelsJson = json_encode($labels);
                                    $dataJsoncompleted = json_encode($data);
                                ?>
                    
                                <?php
                                    $chartJson = getChart($db_prefix.'transaction', 'created_at', 'monthly', null, null, 'transaction_status = "pending"');
                                    $chartData = json_decode($chartJson, true);
                                    
                                    $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                    $data = isset($chartData['data']) ? $chartData['data'] : [];
                    
                                    $dataJsonpending = json_encode($data);
                                ?>
                                
                                <?php
                                    $chartJson = getChart($db_prefix.'transaction', 'created_at', 'monthly', null, null, 'transaction_status = "refunded"');
                                    $chartData = json_decode($chartJson, true);
                                    
                                    $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                    $data = isset($chartData['data']) ? $chartData['data'] : [];
                    
                                    $dataJsonrefunded = json_encode($data);
                                ?>
                                
                                <?php
                                    $chartJson = getChart($db_prefix.'transaction', 'created_at', 'monthly', null, null, 'transaction_status = "failed"');
                                    $chartData = json_decode($chartJson, true);
                                    
                                    $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                    $data = isset($chartData['data']) ? $chartData['data'] : [];
                    
                                    $dataJsonfailed = json_encode($data);
                                ?>
                                    
                              updatingBarChart.data.labels = <?php echo $labelsJson?>;
                              updatingBarChart.data.datasets = [
                                {
                                  "data": <?php echo $dataJsoncompleted?>,
                                  "backgroundColor": "#3BB77E",
                                  "hoverBackgroundColor": "#3BB77E",
                                  "borderColor": "#3BB77E",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonpending?>,
                                  "backgroundColor": "#f5ca99",
                                  "borderColor": "#f5ca99",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonrefunded?>,
                                  "backgroundColor": "#f5ca99",
                                  "borderColor": "#f5ca99",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonfailed?>,
                                  "backgroundColor": "#d63384",
                                  "borderColor": "#d63384",
                                  "maxBarThickness": "10"
                                }
                              ];
                              updatingBarChart.update();
                          } else {
                                <?php
                                    $chartJson = getChart($db_prefix.'transaction', 'created_at', 'yearly', null, null, 'transaction_status = "completed"');
                                    $chartData = json_decode($chartJson, true);
                                    
                                    $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                    $data = isset($chartData['data']) ? $chartData['data'] : [];
                    
                                    $labelsJson = json_encode($labels);
                                    $dataJsoncompleted = json_encode($data);
                                ?>
                    
                                <?php
                                    $chartJson = getChart($db_prefix.'transaction', 'created_at', 'yearly', null, null, 'transaction_status = "pending"');
                                    $chartData = json_decode($chartJson, true);
                                    
                                    $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                    $data = isset($chartData['data']) ? $chartData['data'] : [];
                    
                                    $dataJsonpending = json_encode($data);
                                ?>
                                
                                <?php
                                    $chartJson = getChart($db_prefix.'transaction', 'created_at', 'yearly', null, null, 'transaction_status = "refunded"');
                                    $chartData = json_decode($chartJson, true);
                                    
                                    $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                    $data = isset($chartData['data']) ? $chartData['data'] : [];
                    
                                    $dataJsonrefunded = json_encode($data);
                                ?>
                                
                                <?php
                                    $chartJson = getChart($db_prefix.'transaction', 'created_at', 'yearly', null, null, 'transaction_status = "failed"');
                                    $chartData = json_decode($chartJson, true);
                                    
                                    $labels = isset($chartData['labels']) ? $chartData['labels'] : [];
                                    $data = isset($chartData['data']) ? $chartData['data'] : [];
                    
                                    $dataJsonfailed = json_encode($data);
                                ?>
                                    
                              updatingBarChart.data.labels = <?php echo $labelsJson?>;
                              updatingBarChart.data.datasets = [
                                {
                                  "data": <?php echo $dataJsoncompleted?>,
                                  "backgroundColor": "#3BB77E",
                                  "hoverBackgroundColor": "#3BB77E",
                                  "borderColor": "#3BB77E",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonpending?>,
                                  "backgroundColor": "#f5ca99",
                                  "borderColor": "#f5ca99",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonrefunded?>,
                                  "backgroundColor": "#f5ca99",
                                  "borderColor": "#f5ca99",
                                  "maxBarThickness": "10"
                                },
                                {
                                  "data": <?php echo $dataJsonfailed?>,
                                  "backgroundColor": "#d63384",
                                  "borderColor": "#d63384",
                                  "maxBarThickness": "10"
                                }
                              ];
                              updatingBarChart.update();
                          }
                        }
                      })
                    })
            
                    HSCore.components.HSChartJS.init('.js-chart-datalabels', {
                      plugins: [ChartDataLabels],
                      options: {
                        plugins: {
                          datalabels: {
                            anchor: function (context) {
                              var value = context.dataset.data[context.dataIndex];
                              return value.r < 20 ? 'end' : 'center';
                            },
                            align: function (context) {
                              var value = context.dataset.data[context.dataIndex];
                              return value.r < 20 ? 'end' : 'center';
                            },
                            color: function (context) {
                              var value = context.dataset.data[context.dataIndex];
                              return value.r < 20 ? context.dataset.backgroundColor : context.dataset.color;
                            },
                            font: function (context) {
                              var value = context.dataset.data[context.dataIndex],
                                fontSize = 25;
            
                              if (value.r > 50) {
                                fontSize = 35;
                              }
            
                              if (value.r > 70) {
                                fontSize = 55;
                              }
            
                              return {
                                weight: 'lighter',
                                size: fontSize
                              };
                            },
                            formatter: function (value) {
                              return value.r
                            },
                            offset: 2,
                            padding: 0
                          }
                        },
                      }
                    })
            
                    HSCore.components.HSTomSelect.init('.js-select')
            
                    HSCore.components.HSClipboard.init('.js-clipboard')
              }
              
              initialize111();
              
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
                        formData.append('action', 'pp_bulk_action_transaction');
                        formData.append('action_name', action);
                        formData.append('ids', ids);
                        
                        $.ajax({
                            type: "POST",
                            url: "/admin/dashboard",
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
                                    load_content('Dashboard','dashboard','nav-btn-dashboard');
                                }
                            }
                        });
                    }
                }
          </script>
<?php
    }
?>