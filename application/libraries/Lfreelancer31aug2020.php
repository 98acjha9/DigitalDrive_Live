<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lfreelancer {
	
	public function dashboard_page(){
		$CI =& get_instance();
		$CI->load->model('Users');
        $CI->load->model('Tasks');
		$CI->load->model('Freelancers');
        $CI->load->library("pagination");
		
		
		// Search text
		$searchValue = "";
		 $searchValue = $CI->input->post('search');
		/* if($CI->input->post('search') != NULL ){
		  $searchValue = $CI->input->post('search');
		  $CI->session->set_userdata(array("searchValue"=>$searchValue));
		}else{
		  if($CI->session->userdata('searchValue') != NULL){
			$searchValue = $CI->session->userdata('searchValue');
		  }
		} */
		
		// Row per page
		$rowperpage = PER_PAGE;
		// Row position
		
		if($CI->uri->segment(2)){
			$rowno = ($CI->uri->segment(2));
		}
		else{
			$rowno = 1;
		}
		
		if($rowno != 0){
		  $rowno = ($rowno-1) * $rowperpage;
		}
		
		// Pagination Configuration
		$config['base_url'] = base_url().'freelancer-dashboard';
		$config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = true;
		$config['first_tag_open'] = '<li class="previous">';
		$config['first_tag_close'] = '</li>';  
		$config['last_link'] = true;
		$config['first_tag_open'] = '<li class="next">';
		$config['first_tag_close'] = '</li>'; 
		$config['next_link'] = 'Next';
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = '</li>';  
		$config['prev_link'] = 'Previous';
		$config['prev_tag_open'] = '<li class="previous">';
		$config['prev_tag_close'] = '</li>'; 
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>'; 
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		
		$config['use_page_numbers'] = TRUE;
		$config['total_rows'] = $CI->Freelancers->get_freelancer_dashboard_count($searchValue);
		$config['per_page'] = $rowperpage;
		
		$CI->pagination->initialize($config);
		
		$data['links'] = $CI->pagination->create_links();
		
		$data['job_array'] = $CI->Freelancers->get_freelancer_dashboard_data($searchValue,$rowperpage,$rowno);
		
		$userData = $CI->Users->get_user_profile_info_by_id($CI->session->userdata('user_id'));
		
		$userLoginData = $CI->Users->getUserLoginData('user_id', $CI->session->userdata('user_id'));
		if(!empty($userLoginData)){
			$connections = $userLoginData->total_connects;
			$total_positive_coins = $userLoginData->total_coins;
			$total_negative_coins = $userLoginData->total_negative_coins;
		}else{
			$connections = $total_positive_coins = $total_negative_coins = 0;
		}
		
		$user_profile_image = $CI->session->userdata('user_image');
		
		if(empty($user_profile_image)) {
			$user_profile_image = base_url('assets/img/no-image.png');
		}else{
			$user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);	    	
		}

		if(!empty($userData)) {
			$spend_by_user = $CI->Tasks->get_user_total_spend($userData['basic_info']->user_id);
			
			$userInfo = array('id' => $userData['basic_info']->user_id, 'name' => $userData['basic_info']->name, 'country' => $userData['basic_info']->country, 'gender' => $userData['basic_info']->gender, 'date_of_birth' => $userData['basic_info']->date_of_birth, 'bio' => $userData['basic_info']->bio, 'address' => $userData['basic_info']->address, 'state' => $userData['basic_info']->state, 'city' => $userData['basic_info']->city, 'vat' => $userData['basic_info']->vat, 'user_languages' => implode(', ', $userData['user_selected_languages']), 'user_skills' => $userData['user_selected_skills'], 'user_image' => $user_profile_image, 'spend_by_user' => $spend_by_user,'total_positive_coins'=> $total_positive_coins,'total_negative_coins'=> $total_negative_coins, 'connections' => $connections);

		}
		$data['user_info'][] = $userInfo;
		//echo '<pre>'; print_r($data);die();
		$AccountForm = $CI->parser->parse('freelancer/dashboard',$data,true);
		return $AccountForm;
	}
	
	public function key_list($rowno = ''){
		
		$CI =& get_instance();
		$CI->load->model('Users');
        $CI->load->model('Tasks');
		$CI->load->model('Freelancers');
        $CI->load->library("pagination");
		$data = $freelancerInfo = $arrCountry =  $arrSkills = $jobsData = array();
		
		// Search text
		$searchValue = "";
		if($CI->input->post('search') != NULL ){
		  $searchValue = $CI->input->post('searchValue');
		  $CI->session->set_userdata(array("searchValue"=>$searchValue));
		}else{
		  if($CI->session->userdata('searchValue') != NULL){
			$searchValue = $CI->session->userdata('searchValue');
		  }
		}
		
		// Row per page
		$rowperpage = PER_PAGE;
		// Row position
		
		if($CI->uri->segment(2)){
			$rowno = ($CI->uri->segment(2));
		}
		else{
			$rowno = 1;
		}
		
		
		if($rowno != 0){
		  $rowno = ($rowno-1) * $rowperpage;
		}
		// All records count
		$allcount = $CI->Freelancers->search_jobs_by_keyword_count($searchValue);		
		// Get records
		$taskList = $CI->Freelancers->search_jobs_by_keyword($rowno,$rowperpage,$searchValue);
		
		//echo '<pre>'; print_r($taskList); die;
		
		
		if(!empty($taskList)){
			foreach($taskList as $row){ 
				$skillsArr['skill_name_show'] = array();
				
				$jobsData[] = array(
					'task_id' => $row['basic_info']['task_id'],
					'user_task_id' => $row['basic_info']['user_task_id'],
					'task_name' => ucfirst($row['basic_info']['task_name']),
					'task_details' => $row['basic_info']['task_details'],
					'task_total_budget' => $row['basic_info']['task_total_budget'],
					'task_requirements' => $row['task_requirements'],
					'task_origin_location' => $row['basic_info']['task_origin_location'],
					'task_origin_country' => $row['basic_info']['task_origin_country'],
					'task_doc' => date('d M Y',strtotime($row['basic_info']['task_doc'])),
					'offer_count' => $row['basic_info']['offer_count']
				);
			}
		}
		
		
		
		// Pagination Configuration
		$config['base_url'] = base_url().'key-list';
		$config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = true;
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';  
        $config['last_link'] = true;
        $config['first_tag_open'] = '<li class="next">';
        $config['first_tag_close'] = '</li>'; 
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="next">';
        $config['next_tag_close'] = '</li>';  
        $config['prev_link'] = 'Previous';
        $config['prev_tag_open'] = '<li class="previous">';
        $config['prev_tag_close'] = '</li>'; 
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>'; 
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
		
		
		$config['use_page_numbers'] = TRUE;
		$config['total_rows'] = $allcount;
		$config['per_page'] = $rowperpage;
		
		$CI->pagination->initialize($config);
		
		$data['links'] = $CI->pagination->create_links();
		$data["jobs"] = $jobsData;
		$data['row'] = $rowno;
		$data['search'] = $searchValue;





		// echo '<pre>'; print_r($data["jobs"]);die;
		
		$AccountForm = $CI->parser->parse('freelancer/key-list',$data,true);
		return $AccountForm;
	}
	
	public function freelancer_job_details($taskID = ''){

        $CI =& get_instance();
        $CI->load->model('Tasks');
		$CI->load->model("Users");
		$CI->load->model("Freelancers"); 
		$CI->load->model('Continent');
		$CI->load->model('Countries');

        $data = $arrTask = $arrOfferSend = $commentArr = array();

        $task_details = $CI->Tasks->get_task_info_by_user_task_id($taskID);
		
        if(!empty($task_details)){
            $task_id = $task_details->task_id;
			$task_created_by = $task_details->task_created_by;
		}else{
			$task_id = $task_created_by = ''; 
		}
		
		$savedcheck = $CI->Freelancers->check_saved_task($task_id, $CI->session->userdata('user_id'));
		if($savedcheck){
			$data['savetext'] = '<span id="">Already Saved</span>';
			$data['savetextclass'] = 'HireBtn';
		}else{
			$data['savetext'] = '<span id="txtShow">Save This Job</span>';
			$data['savetextclass'] = 'HireBtn saveBtn';
		}
		
		$savedinappropriate = $CI->Freelancers->check_inappropriate_task($task_id, $CI->session->userdata('user_id'));
		if($savedinappropriate){
			$data['inappropriatetext'] = '<span id="">Already Flaged This Job As an inappropriate</span>';
			$data['inappropriateclass'] = 'flb';
		}else{
			$data['inappropriatetext'] = '<span id="txtShow">Flag This Job As an inappropriate</span>';
			$data['inappropriateclass'] = 'flb flagBtn';
		}
		
		// get freelancer connection 
		$getcoonects = $CI->Freelancers->get_user_basic_info($CI->session->userdata('user_id'));
		if(!empty($getcoonects)){
			$data['connection'] = $getcoonects['total_connects'];
		}else{
			$data['connection'] = 0;
		}
		
		$data['offer_send'] = $CI->Freelancers->job_offer_user($task_id);
		
		$data['proposal_count'] = $CI->Freelancers->proposal_count($task_id, $count = '');
		
		$tablename = array('comment_master','users','user_login');
		$jointype = array('left','left');
		$joincondition = array('comment_master_alias.user_id=users_alias.user_id','comment_master_alias.user_id=user_login_alias.user_id');
		$condition = 'comment_master_alias.tast_user_id="'.$taskID.'"';
		$fieldArr		= array('*','*','*');
		$limit			= "all";
		$oderby			= 'comment_master_alias.id desc';

		$comment_info = $CI->Tasks->getJoinDataByCondition($tablename,$jointype,$joincondition,$condition,$fieldArr,$limit,$oderby);
		if(!empty($comment_info)){
			foreach($comment_info as $row){
				if($row->profile_image != NULL){
					$img_url = base_url().'uploads/user/profile_image/'.$row->profile_image;
				}else{
					$img_url = base_url().'assets/img/user2.png';
				}
				$commentArr[] = array('user_id' => $row->user_id, 'profile_image' => $img_url, 'name' => $row->name, 'remarks' => $row->remarks);
			}
		}
		
        $task = $CI->Tasks->task_status_info_by_task_id($task_details->task_id); 
        foreach($task as $details) {
            $continent = $CI->Continent->get_continent_by_id($details['basic_info']['task_origin_location']);
            $country = $CI->Countries->get_country_by_id($details['basic_info']['task_origin_country']);

            $user_info = array();
            if(!empty($details['task_hired']) && count($details['task_hired']) > 0){
                foreach($details['task_hired'] as $freelancer_hired){
                    $user_details = $CI->Users->get_user_profile_info_by_id($freelancer_hired['receiver_id']);
                    $user_status = $CI->Users->get_user_info_by_id($freelancer_hired['receiver_id']);
                    $user_profile_image = $user_status->profile_image;
                    if(empty($user_profile_image)) {
                        $user_profile_image = base_url('assets/img/no-image.png');
                    } else {
                        $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
                    }
                    $is_login = $user_status->is_login; 
                    $user_info[] = array('freelancer_id' => $user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'is_online' => (($is_login == '1')?'<span> </span>':''));

                }
            }

            

            $datetime1 = strtotime($details['basic_info']['task_doc']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);

            $years = floor($interval / (365*60*60*24));
            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    
            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 
            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     
            $minutes   = round($interval / 60);
			
			$tduration="";
			if($details['basic_info']['task_duration_type']=='Hourly')
			{
				$tduration= $details['basic_info']['task_duration'].' Hour';
			}
			else if($details['basic_info']['task_duration_type']=='Daily')
			{
				$tduration= $details['basic_info']['task_duration'].' Day';
			}
			else if($details['basic_info']['task_duration_type']=='Monthly')
			{
				$tduration= $details['basic_info']['task_duration'].' Month';
			}
			else if($details['basic_info']['task_duration_type']=='Yearly')
			{
				$tduration= $details['basic_info']['task_duration'].' Year';
			}


            $arrTask[] = array(
				'task_id' => $task_id,
				'user_task_id' => $details['basic_info']['user_task_id'], 
				'task_title' => ucfirst($details['basic_info']['task_name']), 
				'task_details' => $details['basic_info']['task_details'], 
				'task_due_date' => $details['basic_info']['task_due_date'], 
				'tasktime_duration' => $tduration, 
				'task_duration_type' => $details['basic_info']['task_duration_type'],
				'task_total_budget' => $details['basic_info']['task_total_budget'], 
				'task_doc' => date('d M Y', strtotime($details['basic_info']['task_doc'])), 
				'task_continent' => $continent->name, 
				'task_country' => $country->name, 
				'task_duration' => $minutes, 
				'task_attachments' => $details['task_attachments'], 
				'task_requirements' => $details['task_requirements'], 
				'task_freelancer_hire' => count($details['task_hired']), 
				'task_freelancer_hired_details' => $user_info, 
				'offer_send' => $arrOfferSend, 
				'commentArr' => $commentArr 
			);
        }

        // task creator info
		$creator_info = $CI->Freelancers->get_user_basic_info($task_created_by);
		$creator_post_info = $CI->Freelancers->get_client_post_count_info($task_created_by);
		
        $data['task_info'] = $arrTask; 
		
			// get milestone list data by hired id
		$getHireData = $CI->Freelancers->get_hire_info($task_id, $CI->session->userdata('user_id'));
		if(!empty($getHireData)){
				$hire_id = $getHireData['hired_id'];
				$offer_id = $getHireData['offer_id'];
				$milestoneInfo = $CI->Freelancers->get_task_milestone_list($task_id, $hire_id);
				/* echo "<pre/>";
				print_r($milestoneInfo); */
				
				$data['milestoneInfo'] = $milestoneInfo; 
			}else{
				$offer_id = $hire_id = 0;
				$data['milestoneInfo'] = array(); 
		}
		
		
		 
		$data['creator_data'][] = array('client_name'=> $creator_info['name'], 'since' => date('Y',strtotime($creator_info['doc'])), 'creator_post_count' => $creator_post_info  ); 
		
		//echo '<pre>';print_r($data);die('jjj');
			
		$AccountForm = $CI->parser->parse('freelancer/job-details',$data,true);
		return $AccountForm;
		
	}
	
	function ajax_save_job($task_user_id = ''){ 
		$CI =& get_instance();
		$CI->load->model("Freelancers"); 
		$CI->Freelancers->save_user_jobs($task_user_id);
	}
	
	public function freelancer_proposal(){
		$CI =& get_instance();
		$data = $userInfo = array();
		$CI->load->model("Tasks");
		$CI->load->model("Freelancers"); 
		$CI->load->model("Continent");
		$CI->load->model("Countries");
		
		
		$taskID = $CI->input->post('task_id');
		
		if($taskID == ''){
			redirect('key-list', 'refresh');
		}else{
			
			$details = $CI->Tasks->task_status_info_by_task_id($taskID);
			
			$user_info = $CI->Users->get_user_info_by_id($CI->session->userdata('user_id'));
			
			$continent = $CI->Continent->get_continent_by_id($details[0]['basic_info']['task_origin_location']);
			$country = $CI->Countries->get_country_by_id($details[0]['basic_info']['task_origin_country']);
			
			$datetime1 = strtotime($details[0]['basic_info']['task_doc']);
			$datetime2 = strtotime(date("Y-m-d H:i:s"));
			$interval  = abs($datetime2 - $datetime1);
	
			$years = floor($interval / (365*60*60*24));
			$months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    
			$days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			$hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 
			$minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
			$seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     
			$minutes   = round($interval / 60);
			
			$arrTask['task_info'] = array(
				'task_id' => $taskID,
				'user_task_id' => $details[0]['basic_info']['user_task_id'], 
				'task_title' => $details[0]['basic_info']['task_name'], 
				'task_details' => $details[0]['basic_info']['task_details'], 
				'task_due_date' => $details[0]['basic_info']['task_due_date'],
                'task_due_type' => $details[0]['basic_info']['task_duration_type'],				
				'task_total_budget' => $details[0]['basic_info']['task_total_budget'], 
				'task_continent' => $continent->name, 
				'task_country' => $country->name, 
				'task_duration' => $months, 
				'task_attachments' => $details[0]['task_attachments'], 
				'task_requirements' => $details[0]['task_requirements'],
				'user_connects' => $user_info->total_connects
				
			);
			
			$data['task_data'] = $arrTask;
			//print_r($details); print_r($arrTask); print_r($data['task_data']);  die('libr');
			
			$AccountForm = $CI->parser->parse('freelancer/proposal',$data,true);
			return $AccountForm;
		}
	}
	public function submit_proposal(){
		$CI =& get_instance();
		$data = $userInfo = array();
		
		$CI->load->model('Freelancers');
		$CI->load->model('Users');
		
		// proposal send or not
		$proposal_info = $CI->Freelancers->get_proposal_info($postVal = array('task_id' => $CI->input->post('task_id'), 'user_id' => $CI->session->userdata('user_id')));
		if(trim($proposal_info) == 'yes'){
			$CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Proposal Already Sent.</div>');
			redirect('key-list', 'refresh');
		}else{
			
			$user_info = $CI->Users->get_user_info_by_id($CI->session->userdata('user_id'));
			
			/* if($user_info->total_connects == 0){
				$CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Insufficient Connects.</div>');
				redirect('purchase-list', 'refresh');
			}else{ */
					
				if(!empty($_FILES) && is_array($_FILES)){  
					for($i = 0; $i<count($_FILES['fldTaskDocuments']['name']); $i++) {
						$path = $_FILES['fldTaskDocuments']['name'][$i];
						$path_parts = pathinfo($path);
						$filename = time() . $CI->session->userdata('user_id') . '_' . $path;
						$sourcePath = $_FILES['fldTaskDocuments']['tmp_name'][$i];
						$targetPath = "./uploads/proposal/".$filename;
						if(move_uploaded_file($sourcePath,$targetPath)) {
							 $postData['uploadFiles'][] = $filename;
						}
					}
				}
			 
				/*echo "<pre/>";
		print_r($_REQUEST);
		exit;  */
	
 		
			$result=	$CI->Freelancers->add_proposal($postData);
			
			if($result) {
				$getClientInfo = $CI->db->query("select user_task_id,task_name,task_created_by from task where task_id = '".$_REQUEST['task_id']."'")->row();
				$task_name = $getClientInfo->task_name;
			$CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Your proposal for   '.$task_name.' has been submitted.</div>');
			redirect('key-list', 'refresh');
			//return array('status' =>  TRUE, 'message' => 'Task data save successfully.');            	
		}else{
			$CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to submit proposal. Please try again.</div>');
			//return array('status' =>  FALSE, 'message' => 'Unable to save task data.');
			redirect('key-list', 'refresh');
		}	
			
			//}
		}
	}

	public function public_profile($user_id = ''){

		$CI =& get_instance();
		$data = $userInfo = array();
		$CI->load->model('Users');
		$CI->load->model('Tasks');
		$CI->load->library('Ltask');

		$userData = $CI->Users->get_user_profile_info_by_id($user_id);
		$user_profile_image = $CI->session->userdata('user_image');
		$user_login = $CI->Users->get_user_info_by_id($user_id);

		if(empty($user_login->profile_image)) {
	    	$user_profile_image = base_url('assets/img/no-image.png');
	    }else {
	    	$user_profile_image = base_url('uploads/user/profile_image/'.$user_login->profile_image);	    	
	    } 

		if(!empty($userData)) {
        	//$spend_by_user = $CI->Tasks->get_user_total_spend($CI->session->userdata('user_id'));

			$spend_by_user = array();

            $data= array(

				'id' => $userData['basic_info']->user_id, 
				'name' => $userData['basic_info']->name,
				'address' => $userData['basic_info']->address,
				'city' => $userData['basic_info']->city,
				'state' => $userData['basic_info']->state, 
				'country' => $userData['basic_info']->country, 
				'gender' => $userData['basic_info']->gender, 
				'date_of_birth' => $userData['basic_info']->date_of_birth, 
				'bio' => $userData['basic_info']->bio,
				'vat' => $userData['basic_info']->vat,
				'user_languages' => implode(', ', $userData['user_selected_languages']), 
				'user_skills' => $userData['user_selected_skills'], 
				'user_image' => $user_profile_image, 
				'spend_by_user' => $spend_by_user,
				'user_image' => $user_profile_image,
			);
        }

		$data = array_merge($data,(array)($user_login));

		//print_r($userData); print_r($CI->session->all_userdata());
		//print_r($data); 
		//die;
		$AccountForm = $CI->parser->parse('freelancer/public_profile',$data,true);
		return $AccountForm;
	}

	public function earnings(){
		$CI =& get_instance();
		$data = $userInfo = array();
		$AccountForm = $CI->parser->parse('freelancer/earnings',$data,true);
		return $AccountForm;
	}

	public function analytics(){
		$CI =& get_instance();
		$data = $userInfo = array();
		//echo $CI->session->userdata('user_id');
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND  month(hired_doc)= 1 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		//Earnings Overview
		if(count($result)>0) {
			$month['January']=$result[0]->earned;
		}else{
			$month['January']=0;
		}
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND  month(hired_doc)= 2 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['February']=$result[0]->earned;
		}else{
			$month['February']=0;
		}
		
		
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 3 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['March']=$result[0]->earned;
		}else{
			$month['March']=0;
		}
		
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 4 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['April']=$result[0]->earned;
		}else{
			$month['April']=0;
		}
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 5 AND hire_is_completed=1  GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['May']=$result[0]->earned;
		}else{
			$month['May']=0;
		}
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 6 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['June']=$result[0]->earned;
		}else{
			$month['June']=0;
		}
		
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 7 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['July']=$result[0]->earned;
		}else{
			$month['July']=0;
		}
		
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 8 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['August']=$result[0]->earned;
		}else{
			$month['August']=0;
		}
		//echo '<pre>'; print_r($month['August']);die();
		
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 9 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['September']=$result[0]->earned;
		}else{
			$month['September']=0;
		}
		
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 10 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['October']=$result[0]->earned;
		}else{
			$month['October']=0;
		}
		
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 11 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['November']=$result[0]->earned;
		}else{
			$month['November']=0;
		}
		
		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 12 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['December']=$result[0]->earned;
		}else{
			$month['December']=0;
		}

		$q=$CI->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND month(hired_doc)= 12 AND hire_is_completed=1 GROUP BY mon");
		$result =$q->result();
		
		if(count($result)>0) {
			$month['December']=$result[0]->earned;
		}else{
			$month['December']=0;
		}
		 
		 
		
		// Revenue Sources
		$offer_q=$CI->db->query("SELECT task_id FROM task_notification WHERE notification_from='".$CI->session->userdata('user_id')."' AND notification_master_id=12");
		$offer_result=$offer_q->result_array();
		$hired_q=$CI->db->query("SELECT task_id FROM task_notification WHERE notification_from='".$CI->session->userdata('user_id')."' AND notification_master_id=14");
		$hired_result=$hired_q->result_array();
		$offerArray = array_map('current', $offer_result);
		$hireArray = array_map('current', $hired_result);
		$refferal=array_diff($hireArray,$offerArray);
		
		//Projects
		$task_q=$CI->db->query("SELECT * FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND hire_is_completed=1");
		$task_result=$task_q->result();
		$no_web=0;
		$no_php=0;
		$no_cms=0;
		$no_design=0;
		
		foreach($task_result as $t){
			
			$skill_q=$CI->db->query("SELECT task_id FROM task_requirements WHERE area_of_interest_id IN(1,9,10,11,12,19) AND task_id='".$t->task_id."'");
			$no_web=$no_web+$skill_q->num_rows();
			
			$skill_q=$CI->db->query("SELECT task_id FROM task_requirements WHERE area_of_interest_id IN(3,5,6) AND task_id='".$t->task_id."'");
			$no_php=$no_php+$skill_q->num_rows();
			
			$skill_q=$CI->db->query("SELECT task_id FROM task_requirements WHERE area_of_interest_id IN(2,4) AND task_id='".$t->task_id."'");
			$no_cms=$no_cms+$skill_q->num_rows();
			
			$skill_q=$CI->db->query("SELECT task_id FROM task_requirements WHERE area_of_interest_id IN(8,13,14,15,16,17) AND task_id='".$t->task_id."'");
			$no_design=$no_design+$skill_q->num_rows();
			
			
		}

		$monthly_projects_arr=$CI->db->query("SELECT * FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND hire_is_completed=1 AND MONTH(hired_doc) = MONTH(CURRENT_DATE())");
			$monthly_projects=$monthly_projects_arr->num_rows();

		$yearly_projects_arr=$CI->db->query("SELECT * FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND hire_is_completed=1 AND YEAR(hired_doc) = YEAR(CURRENT_DATE())");
			$yearly_projects=$yearly_projects_arr->num_rows();

		$pending_projects_arr=$CI->db->query("SELECT * FROM task_hired WHERE freelancer_id='".$CI->session->userdata('user_id')."' AND hire_is_completed=0 AND MONTH(hired_doc) = MONTH(CURRENT_DATE())");
			$pending_projects=$pending_projects_arr->num_rows();

		$total_microkey_projects_arr=$CI->db->query("SELECT * FROM microkey WHERE user_id='".$CI->session->userdata('user_id')."'");
			$total_microkey_projects=$total_microkey_projects_arr->num_rows();

			//echo $count11;
		
		$current_month = date('F');


		
		$data['month']=$month;
		//echo $data['month'][$current_mpnth];
		$data['yearly_income'] = array_sum($data['month']);
		$data['monthly_income'] = round($data['month'][$current_month]);

		$data['yearly_projects'] = $yearly_projects;
		$data['monthly_projects'] = $monthly_projects;
		$data['pending_projects'] = $pending_projects;

		//echo '<pre>'; print_r($data['month']);die();
	
	 	$data['total_offer']=count($offerArray);
		$data['total_referral']=count($refferal);
		$data['total_microkey_projects']=$total_microkey_projects;

		$data['no_web']=$no_web;
		$data['no_php']=$no_php;
		$data['no_cms']=$no_cms;
		$data['no_design']=$no_design;
		

		
		$CI->load->model('Tasks');
		$data['analytics'] = $CI->Tasks->project_details($CI->session->userdata('user_id'));
		
		$AccountForm = $CI->parser->parse('freelancer/analytics',$data,true);
		return $AccountForm;
	}

	public function problem_ticket($userInfo = array()){

		$CI =& get_instance();
		$CI->load->model('Freelancers');
		$data = array();
		$data['grievance'] = $CI->Freelancers->get_grievance();
		$AccountForm = $CI->parser->parse('freelancer/problem_ticket',$data,true);
		return $AccountForm;
	}
	
	public function add_ticket($userInfo = array()){
		$CI =& get_instance();
		$CI->load->model('Freelancers');	
		$submitData = $CI->input->post(); 
		$result = $CI->Freelancers->add_ticket($userInfo, $submitData); 

		if($result) {
			$CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">New ticket has been generated successfully. Your ticket number : '.$result['message'].'</div>');
			//return array('status' =>  TRUE, 'message' => 'Task data save successfully.');            	
		}else{
			$CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to generate ticket. Please try again.</div>');
			//return array('status' =>  FALSE, 'message' => 'Unable to save task data.');
		}	
	}
	
	public function job_list_data($type){
		$CI =& get_instance();
		$CI->load->model('Users');
        $CI->load->model('Tasks');
		$CI->load->model('Freelancers');
        $CI->load->library("pagination");
		
		
		// Search text
		$searchValue = "";
		  $searchValue = $CI->input->post('search');
		/* if($CI->input->post('search') != NULL ){
		  $searchValue = $CI->input->post('searchValue');
		  $CI->session->set_userdata(array("searchValue"=>$searchValue));
		}else{
		  if($CI->session->userdata('searchValue') != NULL){
			$searchValue = $CI->session->userdata('searchValue');
		  }
		} */
		
		// Row per page
		$rowperpage = PER_PAGE;
		// Row position
		
		if($CI->uri->segment(3)){
			$rowno = ($CI->uri->segment(3));
		}
		else{
			$rowno = 1;
		}
		
		if($rowno != 0){
		  $rowno = ($rowno-1) * $rowperpage;
		}
		
		// Pagination Configuration
		$config['base_url'] = base_url().'job-list/'.$type;
		$config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = false;
		$config['first_tag_open'] = '<li class="previous">';
		$config['first_tag_close'] = '</li>';  
		$config['last_link'] = false;
		$config['first_tag_open'] = '<li class="next">';
		$config['first_tag_close'] = '</li>'; 
		$config['next_link'] = 'Next';
		$config['next_tag_open'] = '<li class="next">';
		$config['next_tag_close'] = '</li>';  
		$config['prev_link'] = 'Previous';
		$config['prev_tag_open'] = '<li class="previous">';
		$config['prev_tag_close'] = '</li>'; 
		$config['cur_tag_open'] = '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>'; 
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		
		$config['use_page_numbers'] = TRUE;
		$config['total_rows'] = $CI->Freelancers->get_job_by_type_count($type,$searchValue);
		$config['per_page'] = $rowperpage;
		
		$CI->pagination->initialize($config);
		
		$data['links'] = $CI->pagination->create_links();
		
		$data['job_array'] = $CI->Freelancers->get_job_by_type($type,$searchValue,$rowperpage,$rowno);
		
		//echo '<pre>'; print_r($data['job_array']); die;
		/*
		if(!empty($data['job_array'])){ $i = 0;
			foreach($data['job_array'] as $singl){
				if($type == 'active'){
					$data['job_array'] = array('view_link' => ); 
				}else{
					$data['job_array'] =  array('view_link' => ); 
				}
				$i++;
			}
		}*/
		
		
		$userLoginData = $CI->Users->getUserLoginData('user_id', $CI->session->userdata('user_id'));
		
		if(!empty($userLoginData)){
			$connections = $userLoginData->total_connects;
			$total_positive_coins=0;			
            if($userLoginData->total_coins>=0){ $total_positive_coins='+ '.$userLoginData->total_coins;}else{ $total_positive_coins=$userLoginData->total_coins;}
			//$total_positive_coins = $userLoginData->total_positive_coins;
			$total_negative_coins = $userLoginData->total_negative_coins;
		}else{
			$connections = $total_positive_coins = $total_negative_coins = 0;
		}
				
		$userData = $CI->Users->get_user_profile_info_by_id($CI->session->userdata('user_id'));
		$user_profile_image = $CI->session->userdata('user_image');
		
		if(empty($user_profile_image)) {
			$user_profile_image = base_url('assets/img/no-image.png');
		}else{
			$user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);	    	
		}

		if(!empty($userData)) {
			$spend_by_user = $CI->Tasks->get_user_total_spend($CI->session->userdata('user_id'));
			
			$userInfo = array('id' => $userData['basic_info']->user_id, 'name' => $userData['basic_info']->name, 'country' => $userData['basic_info']->country, 'gender' => $userData['basic_info']->gender, 'date_of_birth' => $userData['basic_info']->date_of_birth, 'bio' => $userData['basic_info']->bio, 'address' => $userData['basic_info']->address, 'state' => $userData['basic_info']->state, 'city' => $userData['basic_info']->city, 'vat' => $userData['basic_info']->vat, 'user_languages' => implode(', ', $userData['user_selected_languages']), 'user_skills' => $userData['user_selected_skills'], 'user_image' => $user_profile_image, 'spend_by_user' => $spend_by_user, 'connections' => $connections,'total_positive_coins' => $total_positive_coins,'total_negative_coins' => $total_negative_coins );

		}
		$data['user_info'][] = $userInfo;
		
		$AccountForm = $CI->parser->parse('freelancer/job_list_page',$data,true);
		return $AccountForm;
	}
	
	public function saved_job_list(){
		$CI =& get_instance();
		$CI->load->model('Freelancers');
		$CI->load->model('Tasks');
		$CI->load->model('Users');
		$CI->load->library('pagination');
		
		// Pagination Configuration
		$config['base_url'] = base_url().'save-job-list';
		$config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = true;
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';  
        $config['last_link'] = true;
        $config['first_tag_open'] = '<li class="next">';
        $config['first_tag_close'] = '</li>'; 
        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="next">';
        $config['next_tag_close'] = '</li>';  
        $config['prev_link'] = 'Previous';
        $config['prev_tag_open'] = '<li class="previous">';
        $config['prev_tag_close'] = '</li>'; 
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>'; 
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
		$config["per_page"] = PER_PAGE;	

		
        $CI->pagination->initialize($config);
        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        
        $data["links"] = $CI->pagination->create_links();
		
		$userData = $CI->Users->get_user_profile_info_by_id($CI->session->userdata('user_id'));
	    $user_profile_image = $CI->session->userdata('user_image');
		
	    if(empty($user_profile_image)) {
	    	$user_profile_image = base_url('assets/img/no-image.png');
	    }else{
	    	$user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);	    	
	    }

        if(!empty($userData)) {
        	$spend_by_user = $CI->Tasks->get_user_total_spend($CI->session->userdata('user_id'));
            $userInfo = array('id' => $userData['basic_info']->user_id, 'name' => $userData['basic_info']->name, 'country' => $userData['basic_info']->country, 'gender' => $userData['basic_info']->gender, 'date_of_birth' => $userData['basic_info']->date_of_birth, 'bio' => $userData['basic_info']->bio, 'address' => $userData['basic_info']->address, 'state' => $userData['basic_info']->state, 'city' => $userData['basic_info']->city, 'vat' => $userData['basic_info']->vat, 'user_languages' => implode(', ', $userData['user_selected_languages']), 'user_skills' => $userData['user_selected_skills'], 'user_image' => $user_profile_image, 'spend_by_user' => $spend_by_user);
        }
		$data['user_info'][] = $userInfo;
		$data['job_array'] = $CI->Freelancers->saved_job_list($config["per_page"],$page);
		$AccountForm = $CI->parser->parse('freelancer/save_job_list_page',$data,true);

		return $AccountForm;
	}
	
	public function hired_job_details($taskID = 0){
		$CI =& get_instance();
        $CI->load->model('Tasks');
		$CI->load->model("Users");
		$CI->load->model("Freelancers"); 
		$CI->load->model('Continent');
		$CI->load->model('Countries');
		$CI->load->model('Notifications');

        $data = $arrTask = $arrOfferSend = $commentArr = array();

        $task_details = $CI->Tasks->get_task_info_by_user_task_id($taskID);
		 
        if(!empty($task_details)){
            $task_id = $task_details->task_id;
			$task_created_by = $task_details->task_created_by;
		}else{
			$task_id = $task_created_by = ''; 
		}
		/*
		$savedcheck = $CI->Freelancers->check_saved_task($task_id, $CI->session->userdata('user_id'));
		if($savedcheck){
			$data['savetext'] = '<span id="">Already Saved</span>';
		}else{
			$data['savetext'] = '<span id="txtShow">Save This Job</span>';
		}	
		*/
		// get freelancer connection 
		$getcoonects = $CI->Freelancers->get_user_basic_info($CI->session->userdata('user_id'));
		if(!empty($getcoonects)){
			$data['connection'] = $getcoonects['total_connects'];
		}else{
			$data['connection'] = 0;
		}
		
		$data['offer_send'] = $CI->Freelancers->job_offer_user($task_id);
		
		$data['proposal_count'] = $CI->Freelancers->proposal_count($task_id, $count = '');
		
		$tablename = array('comment_master','users','user_login');
		$jointype = array('left','left');
		$joincondition = array('comment_master_alias.user_id=users_alias.user_id','comment_master_alias.user_id=user_login_alias.user_id');
		$condition = 'comment_master_alias.tast_user_id="'.$taskID.'"';
		$fieldArr		= array('*','*','*');
		$limit			= "all";
		$oderby			= 'comment_master_alias.id desc';

		$comment_info = $CI->Tasks->getJoinDataByCondition($tablename,$jointype,$joincondition,$condition,$fieldArr,$limit,$oderby);
		if(!empty($comment_info)){
			foreach($comment_info as $row){
				if($row->profile_image != NULL){
					$img_url = base_url().'uploads/user/profile_image/'.$row->profile_image;
				}else{
					$img_url = base_url().'assets/img/user2.png';
				}
				$commentArr[] = array('user_id' => $row->user_id, 'profile_image' => $img_url, 'name' => $row->name, 'remarks' => $row->remarks);
			}
		}
		
        $task = $CI->Tasks->task_status_info_by_task_id($task_details->task_id); 
		 
        foreach($task as $details) {
            $continent = $CI->Continent->get_continent_by_id($details['basic_info']['task_origin_location']);
            $country = $CI->Countries->get_country_by_id($details['basic_info']['task_origin_country']);

            $user_info = array();
            if(!empty($details['task_hired']) && count($details['task_hired']) > 0){
                foreach($details['task_hired'] as $freelancer_hired){
                    $user_details = $CI->Users->get_user_profile_info_by_id($freelancer_hired['receiver_id']);
                    $user_status = $CI->Users->get_user_info_by_id($freelancer_hired['receiver_id']);
                    $user_profile_image = $user_status->profile_image;
                    if(empty($user_profile_image)) {
                        $user_profile_image = base_url('assets/img/no-image.png');
                    } else {
                        $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
                    }
                    $is_login = $user_status->is_login; 
                    $user_info[] = array('freelancer_id' => $user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'is_online' => (($is_login == '1')?'<span> </span>':''));

                }
            }
			
			// get milestone list data by hired id
			$getHireData = $CI->Freelancers->get_hire_info($task_id, $CI->session->userdata('user_id'));
			if(!empty($getHireData)){
				$hire_id = $getHireData['hired_id'];
				$offer_id = $getHireData['offer_id'];
				$milestoneInfo = $CI->Freelancers->get_task_milestone_list($task_id, $hire_id);
				$data['milestoneInfo'] = $milestoneInfo; 
				$data['hire_id'] = $hire_id;
			}else{
				$offer_id = $hire_id = 0;
				$data['milestoneInfo'] = array(); 
				$data['hire_id'] ='';
			}
            

            $datetime1 = strtotime($details['basic_info']['task_doc']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);

            $years = floor($interval / (365*60*60*24));
            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    
            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 
            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     
            $minutes   = round($interval / 60);

            $tduration="";
			if($details['basic_info']['task_duration_type']=='Hourly')
			{
				$tduration= $details['basic_info']['task_duration'].' Hour';
			}
			else if($details['basic_info']['task_duration_type']=='Daily')
			{
				$tduration= $details['basic_info']['task_duration'].' Day';
			}
			else if($details['basic_info']['task_duration_type']=='Monthly')
			{
				$tduration= $details['basic_info']['task_duration'].' Month';
			}
			else if($details['basic_info']['task_duration_type']=='Yearly')
			{
				$tduration= $details['basic_info']['task_duration'].' Year';
			}
            $arrTask[] = array(
				'task_id' => $task_id,
				'enc_task_id' => base64_encode($task_id),
				'user_task_id' => $details['basic_info']['user_task_id'], 
				'task_title' => $details['basic_info']['task_name'], 
				'task_details' => $details['basic_info']['task_details'], 
				'task_due_date' => $details['basic_info']['task_due_date'], 
				'tasktime_duration' => $tduration, 
				'task_duration_type' => $details['basic_info']['task_duration_type'],
				'task_total_budget' => $details['basic_info']['task_total_budget'], 
				'task_doc' => date('d M Y', strtotime($details['basic_info']['task_doc'])), 
				'task_continent' => $continent->name, 
				'task_country' => $country->name, 
				'task_duration' => $minutes, 
				'task_attachments' => $details['task_attachments'], 
				'task_requirements' => $details['task_requirements'], 
				'task_freelancer_hire' => count($details['task_hired']), 
				'task_freelancer_hired_details' => $user_info, 
				'offer_send' => $arrOfferSend, 
				'commentArr' => $commentArr 
			);
        }

        // task creator info
		$creator_info = $CI->Freelancers->get_user_basic_info($task_created_by);
		$creator_post_info = $CI->Freelancers->get_client_post_count_info($task_created_by);
		//echo $task_id;
		$notification_row_id=0;
		$condition=array('task_id'=>$task_id,'notification_master_id'=>'11');
		$task_notification_info=$CI->Notifications->get_task_notification($condition);
	 
		if(count($task_notification_info)>0){
			$notification_row_id=$task_notification_info[0]->task_notification_id;
			$notification_master_id=$task_notification_info[0]->notification_master_id;
		}else{
			$condition=array('task_id'=>$task_id,'notification_master_id'=>'9');
			$task_notification_info=$CI->Notifications->get_task_notification($condition);
			if(count($task_notification_info)>0){
				$notification_row_id=$task_notification_info[0]->task_notification_id;
				$notification_master_id=$task_notification_info[0]->notification_master_id;
			}
			
			
		}
	    
		
		 
        $data['task_info'] = $arrTask;
        $data['notification_row_id'] = base64_encode($notification_row_id);
		$data['notification_master_id'] = base64_encode($notification_master_id);
        //$data['notification_action_id'] = base64_encode('13');
		
		//$data['task_reject']="<a href=".base_url().'take-action/'.base64_encode($notification_row_id).'/'.base64_encode('13')."class=view-btn2 reject>Reject/Cancel</a>";
		
		
		$data['creator_data'][] = array('client_id'=>base64_encode($creator_info['user_id']),'client_name'=> $creator_info['name'], 'since' => date('Y, M',strtotime($creator_info['doc'])), 'creator_post_count' => $creator_post_info  ); 
		
		// echo '<pre>';print_r($data['task_info']);die('jjj');
		
		$is_cancelled=$task_details->task_is_cancelled;
		$task_is_hired=$task_details->task_hired;
		$task_is_completed=$task_details->task_is_complete;
		
		
		//------------------------------PM
		$data['task_is_hired'] =$task_is_hired;
		$data['task_is_completed'] =$task_is_completed;
		
		$check_off_task = $CI->db->query("select * from offer_task where task_id = ".$task_id." and receiver_id = '".$CI->session->userdata('user_id')."'");
		$row=$check_off_task->row();
		$offer_rejected='no';
		$offer_accepted='no';
 		if($check_off_task->num_rows() > 0){
			echo $is_responded=$row->is_responded;
			$is_refused=$row->is_refused;
			if($is_responded==1 ){
				$offer_accepted='yes';
			}
			if($offer_rejected==1 ){
				$offer_rejected='yes';
			}
		}
		$data['offer_rejected']=$offer_rejected;
		$data['offer_accepted']=$offer_accepted;
		//if($is_cancelled=="1" && $task_is_hired=="0"){
		//	$AccountForm = $CI->parser->parse('freelancer/hired-job-details-cancelled',$data,true);
		//}else if($task_is_completed=="1"){
		//	$AccountForm = $CI->parser->parse('freelancer/hired-job-details-completed',$data,true);
		//}else{
				
			$AccountForm = $CI->parser->parse('freelancer/hired-job-details',$data,true);
		//}
			
		return $AccountForm;
	}
	
	public function freelancer_direct_action($data = array()){
		$CI =& get_instance();
        $CI->load->model('Notifications');
		
		
		if(!empty($data)){
			$task_id = base64_decode($data['task_id']);
			$action_type = base64_decode($data['action_type']);
			
			$return = $CI->Notifications->hired_action($task_id,'A');
			if($return){
				redirect('notification');
			}else{
				redirect('notification');
			}
		}
		
		
		
		
	}
	
	
	
	public function freelancer_take_action($data = array()){
		$CI =& get_instance();
        $CI->load->model('Notifications');
		
		
		if(!empty($data)){
			$task_id = base64_decode($data['task_id']);
			$action_type = base64_decode($data['action_type']);
			
			$return = $CI->Notifications->freelancer_take_action($task_id,$action_type);
			if($return){
				redirect('notification');
			}else{
				redirect('notification');
			}
		}
		
		
		
		
	}
	
		
	
	public function close_contract_page(){
		$CI =& get_instance();
        $CI->load->model('Hires');
		
        $contractInfo = $CI->Hires->get_contract_details_by_task_id(base64_decode($CI->uri->segment(3)));
		
		//echo '<pre>'; print_r($contractInfo); die;
		
		if(!empty($contractInfo)){
			$freelancer_id = $contractInfo['contract_details']['freelancer_id']; 
			$data = array(
				'contract_title' => $contractInfo['contract_details']['hired_title'],
				'contract_id' => $contractInfo['contract_details']['hired_id'],
				'offer_id' => $contractInfo['contract_details']['offer_id'],
				'task_id' => $contractInfo['contract_details']['task_id'],
				'freelancer_id' => base64_decode($CI->uri->segment(2))
			);
			
			$clientInfo = $CI->Hires->get_client_info_by_id(base64_decode($CI->uri->segment(2)));
			
			$user_profile_image = $clientInfo['basic_info']['profile_image'];
			
			if(empty($user_profile_image)) {
				$user_profile_image = base_url('assets/img/no-image.png');
			} else {
				$user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
			}
			$is_login = $clientInfo['basic_info']['is_login'];
			$arrFreelancer[] = array(
				'freelancer_id' => $clientInfo['basic_info']['user_id'], 
				'freelancer_name' => $clientInfo['basic_info']['name'], 
				'freelancer_country' => $clientInfo['basic_info']['country'], 
				'freelancer_state' => $clientInfo['basic_info']['state'], 
				'freelancer_city' => $clientInfo['basic_info']['city'], 
				'freelancer_address' => $clientInfo['basic_info']['address'], 
				'user_image' => $user_profile_image, 
				'is_online' => (($is_login == '1')?'<div class="round"> </div>':''),
				'freelancer_public_id' => $clientInfo['basic_info']['profile_id'],
				'freelancer_total_positive_coins' => $clientInfo['basic_info']['total_positive_coins'],
				'freelancer_total_negative_coins' => $clientInfo['basic_info']['total_negative_coins']
			);
			$data['freelancer_details'] = $arrFreelancer;
			
		}else{
			$freelancer_id = 0;
			$data['freelancer_details'] = array();
		}
		
		$AccountForm = $CI->parser->parse('freelancer/close-contract',$data,true);
		return $AccountForm;
	}
	
	
public function analytics_details(){
		$CI =& get_instance();
		$CI->load->model('Tasks');
		$data = array();
		$data['analytics'] = $CI->Tasks->project_details($CI->session->userdata('user_id'), 100);
		$AccountForm = $CI->parser->parse('freelancer/analytics_details', $data, true);
		return $AccountForm;
	}
	
}