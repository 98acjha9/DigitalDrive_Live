<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tasks extends CI_Model {

	public function __construct(){
		parent::__construct();
        $this->load->model('Users');
	}

	/*

 	 * Task Addition

 	 */


	public function count_user_all_microkey_tasks($userID = null) {

        $task_count = 0;



        if(empty($userID))

            return $task_count;



       //$this->db->where('microkey_client.task_status', 1);

        //$this->db->where('microkey_client.task_is_complete', 0); 

        //$this->db->where('microkey_client.task_is_ongoing', 0);                

        //$this->db->where('(microkey_client.task_is_deleted IS NULL OR microkey_client.task_is_deleted=0)');

        $this->db->where('microkey_client.user_id', $userID);        

        //$this->db->where('microkey_client.task_due_date > NOW()'); 

        $this->db->from('microkey_client');

        $task_count = $this->db->count_all_results();

        //echo $this->db->last_query();



        return $task_count;     

    }

    public function getFreelancerByMicrokey($taskId = ''){
		$sql = "select COUNT(*) as total,m.user_id,u.*,ul.*
				FROM microkey as m
				inner join users u on u.user_id = m.user_id
				inner join user_login ul on ul.user_id = m.user_id
				WHERE ul.total_coins !='0'
				GROUP BY m.user_id
				HAVING COUNT(m.user_id) >= 1 order by total desc";
		$getClientInfo = $this->db->query($sql);

		return $getClientInfo->result_object();
	}
 public function project_detailsByType($type,$user_id, $limit = 0)
    { 
        $this->db->join('task_hired', 'task_hired.task_id = task.task_id');
        $this->db->join('users', 'users.user_id = task.task_created_by');
        $this->db->where('freelancer_id', $user_id); 
        $this->db->where('task.task_status', 1);
        if($this->input->post('from_date')) {
        	$from_date = date('Y-m-d', strtotime($this->input->post('from_date')));        	
        	$this->db->where('task.task_doc >=', $from_date);
        }
        if($this->input->post('to_date')) {
        	$to_date = date('Y-m-d', strtotime($this->input->post('to_date')));        	
        	$this->db->where('task.task_doc <=', $to_date);
        }
        if($limit > 0)
			$this->db->limit($limit);
		
		if($type=='active-invitation'){
			$this->db->where('month(task.task_doc)',date('m')); 
		}
		
		if($type=='previous-invitation'){
			$this->db->where('month(task.task_doc) <',date('m'));
		}

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        $result = $this->db->get('task');
 
        if($result->num_rows() > 0){
            return $result->result();
        }
        return [];
    }

    public function get_microkey_client_info_by_id($id = null){

        if(empty($id))

            return FALSE;



        $this->db->select('*');

        $this->db->from('microkey_client');

        $this->db->where('id', $id);

        $query = $this->db->get();

        return $query->row();

    }

    public function list_user_all_microkey_tasks($userID = null, $limit = 10, $start = 1) {

        $task_list = array();



        if(empty($userID))

            return $task_list;



        $this->db->select('microkey_client.*');

        $this->db->from('microkey_client');

        //$this->db->where('microkey_client.task_status', 1);

        //$this->db->where('microkey_client.task_is_complete', 0); 

       // $this->db->where('microkey_client.task_is_ongoing', 0);         

        //$this->db->where('(microkey_client.task_is_deleted IS NULL OR microkey_client.task_is_deleted=0)');

        $this->db->where('microkey_client.user_id', $userID);        

       // $this->db->where('task.task_due_date >= CURDATE()'); 
		
		$this->db->order_by('microkey_client.id','DESC');

        $this->db->limit($limit, $start);       

        $query = $this->db->get();
		
		 

        foreach ($query->result() as $row){

            $task_list[] = $row;

        }



        return $task_list;        

    }

	public function add_new_task($userID = null, $taskData = array()){

        if(empty($taskData))
        	return array('status' => FALSE, 'message' => 'invalid_data');

        $id_generator = $this->auth->generator(10);
        $date_of_creation = date("Y-m-d H:i:s");
        //print_r($taskData);
        $data = array(
            'user_task_id' => $id_generator,         
            'task_name' => $taskData['task']['task_name'],
            'task_details' => $taskData['task']['task_details'],
            'task_origin_location' =>  $taskData['task']['task_origin_location'],
            'task_origin_country' =>  $taskData['task']['task_origin_country'],
            'task_total_budget' =>  $taskData['task']['task_total_budget'],
            'task_history' =>  json_encode(array('status' => 'open', 'by' => 'owner', 'at' => $date_of_creation)),
            'task_created_by' =>  $userID,
            'task_doc' =>  $date_of_creation,         
            'task_status' => $taskData['task']['task_status'],
			'task_keywords' => $taskData['task']['task_keywords'],
			'task_duration' => $taskData['task']['task_due_date'],
			'task_duration_type' => $taskData['task']['task_duration_type']
        );

       $result = $this->db->insert('task',$data);  

       $insert_id = $this->db->insert_id(); 



        if($result) {

            foreach($taskData['task_requirements'] as $val) {

                $date_of_creation = date("Y-m-d H:i:s");
                if(filter_var($val, FILTER_VALIDATE_INT) === false){

                    $count = $this->db->where(['name' => $val])->from('area_of_interest')->count_all_results();

                    if($count == 0){
                        $this->db->insert('area_of_interest', ['name' => $val, 'status' => 1]);
                        $area_of_interest_id = $this->db->insert_id();
                    }
                }else{

                	$area_of_interest_id = $val;
                }

                $data = array(
                    'task_id' => $insert_id,         
                    'area_of_interest_id' => $area_of_interest_id,
                    'doc' => $date_of_creation,
					'deleted' => 0
                );
                $result_sub = $this->db->insert('task_requirements',$data);  
            }

            foreach($taskData['task_attachments'] as $val) {
                $date_of_creation = date("Y-m-d H:i:s");
                $data = array(
                    'task_id' => $insert_id,         
                    'task_attach_filename' => $val,
                    'task_attachment_doc' => $date_of_creation
                );
                $result_sub = $this->db->insert('task_attachments',$data);  
            }

			
		    /*-------------PM code start-----------------------*/
			
			$notification_master_data = $this->db->select('*')->from('notification_type')->where('NOTIFICATION_TYPE_ID',28)->get()->row();
			if(!empty($notification_master_data)){
				$message = $notification_master_data->MESSAGE;
			}else{
				$message = '';
			}
		    $this->db->select('user_id');
            $this->db->from('user_login');            
			$this->db->where('user_type', 4);
            $freelancer = $this->db->get()->result();
            foreach($freelancer as $f)
			{  
                $this->db->where('user_id', $f->user_id);				
			    $this->db->where_in('area_of_interest_id',$taskData['task_requirements']);
				$query=$this->db->get('user_area_of_interest');
				$num=$query->num_rows();
                if($num>0)
				{
					 $this->db->where('task_id', $insert_id);
					 $this->db->where('notification_from', $this->session->userdata('user_id'));
                     $query=$this->db->get('task_notification');
				     $notice_num=$query->num_rows();
                     if($notice_num>0)
				     {	
					 }else{	
                           $job_details_link = '<a href="'.base_url().'job-details/'.$id_generator.'">'.$taskData['task']['task_name'].'</a>';					 
							$data = array(
							'offer_id' => 0,
							'task_id' => $insert_id,
							'notification_from' => $this->session->userdata('user_id'),
							'notification_to' => $f->user_id,
							'notification_details' => 'NEW TASK',
							'notification_master_id' => 28,
							'notification_message' => '<strong>'.'<a href='.base_url().'public-profile/'.$this->session->userdata('profile_id').'>'.$this->session->userdata('user_name').'</a></strong> '.$message.' <strong>'.$job_details_link.'</strong>',
							'notification_doc' => $date_of_creation
						);

						$result_sub = $this->db->insert('task_notification',$data);
					 }
				}
			}
			/*-------------------PM code end-----------------*/
        }

        return array('status' => FALSE, 'message' => 'unable_to_add_record_in_db');

	}

	

	public function edit_new_task($usertaskID = null,$taskData = array()){

		if(empty($taskData))

        	return array('status' => FALSE, 'message' => 'invalid_data');



        $info = $this->get_task_info_by_user_task_id($usertaskID);

		$taskID = $info->task_id; 

		

        $date_of_creation = date("Y-m-d H:i:s");

        //print_r($taskData);

        $data = array(

            //'user_task_id' => $id_generator,         

            'task_name' => $taskData['task']['task_name'],

            'task_details' => $taskData['task']['task_details'],

            'task_due_date' =>  $taskData['task']['task_due_date'],
            'task_keywords' => $taskData['task']['task_keywords'],
            'task_origin_location' =>  $taskData['task']['task_origin_location'],

            'task_origin_country' =>  $taskData['task']['task_origin_country'],

            'task_total_budget' =>  $taskData['task']['task_total_budget'],

            'task_history' =>  json_encode(array('status' => 'open', 'by' => 'owner', 'at' => $date_of_creation)),

            //'task_created_by' =>  $userID,

            //'task_doc' =>  $date_of_creation,         

            'task_status' => $taskData['task']['task_status']

        );

        $this->db->where('task_id',$taskID);

		$result = $this->db->update('task',$data);  

        

        if($result) {

			if(!empty($taskData['task_requirements'])){

				$result_sub = $this->db->update('task_requirements',array('deleted'=>1), array('task_id' => $taskID)); 

				foreach($taskData['task_requirements'] as $val) {

					$date_of_creation = date("Y-m-d H:i:s");

					$data = array(

						'area_of_interest_id' => $val,

						'doc' => $date_of_creation,

						'deleted' => 0

					);

					

					// check already inserted

					$this->db->select('*');

					$this->db->from('task_requirements');

					$this->db->where('task_id', $taskID);

					$this->db->where('area_of_interest_id', $val);

					$query = $this->db->get();

					if($query->num_rows() > 0){

						$result_sub = $this->db->update('task_requirements',$data, array('task_id' => $taskID, 'area_of_interest_id' => $val)); 

					}else{

						$data['task_id'] =  $taskID;

						$result_sub = $this->db->insert('task_requirements',$data); 

					} 

				}

			}

			

			if(!empty($taskData['task_attachments'])){

				//$result_sub = $this->db->update('task_attachments',array('is_deleted'=>1), array('task_id' => $taskID)); 

				foreach($taskData['task_attachments'] as $val) {

					$date_of_creation = date("Y-m-d H:i:s");

					$data = array(

						'task_attach_filename' => $val,

						'task_attachment_doc' => $date_of_creation

					);

					// check already inserted

					//$this->db->select('*');

					//$this->db->from('task_attachments');

					//$this->db->where('task_id', $taskID);

					//$this->db->where('task_attach_filename', $val);

					//$query = $this->db->get();

					//if($query->num_rows() > 0){

						//$result_sub = $this->db->update('task_attachments',$data, array('task_id' => $taskID)); 

					//}else{

						$data['task_id'] =  $taskID;

						$result_sub = $this->db->insert('task_attachments',$data); 

					//}

				}

			}

            

        }

		

        return array('status' => FALSE, 'message' => 'unable_to_edit_record_in_db');

	}



    public function save_offer($offerDetails = array(), $userData = null){
		
		$return = FALSE;
        if(empty($offerDetails)){
			$response = array('status' => FALSE, 'message' => 'Unable to send offers for mentioned job post');
			json_encode($response);
			return array('status' => FALSE, 'message' => 'Unable to send offers for mentioned job post');
		}else{
			//echo '<pre>'; print_r($offerDetails); die;
			$date_of_creation = date("Y-m-d H:i:s");
			foreach($offerDetails as $offer) {
				
				$userInfo = $this->Users->get_user_profile_info_by_id($offer['offer_send_by']);
				$task_details = $this->get_task_info_by_user_task_id($offer['task_id']);
				
				//echo $task_details->task_id;
				//echo '<br>';
				//echo $offer['freelancer_id'];
				
				// Check already send msg_get_queue
				 $check = $this->db->query("select * from offer_task where task_id = ".$task_details->task_id." and receiver_id = '".$offer['freelancer_id']."'");
				 $row=$check->row();
 				
				if($check->num_rows() > 0){
					
					 $is_hired=$row->is_hired;
					 $is_deleted=$row->offer_is_deleted;
					 
					 if($is_hired==0 && $is_deleted==0 ){
						 return array('status' => FALSE,'is_cancelled'=>'Y', 'message' => 'Offer has been cancelled/rejected');
					 }else{
							$response = array('status' => 1,'is_cancelled'=>'N', 'message' => 'Offer sent already for mentioned job post');
							json_encode($response);
							return array('status' => FALSE, 'message' => 'offer_send_already');
					 }
					
					
				}else{
					
					$data = array(
						'task_id' => $task_details->task_id,         
						'receiver_id' => $offer['freelancer_id'],
						'offer_details' => "Hello!\n\nI'd like to invite you to take a look at the job I've posted. Please submit a proposal if you're available and interested.\n\n".$userInfo['basic_info']->name,
						'offer_send_by' => $offer['offer_send_by'],
						'offer_doc' =>  $date_of_creation
					);
					$result = $this->db->insert('offer_task',$data);    
					$insert_id = $this->db->insert_id(); 

					if($result) {
						$date_of_creation = date("Y-m-d H:i:s");
						
						$job_details_link = '<a href="'.base_url().'hired-job-details/'.$task_details->user_task_id.'">'.$task_details->task_name.'</a>';
						
						$data = array(
							'offer_id' => $insert_id,
							'task_id' => $task_details->task_id,
							'notification_from' => $offer['offer_send_by'],
							'notification_to' => $offer['freelancer_id'],
							'notification_details' => 'MADE AN OFFER',
							'notification_master_id' => 9,
							'notification_message' => '<strong> ' . '<a href='.base_url().'public-profile/'.$userData['profile_id'].'>'. $userData['user_name'].'</a></strong> ' . ' </strong> Made an offer for <strong>'.$job_details_link.'</strong>',
							'notification_doc' => $date_of_creation
						);

						$result_sub = $this->db->insert('task_notification',$data);

						$insert = array(
							'req_send_user_id' => $offer['offer_send_by'],
							'req_date_time' => $date_of_creation,
							'interested_user_id' => $offer['freelancer_id'],
							'request_send' => 'Y',
							'task_id' => $task_details->task_id,
							'notification_type_id' => 9,
							'accept_status' => 'N',
							'delete_status' => 0
						);
						$return = $this->db->insert('task_interested',$insert);
						$return = TRUE;
					}
				}
			}
			if($return){
				$response = array('status' => 1,'is_cancelled'=>'N', 'message' => 'Send Offer for mentioned job post');
				json_encode($response);
				return array('status' => TRUE, 'message' => 'successfully_add_record_in_db');
			}
		}
    } 


    public function get_task_info_by_user_task_id($user_task_id = null){

        if(empty($user_task_id))

            return FALSE;



        $this->db->select('*');

        $this->db->from('task');

        $this->db->where('user_task_id', $user_task_id);

        $query = $this->db->get();

        return $query->row();

    } 



    public function get_freelancer_hired_for_task($userID = null, $task_id = null){

        if(empty($task_id) || empty($userID))

            return FALSE;

        $this->db->select('*');

        //$this->db->from('offer_task');
		
		$this->db->from('task_hired');

        $this->db->where('task_id', $task_id);

        $this->db->where('hired_status', 1);        

        $query = $this->db->get();

        $result = $query->row();



        if($result) {

            if($result->freelancer_id == $userID) {

                return $result->offer_send_by;

            }

            else {

                return $result->freelancer_id;

            }

        }

        else

            return FALSE;

    }     



    public function get_task_offer_details($task_offer_id = null){

        if(empty($task_offer_id))

            return FALSE;



        $this->db->select('*');

        $this->db->from('offer_task');

        $this->db->where('offer_id', $task_offer_id);

        $query = $this->db->get();

        return $query->row();

    }    



    public function task_offers_list_by_offer_id($offerID = null, $userID = null, $limit = 5) {

        $task_offers = array();



        if(empty($offerID))

            return $task_offers;  



        $this->db->select('offer_task.offer_send_by, offer_task.offer_id, task.user_task_id, offer_task.task_id, offer_task.offer_details');

        $this->db->from('offer_task');

        $this->db->join('task', 'task.task_id = offer_task.task_id');        

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0);         

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('offer_task.offer_id', $offerID);

        $this->db->where('offer_task.offer_send_by <> \'' . $userID . '\'');                

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->limit($limit, 0);       

        $query = $this->db->get();

        //echo $this->db->last_query();

        foreach ($query->result() as $row){

            $task_offers[] = $row;

        }



        return $task_offers;

    }            


//modified on 19-10-2020
    public function count_all_upcoming_tasks($searchCriteria) {

        $task_count = 0;
	
		$this->db->select('task.*');
        $this->db->from('task');
        $this->db->where('task.task_status', 1);
        $this->db->where('task.task_is_complete', 0); 
        $this->db->where('task.task_is_ongoing', 0);         
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)'); 
		
		if(!empty($searchCriteria)){
            $this->db->where('(task.task_name LIKE \'%'.$searchCriteria.'%\' OR task.task_details LIKE \'%'.$searchCriteria.'%\')');
		} 
		
        $task_count = $this->db->count_all_results();

        //echo $this->db->last_query();



        return $task_count;        

    
	}


    public function get_user_total_spend($userID = null) {
        $total_spend = 0;
        if(empty($userID))
            return $total_spend;
        $this->db->select_sum('task_total_budget');
        $this->db->where('task.task_is_complete', 1);
        $this->db->where('task.task_status', 1); 
		$this->db->where('user_task_id',$userID);
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');               

        $query = $this->db->get('task');

        //echo $this->db->last_query();

        $total_spend = $query->row();



        return (float)$total_spend->task_total_budget;        

    }



    public function get_all_upcoming_tasks($limit = 10, $start = 1,$searchCriteria="") {

        $task_list = array();
//        $this->db->select('task.*');
//        $this->db->from('task');
//        $this->db->join('offer_task', 'offer_task.task_id = task.task_id', 'left');
//        $this->db->where('task.task_status', 1);
//        $this->db->where('task.task_is_complete', 0); 
//        $this->db->where('task.task_is_ongoing', 0);
//        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
//        $this->db->where('task.task_due_date > NOW()'); 
//        //$this->db->where('task.task_id IN (SELECT DISTINCT offer_task.task_id FROM offer_task where offer_task.is_hired=0)');  
//        $this->db->where('(offer_task.is_hired=0 OR offer_task.is_hired IS NULL)');
//        $this->db->limit($limit, $start);  	
//        $this->db->order_by('task_doc','DESC');   
//        $query = $this->db->get();
        //echo $this->db->last_query();
        $this->db->select('task.*');
        $this->db->from('task');
        $this->db->where('task.task_status', 1);
        $this->db->where('task.task_is_complete', 0); 
        $this->db->where('task.task_is_ongoing', 0);         
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
		 if(!empty($searchCriteria)) {
            $this->db->where("(task.task_name like '%$searchCriteria%' OR task.task_details like '%$searchCriteria%' OR task_id IN (SELECT DISTINCT task_requirements.task_id FROM task_requirements JOIN area_of_interest ON area_of_interest.area_of_interest_id=task_requirements.area_of_interest_id WHERE area_of_interest.name like '%$searchCriteria%'))");
        }  
        //$this->db->where('task.task_created_by', $userID);        
       // $this->db->where('task.task_due_date >= CURDATE()'); 
		$this->db->order_by('task.task_doc','DESC');
        $this->db->limit($limit, $start);       
        $query = $this->db->get();
        foreach ($query->result() as $row){
            $task_list[] = $row;
        }
		//print_r($task_list);exit;
        return $task_list;  
    }
	public function get_all_micro_client_task_count($searchCriteria=""){
		$this->db->from('microkey_client');
		$this->db->join('user_login', 'user_login.user_id = microkey_client.user_id', 'left');
		$this->db->join('users', 'users.user_id=user_login.user_id', 'left');
		$this->db->join('continent', 'continent.continent_id=microkey_client.continent', 'left');
		$this->db->join('country', 'country.country_id=microkey_client.country_id', 'left');
		if(!empty($searchCriteria)){
            $this->db->where('(microkey_client.title LIKE \'%'.$searchCriteria.'%\' OR microkey_client.description LIKE \'%'.$searchCriteria.'%\') OR microkey_client.skills IN (SELECT DISTINCT `area_of_interest_id` FROM `area_of_interest` WHERE `name` LIKE \'%'.$searchCriteria.'%\')');
		} 
		$task_count = $this->db->count_all_results();
		return $task_count;
	}
    public function get_all_micro_client_task_lists($offset=10,$limit=0,$searchCriteria=""){
		$task_list = array();
		$this->db->select('microkey_client.title,microkey_client.id as m_task_id,microkey_client.budget,microkey_client.image AS microkey_post_image,
		user_login.profile_image,user_login.last_login,continent.name continent,
		country.name country,users.name username,user_login.total_coins,"M" AS task_identity,microkey_client.user_id');
		$this->db->from('microkey_client');
		$this->db->join('user_login', 'user_login.user_id = microkey_client.user_id', 'left');
		$this->db->join('users', 'users.user_id=user_login.user_id', 'left');
		$this->db->join('continent', 'continent.continent_id=microkey_client.continent', 'left');
		$this->db->join('country', 'country.country_id=microkey_client.country_id', 'left');
		if(!empty($searchCriteria)){
            $this->db->where('(microkey_client.title LIKE \'%'.$searchCriteria.'%\' OR microkey_client.description LIKE \'%'.$searchCriteria.'%\') OR microkey_client.skills IN (SELECT DISTINCT `area_of_interest_id` FROM `area_of_interest` WHERE `name` LIKE \'%'.$searchCriteria.'%\')');
		} 
		$this->db->limit( $limit,$offset);   
		$query = $this->db->get();

		foreach ($query->result() as $row){
			$task_list[] = $row;
		}
		return $task_list;  
	}
 public function client_analytics_by_id($user_id){
    	$month = array();

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 1 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();
    	
    	//Earnings Overview
    	if(count($result)>0) {
    		$month['January']=$result[0]->earned;
    	} else{
    		$month['January']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 2 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();
    	
    	//Earnings Overview
    	if(count($result)>0) {
    		$month['February']=$result[0]->earned;
    	} else{
    		$month['February']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 3 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['March']=$result[0]->earned;
    	} else{
    		$month['March']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 4 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['April']=$result[0]->earned;
    	} else{
    		$month['April']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 5 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['May']=$result[0]->earned;
    	} else{
    		$month['May']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 6 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['June']=$result[0]->earned;
    	} else{
    		$month['June']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 7 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['July']=$result[0]->earned;
    	} else{
    		$month['July']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 8 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['August']=$result[0]->earned;
    	} else{
    		$month['August']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 9 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['September']=$result[0]->earned;
    	} else{
    		$month['September']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 10 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['October']=$result[0]->earned;
    	} else{
    		$month['October']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 11 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['November']=$result[0]->earned;
    	} else{
    		$month['November']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$user_id."' AND  month(hired_doc) = 12 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['December']=$result[0]->earned;
    	} else{
    		$month['December']=0;
    	}

    	$monthly_projects_arr = $this->db->query("SELECT * FROM task WHERE task.task_created_by='".$user_id."' AND MONTH(task.task_doc) = MONTH(CURRENT_DATE())");
    	$monthly_projects = $monthly_projects_arr->num_rows();

    	$yearly_projects_arr = $this->db->query("SELECT * FROM task WHERE task.task_created_by='".$user_id."' AND YEAR(task.task_doc) = YEAR(CURRENT_DATE())");
    	$yearly_projects = $yearly_projects_arr->num_rows();    	

    	$pending_projects_arr = $this->db->query("SELECT * FROM task WHERE task.task_created_by='".$user_id."' AND task.task_status = 1 AND task.task_is_complete = 0 AND task.task_is_ongoing = 0 AND (task.task_is_deleted IS NULL OR task.task_is_deleted=0)");
    	$pending_projects = $pending_projects_arr->num_rows();

    	$offer_q = $this->db->query("SELECT task_id FROM task_notification WHERE notification_to='".$user_id."' AND notification_master_id=12");
    	$offer_result = $offer_q->result_array();

    	$hired_q = $this->db->query("SELECT task_id FROM task_notification WHERE notification_to='".$user_id."' AND notification_master_id=14");
    	$hired_result = $hired_q->result_array();

    	$total_microkey_projects_arr = $this->db->query("SELECT * FROM microkey WHERE user_id='".$user_id."'");
    	$total_microkey_projects = $total_microkey_projects_arr->num_rows();

    	$offerArray = array_map('current', $offer_result);
    	$hireArray = array_map('current', $hired_result);
    	$refferal = array_diff($hireArray , $offerArray);

		  //   	print_r($offerArray);
		  //   	echo "<br>";
		  //   	print_r($hireArray);
				// echo "<br>";
		  //   	print_r($refferal);
		  //   	exit;

    	$data['total_offer'] = count($offerArray);
    	$data['total_referral'] = count($refferal);
    	$data['total_microkey_projects'] = $total_microkey_projects;

        $this->db->select('agreed_budget, name, task_name, freelancer_id, task_created_by, task_hired.hire_date, task_hired.hired_end_date');
        $this->db->join('task_hired', 'task_hired.task_id = task.task_id');
        $this->db->join('users', 'users.user_id = task_hired.freelancer_id');
        $this->db->where('task_created_by', $user_id);
        $this->db->where('task.task_is_complete', 1);
        $this->db->where('task.task_status', 1);
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        if($this->input->post('from_date')) {
        	$from_date = date('Y-m-d', strtotime($this->input->post('from_date')));        	
        	$this->db->where('task.task_doc >=', $from_date);
        }
        if($this->input->post('to_date')) {
        	$to_date = date('Y-m-d', strtotime($this->input->post('to_date')));        	
        	$this->db->where('task.task_doc <=', $to_date);
        }
        $this->db->group_by('task.task_id');
        $result = $this->db->get('task');
        if($result->num_rows() > 0){
            $result = $result->result();
        } else {
        	$result = [];
        }

        $current_month = date('F');

        $yearly_income = array_sum($month);
        $monthly_income = round($month[$current_month]);
        
        $return_data = array(
        	'analytics' => $result,
        	'month' => $month,
        	'monthly_projects' => $monthly_projects,
        	'yearly_projects' => $yearly_projects,
        	'yearly_income' => $yearly_income,
        	'monthly_income' => $monthly_income,
        	'pending_projects' => $pending_projects,
        	'total_offer' => count($offerArray),
        	'total_referral' => count($refferal),
        	'total_microkey_projects' => $total_microkey_projects
        );

        return $return_data;
    }

    public function ajax_get_all_upcoming_tasks($searchCriteria = null) {

        $task_list = array();



        $this->db->select('task.*');

        $this->db->from('task');

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0);         

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->where('task.task_id NOT IN (SELECT DISTINCT offer_task.task_id FROM offer_task where offer_task.is_hired=0)');  

        if(!empty($searchCriteria)) {

            $this->db->where("(task.task_name like '%$searchCriteria%' OR task.task_details like '%$searchCriteria%' OR task_id IN (SELECT DISTINCT task_requirements.task_id FROM task_requirements JOIN area_of_interest ON area_of_interest.area_of_interest_id=task_requirements.area_of_interest_id WHERE area_of_interest.name like '%$searchCriteria%'))");

        }      



        $query = $this->db->get();

        foreach ($query->result() as $row){

            $task_list[] = $row;

        }



        return $task_list;        

    }    



    public function get_task_skill_requirements($task_id = null) {

        $task_skill_list = array();



        if(empty($task_id))

            return $task_skill_list;



        $this->db->select('task_requirements.area_of_interest_id,area_of_interest.name');

        $this->db->from('task_requirements');

        $this->db->join('area_of_interest', 'area_of_interest.area_of_interest_id = task_requirements.area_of_interest_id');

        $this->db->where('task_id', $task_id);      

        $query = $this->db->get();

        foreach ($query->result() as $row){

            $task_skill_list[] = $row;

        }



        return $task_skill_list;        

    } 



    public function get_task_attachments($task_id = null) {

        $task_attachment_list = array();



        if(empty($task_id))

            return $task_attachment_list;



        $this->db->select('*');

        $this->db->from('task_attachments');

        $this->db->where('task_id', $task_id);  

        $this->db->where('(is_deleted IS NULL OR is_deleted=0)');            

        $query = $this->db->get();

        foreach ($query->result() as $row){

            $task_attachment_list[] = $row;

        }


        return $task_attachment_list;        

    } 
	
	
	public function get_proposal_attachments($pid = null) {

        $task_attachment_list = array();



        if(empty($pid))

            return $task_attachment_list;



        $this->db->select('*');

        $this->db->from('task_proposal');

        $this->db->where('proposal_id', $pid);  

        $this->db->where('(is_deleted IS NULL OR is_deleted=0)');            

        $query = $this->db->get();

        foreach ($query->result() as $row){

            $task_attachment_list[] = $row;

        }


        return $task_attachment_list;        

    } 



    public function count_all_task_offers($task_id = null) {

        $task_offers_count = 0;



        if(empty($task_id))

            return $task_offers_count;



        $this->db->where('offer_status', 1);              

        $this->db->where('(offer_is_deleted IS NULL OR offer_is_deleted=0)');

        $this->db->where('task_id', $task_id);        

        $this->db->from('offer_task');

        $task_offers_count = $this->db->count_all_results();

        //echo $this->db->last_query();



        return $task_offers_count;     

    }               



    public function get_task_info_by_user_id($task_id = null){

        $task_list = array();



        if(empty($task_id))

            return $task_list;



        $this->db->select('task.*');

        $this->db->from('task');

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0);         

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_created_by', $userID);        

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->limit($limit, $start);       

        $query = $this->db->get();

        foreach ($query->result() as $row){

            $task_list[] = $row;

        }



        return $task_list;                 

    }  



    public function task_details_by_user_task_id($user_task_id = null) {

        $task_details = array();

 

        if(empty($user_task_id))

            return $task_details;     



        $this->db->select('task.*');

        $this->db->from('task');  

        $this->db->where('task.user_task_id',$user_task_id);            

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

                //$arrFileName = explode('_', $row_task_attach->task_attach_filename);
				//$arrFileName = $row_task_attach->task_attach_filename;
				
				$arrFileName = explode('_', $row_task_attach->task_attach_filename);
				
                $task_attachments[] = array('file_name' => $row_task_attach->task_attach_filename, 'file_display_name' => end($arrFileName) , 'task_attachment_id' => $row_task_attach->task_attachment_id);

            } 

			 
			$c=count($query_task_attach->result());
			$t=4-$c;
			$new_attachment="";
			for($i=0;$i<$t;$i++){
			 	$new_attachment.='<div class="col-sm-3"><input type="file" name="fldTaskDocuments[]"  class="dropify" multiple /></div>';
			}
			

 
            $this->db->select('task_requirements.area_of_interest_id,area_of_interest.name,task_requirements.task_id');

            $this->db->from('task_requirements');

            $this->db->join('area_of_interest', 'area_of_interest.area_of_interest_id = task_requirements.area_of_interest_id');

            $this->db->where('task_requirements.task_id', $row->task_id);

			$this->db->where('task_requirements.deleted',0);

            $query_task_requirements = $this->db->get();

            //echo $this->db->last_query();

            foreach ($query_task_requirements->result() as $row_task_requirement){

                $task_requirements[] = array('skill_id' => $row_task_requirement->area_of_interest_id, 'skill_name' => $row_task_requirement->name, 'task_id' => $row_task_requirement->task_id);

            }

            $basic_info = array('task_name'=> $row->task_name, 'task_id' => $row->task_id, 'user_task_id' => $row->user_task_id, 'task_keywords' => $row->task_keywords,'task_details' => $row->task_details, 'task_due_date' => date('m-d-Y', strtotime($row->task_due_date)), 'task_origin_location' => $row->task_origin_location, 'task_origin_country' => $row->task_origin_country, 'task_total_budget' => $row->task_total_budget,'task_hired'=>$row->task_hired,'task_is_ongoing'=>$row->task_is_ongoing,'task_status'=>$row->task_status, "task_duration" => $row->task_duration, "task_duration_type" => $row->task_duration_type);



            $task_details[] = array('basic_info' => $basic_info, 'task_attachments' => $task_attachments,'new_attachment'=>$new_attachment, 'task_requirements' => $task_requirements);

        }

        

        return $task_details;                

    }

	public function update_task_budget($data = array()){
		//print_r($data);  exit;
		$task_id = $data['task_id'];
		$freelancer_id = $data['freelancer_id'];
		$estimated_budget = $data['estimated_budget'];
		
		$return = $this->db->where('task_id', $task_id)->update('task', array('task_total_budget' => $estimated_budget));
		return $return;
	}

    public function task_status_info_by_task_id($task_id = null) {
        $task_details = array();
        if(empty($task_id))
            return $task_details;     

        $this->db->select('task.*');
        $this->db->from('task');  
        $this->db->where('task.task_id',$task_id);            
        $query = $this->db->get();
        //echo $this->db->last_query();
        foreach ($query->result() as $row){
            $task_attachments = $task_requirements = $task_hired_freelancer = array();

            $this->db->select('task_attachments.*');
            $this->db->from('task_attachments');
            $this->db->where('task_attachments.task_id', $row->task_id);
			$this->db->where('task_attachments.is_deleted',0);
            $query_task_attach = $this->db->get();
            //echo $this->db->last_query();
            foreach ($query_task_attach->result() as $row_task_attach){
                $arrFileName = explode('_', $row_task_attach->task_attach_filename);
                $file_typ_img = get_file_ext($row_task_attach->task_attach_filename);
                $task_attachments[] = array('file_ext_type' => $file_typ_img,  'file_name' => $row_task_attach->task_attach_filename, 'file_display_name' => end($arrFileName));
            } 

            $this->db->select('task_requirements.area_of_interest_id,area_of_interest.name,task_requirements.task_id');
            $this->db->from('task_requirements');
            $this->db->join('area_of_interest', 'area_of_interest.area_of_interest_id = task_requirements.area_of_interest_id');
            $this->db->where('task_requirements.task_id', $row->task_id);
			$this->db->where('task_requirements.deleted',0);
            $query_task_requirements = $this->db->get();
            //echo $this->db->last_query();
            foreach ($query_task_requirements->result() as $row_task_requirement){
                $task_requirements[] = array('skill_id' => $row_task_requirement->area_of_interest_id, 'skill_name' => $row_task_requirement->name, 'task_id' => $row_task_requirement->task_id);
            }

            //$this->db->select('offer_task.*');
			$this->db->select('task_hired.*');
            $this->db->from('task_hired');
            $this->db->where('task_hired.task_id', $row->task_id);
            $this->db->where('task_hired.hired_status', 1);
            $query_task_hired_freelancer = $this->db->get();

            //echo $this->db->last_query();

            foreach ($query_task_hired_freelancer->result() as $row_freelancer_hired){
                $task_hired_freelancer[] = array('hired_id' => $row_freelancer_hired->hired_id, 'task_id' => $row_freelancer_hired->task_id, 'receiver_id' => $row_freelancer_hired->freelancer_id, 'hired_status' => $row_freelancer_hired->hired_status, 'hire_date' => ($row_freelancer_hired->hire_date != NULL) ? date('d/m/Y',strtotime($row_freelancer_hired->hire_date)) : '',  'offer_send_by' => $row_freelancer_hired->offer_send_by, 'hired_doc' => $row_freelancer_hired->hired_doc);
            } //'offer_status' => $row_freelancer_hired->offer_status,           

            $basic_info = array('user_task_id' => $row->user_task_id, 'task_name' => $row->task_name, 'task_keywords' => $row->task_keywords, 'task_details' => $row->task_details, 'task_due_date' => $row->task_due_date, 'task_origin_location' => $row->task_origin_location, 'task_origin_country' => $row->task_origin_country, 'task_total_budget' => $row->task_total_budget, 'task_doc' => $row->task_doc,'task_duration' => $row->task_duration ,'task_duration_type' => $row->task_duration_type,'task_completed_by_owner' => $row->task_completed_by_owner );

            $task_details[] = array('basic_info' => $basic_info, 'task_attachments' => $task_attachments, 'task_requirements' => $task_requirements, 'task_hired' => $task_hired_freelancer);
        }
		return $task_details;                
    }  



    public function offered_task_list_by_user($userID = null, $limit = 5) {

        $task_offered = array();
        if(empty($userID))
            return $task_offered;  

        $this->db->select('task.*, offer_task.receiver_id');
        $this->db->from('offer_task');
        $this->db->join('task', 'task.task_id = offer_task.task_id');        
        $this->db->where('task.task_status', 1);
        $this->db->where('task.task_is_complete', 0); 
        $this->db->where('task.task_is_ongoing', 0);         
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        $this->db->where('offer_task.offer_send_by', $userID);        
        $this->db->where('task.task_due_date > NOW()');
        $this->db->where('(offer_task.offer_status=1 AND offer_task.offer_is_deleted=0)');         
        $this->db->limit($limit, 0);       
        $query = $this->db->get();
        //echo $this->db->last_query();
        foreach ($query->result() as $row){
            $task_offered[] = $row;
        }
        return $task_offered;
    }
    


    public function task_offers_list_by_task_id($taskID = null, $userID = null, $limit = 5) {

        $task_offers = array();



        if(empty($taskID))

            return $task_offers;  



        $this->db->select('offer_task.offer_send_by, offer_task.offer_id, task.user_task_id, offer_task.task_id, offer_task.offer_details');

        $this->db->from('offer_task');

        $this->db->join('task', 'task.task_id = offer_task.task_id');        

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0);         

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('offer_task.task_id', $taskID);

        $this->db->where('offer_task.offer_send_by',$userID);                

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->limit($limit, 0);       

        $query = $this->db->get();

        //echo $this->db->last_query();

        foreach ($query->result() as $row){

            $task_offers[] = $row;

        }



        return $task_offers;

    }      



    public function offered_send_list_by_user($userID = null, $limit = 10,$taskID = 0) {

        $task_offered = array();



        if(empty($userID))

            return $task_offered;  



        $this->db->select('offer_task.*');

        $this->db->from('offer_task');

        $this->db->join('task', 'task.task_id = offer_task.task_id');        

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0);         

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('offer_task.offer_send_by', $userID);  
		
		$this->db->where('task.user_task_id', $taskID);

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->where('(offer_task.offer_status=1 AND offer_task.offer_is_deleted=0)');        

        $this->db->limit($limit, 0);       

        $query = $this->db->get();

        //echo $this->db->last_query();

        foreach ($query->result() as $row){

            $task_offered[] = $row;

        }



        return $task_offered;

    }      



    public function count_user_all_upcoming_tasks($userID = null) {

        $task_count = 0;



        if(empty($userID))

            return $task_count;



        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0);                

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_created_by', $userID);        

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->from('task');

        $task_count = $this->db->count_all_results();

        //echo $this->db->last_query();



        return $task_count;     

    }



    public function list_user_all_upcoming_tasks($userID = null, $limit = 10, $start = 1) {

        $task_list = array();



        if(empty($userID))

            return $task_list;



        $this->db->select('task.*');

        $this->db->from('task');

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0);         

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_created_by', $userID);        

       // $this->db->where('task.task_due_date >= CURDATE()'); 
		
		$this->db->order_by('task.task_doc','DESC');

        $this->db->limit($limit, $start);       

        $query = $this->db->get();
		
		 

        foreach ($query->result() as $row){

            $task_list[] = $row;

        }



        return $task_list;        

    }



    public function count_user_all_ongoing_tasks($userID = null) {

        $task_count = 0;



        if(empty($userID))

            return $task_count;



        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 1);                

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_created_by', $userID);        

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->from('task');

        $task_count = $this->db->count_all_results();

        //echo $this->db->last_query();



        return $task_count;     

    }



    public function list_user_all_ongoing_tasks($userID = null, $limit = 10, $start = 1) {

        $task_list = array();
		
		$current_datetime = date('Y-m-d H:i:s',NOW());


        if(empty($userID))

            return $task_list;



        $this->db->select('task.*');

        $this->db->from('task');

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 1); 
        $this->db->where('task_hired.hired_status', 1);        

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_created_by', $userID);        

      //  $this->db->where('task.task_due_date > ',$current_datetime); 

        $this->db->limit($limit, $start);    
		$this->db->join('task_hired', 'task_hired.task_id = task.task_id');   
        $this->db->order_by('task_doc','DESC');       

        $query = $this->db->get();

        foreach ($query->result() as $row){

            $task_list[] = $row;

        }



        return $task_list;        

    } 



    public function count_user_all_hired_tasks($userID = null) {
        $task_count = 0;
        if(empty($userID))
            return $task_count;

        $this->db->where('task.task_status', 1);
        $this->db->where('task.task_is_complete', 0); 
        $this->db->where('task.task_is_ongoing', 1); 
        $this->db->where('task_hired.hired_status', 1);                         
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        $this->db->where('task.task_created_by', $userID);        
        //$this->db->where('task.task_due_date > NOW()');
        $this->db->join('task_hired', 'task_hired.task_id = task.task_id');          
        $this->db->from('task');
        $task_count = $this->db->count_all_results();
        //echo $this->db->last_query();

        return $task_count;     

    }

    public function list_user_all_hired_tasks($userID = null, $limit = 10, $start = 1) {
        $task_list = array();
        if(empty($userID))
            return $task_list;

        $this->db->select('task.*,task_hired.hired_id, task_hired.hired_end_date');
        $this->db->from('task');
        //$this->db->join('offer_task', 'offer_task.task_id = task.task_id'); 
		$this->db->join('task_hired', 'task_hired.task_id = task.task_id'); 
        $this->db->where('task.task_status', 1);
        $this->db->where('task.task_is_complete', 0); 
        $this->db->where('task.task_is_ongoing', 1);  
        $this->db->where('task_hired.hired_status', 1);                 
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        $this->db->where('task.task_created_by', $userID);        
        //$this->db->where('task.task_due_date > NOW()'); 
        $this->db->limit($limit, $start);       
        $query = $this->db->get();
        foreach ($query->result() as $row){
            $task_list[] = $row;
        }
		
		//echo '<pre>'; print_r($task_list); die;
		
        return $task_list;        
    }       
    public function get_user_hired_task($user_task_id) {
        $task_list = array();
        $this->db->select('task_hired.hired_id');
        $this->db->from('task');
		$this->db->join('task_hired', 'task_hired.task_id = task.task_id'); 
        $this->db->where('task.user_task_id', $user_task_id);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $task_list[] = $row;
        }
        return $task_list;        
    }      


    public function list_user_all_unhired_tasks($userID = null) {
        $task_list = array();
        if(empty($userID))
            return $task_list;

        $this->db->select('task.*');
        $this->db->from('task');
        //$this->db->join('offer_task', 'offer_task.task_id = task.task_id', 'left');        
        $this->db->where('task.task_status', 1);
        $this->db->where('task.task_is_complete', 0); 
        $this->db->where('task.task_is_ongoing', 0); 
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted = 0)');
        $this->db->where('task.task_created_by', $userID);        
       // $this->db->where('task.task_due_date > NOW()'); 
        //$this->db->where('(offer_task.is_hired IS NULL OR offer_task.is_hired = 0)');    
        $query = $this->db->get();
        //echo $this->db->last_query();
        foreach ($query->result() as $row){
            $task_list[] = $row;
        }
        return $task_list;        

    }        



    public function update_task($objUserData = null, $userId = null){

        if(!empty($objUserData) && !empty($userId)) {

            $data = array();



            if(!empty($objUserData['fldEmail'])) {

            	$data['email'] = $objUserData['fldEmail'];



		        $this->db->where('user_id', $userId);

		        $result = $this->db->update('user_login', $data);            	

            }elseif(!empty($objUserData['mobile'])) {

            	$data['mobile'] = $objUserData['mobile'];



		        $this->db->where('user_id', $userId);

		        $result = $this->db->update('user_login', $data);            	

            }elseif(isset($objUserData['receive_transactional_notification'])) {

            	$data['receive_transactional_notification'] = (int)$objUserData['receive_transactional_notification'];



		        $this->db->where('user_id', $userId);

		        $result = $this->db->update('user_login', $data);            	

            }elseif(isset($objUserData['receive_task_update_notification'])) {

            	$data['receive_task_update_notification'] = (int)$objUserData['receive_task_update_notification'];



		        $this->db->where('user_id', $userId);

		        $result = $this->db->update('user_login', $data);            	

            }elseif(isset($objUserData['receive_task_reminder_notification'])) {

            	$data['receive_task_reminder_notification'] = (int)$objUserData['receive_task_reminder_notification'];



		        $this->db->where('user_id', $userId);

		        $result = $this->db->update('user_login', $data);            	

            }elseif(isset($objUserData['receive_helpful_notification'])) {

            	$data['receive_helpful_notification'] = (int)$objUserData['receive_helpful_notification'];



		        $this->db->where('user_id', $userId);

		        $result = $this->db->update('user_login', $data);            	

            }elseif(isset($objUserData['fldProfileImage'])) {

            	$data['profile_image'] = $objUserData['fldProfileImage'];



		        $this->db->where('user_id', $userId);

		        $result = $this->db->update('user_login', $data);            	

            }elseif(!empty($objUserData['fldLanguages']) && is_array($objUserData['fldLanguages'])) {

                foreach($objUserData['fldLanguages'] as $val) {

                	$data[] = array('user_id' => $userId, 'language_id' => $val, 'doc' => date('Y-m-d H:i:s'));

                }



                $result = $this->db->delete('user_languages', array('user_id' => $userId));

                if($result) {

                	$this->db->insert_batch('user_languages', $data);

                }

            }elseif(!empty($objUserData['fldSkills']) && is_array($objUserData['fldSkills'])) {

                foreach($objUserData['fldSkills'] as $val) {

                	$data[] = array('user_id' => $userId, 'area_of_interest_id' => $val, 'doc' => date('Y-m-d H:i:s'));

                }



                $result = $this->db->delete('user_area_of_interest', array('user_id' => $userId));

                if($result) {

                	$this->db->insert_batch('user_area_of_interest', $data);

                }

            }            

            else {

                if(!empty($objUserData['fldName'])) {

            	    $data['name'] = $objUserData['fldName'];

                }



                if(!empty($objUserData['fldCountry'])) {

            	    $data['country'] = $objUserData['fldCountry'];

                }   

                

                if(!empty($objUserData['fldBio'])) {

            	    $data['bio'] = $objUserData['fldBio'];

                } 



                if(!empty($objUserData['fldState'])) {

            	    $data['state'] = $objUserData['fldState'];

                } 



                if(!empty($objUserData['fldAddress'])) {

            	    $data['address'] = $objUserData['fldAddress'];

                } 



                if(!empty($objUserData['fldCity'])) {

            	    $data['city'] = $objUserData['fldCity'];

                }   

                

                if(!empty($objUserData['fldVAT'])) {

            	    $data['vat'] = $objUserData['fldVAT'];

                }   



                if(!empty($objUserData['fldUserGender'])) {

            	    $data['gender'] = $objUserData['fldUserGender'];

                }                                                                                                          



		        $this->db->where('user_id', $userId);

		        $result = $this->db->update('users', $data);

            }



		    if(!empty($result)) {

			    return TRUE;

		    }

		    else {

			    return FALSE;

		    }

        }

        else {

        	return FALSE;

        }

	} 



    public function count_task_completed_by_user($userID = null) {

        $task_completed_count = 0;



        if(empty($userID))

            return $task_completed_count;



        $this->db->where('task_status', 1);

        $this->db->where('task_is_complete', 1);                      

        $this->db->where('(task_is_deleted IS NULL OR task_is_deleted=0)');

        $this->db->where('task_id IN (SELECT task_id FROM offer_task WHERE is_hired=1 AND receiver_id=\''.$userID.'\')');        

        $this->db->from('task');

        $task_completed_count = $this->db->count_all_results();

        //echo $this->db->last_query();



        return $task_completed_count;     

    } 



    public function user_total_earning($userID = null) {

        $total_earning = 0;



        if(empty($userID))

            return $total_earning;



        $query = $this->db->query("SELECT SUM(task_total_budget) AS total_earned FROM task WHERE task_status=1 AND task_is_complete=1 AND (task_is_deleted IS NULL OR task_is_deleted=0) AND task_id IN (SELECT task_id FROM offer_task WHERE is_hired=1 AND receiver_id='".$userID."')");

        //echo $this->db->last_query();        

        $row = $query->row();



        return $row->total_earned;     

    }



    public function count_list_offer_send_by_post($userID = null) {

        $task_count = 0;



        if(empty($userID))

            return $task_count;



        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0); 

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_created_by', $userID);        

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->where('task.task_id IN (SELECT offer_task.task_id FROM offer_task WHERE offer_task.offer_send_by=\''.$userID.'\' AND offer_task.offer_status=1 AND (offer_task.offer_is_deleted=0 OR offer_task.offer_is_deleted IS NULL))');     

        $this->db->from('task');

        $task_count = $this->db->count_all_results();

        //echo $this->db->last_query();



        return $task_count;     

    }    



    public function list_offer_send_by_post($userID = null, $limit = 10, $start = 1) {

        $task_list = array();



        if(empty($userID))

            return $task_list;



        $this->db->select('task.*');

        $this->db->from('task');      

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0); 

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_created_by', $userID);        

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->where('task.task_id IN (SELECT offer_task.task_id FROM offer_task WHERE offer_task.offer_send_by=\''.$userID.'\' AND offer_task.offer_status=1 AND (offer_task.offer_is_deleted=0 OR offer_task.offer_is_deleted IS NULL))');         

        $this->db->limit($limit, $start);       

        $query = $this->db->get();

        //echo $this->db->last_query();die;

        foreach ($query->result() as $row){

            $offered_freelancer_list = array();



            $this->db->select('offer_task.*');

            $this->db->from('offer_task');

            $this->db->where('offer_task.task_id', $row->task_id);

            $this->db->where('offer_task.offer_send_by=\''.$userID.'\'');    

            $this->db->where('offer_task.offer_status', 1);

            $this->db->where('offer_task.offer_is_deleted', 0);                                

            $query_offer_send_to_freelancer = $this->db->get();

            foreach ($query_offer_send_to_freelancer->result() as $row_offer_send_to_freelancer){

                $offered_freelancer_list[] = array('freelancer_id' => $row_offer_send_to_freelancer->receiver_id);

            } 



            $this->db->from('offer_task');

            $this->db->where('offer_task.task_id', $row->task_id);

            $this->db->where('offer_task.offer_send_by=\''.$userID.'\'');    

            $this->db->where('offer_task.is_responded', 1);

            $this->db->where('offer_task.offer_status', 1);

            $this->db->where('offer_task.offer_is_deleted', 0);                                          

            $offer_send_response_count = $this->db->count_all_results();

 

            $this->db->from('offer_task');

            $this->db->where('offer_task.task_id', $row->task_id);

            $this->db->where('offer_task.offer_send_by=\''.$userID.'\'');    

            $this->db->where('offer_task.is_refused', 1);  

            $this->db->where('offer_task.offer_status', 1);

            $this->db->where('offer_task.offer_is_deleted', 0);                                        

            $offer_send_refuse_count = $this->db->count_all_results();



            $this->db->from('offer_task');

            $this->db->where('offer_task.task_id', $row->task_id);

            $this->db->where('offer_task.offer_send_by=\''.$userID.'\'');    

            $this->db->where('offer_task.is_refused', 0);  

            $this->db->where('offer_task.is_responded', 0);    

            $this->db->where('offer_task.offer_status', 1);

            $this->db->where('offer_task.offer_is_deleted', 0);                                                

            $offer_send_wait_count = $this->db->count_all_results();





            $basicInfo = array('task_id' => $row->task_id, 'user_task_id' => $row->user_task_id, 'task_name' => $row->task_name, 'task_details' => $row->task_details, 'task_due_date' => $row->task_due_date, 'task_origin_location' => $row->task_origin_location, 'task_origin_country' => $row->task_origin_country, 'task_total_budget' => $row->task_total_budget, 'task_status' => $row->task_status, 'task_history' => $row->task_history, 'task_is_complete' => $row->task_is_complete, 'task_is_ongoing' => $row->task_is_ongoing, 'task_is_deleted' => $row->task_is_deleted, 'task_created_by' => $row->task_created_by, 'task_doc' => $row->task_doc);



            $task_list[] = array('basic_info' => $basicInfo, 'freelancer_list' => $offered_freelancer_list, 'offer_send_response_count' => $offer_send_response_count, 'offer_send_refuse_count' => $offer_send_refuse_count, 'offer_send_wait_count' => $offer_send_wait_count);

        }



        return $task_list;         

    }



    public function ajax_list_offer_send_by_post($userID = null, $searchCriteria = null) {

        $task_list = array();



        if(empty($userID))

            return $task_list;



        $this->db->select('task.*');

        $this->db->from('task');      

        $this->db->where('task.task_status', 1);

        $this->db->where('task.task_is_complete', 0); 

        $this->db->where('task.task_is_ongoing', 0); 

        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');

        $this->db->where('task.task_created_by', $userID);        

        $this->db->where('task.task_due_date > NOW()'); 

        $this->db->where('task.task_id IN (SELECT offer_task.task_id FROM offer_task WHERE offer_task.offer_send_by=\''.$userID.'\' AND offer_task.offer_status=1 AND (offer_task.offer_is_deleted=0 OR offer_task.offer_is_deleted IS NULL))');   

        if(!empty($searchCriteria)) {

            $this->db->where('(task.task_name LIKE \'%'.$searchCriteria.'%\' OR task.task_details LIKE \'%'.$searchCriteria.'%\')');

        }            

        $query = $this->db->get();

        //echo $this->db->last_query();

        foreach ($query->result() as $row){

            $offered_freelancer_list = array();



            $this->db->select('offer_task.*');

            $this->db->from('offer_task');

            $this->db->where('offer_task.task_id', $row->task_id);

            $this->db->where('offer_task.offer_send_by=\''.$userID.'\'');   

            $this->db->where('offer_task.offer_status', 1);

            $this->db->where('offer_task.offer_is_deleted', 0);                      

            $query_offer_send_to_freelancer = $this->db->get();

            foreach ($query_offer_send_to_freelancer->result() as $row_offer_send_to_freelancer){

                $offered_freelancer_list[] = array('freelancer_id' => $row_offer_send_to_freelancer->receiver_id);

            } 



            $this->db->from('offer_task');

            $this->db->where('offer_task.task_id', $row->task_id);

            $this->db->where('offer_task.offer_send_by=\''.$userID.'\'');    

            $this->db->where('offer_task.is_responded', 1);        

            $this->db->where('offer_task.offer_status', 1);

            $this->db->where('offer_task.offer_is_deleted', 0);                                  

            $offer_send_response_count = $this->db->count_all_results();

 

            $this->db->from('offer_task');

            $this->db->where('offer_task.task_id', $row->task_id);

            $this->db->where('offer_task.offer_send_by=\''.$userID.'\'');    

            $this->db->where('offer_task.is_refused', 1);   

            $this->db->where('offer_task.offer_status', 1);

            $this->db->where('offer_task.offer_is_deleted', 0);                                       

            $offer_send_refuse_count = $this->db->count_all_results();



            $this->db->from('offer_task');

            $this->db->where('offer_task.task_id', $row->task_id);

            $this->db->where('offer_task.offer_send_by=\''.$userID.'\'');    

            $this->db->where('offer_task.is_refused', 0);  

            $this->db->where('offer_task.is_responded', 0);  

            $this->db->where('offer_task.offer_status', 1);

            $this->db->where('offer_task.offer_is_deleted', 0);                                                  

            $offer_send_wait_count = $this->db->count_all_results();





            $basicInfo = array('task_id' => $row->task_id, 'user_task_id' => $row->user_task_id, 'task_name' => $row->task_name, 'task_details' => $row->task_details, 'task_due_date' => $row->task_due_date, 'task_origin_location' => $row->task_origin_location, 'task_origin_country' => $row->task_origin_country, 'task_total_budget' => $row->task_total_budget, 'task_status' => $row->task_status, 'task_history' => $row->task_history, 'task_is_complete' => $row->task_is_complete, 'task_is_ongoing' => $row->task_is_ongoing, 'task_is_deleted' => $row->task_is_deleted, 'task_created_by' => $row->task_created_by, 'task_doc' => $row->task_doc);



            $task_list[] = array('basic_info' => $basicInfo, 'freelancer_list' => $offered_freelancer_list, 'offer_send_response_count' => $offer_send_response_count, 'offer_send_refuse_count' => $offer_send_refuse_count, 'offer_send_wait_count' => $offer_send_wait_count);

        }



        return $task_list;         

    }



    public function close_offer_send_by_user($userID = null, $user_task_id = null) {



        if(empty($userID) || empty($user_task_id))

            return FALSE;



        $task_details = $this->get_task_info_by_user_task_id($user_task_id); 

        $data = array(

            'offer_is_deleted' => 1,

            'offer_status' => 0

        );

        $this->db->where('task_id', $task_details->task_id);

        $this->db->where('offer_send_by', $userID);        

        $result = $this->db->update('offer_task', $data);

        //echo $this->db->last_query();

        if($result)

            return TRUE;

        else

            return FALSE;

    }              

	public function insert_data($tableName,$data) { 

		if ($this->db->insert($tableName, $data)) { 

			return TRUE; 

		}else{

			return array('status' => FALSE, 'message' => 'unable_to_add_record_in_db');

		} 

	} 

	

	public function get_comment_by_task($userID = null){

		if(empty($userID))

            return $task_list;

		

		$this->db->select('*');

		$this->db->from('comment_master');

		$this->db->join('users','users.user_id=comment_master.user_id','left');

		$this->db->join('user_login','user_login.user_id=comment_master.user_id','left');

		$this->db->where('tast_user_id',$userID);

		$query = $this->db->get();

		if($query->num_rows() > 0){

			return $query->result();

		}else{

			return array();

		}

	}
	
	public function get_hired_freelancer_info($taskId = ''){
		$this->db->select('task_hired.*');
		$this->db->from('task_hired');
		$this->db->where('task_hired.task_id',$taskId);
		$this->db->where('task_hired.hired_status',1);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			return array();
		}
	}

	

	public function getJoinDataByCondition($tablename=array(),$jointype=array(),$joincondition=array(),$condition='',$fieldArr=array(),$limit=0,$oderby=''){

		$this->db->select($tablename[0].'_alias.'.$fieldArr[0]);

		$this->db->from($tablename[0].' as '.$tablename[0].'_alias');

		for($i=1;$i<=sizeof($jointype);$i++){

			$this->db->select($tablename[$i].'_alias.'.$fieldArr[$i]);

			$this->db->join($tablename[$i].' as '.$tablename[$i].'_alias',$joincondition[$i-1],$jointype[$i-1]);

		}

		$this->db->where($condition);

		if($limit!=''){

			if($limit!="all"){

				$this->db->limit($limit);

			}

			if($oderby!=''){

				$this->db->order_by($oderby);

			}

			$data	= $this->db->get();
            //showQuery();die('1');
			if($data->num_rows() > 0){

				return $data->result();

			}

		}else if($limit==0){

			if($oderby!=''){

				$this->db->order_by($oderby);

			}

			$data	= $this->db->get();

			if($data->num_rows() > 0){

				return $data->row();

			}

		}

	}

	

	public function accept_offer($task_id = '',$user_id = '',$action = ''){
		if($task_id != '' && $action != ''){
			$return = $this->db->where('task_interested.task_id',$task_id)->where('task_interested.interested_user_id',$user_id)->update('task_interested',array('accept_status' => $action, 'interested_date_time'=> date('Y-m-d H:i:s')));
			
			if($action == 'A'){
				$this->db->where('task_id',$task_id)->where('receiver_id',$user_id)->update('offer_task',array('is_responded' => 1,'response_date' => date('Y-m-d H:i:s')));
			}else if($action == 'R'){
				$this->db->where('task_id',$task_id)->where('receiver_id',$user_id)->update('offer_task',array('is_refused' => 1,'refused_date' => date('Y-m-d H:i:s')));
			}
			return true;
		}else{
			return false;
		}
	}

	function delete_action($table = '', $data = array(), $condition = array()){
		$return = $this->db->where($condition)->update($table,$data);
		if($return){
			return true;
		}else{
			return false;
		}
	}
	 public function project_details($user_id, $limit = 0)
    {
        $this->db->join('task_hired', 'task_hired.task_id = task.task_id');
        $this->db->join('users', 'users.user_id = task.task_created_by');
        $this->db->where('freelancer_id', $user_id);
        $this->db->where('task.task_is_complete', 1);
        $this->db->where('task.task_status', 1);
        if($this->input->post('from_date')) {
        	$from_date = date('Y-m-d', strtotime($this->input->post('from_date')));        	
        	$this->db->where('task.task_doc >=', $from_date);
        }
        if($this->input->post('to_date')) {
        	$to_date = date('Y-m-d', strtotime($this->input->post('to_date')));        	
        	$this->db->where('task.task_doc <=', $to_date);
        }
        if($limit > 0)
            $this->db->limit($limit);
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        $result = $this->db->get('task');
        if($result->num_rows() > 0){
            return $result->result();
        }
        return [];
    }

    public function project_details_client($user_id, $limit = 0)
    {
        $this->db->join('task_hired', 'task_hired.task_id = task.task_id');
        $this->db->join('users', 'users.user_id = task.task_created_by');
        $this->db->where('task_created_by', $user_id);
        $this->db->where('task.task_is_complete', 1);
        $this->db->where('task.task_status', 1);
        if($this->input->post('from_date')) {
        	$from_date = date('Y-m-d', strtotime($this->input->post('from_date')));        	
        	$this->db->where('task.task_doc >=', $from_date);
        }
        if($this->input->post('to_date')) {
        	$to_date = date('Y-m-d', strtotime($this->input->post('to_date')));        	
        	$this->db->where('task.task_doc <=', $to_date);
        }
        if($limit > 0)
            $this->db->limit($limit);
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        $result = $this->db->get('task');
        if($result->num_rows() > 0){
            return $result->result();
        }
        return [];
    }

    public function client_analytics($user_id)
    {
    	$month = array();

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 1 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();
    	
    	//Earnings Overview
    	if(count($result)>0) {
    		$month['January']=$result[0]->earned;
    	} else{
    		$month['January']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 2 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();
    	
    	//Earnings Overview
    	if(count($result)>0) {
    		$month['February']=$result[0]->earned;
    	} else{
    		$month['February']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 3 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['March']=$result[0]->earned;
    	} else{
    		$month['March']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 4 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['April']=$result[0]->earned;
    	} else{
    		$month['April']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 5 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['May']=$result[0]->earned;
    	} else{
    		$month['May']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 6 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['June']=$result[0]->earned;
    	} else{
    		$month['June']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 7 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['July']=$result[0]->earned;
    	} else{
    		$month['July']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 8 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['August']=$result[0]->earned;
    	} else{
    		$month['August']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 9 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['September']=$result[0]->earned;
    	} else{
    		$month['September']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 10 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['October']=$result[0]->earned;
    	} else{
    		$month['October']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 11 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['November']=$result[0]->earned;
    	} else{
    		$month['November']=0;
    	}

    	$q = $this->db->query("SELECT MONTHNAME(hired_doc) as mon, SUM(agreed_budget) as earned FROM task JOIN task_hired ON task_hired.task_id = task.task_id WHERE task_created_by = '".$this->session->userdata('user_id')."' AND  month(hired_doc) = 12 AND hire_is_completed=1 GROUP BY mon");
    	$result = $q->result();

    	//Earnings Overview
    	if(count($result)>0) {
    		$month['December']=$result[0]->earned;
    	} else{
    		$month['December']=0;
    	}

    	$monthly_projects_arr = $this->db->query("SELECT * FROM task WHERE task.task_created_by='".$this->session->userdata('user_id')."' AND MONTH(task.task_doc) = MONTH(CURRENT_DATE())");
    	$monthly_projects = $monthly_projects_arr->num_rows();

    	$yearly_projects_arr = $this->db->query("SELECT * FROM task WHERE task.task_created_by='".$this->session->userdata('user_id')."' AND YEAR(task.task_doc) = YEAR(CURRENT_DATE())");
    	$yearly_projects = $yearly_projects_arr->num_rows();    	

    	$pending_projects_arr = $this->db->query("SELECT * FROM task WHERE task.task_created_by='".$this->session->userdata('user_id')."' AND task.task_status = 1 AND task.task_is_complete = 0 AND task.task_is_ongoing = 0 AND (task.task_is_deleted IS NULL OR task.task_is_deleted=0)");
    	$pending_projects = $pending_projects_arr->num_rows();

    	$offer_q = $this->db->query("SELECT task_id FROM task_notification WHERE notification_to='".$this->session->userdata('user_id')."' AND notification_master_id=12");
    	$offer_result = $offer_q->result_array();

    	$hired_q = $this->db->query("SELECT task_id FROM task_notification WHERE notification_to='".$this->session->userdata('user_id')."' AND notification_master_id=14");
    	$hired_result = $hired_q->result_array();

    	$total_microkey_projects_arr = $this->db->query("SELECT * FROM microkey WHERE user_id='".$this->session->userdata('user_id')."'");
    	$total_microkey_projects = $total_microkey_projects_arr->num_rows();

    	$offerArray = array_map('current', $offer_result);
    	$hireArray = array_map('current', $hired_result);
    	$refferal = array_diff($hireArray , $offerArray);

		  //   	print_r($offerArray);
		  //   	echo "<br>";
		  //   	print_r($hireArray);
				// echo "<br>";
		  //   	print_r($refferal);
		  //   	exit;

    	$data['total_offer'] = count($offerArray);
    	$data['total_referral'] = count($refferal);
    	$data['total_microkey_projects'] = $total_microkey_projects;

        $this->db->select('agreed_budget, name, task_name, freelancer_id, task_created_by, task_hired.hire_date, task_hired.hired_end_date');
        $this->db->join('task_hired', 'task_hired.task_id = task.task_id');
        $this->db->join('users', 'users.user_id = task_hired.freelancer_id');
        $this->db->where('task_created_by', $user_id);
        $this->db->where('task.task_is_complete', 1);
        $this->db->where('task.task_status', 1);
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        if($this->input->post('from_date')) {
        	$from_date = date('Y-m-d', strtotime($this->input->post('from_date')));        	
        	$this->db->where('task.task_doc >=', $from_date);
        }
        if($this->input->post('to_date')) {
        	$to_date = date('Y-m-d', strtotime($this->input->post('to_date')));        	
        	$this->db->where('task.task_doc <=', $to_date);
        }
        $this->db->group_by('task.task_id');
        $result = $this->db->get('task');
        if($result->num_rows() > 0){
            $result = $result->result();
        } else {
        	$result = [];
        }

        $current_month = date('F');

        $yearly_income = array_sum($month);
        $monthly_income = round($month[$current_month]);
        
        $return_data = array(
        	'analytics' => $result,
        	'month' => $month,
        	'monthly_projects' => $monthly_projects,
        	'yearly_projects' => $yearly_projects,
        	'yearly_income' => $yearly_income,
        	'monthly_income' => $monthly_income,
        	'pending_projects' => $pending_projects,
        	'total_offer' => count($offerArray),
        	'total_referral' => count($refferal),
        	'total_microkey_projects' => $total_microkey_projects
        );

        return $return_data;
    }

	/*-------------------PM--------------------*/
	public function get_proposal($task_id = '')
	{		
		$this->db->select('task_proposal.*','task.task_name');
		$this->db->from('task_proposal');
		$this->db->join('task','task.task_id=task_proposal.task_id','left');
		$this->db->where('task_proposal.task_id',$task_id);
		$query = $this->db->get();
		return $query->result();
	}

    public function get_freelancers_proposal($task_id = '')
    {
    	$this->db->select('task_proposal.*','task.task_name');
        $this->db->from('task_proposal');
        $this->db->join('task','task.task_id=task_proposal.task_id','left');
        $this->db->where('task.user_task_id',$task_id);
        $this->db->where('task.task_is_complete', 0);
        $this->db->where('task.task_is_ongoing', 0);
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        if($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }
    public function get_freelancers_proposa_admin($task_id = '')
    {
    	
        $this->db->select('task_proposal.*','task.task_name');
        $this->db->from('task_proposal');
        $this->db->join('task','task.task_id=task_proposal.task_id','left');
        $this->db->where('task.user_task_id',$task_id);
        // $this->db->where('task.task_is_complete', 0);
        // $this->db->where('task.task_is_ongoing', 0);
        $this->db->where('(task.task_is_deleted IS NULL OR task.task_is_deleted=0)');
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        if($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }
	public function getTaskDataById($task_id = 0) {
		$data = $this->db->select("*")->from('task')->where('task_id', $task_id)->get()->row();
		return $data;
	}

}		