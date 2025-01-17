<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<main id="main"> 
  
  <?php 
  $msg = $this->session->flashdata('msg'); 
  if(!empty($msg)) {
  ?>
  <section style="padding-top: 7%;">
    <?php echo $msg; ?>
  </section>
  <?php
  }
  ?>

  <?php 
  $frmValidationMsg = validation_errors(); 
  if(!empty($frmValidationMsg)) {
  ?>
  <section style="padding-top: 7%;">
    <?php echo '<div class="alert alert-danger text-center">' . $frmValidationMsg . '</div>'; ?>
  </section>
  <?php
  }
  ?>

  <!--==========================
      ConterDiv Section
    ============================-->
  <form action="<?php echo base_url(); ?>Task/send_offer_to_user" name="frmSendOffer" method="post">
  <section id="postDiv">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
          <div class="task_Left">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Make offer</li>
              </ol>
            </nav>
            <div class="make-in-offer">
              <div class="bod-sec">
                <h2> Frelancer </h2>
                {freelancerInfo}
                <div class="img2-ses">
                  <input type="hidden" name="arrSelectedFreelancer[]" value="{freelancer_id}" />
                  <span> 
                    <img src="{user_image}" alt="{freelancer_name}" style="width:69px;height:69px;">{is_online}
                  </span>
                  <div class="caption">
                    <h3> {freelancer_name} </h3>
                    <p> {freelancer_city}, {freelancer_state}, {freelancer_country} </p>
                     <small> {total_positive_coins} Coins </small> 
                     <small> {total_negative_coins} Coins </small> 
                  </div>
                </div>
                {/freelancerInfo}
              </div>
            </div>
            
			<div class="postDiv_Box postDiv_Box2">
                <div class="step_Box">
                  <h3>Details</h3>
                  <ul>
                    <li> 
                      <span>
                        <label>Title</label>
                        <!--<input class="form-control" type="text" name="fldJobTitle" id="fldJobTitle" readonly />-->
                        <div class="select-style">
                          <select name="fldJobTitle" id="fldJobTitle" required>
                            <option value="">Select Post</option>
							<?php print_r($jobs); ?>
                            {jobs}
                            <option value="{user_task_id}">{task_name} (${task_total_budget})</option>
                            {/jobs}
							<option value="new_post">Add New Post</option>
                          </select>
                        </div>                      
                      </span> 
                      <span id="secRequirements">
                        <label>Skill Required</label>
                        <!--<select class="multipleSelect" name="fldSpecialities" id="fldSpecialities" multiple>
                          {skills}
                          <option value="{key}" {currentselection}>{value}</option>
                          {/skills}                          
                        </select>-->
                        <div class="task_Left_Div_3">
                          <span id="skill_list"><!--<a href="#">ActionScript</a><a href="#">Ada</a><a href="#">ALGOL</a><a href="#">C#</a>--></span>
                        </div>
                      </span>
                    </li>
                    <li>
                      <label>Description</label>
                      <textarea class="form-control" rows="7" cols="" name="fldJobDescription" id="fldJobDescription" style="border: 1px solid #dfdfdf !important;" readonly ></textarea>
                    </li>
                  </ul>
                  
                  <!--user post text -wrap end-->
                  <ul class="clearfix">
                    <label> Attachment </label>
                    <!--<li class="myupload"> 
                      <span><i class="fa fa-plus" aria-hidden="true"></i>
                      <input type="file" click-type="type2" id="picupload" class="picupload" multiple="">
                      </span> 
                    </li>-->
                    <li>
                      <span id="listAttachments"></span>
                    </li>
                  </ul>
                </div>
            </div>
            
			<div class="postDiv_Box" style="margin-top:20px;">
              <div class="postDiv_BoxFrm">
                <h3 class="p-0">Budget</h3>
                <ul>
                  <li>
                    <label>Estimated Budget</label>
                    <div class="input-group amt"> <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                      <input class="form-control" type="text" name="fldJobBudget" id="fldJobBudget" readonly />
                    </div>
                  </li>
                </ul>
                <!--<div class="row">
                  <div class="col-lg-4 col-sm-6 col-12">
                    <div class="planBox">
                      <h3><span id="grossBudget">$0</span></h3>
                      <p>Total Price of project</p>
                      <em>This includes all milestones.</em> </div>
                  </div>
                  <div class="col-lg-4 col-sm-6 col-12">
                    <div class="planBox">
                      <h3><span id="serviceCharges">$0</span></h3>
                      <p>15% Hire-n-Work service fee </p>
                    </div>
                  </div>
                  <div class="col-lg-4 col-sm-6 col-12">
                    <div class="planBox">
                      <h3><span id="netBudget">$0</span></h3>
                      <p>You will pay</p>
                    </div>
                  </div>
                </div>-->
              </div>
            </div>
			
			<div class="postDiv_Box" style="margin-top:20px;">
                <div class="step_Box step_Box2">
                  <h3>Location</h3>
                  <ul>
                    <li>
                      <label>Select Continent</label>
                      <div class="select-style" id="listContinent">
                        <select id="fldSelContinent" disabled>
                          <option value="">Select Continent</option>
                          {continents}
                          <option value="{key}" {currentselection}>{value}</option>
                          {/continents}                           
                        </select>
                      </div>
                    </li>
                    <li>
                      <h4>Date</h4>
                      <label>Due date</label>
                      <!--<div id="datepicker" class="input-group date" data-date-format="mm-dd-yyyy">-->
                        <input class="form-control" type="text" id="fldTaskDueDate" style="border: 1px solid #dfdfdf !important;" readonly />
                        <!--<span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                      </div>-->
                    </li>
                    <li>
                      <label>Select Country</label>
                      <div class="select-style" id="listCountry">
                        <select id="fldSelCountry" disabled>
                          <option value="">Select Country</option>
                          {countries}
                          <option value="{key}" {currentselection}>{value}</option>
                          {/countries}                          
                        </select>
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="fullDiv_bottom">
                  <!--<button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#SentOffer"> Send Offer </button>-->
                  <button type="submit" class="btn btn-primary"> Send Offer </button>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  </form>
</main>

<!-- The Modal -->
<div class="modal CmnModal" id="SentOffer">
  <div class="modal-dialog">
    <div class="modal-content"> 
      <!-- Modal Header -->
      <div class="modal-header"> 
        <!--<h2>Budget</h2> -->
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body"> <img src="<?php echo base_url(); ?>assets/img/deliver-img.png" alt="">
        <h2>Congratulation your <br>
          offer has been sent to the freelancer</h2>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type.</p>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer"> <a href="#" class="Terms_Btn" data-dismiss="modal">Okey </a> </div>
    </div>
  </div>
</div>

<!-- script for auto complete multiselect --> 
<script src="<?php  echo base_url('assets/js/fastselect.standalone.js'); ?>"></script> 
<script>
  //$('.multipleSelect').fastselect();
</script> 


<script> 
  $(document).ready(function() { 
    $('#fldJobTitle').change(function() {      
      var postId = $(this).val();
     
      if(postId == '') {
        alert('Please select a job post for sending offer to freelancer.')
      }else {
        //alert('Call ajax.');
        $.ajax({
          method: "POST",
          url: "<?php echo base_url(); ?>Task/ajax_get_task_details",
          data: { task_id: postId }
        })
        .done(function( msg ) {
          var obj = jQuery.parseJSON(msg);
          //alert(obj.task_details[0].basic_info.task_due_date);
          if(obj.status == 1) {
            var skill_list = '';
            var files_list = '';            
            var gross_total = obj.task_details[0].basic_info.task_total_budget;
            //var service_fee = (gross_total * 0.15);
            //var net_total = parseFloat(gross_total) + parseFloat(service_fee);
			var service_fee = 0;
			var net_total = (gross_total);

            $('#fldTaskDueDate').val(obj.task_details[0].basic_info.task_due_date);
            $("textarea#fldJobDescription").val(obj.task_details[0].basic_info.task_details);
            $("#fldJobBudget").val(gross_total);

            $('#grossBudget').html('$' + gross_total);
            $('#serviceCharges').html('$' + service_fee.toFixed(2));
            //$('#netBudget').html('$' + net_total.toFixed(2));  
			$('#netBudget').html('$' + net_total);  
			
            $('select#fldSelContinent>option:eq('+obj.task_details[0].basic_info.task_origin_location+')').attr('selected', true);
            $('select#fldSelCountry>option:eq('+obj.task_details[0].basic_info.task_origin_country+')').attr('selected', true);    

            for (i in obj.task_details[0].task_attachments) {
              files_list = files_list + '<a href="<?php echo base_url(); ?>download_file/' + obj.task_details[0].task_attachments[i].file_name + '">' + obj.task_details[0].task_attachments[i].file_display_name + '</a><br/>';
            }    
            $('#listAttachments').html(files_list);                     

            for (i in obj.task_details[0].task_requirements) {
              skill_list = skill_list + '<a href="#">' + obj.task_details[0].task_requirements[i].skill_name + '</a>';
            }
            $('#skill_list').html(skill_list);
          }
          else{
            alert('Unable to fetch post data. Please try after sometime.');
          }
        });        
      }
    }); 
    
  }); 
  
  $('#fldJobTitle').change(function () {
     var optionSelected = $(this).find("option:selected");
     //var valueSelected  = optionSelected.val();
     var textSelected   = optionSelected.text();
	 if(textSelected=="Add New Post")
	 {
		 window.location.href = "<?php echo base_url(); ?>post-task-step-1";
	 }
 });
</script>     