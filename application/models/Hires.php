<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hires extends CI_Model {

	public function __construct(){

		parent::__construct();

        $this->load->model('Users');

	}
	
	public function get_task_data_by_user(){
		$user_id = $this->session->userdata('user_id');
		
	//	$current_datetime = date('Y-m-d H:i:s',NOW());
		
		$this->db->where('task_created_by',$user_id);
		//$this->db->where('task_due_date >= ','CURDATE()');
		$this->db->where('task_is_ongoing',0);
		$this->db->where('task_status',1);
		$this->db->select('*');
		$this->db->from('task');
		$query = $this->db->get();
		
		if($query->num_rows() >0){
			return $query->result();
		}else{
			return array();
		}
	}

	public function hire_data_insert($postValue = array()){
        
		// echo '<pre>';	print_r($postValue['milestone_end_date']);
		  
		//echo date('Y-m-d H:i:s',strtotime($postValue['milestone_end_date'][2]));
	/* 	 $date = date_parse_from_format("m-d-Y", $postValue['milestone_end_date'][1]);
		 
		 $m= $date['year']."-".$date["month"]."-".$date["day"];
		 
		 echo date('Y-m-d H:i:s',strtotime($m));

		 echo "<pre/>";
		 print_r($date);
		 die('model'); */
		
		if(empty($postValue)){
        	return array('status' => FALSE, 'message' => 'invalid_data');
		}else{
			// get task_id by user_task_id
			$get_info = $this->Tasks->get_task_info_by_user_task_id($user_task_id = $postValue['fldJobTitle']);
			if(!empty($get_info)){
				$task_id = $get_info->task_id;
				$task_budget = $get_info->task_total_budget;
			}
			
			// check already hired
			$checkData = $this->db->select('hired_id')->from('task_hired')->where('task_id',$task_id)->where('freelancer_id',$postValue['freelancer_id'])->get();
			
			if($checkData->num_rows() == 0){
				$date_of_creation = date("Y-m-d H:i:s");
				if($postValue['terms'] == 'pay_by_milestone'){
					$deposit = $postValue['deposite_milestone'];
				}else{
					$deposit = $postValue['deposit'];
				}

				if($postValue['task_duration_type'] == "Hourly") {
					$madu = "hours";
				} else if($postValue['task_duration_type'] == "Daily") {
					$madu = "days";
				} else if($postValue['task_duration_type'] == "Monthly"){
					$madu = "months";
				} else {
					$madu = "years";
				}

				$his = date("H:i:s");
				$hire_start_date = date("Y-m-d $his", strtotime($postValue['hire_date']));
				$mdur = "+".$postValue['task_duration']." ".$madu;
				$hired_end_date = date("Y-m-d H:i:s", strtotime($mdur, strtotime($hire_start_date)));				
							
				$data = array(
					'task_id' => $task_id,
					'user_task_id' => $postValue['fldJobTitle'],
					'freelancer_id' => $postValue['freelancer_id'],
					'hired_title' => $postValue['contract_title'],
					'agreed_term' => $postValue['terms'],
					'agreed_budget' => $task_budget,
					'term_amount' => $postValue['amount'],
					'deposit' => $deposit,
					// 'hired_end_date' => date('Y-m-d H:i:s',strtotime($postValue['due_date'])),
					'hire_date' => $hire_start_date,
					'hired_end_date' => $hired_end_date,
					'hired_details' => $postValue['hire_details'],
					'hired_doc' => $date_of_creation,
					'hired_dom' => $date_of_creation,
				);
				
				if($postValue['terms'] == 'pay_by_milestone'){
					if(isset($postValue['milestone_title'])){
						$this->db->insert('task_hired',$data);
						$insert_id = $this->db->insert_id();
						
						foreach($postValue['milestone_title'] as $key => $row){		 //echo $key; echo $row;	
							$date = date_parse_from_format("m-d-Y", $postValue['milestone_end_date'][$key]);
							$milestone_date=$date['year']."-".$date["month"]."-".$date["day"];
							$data_milestone = array(
								'hired_id' => $insert_id,
								'milestone_title' => $postValue['milestone_title'][$key],
								'milestone_end_date' => date('Y-m-d H:i:s',strtotime($milestone_date)),
								'milestone_agreed_budget' => $postValue['milestone_agreed_budget'][$key],
								'milestone_doc' => $date_of_creation,
								'milestone_created_by' => $this->session->userdata('user_id')
							);
							
							//echo '<pre>'; print_r($data_milestone);
							
							$this->db->insert('task_hired_milestone',$data_milestone);
						}
					}else{
						return array('status' => FALSE, 'message' => 'Something Wrong');
					}
				}else{
					$this->db->insert('task_hired',$data);
					$insert_id = $this->db->insert_id();
					
					$data_milestone = array(
						'hired_id' => $insert_id,
						'milestone_title' => $postValue['contract_title'],
						'milestone_end_date' => $hired_end_date,
						'milestone_agreed_budget' => $postValue['amount'],
						
						'milestone_approval_date' => $date_of_creation,
						'milestone_doc' => $date_of_creation,
						'milestone_created_by' => $this->session->userdata('user_id')
					);
					
					//echo '<pre>'; print_r($data_milestone);
					
					$this->db->insert('task_hired_milestone',$data_milestone);					
				}
				
				// project title
				$task_query = $this->db->select('task.*')->from('task')->where('task_id',$task_id)->get();
				if($task_query->num_rows() >0){
					$task_info = $task_query->row();
					$task_name = $task_info->task_name;
					$user_task_id = $task_info->user_task_id;
				}else{
					$task_name = $user_task_id = '';
				}
				
				$job_details_link = '<a href="'.base_url().'hired-job-details/'.$user_task_id.'">'.$task_name.'</a>';
				
				// insert notification
				$notidata = array(
					'task_id' => $task_id,
					'offer_id' => 0,
					'notification_master_id' => 11,
					'notification_from' => $this->session->userdata('user_id'),
					'notification_to' => $postValue['freelancer_id'],
					'notification_details' => 'SEND HIRED ',
					'notification_message' => '<strong>'.'<a href="'.base_url().'public-profile/'.$this->session->userdata('profile_id').'">'.$this->session->userdata('user_name').'</a></strong> wants to hire you for <strong> '.$job_details_link.' </strong>',
					'notification_doc' => date('Y-m-d H:i:s')
				);
				$this->db->insert('task_notification',$notidata);
				
				return array('status' => TRUE, 'message' => 'Hire request has been sent successfully.');
			}else{
				return array('status' => FALSE, 'message' => 'Request has been sent already.');
			}
		}
    } 
	
	
	public function get_freelancer_info_by_id($freelancer_id = 0) {
        $freelancerList = array();
		        
        $this->db->select('users.*,user_login.*,country.name as country');
        $this->db->from('users');
        $this->db->join('user_login', 'user_login.user_id = users.user_id');
		$this->db->join('country','country.country_id = users.country', 'left');
        $this->db->where('user_login.user_type', 4);
        $this->db->where('user_login.status', 1);    
        $this->db->where_in('users.user_id',$freelancer_id);            
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
			
			$row = $query->row_array();
			
            $user_languages = $user_skills = array();

            // Get user selected languages
            $this->db->select('user_languages.language_id,languages.name');
            $this->db->from('user_languages');
            $this->db->join('languages', 'languages.language_id = user_languages.language_id');
            $this->db->where('user_languages.user_id', $freelancer_id);
            $query_lang = $this->db->get();
            foreach ($query_lang->result() as $row_lang){
                $user_languages[] = $row_lang->name;
            } 

            // Get user selected skills
            $this->db->select('user_area_of_interest.area_of_interest_id,area_of_interest.name,user_area_of_interest.user_id');
            $this->db->from('user_area_of_interest');
            $this->db->join('area_of_interest', 'area_of_interest.area_of_interest_id = user_area_of_interest.area_of_interest_id');
            $this->db->where('user_area_of_interest.user_id', $freelancer_id);
            $query_skill = $this->db->get();
            foreach ($query_skill->result() as $row_skill){
                $user_skills[] = array('skill_id' => $row_skill->area_of_interest_id, 'skill_name' => $row_skill->name, 'user_id' => $row_skill->user_id);
            }
			
			

            $freelancerData = array('basic_info' => $row, 'user_selected_languages' => $user_languages, 'user_selected_skills' => $user_skills);
        }
		
        return $freelancerData; 
	}
	
	
	
	
	public function get_client_info_by_id($user_id = 0) {
        $freelancerList = array();
		        
        $this->db->select('users.*,user_login.*,country.name as country');
        $this->db->from('users');
        $this->db->join('user_login', 'user_login.user_id = users.user_id');
		$this->db->join('country','country.country_id = users.country', 'left');
        $this->db->where('user_login.user_type', 3);
        $this->db->where('user_login.status', 1);    
        $this->db->where_in('users.user_id',$user_id);            
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
			
			$row = $query->row_array();
			
            $user_languages = $user_skills = array();

            // Get user selected languages
            $this->db->select('user_languages.language_id,languages.name');
            $this->db->from('user_languages');
            $this->db->join('languages', 'languages.language_id = user_languages.language_id');
            $this->db->where('user_languages.user_id', $user_id);
            $query_lang = $this->db->get();
            foreach ($query_lang->result() as $row_lang){
                $user_languages[] = $row_lang->name;
            } 

            // Get user selected skills
            $this->db->select('user_area_of_interest.area_of_interest_id,area_of_interest.name,user_area_of_interest.user_id');
            $this->db->from('user_area_of_interest');
            $this->db->join('area_of_interest', 'area_of_interest.area_of_interest_id = user_area_of_interest.area_of_interest_id');
            $this->db->where('user_area_of_interest.user_id', $user_id);
            $query_skill = $this->db->get();
            foreach ($query_skill->result() as $row_skill){
                $user_skills[] = array('skill_id' => $row_skill->area_of_interest_id, 'skill_name' => $row_skill->name, 'user_id' => $row_skill->user_id);
            }
			
			

            $freelancerData = array('basic_info' => $row, 'user_selected_languages' => $user_languages, 'user_selected_skills' => $user_skills);
        }
		
        return $freelancerData; 
	}
	
	public function get_contract_details_by_id($hired_id = 0){
		$hired_id = base64_decode($hired_id);
		$total_fund_deposited = $total_fund_spend = 0;
		if($hired_id != 0){
			$this->db->select('task_hired.*');
			$this->db->from('task_hired');
			$this->db->where('hired_id',$hired_id);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				$return['contract_details'] = $query->row_array();
			}
		}else{
			$return['contract_details'] = array();
		}
		return $return;
	}
	
	public function get_contract_details_by_task_id($task_id = 0){

		if($task_id != 0){
			$this->db->select('task_hired.*');
			$this->db->from('task_hired');
			$this->db->where('task_id',$task_id);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				$return['contract_details'] = $query->row_array();
			}
		}else{
			$return['contract_details'] = array();
		}
		return $return;
	}
	
	
	
	public function get_contract_details($hired_id = 0){
		$hired_id = base64_decode($hired_id);
		$total_fund_deposited = $total_fund_spend = 0;
		if($hired_id != 0){
			$this->db->select('task_hired.*');
			$this->db->from('task_hired');
			$this->db->where('hired_id',$hired_id);
			$query = $this->db->get();
			if($query->num_rows() > 0){
				$return['contract_details'] = $query->row_array();
				
				$this->db->select('task_hired_milestone.*');
				$this->db->from('task_hired_milestone');
				$this->db->where('hired_id',$hired_id);
				$this->db->where('milestone_is_deleted',0);
				$query2 = $this->db->get();
				
				$return['contract_details']['milestone_cnt'] = $query2->num_rows();
				
				if($query2->num_rows() >0){
					$milestonedata = $query2->result_array();
					
					foreach($milestonedata as $row){
						
						if($row['milestone_agreed_budget'] > $row['milestone_fund_deposited']){
							$funddepositelink = '<a class="RNR DF" href=""> Deposit Fund</a>';
						}else{
							$funddepositelink = 'Fund Deposited';
						}
						
						
						$status = ''; $payment = '';
						
						if($row['milestone_current_status'] == 'NR'){
							$status = '<big> No Request Yet </big>';
							$payment = $funddepositelink;
						}else if($row['milestone_current_status'] == 'FS'){
							$status = '<small> Requested for payment </small><p> Requested: '.date('d/m/Y',strtotime($row['milesone_payment_request_date'])) .'</p>';
							$payment = '<a class="RNR" href="'.base_url().'release-approve/'.base64_encode($row['milestone_id']).'">Review & Release</a>';
						}else if($row['milestone_current_status'] == 'RC'){
							$status = '<small><i class="fa fa-undo"></i> Change Request Send</small>';
							$payment = '';
						}else if($row['milestone_current_status'] == 'AR'){
							$status = '<span> <i class="fa fa-check-circle"></i> Approved </span>';
							$payment = 'Released';
							$total_fund_spend = $total_fund_spend + $row['milestone_agreed_budget'];
						}else if($row['milestone_current_status'] == 'CC'){
							$status = 'Contract Closed';
							$payment = '';
						}
						
						
						$details[] = array(
							'title' => $row['milestone_title'],
							'milestone_id' => $row['milestone_id'],
							'due_date' => date('d/m/Y',strtotime($row['milestone_end_date'])),
							'amount' => $row['milestone_agreed_budget'],
							'status' => $status,
							'payment_status' => $payment,
							'milestone_fund_deposited' => $row['milestone_fund_deposited'],
							'milestone_approval_date' => $row['milestone_approval_date'],
							'milesone_payment_request_date' => date('d/m/Y',strtotime($row['milesone_payment_request_date'])),
							'request_change_in_milestone' => $row['request_change_in_milestone'],
							'milestone_status' => $row['milestone_status'],
							'milestone_is_deleted' => $row['milestone_is_deleted']
						);
						
						$total_fund_deposited = (int)$total_fund_deposited + (int)$row['milestone_fund_deposited'];
						
						
					}
					$return['contract_details']['contract_total_budget_escrow'] = $total_fund_deposited;
					$return['contract_details']['contract_total_budget_spend'] = $total_fund_spend;
					
					$return['milestone_details'] = $details;
					
				}else{
					$return['contract_details']['contract_total_budget_escrow'] = 0;
					$return['contract_details']['contract_total_budget_spend'] = 0;
					$return['milestone_details'] = array();
				}
				return $return;
			}else{			
				return array();
			}
		}else{
			return array();
		}
	}
	
	public function get_old_hire_list($user_id = ''){
		$this->db->select('task_hired.*, task.*, users.*,user_login.*, country.name as country');
		$this->db->from('task_hired');
		$this->db->join('task','task.task_id = task_hired.task_id');
		$this->db->join('users','users.user_id = task_hired.freelancer_id');
		$this->db->join('user_login','user_login.user_id = users.user_id');
		$this->db->join('country','country.country_id = users.country','left');
		
		$this->db->where('task.task_created_by',$user_id);
		$this->db->group_by('users.user_id');
		$query = $this->db->get();
		//echo $this->db->last_query();die;
		if($query->num_rows() >0){
			return $query->result();
			
		}else{
			return array();
		}
	}
	
	public function update_data($data = array()){
		
		if(!empty($data)){
			$updatedata = array(
				$data['updateField'] => $data['updateData'],
				'milestone_dom' => date('Y-m-d H:i:s')
			);			
			$return = $this->db->where($data['checkField'],$data['checkVal'])->update($data['updateTable'],$updatedata);
			return $return;
		}else{
			return 0;
		}	
	}
	
	public function add_new_milestone($data){
		if(!empty($data)){
			$addmoreArr = array(
				'hired_id' => $this->input->post('frmAddNewMilestone_Contract_id'),
				'milestone_title' => $this->input->post('fldMilestoneTitle'),
				'milestone_end_date' => date('Y-m-d H:i:s',strtotime($this->input->post('fldMilestoneDueDate'))),
				'milestone_agreed_budget' => $this->input->post('fldMilestoneAmount'),
				'milestone_doc' => date('Y-m-d H:i:s'),
				'milestone_created_by' => $this->session->userdata('user_id')
			);
			$return = $this->db->insert('task_hired_milestone',$addmoreArr);
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	public function get_milestone_details_by_id($milestone_id = 0){
		if($milestone_id != ''){
			$this->db->select('task_hired_milestone.*,task_hired.*');
			$this->db->from('task_hired_milestone');
			$this->db->join('task_hired','task_hired.hired_id = task_hired_milestone.hired_id');
			$this->db->where('milestone_id', base64_decode($milestone_id));
			$query = $this->db->get();
			
			if($query->num_rows() > 0){
				return $query->row_array();
			}else{
				return array();
			}
		}else{ 
			return array();
		}
	}
	
	public function submit_approval($data = array()){
		
		$currentdate = date('Y-m-d H:i:s');
		$milestone_id = base64_decode($milestone_id);
		
		if(!empty($data)){
			$milestone_id = base64_decode($data['milestone_id']);
			
			if($data['radio'] == 'AR'){
				$updateArr = array(
					'milestone_current_status' => 'AR',
					'milestone_approval_date' => $currentdate,
					'milestone_dom' => $currentdate
				);
			}else if($data['radio'] == 'RC'){
				$updateArr = array(
					'milestone_current_status' => 'RC',
					'request_change_in_milestone' => 1,
					'milestone_review_message' => $data['change_request_msg'],
					'milestone_dom' => $currentdate,
				);
			}
			$return = $this->db->where('milestone_id',$milestone_id)->update('task_hired_milestone',$updateArr);
			if($return){
				return TRUE;
			}else{
				return FALSE;
			}	
		}
	}
	
	public function close_contract_update($data = array()){
		$this->load->model("Reviews");
		$this->load->model("Notifications");
		
		/* $current_dttm = date('Y-m-d H:i:s');
		$fldFreelancerID = $data['fldFreelancerID'];
		// get previous coin data
		$prevData = $this->db->select('total_positive_coins,total_negative_coins,total_coins')->from('user_login')->where('user_id', $fldFreelancerID)->get();
		if($prevData->num_rows() > 0){
			$userinfo = $prevData->row_array();
			$prv_total_coins = $userinfo['total_coins'];
			$prv_positive_coins = $userinfo['total_positive_coins'];
			$prv_negative_coins = $userinfo['total_negative_coins'];
			
		}else{
			$prv_total_coins = $prv_positive_coins = $prv_negative_coins = 0;
		}
		
		if($data['coin'] == 'complete'){
			$val = 2;
			$master['total_positive_coins'] = $prv_positive_coins + $val;
		}else{
			$val = -2;
			$master['total_negative_coins'] = $prv_negative_coins + $val;
		} */
		
		/* $updateData = array(
			'hire_is_completed' => 1,
			'hire_final_status' => 'CC',
			'hire_coin_to_freelancer' => $val,
			'hired_is_open' => 1,
			'hire_client_review' => $data['fldContractReview'],
			'hired_dom' => $current_dttm
		); */
				
		/* $newcoin = $prv_total_coins + $val; 
		$master['total_coins'] = $newcoin;
		*/
		
		//echo $data['fldContractID'];
	//	 echo '<pre>'; print_r($updateData); print_r($master); die;
		
		/* $return = $this->db->where('hired_id',$data['fldContractID'])->update('task_hired',$updateData);
		$this->db->where('task_id',$task_id)->update('task',array('task_status'=>'1','task_hired'=>'1','task_is_complete'=>'1','task_completion_date'=>date('Y-m-d H:i:s')));
		$this->db->where('user_id',$fldFreelancerID)->update('user_login',$master); */
		
	
		$fldFreelancerID = $data['fldFreelancerID'];
		$coin=$data['coin'];
		//-----------------------
		$taskData = $this->db->select('*')->from('task_hired')->where('hired_id', $data['fldContractID'])->get();
		$taskinfo = $taskData->row_array();
		$task_id=$taskinfo['task_id'];
		$offer_id=0;
		//------------------------
		if($data['action']=="complete"){
			$action_id="16";
			$this->db->query("UPDATE task SET task_completed_by_owner=1 WHERE task_id='".$data['fldTaskID']."'");
		}else{
			$action_id="18";
		}
		$notification_from=$this->session->userdata('user_id');
		$prevData = $this->db->select('user_id')->from('user_login')->where('user_id', $fldFreelancerID)->get();
		$userinfo = $prevData->row_array();
		$notification_to = $userinfo['user_id'];
				
		$query_notification = $this->db->select('*')->from('notification_type')->where('NOTIFICATION_TYPE_ID',$action_id)->get();		
		
		
		$task_query = $this->db->select('task.*')->from('task')->where('task_id',$data['fldTaskID'])->get();
		if($task_query->num_rows() >0){
			$task_info = $task_query->row();
			$task_name = $task_info->task_name;
			$user_task_id = $task_info->user_task_id;
		}else{
			$task_name = $user_task_id = '';
		}
		
		$title="";
		$message="";
		if(!empty($query_notification)){
			
			$masterInfo=$query_notification->row();
			
			$title = $masterInfo->TITLE;
			$message = $masterInfo->MESSAGE;
		}		
		
		if($coin==0){
			$this->db->query("UPDATE user_login SET total_coins=total_coins-2,total_negative_coins=total_negative_coins-2 WHERE user_id='".$fldFreelancerID."'");
					
		}else if($coin==-1){
			$this->db->query("UPDATE user_login SET total_coins=total_coins-1,total_negative_coins=total_negative_coins-1 WHERE user_id='".$fldFreelancerID."'");
			
		}else if($coin==1){
			$this->db->query("UPDATE user_login SET total_coins=total_coins+1,total_positive_coins=total_positive_coins+1 WHERE user_id='".$fldFreelancerID."'");
			
		}else if($coin==2){
			$this->db->query("UPDATE user_login SET total_coins= total_coins + 2,total_positive_coins= total_positive_coins + 2 WHERE user_id='".$fldFreelancerID."'");
			
		}
		//-------------------PM
		$action_id=20;		
		$show_review="";
		$checkreview=$this->db->query("SELECT * FROM reviews WHERE review_provided_by='".$fldFreelancerID."' AND review_received='".$this->session->userdata('user_id')."' AND taskid='".$task_id."'");
	    if($checkreview->num_rows()>0){
			$show_review=1;
			$r=$checkreview->row();
			$this->db->query("UPDATE reviews SET show_review=1 WHERE review_provided_by='".$fldFreelancerID."' AND review_received='".$this->session->userdata('user_id')."' AND taskid='".$task_id."'");
			
			$sender=$this->db->select('name')->from('users')->where('user_id',$fldFreelancerID )->get()->row();
			
			/*$rmessage='<strong>'.$sender->name.'</strong> has been completed the task and sent review and  '.$r->coins.' coins given for the project.<a href="'.base_url().'reviews">View Reviews</a>';*/

			$rmessage='<strong>'.$sender->name.'</strong> has been completed the task and sent review given for the project.<a href="'.base_url().'reviews">View Reviews</a>';
		    $title="REVIEW";
		    $insert = array(
					'task_id' => $task_id,
					'offer_id' => $offer_id,
					'notification_master_id' => $action_id,
					'notification_from' =>$fldFreelancerID,
					'notification_to' =>$this->session->userdata('user_id') ,
					'notification_details' => $title,
					'notification_message' => $rmessage,
					'notification_doc' => date('Y-m-d H:i:s')
				);				
		   $this->Notifications->insert_notification('task_notification',$insert);
		}else{
			$show_review=0;
			}
			//--------------PM
			
		$job_details_link = '<a href="'.base_url().'hired-job-details/'.$user_task_id.'">'.$task_name.'</a>';
		$insert = array(
				'task_id' => $task_id,
				'offer_id' => $offer_id,
				'notification_master_id' => $action_id,
				'notification_from' => $notification_from,
				'notification_to' => $notification_to,
				'notification_details' => $title,
				'notification_message' => '<strong>'.$this->session->userdata('user_name').'</strong> '.$message.' and send review for the project <strong>'.$job_details_link.'</strong>',
				'coins'=>$data['coin'],
				'review'=>$data['fldContractReview'],
				'notification_doc' => date('Y-m-d H:i:s')
			);
			
			// echo "<pre/>";print_r($insert);exit;
			
		$this->db->insert('task_notification',$insert);
		
		$data_review=array(
			'review_provided_by'=>$this->session->userdata('user_id'),
			'review_received'=>$fldFreelancerID,
			'review_provided'=>$this->input->post('fldContractReview'),
			'review_provided_on'=>date("Y-m-d H:i:s"),
			'show_review'=>$show_review,
			'review_doc'=>date("Y-m-d H:i:s"),
		    'taskid'=>$task_id,
			'coins'=>$coin
		);
		
       $return=$this->Reviews->insert_review($data_review);		 	
		
		if($return){
			return TRUE;
		}else{
			return FALSE;
		}	
		
	}
}