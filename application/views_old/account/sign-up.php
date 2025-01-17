<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
.skill span.selection {
    width: 100%;
}
.skill span.select2-search.select2-search--inline {
    float: none;
}
.skill span.select2-selection.select2-selection--multiple:last-child {
    margin-left: 0px;
    float: left;
    width: 100%;
    border-radius: 4px;
    height: 50px;
    color: #293134;
    font-size: 16px;
    font-weight: 400;
    margin-bottom: 25px;
    border: 1px solid #ccc;
}
.skill .select2-container--default .select2-selection--multiple .select2-selection__choice {
    display: inline-flex;
}
.skill span .select2-selection_choice_display {
    width: auto;
}
.skill span .select2-selection__choice__display {
    width: auto;
}
</style>
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
  
  <!--==========================
      ConterDiv Section
    ============================-->
  <section id="signInDiv">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="singUpDiv">

            <?php $frmValidationMsg = validation_errors(); 
            if(!empty($frmValidationMsg)) { ?>
            <section style="padding-top: 7%;">
              <?php echo '<div class="alert alert-danger text-center">' . $frmValidationMsg . '</div>'; ?>
            </section>
            <?php } ?>

            <h3>New to <span>Hire-n-Work</span>? Sign up below</h3>
            <form action="<?php echo base_url(); ?>confirm-sign-up" name="frmSignUp" id="frmSignUp" method="post">
              <!-- http://demoupdates.com/updates/nlaucer/html/confirmation.html -->
              <input type="hidden" name="fldUserType" value="{sign_up_as}" />
              <div class="form-group">
                <input type="text" class="form-control" id="fldName" name="fldName" placeholder="Enter Name" value="" required>
              </div>
              <div class="form-group">
                <input type="text" class="form-control" id="username" name="username" data-placement="top"  data-toggle="tooltip" title="Username will be unique , so choose ur username consciously" placeholder="Enter Username" value="" required>
              </div>
              <div class="form-group">
                <select id="fldCountry" name="fldCountry" value="" required>
                  <option value="">Select Country</option>
                  {countries} <option value="{key}">{value}</option> {/countries}                 
                </select>
              </div>
              <?php if($sign_up_as == 'freelancer'){ ?>              
                    <div class="form-group skill">
                      <select id="fldSkills" name="fldSkills[]" required="" multiple="" class="js-example-placeholder-multiple form-control" oninvalid="this.setCustomValidity('Enter upto 2 skills Here')" oninput="this.setCustomValidity('')">
                        {skills} 
                       <!--  <option value="{area_of_interest_id}">{name}</option> -->
                         <option value="{name}">{name}</option>
                        {/skills}
                      </select>
                    </div>
              <?php } ?>
              <div class="form-group"> 
                <span>
                  <input type="email" class="form-control" id="fldEmail" name="fldEmail" placeholder="Email Address" value="" required>
                </span> 
                <span>
                  <input type="password" class="form-control" id="fldPassword" name="fldPassword" data-placement="top" data-toggle="tooltip" title="Password should be, Capital, small, number & special character combination with 8 digit." placeholder="Enter Password" value="" required>
                </span> 
              </div>
			  
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="fldTnC" value="yes" required>I agree to the <a href="<?php echo base_url(); ?>terms-and-condition" target="_blank">Terms & Conditions</a>, <a href="<?php echo base_url(); ?>privacy-policy" target="_blank">Privacy Policy</a> 
                </label>
              </div>
              <button type="submit" name="btnSubmit" id="btnSubmit" class="btn btn-primary">Sign up</button>
            </form>
            <div class="withdiv"> 
            <h4>Or Sign in with</h4>
            </div>
            
            <div class="alink2"> 
              <!--<a href="#" class="facebookLink"><i class="fa fa-facebook"></i> Facebook</a>--> 
              <a href="#" class="googleLink"><i class="fa fa-google-plus"></i> Google +</a> 
              <a href="#" class="linkedinLink"><i class="fa fa-linkedin"></i> Linkedin</a> 
            </div>
            <h2>Already have an account <a href="<?php echo base_url(); ?>sign-in">Sign in</a></h2>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script type="text/javascript" language="javascript">
  $(".js-example-placeholder-multiple").select2({
    placeholder: "Select maximum 2 Skills",
    maximumSelectionLength: 2,
    tags: true
  });
    $(document).ready(function() { 
        $('[data-toggle="tooltip"]').tooltip();   
    });
</script>