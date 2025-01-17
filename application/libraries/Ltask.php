<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ltask {
	public function past_task_page_1($postData = array()){
		$CI =& get_instance();
		$data = $arrSkills = $userInfo = array();

		if(!empty($postData) && is_array($postData)) {
			$data['fldTaskTitle'] = $postData['fldTaskTitle'];
			$data['fldSkillRequired'] = $postData['fldSkillRequired'];
			$data['fldTaskDescription'] = $postData['fldTaskDescription'];						
		}else {
			$data['fldTaskTitle'] = '';
			$data['fldSkillRequired'] = array();
			$data['fldTaskDescription'] = '';			
		}

        $skills = $CI->Skills->get_all_skill_info();
        if(!empty($skills)) {
        	foreach($skills as $skill) {
        	    $arrSkills[] = array('key' => $skill->area_of_interest_id, 'value' => $skill->name, 'currentselection' => ((!empty($postData) && in_array($skill->area_of_interest_id, $data['fldSkillRequired']))?'selected':''));

        	}
        }   

		$data['skills'] = $arrSkills;	               

		$AccountForm = $CI->parser->parse('task/post-task-step-1',$data,true);
		return $AccountForm;
	}

	public function browse_freelancer_page($pageIndex = null) {

		$CI =& get_instance();

        $CI->load->model('Tasks');

        $CI->load->model("Users");

        $CI->load->library("pagination");





        $config = $data = $arrJobs = $arrTaskSkills = $userInfo = array();

        $config["base_url"] = base_url() . "browse-task";

        $config["total_rows"] = $CI->Tasks->count_all_upcoming_tasks();

        $config["per_page"] = 10;

        $config["uri_segment"] = 2;

        $config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';

        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = 'First';

        $config['first_tag_open'] = '<li class="previous">';

        $config['first_tag_close'] = '</li>';  

        $config['last_link'] = 'Last';

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





        $CI->pagination->initialize($config);



        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        



        $data["links"] = $CI->pagination->create_links();

        $jobs = $CI->Tasks->get_all_upcoming_tasks($config["per_page"], $page);	

        foreach($jobs as $job) {

            $task_skill_requirements = $CI->Tasks->get_task_skill_requirements($job->task_id);

            $task_offers_count = $CI->Tasks->count_all_task_offers($job->task_id);



        	$userInfo = $CI->Users->get_user_info_by_id($job->task_created_by);

        	$userData = $CI->Users->get_user_profile_info_by_id($job->task_created_by);



	        $user_profile_image = $userInfo->profile_image;

	        if(empty($user_profile_image)) {

	    	    $user_profile_image = base_url('assets/img/no-image.png');

	        }

	        else {

	    	    $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);	    	

	        }



	        if(empty($userInfo->is_login) || $userInfo->is_login === 0) {

	    	    $user_is_login = '<em> </em>';

	        }

	        else {

	    	    $user_is_login = '<small> </small>';	    	

	        } 



	        foreach($task_skill_requirements as $taskSkill) {

	        	$arrTaskSkills[] = array('name' => $taskSkill->name);

	        }	         



            $datetime1 = strtotime($job->task_doc);

            $datetime2 = strtotime(date("Y-m-d H:i:s"));

            $interval  = abs($datetime2 - $datetime1);



            // To get the year divide the resultant date into 

            // total seconds in a year (365*60*60*24) 

            $years = floor($interval / (365*60*60*24)); 



            // To get the month, subtract it with years and 

            // divide the resultant date into 

            // total seconds in a month (30*60*60*24) 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    



            // To get the day, subtract it with years and  

            // months and divide the resultant date into 

            // total seconds in a days (60*60*24) 

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));



            // To get the hour, subtract it with years,  

            // months & seconds and divide the resultant 

            // date into total seconds in a hours (60*60) 

            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 



            // To get the minutes, subtract it with years, 

            // months, seconds and hours and divide the  

            // resultant date into total seconds i.e. 60 

            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);



            // To get the minutes, subtract it with years, 

            // months, seconds, hours and minutes  

            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     



            // Print the result 

            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 



            $minutes   = round($interval / 60);

            

        	$arrJobs[] = array('task_id' => $job->task_id, 'user_task_id' => $job->user_task_id, 'task_name' => $job->task_name, 'task_details' => $job->task_details, 'task_total_budget' => $job->task_total_budget, 'task_created_by' => $userData['basic_info']->name, 'user_country' => $userData['basic_info']->country, 'user_state' => $userData['basic_info']->state, 'user_city' => $userData['basic_info']->city, 'user_is_online' => $user_is_login, 'task_skill_requirements' => $arrTaskSkills, 'task_post_duration' => $minutes, 'task_offers' => $task_offers_count, 'user_image' => $user_profile_image);        	

        }

        $data["jobs"] = $arrJobs;		



		$AccountForm = $CI->parser->parse('task/browse-freelancer',$data,true);
		//echo '884'; die();

		return $AccountForm;

	}


	public function get_user_microkey_task($userInfo = null, $pageIndex = null) {

		$CI =& get_instance();

        $CI->load->model('MicrokeyClients');
        $CI->load->model('Tasks');

        $CI->load->library("pagination");





        $config = $data = $arrJobs = array();

        $config["base_url"] = base_url() . "microkey-list-client";

        $config["total_rows"] = $CI->Tasks->count_user_all_microkey_tasks($userInfo['user_id']);

        $config["per_page"] = 10;

        $config["uri_segment"] = 2;

        $config['full_tag_open'] = '<ul class="pagination">';

        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = 'First';

        $config['first_tag_open'] = '<li class="previous">';

        $config['first_tag_close'] = '</li>';  

        $config['last_link'] = 'Last';

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





        $CI->pagination->initialize($config);



        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        



        $data["links"] = $CI->pagination->create_links();

        $jobs = $CI->Tasks->list_user_all_microkey_tasks($userInfo['user_id'], $config["per_page"], $page);
        //echo '<pre>'; print_r($jobs);die();
		
	
        foreach($jobs as $job) {

        	$arrJobs[] = array('id' => $job->id, 'user_id' => $job->user_id, 'title' => $job->title, 'budget' => $job->budget);

        }

        $data["jobs"] = $arrJobs;		



        return $data;

	}

	public function microkey_client_details_page($taskID = null, $userInfo = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model('MicrokeyClients');
        $CI->load->model('Microkeys');
        $CI->load->model('Hires');
        $CI->load->model("Users");        

        $data = $arrTask = $arrOfferSend = $commentArr = array();

        $task_details = $CI->Tasks->get_microkey_client_info_by_id($taskID);
        //echo '<pre>'; print_r($task_details);die();
        //if(empty($task_details))
         //   redirect('microkey-list-client', 'refresh');

        /*$offer_send = $CI->Tasks->offered_task_list_by_user($userInfo['user_id']);
        foreach($offer_send as $offer) {
            $user_details = $CI->Users->get_user_profile_info_by_id($offer->receiver_id);
            $arrOfferSend[] = array('task_name' => $offer->task_name, 'freelancer_name' => $user_details['basic_info']->name);
        }*/
		
		/*$offer_send = $CI->Tasks->get_proposal($taskID);
        foreach($offer_send as $offer) {
            $user_details = $CI->Users->get_user_profile_info_by_id($offer->user_id);
			$profile_img=$user_details['basic_info']->profile_image;
			if( $profile_img != NULL){
					$img_url = base_url().'uploads/user/profile_image/'.$profile_img;
				}else{
					$img_url = base_url().'assets/img/no-image.png';
				}
            $arrOfferSend[] = array(
			'freelancer_id' => $user_details['basic_info']->user_id,
			'freelancer_name' => $user_details['basic_info']->name,
			'freelancer_city' => $user_details['basic_info']->city,
			'freelancer_state' => $user_details['basic_info']->state,
			'freelancer_country' => $user_details['basic_info']->country,
			'freelancer_profile_img' => $img_url
			);
        }*/

		$tablename = array('microkey','users','user_login');
		$jointype = array('left','left');
		$joincondition = array('microkey_alias.user_id = user_alias.user_id',
			'users_alias.user_id = user_login_alias.user_id');
		//$condition = 'microkey_client_alias.user_id="'.$task_details->user_id.'"';
		$condition = '';
		$fieldArr		= array('*','*','*','*');
		//$limit			= "all";
		$limit			= 5;
		$oderby			= 'users_alias.doc desc';
			

		//$comment_info = $CI->Tasks->getJoinDataByCondition($tablename,$jointype,$joincondition,$condition,$fieldArr,$limit,$oderby);
		$comment_info = $CI->Tasks->getFreelancerByMicrokey();
		

		
		 //echo "<pre/>";
		//print_r($comment_info);
		//exit; 
		/*$skills = $CI->Skills->get_all_skill_info(); 
		/* echo "<pre/>";
		print_r($skills);
		exit; */
		if(!empty($comment_info)){
			foreach($comment_info as $row){
				if($row->profile_image != NULL){
					$img_url = base_url().'uploads/user/profile_image/'.$row->profile_image;
				}else{
					$img_url = base_url().'assets/img/no-image.png';
				}
				$posting_date = date('d/m/Y, h iA',strtotime($row->doc));
				/*if($row->attachments != NULL && $row->attachments !=''){
				//	$p_attachments = 'Download Attachment  <a href=""><i class="fa fa-download"></i></a>';
					$p_attachments = $row->proposal_id;
					
				}else{
					$p_attachments = '';
				}*/
				
				
				
				$user_sel_skills = $CI->Users->get_user_selected_skills_by_id($row->user_id);
				 
			
				$user_skills="";
				if(!empty($user_sel_skills) && count($user_sel_skills) >0 ){
					
					foreach($user_sel_skills as $sk){
						
						$key = array_search($sk, array_column($skills, 'area_of_interest_id'));
						$user_skills.=$skills[$key]->name.",";
					}
					
				}
				
				 $user_skills=rtrim($user_skills,",");
				
			         
				$cpositivecoin=0;			
                if($row->total_coins>=0){ $cpositivecoin='+ '.$row->total_coins; }else{ $cpositivecoin=$row->total_coins; }
				
				$commentArr[] = array('user_id' => $row->user_id, 'profile_image' => $img_url, 'name' => $row->name,'posting_date' => $posting_date,'total_positive_coins'=>$cpositivecoin,'total_negative_coins'=>$row->total_negative_coins,'total_coins'=>$row->total_coins,'user_skills'=>$user_skills,'state' => $row->state, 'city' => $row->city,'public_id'=>$row->profile_id
				);
			}
		}

		//echo "<pre/>";
		//print_r($commentArr);
		//exit;

        /*$task = $CI->Tasks->task_status_info_by_task_id($task_details->task_id); 
        
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
                    $positivecoin=0;			
                    if($user_status->total_coins>=0){ $positivecoin='+ '.$user_status->total_coins;}else{ $positivecoin=$user_status->total_coins;}
                    $user_info[] = array('freelancer_id' => $user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'total_positive_coins' => $positivecoin,'total_negative_coins' => $user_status->total_negative_coins, 'hired_id'=> base64_encode($freelancer_hired['hired_id']), 'is_online' => (($is_login == '1')?'<span> </span>':'<span class="green"> </span>'));

                }
            }*/ 


            $microkey_image = $task_details->image;		
			if(empty($task_details)) {
				$microkey_image_path = base_url('assets/img/no-image.png');
			}else{
				$microkey_image_path = base_url('uploads/user/microkey_client_files/'.$microkey_image);	    	
			}         

            $datetime1 = strtotime($task_details->created);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);
 
            $years = floor($interval / (365*60*60*24)); 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 
            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     
            $minutes   = round($interval / 60);

//echo '<pre>'; print_r($details['task_attachments']);die;

            $tduration="";
            if($task_details->duration_type=='Hourly')
			{
				$tduration= $task_details->duration.' Hour';
			}
			else if($task_details->duration_type=='Daily')
			{
				$tduration= $task_details->duration.' Day';
			}
			else if($task_details->duration_type=='Monthly')
			{
				$tduration= $task_details->duration.' Month';
			}
			else if($task_details->duration_type=='Yearly')
			{
				$tduration= $task_details->duration.' Year';
			}
			/*
            $arrTask[] = array('user_task_id' => $details['basic_info']['user_task_id'], 'task_title' => $details['basic_info']['task_name'], 'task_details' => $details['basic_info']['task_details'], 'task_due_date' => $details['basic_info']['task_due_date'], 'task_total_budget' => $details['basic_info']['task_total_budget'], 'task_continent' => $continent->name, 'task_country' => $country->name, 'task_duration' => $minutes, 'task_attachments' => $details['task_attachments'], 'task_requirements' => $details['task_requirements'], 'task_freelancer_hire' => count($details['task_hired']), 'task_freelancer_hired_details' => $user_info, 'offer_send' => $arrOfferSend, 'commentArr' => $commentArr ,'taskDuration' => $tduration);



        }*/
		
		//$data['hire_list'] = $CI->Hires->get_old_hire_list($CI->session->userdata('user_id'));
		 //echo '<pre>'; print_r($data['hire_list']); die;
        //print_r($arrTask);die;

        //$data['task_info'] = $arrTask;

        /*$arrTask[] = array('user_task_id' => $details['basic_info']['user_task_id'], 'task_title' => $details['basic_info']['task_name'], 'task_details' => $details['basic_info']['task_details'], 'task_due_date' => $details['basic_info']['task_due_date'], 'task_total_budget' => $details['basic_info']['task_total_budget'], 'task_continent' => $continent->name, 'task_country' => $country->name, 'task_duration' => $minutes, 'task_attachments' => $details['task_attachments'], 'task_requirements' => $details['task_requirements'], 'task_freelancer_hire' => count($details['task_hired']), 'task_freelancer_hired_details' => $user_info, 'offer_send' => $arrOfferSend, 'commentArr' => $commentArr ,'taskDuration' => $tduration);*/

        $skills = $CI->Skills->get_all_skill_info();

        $user_sel_skills=explode(',',$task_details->skills);
        $user_skills="";
		if(!empty($user_sel_skills) && count($user_sel_skills) >0 ){
			
			foreach($user_sel_skills as $sk){
				
				$key = array_search($sk, array_column($skills, 'area_of_interest_id'));
				$user_skills.=$skills[$key]->name.",";
			}
			
		}
				
		$user_skills=rtrim($user_skills,",");
        //echo '<pre>'; print_r($user_skills);die();

        $continent = $CI->Continent->get_continent_by_id($task_details->continent);
        $country = $CI->Countries->get_country_by_id($task_details->country_id);

        $arrMicrokeyClients = array('task_duration' => $minutes,
									'id' => $task_details->id,
									'user_id' => $task_details->user_id,
									'title' => $task_details->title,
									'addon_file_url' => $task_details->addon_file_url,
									'skills' => $user_skills,
									'budget' => $task_details->budget,
									'continent' => $continent->name,
									'country' => $country->name,
									'duration' => $tduration,
									'description' => $task_details->description,
									'file_name' => $microkey_image_path,
									'created' => $task_details->created
									);

        $data = $arrMicrokeyClients;
        $data['freelancer_info'] = $commentArr;
       // echo '<pre>'; print_r($data);die();

        $AccountForm = $CI->parser->parse('task/microkey_client_details',$data,true);

        return $AccountForm;

    }
	
	public function view_microkey_task($taskID = null)
	{
		
		        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model('MicrokeyClients');
        $CI->load->model('Microkeys');
        $CI->load->model('Hires');
        $CI->load->model("Users");        
        $data = $arrTask = $arrOfferSend = $commentArr = array();
        $task_details = $CI->Tasks->get_microkey_client_info_by_id($taskID);
		$tablename = array('microkey','users','user_login');
		$jointype = array('left','left');
		$joincondition = array('microkey_alias.user_id = user_alias.user_id','users_alias.user_id = user_login_alias.user_id');
		//$condition = 'microkey_client_alias.user_id="'.$task_details->user_id.'"';
		$condition = '';
		$fieldArr		= array('*','*','*','*');
		//$limit			= "all";
		$limit			= 5;
		$oderby			= 'users_alias.doc desc';
		$comment_info = $CI->Tasks->getFreelancerByMicrokey();
		if(!empty($comment_info)){
			foreach($comment_info as $row){
				if($row->profile_image != NULL){
					$img_url = base_url().'uploads/user/profile_image/'.$row->profile_image;
				}else{
					$img_url = base_url().'assets/img/no-image.png';
				}
				$posting_date = date('d/m/Y, h iA',strtotime($row->doc));
				$user_sel_skills = $CI->Users->get_user_selected_skills_by_id($row->user_id);
				 
			
				$user_skills="";
				if(!empty($user_sel_skills) && count($user_sel_skills) >0 ){
					
					foreach($user_sel_skills as $sk){
						
						$key = array_search($sk, array_column($skills, 'area_of_interest_id'));
						$user_skills.=$skills[$key]->name.",";
					}
					
				}
				 $user_skills=rtrim($user_skills,",");
				$cpositivecoin=0;			
                if($row->total_coins>=0){ $cpositivecoin='+ '.$row->total_coins; }else{ $cpositivecoin=$row->total_coins; }
				$commentArr[] = array('user_id' => $row->user_id, 'profile_image' => $img_url, 'name' => $row->name,'posting_date' => $posting_date,'total_positive_coins'=>$cpositivecoin,'total_negative_coins'=>$row->total_negative_coins,'total_coins'=>$row->total_coins,'user_skills'=>$user_skills,'state' => $row->state, 'city' => $row->city,'public_id'=>$row->profile_id
				);
			}
		}

            $microkey_image = $task_details->image;		
			if(empty($task_details)) {
				$microkey_image_path = base_url('assets/img/no-image.png');
			}else{
				$microkey_image_path = base_url('uploads/user/microkey_client_files/'.$microkey_image);	    	
			}         

            $datetime1 = strtotime($task_details->created);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);
 
            $years = floor($interval / (365*60*60*24)); 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 
            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     
            $minutes   = round($interval / 60);

//echo '<pre>'; print_r($details['task_attachments']);die;

            $tduration="";
            if($task_details->duration_type=='Hourly')
			{
				$tduration= $task_details->duration.' Hour';
			}
			else if($task_details->duration_type=='Daily')
			{
				$tduration= $task_details->duration.' Day';
			}
			else if($task_details->duration_type=='Monthly')
			{
				$tduration= $task_details->duration.' Month';
			}
			else if($task_details->duration_type=='Yearly')
			{
				$tduration= $task_details->duration.' Year';
			}
			$skills = $CI->Skills->get_all_skill_info();
			$user_sel_skills=explode(',',$task_details->skills);
			$user_skills="";
			if(!empty($user_sel_skills) && count($user_sel_skills) >0 ){
				
				foreach($user_sel_skills as $sk){
					
					$key = array_search($sk, array_column($skills, 'area_of_interest_id'));
					$user_skills.=$skills[$key]->name.",";
				}
				
			}
					
			$user_skills=rtrim($user_skills,",");
			//echo '<pre>'; print_r($user_skills);die();
			$continent = $CI->Continent->get_continent_by_id($task_details->continent);
			$country = $CI->Countries->get_country_by_id($task_details->country_id);
        $arrMicrokeyClients = array('task_duration' => $minutes,
									'id' => $task_details->id,
									'user_id' => $task_details->user_id,
									'title' => $task_details->title,
									'addon_file_url' => $task_details->addon_file_url,
									'skills' => $user_skills,
									'budget' => $task_details->budget,
									'continent' => $continent->name,
									'country' => $country->name,
									'duration' => $tduration,
									'description' => $task_details->description,
									'file_name' => $microkey_image_path,
									'created' => $task_details->created
									);

        $data = $arrMicrokeyClients;
        $data['freelancer_info'] = $commentArr;
       //echo '<pre>'; print_r($data);die();
        $AccountForm = $CI->parser->parse('tasklist/view-microkey-task',$data,true);
        return $AccountForm;

	}

    public function edit_microkey_client($taskID = null){

		$CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model('MicrokeyClients');
		$data = $arrSkills = $userInfo = $taskArr = array();

		$task_details = $CI->Tasks->get_microkey_client_info_by_id($taskID);

		$data['id'] = $task_details->id;
		$data['user_id'] = $task_details->user_id;
		$data['title'] = $task_details->title;
		//$data['skills'] = $task_details->skills;
		$data['budget'] = $task_details->budget;
		$data['continent'] = $task_details->continent;
		$data['country_id'] = $task_details->country_id;
		$data['duration'] = $task_details->duration;
		$data['duration_type'] = $task_details->duration_type;
		$data['description'] = $task_details->description;
		$data['image'] = $task_details->image;
		$data['addon_file_url'] = $task_details->addon_file_url;
		//echo '<pre>'; print_r($task_details); exit;
		
		/*$hired=$task[0]['basic_info']['task_hired'];
		$AccountForm="";
		if($hired=="1"){
			$AccountForm="1";
			
		}*/
		
	
		


			/*if(!empty($task_details->skills)){
				foreach(explode(',',$task_details->skills) as $task_arr){
					$taskArr[] = $task_arr['skill_id'];
				}
			}*/

			$taskArr = explode(',',$task_details->skills);
			$skills = $CI->Skills->get_all_skill_info();
			if(!empty($skills)) {
				foreach($skills as $skill) {
					$arrSkills[] = array('key' => $skill->area_of_interest_id, 'value' => $skill->name, 'currentselection' => (
						((in_array($skill->area_of_interest_id,$taskArr))) ?'selected':'')
					);        	    
				}
			}

			$continents = $CI->Continent->get_all_continent_info();
	        if(!empty($continents)) {
	        	foreach($continents as $continent) {
	        	    $arrContinent[] = array('key' => $continent->continent_id, 'value' => $continent->name, 'currentselection' => (
					($task_details->continent == $continent->continent_id ) ?'selected':''));
	        	}
	        }

	        $countries = $CI->Countries->get_country_by_continent_id($task_details->continent);
			if(!empty($countries)) {
				foreach($countries as $country) {
					$arrCountry[] = array('key' => $country->country_id, 'value' => $country->name, 'currentselection' => (
					($task_details->country_id == $country->country_id ) ?'selected':''));
				}
			}
			$microkey_image = $task_details->image;		
			if(empty($task_details)) {
				$data['image'] = base_url('assets/img/no-image.png');
			}else{
				$data['image'] = base_url('uploads/user/microkey_client_files/'.$microkey_image);	    	
			}
			
			$data['continents'] = $arrContinent;
			$data['countries'] = $arrCountry;
			$data['skills'] = $arrSkills;
			//echo '<pre>'; print_r($data);die();
			//$data = array_merge($data);

			$AccountForm = $CI->parser->parse('task/edit_microkey_client',$data,true);
		
		
		return $AccountForm;
	}

	public function past_task_page_2($postData = array()){
		$CI =& get_instance();
		$data = $arrCountry = $arrContinent = array();

        //print_r($postData);
        //print_r($_FILES); 

        $continents = $CI->Continent->get_all_continent_info();
        if(!empty($continents)) {
        	foreach($continents as $continent) {
        	    $arrContinent[] = array('key' => $continent->continent_id, 'value' => $continent->name, 'currentselection' => '');
        	}
        } 

        $arrValue = array();
        if(is_array($postData['fldSkillRequired'])) {
            foreach($postData['fldSkillRequired'] as $val) {
            	$arrValue[] = array('value' => $val);
            }
            $postData['fldSkillRequired'] = $arrValue;
        }

        if(!empty($_FILES) && is_array($_FILES)){  
        	$postData['uploadFiles'] = array();
            for($i = 0; $i<count($_FILES['fldTaskDocuments']['name']); $i++) {
                $path = $_FILES['fldTaskDocuments']['name'][$i];
                $path_parts = pathinfo($path);
                $filename = time() . $CI->session->userdata('user_id') . '_' . $path;

           		$sourcePath = $_FILES['fldTaskDocuments']['tmp_name'][$i];
                $targetPath = "./uploads/tmp/".$filename;

                if(move_uploaded_file($sourcePath,$targetPath)) {
                     $postData['uploadFiles'][] = array('fname' => $filename);
                }
            }
        }

		$data['continents'] = $arrContinent;	   
		$data['countries'] = $arrCountry;
		$data = array_merge($data, $postData);		            

		$AccountForm = $CI->parser->parse('task/post-task-step-2',$data,true);
		return $AccountForm;
	}

	public function past_task_page_3($postData = array()){
		$CI =& get_instance();
		$data = array();

        //print_r($postData);
        $arrValue = array();
        if(is_array($postData['fldSkillRequired'])) {
            foreach($postData['fldSkillRequired'] as $val) {
            	$arrValue[] = array('value' => $val);
            }
            $postData['fldSkillRequired'] = $arrValue;
        }

        $arrValue = array();
		if(!empty($postData['uploadFiles'])){
			if(is_array($postData['uploadFiles'])) {
				foreach($postData['uploadFiles'] as $val) {
					$arrValue[] = array('fname' => $val);
				}
				$postData['uploadFiles'] = $arrValue;
			} 
		}else{
			$postData['uploadFiles'] = $arrValue;
		}

		$data = $postData;		            
		$AccountForm = $CI->parser->parse('task/post-task-step-3',$data,true);
		return $AccountForm;
	}	

	public function add_new_job($userInfo = null){
		$CI =& get_instance();
        $CI->load->model('Tasks');		

        $table_data = array('task' => array(), 'task_requirements' => array(), 'task_attachments' => array());
        $submitData = $CI->input->post(); 

       
        if(!empty($submitData)) {
        	//$arrDateParts = explode('-', $submitData['fldDueDate']);
        	//print_r($arrDateParts);
        	//$table_data['task'] = array('task_name' => $submitData['fldTaskTitle'], 'task_details' => $submitData['fldTaskDescription'], 'task_due_date' => date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), (int)$arrDateParts[0], (int)$arrDateParts[1], (int)$arrDateParts[2])), 'task_origin_location' => $submitData['fldSelContinent'], 'task_origin_country' => $submitData['fldSelCountry'], 'task_total_budget' => $submitData['fldTotalBudget'], 'task_status' => 1,'task_keywords' =>$submitData['fldTaskKeywords']);  

            $table_data['task'] = array('task_name' => $submitData['fldTaskTitle'], 'task_details' => $submitData['fldTaskDescription'], 'task_due_date' => $submitData['flddurationfield'], 'task_origin_location' => $submitData['fldSelContinent'], 'task_origin_country' => $submitData['fldSelCountry'], 'task_total_budget' => $submitData['fldTotalBudget'], 'task_status' => 1,'task_keywords' =>$submitData['fldTaskKeywords'],'task_duration_type' =>$submitData['flddurationtype']); 			

            if(is_array($submitData['fldSkillRequired'])) {
                foreach($submitData['fldSkillRequired'] as $val) {
                	$table_data['task_requirements'][] = $val;
                }
            }
			if(isset($submitData['uploadFiles'])){
				if((is_array($submitData['uploadFiles'])) && !empty($submitData['uploadFiles'])) {
					foreach($submitData['uploadFiles'] as $val) {
						$sourcePath = "./uploads/tmp/".$val;
						$targetPath = "./uploads/user/project_documents/".$val;
						if(file_exists($sourcePath)) {
							if(rename($sourcePath,$targetPath)) {
								//var_dump(file_exists($sourcePath));                    		
								$table_data['task_attachments'][] = $val;
							}
						}
					}
				}
			}

			//echo '<pre>'; print_r($submitData); echo '<br/>'; print_r($table_data); die;

            $result = $CI->Tasks->add_new_task($userInfo['user_id'], $table_data); 
            if($result) {
                $CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">New Task added successfully.</div>');
                //return array('status' =>  TRUE, 'message' => 'Task data save successfully.');            	
            }else {
                $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to add new task.</div>');
                //return array('status' =>  FALSE, 'message' => 'Unable to save task data.');
		    }	                           	
        } else {
                $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to add new task.</div>');
                //return array('status' =>  FALSE, 'message' => 'Unable to save task data.');
		}
		redirect('upcoming-projects', 'refresh');	
	}

	public function edit_task_page_1($taskID = null, $postData = array()){

		$CI =& get_instance();
        $CI->load->model('Tasks');
		$data = $arrSkills = $userInfo = $taskArr = array();

		$task = $CI->Tasks->task_details_by_user_task_id($taskID);
//		  echo '<pre>'; print_r($task); exit;
		
		$hired=$task[0]['basic_info']['task_hired'];
		$AccountForm="";
		if($hired=="1"){
			$AccountForm="1";
			
		}
		
	
		if(is_array($task) && count($task) >0 && $hired=="0" ){
		 
			// echo '<pre>'; print_r($task); die;
			$data['task_attachments'] = $task[0]['task_attachments'];
			$data['new_attachment'] = $task[0]['new_attachment'];

			if(!empty($postData) && is_array($postData)) {
				$data['fldTaskTitle'] = $postData['fldTaskTitle'];
				$data['fldSkillRequired'] = $postData['fldSkillRequired'];
				$data['fldTaskDescription'] = $postData['fldTaskDescription'];						
				$data['fldTaskKeywords'] = $postData['fldTaskKeywords'];						
			} else {
				$data['fldTaskTitle'] = $task[0]['basic_info']['task_name'];
				$data['fldSkillRequired'] = array();
				$data['fldTaskDescription'] = $task[0]['basic_info']['task_details'];
				$data['fldTaskKeywords'] = $task[0]['basic_info']['task_keywords'];
			}

			if(!empty($task[0]['task_requirements'])){
				foreach($task[0]['task_requirements'] as $task_arr){
					$taskArr[] = $task_arr['skill_id'];
				}
			}
		
			$skills = $CI->Skills->get_all_skill_info();
			if(!empty($skills)) {
				foreach($skills as $skill) {
					$arrSkills[] = array('key' => $skill->area_of_interest_id, 'value' => $skill->name, 'currentselection' => (
						((in_array($skill->area_of_interest_id,$taskArr))) ?'selected':'')
					);        	    
				}
			}   

			$data['skills'] = $arrSkills;
			$data = array_merge($data, $postData);
//                        dd($data);
			$AccountForm = $CI->parser->parse('task/edit-task-step-1',$data,true);
		}
		
		
		return $AccountForm;
	}

	public function edit_task_page_2($taskID = null, $postData = array()){

		$CI =& get_instance();

        $CI->load->model('Tasks');

		$data = $arrCountry = $arrContinent = array();



        //print_r($postData);

        //print_r($_FILES); 

		$task = $CI->Tasks->task_details_by_user_task_id($taskID);

		



        $continents = $CI->Continent->get_all_continent_info();

        if(!empty($continents)) {

        	foreach($continents as $continent) {

        	    $arrContinent[] = array('key' => $continent->continent_id, 'value' => $continent->name, 'currentselection' => (

				($task[0]['basic_info']['task_origin_location'] == $continent->continent_id ) ?'selected':''));

        	}

        } 

		

		$countries = $CI->Countries->get_country_by_continent_id($task[0]['basic_info']['task_origin_location']);	

		if(!empty($countries)) {

			foreach($countries as $country) {

				$arrCountry[] = array('key' => $country->country_id, 'value' => $country->name, 'currentselection' => (

				($task[0]['basic_info']['task_origin_country'] == $country->country_id ) ?'selected':''));

			}

		}



        $arrValue = array();

        if(is_array($postData['fldSkillRequired'])) {

            foreach($postData['fldSkillRequired'] as $val) {

            	$arrValue[] = array('value' => $val);

            }

            $postData['fldSkillRequired'] = $arrValue;

        }



  $postData['uploadFiles'] = array();

        if(!empty($_FILES) && is_array($_FILES)){  

        	

            for($i = 0; $i<count($_FILES['fldTaskDocuments']['name']); $i++) {

                $path = $_FILES['fldTaskDocuments']['name'][$i];

                $path_parts = pathinfo($path);

                $filename = time() . $CI->session->userdata('user_id') . '_' . $path;



           		$sourcePath = $_FILES['fldTaskDocuments']['tmp_name'][$i];

                $targetPath = "./uploads/tmp/".$filename;



                if(move_uploaded_file($sourcePath,$targetPath)) {

                     $postData['uploadFiles'][] = array('fname' => $filename);

                }

            }

        }

	

		$data['continents'] = $arrContinent;	   

		$data['countries'] = $arrCountry;

		$data['fldDueDate'] = $task[0]['basic_info']['task_due_date'];

		$data = array_merge($data, $postData);		            



		$AccountForm = $CI->parser->parse('task/edit-task-step-2',$data,true);

		return $AccountForm;

	}

	

	public function edit_task_page_3($taskID = null, $postData = array()){

		$CI =& get_instance();

		$CI->load->model('Tasks');

		$data = array();

		

		$task = $CI->Tasks->task_details_by_user_task_id($taskID);



        //print_r($postData); die;

        $arrValue = array();

        if(is_array($postData['fldSkillRequired'])) {

            foreach($postData['fldSkillRequired'] as $val) {

            	$arrValue[] = array('value' => $val);

            }

            $postData['fldSkillRequired'] = $arrValue;

        }



        $arrValue = array();

		if(isset($postData['uploadFiles'])){

			if(is_array($postData['uploadFiles'])) {

				foreach($postData['uploadFiles'] as $val) {

					$arrValue[] = array('fname' => $val);

				}

				$postData['uploadFiles'] = $arrValue;

			} 

		}else{

			$postData['uploadFiles'] = array();

		}		

		

		$data = $postData;	
		$total = $task[0]['basic_info']['task_total_budget'];

		$service = ($total*0.15);

		$total_pay = ($total + $service);
		
		/*if($total < 100){
			$rate_view = 3; 
		}else if($total >= 100 && $total < 500){
			$rate_view = 5;
		}else if($total >= 500 && $total < 1000){
			$rate_view = 10;
		}else if($total >= 1000 && $total < 3000){
			$rate_view = 15;
		}else if($total >= 3000){
			$rate_view = 20;
		}
		
		$data['serviceChargeShow'] = $rate_view;
		$data['fldTotalBudget'] = $task[0]['basic_info']['task_total_budget'];
		$data['serviceCharge'] = $service;
		$data['totalPay'] = $total_pay;*/
		
		$data['serviceChargeShow'] = 0;
		$data['fldTotalBudget'] = $task[0]['basic_info']['task_total_budget'];
		$data['serviceCharge'] = 0;
		$data['totalPay'] = $total;

		

		$data = array_merge($data, $postData);	

		$AccountForm = $CI->parser->parse('task/edit-task-step-3',$data,true);

		return $AccountForm;

	}

	

	public function edit_new_job($taskID = null, $userInfo = null){

		$CI =& get_instance();

        $CI->load->model('Tasks');		



        $table_data = array('task' => array(), 'task_requirements' => array(), 'task_attachments' => array());

        $submitData = $CI->input->post(); 

        if(!empty($submitData)) {
			
        	$arrDateParts = explode('-', $submitData['fldDueDate']);

        	//print_r($arrDateParts);

        	$table_data['task'] = array('task_name' => $submitData['fldTaskTitle'],'task_keywords' =>$submitData['fldTaskKeywords'], 'task_details' => $submitData['fldTaskDescription'], 'task_due_date' => date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), (int)$arrDateParts[0], (int)$arrDateParts[1], (int)$arrDateParts[2])), 'task_origin_location' => $submitData['fldSelContinent'], 'task_origin_country' => $submitData['fldSelCountry'], 'task_total_budget' => $submitData['fldTotalBudget'], 'task_status' => 1);   	



            if(is_array($submitData['fldSkillRequired'])) {

                foreach($submitData['fldSkillRequired'] as $val) {

                	$table_data['task_requirements'][] = $val;

                }

            }  

			

			if(isset($submitData['uploadFiles'])){

				if(is_array($submitData['uploadFiles'])) {

					foreach($submitData['uploadFiles'] as $val) {

						$sourcePath = "./uploads/tmp/".$val;

						$targetPath = "./uploads/user/project_documents/".$val;



						if(file_exists($sourcePath)) {

							if(rename($sourcePath,$targetPath)) {

								//var_dump(file_exists($sourcePath));                    		

								$table_data['task_attachments'][] = $val;

							}

						}

					}

				}

			}



            

			//echo '<pre>';

            //print_r($submitData);

            //echo '<pre>';

            //print_r($table_data); die;

            $result = $CI->Tasks->edit_new_task($taskID, $table_data); 

            if($result) {

                $CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Task edited successfully.</div>');

                //return array('status' =>  TRUE, 'message' => 'Task data save successfully.');            	

            }

            else {

                $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to edit task.</div>');

                //return array('status' =>  FALSE, 'message' => 'Unable to save task data.');

		    }	                           	

        }    

        else {

                $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to edit task.</div>');

                //return array('status' =>  FALSE, 'message' => 'Unable to save task data.');

		}

		redirect('upcoming-projects', 'refresh');	

	}

	

	public function get_user_upcoming_task($userInfo = null, $pageIndex = null) {

		$CI =& get_instance();

        $CI->load->model('Tasks');
        $CI->load->model('Freelancers');
        $CI->load->library("pagination");





        $config = $data = $arrJobs = array();

        $config["base_url"] = base_url() . "upcoming-projects";

        $config["total_rows"] = $CI->Tasks->count_user_all_upcoming_tasks($userInfo['user_id']);

        $config["per_page"] = 10;

        $config["uri_segment"] = 2;

        $config['full_tag_open'] = '<ul class="pagination">';

        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = 'First';

        $config['first_tag_open'] = '<li class="previous">';

        $config['first_tag_close'] = '</li>';  

        $config['last_link'] = 'Last';

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





        $CI->pagination->initialize($config);



        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        



        $data["links"] = $CI->pagination->create_links();

        $jobs = $CI->Tasks->list_user_all_upcoming_tasks($userInfo['user_id'], $config["per_page"], $page);	
		
	
        foreach($jobs as $job) {
            $total_proposal = $CI->Freelancers->proposal_count($job->task_id, $count = '');
        	$arrJobs[] = array('task_id' => $job->task_id, 'user_task_id' => $job->user_task_id, 'task_name' => $job->task_name, 'task_total_budget' => $job->task_total_budget,'total_proposal'=>$total_proposal);

        }

        $data["jobs"] = $arrJobs;		



        return $data;

	}



    public function get_user_ongoing_task($userInfo = null, $pageIndex = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model('Users');        
        $CI->load->library("pagination");

        $config = $data = $arrJobs = array();
        $config["base_url"] = base_url() . "dashboard";
        $config["total_rows"] = $CI->Tasks->count_user_all_ongoing_tasks($userInfo['user_id']);
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';  
        $config['last_link'] = 'Last';
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
        $CI->pagination->initialize($config);
        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        
        
		$data["links"] = $CI->pagination->create_links();
        
		$jobs = $CI->Tasks->list_user_all_ongoing_tasks($userInfo['user_id'], $config["per_page"], $page); 
		
		//echo '<pre>'; print_r($jobs); 
		
        foreach($jobs as $job) {
			
			$freelancer_id = $CI->Tasks->get_freelancer_hired_for_task($userInfo['user_id'], $job->task_id); 
			if(empty($freelancer_id)){
				$user_data = $CI->Tasks->get_hired_freelancer_info($job->task_id);
				if(!empty($user_data)){
					$freelancer_id = $user_data->freelancer_id;
				}
			}
			
			$user_details = $CI->Users->get_user_profile_info_by_id($freelancer_id);
			$user_status = $CI->Users->get_user_info_by_id($freelancer_id);
			//echo '<pre>'; print_r($user_details);
			//exit();
			$user_profile_image ="";
			if(empty($user_status->profile_image)) {
				$user_profile_image = base_url('assets/img/no-image.png');
			} else {
				$user_profile_image = base_url('uploads/user/profile_image/'.$user_status->profile_image);          
			}
			$is_login = @$user_status->is_login;

			$offer_count = $CI->Tasks->count_all_task_offers($job->task_id);     

			$arrJobs[] = array(
			'task_id' => $job->task_id, 
			'user_task_id' => $job->user_task_id,
			'task_name' => $job->task_name, 
			'task_total_budget' => $job->task_total_budget, 
			'freelancer_name' => @$user_details['basic_info']->name,
			'freelancer_country' => @$user_details['basic_info']->country,
			'freelancer_state' => @$user_details['basic_info']->state,
			'freelancer_city' => @$user_details['basic_info']->city,
			'user_image' => $user_profile_image, 
			'is_online' => (($is_login == '1')?'<div class="green-tb"></div>':'<div class="gray-tb"></div>'),
			'offer_cnt' => $offer_count
			);
			
        }
		
		//print_r($arrJobs);  die;
		
        $data["jobs"] = $arrJobs;       
		
        return $data;

    }  



    public function get_user_hired_job_list($userInfo = null, $pageIndex = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model('Users');        
        $CI->load->library("pagination");

        $config = $data = $arrJobs = array();
        $config["base_url"] = base_url() . "dashboard";
        $config["total_rows"] = $CI->Tasks->count_user_all_hired_tasks($userInfo['user_id']);
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';  
        $config['last_link'] = 'Last';
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

        $CI->pagination->initialize($config);
        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        

        $data["links"] = $CI->pagination->create_links();
        $jobs = $CI->Tasks->list_user_all_hired_tasks($userInfo['user_id'], $config["per_page"], $page); 
        foreach($jobs as $job) {
            $freelancer_id = $CI->Tasks->get_freelancer_hired_for_task($userInfo['user_id'], $job->task_id); 
            if(empty($freelancer_id))
                continue;
            $user_details = $CI->Users->get_user_profile_info_by_id($freelancer_id);
            $user_status = $CI->Users->get_user_info_by_id($freelancer_id);

            $user_profile_image = $user_status->profile_image;
            if(empty($user_profile_image)) {
                $user_profile_image = base_url('assets/img/no-image.png');
            } else {
                $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
            }
            $is_login = $user_status->is_login;
            $offer_count = $CI->Tasks->count_all_task_offers($job->task_id);               

            $arrJobs[] = array('hired_id'=> base64_encode($job->hired_id),'task_id' => $job->task_id, 'user_task_id' => $job->user_task_id, 'task_name' => $job->task_name, 'task_total_budget' => $job->task_total_budget, 'task_due_date' => ($job->hired_end_date ? date("d/m/Y", strtotime($job->hired_end_date)) : ''),'freelancer_id' =>$user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'is_online' => (($is_login == '1')?'<div class="green-tb"></div>':'<div class="gray-tb"></div>'), 'offer_cnt' => $offer_count);

        }
        $data["jobs"] = $arrJobs; 
        return $data;
    }       

	public function get_user_hired_task($userInfo = null, $pageIndex = null) {

		$CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->library("pagination");

        $config = $data = $arrJobs = array();
        $config["base_url"] = base_url() . "hired";
        $config["total_rows"] = $CI->Tasks->count_user_all_hired_tasks($userInfo['user_id']);
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';  
        $config['last_link'] = 'Last';
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

        $CI->pagination->initialize($config);
        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        

        $data["links"] = $CI->pagination->create_links();
        $jobs = $CI->Tasks->list_user_all_hired_tasks($userInfo['user_id'], $config["per_page"], $page);	
        foreach($jobs as $job) {
        	$arrJobs[] = array('task_id' => $job->task_id, 'user_task_id' => $job->user_task_id, 'task_name' => $job->task_name, 'task_total_budget' => $job->task_total_budget);
        }
        $data["jobs"] = $arrJobs;		
        return $data;
	}


	//modified on 19-10-2020
	public function browse_task_page($pageIndex = 0) {
		$CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model("Users");
        $CI->load->library("pagination");
		$SearchKeyWord="";
		if(isset($_POST['searchCriteria'])){
			echo $SearchKeyWord=$CI->input->post('searchCriteria');
		}
		
		
		$task_count=$CI->Tasks->count_all_upcoming_tasks($SearchKeyWord); //echo "<br>";
		$micro_project_count=$CI->Tasks->get_all_micro_client_task_count($SearchKeyWord);//echo "<br>";
		$total_count=$task_count+$micro_project_count;
		$per_page=9;
        $config = $data = $arrJobs = $arrMicroClientJobs = $arrTaskSkills = $userInfo = array();
        $config["base_url"] = base_url() . "browse-task";
        $config["total_rows"] = $total_count;
        $config["per_page"] = $per_page;
        $config["uri_segment"] = 2;
        $config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';  
        $config['last_link'] = 'Last';
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

      $task_limit = $url_var = $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;      
	   $rest_task=$task_count-$url_var;
	   $task_offset=$per_page;
	   $micro_key_offset = $micro_key_limit=0;
	   if($rest_task<$per_page){
		   if($rest_task>0){
			   $micro_key_offset = 0;
			   $micro_key_limit = $per_page - $rest_task;
		   }else{
			   $task_offset = $task_limit = 0;
			   $micro_key_offset = $url_var - $task_count;
			   $micro_key_limit = $per_page ;
		   }
	   }
	   if(isset($_POST['searchCriteria'])){
		   $task_offset=$task_count;
		   $task_limit=0;
		    $micro_key_limit =$micro_project_count;
		   $micro_key_offset=0;
	   }

        $jobs = $CI->Tasks->get_all_upcoming_tasks($task_offset, $task_limit,$SearchKeyWord);	
		//$jobs = $CI->Tasks->get_all_upcoming_tasks(10,1);	
		//print_r($jobs);
        foreach($jobs as $job) {
            $task_skill_requirements = $CI->Tasks->get_task_skill_requirements($job->task_id);
            $task_offers_count = $CI->Tasks->count_all_task_offers($job->task_id);
        	$userInfo = $CI->Users->get_user_info_by_id($job->task_created_by);
        	$userData = $CI->Users->get_user_profile_info_by_id($job->task_created_by);
	        $user_profile_image = $userInfo->profile_image;
			 $allanalytics = $CI->Tasks->client_analytics_by_id($job->task_created_by); 
				//echo "<pre>";print_r($allanalytics); die;
			 $hired_freelancer=$CI->Tasks->count_user_all_hired_tasks($job->task_created_by);
	        if(empty($user_profile_image)) {
	    	    $user_profile_image = base_url('assets/img/no-image.png');
	        }
	        else {
	    	    $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);	    	
	        }
	        if(empty($userInfo->is_login) || $userInfo->is_login === 0) {

	    	    $user_is_login = '<em> </em>';
	        }
	        else {
	    	    $user_is_login = '<small> </small>';	    	
	        } 
	        foreach($task_skill_requirements as $taskSkill) {
	        	$arrTaskSkills[] = array('name' => $taskSkill->name);
	        }	         
            $datetime1 = strtotime($job->task_doc);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);
            // To get the year divide the resultant date into 
            // total seconds in a year (365*60*60*24) 
            $years = floor($interval / (365*60*60*24)); 
            // To get the month, subtract it with years and 
            // divide the resultant date into 
            // total seconds in a month (30*60*60*24) 
            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    
            // To get the day, subtract it with years and  
            // months and divide the resultant date into 
            // total seconds in a days (60*60*24) 
            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            // To get the hour, subtract it with years,  
            // months & seconds and divide the resultant 
            // date into total seconds in a hours (60*60) 
            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 
            // To get the minutes, subtract it with years, 
            // months, seconds and hours and divide the  
            // resultant date into total seconds i.e. 60 
            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
            // To get the minutes, subtract it with years, 
            // months, seconds, hours and minutes  
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     
            // Print the result 
            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 
            $minutes   = round($interval / 60);
			if($userData['basic_info']->total_coins==null || $userData['basic_info']->total_coins==""){
				$userData['basic_info']->total_coins=0;
			}
        	$arrJobs[] = array('task_id' => $job->task_id, 'user_task_id' => $job->user_task_id, 'task_name' => $job->task_name, 'task_details' => $job->task_details, 'task_total_budget' => $job->task_total_budget, 'task_created_by' => $userData['basic_info']->name, 'user_country' => $userData['basic_info']->country,'total_coins' => $userData['basic_info']->total_coins, 'user_state' => $userData['basic_info']->state, 'last_login' => $userData['basic_info']->last_login, 'user_city' => $userData['basic_info']->city, 'user_is_online' => $user_is_login, 'task_skill_requirements' => $arrTaskSkills, 'task_post_duration' => $minutes, 'task_offers' => $task_offers_count, 'user_image' => $user_profile_image,'total_project_completed'=>$allanalytics['yearly_projects'],'total_money_spent'=>$allanalytics['yearly_income'],'total_hired_freelancer'=> $hired_freelancer);        	

        }
		//print_r($arrJobs);
		// fetch micro client posts
		$micro_client_jobs = $CI->Tasks->get_all_micro_client_task_lists($micro_key_offset,$micro_key_limit,$SearchKeyWord);	
		
		foreach($micro_client_jobs as $micro_client_job){
			
			 $allanalytics = $CI->Tasks->client_analytics_by_id($micro_client_job->user_id); 
			 $hired_freelancer=$CI->Tasks->count_user_all_hired_tasks($micro_client_job->user_id);
			$user_profile_image = $micro_client_job->profile_image;
	        if(empty($user_profile_image)) {
	    	    $user_profile_image = base_url('assets/img/no-image.png');
	        }
	        else {
	    	    $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);	    	
	        }
			if($micro_client_job->total_coins==null || $micro_client_job->total_coins==""){
				$micro_client_job->total_coins=0;
			}
			$arrMicroClientJobs[]=array('username' => $micro_client_job->username,
										'client_address' => $micro_client_job->country." , ".$micro_client_job->continent,
										'post_title' => $micro_client_job->title,
										'profile_image' => $user_profile_image,
										'total_coins' => $micro_client_job->total_coins,
										'budget' => $micro_client_job->budget,
										'last_login' => $micro_client_job->last_login,
										'post_image' => $micro_client_job->microkey_post_image,
										'task_id' => $micro_client_job->m_task_id, 
										'task_identity' => $micro_client_job->task_identity
										,'m_total_project_completed'=>$allanalytics['yearly_projects'],
										'm_total_money_spent'=>$allanalytics['yearly_income'],
										'm_total_hired_freelancer'=> $hired_freelancer
										);
		}
        $data["jobs"] = $arrJobs;	
		$data["links"]="";
        $data["MicroClientJobs"] = $arrMicroClientJobs;	
		if(!isset($_POST['searchCriteria']))
		$CI->pagination->initialize($config);
		$data["MicroClientJobsPagination"] = $CI->pagination->create_links();
		// print_r($micro_client_jobs);die;
		$AccountForm = $CI->parser->parse('task/browse-task',$data,true);

		return $AccountForm;

	}	



	public function ajax_list_tasks($pageIndex = null) {

		$CI =& get_instance();

        $CI->load->model('Tasks');

        $CI->load->model("Users");



        $data = $userInfo = array();

        $output = '';

                    

        $data = $CI->input->post();  

        $searchCriteria = (!empty($data['searchCriteria']))?$data['searchCriteria']:'';                           

        $jobs = $CI->Tasks->ajax_get_all_upcoming_tasks($searchCriteria);	

        foreach($jobs as $job) {

            $task_skill_requirements = $CI->Tasks->get_task_skill_requirements($job->task_id);

            $task_offers_count = $CI->Tasks->count_all_task_offers($job->task_id);



        	$userInfo = $CI->Users->get_user_info_by_id($job->task_created_by);

        	$userData = $CI->Users->get_user_profile_info_by_id($job->task_created_by);



	        $user_profile_image = $userInfo->profile_image;

	        if(empty($user_profile_image)) {

	    	    $user_profile_image = base_url('assets/img/no-image.png');

	        }

	        else {

	    	    $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);	    	

	        }



	        if(empty($userInfo->is_login) || $userInfo->is_login === 0) {

	    	    $user_is_login = '<em> </em>';

	        }

	        else {

	    	    $user_is_login = '<small> </small>';	    	

	        } 



	         



            $datetime1 = strtotime($job->task_doc);

            $datetime2 = strtotime(date("Y-m-d H:i:s"));

            $interval  = abs($datetime2 - $datetime1);



            // To get the year divide the resultant date into 

            // total seconds in a year (365*60*60*24) 

            $years = floor($interval / (365*60*60*24)); 



            // To get the month, subtract it with years and 

            // divide the resultant date into 

            // total seconds in a month (30*60*60*24) 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    



            // To get the day, subtract it with years and  

            // months and divide the resultant date into 

            // total seconds in a days (60*60*24) 

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));



            // To get the hour, subtract it with years,  

            // months & seconds and divide the resultant 

            // date into total seconds in a hours (60*60) 

            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 



            // To get the minutes, subtract it with years, 

            // months, seconds and hours and divide the  

            // resultant date into total seconds i.e. 60 

            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);



            // To get the minutes, subtract it with years, 

            // months, seconds, hours and minutes  

            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     



            // Print the result 

            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 



            $minutes   = round($interval / 60);

            

            $output .= '<div class="task_Left_Div task_Left_Div-new">

                <div class="bod-sec">

                    <div class="img2-ses"> <span> <img src="' . $user_profile_image . '" alt="' . $userData['basic_info']->name . '" style="height:67px;width:69px;"> </span>

                        <div class="caption">

                            <h3> ' . $userData['basic_info']->name . '</h3>

                            <p> ' . $userData['basic_info']->city . ', ' . $userData['basic_info']->state . ', ' . $userData['basic_info']->country . ' </p>

                        </div>

                    </div>

                </div>

                <h3>

                    <a href="' . base_url() . 'task-details/' . $job->user_task_id . '" target="_blank" style="color: #293134;">' . $job->task_name . '</a>

                </h3>

                <small> Posted ' . $minutes . ' minutes ago </small>

                <p> ' . $job->task_details . ' </p>

                <h4> Skills Requered </h4>

                <span>';



	            foreach($task_skill_requirements as $taskSkill) {

	        	    $output .= '<a href="#">' . $taskSkill->name . '</a>';

	            }



            $output .= '</span>

                <div class="task_info"> <span>

                    <h5>Budget</h5>

                    <em>$' . $job->task_total_budget . '</em> </span> <span>

                    <h5>Offers</h5>

                    <em> ' . $task_offers_count . ' Offers </em> </span> <span>

                    <h5>Comment</h5>

                    <em> 0 Comments </em> </span> 

                </div>

            </div>';        	       	

        }



		return $output;

	}	



    public function ajax_get_task_details($pageIndex = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $data = array();                   
        $data = $CI->input->post();  
        $task_id = (!empty($data['task_id']))?$data['task_id']:null;    
        if(!empty($task_id)) {
            $task = $CI->Tasks->task_details_by_user_task_id($task_id); 
            //print_r($task);
            $data = array('task_details' => $task, 'status' => 1, 'message' => 'successful');
        } else {
            $data = array('task_details' => null, 'status' => 0, 'message' => 'error');
        }
        return json_encode($data);
    } 

	public function ajax_update_budget(){
		$CI =& get_instance();
        $CI->load->model('Tasks');
        $data = array();                   
        $data = $CI->input->post();  
		if(!empty($data)){
			$return = $CI->Tasks->update_task_budget($data);
			$data = array('status' => 1, 'amount' => $CI->input->post('estimated_budget'), 'message' => 'successful');
		}else{
			$data = array('status' => 0, 'amount' => $CI->input->post('estimated_budget'), 'message' => 'error');
		}
		return json_encode($data);
	}



	public function make_an_offer_page($userInfo = null) {

		$CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model("Users");        

		$data = $selectedFreelancer = $arrFreelancer = $arrJobs = $arrCountry = $arrContinent = $arrSkills = array();
        $post_data = $CI->input->post();               
        $jobs = $CI->Tasks->list_user_all_unhired_tasks($userInfo['user_id']);	

        //print_r($post_data);
        if(!empty($post_data)) {
            if(!empty($post_data['fldSelectedFreelancer']) && is_array($post_data['fldSelectedFreelancer'])) {
               $selectedFreelancer = $post_data['fldSelectedFreelancer'];
            } else {
               $selectedFreelancer[] = $post_data['chkMakeOfferFreelancer'];
            }
        } else {
            $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Please select a freelancer for sending offer.</div>');
            redirect('search-freelancer', 'refresh');
        }
        $positivecoin=0;        
        $freelancers_list = $CI->Users->get_freelancers_profile_info_by_id($selectedFreelancer);
        if(!empty($freelancers_list)) {
            foreach($freelancers_list as $row) {
			
				if($row['basic_info']->total_coins>=0){ $positivecoin='+ '.$row['basic_info']->total_coins;}else{ $positivecoin=$row['basic_info']->total_coins;}
                $user_status = $CI->Users->get_user_info_by_id($row['basic_info']->user_id);
                $user_profile_image = $user_status->profile_image;
                if(empty($user_profile_image)) {
                    $user_profile_image = base_url('assets/img/no-image.png');
                } else {
                    $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
                }
                $is_login = $user_status->is_login;               

                $arrFreelancer[] = array('freelancer_id' => $row['basic_info']->user_id, 'freelancer_name' => $row['basic_info']->name, 'freelancer_country' => $row['basic_info']->country, 'freelancer_state' => $row['basic_info']->state, 'freelancer_city' => $row['basic_info']->city, 'user_image' => $user_profile_image, 'total_positive_coins'=>$positivecoin,'total_negative_coins'=> $row['basic_info']->total_negative_coins, 'is_online' => (($is_login == '1')?'<div class="round"> </div>':''));

            }
        }

        $continents = $CI->Continent->get_all_continent_info();

        if(!empty($continents)) {

            foreach($continents as $continent) {

                $arrContinent[] = array('key' => $continent->continent_id, 'value' => $continent->name, 'currentselection' => '');

            }

        }



        $countries = $CI->Countries->get_all_country_info();

        if(!empty($countries)) {

            foreach($countries as $countrie) {

                $arrCountry[] = array('key' => $countrie->country_id, 'value' => $countrie->name, 'currentselection' => '');

            }

        }



        $skills = $CI->Skills->get_all_skill_info();

        if(!empty($skills)) {

            foreach($skills as $skill) {

                $arrSkills[] = array('key' => $skill->area_of_interest_id, 'value' => $skill->name, 'currentselection' => '');

            }

        }         



        //print_r($jobs);

        if(!empty($jobs)) {

            foreach($jobs as $row) {

                $arrJobs[] = array('task_id' => $row->task_id, 'user_task_id' => $row->user_task_id, 'task_name' => $row->task_name, 'task_total_budget' => $row->task_total_budget);

            }

        }        



        $data['freelancerInfo'] = $arrFreelancer;

        $data['jobs'] = $arrJobs;

        $data['skills'] = $arrSkills;

        $data['countries'] = $arrCountry;  

        $data['continents'] = $arrContinent;                         



		$AccountForm = $CI->parser->parse('task/make-an-offer',$data,true);

		return $AccountForm;

	}



    public function task_details_page($taskID = null, $userInfo = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model('Hires');
        $CI->load->model("Users");        

        $data = $arrTask = $arrOfferSend = $commentArr = array();

        $task_details = $CI->Tasks->get_task_info_by_user_task_id($taskID);
        
        if(empty($task_details))
            redirect('upcoming-projects', 'refresh');

        /*$offer_send = $CI->Tasks->offered_task_list_by_user($userInfo['user_id']);
        foreach($offer_send as $offer) {
            $user_details = $CI->Users->get_user_profile_info_by_id($offer->receiver_id);
            $arrOfferSend[] = array('task_name' => $offer->task_name, 'freelancer_name' => $user_details['basic_info']->name);
        }*/
		
        $offer_send = $CI->Tasks->get_proposal($taskID);
        $proposals = $CI->Tasks->get_freelancers_proposa_admin($taskID);
        foreach($offer_send as $offer) {
            $user_details = $CI->Users->get_user_profile_info_by_id($offer->user_id);
            $profile_img = $user_details['basic_info']->profile_image;
            if ($profile_img != NULL) {
                $img_url = base_url() . 'uploads/user/profile_image/' . $profile_img;
            } else {
                $img_url = base_url() . 'assets/img/no-image.png';
            }
            $arrOfferSend[] = array(
                'freelancer_id' => $user_details['basic_info']->user_id,
                'freelancer_name' => $user_details['basic_info']->name,
                'freelancer_city' => $user_details['basic_info']->city,
                'freelancer_state' => $user_details['basic_info']->state,
                'freelancer_country' => $user_details['basic_info']->country,
                'freelancer_profile_img' => $img_url
            );
        }
        $data['proposals'] = $proposals;
        $tablename = array('task_proposal', 'task', 'users', 'user_login');
        $jointype = array('left', 'left', 'left');
        $joincondition = array('task_proposal_alias.task_id = task_alias.task_id', 'users_alias.user_id = task_proposal_alias.user_id', 'task_proposal_alias.user_id = user_login_alias.user_id');
        $condition = 'task_proposal_alias.task_id="' . $task_details->task_id . '"';
        $fieldArr = array('*, task_proposal_alias.doc as task_proposal_doc', '*', '*', '*');
        //$limit			= "all";
        $limit = 5;
        $oderby = 'task_proposal_alias.doc desc';
        $comment_info = $CI->Tasks->getJoinDataByCondition($tablename, $jointype, $joincondition, $condition, $fieldArr, $limit, $oderby);
        //pre($comment_info);die;
        $skills = $CI->Skills->get_all_skill_info();
        if (!empty($comment_info)) {
            foreach ($comment_info as $row) {
                if ($row->profile_image != NULL) {
                    $img_url = base_url() . 'uploads/user/profile_image/' . $row->profile_image;
                } else {
                    $img_url = base_url() . 'assets/img/no-image.png';
                }
                // $posting_date = date('d/m/Y, h iA',strtotime($row->doc));
                $posting_date = date('d/m/Y, h:i A', strtotime($row->task_proposal_doc));
                if ($row->attachments != NULL && $row->attachments != '') {
                    //	$p_attachments = 'Download Attachment  <a href=""><i class="fa fa-download"></i></a>';
                    $p_attachments = $row->proposal_id;
                } else {
                    $p_attachments = '';
                }

                $user_sel_skills = $CI->Users->get_user_selected_skills_by_id($row->user_id);


                $user_skills = "";
                if (!empty($user_sel_skills) && count($user_sel_skills) > 0) {

                    foreach ($user_sel_skills as $sk) {

                        $key = array_search($sk, array_column($skills, 'area_of_interest_id'));
                        $user_skills.=$skills[$key]->name . ",";
                    }
                }

                $user_skills = rtrim($user_skills, ",");


                $cpositivecoin = 0;
                if ($row->total_coins >= 0) {
                    $cpositivecoin = '+ ' . $row->total_coins;
                } else {
                    $cpositivecoin = $row->total_coins;
                }

                $commentArr[] = array('user_id' => $row->user_id, 'profile_image' => $img_url, 'name' => $row->name, 'remarks' => $row->cover_letter, 'bidamount' => $row->terms_amount_max, 'p_attachments' => $p_attachments, 'posting_date' => $posting_date, 'total_positive_coins' => $cpositivecoin, 'total_negative_coins' => $row->total_negative_coins, 'total_coins' => $row->total_coins, 'user_skills' => $user_skills, 'state' => $row->state, 'city' => $row->city, 'public_id' => $row->profile_id, 'task_name' => $row->task_name
                );
                //pre($commentArr);
            }
        }

        $task = $CI->Tasks->task_status_info_by_task_id($task_details->task_id);
//        dd($task_details);
        foreach ($task as $details) {
            //pre($details);
            $continent = $CI->Continent->get_continent_by_id($details['basic_info']['task_origin_location']);
            $country = $CI->Countries->get_country_by_id($details['basic_info']['task_origin_country']);

            $user_info = array();
            if (!empty($details['task_hired']) && count($details['task_hired']) > 1) {
                foreach ($details['task_hired'] as $freelancer_hired) {
                    $user_details = $CI->Users->get_user_profile_info_by_id($freelancer_hired['receiver_id']);
                    //pre($user_details);die;
                    if($user_details['basic_info']) {
                        $user_status = $CI->Users->get_user_info_by_id($freelancer_hired['receiver_id']);
                        $user_profile_image = $user_status->profile_image;
                        if (empty($user_profile_image)) {
                            $user_profile_image = base_url('assets/img/no-image.png');
                        } else {
                            $user_profile_image = base_url('uploads/user/profile_image/' . $user_profile_image);
                        }

                        $is_login = $user_status->is_login;
                        $positivecoin = 0;
                        if ($user_status->total_coins >= 0) {
                            $positivecoin = '+ ' . $user_status->total_coins;
                        } else {
                            $positivecoin = $user_status->total_coins;
                        }
                        $user_info[] = array('freelancer_id' => $user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'total_positive_coins' => $positivecoin, 'total_negative_coins' => $user_status->total_negative_coins, 'hired_id' => base64_encode($freelancer_hired['hired_id']), 'is_online' => (($is_login == '1') ? '<span> </span>' : '<span class="green"> </span>'));
                    }
                }
            }
            if($details['basic_info']) {
                $datetime1 = strtotime($details['basic_info']['task_doc']);
                $datetime2 = strtotime(date("Y-m-d H:i:s"));
                $interval = abs($datetime2 - $datetime1);
                $data['interval']=$details['basic_info']['task_doc'];
                $years = floor($interval / (365 * 60 * 60 * 24));

                $months = floor(($interval - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

                $days = floor(($interval - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                $hours = floor(($interval - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
                $minutes = floor(($interval - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
                $seconds = floor(($interval - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));
                $minutes = round($interval / 60);
                $minutes=date('i', $minutes);

//echo '<pre>'; print_r($details['task_attachments']);die;

                $tduration = "";
                if ($details['basic_info']['task_duration_type'] == 'Hourly') {
                    $tduration = $details['basic_info']['task_duration'] . ' Hour';
                } else if ($details['basic_info']['task_duration_type'] == 'Daily') {
                    $tduration = $details['basic_info']['task_duration'] . ' Day';
                } else if ($details['basic_info']['task_duration_type'] == 'Monthly') {
                    $tduration = $details['basic_info']['task_duration'] . ' Month';
                } else if ($details['basic_info']['task_duration_type'] == 'Yearly') {
                    $tduration = $details['basic_info']['task_duration'] . ' Year';
                }

                $arrTask[] = array('user_task_id' => $details['basic_info']['user_task_id'], 'task_title' => $details['basic_info']['task_name'], 'task_details' => $details['basic_info']['task_details'], 'task_due_date' => $details['basic_info']['task_due_date'], 'task_total_budget' => $details['basic_info']['task_total_budget'], 'task_continent' => $continent->name, 'task_country' => $country->name, 'task_duration' => $minutes, 'task_attachments' => $details['task_attachments'], 'task_requirements' => $details['task_requirements'], 'task_freelancer_hire' => count($details['task_hired']), 'task_freelancer_hired_details' => $user_info, 'offer_send' => $arrOfferSend, 'commentArr' => $commentArr, 'taskDuration' => $tduration,'task_doc'=>$details['basic_info']['task_doc']);
            }
        }
        $tFilter['limit'] = array('limit' => 5);
        $data['top_freelancers'] = $CI->Users->get_top_freelancers_profile_info($tFilter);
        $data['hire_list'] = $CI->Hires->get_old_hire_list($CI->session->userdata('user_id'));
        //echo '<pre>'; print_r($data['hire_list']); die;
        //print_r($arrTask);die;
        $data['task_info'] = $arrTask;

        $AccountForm = $CI->parser->parse('task/task-details', $data, true);

        return $AccountForm;

    }

	 public function view_task_details($taskID = null)
	 { 		         
	 	$CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model('Hires');
        $CI->load->model("Users");        

        $data = $arrTask = $arrOfferSend = $commentArr = array();

        $task_details = $CI->Tasks->get_task_info_by_user_task_id($taskID);
        
        if(empty($task_details))
            redirect('upcoming-projects', 'refresh');

        $offer_send = $CI->Tasks->get_proposal($taskID);
        foreach($offer_send as $offer) {
            $user_details = $CI->Users->get_user_profile_info_by_id($offer->user_id);
            $profile_img = $user_details['basic_info']->profile_image;
            if ($profile_img != NULL) {
                $img_url = base_url() . 'uploads/user/profile_image/' . $profile_img;
            } else {
                $img_url = base_url() . 'assets/img/no-image.png';
            }
            $arrOfferSend[] = array(
                'freelancer_id' => $user_details['basic_info']->user_id,
                'freelancer_name' => $user_details['basic_info']->name,
                'freelancer_city' => $user_details['basic_info']->city,
                'freelancer_state' => $user_details['basic_info']->state,
                'freelancer_country' => $user_details['basic_info']->country,
                'freelancer_profile_img' => $img_url
            );
        }

        $tablename = array('task_proposal', 'task', 'users', 'user_login');
        $jointype = array('left', 'left', 'left');
        $joincondition = array('task_proposal_alias.task_id = task_alias.task_id', 'users_alias.user_id = task_proposal_alias.user_id', 'task_proposal_alias.user_id = user_login_alias.user_id');
        $condition = 'task_proposal_alias.task_id="' . $task_details->task_id . '"';
        $fieldArr = array('*, task_proposal_alias.doc as task_proposal_doc', '*', '*', '*');
        //$limit			= "all";
        $limit = 5;
        $oderby = 'task_proposal_alias.doc desc';


        $comment_info = $CI->Tasks->getJoinDataByCondition($tablename, $jointype, $joincondition, $condition, $fieldArr, $limit, $oderby);
        $skills = $CI->Skills->get_all_skill_info();
        if (!empty($comment_info)) {
            foreach ($comment_info as $row) {
                if ($row->profile_image != NULL) {
                    $img_url = base_url() . 'uploads/user/profile_image/' . $row->profile_image;
                } else {
                    $img_url = base_url() . 'assets/img/no-image.png';
                }
                // $posting_date = date('d/m/Y, h iA',strtotime($row->doc));
                $posting_date = date('d/m/Y, h:i A', strtotime($row->task_proposal_doc));
                if ($row->attachments != NULL && $row->attachments != '') {
                    //	$p_attachments = 'Download Attachment  <a href=""><i class="fa fa-download"></i></a>';
                    $p_attachments = $row->proposal_id;
                } else {
                    $p_attachments = '';
                }



                $user_sel_skills = $CI->Users->get_user_selected_skills_by_id($row->user_id);


                $user_skills = "";
                if (!empty($user_sel_skills) && count($user_sel_skills) > 0) {

                    foreach ($user_sel_skills as $sk) {

                        $key = array_search($sk, array_column($skills, 'area_of_interest_id'));
                        $user_skills.=$skills[$key]->name . ",";
                    }
                }

                $user_skills = rtrim($user_skills, ",");


                $cpositivecoin = 0;
                if ($row->total_coins >= 0) {
                    $cpositivecoin = '+ ' . $row->total_coins;
                } else {
                    $cpositivecoin = $row->total_coins;
                }

                $commentArr[] = array('user_id' => $row->user_id, 'profile_image' => $img_url, 'name' => $row->name, 'remarks' => $row->cover_letter, 'bidamount' => $row->terms_amount_max, 'p_attachments' => $p_attachments, 'posting_date' => $posting_date, 'total_positive_coins' => $cpositivecoin, 'total_negative_coins' => $row->total_negative_coins, 'total_coins' => $row->total_coins, 'user_skills' => $user_skills, 'state' => $row->state, 'city' => $row->city, 'public_id' => $row->profile_id, 'task_name' => $row->task_name
                );
            }
        }

        $task = $CI->Tasks->task_status_info_by_task_id($task_details->task_id);
//        dd($task_details);
        foreach ($task as $details) {
            $continent = $CI->Continent->get_continent_by_id($details['basic_info']['task_origin_location']);
            $country = $CI->Countries->get_country_by_id($details['basic_info']['task_origin_country']);

            $user_info = array();
            if (!empty($details['task_hired']) && count($details['task_hired']) > 0) {
                foreach ($details['task_hired'] as $freelancer_hired) {
                    $user_details = $CI->Users->get_user_profile_info_by_id($freelancer_hired['receiver_id']);
                    $user_status = $CI->Users->get_user_info_by_id($freelancer_hired['receiver_id']);
                    $user_profile_image = $user_status->profile_image;
                    if (empty($user_profile_image)) {
                        $user_profile_image = base_url('assets/img/no-image.png');
                    } else {
                        $user_profile_image = base_url('uploads/user/profile_image/' . $user_profile_image);
                    }

                    $is_login = $user_status->is_login;
                    $positivecoin = 0;
                    if ($user_status->total_coins >= 0) {
                        $positivecoin = '+ ' . $user_status->total_coins;
                    } else {
                        $positivecoin = $user_status->total_coins;
                    }
                    $user_info[] = array('freelancer_id' => $user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'total_positive_coins' => $positivecoin, 'total_negative_coins' => $user_status->total_negative_coins, 'hired_id' => base64_encode($freelancer_hired['hired_id']), 'is_online' => (($is_login == '1') ? '<span> </span>' : '<span class="green"> </span>'));
                }
            }

            $datetime1 = strtotime($details['basic_info']['task_doc']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval = abs($datetime2 - $datetime1);

            $years = floor($interval / (365 * 60 * 60 * 24));

            $months = floor(($interval - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

            $days = floor(($interval - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            $hours = floor(($interval - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
            $minutes = floor(($interval - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
            $seconds = floor(($interval - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));
            $minutes = round($interval / 60);
		   $minutes=date('i', $minutes);

//echo '<pre>'; print_r($details['task_attachments']);die;

            $tduration = "";
            if ($details['basic_info']['task_duration_type'] == 'Hourly') {
                $tduration = $details['basic_info']['task_duration'] . ' Hour';
            } else if ($details['basic_info']['task_duration_type'] == 'Daily') {
                $tduration = $details['basic_info']['task_duration'] . ' Day';
            } else if ($details['basic_info']['task_duration_type'] == 'Monthly') {
                $tduration = $details['basic_info']['task_duration'] . ' Month';
            } else if ($details['basic_info']['task_duration_type'] == 'Yearly') {
                $tduration = $details['basic_info']['task_duration'] . ' Year';
            }

            $arrTask[] = array('user_task_id' => $details['basic_info']['user_task_id'], 'task_title' => $details['basic_info']['task_name'], 'task_details' => $details['basic_info']['task_details'], 'task_due_date' => $details['basic_info']['task_due_date'], 'task_total_budget' => $details['basic_info']['task_total_budget'], 'task_continent' => $continent->name, 'task_country' => $country->name, 'task_duration' => $minutes, 'task_attachments' => $details['task_attachments'], 'task_requirements' => $details['task_requirements'], 'task_freelancer_hire' => count($details['task_hired']), 'task_freelancer_hired_details' => $user_info, 'offer_send' => $arrOfferSend, 'commentArr' => $commentArr, 'taskDuration' => $tduration);
        }
        $tFilter['limit'] = array('limit' => 5);
        $data['top_freelancers'] = $CI->Users->get_top_freelancers_profile_info($tFilter);
        $data['hire_list'] = $CI->Hires->get_old_hire_list($CI->session->userdata('user_id'));
      
        //echo '<pre>'; print_r($data['hire_list']); die;
        //print_r($arrTask);die;
        $data['task_info'] = $arrTask;

        $AccountForm = $CI->parser->parse('tasklist/view-task-details', $data, true);

        return $AccountForm;
}

	public function comment_post($taskID, $submitData){

		$task_user_id = $taskID;

		$CI =& get_instance();

        $CI->load->model('Tasks');

        $CI->load->model("Users");    

		if($task_user_id != ''){

			$task = $CI->Tasks->get_task_info_by_user_task_id($task_user_id);

			$task_id = $task->task_id;

		}else{

			$task_id = 0;

		}		

		

		$submitData = array(

			'user_id' => $submitData['user_id'],

			'remarks' => $submitData['taskRemark'],

			'remark_doc' => date('Y-m-d H:i:s'),

			'tast_id' => $task_id,

			'tast_user_id' => $task_user_id,

			'is_active' => '1'

		);

		

		$CI->Tasks->insert_data('comment_master',$submitData);

		redirect('task-details/'.$task_user_id, 'refresh');

	}

	

    public function view_all_offers_page($taskID = null, $userInfo = null) {

        $CI =& get_instance();

        $CI->load->model('Tasks');

        $CI->load->model("Users");        



        $data = $arrTask = $arrOfferSend = array();



        $task_details = $CI->Tasks->get_task_info_by_user_task_id($taskID);

        if(empty($task_details))

            redirect('upcoming-projects', 'refresh');





        $offer_send = $CI->Tasks->task_offers_list_by_task_id($task_details->task_id, $userInfo['user_id'], 100);

        foreach($offer_send as $offer) {

            $user_details = $CI->Users->get_user_profile_info_by_id($offer->offer_send_by);

            $user_status = $CI->Users->get_user_info_by_id($offer->offer_send_by);

            $user_profile_image = $user_status->profile_image;

            if(empty($user_profile_image)) {

                $user_profile_image = base_url('assets/img/no-image.png');

            }

            else {

                $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          

            }



            $is_login = $user_status->is_login;               

         

            $arrOfferSend[] = array('offer_id' => base64_encode($offer->offer_id.'_'.$offer->user_task_id), 'freelancer_id' => $user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'is_online' => (($is_login == '1')?' round-color':''), 'offer_details' => nl2br($offer->offer_details));

        }





        $task = $CI->Tasks->task_status_info_by_task_id($task_details->task_id); 

        foreach($task as $details) {

            $continent = $CI->Continent->get_continent_by_id($details['basic_info']['task_origin_location']);

            $country = $CI->Countries->get_country_by_id($details['basic_info']['task_origin_country']);

           



            $datetime1 = strtotime($details['basic_info']['task_doc']);

            $datetime2 = strtotime(date("Y-m-d H:i:s"));

            $interval  = abs($datetime2 - $datetime1);



            // To get the year divide the resultant date into 

            // total seconds in a year (365*60*60*24) 

            $years = floor($interval / (365*60*60*24)); 



            // To get the month, subtract it with years and 

            // divide the resultant date into 

            // total seconds in a month (30*60*60*24) 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    



            // To get the day, subtract it with years and  

            // months and divide the resultant date into 

            // total seconds in a days (60*60*24) 

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));



            // To get the hour, subtract it with years,  

            // months & seconds and divide the resultant 

            // date into total seconds in a hours (60*60) 

            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 



            // To get the minutes, subtract it with years, 

            // months, seconds and hours and divide the  

            // resultant date into total seconds i.e. 60 

            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);



            // To get the minutes, subtract it with years, 

            // months, seconds, hours and minutes  

            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     



            // Print the result 

            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 



            $minutes   = round($interval / 60);







            $arrTask[] = array('user_task_id' => $details['basic_info']['user_task_id'], 'task_title' => $details['basic_info']['task_name'], 'task_details' => $details['basic_info']['task_details'], 'task_due_date' => $details['basic_info']['task_due_date'], 'task_total_budget' => $details['basic_info']['task_total_budget'], 'task_continent' => $continent->name, 'task_country' => $country->name, 'task_duration' => $minutes, 'task_attachments' => $details['task_attachments'], 'task_requirements' => $details['task_requirements'], 'offer_send' => $arrOfferSend);



        }

        //print_r($arrTask);

        $data['task_info'] = $arrTask;                      



        $AccountForm = $CI->parser->parse('task/view-all-offers',$data,true);

        return $AccountForm;

    }     



    public function view_offer_details_page($OffertaskID = null, $userInfo = null) {

        $CI =& get_instance();

        $CI->load->model('Tasks');

        $CI->load->model("Users");        



        $data = $arrTask = array();



        // base64_encode($offer->offer_id.'_'.$offer->user_task_id)

        $offerId = base64_decode($OffertaskID);

        $offerId = explode('_', $offerId);





        $task_offer_details = $CI->Tasks->get_task_offer_details($offerId[0]);

        if(empty($task_offer_details))

            redirect('dashboard', 'refresh');





        $offer_send = $CI->Tasks->task_offers_list_by_offer_id($task_offer_details->offer_id, $userInfo['user_id'], 10);

        foreach($offer_send as $offer) {

            $user_details = $CI->Users->get_user_profile_info_by_id($offer->offer_send_by);

            $user_status = $CI->Users->get_user_info_by_id($offer->offer_send_by);

            $user_profile_image = $user_status->profile_image;

            if(empty($user_profile_image)) {

                $user_profile_image = base_url('assets/img/no-image.png');

            }

            else {

                $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          

            }



            $is_login = $user_status->is_login;               

            $arrOfferSend[] = array('offer_id' => base64_encode($offer->offer_id.'_'.$offer->user_task_id), 'freelancer_id' => $user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'is_online' => (($is_login == '1')?' round-color':''), 'offer_details' => nl2br($offer->offer_details));
        }

        $task = $CI->Tasks->task_status_info_by_task_id($task_offer_details->task_id); 
        //print_r($task);

        foreach($task as $details) {
            $continent = $CI->Continent->get_continent_by_id($details['basic_info']['task_origin_location']);
            $country = $CI->Countries->get_country_by_id($details['basic_info']['task_origin_country']);

            $datetime1 = strtotime($details['basic_info']['task_doc']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);


            // To get the year divide the resultant date into 
            // total seconds in a year (365*60*60*24) 

            $years = floor($interval / (365*60*60*24)); 


            // To get the month, subtract it with years and 
            // divide the resultant date into 
            // total seconds in a month (30*60*60*24) 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    

            // To get the day, subtract it with years and  
            // months and divide the resultant date into 
            // total seconds in a days (60*60*24) 

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            // To get the hour, subtract it with years,  
            // months & seconds and divide the resultant 
            // date into total seconds in a hours (60*60) 

            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 


            // To get the minutes, subtract it with years, 
            // months, seconds and hours and divide the  
            // resultant date into total seconds i.e. 60 

            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

            // To get the minutes, subtract it with years, 
            // months, seconds, hours and minutes  
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     

            // Print the result 
            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 
            $minutes   = round($interval / 60);

            $arrTask[] = array('user_task_id' => $details['basic_info']['user_task_id'], 'task_title' => $details['basic_info']['task_name'], 'task_details' => $details['basic_info']['task_details'], 'task_due_date' => $details['basic_info']['task_due_date'], 'task_total_budget' => $details['basic_info']['task_total_budget'], 'task_continent' => $continent->name, 'task_country' => $country->name, 'task_duration' => $minutes, 'task_attachments' => $details['task_attachments'], 'task_requirements' => $details['task_requirements'], 'task_freelancer_hire' => count($details['task_hired']), 'offer_id' => $arrOfferSend[0]['offer_id'], 'offer_id' => $arrOfferSend[0]['offer_id'], 'offer_id' => $arrOfferSend[0]['offer_id'], 'offer_id' => $arrOfferSend[0]['offer_id'], 'freelancer_id' => $arrOfferSend[0]['freelancer_id'], 'freelancer_name' => $arrOfferSend[0]['freelancer_name'], 'freelancer_country' => $arrOfferSend[0]['freelancer_country'], 'freelancer_state' => $arrOfferSend[0]['freelancer_state'], 'freelancer_city' => $arrOfferSend[0]['freelancer_city'], 'user_image' => $arrOfferSend[0]['user_image'], 'is_online' => $arrOfferSend[0]['is_online'], 'offer_details' => $arrOfferSend[0]['offer_details']);

        }

        //print_r($arrTask);

        $data['task_info'] = $arrTask;                      
        $AccountForm = $CI->parser->parse('task/view-offer-details',$data,true);
        return $AccountForm;
    }     

    public function offer_details_page($taskID = null, $userInfo = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model("Users");        

        $data = $arrTask = $arrOfferSend = array();

        $task_details = $CI->Tasks->get_task_info_by_user_task_id($taskID);
        if(empty($task_details))
            redirect('upcoming-projects', 'refresh');

        $offer_send = $CI->Tasks->offered_send_list_by_user($userInfo['user_id'],PER_PAGE,$taskID);
        foreach($offer_send as $offer) {
            $user_details = $CI->Users->get_user_profile_info_by_id($offer->receiver_id);
            $user_status = $CI->Users->get_user_info_by_id($offer->receiver_id);
			
			if(!empty($user_status)){
				$total_positive_coins = $user_status->total_positive_coins;
				$total_negative_coins = $user_status->total_negative_coins;
				$total_connects = $user_status->total_connects;
			}else{
				$total_positive_coins = $total_negative_coins = $total_connects = 0;
			}

            $user_profile_image = $user_status->profile_image;
            if(empty($user_profile_image)) {
                $user_profile_image = base_url('assets/img/no-image.png');
            } else {
                $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
            }

            $is_login = $user_status->is_login;  

            $arrOfferSend[] = array('is_responded' => $offer->is_responded, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'total_positive_coins' => $total_positive_coins, 'total_negative_coins' => $total_negative_coins, 'total_connects' => $total_connects, 'is_online' => (($is_login == '1')?'<div class="round"> </div>':''), 'response_action_buttons' => (($offer->is_responded == 1)?'<ul><li> <a href="'.base_url().'direct-hire"> Hire</a> </li><li> <a href="'.base_url().'messages"> Send Message </a> </li></ul>':'<a href="#" class="no-rs"> No Response </a>'));

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
                    }  else {
                        $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
                    }

                    $is_login = $user_status->is_login;               

                    $user_info[] = array('freelancer_id' => $user_details['basic_info']->user_id, 'freelancer_name' => $user_details['basic_info']->name, 'freelancer_country' => $user_details['basic_info']->country, 'freelancer_state' => $user_details['basic_info']->state, 'freelancer_city' => $user_details['basic_info']->city, 'user_image' => $user_profile_image, 'is_online' => (($is_login == '1')?'<span> </span>':''));

                }
            }            

            $datetime1 = strtotime($details['basic_info']['task_doc']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);


            // To get the year divide the resultant date into 
            // total seconds in a year (365*60*60*24) 

            $years = floor($interval / (365*60*60*24)); 

            // To get the month, subtract it with years and 
            // divide the resultant date into 
            // total seconds in a month (30*60*60*24) 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    

            // To get the day, subtract it with years and  
            // months and divide the resultant date into 
            // total seconds in a days (60*60*24) 

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            // To get the hour, subtract it with years,  
            // months & seconds and divide the resultant 
            // date into total seconds in a hours (60*60) 

            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 

            // To get the minutes, subtract it with years, 
            // months, seconds and hours and divide the  
            // resultant date into total seconds i.e. 60 

            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

            // To get the minutes, subtract it with years, 
            // months, seconds, hours and minutes  
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     

            // Print the result 

            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 

          $minutes   = round($interval / 60);

            $arrTask[] = array('task_title' => $details['basic_info']['task_name'], 'task_details' => $details['basic_info']['task_details'], 'task_due_date' => $details['basic_info']['task_due_date'], 'task_total_budget' => $details['basic_info']['task_total_budget'], 'task_continent' => $continent->name, 'task_country' => $country->name, 'task_duration' => $minutes, 'task_attachments' => $details['task_attachments'], 'task_requirements' => $details['task_requirements'], 'task_freelancer_hire' => count($details['task_hired']), 'task_freelancer_hired_details' => $user_info, 'offer_send' => $arrOfferSend);

        }
        //print_r($arrTask);
        $data['task_info'] = $arrTask;                      

        $AccountForm = $CI->parser->parse('task/offer-details',$data,true);
        return $AccountForm;
    } 

    public function ajax_sent_offer_page($userInfo = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model("Users");        

        $data = array();     
        $output = '';                      
        $data = $CI->input->post();  
        $searchCriteria = (!empty($data['searchCriteria']))?$data['searchCriteria']:'';   
        $jobs = $CI->Tasks->ajax_list_offer_send_by_post($userInfo['user_id'], $searchCriteria);       
        foreach($jobs as $job) {
            $datetime1 = strtotime($job['basic_info']['task_doc']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);

            // To get the year divide the resultant date into 
            // total seconds in a year (365*60*60*24) 
            $years = floor($interval / (365*60*60*24)); 

            // To get the month, subtract it with years and 
            // divide the resultant date into 
            // total seconds in a month (30*60*60*24) 
            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    

            // To get the day, subtract it with years and  
            // months and divide the resultant date into 
            // total seconds in a days (60*60*24) 
            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            // To get the hour, subtract it with years,  
            // months & seconds and divide the resultant 
            // date into total seconds in a hours (60*60) 
            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 

            // To get the minutes, subtract it with years, 
            // months, seconds and hours and divide the  
            // resultant date into total seconds i.e. 60 

            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

            // To get the minutes, subtract it with years, 
            // months, seconds, hours and minutes  

            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     

            // Print the result 
            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 

            $minutes   = round($interval / 60);

            $output .= '<div class="my-mbl-app">
            <div class="my-mbl-app-lft"> <small> ' . $minutes . ' minutes ago </small>
              <h2> ' . $job['basic_info']['task_name'] . ' </h2>
              <ul>
                <li style="padding: 6px 12px;"><span style="margin-right: 5px;"><b>'.$job['offer_send_wait_count'].'</b></span><span> Waiting for freelancer </span></li>
                <li style="background: #e1f7e1 0px 2px;padding: 7px 23px 7px 13px;"><span style="margin-right: 5px;"><b>'.$job['offer_send_response_count'].'</b></span><span>  Accepted  </span></li>
                <li style="background: #fddbe7 0px 2px;padding: 7px 23px 7px 13px;"><span style="margin-right: 5px;"><b>'.$job['offer_send_refuse_count'].'</b></span><span> Refusal </span></li>
              </ul>
              <div class="freelancerDiv">
                <ul>
                  <li> <span> Freelancer </span> </li>';


            foreach($job['freelancer_list'] as $freelancer) {
                $userData = $CI->Users->get_user_info_by_id($freelancer['freelancer_id']);
                $user_profile_image = $userData->profile_image;
                if(empty($user_profile_image)) {
                    $user_profile_image = base_url('assets/img/no-image.png');
                }  else {
                    $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
                }

                if(empty($userData->is_login) || $userData->is_login === 0) {
                    $user_is_login = '<em> </em>';
                } else {
                    $user_is_login = '<small> </small>';            
                } 

                $output .= '<li> <img src="'.$user_profile_image.'" alt="Freelancer" style="width:65px;height:64px;"> '.$user_is_login.' </li>';
            }

            $output .= '</ul>
              </div>
            </div>
            <div class="my-mbl-app-rht"> <a href="'.base_url().'offer-details/'.$job['basic_info']['user_task_id'].'" class="view-btn1"> View Details </a> <a href="#" class="view-btn2" onClick="setUserTaskId(\''.$job['basic_info']['user_task_id'].'\')" data-toggle="modal" data-target="#myModal7"> Close This Offer </a> </div>

          </div>';                  
        }
        return $output;
    } 

    public function ajax_close_offer_send_user($userInfo = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');       

        $data = $CI->input->post();  
        $task_id = (!empty($data['task_id']))?$data['task_id']:'';   

        if(!empty($task_id)) {
            $result = $CI->Tasks->close_offer_send_by_user($userInfo['user_id'], $task_id);
            if($result) {
                $CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Successfully closed offers for mentioned job post.</div>');
                $response = array('status' => 1, 'message' => 'Successfully closed offers for mentioned job post');
                return json_encode($response);

            } else {
                $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to close offers for mentioned job post.</div>');
                $response = array('status' => 0, 'message' => 'Unable to close offers for mentioned job post');
                return json_encode($response);
            }
        }  else {
            $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to close offers for mentioned job post.</div>');
            $response = array('status' => 0, 'message' => 'Unable to close offers for mentioned job post');
            return json_encode($response);
        }
    }     


public function sent_offer_page_new($userInfo = null, $type) {
        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model("Users");
        $CI->load->library("pagination");

        $config = $data = $arrJobs = array();
        $config["base_url"] = base_url() . "sent-offer";
        $config["total_rows"] = $CI->Tasks->count_list_offer_send_by_post();
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;
        $config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';  
        $config['last_link'] = 'Last';
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

        $CI->pagination->initialize($config);

        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        

        $data["links"] = $CI->pagination->create_links();

        $jobs = $CI->Tasks->list_offer_send_by_post($userInfo['user_id'], $config["per_page"], $page); 
		$jobs=array();
        foreach($jobs as $job) {
            $arrUserDetailList = array();
            foreach($job['freelancer_list'] as $freelancer) {
                $userData = $CI->Users->get_user_info_by_id($freelancer['freelancer_id']);
                $user_profile_image = $userData->profile_image;
                if(empty($user_profile_image)) {
                    $user_profile_image = base_url('assets/img/no-image.png');
                } else {
                    $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
                }
                if(empty($userData->is_login) || $userData->is_login === 0) {
                    $user_is_login = '<em> </em>';
                } else {
                    $user_is_login = '<small> </small>';            
                } 

                $arrUserDetailList[] = array('user_profile_image' => $user_profile_image, 'user_is_login' => $user_is_login);
            }

            $datetime1 = strtotime($job['basic_info']['task_doc']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);

            // To get the year divide the resultant date into 
            // total seconds in a year (365*60*60*24) 

            $years = floor($interval / (365*60*60*24)); 

            // To get the month, subtract it with years and 
            // divide the resultant date into 
            // total seconds in a month (30*60*60*24) 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    

            // To get the day, subtract it with years and  
            // months and divide the resultant date into 
            // total seconds in a days (60*60*24) 

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            // To get the hour, subtract it with years,  
            // months & seconds and divide the resultant 
            // date into total seconds in a hours (60*60) 

            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 

            // To get the minutes, subtract it with years, 
            // months, seconds and hours and divide the  
            // resultant date into total seconds i.e. 60 
            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

            // To get the minutes, subtract it with years, 
            // months, seconds, hours and minutes  
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     

            // Print the result 
            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 
            $minutes   = round($interval / 60);

            $arrJobs[] = array('task_id' => $job['basic_info']['task_doc'], 'user_task_id' => $job['basic_info']['user_task_id'], 'task_name' => $job['basic_info']['task_name'], 'task_details' => $job['basic_info']['task_details'], 'task_due_date' => $job['basic_info']['task_due_date'], 'task_origin_location' => $job['basic_info']['task_origin_location'], 'task_origin_country' => $job['basic_info']['task_origin_country'], 'task_total_budget' => $job['basic_info']['task_total_budget'], 'task_history' =>$job['basic_info']['task_history'], 'task_created_by' => $job['basic_info']['task_created_by'], 'task_doc' => $job['basic_info']['task_doc'], 'task_post_duration' => $minutes, 'freelancer_offer' => $arrUserDetailList, 'waiting_for_response' => $job['offer_send_wait_count'], 'refused' => $job['offer_send_refuse_count'], 'responded' => $job['offer_send_response_count']);          

        }

        //print_r($arrJobs);
        $data["jobs"] = $arrJobs; 

		$data['analytics']=array();
		$CI->db->join('task_hired', 'task_hired.task_id = task.task_id');
        $CI->db->join('users', 'users.user_id = task.task_created_by');
        $CI->db->where('task_created_by', $userInfo['user_id']);
        $CI->db->where('task.task_is_complete', 1);
        $CI->db->where('task.task_status', 1);
        if($CI->input->post('from_date')) {
        	$from_date = date('Y-m-d', strtotime($CI->input->post('from_date')));        	
        	$CI->db->where('task.task_doc >=', $from_date);
        }
        if($CI->input->post('to_date')) {
        	$to_date = date('Y-m-d', strtotime($CI->input->post('to_date')));        	
        	$CI->db->where('task.task_doc <=', $to_date);
        }
		$data['selectfreeid']=""; 
		if($CI->input->post('freelancer_id')){
			$data['selectfreeid']=$CI->input->post('freelancer_id');
			$CI->db->where('task.user_task_id', $CI->input->post('freelancer_id'));
		} 
		
		
		$data['search_skill']=array();
		if(!empty($CI->input->post('fldSkillRequired'))){
			$skillCond="(";
			$skillArray=$CI->input->post('fldSkillRequired');
		 
			foreach($skillArray as $skills){
				$skillCond.="task.task_keywords LIKE '".$skills."' or task.task_keywords LIKE '%,".$skills."' or task.task_keywords LIKE '".$skills.",%' or task.task_keywords LIKE '%,".$skills.",%' or ";
			}
			$skillCond=rtrim($skillCond,' or').')';
			$data['search_skill']=$CI->input->post('fldSkillRequired');
			$CI->db->where($skillCond); 
		}
		
		if($type=='active-invitation'){
			$CI->db->where('month(task.task_doc)', date('m'));
		}
		if($type=='previous-invitation'){
			$CI->db->where('month(task.task_doc) <', date('m'));
		} 
        $CI->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
		
        $result = $CI->db->get('task');
		 
		 
        if($result->num_rows() > 0){
            $data['analytics']=$result->result();
        }  
		 
		$skills = $CI->Skills->get_all_skill_info();
		 
        if(!empty($skills)) {
        	foreach($skills as $skill) {
        	    $arrSkills[] = array('key' => $skill->area_of_interest_id, 'value' => $skill->name, 'currentselection' => ((!empty($postData) && in_array($skill->area_of_interest_id, $data['fldSkillRequired']))?'selected':''));

        	}
        }   
		
		$data['skills'] = $arrSkills;	
	 
		$data['type']=$type;

		$data['freelances']=$CI->db->order_by('total_positive_coins','DESC')->limit(10)->get_where('user_login',array('user_type'=>'4'))->result_array();
		 
        $AccountForm = $CI->parser->parse('task/sent-offer',$data,true);
        return $AccountForm;

    }


    public function sent_offer_page($userInfo = null, $pageIndex = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model("Users");
        $CI->load->library("pagination");

        $config = $data = $arrJobs = array();
        $config["base_url"] = base_url() . "sent-offer";
        $config["total_rows"] = $CI->Tasks->count_list_offer_send_by_post();
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;
        $config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';  
        $config['last_link'] = 'Last';
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

        $CI->pagination->initialize($config);

        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;        

        $data["links"] = $CI->pagination->create_links();

        $jobs = $CI->Tasks->list_offer_send_by_post($userInfo['user_id'], $config["per_page"], $page); 
        foreach($jobs as $job) {
            $arrUserDetailList = array();
            foreach($job['freelancer_list'] as $freelancer) {
                $userData = $CI->Users->get_user_info_by_id($freelancer['freelancer_id']);
                $user_profile_image = $userData->profile_image;
                if(empty($user_profile_image)) {
                    $user_profile_image = base_url('assets/img/no-image.png');
                } else {
                    $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);          
                }
                if(empty($userData->is_login) || $userData->is_login === 0) {
                    $user_is_login = '<em> </em>';
                } else {
                    $user_is_login = '<small> </small>';            
                } 

                $arrUserDetailList[] = array('user_profile_image' => $user_profile_image, 'user_is_login' => $user_is_login);
            }

            $datetime1 = strtotime($job['basic_info']['task_doc']);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);

            // To get the year divide the resultant date into 
            // total seconds in a year (365*60*60*24) 

            $years = floor($interval / (365*60*60*24)); 

            // To get the month, subtract it with years and 
            // divide the resultant date into 
            // total seconds in a month (30*60*60*24) 

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));    

            // To get the day, subtract it with years and  
            // months and divide the resultant date into 
            // total seconds in a days (60*60*24) 

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            // To get the hour, subtract it with years,  
            // months & seconds and divide the resultant 
            // date into total seconds in a hours (60*60) 

            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60)); 

            // To get the minutes, subtract it with years, 
            // months, seconds and hours and divide the  
            // resultant date into total seconds i.e. 60 
            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

            // To get the minutes, subtract it with years, 
            // months, seconds, hours and minutes  
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));     

            // Print the result 
            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds); 
            $minutes   = round($interval / 60);

            $arrJobs[] = array('task_id' => $job['basic_info']['task_doc'], 'user_task_id' => $job['basic_info']['user_task_id'], 'task_name' => $job['basic_info']['task_name'], 'task_details' => $job['basic_info']['task_details'], 'task_due_date' => $job['basic_info']['task_due_date'], 'task_origin_location' => $job['basic_info']['task_origin_location'], 'task_origin_country' => $job['basic_info']['task_origin_country'], 'task_total_budget' => $job['basic_info']['task_total_budget'], 'task_history' =>$job['basic_info']['task_history'], 'task_created_by' => $job['basic_info']['task_created_by'], 'task_doc' => $job['basic_info']['task_doc'], 'task_post_duration' => $minutes, 'freelancer_offer' => $arrUserDetailList, 'waiting_for_response' => $job['offer_send_wait_count'], 'refused' => $job['offer_send_refuse_count'], 'responded' => $job['offer_send_response_count']);          

        }

        //print_r($arrJobs);
        $data["jobs"] = $arrJobs;       

        $AccountForm = $CI->parser->parse('task/sent-offer',$data,true);
        return $AccountForm;

    }

    public function received_offers_page($userInfo = null, $user_task_id = null) {

        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model("Users");
        $CI->load->model("Hires");
        $CI->load->library("pagination");

        $config = $data = $arrJobs = array();
        $config["base_url"] = base_url() . "sent-offer";
        $config["total_rows"] = $CI->Tasks->count_list_offer_send_by_post();
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;
        $config['full_tag_open'] = '<ul class="pagination" style="margin-top:20px;">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
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

        $CI->pagination->initialize($config);

        $page = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : 0;

        $data["links"] = $CI->pagination->create_links();

        /*$arrOfferSend[] = array();
        $offer_send = $CI->Tasks->get_freelancers_proposal($taskID);
        //pre($offer_send);die;
        foreach($offer_send as $offer) {
            $user_details = $CI->Users->get_user_profile_info_by_id($offer->user_id);
            $profile_img=$user_details['basic_info']->profile_image;
            if( $profile_img != NULL){
                $img_url = base_url().'uploads/user/profile_image/'.$profile_img;
            }else{
                $img_url = base_url().'assets/img/no-image.png';
            }
            $arrOfferSend[] = array(
                'freelancer_id' => $user_details['basic_info']->user_id,
                'freelancer_name' => $user_details['basic_info']->name,
                'freelancer_city' => $user_details['basic_info']->city,
                'freelancer_state' => $user_details['basic_info']->state,
                'freelancer_country' => $user_details['basic_info']->country,
                'freelancer_profile_img' => $img_url
            );
        }*/

        $jobs = $CI->Tasks->get_freelancers_proposal($user_task_id);
        //pre($jobs);die;
        $arrUserDetailList = array();
        $a_attachments = array();
        /*if(isset($jobs[0]->task_id)) {
            $task = $CI->Tasks->task_status_info_by_task_id($jobs[0]->task_id);
            $a_attachments = $task[0]['task_attachments'];
            pre($a_attachments);
            die;
        }*/
        foreach($jobs as $job) {
            $userData = $CI->Users->get_user_info_by_id($job->user_id);
            $user_profile_image = $userData->profile_image;
            if(empty($user_profile_image)) {
                $user_profile_image = base_url('assets/img/no-image.png');
            } else {
                $user_profile_image = base_url('uploads/user/profile_image/'.$user_profile_image);
            }
            if(empty($userData->is_login) || $userData->is_login === 0) {
                $user_is_login = '<em> </em>';
            } else {
                $user_is_login = '<small> </small>';
            }

            $task_attachments = array();
            if($job->attachments) {
                $a_attachments = explode(",",$job->attachments);
                foreach ($a_attachments as $attachVal){
                    $arrFileName = explode('_', $attachVal);
                    $file_typ_img = get_file_ext($attachVal);
                    $task_attachments[] = array('file_ext_type' => $file_typ_img,  'file_name' => $attachVal, 'file_display_name' => end($arrFileName));
                }
            }

            $datetime1 = strtotime($job->doc);
            $datetime2 = strtotime(date("Y-m-d H:i:s"));
            $interval  = abs($datetime2 - $datetime1);

            $posting_date = date('d/m/Y, h:i A',strtotime($job->doc));

            // To get the year divide the resultant date into
            // total seconds in a year (365*60*60*24)

            $years = floor($interval / (365*60*60*24));

            // To get the month, subtract it with years and
            // divide the resultant date into
            // total seconds in a month (30*60*60*24)

            $months = floor(($interval - $years * 365*60*60*24) / (30*60*60*24));

            // To get the day, subtract it with years and
            // months and divide the resultant date into
            // total seconds in a days (60*60*24)

            $days = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            // To get the hour, subtract it with years,
            // months & seconds and divide the resultant
            // date into total seconds in a hours (60*60)

            $hours = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24) / (60*60));

            // To get the minutes, subtract it with years,
            // months, seconds and hours and divide the
            // resultant date into total seconds i.e. 60
            $minutes = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

            // To get the minutes, subtract it with years,
            // months, seconds, hours and minutes
            $seconds = floor(($interval - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));

            // Print the result
            //printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds);
            $minutes   = round($interval / 60);

            $skills = "";
            if($skills = $CI->Skills->get_user_skills($userData->user_id)) {
                $skills = implode(", ", $skills);
            }

            $jobInfo = $CI->Tasks->get_task_info_by_user_task_id($user_task_id);            
            if(!empty($jobInfo)) {
                $jobInfo = (array) $jobInfo;
                $task_name = $jobInfo['task_name'];
            } else {
                $jobInfo = array();
                $task_name = '';
            }

            $arrJobs[] = array(
                'user_task_id' => $user_task_id, 
                'task_name' => $task_name,                
                'terms_amount_max' => $job->terms_amount_max, 
                'terms_amount_min' => $job->terms_amount_min, 
                'remarks' => $job->cover_letter, 
                'task_id' => $job->task_id, 
                'proposal_id' => $job->proposal_id, 
                'user_profile_image' => $user_profile_image, 
                'user_is_login' => $user_is_login, 
                'task_post_duration' => $minutes,
                'name' => $userData->name,
                'country' => $userData->country,
                'profile_title' => $userData->profile_title,
                'profile_title_skill' => $skills,
                'user_id' => $userData->user_id,
                'profile_id' => $userData->profile_id,
                'total_positive_coins' => $userData->total_positive_coins,
                'total_negative_coins' => $userData->total_negative_coins,
                'is_login' => $userData->is_login,
                'posting_date' => $posting_date,
                'p_attachments' => $job->proposal_id,
                'attachments' => $task_attachments,
                'job_doc' => isset($job->doc) ?  get_time_ago($job->doc) : ''
            );

            //$arrJobs[] = array('task_id' => $job['basic_info']['task_doc'], 'user_task_id' => $job['basic_info']['user_task_id'], 'task_name' => $job['basic_info']['task_name'], 'task_details' => $job['basic_info']['task_details'], 'task_due_date' => $job['basic_info']['task_due_date'], 'task_origin_location' => $job['basic_info']['task_origin_location'], 'task_origin_country' => $job['basic_info']['task_origin_country'], 'task_total_budget' => $job['basic_info']['task_total_budget'], 'task_history' =>$job['basic_info']['task_history'], 'task_created_by' => $job['basic_info']['task_created_by'], 'task_doc' => $job['basic_info']['task_doc'], 'task_post_duration' => $minutes, 'freelancer_offer' => $arrUserDetailList, 'waiting_for_response' => $job['offer_send_wait_count'], 'refused' => $job['offer_send_refuse_count'], 'responded' => $job['offer_send_response_count']);

        }

        //pre($a_attachments);die;
        $data["jobs"] = $arrJobs;
        $AccountForm = $CI->parser->parse('task/received-offers',$data,true);
        return $AccountForm;

    }

    public function send_offer_to_user($postData = null, $userInfo = null) {
        $CI =& get_instance();
        $CI->load->model('Tasks');
        $CI->load->model('Users');  

        if(!empty($postData) && !empty($userInfo)) {
            $table_data = array(); 
            foreach($postData['arrSelectedFreelancer'] as $fId) {
                $table_data[] = array('task_id' => $postData['fldJobTitle'], 'freelancer_id' => $fId, 'offer_send_by' => $userInfo['user_id']);
            }
            $result = $CI->Tasks->save_offer($table_data, $userInfo);
			 
            if($result['status']==TRUE) {
				 
                $task_details = $CI->Tasks->get_task_info_by_user_task_id($postData['fldJobTitle']);

                foreach($postData['arrSelectedFreelancer'] as $fId) {
                    $user_details = $CI->Users->get_user_info_by_id($fId);
                    $from = $userInfo['user_email'];    //senders email address
                    $subject = 'Invitation to Interview for:'.$task_details->task_name;  //email subject

                    //message body
                    $message = 'Dear User,<br><br> <strong>Congrats! You have been invited to submit a proposal!</strong><br><br>Read more about the job below and submit a proposal if you are interested.<br><br><strong><a href=\'' . base_url() . 'view-task-details/' . $postData['fldJobTitle'] . '\'>'.$task_details->task_name.'</a></strong><br/>'.$task_details->task_details.'<br/></br>Thanks';

                    $CI->email->from($from);
                    $CI->email->to($user_details->email);
                    $CI->email->subject($subject);
                    $CI->email->message($message);
                    $CI->email->send();
                }
                $CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Offer send to freelancer successfully.</div>');            
            } else {
				 
				$message=$result['message'];
				
                $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">'.$message.'</div>');
            } 
            redirect('search-freelancer', 'refresh');            
        } else {
            $CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">No data available for storing.</div>');
            redirect('search-freelancer', 'refresh');
        }
    }		


	public function getCountryByContinent() {

		$CI =& get_instance();		
		$output = "";
		$data = $CI->input->post();
		if(!empty($data)) {
            $output .= '<select name="fldSelCountry" id="fldSelCountry" required><option value="">Select</option>';		
            $countries = $CI->Countries->get_country_by_continent_id($data['fldSelContinent']);	
            if(!empty($countries)) {
        	    foreach($countries as $country) {
        	    	$output .= '<option value="' . $country->country_id . '">' . $country->name . '</option>';
        	    }
            }
            $output .= '</select>';
		} else {
            $output .= '<select name="fldSelCountry" id="fldSelCountry" required><option value="">Select</option></select>';
        }
        return $output;
    }



    public function download_file($filename = null) {
        if ($filename) {
             $file = realpath (FCPATH.'uploads/user/project_documents') . "/" .  trim(urldecode($filename));
	    
            // check file exists    
            if (file_exists ( $file )) {
                $arrFileName = explode('_', $file);
                // get file content
                $data = file_get_contents ( $file );
                //force download
                force_download ( end($arrFileName), $data );
            } else {
                // Redirect to base url
                redirect ( base_url () );
            }
        }        
    }

	public function download_attachment($utaskID=null){
		$CI =& get_instance();	
		$CI->load->library('zip');
		$CI->load->model('Tasks');
		
	
		$res=$CI->Tasks->get_proposal_attachments($utaskID);
		 
		
	  	$attachment_files=$res[0]->attachments;
		
		$attachments= explode(",",$attachment_files);
		
 
		foreach($attachments as $att){
			 
			$file = realpath (FCPATH.'uploads/proposal') . "/" . $att;
			$nname = explode('_', $file);
			$nname = array_reverse($nname);			
			$CI->zip->read_file($file, FALSE, $nname[0]);
		}
		       
		 //$filename = time()."-"."proposal.zip";
		 $filename = "Proposal_Document.zip";
		 $CI->zip->download($filename);
	}


    public function download_attachment_proposal($filename = null) {
        if ($filename) {
            $file = realpath (FCPATH.'uploads/proposal') . "/" .  trim(urldecode($filename));

            // check file exists
            if (file_exists ( $file )) {
                $arrFileName = explode('_', $file);
                // get file content
                $data = file_get_contents ( $file );
                //force download
                force_download ( end($arrFileName), $data );
            } else {
                // Redirect to base url
                redirect ( base_url () );
            }
        }
    }

	public function send_offer(){
		$CI =& get_instance();	
		$CI->load->model('Tasks');
		$user_id_to = $_POST['userId'];
		$user_task_id = $_POST['taskUserId'];


		if(!empty($user_id_to)) {

			$offerDetails[] = array('task_id' => $user_task_id, 'freelancer_id' => $user_id_to, 'offer_send_by' => $CI->session->userdata('user_id'));
			$CI->Tasks->save_offer($offerDetails,$CI->session->all_userdata());
			$CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Send Offer for mentioned job post</div>');
			
			//$response = array('status' => 1, 'message' => 'Send Offer for mentioned job post');
			//return json_encode($response);
		}else{
			$CI->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Unable to send offers for mentioned job post</div>');
			
			$response = array('status' => 0, 'message' => 'Unable to send offers for mentioned job post');
			json_encode($response);
		}
	}

	public function accept_offer($task_id = ''){

		$CI =& get_instance();	
		$CI->load->model('Tasks');

		$return = $CI->Tasks->accept_offer($task_id,$CI->session->userdata('user_id'),'A');
		if($return){
			$CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Offer Accepted</div>');
			$response = array('status' => 1, 'message' => 'Offer Accepted');
			return json_encode($response);
		}else{
			$CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Something Error. Please try again.</div>');
			$response = array('status' => 1, 'message' => 'Offer Rejected');
			return json_encode($response);
		}
	}

	public function reject_offer($task_id = ''){
		$CI =& get_instance();	
		$CI->load->model('Tasks');

		$return = $CI->Tasks->accept_offer($task_id,$CI->session->userdata('user_id'),'R');
		if($return){
			$CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Offer Rejected</div>');
			$response = array('status' => 1, 'message' => 'Offer Accepted');
			return json_encode($response);
		}else{
			$CI->session->set_flashdata('msg', '<div class="alert alert-success text-center">Something Error. Please try again.</div>');
			$response = array('status' => 1, 'message' => 'Offer Rejected');
			return json_encode($response);
		}
	}

}

