<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Freelancer extends CI_Controller {

	function __construct() {
      	parent::__construct();

		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0',false);
		header('Pragma: no-cache');

		$this->load->library('Lfreelancer');
		$this->load->library('Lmicrokey');
		$this->load->library('Ltask');
		if(!$this->auth->is_logged()) {
        	$this->session->set_flashdata('msg', '<div class="alert alert-info text-center">You haven\'t login to the portal. Please login to proceed further.</div>');
        	redirect('sign-in', 'refresh');

        }
    }

     public function microkey_client_details($taskID = null){
		if(empty($taskID))
			redirect('microkey-list-client', 'refresh');

        $content = $this->ltask->microkey_client_details_page($taskID, $this->session->all_userdata());
	    $data = array(
	        'content' => $content,
	        'title' => display('Job Details :: Hire-n-Work'),
	    );

		$this->template->full_freelancer_html_view($data);
	}

	public function post_microkey_client()	{

		$content = $this->lmicrokey->post_microkey_client();
		$data = array(
					'content' => $content,
					'title' => display('Key List :: Hire-n-Work'),
				);		
		//$this->template->full_freelancer_html_view($data);
		$this->template->full_customer_html_view($data);
	}

	public function microkey_list_client($pageIndex = 0){
		$content = $this->lmicrokey->microkey_list_client_page();
		$data = array(
			'content' => $content,
			'title' => display('Upcoming Projects :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);
	}

	public function add_microkey_client_post() {
		$data = array();

        $this->form_validation->set_rules('fldTaskTitle','Title', 'required');
		//$this->form_validation->set_rules('fldTaskKeywords','Keywords','required');
        //$this->form_validation->set_rules('fldSkillRequired','Skill Requirement','callback_required_check');
       // $this->form_validation->set_rules('fldTaskDescription','Description', 'required');
       // $this->form_validation->set_rules('fldSelContinent','Continent', 'required');
       // $this->form_validation->set_rules('flddurationfield','Duration Period', 'required');
		//$this->form_validation->set_rules('flddurationtype','Duration Type', 'required');
      //  $this->form_validation->set_rules('fldSelCountry','Country', 'required');
       // $this->form_validation->set_rules('fldTotalBudget','Total Amount', 'required|decimal|greater_than_equal_to[1500]');
		//$this->form_validation->set_rules('fldTotalBudget','Total Amount', 'required');

        $submitData = $this->input->post();
        //echo '<pre>'; print_r($submitData);die();
        if($this->form_validation->run() == false){
	        $content = $this->lmicrokey->add_microkey_client_post($this->session->all_userdata());
	        $data = array(
		        'content' => $content,
		        'title' => display('Post Task Step - 3 :: Hire-n-Work'),
	        );
		    $this->template->full_customer_html_view($data);
		} else {
        	$this->lmicrokey->add_microkey_client_post($this->session->all_userdata());
	    }
	}

	public function update_microkey_client_post($id) {
		$data = array();

        $this->form_validation->set_rules('fldTaskTitle','Title', 'required');
		//$this->form_validation->set_rules('fldTaskKeywords','Keywords','required');
        //$this->form_validation->set_rules('fldSkillRequired','Skill Requirement','callback_required_check');
       // $this->form_validation->set_rules('fldTaskDescription','Description', 'required');
       // $this->form_validation->set_rules('fldSelContinent','Continent', 'required');
       // $this->form_validation->set_rules('flddurationfield','Duration Period', 'required');
		//$this->form_validation->set_rules('flddurationtype','Duration Type', 'required');
      //  $this->form_validation->set_rules('fldSelCountry','Country', 'required');
       // $this->form_validation->set_rules('fldTotalBudget','Total Amount', 'required|decimal|greater_than_equal_to[1500]');
		//$this->form_validation->set_rules('fldTotalBudget','Total Amount', 'required');
	//echo $id;
        $submitData = $this->input->post();
        //echo '<pre>'; print_r($submitData);die();
         $content = $this->lmicrokey->edit_microkey_client_post($id,$this->session->all_userdata());
	        $data = array(
		        'content' => $content,
		        'title' => display('Edit Task :: Hire-n-Work'),
	        );
		    $this->template->full_customer_html_view($data);
	}
	public function microkey_list_clientpanel(){
		
		$content = $this->lmicrokey->microkey_list_clientpanel();
		$data = array(
					'content' => $content,
					'title' => display('Key List :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
	//modified on 23-10-2020
	public function submit_microkey_proposal(){
			
		$this->form_validation->set_rules('terms_amount_max','Bid Amount', 'required');
		
		$submitData = $this->input->post();        
		 
		if($this->form_validation->run() == false){ 
			$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Bid amount should not be empty.</div>');
			redirect('microkey-job-details/'.$this->input->post('microkey_id'),'refresh'); 
		}else{			
			$r = $this->lmicrokey->microkey_proposal_already_added();
			if($r['is_added']) {
				$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">You are already sended the proposal.</div>');
				redirect('microkey-job-details/'.$this->input->post('microkey_id'),'refresh'); 				
			} else {
				$this->lmicrokey->submit_microkey_proposal();
				redirect('microkey-list-clientpanel','refresh');
			}
		}
	}
	//modified on 23-10-2020
	public function add_microkey_proposal(){
		//microkey-proposal
		$content = $this->lmicrokey->add_microkey_proposal_layout();
		$data = array(
					'content' => $content,
					'title' => display('Freelancer Proposal :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
		
	}
	//modified on 23-10-2020
	public function save_inappropriate_microkey(){
		$content = $this->lmicrokey->save_inappropriate_microkey();
		echo $content;
	}
	//modified on 23-10-2020
	public function save_microkey_job(){
		$content = $this->lmicrokey->save_microkey_job();
		echo $content;
	}
	//modified on 23-10-2020
	public function microkey_job_details($microkey_id=''){
		$microkey_id = $this->uri->segment(2);
		$content = $this->lmicrokey->get_microkey_details_by_id($microkey_id);
		$data = array(
					'content' => $content,
					'title' => display('Freelancer Job Details :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
	public function micro_freelancer_details(){
		$microkey_id = $this->uri->segment(2);
		
		$content = $this->lmicrokey->microkey_details($microkey_id);
		//echo '<pre>'; print_r($content);die();
		$data = array(
					'content' => $content,
					'title' => display('Freelancer Job Details :: Hire-n-Work'),
				);		
		$this->template->full_customer_html_view($data);
	}

	public function micro_freelancer(){
		
		$content = $this->lmicrokey->micro_freelancer();
		$data = array(
					'content' => $content,
					'title' => display('Key List :: Hire-n-Work'),
				);		
		$this->template->full_customer_html_view($data);
	}

    public function past_microkey_page_1()	{
		$content = $this->lmicrokey->past_microkey_page_1();
		$data = array(
			'content' => $content,
			'title' => display('Post MicroKey Step - 1 :: Hire-n-Work'),
		);
		$this->template->full_customer_html_view($data);
	}

	public function past_microkey_page_2() {
            $data = array();

            $this->form_validation->set_rules('fldTaskTitle', 'Title', 'required');
            //$this->form_validation->set_rules('fldTaskKeywords','Keywords', 'required');
            //$this->form_validation->set_rules('fldSkillRequired','Skill Requirement','callback_required_check');
            //$this->form_validation->set_rules('fldTaskDescription','Description', 'required');

            $submitData = $this->input->post();
            //echo '<pre>'; print_r($submitData);die();
            if ($this->form_validation->run() == false) {
                $content = $this->lmicrokey->past_microkey_page_1($submitData);
                $data = array(
                    'content' => $content,
                    'title' => display('Post MicroKey Step - 1 :: Hire-n-Work'),
                );
            } else {
                if (!empty($submitData['fldTaskTitle']) &&
                        //!empty($submitData['fldTaskKeywords']) && 
                        !empty($submitData['fldSkillRequired'])
                //!empty($submitData['fldTaskDescription']
                ) {
                    $content = $this->lmicrokey->past_microkey_page_2($submitData);
                    $data = array(
                        'content' => $content,
                        'title' => display('Post MicroKey Step - 2  :: Hire-n-Work'),
                    );
                }
            }

            $this->template->full_customer_html_view($data);
        }


	public function past_microkey_page_3() {

        $data = array();
        $this->form_validation->set_rules('fldTaskTitle', 'Title', 'required');
        //$this->form_validation->set_rules('fldTaskKeywords','Keywords', 'required');
        //$this->form_validation->set_rules('fldSkillRequired','Skill Requirement','callback_required_check');
        // $this->form_validation->set_rules('fldTaskDescription','Description', 'required');
        // $this->form_validation->set_rules('fldSelContinent','Continent', 'required');
        //$this->form_validation->set_rules('flddurationfield','Duration Period', 'required');
        //$this->form_validation->set_rules('flddurationtype','Duration Type', 'required');
        $this->form_validation->set_rules('fldTotalBudget', 'Budget', 'required');

        $submitData = $this->input->post();

        if ($this->form_validation->run() == false) {
            //echo '<pre>'; print_r($submitData);
            $content = $this->lmicrokey->past_microkey_page_3($submitData);
            $data = array(
                'content' => $content,
                'title' => display('Post MicroKey Step - 2 :: Hire-n-Work'),
            );
        } else {
            if (!empty($submitData['fldTaskTitle']) &&
                    //!empty($submitData['fldTaskKeywords']) &&
                    !empty($submitData['fldSkillRequired']) &&
                    !empty($submitData['fldTotalBudget'])
            //!empty($submitData['fldTaskDescription']) &&
            //!empty($submitData['fldSelContinent']) && 
            //!empty($submitData['flddurationfield']) &&
            //!empty($submitData['flddurationtype']) && !empty($submitData['fldSelCountry'])
            ) {
                $content = $this->lmicrokey->past_microkey_page_3($submitData);
                $data = array(
                    'content' => $content,
                    'title' => display('Post MicroKey Step - 3  :: Hire-n-Work'),
                );
            }
        }
        $this->template->full_customer_html_view($data);
    }

	public function add_new_job_post() {
		$data = array();

        $this->form_validation->set_rules('fldTaskTitle','Title', 'required');
		//$this->form_validation->set_rules('fldTaskKeywords','Keywords','required');
        //$this->form_validation->set_rules('fldSkillRequired','Skill Requirement','callback_required_check');
       // $this->form_validation->set_rules('fldTaskDescription','Description', 'required');
       // $this->form_validation->set_rules('fldSelContinent','Continent', 'required');
       // $this->form_validation->set_rules('flddurationfield','Duration Period', 'required');
		//$this->form_validation->set_rules('flddurationtype','Duration Type', 'required');
      //  $this->form_validation->set_rules('fldSelCountry','Country', 'required');
       // $this->form_validation->set_rules('fldTotalBudget','Total Amount', 'required|decimal|greater_than_equal_to[1500]');
		//$this->form_validation->set_rules('fldTotalBudget','Total Amount', 'required');

        $submitData = $this->input->post();
        //echo '<pre>'; print_r($submitData);die();
        if($this->form_validation->run() == false){
	        $content = $this->lmicrokey->past_microkey_page_3($submitData);
	        $data = array(
		        'content' => $content,
		        'title' => display('Post Task Step - 3 :: Hire-n-Work'),
	        );
		    $this->template->full_customer_html_view($data);
		} else {
        	$this->lmicrokey->add_new_job($this->session->all_userdata());
	    }
	}

	public function microkey_list(){
		
		$content = $this->lmicrokey->microkey_list();
		$data = array(
					'content' => $content,
					'title' => display('Key List :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}

	public function microkey_details(){
		$microkey_id = $this->uri->segment(2);
		
		$content = $this->lmicrokey->microkey_details($microkey_id);
		//echo '<pre>'; print_r($content);die();
		$data = array(
					'content' => $content,
					'title' => display('Freelancer Job Details :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}

	public function index(){
		
		$content = $this->lfreelancer->dashboard_page();
		$data = array(
			'content' => $content,
			'title' => display('Dashboard :: Hire-n-Work'),
		);
		$this->template->full_freelancer_html_view($data);
	}
	
	public function key_list(){
		
		$content = $this->lfreelancer->key_list();
		$data = array(
					'content' => $content,
					'title' => display('Key List :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
	public function freelancer_job_details(){
		$task_id = $this->uri->segment(2);
		
		$content = $this->lfreelancer->freelancer_job_details($task_id);
		$data = array(
					'content' => $content,
					'title' => display('Freelancer Job Details :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
	public function save_job(){
		$task_user_id = $_POST['taskId']; 
		$this->lfreelancer->ajax_save_job($task_user_id);
	}
	public function save_inappropriate(){
		$this->load->model("Freelancers"); 
		$task_user_id = $_POST['taskId']; 
		$this->Freelancers->save_inappropriate_task($task_user_id);
	}
	public function save_job_list(){
		$content = $this->lfreelancer->saved_job_list();
		$data = array(
					'content' => $content,
					'title' => display('Freelancer Job List :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
	public function proposal(){
		
		$content = $this->lfreelancer->freelancer_proposal();
		$data = array(
					'content' => $content,
					'title' => display('Freelancer Proposal :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
		
	}
	public function submit_proposal(){
			
		$this->form_validation->set_rules('terms_amount_max','Bid Amount', 'required');
		
		$submitData = $this->input->post();        
		 
		if($this->form_validation->run() == false){ 
			$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Bid amount should not be empty.</div>');
			redirect('job-details/'.$this->input->post('user_task_id'),'refresh'); 
		}else{			
			$r = $this->lfreelancer->proposal_already_added();
			if($r['is_added']) {
				$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">You are already sended the proposal.</div>');
				redirect('job-details/'.$this->input->post('user_task_id'),'refresh'); 				
			} else {
				$this->lfreelancer->submit_proposal();
				redirect('key-list','refresh');
			}
		}
	}
	
	public function public_profile(){
		$user_id = $this->uri->segment(2);
		if($user_id == ''){
			$user_id = $this->session->userdata('user_id');
		}
		$content = $this->lfreelancer->public_profile($user_id);
		$data = array(
					'content' => $content,
					'title' => display('My Jobs :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
		
	public function earnings(){
		$content = $this->lfreelancer->earnings();
		$data = array(
					'content' => $content,
					'title' => display('Key List :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
	public function analytics(){
		$content = $this->lfreelancer->analytics();
		$data = array(
					'content' => $content,
					'title' => display('Key List :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
	public function problem_ticket(){
		$content = $this->lfreelancer->problem_ticket();
		
		$data = array(
			'content' => $content,
			'title' => display('Problem Ticket :: Hire-n-Work'),
		);		
		$this->template->full_freelancer_html_view($data);
	}

	public function ticket_history(){

		
		$content = $this->lfreelancer->problem_ticket_history();
		
		$data = array(
			'content' => $content,
			'title' => display('Problem Ticket History :: Hire-n-Work'),
		);		
		$this->template->full_freelancer_html_view($data);
	}

	public function user_compose_email($ticket_id=''){

		$content = $this->lfreelancer->get_ticket_details($ticket_id);
		
		$data = array(
			'content' => $content,
			'title' => display('Problem Ticket History Details :: Hire-n-Work'),
		);		
		$this->template->full_freelancer_html_view($data);
	}
	public function ticket_send_email(){

			$content = $this->lfreelancer->ticket_send_email();
			
			$data = array(
				'content' => $content,
				'title' => display('Problem Ticket History Details :: Hire-n-Work'),
			);		
			$this->template->full_freelancer_html_view($data);
		}
	public function ticket_history_details($ticket_id=''){

		$content = $this->lfreelancer->problem_ticket_history_details($ticket_id);
		
		$data = array(
			'content' => $content,
			'title' => display('Problem Ticket History Details :: Hire-n-Work'),
		);		
		$this->template->full_freelancer_html_view($data);
	}
	
	public function save_problem_ticket(){
		$this->form_validation->set_rules('grievance_id','Issue Type', 'required');
        $this->form_validation->set_rules('grievance_content','Description','required');       

        if($this->form_validation->run() == false){ 
			$this->lfreelancer->add_ticket($this->session->all_userdata());
		}
		redirect('problem-ticket');
	}
	public function job_list(){
		$type = $this->uri->segment(2); 
		
		$content = $this->lfreelancer->job_list_data($type);
		
		$data = array(
			'content' => $content,
			'title' => display('Job List :: Hire-n-Work'),
		);		
		$this->template->full_freelancer_html_view($data);
		
	}
	
	public function offer_jobs(){
		$type = 'offer'; 
		
		$content = $this->lfreelancer->job_list_data($type);
		
		$data = array(
			'content' => $content,
			'title' => display('Job List :: Hire-n-Work'),
		);		
		$this->template->full_freelancer_html_view($data);
		
	}
	
	public function hired_job_details(){
		$task_id = $this->uri->segment(2);
		
		$content = $this->lfreelancer->hired_job_details($task_id);
		$data = array(
					'content' => $content,
					'title' => display('Hired Job Details :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
	public function freelancer_direct_action(){
		$data['task_id'] = $this->uri->segment(2);
		$data['action_type'] = $this->uri->segment(4);
				
		$this->lfreelancer->freelancer_direct_action($data);
	}
	
	
	public function freelancer_take_action(){
		$data['task_id'] = $this->uri->segment(2);
		$data['action_type'] = $this->uri->segment(3);
				
		$this->lfreelancer->freelancer_take_action($data);
	}
	
	
	public function close_contract(){ 
		$content = $this->lfreelancer->close_contract_page($this->uri->segment(2));
		$data = array(
					'content' => $content,
					'title' => display('Complete Task:: Hire-n-Work'),
				);		
		$this->template->full_customer_html_view($data);
	}
	
	public function see_all_projects()
	{
		$content = $this->lfreelancer->analytics_details();
		$data = array(
					'content' => $content,
					'title' => display('Analytics Details :: Hire-n-Work'),
				);		
		$this->template->full_freelancer_html_view($data);
	}
	
}