<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Freelancers extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
    
    public function get_grievance(){
		$user_type = $this->session->userdata('user_type');
    	$this->db->select('*')->from('grievance')->where('grievance.status','1')->where('grievance.user_type',$user_type);
		$query = $this->db->get();
		
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return array();
		}
    }
    
    public function add_ticket($userInfo = array(), $submitData = array()){
		// check last id
		$info = $this->db->query("select max(id) maxid from user_grievance")->row();
		if(!empty($info) && $info->maxid !=''){
			$ticket_id = 'TICK'.date('Y').($info->maxid + 1);
		}else{
			$ticket_id = 'TICK'.date('Y').'1';
		}
		
		$data = array(
			'user_type' => $userInfo['user_type'],
			'user_id' => $userInfo['user_id'],
			'grievance_id' => $submitData['problem_id'],
			'grievance_subject' => ($submitData['grievance_subject'] != '' )?$submitData['grievance_subject'] : '',
			'grievance_content' => $submitData['grievance_content'],
			'problem_ticket_no' => $ticket_id,
			'doc' => date('Y-m-d H:i:s')
		);

		$result = $this->db->insert('user_grievance',$data);

		if($result){
			return array('status' => TRUE, 'message' => $ticket_id);
		}else{
			return array('status' => FALSE, 'message' => 'unable_to_add_record_in_db');
		}        
	}
	
	public function search_jobs_by_keyword_count($searchValue = ''){
		$sql = "select distinct(main.task_id) as task_id
				from (
					select tr.area_of_interest_id, t.task_id, t.task_name, ari.name as skill_name,conti.name as continent_name, count.name as country_name
					from task_requirements tr
					join task t on t.task_id = tr.task_id
					left join area_of_interest ari on ari.area_of_interest_id = tr.area_of_interest_id
					left join continent conti on conti.continent_id = t.task_origin_location
					left join country count on count.country_id = t.task_origin_country
					where (tr.deleted = 0 or tr.deleted is null) and ( t.task_name like '%".addslashes($searchValue)."%' or ari.name like '".addslashes($searchValue)."%' or conti.name like '".addslashes($searchValue)."%' or count.name like '".addslashes($searchValue)."%' or t.task_total_budget = '".addslashes($searchValue)."' )
				) main";
		
		$searchresult = $this->db->query($sql);
		return $searchresult->num_rows();
	}
		
	public function search_jobs_by_keyword($start, $limit , $searchValue = ''){ 
		
        $task_details = array();
		//$searchValue = $this->session->userdata('searchValue');
		// make join query with skill table and country table
		
		if($searchValue !=''){ 
			$sql = "select distinct(main.task_id) as task_id
					from (
						select tr.area_of_interest_id, t.task_id, t.task_name, ari.name as skill_name,conti.name as continent_name, count.name as country_name
						from task_requirements tr
						join task t on t.task_id = tr.task_id
						left join area_of_interest ari on ari.area_of_interest_id = tr.area_of_interest_id
						left join continent conti on conti.continent_id = t.task_origin_location
						left join country count on count.country_id = t.task_origin_country
						where (tr.deleted = 0 or tr.deleted is null) and ( t.task_name like '%".addslashes($searchValue)."%' or ari.name like '".addslashes($searchValue)."%' or conti.name like '".addslashes($searchValue)."%' or count.name like '".addslashes($searchValue)."%' or t.task_total_budget = '".addslashes($searchValue)."' )
					) main ";
			
			$searchresult = $this->db->query($sql);
			//echo $this->db->last_query(); 
			
			if($searchresult->num_rows() > 0){ 
				foreach($searchresult->result_array() as $val){
					$task_arr[] = $val['task_id'];
				}
				$task_arr = implode(',',($task_arr));
				$this->session->set_flashdata('msg', '<div class="alert alert-success text-center">Search results... </div>');
			}else{
				$task_arr = array();
				$this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">No search result found... </div>');
				return array();
			}
			
			//print_r($task_arr);echo $this->db->last_query(); die; 
			
		}else{
			$task_arr = array();
		}
		
        $this->db->select('task.*,continent.name as continent_name,country.name as country_name');
        $this->db->from('task');  
		$this->db->join('continent', 'continent.continent_id = task.task_origin_location');
		$this->db->join('country', 'country.country_id = task.task_origin_country');
		if(!empty($task_arr)){
			$this->db->where('task.task_id in ('.$task_arr.')');
		}
		$this->db->order_by('task.task_doc','desc');
		$this->db->limit($limit, $start);
		
        $query = $this->db->get();

        //echo $this->db->last_query(); 

        foreach ($query->result() as $row){

            $task_attachments = $task_requirements = array();
            $this->db->select('task_attachments.*');
            $this->db->from('task_attachments');
            $this->db->where('task_attachments.task_id', $row->task_id);
			$this->db->where('task_attachments.is_deleted',0);
            $query_task_attach = $this->db->get();

            //echo $this->db->last_query();

            foreach ($query_task_attach->result() as $row_task_attach){

                $arrFileName = explode('_', $row_task_attach->task_attach_filename);

                $task_attachments[] = array('file_name' => $row_task_attach->task_attach_filename, 'file_display_name' => end($arrFileName) , 'task_attachment_id' => $row_task_attach->task_attachment_id);

            }


            $this->db->select('task_requirements.area_of_interest_id,area_of_interest.name,task_requirements.task_id');
            $this->db->from('task_requirements');
            $this->db->join('area_of_interest', 'area_of_interest.area_of_interest_id = task_requirements.area_of_interest_id');
            $this->db->where('task_requirements.task_id', $row->task_id);
			$this->db->where('task_requirements.deleted',0);			
			if($searchValue !=''){
				$this->db->where('area_of_interest.name like "%'.$searchValue.'%"');
			}

            $query_task_requirements = $this->db->get();

            //echo $this->db->last_query();

            foreach ($query_task_requirements->result() as $row_task_requirement){

                $task_requirements[] = array('skill_id' => $row_task_requirement->area_of_interest_id, 'skill_name' => $row_task_requirement->name, 'task_id' => $row_task_requirement->task_id);

            }
			
			//$offer_count = $this->job_offer_user($row->task_id);
			$offer_count = $this->get_number_proposal($row->task_id);

            $basic_info = array(
				'task_id'=> $row->task_id, 
				'task_name'=> $row->task_name, 
				'user_task_id' => $row->user_task_id, 
				'task_details' => $row->task_details, 
				'task_due_date' => date('m-d-Y', strtotime($row->task_due_date)), 
				'task_origin_location' => $row->continent_name, 
				'task_origin_country' => $row->country_name, 
				'task_total_budget' => $row->task_total_budget,
				'task_doc' => $row->task_doc,
				'offer_count' => $offer_count
			);



            $task_details[] = array('basic_info' => $basic_info, 'task_attachments' => $task_attachments, 'task_requirements' => $task_requirements);

        }
		
		// echo '<pre>'; print_r($task_details); die;

        return $task_details;  
	}
	
	public function job_offer_user($task_id = ''){
		if($task_id == ''){
			return 0;
		}else{
			$this->db->select('task.task_id, offer_task.receiver_id');
			$this->db->from('offer_task');
			$this->db->join('task', 'task.task_id = offer_task.task_id');        
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_is_complete', 0); 
			$this->db->where('task.task_is_ongoing', 0);         
			$this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
			$this->db->where('task.task_id',$task_id);
			$this->db->where('(offer_task.offer_status=1 AND offer_task.offer_is_deleted=0)');		
			$query = $this->db->get();

			//echo $this->db->last_query();
			return $query->num_rows();
		}
		 
	}
	
	
	public function get_number_proposal($task_id = ''){
		
		$q=$this->db->query("SELECT COUNT(task_id) AS total FROM task_proposal WHERE task_id=$task_id");
		$res=$q->result();
		$total=$res[0]->total;
	 
		 return $total;
	}
	
	public function proposal_count($task_id = '',$return_type =''){
		if($task_id == ''){
			return 0;
		}else{
			$this->db->select('task.task_id, task_proposal.*');
			$this->db->from('task_proposal');
			$this->db->join('task', 'task.task_id = task_proposal.task_id');        
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_is_complete', 0); 
			$this->db->where('task.task_is_ongoing', 0);         
			$this->db->where('task_proposal.task_id',$task_id);
			$query = $this->db->get();
			
			//echo $this->db->last_query();
			
			if($return_type == ''){
				return $query->num_rows();
			}else if($return_type != '' && $return_type == '1' ){
				return $query->row_array();
			}else{
				return $query->result_array();
			}
		}
	
	}
	
	public function get_proposal_info($searchVal = array()){
		$query = $this->db->select('*')->from('task_proposal')->where('task_id',$searchVal['task_id'])->where('user_id',$searchVal['user_id'])->get();
		if($query->num_rows() > 0){
			return 'yes';
		}else{
			return 'no';
		}
	}
	
	public function add_proposal($postData = array()){
		
		// get connects
		$total_connects = $this->db->select('user_login.total_connects')->from('user_login')->where('user_id',$this->session->userdata('user_id'))->get()->row()->total_connects;
		$new_total_connects = ($total_connects-1);
		
		if(!empty($postData['uploadFiles'])){
			$str = implode(',',$postData['uploadFiles']);
		}else{
			$str = ''; 
		}
		$date_of_creation = date("Y-m-d H:i:s");
		
		$insert = array(
			'task_id' => $this->input->post('task_id'),
			'user_id' => $this->session->userdata('user_id'),
			'terms_amount_max' => $this->input->post('terms_amount_max'),
			'terms_amount_min' => 0,
			'cover_letter' => $this->input->post('cover_letter'),
			'attachments' => $str,
			'doc' => $date_of_creation,
			'dom' => $date_of_creation,
			'status' => 1,
			'is_deleted' => 0
		);
		
		$this->db->insert('task_proposal',$insert);
		// get client id from task creadted by
		$getClientInfo = $this->db->query("select user_task_id,task_name,task_created_by from task where task_id = '".$this->input->post('task_id')."'")->row();
		if(!empty($getClientInfo)){
			$client_id = $getClientInfo->task_created_by;
		//	$task_name = $getClientInfo->task_name;
		
			$task_name = '<a href="'.base_url().'task-details/'.$getClientInfo->user_task_id.'">'.$getClientInfo->task_name.'</a>';
		}else{
			$client_id = 0;
			$task_name = '';
		}
		// data insert into task_interested table
		
		$insert = array(
			'req_send_user_id' => $client_id,
			'req_date_time' => $date_of_creation,
			'interested_user_id' => $this->session->userdata('user_id'),
			'request_send' => 'Y',
			'task_id' => $this->input->post('task_id'),
			'notification_type_id' => 10,
			'accept_status' => 'Y',
			'delete_status' => 0
		);
		$return = $this->db->insert('task_interested',$insert);
		
		// 
		$notification_master_data = $this->db->select('*')->from('notification_type')->where('NOTIFICATION_TYPE_ID',10)->get()->row();
		if(!empty($notification_master_data)){
			$message = $notification_master_data->MESSAGE;
		}else{
			$message = '';
		}
		
		$data = array(
			'offer_id' => 0,
			'task_id' => $this->input->post('task_id'),
			'notification_from' => $this->session->userdata('user_id'),
			'notification_to' => $client_id,
			'notification_details' => 'SEND PROPOSAL',
			'notification_master_id' => 10,
			'notification_message' => '<strong>'.'<a href='.base_url().'public-profile/'.$this->session->userdata('profile_id').'>'.$this->session->userdata('user_name').'</a></strong> '.$message.' <strong>'.$task_name.'</strong>',
			'notification_doc' => $date_of_creation
		);

		$result_sub = $this->db->insert('task_notification',$data);
		
		
		// modify connects
		$this->db->where('user_id',$this->session->userdata('user_id'))->update('user_login', array('total_connects' => $new_total_connects));
		
		return true;
	}
	public function get_freelancer_dashboard_count($searchValue){
		$user_id = $this->session->userdata('user_id');
		$this->db->select('task_proposal.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
		$this->db->from('task_proposal');
		$this->db->join('task','task.task_id = task_proposal.task_id');
		$this->db->join('users','users.user_id = task.task_created_by');
		$this->db->where('task.task_status', 1);
		$this->db->where('task.task_is_ongoing', 0);         
		$this->db->where('task_proposal.user_id',$user_id);
		$query = $this->db->get();
		return $query->num_rows();
	
	}
	
	public function get_freelancer_dashboard_data($searchValue,$rowperpage,$rowno){
		
		//echo $rowno; echo $rowperpage; die;
		
		
		$user_id = $this->session->userdata('user_id');
		$this->db->select('task_proposal.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
		$this->db->from('task_proposal');
		$this->db->join('task','task.task_id = task_proposal.task_id');
		$this->db->join('users','users.user_id = task.task_created_by');
		$this->db->where('task.task_status', 1);
		$this->db->where('task.task_is_ongoing', 0);         
		$this->db->where('task_proposal.user_id',$user_id);
		if($searchValue!=""){
			//$this->db->or_like('users.name',$searchValue,'both');
			//$this->db->like('task.task_name',$searchValue,'both');
			
			$this->db->group_start();
		 	$this->db->like('users.name', $searchValue,'both');
			$this->db->or_like('task.task_name', $searchValue,'both');
			$this->db->or_like('task.task_total_budget', $searchValue,'both');
			//$this->db->or_where('task.task_due_date', $searchValue,'both');
			$this->db->group_end();
			
		}
				
		$this->db->order_by('task_proposal.doc','desc');
		$this->db->limit($rowperpage,$rowno);
		$query = $this->db->get();
		// echo $this->db->last_query(); 
	//	 echo '<pre>'; print_r($query->result_array()); die('model');
		
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return array();
		}
	
	}
	
	public function get_job_by_type_count($type = '',$searchValue=''){
		$user_id = $this->session->userdata('user_id');
		if($type == ''){
			$this->db->select('task_proposal.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
			$this->db->from('task_proposal');
			$this->db->join('task','task.task_id = task_proposal.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_is_ongoing', 0);         
			$this->db->where('task_proposal.user_id',$user_id);
		}if($type == 'active'){
			$this->db->select('task_hired.*, task.*, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);
			$this->db->where('task.task_is_ongoing', 1);   
            $this->db->where('task.task_is_complete', 0); 
            $this->db->where('task.task_completed_by_owner', 0);		
			$this->db->where('task_hired.freelancer_id',$user_id);
			
		} if($type == 'late'){
			$this->db->select('task_hired.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);
			$this->db->where('task.task_is_ongoing', 1);         
			$this->db->where('task_hired.freelancer_id',$user_id);
			$this->db->where('task.task_completion_date	> task_hired.hired_end_date');
		} if($type == 'delivered'){			
			$this->db->select('task_hired.*, task.*, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);
            $this->db->where('task.task_is_complete', 1); 
            $this->db->where('task.task_completed_by_owner', 0); 			
			$this->db->where('task_hired.freelancer_id',$user_id);
		} if($type == 'completed'){
			$this->db->select('task_hired.*, task.*, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);
            $this->db->where('task.task_is_complete', 1); 
            $this->db->where('task.task_completed_by_owner', 1); 			
			$this->db->where('task_hired.freelancer_id',$user_id);
		} if($type == 'cancelled'){
			$this->db->select('task_hired.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);       
			$this->db->where('task_hired.freelancer_id',$user_id);
			$this->db->where('task.task_is_cancelled',1);
		} if($type == 'offer'){
			$this->db->select('offer_task.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
			$this->db->from('offer_task');
			$this->db->join('task','task.task_id = offer_task.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('offer_task.receiver_id',$user_id);
			
			
			
		}
		$query = $this->db->get();
		return $query->num_rows();
		
	}	
	
	public function get_job_by_type($type='',$searchValue='',$rowperpage,$rowno){
		$user_id = $this->session->userdata('user_id');
		if($type == ''){
			$this->db->select('task_proposal.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
			$this->db->from('task_proposal');
			$this->db->join('task','task.task_id = task_proposal.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_is_ongoing', 0);         
			$this->db->where('task_proposal.user_id',$user_id);
		}else if($type == 'active'){
			$this->db->select('task_hired.*, task.*, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);
			$this->db->where('task.task_is_ongoing', 1);   
            $this->db->where('task.task_is_complete', 0); 
            $this->db->where('task.task_completed_by_owner', 0);		
			$this->db->where('task_hired.freelancer_id',$user_id);
			if($searchValue!=""){
			 
			
				$this->db->group_start();
				$this->db->like('users.name', $searchValue,'both');
				$this->db->or_like('task.task_name', $searchValue,'both');
				$this->db->or_like('task.task_total_budget', $searchValue,'both');
				$this->db->group_end();
			
			}
			
						
		}else if($type == 'late'){
			$this->db->select('task_hired.*, task.*,users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);
			$this->db->where('task.task_is_ongoing', 1);         
			$this->db->where('task_hired.freelancer_id',$user_id);
			$this->db->where('task.task_completion_date	> task_hired.hired_end_date');
		}else if($type == 'delivered'){
			$this->db->select('task_hired.*, task.*, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);
            $this->db->where('task.task_is_complete', 1); 
            $this->db->where('task.task_completed_by_owner', 0); 			
			$this->db->where('task_hired.freelancer_id',$user_id);
		}else if($type == 'completed'){
			$this->db->select('task_hired.*, task.*, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 1);
            $this->db->where('task.task_is_complete', 1); 
            $this->db->where('task.task_completed_by_owner', 1); 			
			$this->db->where('task_hired.freelancer_id',$user_id);
			 
		}else if($type == 'cancelled'){
			$this->db->select('task_hired.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
			$this->db->from('task_hired');
			$this->db->join('task','task.task_id = task_hired.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('task.task_hired', 0);       
			$this->db->where('task_hired.freelancer_id',$user_id);
			$this->db->where('task.task_is_cancelled',1);
		}else if($type == 'offer'){
			$this->db->select('offer_task.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date, users.*');
			$this->db->from('offer_task');
			$this->db->join('task','task.task_id = offer_task.task_id');
			$this->db->join('users','users.user_id = task.task_created_by');
			$this->db->where('task.task_status', 1);
			$this->db->where('offer_task.is_refused', 0);
			$this->db->where('offer_task.receiver_id',$user_id);
			 
			
		}	
		
		$this->db->order_by('task.task_doc','desc');
		$this->db->limit($rowperpage,$rowno);
		$query = $this->db->get();
		//  echo $this->db->last_query(); exit;
		//echo '<pre>'; print_r($query->result_array()); die('model');
		
		if($query->num_rows() > 0){
			
			$return = $query->result_array();
			/*if($type == 'active'){
				$return['view_link'] = '<a href="'.base_url().'hired-job-details/'.$singl['user_task_id'].'">Details</a>';
			}else{
				$return['view_link'] = '<a href="'.base_url().'job-details/'.$singl['user_task_id'].'">Details</a>';
			}*/
			
			
			return $return;
		}else{
			return array();
		}
	}
	
	public function check_saved_task($task_id = '', $user_id = ''){
				
		if($task_id != ''){
			$this->db->select('id');
			$this->db->from('task_saved');
			$this->db->where('task_saved.user_id', $user_id);
			$this->db->where('task_saved.task_id', $task_id);
			$query = $this->db->get(); 
			if($query->num_rows() > 0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public function check_inappropriate_task($task_id = '', $user_id = ''){
				
		if($task_id != ''){
			$this->db->select('task_inappropriate_id');
			$this->db->from('task_inappropriate');
			$this->db->where('task_inappropriate.freelancer_id', $user_id);
			$this->db->where('task_inappropriate.task_id', $task_id);
			$query = $this->db->get(); 
			if($query->num_rows() > 0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public function save_user_jobs($task_user_id){
		$task_id = $this->db->select('task_id')->from('task')->where('user_task_id',$task_user_id)->get()->row()->task_id;
		$user_id = $this->session->userdata('user_id');		
		$inarr = array(
			'user_id' => $user_id,
			'task_id' => $task_id,
			'task_user_id' => $task_user_id,
			'task_save_status' => 'I',
			'doc' => date('Y-m-d H:i:s'),
			'dom' => date('Y-m-d H:i:s'),
			'status' => 1
		);
		// check already exist or not
		$this->db->select('id')->from('task_saved')->where('task_id',$task_id)->where('user_id',$user_id);
		$check = $this->db->get();
				
		if($check->num_rows() > 0){
			$return = $this->db->where('user_id',$user_id)->where('task_id',$task_id)->update('task_saved',$inarr);	
			return true;
		}else{
			$return = $this->db->insert('task_saved', $inarr);
			if($return){ 
				return true;
			}else{
				return false;
			}
		}
	}
	public function save_inappropriate_task($task_user_id){
		$task_id = $this->db->select('task_id')->from('task')->where('user_task_id',$task_user_id)->get()->row()->task_id;
		$user_id = $this->session->userdata('user_id');		
		$inarr = array(
		    'task_id' => $task_id,
			'freelancer_id' => $user_id,			
			'task_user_id' => $task_user_id			
		);
		// check already exist or not
		$this->db->select('task_inappropriate_id')->from('task_inappropriate')->where('task_id',$task_id)->where('freelancer_id',$user_id);
		$check = $this->db->get();
				
		if($check->num_rows() > 0){
		}else{
			$return = $this->db->insert('task_inappropriate', $inarr);
			if($return){ 
				return true;
			}else{
				return false;
			}
		}
	}
	public function saved_job_list_count(){
		$user_id = $this->session->userdata('user_id');
		
		$this->db->select('task_saved.*, task.*');
		$this->db->from('task_saved');
		$this->db->join('task','task.task_id = task_saved.task_id');
		$this->db->where('user_id',$user_id);
		$query = $this->db->get();
		return $query->num_rows();
		
	}
	
	public function saved_job_list($rowperpage,$rowno){
		$user_id = $this->session->userdata('user_id');
		
		$this->db->select('task_saved.*, task.*, DATE_FORMAT(task.task_due_date, "%d/%m/%Y") as task_due_date,');
		$this->db->from('task_saved');
		$this->db->join('task','task.task_id = task_saved.task_id');
		$this->db->where('user_id',$user_id);
		$this->db->limit($rowperpage,$rowno);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return array();
		}
	}
	
	public function get_user_basic_info($user_id = ''){
		if($user_id != ''){
			$this->db->select('users.*,user_login.*');
			$this->db->from('users');
			$this->db->join('user_login','user_login.user_id = users.user_id','left');
			$this->db->where('users.user_id',$user_id);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				return $query->row_array();
			}			
		}else{
			return array();
		}
	}
	
	public function get_client_post_count_info($client_id = ''){
		if($client_id != ''){
			$this->db->where('task_created_by', $client_id); 
			$this->db->from('task'); 
			return $this->db->count_all_results();		
		}else{
			return 0;
		}
	}
	
	public function get_hire_info($task_id = '',$freelancer_id = 0){
		if($task_id != ''){
			$this->db->select('task_hired.*');
			$this->db->from('task_hired');
			$this->db->where('task_id',$task_id);
			$this->db->where('freelancer_id', $freelancer_id);
			$this->db->where('hired_status', 1);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				return $query->row_array();
			}else{
				return array();
			}
		}
	}
	
	
	public function get_task_milestone_list($task_id = 0, $hired_id = 0){
		if($hired_id != 0){
			$this->db->select('task_hired_milestone.*,task_hired.*,DATE_FORMAT(task_hired_milestone.milestone_end_date, "%d/%m/%Y") as milestone_end_date');
			$this->db->from('task_hired_milestone');
			$this->db->join('task_hired','task_hired.hired_id = task_hired_milestone.hired_id');
			$this->db->where('task_hired.task_id',$task_id);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				return $query->result_array();
			}else{
				return array();
			}
		}
	}
	
	
}