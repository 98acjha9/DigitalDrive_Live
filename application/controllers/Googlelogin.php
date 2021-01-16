<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Googlelogin extends CI_Controller {

public function __construct()
{
	parent::__construct();
	require_once APPPATH.'third_party/src/Google_Client.php';
	require_once APPPATH.'third_party/src/contrib/Google_Oauth2Service.php';
	$this->load->model('Users');
}
	
	public function index()
	{
		$this->load->view('login_view');
	}
	
	public function login($type='')
	{
		if(empty($type)){
			$type="Client";
		}
		$clientId = '688554065697-8bne0fvu2qhlv51nfh41r9j56sfvdqut.apps.googleusercontent.com'; //Google client ID
		$clientSecret = 'GA_GfdecFevGdjsJ2BEkK1fC'; //Google client secret
		$redirectURL = 'https://www.drivedigitally.com/live/sign-in';
		
		//https://curl.haxx.se/docs/caextract.html

		//Call Google API
		$gClient = new Google_Client();
		$gClient->setApplicationName('Login');
		$gClient->setClientId($clientId);
		$gClient->setClientSecret($clientSecret);
		$gClient->setRedirectUri($redirectURL);
		$google_oauthV2 = new Google_Oauth2Service($gClient);
		
		if(isset($_GET['code']))
		{
			$gClient->authenticate($_GET['code']);
			$_SESSION['token'] = $gClient->getAccessToken();
			header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
		}

		if (isset($_SESSION['token'])) 
		{
			$gClient->setAccessToken($_SESSION['token']);
		}
		
		if ($gClient->getAccessToken()) {
			
            $userProfile = $google_oauthV2->userinfo->get();
            $mobile=$userProfile['id'];
            $email=$userProfile['email'];
            $username=$userProfile['email'];
            $picture=$userProfile['picture'];
            $name=$userProfile['name'];
            $type;
            $data =  array($username ,$email,$mobile,$picture,$name,$type);
            	
             
              // $query="INSERT INTO `user_login` (`username`, `email`, `phone_no`, `profile_image`) VALUES ('$name', '$email', 'mobile', '$picture')";
            $userdata = $this->db->get_where('user_login',array('username'=>$username))->row();
              if(empty($userdata)){
            	$content = $this->Users->register_user_google($data);
            }
            else{
            	redirect('sign-in');
            }
          
			
			

        } 
		else 
		{
            $url = $gClient->createAuthUrl();
		    header("Location: $url");
            exit;
        }
	}	
}
