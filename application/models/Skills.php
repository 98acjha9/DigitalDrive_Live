<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Skills extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

    /*
     * Skill information By ID
     */
    public function get_skill_by_id($sId = null){
        if(empty($sId))
            return FALSE;

        $this->db->select('*');
        $this->db->from('area_of_interest');
        $this->db->where('area_of_interest_id', $sId);
        $this->db->where('status', 1);
        $this->db->where('deleted',0);        
        $query = $this->db->get();
        return $query->row();
    }

    /*
     * Skill information By Name
     */
    public function get_skill_by_name($sName= null){
        if(empty($sName))
            return FALSE;

        $this->db->select('*');
        $this->db->from('area_of_interest');
        $this->db->where('name', $sName);
        $this->db->where('status', 1);
        $this->db->where('deleted',0);        
        $query = $this->db->get();
        return $query->row();
    }

    /*
     * Get All Skill information
     */
    public function get_all_skill_info(){
        $skill_list = array();

        $this->db->select('*');
        $this->db->from('area_of_interest');
        $this->db->where('status', 1);
        $this->db->where('deleted',0);
        $query = $this->db->get();
        foreach ($query->result() as $row){
            $skill_list[] = $row;
        }
                
        return $skill_list;
    }

    public function get_user_skills($user_id) {
        $skill_list = array();
        // Get user selected skills
        $this->db->select('user_area_of_interest.area_of_interest_id,area_of_interest.name,user_area_of_interest.user_id');
        $this->db->from('user_area_of_interest');
        $this->db->join('area_of_interest', 'area_of_interest.area_of_interest_id = user_area_of_interest.area_of_interest_id');
        $this->db->where('user_area_of_interest.user_id', $user_id);
        $this->db->limit(1);
        $query_skill = $this->db->get();
        //echo $this->db->last_query();die;
        foreach ($query_skill->result() as $row_skill){
            $skill_list[] = $row_skill->name;
        }
        return $skill_list;
    }
	
	//modified on 23-10-2020
	public function get_all_skill_info_by_id($skillIDs = ''){
		if(empty($skillIDs)) return array();
								   
																														   
												 
		$sql = "SELECT * FROM `area_of_interest` WHERE area_of_interest_id IN (".$skillIDs.")";
		$getClientInfo = $this->db->query($sql);
		$SkillsName=$getClientInfo->result_object();
		// echo '<pre>'; print_r($SkillsName);die();
										   
													   
											 
		 
		return $SkillsName;
	}
}		