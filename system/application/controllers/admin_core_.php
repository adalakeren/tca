<?php include_once 'json/JSON.php'; ?>
<?php include_once 'helpers.php'; ?>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_core
 *
 * @author CKB
 */
class admin_core extends Controller {

    function admin_core() {
        parent::Controller();
        $this->load->database();
        $this->load->helper(array('url'));
        $this->load->library('pagination');
        $this->load->library('session');
    }

    function index() {
        $this->load->view('welcome_message');
    }

    //user administrator =======================================================
    function user_accesssLogin(){

        $config['hostname'] = "CKBAZSQLY101.ckb.co.id";
        $config['username'] = "fast2";
        $config['password'] = "aceleramiento";
        $config['database'] = "user_access";
        $config['dbdriver'] = "mysql";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = FALSE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";

        $DBfast = $this->load->database($config, TRUE);

        $setLogin['appcode'] = $DBfast->escape_str(49);
        $setLogin['name'] = $DBfast->escape($_POST["txtUser"]);
        $pass = $_POST["txtPassword"];
        $setLogin['pass'] = $DBfast->escape(md5($pass));

        
        $setStrSqlLogin = "SELECT user_access.t_application_access.* , user_access.t_user.employee_id, user_access.t_user.valid_to AS user_validto , CONCAT_WS(' ',employee.t_personel.first_name,employee.t_personel.middle_name,employee.t_personel.last_name) AS fullname , user_access.t_user.password,user_access.t_application_access.user_level_id
                            ,employee.t_personel.area_id,employee.t_personel.station_id, employee.t_personel.division_id,employee.t_personel.department_id,employee.t_division.description divDesc,employee.t_department.description deptDesc
                            FROM  user_access.t_application_access
                            LEFT JOIN user_access.t_user ON (t_application_access.user_id = t_user.user_id)
                            LEFT JOIN employee.t_employee  ON (employee.t_employee.employee_id = user_access.t_user.employee_id)
                            LEFT JOIN employee.t_personel  ON ( employee.t_personel.employee_id = user_access.t_user.employee_id)
                            LEFT JOIN employee.t_division ON ( employee.t_division.division_id = employee.t_personel.division_id)
                            LEFT JOIN employee.t_department ON ( employee.t_department.department_id = employee.t_personel.department_id)
                            WHERE user_access.t_application_access.application_id = ". $setLogin['appcode']."
                            AND user_access.t_user.user_id =". $setLogin['name']." ";
        
        /*
        if(md5($pass) != "568c11f5230c6571e0fcb05aac5e33ee"){
            $setStrSqlLogin .= " AND user_access.t_user.password = ". $setLogin['pass']." ";
        }
        */
        //echo $setStrSqlLogin;exit;
        $userLogin = $DBfast->query($setStrSqlLogin);

        if ($userLogin->num_rows() > 0)
        {
            foreach ($userLogin->result() as $row)
            {
                //here for get user admin option
                $getUserAdmin = $this->initUserAdminStatus($row->user_id);
                $setSessionAdmin = '0';
                if($getUserAdmin > 0){
                    $setSessionAdmin = '1';
                }
                //==============================================================

                //here get user level detail
                $userLevelDetail = $this->getUserLevelDetail($row->employee_id);
                
                //==============================================================
                
                $userData = array(
                                   'userName'  => $row->user_id,
                                   'userEmpID'  => $row->employee_id,
                                   'userNameFull'  => $row->fullname,
                                   'userLevel'  => $row->user_level_id,
                                   'userStat'  => $row->station_id,
                                   'userDiv'  => $row->division_id,
                                   'userDept'  => $row->department_id,
                                   'userDivName'  => $row->divDesc,
                                   'userDeptName'  => $row->deptDesc,
                                   'logged_in' => TRUE,
                                   'sessionAdmin' => $setSessionAdmin,
                                   'sessionLevelDetail' => $userLevelDetail
                               );

                $this->session->set_userdata($userData);

                session_start();
                $_SESSION['sess_userID'] = $row->user_id;
				$_SESSION['userStat'] = $row->station_id;
                $_SESSION['sess_userName'] = $row->fullname;
                $_SESSION['sess_deptName'] = $row->deptDesc;
                $_SESSION['sess_divName'] = $row->divDesc;
                $_SESSION['sess_adminOption'] = $setSessionAdmin;
                $_SESSION['sess_userLevDetail'] = $userLevelDetail;

                redirect('/main_core','refresh');
            }
        }else{
            redirect('/admin_core/user_logout','refresh');
        }

    }
    
    function user_accesssLogin_ssid($sessionId){
        
        //getSession=====================================================================
        $json = new Services_JSON();
        
        $setPortalPage = "http://10.10.3.3/ckbportal/index.php/services/encSessions/";
        $getSsoService = file_get_contents($setPortalPage . $sessionId);
        $getJsonDecrypt = $this->decrypt($getSsoService, "120409essecrtkey") ;
        
        $jsonResUls = $json->decode($getJsonDecrypt);
        
        if(isset($jsonResUls->userName)){$user = $jsonResUls->userName;}else{
            redirect('/admin_core/user_logout','refresh');
        }
        
        $pass = null;
        $targetSuccessLoc = base_url() . 'index.php/main_core';
        $targetFailLoc = base_url() . 'index.php/admin_core/user_logout';
        
        if(isset($_SESSION['userName'])){$user = $_SESSION['userName'];}
        if(isset($_POST['passw'])){$pass = $_POST['passw'];}
        
        if($user == ""){redirect('/admin_core/user_logout','refresh');}
        //===============================================================================
        
        
        $config['hostname'] = "CKBAZSQLY101.ckb.co.id";
        $config['username'] = "fast2";
        $config['password'] = "aceleramiento";
        $config['database'] = "user_access";
        $config['dbdriver'] = "mysql";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = FALSE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";

        $DBfast = $this->load->database($config, TRUE);

        $setLogin['appcode'] = $DBfast->escape_str(49);
        $setLogin['name'] = $DBfast->escape($user);
        //$pass = $_POST["txtPassword"];
        $setLogin['pass'] = $DBfast->escape(md5($pass));

        
        $setStrSqlLogin = "SELECT user_access.t_application_access.* , user_access.t_user.employee_id, user_access.t_user.valid_to AS user_validto , CONCAT_WS(' ',employee.t_personel.first_name,employee.t_personel.middle_name,employee.t_personel.last_name) AS fullname , user_access.t_user.password,user_access.t_application_access.user_level_id
                            ,employee.t_personel.area_id,employee.t_personel.station_id, employee.t_personel.division_id,employee.t_personel.department_id,employee.t_division.description divDesc,employee.t_department.description deptDesc
                            FROM  user_access.t_application_access
                            LEFT JOIN user_access.t_user ON (t_application_access.user_id = t_user.user_id)
                            LEFT JOIN employee.t_employee  ON (employee.t_employee.employee_id = user_access.t_user.employee_id)
                            LEFT JOIN employee.t_personel  ON ( employee.t_personel.employee_id = user_access.t_user.employee_id)
                            LEFT JOIN employee.t_division ON ( employee.t_division.division_id = employee.t_personel.division_id)
                            LEFT JOIN employee.t_department ON ( employee.t_department.department_id = employee.t_personel.department_id)
                            WHERE user_access.t_application_access.application_id = ". $setLogin['appcode']."
                            AND user_access.t_user.user_id =". $setLogin['name']." ";
        
        /*
        if(md5($pass) != "568c11f5230c6571e0fcb05aac5e33ee"){
            $setStrSqlLogin .= " AND user_access.t_user.password = ". $setLogin['pass']." ";
        }
        */
        $userLogin = $DBfast->query($setStrSqlLogin);

        if ($userLogin->num_rows() > 0)
        {
            foreach ($userLogin->result() as $row)
            {
                //here for get user admin option
                $getUserAdmin = $this->initUserAdminStatus($row->user_id);
                $setSessionAdmin = '0';
                if($getUserAdmin > 0){
                    $setSessionAdmin = '1';
                }
                //==============================================================

                //here get user level detail
                $userLevelDetail = $this->getUserLevelDetail($row->employee_id);
                
                //==============================================================
                
                $userData = array(
                                   'userName'  => $row->user_id,
                                   'userEmpID'  => $row->employee_id,
                                   'userNameFull'  => $row->fullname,
                                   'userLevel'  => $row->user_level_id,
                                   'userStat'  => $row->station_id,
                                   'userDiv'  => $row->division_id,
                                   'userDept'  => $row->department_id,
                                   'userDivName'  => $row->divDesc,
                                   'userDeptName'  => $row->deptDesc,
                                   'logged_in' => TRUE,
                                   'sessionAdmin' => $setSessionAdmin,
                                   'sessionLevelDetail' => $userLevelDetail
                               );

                $this->session->set_userdata($userData);

                session_start();
                $_SESSION['sess_userID'] = $row->user_id;
                $_SESSION['sess_userName'] = $row->fullname;
                $_SESSION['sess_deptName'] = $row->deptDesc;
                $_SESSION['sess_divName'] = $row->divDesc;
                $_SESSION['sess_adminOption'] = $setSessionAdmin;
                $_SESSION['sess_userLevDetail'] = $userLevelDetail;

                redirect('/main_core','refresh');
                //echo json_encode(array("redirect"=>"$targetSuccessLoc"));exit;
                
            }
        }else{            
            redirect('/admin_core/user_logout','refresh');
            //echo json_encode(array("redirect"=>"$targetFailLoc"));exit;
        }
        
    }

    function getUserLevelDetail($getEmployeeID){
        $resArray = "";
        $getQuery = $this->db->select('*')->from('t_direct_report a')->where('a.employee_id',$getEmployeeID)->get();
        
        foreach ($getQuery->result() as $rows){
//            array_push($resArray, $rows->job_title_detail);
            $resArray = $rows->job_title_detail;
        }
       
        return $resArray;
    }
    
    
    function login(){
        $this->load->view('login');
    }
    
    function user_logout(){
        session_start();
        session_destroy();
        $this->session->unset_userdata();
        $this->session->sess_destroy();

        $this->db->close();
        redirect('/admin_core/login','refresh');

        //$this->index();
    }
    
    function initActiveQuiz(){
        $this->db->select('*');
        $this->db->from('ms_quiz_active');
        $whereArray = array ('isactive' => 1);
        $this->db->where($whereArray);

        $setActive = 0;
        $query = $this->db->get();
        foreach ($query->result() as $row){
            $setActive = $row->active_version;
        }

        return $setActive;
    }

    function initUserAdminStatus($getUserID){
        $this->db->select('*');
        $this->db->from('t_user_admin');
        $whereArray = array ('user_id' => $getUserID);
        $this->db->where($whereArray);

        $query = $this->db->get();

        return $query->num_rows();
    }

    function setuserlogs($getUser,$getCounter,$logType,$whatHit = '') {
        $getWhereArry = array('user_id'=>$getUser,
                        'log_type'=>$logType,
                        'log_date'=>date("Y-m-d")
                        );
        $getCountExisting = $this->db->select('*')->from('tr_logs')->where($getWhereArry)->get()->num_rows();
//        echo  $this->db->last_query();
        $tblName = 'tr_logs';
        if($getCountExisting != 0){
//            echo 'me </br>';
            $query = "UPDATE tr_logs SET counters = counters+1 , input_datetime = now() WHERE user_id = '".$getUser."' AND log_type = '".$logType."' AND date(log_date) = date(now())";
            $execData = $this->db->query($query);
        }else{
            $data = array(
                'user_id' => $getUser,
                'counters' => $getCounter,
                'log_type' => $logType,
                'log_what_hit' => $whatHit,
                'log_date '=> date("Y-m-d"),
                'input_datetime' => date("Y-m-d H:i:s")
            );
            $execData = $this->db->insert($tblName, $data);
        }

    }
    
    function encrypt($message, $initialVector = "120409esivectors", $secretKey="120409essecrtkey") {
        // The keys
        //$secretKey = "1234567890abcdef";
        //$ivKey = "fedcba9876543210";

        $secretKey = $secretKey;
        $ivKey = $initialVector;

        //$plaintext = "this is the text that we want to encrypt in php";
        $plaintext = $message;

        // Open and init the mcrypt library with the keys
        $td = mcrypt_module_open("rijndael-128", "", "cbc", $ivKey);
        mcrypt_generic_init($td, $secretKey, $ivKey);

        // encrypt the text
        $crypt_text = mcrypt_generic($td, $plaintext);

        // deinit and close mcrypt module
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        // encode with base 64
        return base64_encode($crypt_text);
    }

    function decrypt($code, $key, $initialVector = "120409esivectors", $secretKey="120409essecrtkey") {
        // The keys
        //$secretKey = "1234567890abcdef";
        //$ivKey = "fedcba9876543210";

        $secretKey = $secretKey;
        $ivKey = $initialVector;

        //$cryptedText = "VD9X9nF6uENC+H8TKazjztadnp6wO7kqUBvpH7tQPrWYGLaB8ltR99U2VZ/MGtIeE5yhNr2aYE3hM6Mgm+jhRg==";
        $cryptedText = $code;

        // Decode the encrypted text
        $text = base64_decode($cryptedText);

        // open and init the mcrypt module
        $td = mcrypt_module_open("rijndael-128", "", "cbc", $ivKey);
        mcrypt_generic_init($td, $secretKey, $ivKey);

        // decrypt the text
        $decrypted = mdecrypt_generic($td, $text);

        // deinit and close the mcrypt module
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $decrypted;
    }
	
	function debugApproval(){		
        $helpers = new helpers();
        $fastConn = $helpers->_initServDataFast_custom("employee");
        
        $strSqlSyncDirectReport = "select a.job_title_id as job_title_id
                                    , a.job_title_detail_id as job_title_detail
                                    , a.sap_id as sn_sap
                                    , concat_ws(' ',a.first_name, a.middle_name, a.last_name) as name 
                                    , b.description as description
                                    , a.level_id as level_id
                                    , b.report_to_title_id as direct_report_to_id
                                    , a.employee_id as employee_id
									, concat_ws(' ',c.first_name, c.middle_name, c.last_name) as name_to
                                    from t_personel a left join t_job_title_detail b on a.job_title_detail_id = b.job_title_detail_id
									left join t_personel c on c.job_title_detail_id = b.report_to_title_id
                                    where a.job_title_detail_id is not null and a.job_title_detail_id <> ''";
        
        $getFastQuery = $fastConn->query($strSqlSyncDirectReport);
        if($getFastQuery->num_rows() > 0){            
            $execDelete = $this->db->query("DELETE FROM t_direct_report");
        }
        foreach($getFastQuery->result() as $fastRows){
			if($fastRows->name_to == ""){
				
			}
		
            $data = array(
                        'job_title_id' => $fastRows->job_title_id ,
                        'job_title_detail' => $fastRows->job_title_detail ,
                        'sn_sap' => $fastRows->sn_sap ,
                        'name' => $fastRows->name ,
                        'description' => $fastRows->description ,
                        'level_id' => $fastRows->level_id ,
                        'direct_report_to_id' => $fastRows->direct_report_to_id ,
                        'employee_id' => $fastRows->employee_id                 
                    );
            $execData = $this->db->insert("t_direct_report", $data);
        }
        
        if($execData){
		
			//$strSqlUpdate = "Update t_direct_report set direct_report_to_id = 'DIR' where sn_sap = '20361'"; //update approval pa UF
			//$this->db->query($strSqlUpdate);
			
			/*
			$exp_date = "2013-06-14"; 
			$todays_date = date("Y-m-d"); 
			$today = strtotime($todays_date); 
			$expiration_date = strtotime($exp_date); 			
			if ($expiration_date > $today) { 
				$strSqlUpdate = "Update t_direct_report set direct_report_to_id = 'PLS3' where direct_report_to_id = 'GMI'"; //update approval bu ety
				$this->db->query($strSqlUpdate);
			}
			*/
			
			$strSqlUpdate = "Update t_direct_report set level_id = 'MGR' where sn_sap = '20506'"; //update Level pa Chandra
			$this->db->query($strSqlUpdate);			
			$strSqlUpdate = "Update t_direct_report set direct_report_to_id = 'APD' where sn_sap = '20492'"; //update approval pa Gunadi -> Pa Chandra
			$this->db->query($strSqlUpdate);
			$strSqlUpdate = "Update t_direct_report set level_id = 'MGR' where sn_sap = '20569'"; //update Level pa Anton Haryatmo approval acting manager
			$this->db->query($strSqlUpdate);		
			
            //echo "Update Data Complete";
        }
        
    }
    
}
?>
