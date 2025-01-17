<!-- Content Wrapper. Contains page content -->
<link href="<?php echo base_url()?>/assets/admin/js_css_admin/editor.css" type="text/css" rel="stylesheet"/>
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-sm-6">
               <h1 class="m-0 text-dark">Problem Ticket Details</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
               <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="<?= base_url().'admin/dashboard'?>">Home</a></li>
                  <li class="breadcrumb-item active">Problem Ticket Details</li>
               </ol>
            </div>
            <!-- /.col -->
         </div>
         <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
   </div>
   <!-- /.content-header -->
   <!-- Main content -->
   <section class="content">
      <div class="container-fluid">
         <!-- Small boxes (Stat box) -->
         <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
         <div class="container padding-bottom-3x mb-2">
            <div class="row">
				  <div class="col-lg-12">
				  <?php 
				  if($this->session->flashdata('msg')){
					  echo $this->session->flashdata('msg');
				  } 
				              $user_profile_image = $user_details->profile_image;
						if(empty($user_profile_image)) {
							$user_profile_image = base_url('assets/img/no-image.png');
						}
						else {
							$user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
						}           
				  
				  ?>
				  
				  </div>
               <div class="col-lg-4">
                  <aside class="user-info-wrapper">
                     <div class="user-cover" style="background-image: url(https://bootdey.com/img/Content/bg1.jpg);">
                        <div class="info-label" data-toggle="tooltip" title="" data-original-title="You currently have 290 Reward Points to spend"><i class="icon-medal"></i> Ticket No <?php echo isset($user_details->problem_ticket_no) ? $user_details->problem_ticket_no : '';?></div>
                     </div>
                     <div class="user-info">
                        <div class="user-avatar">
                           <a class="edit-avatar" href="#"></a><img src="<?php echo $user_profile_image ;?>">
                        </div>
                        <div class="user-data">
                           <h4><?php echo isset($user_details->name) ? $user_details->name : '';?></h4>
                         <!--   <span>Joined <?php //echo date("F j, Y",strtotime($user_details->created)); ?></span> -->

                            <?php $country_name=$this->db->get_where('country',array('country_id'=>$user_details->country))->row();
                            $username_name=$this->db->get_where('user_login',array('user_id'=>$user_details->user_id))->row();
                            ?>
                            
                              <span>Country: <?=$country_name->name?></span> 
                           <a class="list-group-item" href="<?php echo base_url()?>User/admin_profile_vistor/<?=$user_details->user_id ?>/<?=$user_type='4'?>"><i class="fa fa-user"></i>View  Profile</a>
                        </div>
                     </div>
                  </aside>
                  <!-- <nav class="list-group"><a class="list-group-item with-badge" href="#"><i class="fa fa-th"></i>Jobs<span class="badge badge-primary badge-pill">6</span></a>
                     <a class="list-group-item" href="#"><i class="fa fa-user"></i>Profile</a>
                     <a class="list-group-item" href="#"><i class="fa fa-map"></i>Addresses</a>
                     <a class="list-group-item with-badge" href="#"><i class="fa fa-heart"></i>Wishlist<span class="badge badge-primary badge-pill">3</span></a>
                     <a class="list-group-item with-badge active" href="#"><i class="fa fa-tag"></i>My Tickets<span class="badge badge-primary badge-pill"><?php echo count($info);?></span></a>
                  </nav> -->
               </div>
               <div class="col-lg-8">
                  <div class="padding-top-2x mt-2 hidden-lg-up"></div>
                  <div class="table-responsive margin-bottom-2x">
                     <table class="table margin-bottom-none">
                        <thead>
                           <tr>
                              <th>Date Submitted</th>
                              <th>Last Updated</th>
                              <th>Type</th>
                              <th>Priority</th>
                              <th>Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><?php echo date("d/m/Y",strtotime($user_details->doc)); ?></td>
                              <td>08/14/2017</td>
                              <td><?php echo isset($info[0]->grievance_type) ? $info[0]->grievance_type : '';?></td>
                              <td><span class="text-warning">High</span></td>
                              <td><span class="text-primary"><?php echo isset($user_details->problem_status) ? $user_details->problem_status : 'Open';?></span></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <!-- Messages-->

                  <div class="comment">
                     <div class="comment-author-ava"><img src="<?php echo $user_profile_image; ?>" alt="Avatar"></div>
                     <div class="comment-body">
                        <p class="comment-text"><?php echo $user_details->grievance_content ;?></p>
                        <div class="comment-footer"><span class="comment-meta"><?php echo $user_details->name; ?></span> <span class="comment-meta">   (<?php echo date("F j, Y", strtotime($user_details->dom)) ;?>)<span></div>
                     </div>
                  </div>
                  <?php  if(!empty($info)){ $vcount = count($info); $vcount2 = 0;
					//echo "<pre>";print_r($info);die; 
                        foreach($info as $information) { $vcount2++;	?>
                  <div class="comment <?php echo ($vcount == $vcount2) ? "last_comment" : "" ?>">
                     <div class="comment-author-ava"><img src="<?php echo ($information->user_type=='outbox')?$user_profile_image:base_url('assets/img/logo.png')?>" alt="Avatar"></div>
                     <div class="comment-body">
                        <p class="comment-text"><?php echo $information->email_body ;?></p>
                        <div class="comment-footer"><span class="comment-meta"><?php echo ($information->user_type=='outbox')?$user_details->name:$this->session->userdata('user_name'); ?></span> <span class="comment-meta">   (<?php echo date("F j, Y", strtotime($information->dom)) ;?>)<span></div>
                     </div>
                  </div>
				   <?php }}else{ ?>
				   
						 	  <div class="comment"><div class="comment-body">
                        <p class="comment-text"><div class="" style="text-align: center;padding: 50px;">No message list found</div></p>

                     </div></div>

						 <?php } ?> 	

                  
               </div>
               <div class="col-lg-12">
                  <!-- Reply Form-->
                  <div class="card card-info card-outline">
                     <div class="card-header">Compose Email</div>
                     <div class="card-body">
                        <div class="container-fluid">
                           <div class="middle overViewPage">
                              <div class="container-fluid">
                                 <!-- <h2 class="pageTitle"><strong>Compose</strong></h2> -->
                                 <div class="row">
                                    <div class="col-md-12">
                                       <!-- <h4>Compose</h4> -->
                                       <form class="form-horizontal" action="<?= base_url()?>admin/send_email" method="POST" id="grievance_email_form">
                                          <input type="hidden" name="problem_ticket_no" id="problem_ticket_no" value="<?php echo isset($user_details->problem_ticket_no) ? $user_details->problem_ticket_no : '';?>" />
                                          <input type="hidden" name="from" id="from" value="<?= isset($from) ? $from : '' ?>" />
                                          <input type="hidden" name="user_id" id="user_id" value="<?= isset($user_id) ? $user_id : '' ?>" />
                                          <div class="form-group">
                                             <label for="email" class="col-sm-2 control-label">To:</label>
                                             <div class="col-sm-10">
                                                <input type="email" name="email_to" required="" class="form-control" id="email" placeholder="Email" value="<?= isset($send_mail) ? $send_mail : ''  ?>">
                                             </div>
                                          </div>
                                          <!--                            <div class="form-group">
                                             <label for="carbonCopy" class="col-sm-2 control-label">CC:</label>
                                             <div class="col-sm-10">
                                               <input type="email" class="form-control" id="carbonCopy" placeholder="Carbon copy addresses..." name="cc">
                                             </div>
                                             </div>
                                             <div class="form-group">
                                             <label for="blindCarbonCopy" class="col-sm-2 control-label">BCC:</label>
                                             <div class="col-sm-10">
                                               <input type="email" class="form-control" id="blindCarbonCopy" placeholder="Blind carbon copy addresses..." name="bcc">
                                             </div>
                                             </div>-->
                                          <!--<div class="form-group">
                                             <label for="sentFrom" class="col-sm-2 control-label">Send from:</label>
                                             <div class="col-sm-10">
                                               <input type="email" class="form-control" name="email_from" id="sentFrom" placeholder="Send from addresses..." required="">
                                             </div>
                                             </div>-->
                                          <div class="form-group">
                                             <label for="emailSubject" class="col-sm-2 control-label">Subject:</label>
                                             <div class="col-sm-10">
                                                <input type="text" class="form-control" id="emailSubject" placeholder="Subject of email..." name="email_subject" required="">
                                             </div>
                                          </div>
                                          <div class="form-group">
                                             <label for="emailBody" class="col-sm-2 control-label">Email body:</label>
                                             <div class="col-sm-10">
                                                <textarea id="emailBody" name="email_body" class="form-control" rows="20" placeholder="Message..."></textarea>
                                             </div>
                                          </div>
                                          <div class="form-group">
                                             <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" id="grievance_email_submit" class="btn btn-default">Send</button>
                                             </div>
                                          </div>
                                       </form>
                                    </div>
                                 </div>
                                 <!--end .row -->
                              </div>
                              <!--end .container-fluid-->
                           </div>
                           <!--end .overViewPage-->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
   </section>
   <!-- /.content -->
</div>
<!-- /.content-wrapper sdasd-->

<script src="<?php echo base_url()?>/assets/admin/js_css_admin/editor.js"></script>
<script type="text/javascript" language="javascript">
  
</script>
  <script>
    $(function () {
	   $('html, body').animate({
        scrollTop: $(".last_comment").offset().top - 10
    }, 1000);
       
   });
   
   $(document).ready(function() {
      $("#emailBody").Editor();
   });
   </script>

<style>
   .user-info-wrapper {
   position: relative;
   width: 100%;
   margin-bottom: -1px;
   padding-top: 90px;
   padding-bottom: 30px;
   border: 1px solid #e1e7ec;
   border-top-left-radius: 7px;
   border-top-right-radius: 7px;
   overflow: hidden
   }
   .user-info-wrapper .user-cover {
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 120px;
   background-position: center;
   background-color: #f5f5f5;
   background-repeat: no-repeat;
   background-size: cover
   }
   .user-info-wrapper .user-cover .tooltip .tooltip-inner {
   width: 230px;
   max-width: 100%;
   padding: 10px 15px
   }
   .user-info-wrapper .info-label {
   display: block;
   position: absolute;
   top: 18px;
   right: 18px;
   height: 26px;
   padding: 0 12px;
   border-radius: 13px;
   background-color: #fff;
   color: #606975;
   font-size: 12px;
   line-height: 26px;
   box-shadow: 0 1px 5px 0 rgba(0, 0, 0, 0.18);
   cursor: pointer
   }
   .user-info-wrapper .info-label>i {
   display: inline-block;
   margin-right: 3px;
   font-size: 1.2em;
   vertical-align: middle
   }
   .user-info-wrapper .user-info {
   display: table;
   position: relative;
   width: 100%;
   padding: 0 18px;
   z-index: 5
   }
   .user-info-wrapper .user-info .user-avatar,
   .user-info-wrapper .user-info .user-data {
   display: table-cell;
   vertical-align: top
   }
   .user-info-wrapper .user-info .user-avatar {
   position: relative;
   width: 115px
   }
   .user-info-wrapper .user-info .user-avatar>img {
   display: block;
   width: 100%;
   border: 5px solid #fff;
   border-radius: 50%
   }
   .user-info-wrapper .user-info .user-avatar .edit-avatar {
   display: block;
   position: absolute;
   top: -2px;
   right: 2px;
   width: 36px;
   height: 36px;
   transition: opacity .3s;
   border-radius: 50%;
   background-color: #fff;
   color: #606975;
   line-height: 34px;
   box-shadow: 0 1px 5px 0 rgba(0, 0, 0, 0.2);
   cursor: pointer;
   opacity: 0;
   text-align: center;
   text-decoration: none
   }
   .user-info-wrapper .user-info .user-avatar .edit-avatar::before {
   font-family: feather;
   font-size: 17px;
   content: '\e058'
   }
   .user-info-wrapper .user-info .user-avatar:hover .edit-avatar {
   opacity: 1
   }
   .user-info-wrapper .user-info .user-data {
   padding-top: 48px;
   padding-left: 12px
   }
   .user-info-wrapper .user-info .user-data h4 {
   margin-bottom: 2px
   }
   .user-info-wrapper .user-info .user-data span {
   display: block;
   color: #9da9b9;
   font-size: 13px
   }
   .user-info-wrapper+.list-group .list-group-item:first-child {
   border-radius: 0
   }
   .user-info-wrapper+.list-group .list-group-item:first-child {
   border-radius: 0;
   }
   .list-group-item:first-child {
   border-top-left-radius: 7px;
   border-top-right-radius: 7px;
   }
   .list-group-item:first-child {
   border-top-left-radius: .25rem;
   border-top-right-radius: .25rem;
   }
   a.list-group-item {
   padding-top: .87rem;
   padding-bottom: .87rem;
   }
   a.list-group-item, .list-group-item-action {
   transition: all .25s;
   color: #606975;
   font-weight: 500;
   }
   .with-badge {
   position: relative;
   padding-right: 3.3rem;
   }
   .list-group-item {
   border-color: #e1e7ec;
   background-color: #fff;
   text-decoration: none;
   }
   .list-group-item {
   position: relative;
   display: block;
   padding: .75rem 1.25rem;
   margin-bottom: -1px;
   background-color: #fff;
   border: 1px solid rgba(0,0,0,0.125);
   }
   .badge.badge-primary {
   background-color: #0da9ef;
   }
   .with-badge .badge {
   position: absolute;
   top: 50%;
   right: 1.15rem;
   -webkit-transform: translateY(-50%);
   -ms-transform: translateY(-50%);
   transform: translateY(-50%);
   }
   .list-group-item i {
   margin-top: -4px;
   margin-right: 8px;
   font-size: 1.1em;
   }
   .comment {
   display: block;
   position: relative;
   margin-bottom: 30px;
   padding-left: 66px
   }
   .comment .comment-author-ava {
   display: block;
   position: absolute;
   top: 0;
   left: 0;
   width: 50px;
   border-radius: 50%;
   overflow: hidden;
   }
   .comment .comment-author-ava>img {
   display: block;
   width: 100%
   }
   .comment .comment-body {
   position: relative;
   padding: 24px;
   border: 1px solid #e1e7ec;
   border-radius: 7px;
   background-color: #fff
   }
   .comment .comment-body::after,
   .comment .comment-body::before {
   position: absolute;
   top: 12px;
   right: 100%;
   width: 0;
   height: 0;
   border: solid transparent;
   content: '';
   pointer-events: none
   }
   .comment .comment-body::after {
   border-width: 9px;
   border-color: transparent;
   border-right-color: #fff
   }
   .comment .comment-body::before {
   margin-top: -1px;
   border-width: 10px;
   border-color: transparent;
   border-right-color: #e1e7ec
   }
   .comment .comment-title {
   margin-bottom: 8px;
   color: #606975;
   font-size: 14px;
   font-weight: 500
   }
   .comment .comment-text {
   margin-bottom: 12px
   }
   .comment .comment-footer {
   display: table;
   width: 100%
   }
   .comment .comment-footer>.column {
   display: table-cell;
   vertical-align: middle
   }
   .comment .comment-footer>.column:last-child {
   text-align: right
   }
   .comment .comment-meta {
   color: #9da9b9;
   font-size: 13px
   }
   .comment .reply-link {
   transition: color .3s;
   color: #606975;
   font-size: 14px;
   font-weight: 500;
   letter-spacing: .07em;
   text-transform: uppercase;
   text-decoration: none
   }
   .comment .reply-link>i {
   display: inline-block;
   margin-top: -3px;
   margin-right: 4px;
   vertical-align: middle
   }
   .comment .reply-link:hover {
   color: #0da9ef
   }
   .comment.comment-reply {
   margin-top: 30px;
   margin-bottom: 0
   }
   @media (max-width: 576px) {
   .comment {
	   padding-left: 0
	   }
	   .comment .comment-author-ava {
	   display: none
	   }
	   .comment .comment-body {
	   padding: 15px
	   }
	   .comment .comment-body::before,
	   .comment .comment-body::after {
	   display: none
		}
   }
</style>