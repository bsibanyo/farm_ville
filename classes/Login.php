<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	public function login(){
		// extract($_POST);
		extract(sanitize(($_POST)));

		$qry = $this->conn->query("SELECT * from users where username = '$username' and password = md5('$password') ");
		if($qry->num_rows > 0){
			foreach($qry->fetch_array() as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}

			}
			$this->settings->set_userdata('login_type',1);
		return json_encode(array('status'=>'success'));
		}else{
		return json_encode(array('status'=>'incorrect','last_qry'=>"SELECT * from users where username = '$username' and password = md5('$password') "));
		}
	}
	public function logout(){
		if($this->settings->sess_des()){
			redirect('admin/login.php');
		}
	}
	function login_user(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * from clients where email = '$email' and password = md5('$password') ");
		if($qry->num_rows > 0){
			foreach($qry->fetch_array() as $k => $v){
				$this->settings->set_userdata($k,$v);
			}
			$this->settings->set_userdata('login_type',1);
		$resp['status'] = 'success';
		}else{
		$resp['status'] = 'incorrect';
		}
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function reset_password() {
		// extract($_POST);
		$remember_token = bin2hex(random_bytes(20));
		$password = htmlspecialchars(strip_tags(trim($_POST['password'])));
		$conf_password = htmlspecialchars(strip_tags(trim($_POST['conf_password'])));
		$remember_token = htmlspecialchars(strip_tags(trim($_POST['remember_token'])));
		if ($password === $conf_password) {
			$new_password = md5($password);
			$save = $this->conn->query( "UPDATE clients SET password='$new_password', remember_token=NULL WHERE remember_token='$remember_token'");
			$resp['status'] = 'success';
		}else {
			$resp['status'] = 'incorrect';
		}
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}
		return json_encode($resp);
		// return json_encode($_POST);
	}

	function forgot_password() {
		extract($_POST);
		$qry = $this->conn->query("SELECT * from clients where email = '$email' LIMIT 1");
		if($qry->num_rows > 0){
			$result = $qry->fetch_assoc();
			$id = $result['id'];
			$remember_token = bin2hex(random_bytes(20));
			$save = $this->conn->query( "UPDATE clients SET remember_token='$remember_token' WHERE id=$id");
			if ($save) {
				sendResetPassword($email, $remember_token);
			}
			$this->settings->set_userdata('login_type',0);
			$resp['status'] = 'success';
		}else{
		$resp['status'] = 'incorrect';
		}
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['_error'] = $this->conn->error;
		}
		return json_encode($resp);
		// return json_encode($_POST);
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'login_user':
		echo $auth->login_user();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	case 'forgot_password':
		echo $auth->forgot_password();
		break;
	case 'reset_password':
		echo $auth->reset_password();
		break;
	default:
		echo $auth->index();
		break;
}

