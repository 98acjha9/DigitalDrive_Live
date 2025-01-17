<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller {
	function __construct() {
      	parent::__construct();
		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0',false);
		header('Pragma: no-cache');
		$this->load->library('Luser');
		$this->load->library('Laccount');
		$this->load->helper('captcha');

        if(!$this->auth->is_logged()) {
        	$this->session->set_flashdata('msg', '<div class="alert alert-info text-center">You haven\'t login to the portal. Please login to proceed further.</div>');
        	redirect('sign-in', 'refresh');
        }	
	}
function hashpassword($password) {
        return md5("ctgs".$password);
    }
	public function index(){		
		if($this->session->userdata('user_type') == 1){
			redirect('admin/dashboard','refresh');
		}else if($this->session->userdata('user_type') == 3){
			// $content = $this->luser->dashboard_page();
			// $data = array(
			// 	'content' => $content,
			// 	'title' => display('Dashboard :: Hire-n-Work'),
			// );
			// $this->template->full_customer_html_view($data);	

			redirect('client-dashboard/'.$this->session->userdata('username'),'refresh');

		}elseif($this->session->userdata('user_type') == 4){

			$user_id=$this->session->userdata('user_id');
			$user_details= $this->db->get_where('user_login',array('user_id'=>$user_id))->row();
			
			if($user_details->profile_status==1){

				redirect('freelancer-dashboard/'.$this->session->userdata('username'),'refresh');
			}
			else{
				redirect('user-bio');
			}
			
		}elseif($this->session->userdata('user_type') == 5 || $this->session->userdata('user_type') == 6){
			redirect('nlancer-dashboard/'.$this->session->userdata('username'),'refresh');
		}
	}

#For Admin View User Data 
	public function admin_profile_vistor($user_id,$user_type){
		$content = $this->luser->public_profile_page_admin($user_id,$user_type);
		
		$data = array(
		   'content' => $content,
		    'title' => display('Public Profile :: Hire-n-Work'),
		);	          
		$this->template->full_customer_html_view($data);
		
	
	}
#For Admin View User Data 


	public function upcoming_projects($pageIndex = 0){
		$content = $this->luser->upcoming_projects_page();
		$data = array(
			'content' => $content,
			'title' => display('Upcoming Projects :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);
	}
	public function hired($pageIndex = 0){
		$content = $this->luser->hired();
		$data = array(
			'content' => $content,
			'title' => display('Hired :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);
	}
	public function search_freelancer(){
        $content = $this->luser->search_freelancer_page();
		$data = array(
		    'content' => $content,
		    'title' => display('Search Freelancer :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);
	}		
	public function client_bio(){
		$content = $this->luser->client_bio_page();
		$data = array(
			'content' => $content,
			'title' => display('Client Bio :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);		
	}
	public function editprofile(){
		$content = $this->luser->edit_profile_page();
		$data = array(
			'content' => $content,
			'title' => display('Edit Profile :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);		
	}


	public function portfolio(){
		$content = $this->luser->client_portfolio_page();
		$data = array(
			'content' => $content,
			'title' => display('Portfolio :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);		
	}
	public function gender(){
		
        $this->form_validation->set_rules('fldUserGender','Gender', 'required');
        if($this->form_validation->run() == false){        
		    $content = $this->luser->gender_page();
		    $data = array(
			    'content' => $content,
			    'title' => display('Gender :: Hire-n-Work'),
		    );
		}else{
		    $this->luser->updateUserData($this->session->all_userdata());
		    $content = $this->luser->gender_page();
		    $data = array(
			    'content' => $content,
			    'title' => display('Gender :: Hire-n-Work'),
		    );
	    }
		$this->template->full_customer_html_view($data);
	}
	public function payment_details(){
        $this->form_validation->set_rules('fldCreditCardNo','Card No', 'trim|required|numeric|min_length[13]|max_length[16]');
        $this->form_validation->set_rules('fldCardExpiryMonth','Expiry Month', 'required');
        $this->form_validation->set_rules('fldCardExpiryYear','Expiry Year', 'required');
        $this->form_validation->set_rules('fldCreditCardCvv','CVV No', 'trim|required|numeric|min_length[3]|max_length[4]');                        
        if($this->form_validation->run() == false){        
		    $content = $this->luser->payment_details_page();
		    $data = array(
			    'content' => $content,
			    'title' => display('Payment Details :: Hire-n-Work'),
		    );
		}else{
		    $this->luser->updateUserCardData($this->session->all_userdata());
		    $content = $this->luser->payment_details_page();
		    $data = array(
			    'content' => $content,
			    'title' => display('Payment Details :: Hire-n-Work'),
		    );
	    }
		$this->template->full_customer_html_view($data);
	}
	public function public_profile(){	
        $content = $this->luser->public_profile_page();
		$data = array(
		   'content' => $content,
		    'title' => display('Public Profile :: Hire-n-Work'),
		);	          
		$this->template->full_customer_html_view($data);
	}	
	public function settings(){
        $this->form_validation->set_rules('fldCurrentPassword','Current Password', 'trim|required');
        $this->form_validation->set_rules('fldNewPassword','New Password', 'trim|required');
        $this->form_validation->set_rules('fldConfirmPassword','Expiry Year', 'trim|required|matches[fldNewPassword]');                       
        if($this->form_validation->run() == false){        
		    $content = $this->luser->settings_page();
		    $data = array(
			    'content' => $content,
			    'title' => display('User Profile Settings :: Hire-n-Work'),
		    );	
		}
		$this->template->full_customer_html_view($data);	    			
	}
    public function change_password() {
		$user_data=$this->session->all_userdata(); 
		$user_id=$user_data['user_id'];
		
		$old_password=$this->input->post('fldCurrentPassword'); 
		$old_pass_id=  $password = md5("ctgs".$old_password);
		
		$new_password=$this->input->post('fldNewPassword');
	   $new_pass_id=$this->hashpassword($new_password);
	   
		$query_get_old_pass = $this->db->query("SELECT password FROM user_login WHERE user_id='$user_id'");
			$result=$query_get_old_pass->result() ;
		  $passwd=$result[0]->password;
			//print_r($result);
		 $array_pass=array(
		'password'=>$new_pass_id
		); 
		//$array_pass_xss = $this->security->xss_clean($array_pass);
		if($passwd!=$old_pass_id){
			echo 'wrong';
			//return false;
		}
		else{
			
			 $this->db->where('user_id', $user_id);

		$this->db->update('user_login', $array_pass); 
		echo 'true';
		//return true;
		}
		
		/*$this->luser->updateUserPassword($this->session->all_userdata());
		$content = $this->luser->settings_page();
		echo '<pre>';
		print_r($content);
		echo '</pre>';
		$data = array(
		    'content' => $content,
		    'title' => display('User Profile Settings :: Hire-n-Work'),
		);	
		$this->template->full_customer_html_view($data);*/			
    }

	public function messages(){
		if($this->uri->segment(2) !=''){
			$user_id_to = $this->uri->segment(2);
		}else{
			$user_id_to = '';
		}
		$content = $this->luser->messages($user_id_to);

//		echo '<pre>'; print_r($content); echo '</pre>'; die;
		$data = array(
			'content' => $content,
			'title' => display('Messages :: Hire-n-Work'),
		);
		$this->template->full_website_message_view($data);
	}

	public function get_messages($user_id_to) {
        $content = $this->luser->get_messages($user_id_to);
        $data = array(
            'content' => $content
        );
        $this->load->view('user/messages_ajax', $data);
    }

    public function get_frndlist($user_id_to) {
        $content = $this->luser->get_messages($user_id_to);
        $data = array(
            'content' => $content
        );
        $this->load->view('user/friendList_ajax', $data);
    }

	public function last_seen($user_id_to) {
        $this->load->model('Messages');
        $this->load->model('Users');
        $otherUserData = $this->Users->get_user_profile_info_by_id($user_id_to);
        $otherUserLogin = $otherUserData['basic_info']->is_login;
        $lastSeen = $this->Messages->lastSeen($user_id_to);
        echo ($otherUserLogin == 1) ? 'Online' : $lastSeen;
    }

	public function saveMsgData() {
		return $this->luser->addMsgUserData();
	}

	public function paymentsave() {
		$this->load->model('Users');
		$CI =& get_instance();
            $CI->load->model('Users');
			$user_name =$this->session->userdata('user_name');

			$data= $CI->input->post();

			$data = array(
			    'account_number' => $CI->input->post('account_number'),
			    'account_name' => $CI->input->post('account_name'),
			    'bank_name' => $CI->input->post('bank_name'),
			    'ifsc_code' => $CI->input->post('ifsc_code'),
			    'bank_address' => $CI->input->post('bank_address'),
			    'user_id' => $CI->session->userdata('user_id')
			    
				,
			   
		  );
			$inserting = $this->db->insert('use_bank_info',$data);
			
			$content = $this->laccount->confirm_email_admin($user_name);	
		redirect(base_url().'payment');
		
		
	}


	public function paymentupdate() {
		$this->load->model('Users');
		$CI =& get_instance();
            $CI->load->model('Users');
			
			$user_id=$CI->session->userdata('user_id');
			$data = array(
			    'account_number' => $CI->input->post('account_number'),
			    'account_name' => $CI->input->post('account_name'),
			    'bank_name' => $CI->input->post('bank_name'),
			    'ifsc_code' => $CI->input->post('ifsc_code'),
			    'bank_address' => $CI->input->post('bank_address'),
			    'user_id' => $user_id
				,
		   
		  );
			$this->db->where('user_id', $user_id);
			$inserting = $this->db->update('use_bank_info',$data);
		
		redirect(base_url().'payment');
		
	}
	
	public function saveUserData() {
		return $this->luser->updateUserData($this->session->all_userdata());
	}	
	public function saveUserProfile() {
		return $this->luser->updateUserProfile($this->session->all_userdata());
	}

	public function activeUserProfile() {
		return $this->luser->activeUserProfile($this->session->all_userdata());
	}

	public function savePortfolioData() {
		return $this->luser->insertPortfolioData($this->session->all_userdata());
	}

	public function updateePortfolioData() {
		return $this->luser->updatePortfolioData($this->session->all_userdata());
	}
	
	public function saveProfile_title() {
		return $this->luser->UpdateProfile_title($this->session->all_userdata());
	}

	public function removeUserSkillData() {
		return $this->luser->removeUserSkillData();
	}

	public function save_validate_mobile_no() {
		echo $this->luser->save_validate_mobile_no($this->session->all_userdata());
	}	

	public function save_transactional_notification() {
		echo $this->luser->save_transactional_notification($this->session->all_userdata());
	}

	public function save_task_update_notification() {
		echo $this->luser->save_task_update_notification($this->session->all_userdata());
	}			

	public function save_task_reminder_notification() {
		echo $this->luser->save_task_reminder_notification($this->session->all_userdata());
	}

	public function save_helpful_notification() {
		echo $this->luser->save_helpful_notification($this->session->all_userdata());
	}

	public function uploadUserProfileImage() {
        echo $this->luser->save_user_profile_image($this->session->all_userdata());
	}

    public function ajax_search_freelancer() {
    	echo $this->luser->ajax_search_freelancer();
    }  
	//modified on 21-10-2020
	public function ajax_search_top_freelancer() {
    	echo $this->luser->ajax_search_top_freelancer();
    } 
	public function addSkill(){

		 $response = "";
		 $data = array(
            'name' => $this->input->post('title'),
			'status'=>1,
			'deleted'=>0,
			'doc'=>date('Y-m-d H:i:s')
         );

        $result = $this->db->insert('area_of_interest',$data);  

        $insert_id = $this->db->insert_id();

		$response = array('lastinsertid'=>$insert_id,'title'=>$this->input->post('title'));	
		echo json_encode($response);
		exit;
		
	}
    public function analytics()
	{
		$content = $this->luser->analytics();
		$data = array(
			'content' => $content,
			'title' => display('Analytics :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);
	}

	public function see_all_projects()
	{
		$content = $this->luser->analytics_all();
		$data = array(
			'content' => $content,
			'title' => display('Analytics :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);
	}
}

