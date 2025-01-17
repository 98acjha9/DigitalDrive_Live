<style type="text/css">
.chatDiv .searchDiv ul li a.nav-link.hasNewMessages{
  background: #46b4e73b;
}
.hasNoNewMessages .stat {
  display: none !important;
}
.hasNewMessages .txthldr h2{
  font-weight: bold;
  color: #000 !important;
}
</style>
<div class="chatDiv">
  <div class="searchDiv">
    <div class="inputGroup">
      <div class="input-group"> <span class="input-group-addon"> <img src="<?php  echo base_url('assets/img/search-icon.png'); ?>" alt="img"> </span>
        <input id="msg" type="text" class="form-control" name="msg" placeholder="Search...">
      </div>
    </div>
    <div class="ChatListPanel">
      <ul class="nav nav-tabs">
		{user_info} {frndlist}
		
		<li class="nav-item"> 
		  <a class="{active} {mclass}" href="<?php echo base_url('messages/{user_id}'); ?>">
			<div class="ImgHldrWrapper {is_login}">
			  <div class="imghldr" style="background:url(<?= base_url()?>{profile_image}) no-repeat center top; background-size:cover;">
			  </div>
			  <span class="stat">{totalmessage}</span>
			</div>
			<div class="txthldr">
				<h2>{name}</h2> <!--({task_name})
				<p>{address}, {city}, {state}, {country}</p>-->
			</div>
		  </a> 
		</li>
		{/frndlist} {/user_info} 
      </ul>
    </div>
  </div>
  <div class="right-chatDiv">
<?php 
  /*$msg = $this->session->flashdata('msg'); 
  if(!empty($msg)) {
?>
<section style="padding-top: 7%;">
<?php echo $msg; ?>
</section>
<?php
	}*/
?>
<?php 

	/*$frmValidationMsg = validation_errors(); 
	if(!empty($frmValidationMsg)) {
?>
	<section style="padding-top: 7%;">
		<?php echo '<div class="alert alert-danger text-center">' . $frmValidationMsg . '</div>'; ?>
	</section>
<?php
  }*/
?>
    <div class="tab-content">
      <div id="msg1" class="container tab-pane active">
        <div class="rht-chat">
          <div class="rht-chat-1">
            <div class="ImgHldrWrapper ActivePfl">
              <div class="caption" style="background:url({user_info} {user_image} {/user_info} ) no-repeat center top; background-size:cover;"></div>
              <span class="stat"></span> 
			</div>
            <h3><?php echo $this->session->userdata('user_name'); ?></h3>
            <h6>{user_info} {address} {/user_info}, {user_info} {city} {/user_info}, {user_info} {state} {/user_info}, {user_info} {country} {/user_info}</h6>
			<?php if($this->uri->segment(2) != '' && $this->session->userdata('user_type') == 3){?>
			<div class="btnDiv2">
			 <form name="frmMakeOffer" id="frmMakeOffer_<?php echo $this->uri->segment(2);?>" action="" method="post">
                          <input type="hidden" name="chkMakeOfferFreelancer" value="<?php echo $this->uri->segment(2);?>" />
                        </form>
			  <a href="#" data-formaction="<?php echo base_url(); ?>make-an-offer" data-formid="<?php echo $this->uri->segment(2);?>" class="view-btn1 makeoffer"> Make offer </a> 
              <a href="#" data-formaction="<?php echo base_url(); ?>hire-freelancer" data-formid="<?php echo $this->uri->segment(2);?>" class="view-btn2 directhire"> Hire </a> 
			</div>
			<?php } ?>
          </div>
          <!--<div class="rht-chat-2"> <span class="time"> 09-03 <em> </em> 10:26 AM </span> </div>--> 
        </div>
        <div class="main-chatdiv">
          <div class="ChatPanel">
            <div class="chatWrapper" id="divscroll"> 
              <!--chat 1 -->
			  {user_info}  {msghistory}
              <div class="chat-rht-sec {className}" id="res">
                <div class="chat-back-img"> <span style="background:url({profileImage}) no-repeat center top; background-size:cover;"></span>
                  <div class="chat-back-sec">
                    <div class="cap">
					 <p>{message_content}</p><a href="<?= base_url().'uploads/messages/' ?>{attachement}">{download}</a>
                    </div>
                    <div class="cap2">{date_time}</div>
                  </div>
                </div>
              </div>
              <!--chat 1 --> 
			  {/msghistory} {/user_info}
            </div>
          </div>
        </div>
      </div>	  
    </div>
	
	<?php
	if($this->uri->segment(2) != ''){
		?>
	<div class="ChatBtnDiv">
      <form class="Wrapper" action="<?= base_url('user/saveMsgData'); ?>" method="post" enctype="multipart/form-data">
        <input type="text" class="form-control" name="msg" id="msg" placeholder="Type here...">
		<input type="hidden" name="user_to" value="<?= $this->uri->segment(2) ?>">
        <input name="uploadFiles[]" type="file" />
        <input class="" name="" type="submit" value="Send">
      </form>
    </div>	
		
		<?php
	}else{
		?>
	<div class="ChatBtnDiv">Click on Name to chat with</div>	
		<?php
	}
	
	?>
    	
  </div>
</div>
<script> 
  $(document).ready(function() { 
    //$(".chat-rht-sec").scrollTop($(".cap").height());
	$('.makeoffer').click(function(event) {
      event.preventDefault();
      var actionUrl = $(this).data('formaction');
      var formId = $(this).data('formid');      

      //alert(actionUrl);
      //alert(formId);
      $('#frmMakeOffer_' + formId).attr('action', actionUrl);
      $('#frmMakeOffer_' + formId).submit();
    });

    $('.directhire').click(function(event) {
      event.preventDefault();
      var actionUrl = $(this).data('formaction');
      var formId = $(this).data('formid');      

      //alert(actionUrl);
      //alert(formId);
      $('#frmMakeOffer_' + formId).attr('action', actionUrl);
      $('#frmMakeOffer_' + formId).submit();      
    });
   
	$("#divscroll").animate({
	  scrollTop: $('#divscroll')[0].scrollHeight - $('#divscroll')[0].clientHeight
	}, 0);
  }); 
</script>



