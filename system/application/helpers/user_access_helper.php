<?php
class user_access_helper extends Controller{
	
	var $config;
	
	function user_acces_helper(){
		parent::Controller();
		$config['hostname'] = "10.144.250.4";
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
	}
	
	function get_user_access($appcode, $user)
	{
		$DBfast = $this->load->database($config, TRUE);
		
		$setStrSqlLogin = "SELECT user_access.t_application_access.* , user_access.t_user.employee_id, user_access.t_user.valid_to AS user_validto , CONCAT_WS(' ',employee.t_personel.first_name,employee.t_personel.middle_name,employee.t_personel.last_name) AS fullname , user_access.t_user.password,user_access.t_application_access.user_level_id
                            ,employee.t_personel.area_id,employee.t_personel.station_id, employee.t_personel.division_id,employee.t_personel.department_id,employee.t_division.description divDesc,employee.t_department.description deptDesc
                            FROM  user_access.t_application_access
                            LEFT JOIN user_access.t_user ON (t_application_access.user_id = t_user.user_id)
                            LEFT JOIN employee.t_employee  ON (employee.t_employee.employee_id = user_access.t_user.employee_id)
                            LEFT JOIN employee.t_personel  ON ( employee.t_personel.employee_id = user_access.t_user.employee_id)
                            LEFT JOIN employee.t_division ON ( employee.t_division.division_id = employee.t_personel.division_id)
                            LEFT JOIN employee.t_department ON ( employee.t_department.department_id = employee.t_personel.department_id)
                            WHERE user_access.t_application_access.application_id = '".$appcode."'
                            AND user_access.t_user.user_id = '".$user."'";
							
		$userLogin = $DBfast->query($setStrSqlLogin);
		
		foreach ($userLogin->result() as $row)
        {
			return $row;
		}
		
		return null;
	}
}
	
?>