<?php 
$query_solved=$this->db->query("SELECT * FROM user_grievance WHERE problem_status='solved'");
$solved=$query_solved->result();
/***** Unactive Freelancers ************/
$query_unactive_freelancers=$this->db->query("SELECT * FROM user_login WHERE profile_status='0' AND user_type='4'");
$unactiveFreelancer=$query_unactive_freelancers->result();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>assets/css/jquery.notify.css">
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo isset($client_count) ? $client_count : "0"; ?></h3>

                <p>Clients</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-tie"></i>
              </div>
              <a href="<?php echo base_url().'admin/client-list' ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo isset($freelancer_count) ? $freelancer_count : "0"; ?></h3>
                <p>Freelancers</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="<?php echo base_url().'admin/freelancer-list' ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo isset($naluacer_count) ? $naluacer_count : "0"; ?></h3>
                <p>Naluacers</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="<?php echo base_url().'admin/naluacer-list' ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
         <!--  <div class="col-lg-3 col-6">
            small box -->
            <!-- <div class="small-box bg-success">
              <div class="inner">
                <h3><?php //echo count($solved); ?></h3>
                <p>Problem Solved Tickets</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="<?php //echo base_url().'admin/problem-ticket/solved' ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div> -->
      
       <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo isset($problem_ticket_count) ? $problem_ticket_count : "0"; ?></h3>
                <p>Problem Unsolved Tickets</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="<?php echo base_url().'admin/problem-ticket/unsolved' ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
      
      <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo count($unactiveFreelancer); ?></h3>
                <p>Inactive Freelancers</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="<?php echo base_url().'admin/freelancer-list/inactive' ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <div class="row"></div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  <!--  <div id="alert_popover" class="text-right" style="width: 254px;height: 170px;margin-left: 50%;border: 1px solid;background: gray; color: white;">
  
    
    <button style="color:red;border: 2px solid red;background: black;">&times;</button>

    <div class="wrapper">
      
     <div id="notification_content_Freelancer"  style=" margin-top: 3%;margin-right: 50%;">

     </div>
      <div id="notification_content_Client"  style=" margin-top: 3%; margin-right: 50%;">

     </div>
      <div id="notification_content_Room"  style="margin-top: 3%; margin-right: 50%;">

     </div>
      <div id="notification_content_Ticket" style=" margin-top: 3%;margin-right: 50%;">

     </div>
    </div>
    </div> -->
    
    <div class="notify" data-notify-type="notice">
     <a href="<?php echo base_url()?>admin/freelancer-list" style="color: white;">Freelancer (<b><?php echo count_inactive_freelance(); ?></b>)</a>
   </div>
<div class="notify" data-notify-type="notice">
  <a href="<?php echo base_url()?>admin/client-list" style="color: white;">Client (<b><?php echo count_inactive_client(); ?></b>)</a>
</div>
<div class="notify" data-notify-type="notice">
  <a href="<?= base_url()?>admin/naluacer-lists/client" style="color: white;" >Room (<b><?php echo count_inactive_room(); ?></b>)</a>
</div>
<div class="notify" data-notify-type="notice">
  <a href="<?= base_url() ?>" style="color: white;">Ticket (<b><?php echo count_unsolved_ticket(); ?></b>)</a>
</div>
    <script src="<?php echo base_url()?>assets/jquery.notify.js"></script>
    <script type="text/javascript"><!--
google_ad_client = "ca-pub-2783044520727903";
/* jQuery_demo */
google_ad_slot = "2780937993";
google_ad_width = 1728;
google_ad_height = 190;
//-->
</script>
  <!-- /.content-wrapper -->
  <script type="text/javascript">
  


$(document).ready(function(){
  $("button").click(function(){
    $("#alert_popover").css({"display": "none"});
  });
});

  </script>

  <script>
$.notifySetup({sound: 'https://www.drivedigitally.com/live/assets/audio/notify.wav'});
$('.notify').notify();
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

$(function() {
    function loadCount() {
        count.load(
            "<?php echo $vars['url']; ?>mod/notifications/ajax/livenum.php"
        );
    }

    // Load on page load (call the function loadCount):
    loadCount()

    // Set the refresh interval and call the function loadCount every 60 seconds):
    var refreshId = setInterval(loadCount, 60000);
    $.ajaxSetup({ cache: false });
});


</script>
  <!-- /.content-wrapper -->