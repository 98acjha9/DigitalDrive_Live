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
  <section id="signInDiv">
    <?php 
  $errmsg = $this->session->flashdata('errmsg'); 
  if(!empty($errmsg)) { echo '<div class="alert alert-danger text-center">' . $errmsg . '</div>'; }
  ?>
  
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="signInDiv">
            <div class="col-lg-5">
              <div class="signInFrm">
                <h3>Sign in to your account</h3>
                <form action="<?php echo base_url(); ?>sign-in" name="frmSignIn" method="post">
                  <!-- http://demoupdates.com/updates/nlaucer/html/dashboard.html -->
                  <div class="form-group">
                    <label for="email">Email Or Username</label>
                    <input type="text" class="form-control" id="fldEmail" name="fldEmail" placeholder="Enter your email or username here" required>
                  </div>
                  <div class="form-group">
                    <label for="pwd">Password:</label>
                    
        					<input type="password" class="form-control" id="fldPassword" name="fldPassword" placeholder="Enter Password" required>
        					<span toggle="#fldPassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>
					
					
                  </div>
                  <div class="form-group text-right"> <a href="<?php echo base_url(); ?>forgot-password">Forgot Password?</a> </div>
                  <button type="submit" class="btn btn-primary">Sign in</button>
                </form>
                <h4>Or Sign in with</h4>
                <div class="alink"> 
                  <!--<a href="#" class="facebookLink"><i class="fa fa-facebook"></i> Facebook</a>--> 
                  <a href="<?php echo base_url(); ?>google_login/login" class="googleLink"><i class="fa fa-google-plus"></i> Google +</a>  
                  <a href="<?php echo base_url(); ?>linkedin_login/login?oauth_init=1" class="linkedinLink"><i class="fa fa-linkedin"></i> Linkedin</a> 
                </div>
                <h2>Don't have any account <a href="<?php echo base_url(); ?>sign-up-as">Sign Up</a></h2>
              </div>
            </div>
            <div class="col-lg-7 d-none d-sm-none d-md-block">
              <div class="signInImg"> <img src="<?php echo base_url(); ?>assets/img/singIn.jpg" alt=""> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
