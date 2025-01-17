<main id="main">

    <!--==========================
      ConterDiv Section
    ============================-->

    <div class="main-div-sec">
      <div class="container">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
          <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
          <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
              class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
        </div>

        <!-- Content Row -->
        <div class="row">

          <!-- Earnings (Monthly) Card Example -->
          <div class="col mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Earnings (Monthly)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-calendar fa-2x text-gray-600"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Earnings (Monthly) Card Example -->
          <div class="col mb-4">
            <div class="card border-left-success shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Earnings (Annual)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">$215,000</div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-dollar-sign fa-2x text-gray-600"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col mb-4">
            <div class="card border-left-info shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks(Monthly)</div>
                    <div class="row no-gutters align-items-center">
                      <div class="col-auto">
                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"> 70%</div>
                      </div>
                     
                      <div class="col">
                                <div class="progress progress-sm mr-2">
                                  <div class="progress-bar bg-info" role="progressbar" style="width: 70%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                              </div>
                    </div>
                  </div>
                
                  <div class="col-auto">
                    <i class="fas fa-clipboard-list fa-2x text-gray-600"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Earnings (Monthly) Card Example -->
          <div class="col mb-4">
            <div class="card border-left-info shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks(Annual)</div>
                    <div class="row no-gutters align-items-center">
                      <div class="col-auto">
                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"> 50%</div>
                      </div>
                     
                      <div class="col">
                                <div class="progress progress-sm mr-2">
                                  <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                              </div>
                    </div>
                  </div>
                
                  <div class="col-auto">
                    <i class="fas fa-calculator fa-2x text-gray-600"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Requests Card Example -->
          <div class="col mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Requests</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-comments fa-2x text-gray-600"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Row -->

        <div class="row">

          <!-- Area Chart -->
          <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
              <!-- Card Header - Dropdown -->
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                <div class="dropdown no-arrow">
                  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Dropdown Header:</div>
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
              <!-- Card Body -->
              <div class="card-body">
                <div class="chart-area">
                  <canvas id="lineChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Pie Chart -->
          <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
              <!-- Card Header - Dropdown -->
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                <div class="dropdown no-arrow">
                  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Dropdown Header:</div>
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
              <!-- Card Body -->
              <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                  <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                  <span class="mr-2">
                    <i class="fas fa-circle text-primary"></i> Offer
                  </span>
                  <span class="mr-2">
                    <i class="fas fa-circle text-success"></i> Key
                  </span>
                  <span class="mr-2">
                    <i class="fas fa-circle text-info"></i> Micro Key
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Row -->
        <div class="row">

          <!-- Content Column -->
          <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary" style="float:left;">Projects</h6>
                <a href="http://www.drivedigitally.com/hirenworknew/job-list/completed" style="float:right;">View
                  All</a>
              </div>
              <div class="card-body">

                <div class="projects" data-task_id>
                  <ul class="list-group list-group-flush text-gray-800">
                    <li class="list-group-item">Developer need for wp project</li>
                    <li class="list-group-item"> ci developer need</li>
                    <li class="list-group-item">ci develper 20th Aug</li>
                    <li class="list-group-item"> ci developer need</li>
                    <li class="list-group-item ">ui developer</li>
                  </ul>



                  <div class="ongoing-task-rht">
                    <div class="input-group-sec mr-0 float-right">
                      <div class="input-group">
                        <div class="input-group-btn">
                          <a href='Freelancer/see_all_projects' class="btn btn-default p-2 text-primary font-weight-bold">See More</a>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <!-- Project Card Example -->
            <!-- <div class="card shadow mb-4">
                      <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Projects</h6>
                      </div>
                      <div class="card-body">
                        <h4 class="small font-weight-bold">Server Migration <span class="float-right">20%</span></h4>
                        <div class="progress mb-4">
                          <div class="progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Sales Tracking <span class="float-right">40%</span></h4>
                        <div class="progress mb-4">
                          <div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Customer Database <span class="float-right">60%</span></h4>
                        <div class="progress mb-4">
                          <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Payout Details <span class="float-right">80%</span></h4>
                        <div class="progress mb-4">
                          <div class="progress-bar bg-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <h4 class="small font-weight-bold">Account Setup <span class="float-right">Complete!</span></h4>
                        <div class="progress">
                          <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                      </div>
                    </div> -->

            <!-- Color System -->
            <!-- <div class="row">
                      <div class="col-lg-6 mb-4">
                        <div class="card bg-primary text-white shadow">
                          <div class="card-body">
                            Primary
                            <div class="text-white-50 small">#4e73df</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 mb-4">
                        <div class="card bg-success text-white shadow">
                          <div class="card-body">
                            Success
                            <div class="text-white-50 small">#1cc88a</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 mb-4">
                        <div class="card bg-info text-white shadow">
                          <div class="card-body">
                            Info
                            <div class="text-white-50 small">#36b9cc</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 mb-4">
                        <div class="card bg-warning text-white shadow">
                          <div class="card-body">
                            Warning
                            <div class="text-white-50 small">#f6c23e</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 mb-4">
                        <div class="card bg-danger text-white shadow">
                          <div class="card-body">
                            Danger
                            <div class="text-white-50 small">#e74a3b</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 mb-4">
                        <div class="card bg-secondary text-white shadow">
                          <div class="card-body">
                            Secondary
                            <div class="text-white-50 small">#858796</div>
                          </div>
                        </div>
                      </div>
                    </div> -->

          </div>

          <div class="col-lg-6 mb-4">

            <!-- Illustrations -->
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Illustrations</h6>
              </div>
              <div class="card-body">
                <div class="text-center">
                  <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="img/undraw_posting_photo.svg"
                    alt="">
                </div>


              </div>
            </div>

            <!-- Approach -->
            <!-- <div class="card shadow mb-4">
                      <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Development Approach</h6>
                      </div>
                      <div class="card-body">
                        <p>Hire N Work makes extensive use of Bootstrap 4 utility classes in order to reduce CSS bloat and poor page performance. Custom CSS classes are used to create custom components and custom utility classes.</p>
                        <p class="mb-0">Before working with this theme, you should become familiar with the Bootstrap framework, especially the utility classes.</p>
                      </div>
                    </div> -->

          </div>
        </div>

      </div>
    </div>
  </main>
	
    <script type="text/javascript" language="javascript">
      console.log(task_ids);

  $(".projects").mouseover(function(){
    for (var i = 0; i < task_ids.length; i++) {
      $('.illustrations'+task_ids[i]+'').attr('style', 'display: none;');
    }
    var task_id = $(this).data('task_id');
    $('.illustrations'+task_id+'').attr('style', 'display: block;');
  });
    </script>
	   <script src="<?php echo base_url() ?>assets/js/Chart.min.js"></script>

        <!-- Page level custom scripts -->
         <script src="<?php echo base_url() ?>assets/js/chart-area-demo.js"></script>  
     <!--   <script src="<?php echo base_url() ?>assets/js/chart-pie-demo.js"></script> -->
		
		<script>
		
// Area Chart Example
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    datasets: [{
      label: "Earnings",
      lineTension: 0.3,
      backgroundColor: "rgba(78, 115, 223, 0.05)",
      borderColor: "rgba(78, 115, 223, 1)",
      pointRadius: 3,
      pointBackgroundColor: "rgba(78, 115, 223, 1)",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
      data: [<?php echo $month['January'] ?>, <?php echo $month['February'] ?>, <?php echo $month['March'] ?>, <?php echo $month['April'] ?>, <?php echo $month['May'] ?>, <?php echo $month['June'] ?>, <?php echo $month['July'] ?>, <?php echo $month['August'] ?>, <?php echo $month['September'] ?>, <?php echo $month['October'] ?>, <?php echo $month['November'] ?>, <?php echo $month['December'] ?>],
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          // Include a dollar sign in the ticks
          callback: function(value, index, values) {
            return '$' + number_format(value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
        }
      }
    }
  }
});


// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ["Direct", "Referral", "Microkey Projects"],
    datasets: [{
      data: [<?php echo $total_offer ?>, <?php echo $total_referral ?>, <?php echo $total_microkey_projects ?>],
      backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
      hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: false
    },
    cutoutPercentage: 80,
  },
});

		
		
		</script>
		
		
		<script type='text/javascript'>
$(document).ready(function(){
    $('.web').mouseover(function() {
	 
    $('.illusimg').attr ("src", 'https://img-a.udemycdn.com/course/750x422/576054_7e88_6.jpg');

	});
	
	$('.cms').mouseover(function() {
	 
    $('.illusimg').attr ("src", 'https://www.secret-source.eu/wp-content/uploads/2017/08/best-content-management-systems-business.png');

	});
	
	
	$('.php').mouseover(function() {
	 
    $('.illusimg').attr ("src", 'https://miro.medium.com/max/900/1*j2GtIrbQBiYiAwBGXuBCVw.png');

	});
	
	
	$('.graphics').mouseover(function() {
	 
    $('.illusimg').attr ("src", 'https://www.iquorsolutions.com/images/services/graphic/graphic-banner.jpg');

	});
});

</script>
