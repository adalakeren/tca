<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of helpers
 *
 * @author CKB
 */
class helpers extends Controller {

    function helpers(){
        parent::Controller();
        $this->load->database();
        $this->load->helper(array('url'));
        $this->load->library('session');

    }
    
    function convertDateToSimple($getDate) {        
        $dateRes = "";
        if ($getDate != "") {
            if($getDate != '1970-01-01'){
                $dateRes = date("d-m-Y", strtotime($getDate));
            }            
        }
        return $dateRes;
    }

    // here for helper database

    function _initServData_custom($database) {

        //HERE DB CONFIG
        $config['hostname'] = "10.1.99.18";
        $config['username'] = "ckbitd";
        $config['password'] = "database";
        $config['database'] = $database;
        $config['dbdriver'] = "mysql";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";

        $DBConnect = $this->load->database($config, TRUE);
        //END DB CONFIG

        return $DBConnect;
    }

    function _initServDataFast_custom($database) {

        //HERE DB CONFIG
        $config['hostname'] = "10.144.250.4"; //"CKBAZSQLY101.ckb.co.id"; //10.144.250.4
        $config['username'] = "fast2";
        $config['password'] = "aceleramiento";
        $config['database'] = $database;
        $config['dbdriver'] = "mysql";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";

        $DBConnect = $this->load->database($config, TRUE);
        //END DB CONFIG

        return $DBConnect;
    }

    //additional by alfan to add master city fromm master_param
    function initMasterCity() { 

        $fastConn = $this->_initServDataFast_custom('master_param');

        $fastConn->select('*')
                    ->from('t_city')                    
                    ->order_by('city_name','asc');
        $getDetailDeptQuery = $fastConn->get();
        
        $res = '<select style="width:130px;font-size:12px" id="selectCity" name="selectCity">';
        $res .= '<option value="">:: select City ::</option>';
        foreach ($getDetailDeptQuery->result() as $rows) {
            $res .= $rows->city_id!= "" ? '<option value="'.$rows->city_id.'">' . $rows->city_id . ' - '.$rows->city_name .'</option>' : "";
        }
        $res .= '</select>';
        $getDetailDeptQuery->free_result();

        return $res;
    }

    function initMasterDestination() { 

        $fastConn = $this->_initServDataFast_custom('master_param');

        $fastConn->select('*')
                    ->from('t_city')                    
                    ->order_by('city_name','asc');
        $getDetailDeptQuery = $fastConn->get();
        
        $res = '<select style="width:130px;font-size:12px" id="selectDestination" name="selectDestination">';
        $res .= '<option value="">:: select Destination ::</option>';
        foreach ($getDetailDeptQuery->result() as $rows) {
            $res .= $rows->city_id!= "" ? '<option value="'.$rows->city_id.'">' . $rows->city_id . ' - '.$rows->city_name .'</option>' : "";
        }
        $res .= '</select>';
        $getDetailDeptQuery->free_result();

        return $res;
    }

    function initMasterDepartment() { 

        $fastConn = $this->_initServDataFast_custom('employee');

        $getCondition = 'department_id IN ("EXM","FLT","OPS")';

        $fastConn->select('*')
                    ->from('t_department')
                    ->where($getCondition)                    
                    ->order_by('description','asc');
        $getDetailDeptQuery = $fastConn->get();
        
        $res = '<select style="width:145px;font-size:12px" id="cmbCashDept" name="cmbCashDept">';
        $res .= '<option value="">:: Select Department ::</option>';
        foreach ($getDetailDeptQuery->result() as $rows) {
            $res .= $rows->department_id!= "" ? '<option value="'.$rows->department_id.'">'.$rows->description .'</option>' : "";
        }
        $res .= '</select>';
        $getDetailDeptQuery->free_result();

        return $res;
    }

    function initMasterTrucTypeTest(){
         $serverName ="10.10.3.11";
         $usr="sa";
         $pwd="#sqls3rv3r4dm1n#";
         $db="enterprise";

        $connectionInfo = array("UID" => $usr, "PWD" => $pwd, "Database" => $db);

        $conn = sqlsrv_connect($serverName, $connectionInfo);

        if ($conn === false)
        {
            print_r( sqlsrv_errors());
        }

          $sql = "SELECT id,model_id, model_desc  FROM fms.tr_model WHERE model_id NOT IN ('MLT','L20','MIB','SDN','MBR','SCB','LTR','DDC','TRB','BAK','2AX','3AX','HLB','TLB','LTRK','TRK','PMV','BOX','DLV','PCP','CAP') ";
          $res = sqlsrv_query($conn,$sql);


          $out = '<select style="width:130px;font-size:12px" id="selectTruck" name="selectTruck" onchange="changeTruckType()">';
          $out .= '<option value="">:: select truck type ::</option>';

          while ($row = sqlsrv_fetch_array($res)) {

                $out .= '<option value="'.$row['id'].'">' . $row['model_id'] . '-' . $row['model_desc'] . '</option>' ;
          }

          sqlsrv_close( $conn); 

          $out .= '</select>';

          return $out;
    }

    function _initComboCustomConn($setCmbID, $tblName,$valueField,$valueName,$condition,$dbConnection,$nullVal = false,$nullValText='.::Select::.', $showIdValtoName = false, $overrideStyle = "", $addOtherVal = false, $setOtherVal = 0, $setOtherText = ">- Other -<" ){
        $result = "";
        
        $overrideField = "$valueField,$valueName";
        $dbConnection->select($overrideField);
        $dbConnection->from($tblName);
        if($condition != ""){
            $dbConnection->where($condition);
        }       
        //$dbConnection->order_by($valueName,'asc');
        $getQuery = $dbConnection->get();
        
        //echo $dbConnection->last_query();
        //exit;
        
        $result = "<select id=\"$setCmbID\" name=\"$setCmbID\" style=\"font-size:11px; $overrideStyle\">";
        if($nullVal){
            //$result .= "<option value=\"\">--Select--</option>";
            $result .= "<option value=\"\">$nullValText</option>";
        }
        foreach ($getQuery->result() as $rows) {
            if($showIdValtoName){
                $result .= "<option value=\"".$rows->$valueField."\" >" . "" .$rows->$valueField. " | ". $rows->$valueName. "</option>";
            }else{
                $result .= "<option value=\"".$rows->$valueField."\" >".$rows->$valueName."</option>";
            }
            
        }       
        
        if($addOtherVal){
            $result .= "<option value=\"". $setOtherVal ."\" > ".$setOtherText." </option>";
        }
        $result .= "</select>";
        
        
        return $result;
    }

    function checkExtImg($getFileName) {
        $fileImgRes = "";
        $getFileType = substr(strrchr($getFileName, '.'), 1);
        if ($getFileType == 'pdf') {
            $fileImgRes = base_url() . 'images/icons/document-pdf-text.png';
        }elseif($getFileType == 'flv' || $getFileType == 'swf'){
            $fileImgRes = base_url() . 'images/icons/blue-document-flash-movie.png';
        }elseif($getFileType == 'ppt'){
            $fileImgRes = base_url() . 'images/icons/blue-document-powerpoint.png';
        }elseif($getFileType == 'xls'||$getFileType == 'xlsx'){
            $fileImgRes = base_url() . 'images/icons/blue-document-excel.png';
        }else{
            $fileImgRes = base_url() . 'images/icons/document-broken.png';            
        }

        return $fileImgRes;
    }

    function percent($num_amount, $num_total) {
        try {
            if ($num_amount != null || $num_amount != 0 || $num_total != null || $num_total != 0 ){
                $count1 = $num_amount / $num_total;
                $count2 = $count1 * 100;
                $count = number_format($count2, 0);
                return $count;
            }else{
                return "0";
            }

        } catch (Exception $exc) {
             return "0";
        }

    }
    
    function replaceCurrency($getNominal){
        $search  = array(',', '.');
        $replace = '';        
        $result = str_replace($search, $replace, $getNominal);

        return $result;
    }
    
    function currencyFormat($getNomilal){
        $res = number_format($getNomilal ,0 , ' ', '.');
        return $res;
    }

    function replaceCurrRep_IDR($getValue){
        return number_format($getValue, 0, '', '.');
    }
        
    
    
    //==========================================================================
    
    function initCmbAllValue_cond($setID, $tblName, $fieldShow, $fieldValue, $condition) {
        
        

        // $fastConn = $this->_initServDataFast_custom('employee');
        // $fastConn->select('*')
        //             ->from('t_personel');                  
                    
        // $getDetailDeptQuery = $fastConn->get();

        // $getDeptID = "";

        // foreach ($getDetailDeptQuery->result() as $rows) {
        //     $getDeptID = $rows->department_id;
        // }


        $this->db->select('*');
        $this->db->from($tblName);
        if($condition!= ""){
            $this->db->where($condition);
        }
        
        $query = $this->db->get();

        if($setID == "cmbCashType"){
            $retValue = '<select id="' . $setID . '" name="' . $setID . '" class="cmbBox" onchange="changeRequestType()">';
            $retValue .= '<option value="">:: Select ::</option>';
        }else{
            $retValue = '<select id="' . $setID . '" name="' . $setID . '" class="cmbBox">';
        }
        //$retValue .= '<option id="0" value="0" >.::None::.</option>';
        foreach ($query->result() as $row) {
            if ($getDeptID == "FLT") {
                if ($row->$fieldValue != "trans11") {
                    $retValue .= '<option id="' . $row->$fieldValue . '" value="' . $row->$fieldValue . '" >' . $row->$fieldShow . '</option>';
                }
            }else{
                $retValue .= '<option id="' . $row->$fieldValue . '" value="' . $row->$fieldValue . '" >' . $row->$fieldShow . '</option>';
            }
            
        }
        $retValue .= '</select>';

        return $retValue;
    }


    function initCmbAllValue_cond2($setID, $tblName, $fieldShow, $fieldValue, $condition, $userId) {
        
        
        $whereArry = array('employee_id' => $userId);
        $fastConn = $this->_initServDataFast_custom('employee');
        $fastConn->select('*')
                    ->from('t_personel')
                    ->where($whereArry);                  
                    
        $getDetailDeptQuery = $fastConn->get();

        $getDeptID = "";

        foreach ($getDetailDeptQuery->result() as $rows) {
            $getDeptID = $rows->department_id;
        }


        $this->db->select('*');
        $this->db->from($tblName);
        if($condition!= ""){
            $this->db->where($condition);
            $this->db->order_by('desc_trans', 'desc');
        }
        
        $query = $this->db->get();

        if($setID == "cmbCashType"){
            // $retValue = '<select id="' . $setID . '" name="' . $setID . '" class="cmbBox" onchange="changeRequestType()">';
            $retValue = '<select id="' . $setID . '" name="' . $setID . '" class="cmbBox">';
            // $retValue .= '<option value="">:: Select Type ::</option>';
        }else{
            $retValue = '<select id="' . $setID . '" name="' . $setID . '" class="cmbBox">';
        }
        //$retValue .= '<option id="0" value="0" >.::None::.</option>';
        foreach ($query->result() as $row) {
            if ($getDeptID == "FLT") {
                if ($row->$fieldValue != "trans11") {
                    $retValue .= '<option id="' . $row->$fieldValue . '" value="' . $row->$fieldValue . '" >' . $row->$fieldShow . '</option>';
                }
            }else{
                $retValue .= '<option id="' . $row->$fieldValue . '" value="' . $row->$fieldValue . '" >' . $row->$fieldShow . '</option>';
            }
            
        }
        $retValue .= '</select>';

        return $retValue;
    }

    function initCmbDepartmentCA($setID, $tblName, $fieldShow, $fieldValue, $condition, $userId) {
        
        
        // $whereArry = array('employee_id' => $userId);
        // $fastConn = $this->_initServDataFast_custom('master_param');
        // $fastConn->select('*')
        //             ->from('t_department_ca');                  
                    
        // $getDetailDeptQuery = $fastConn->get();

        // $getDeptID = "";

        // foreach ($getDetailDeptQuery->result() as $rows) {
        //     $getDeptID = $rows->department_id;
        // }


        $this->db->select('*');
        $this->db->from($tblName);
        if($condition!= ""){
            $this->db->where($condition);
        }
        
        $query = $this->db->get();

        if($setID == "cmbCashDept"){
            $retValue = '<select id="' . $setID . '" name="' . $setID . '" class="cmbBox">';
            $retValue .= '<option value="">:: Select Type ::</option>';
        }else{
            $retValue = '<select id="' . $setID . '" name="' . $setID . '" class="cmbBox">';
        }
        //$retValue .= '<option id="0" value="0" >.::None::.</option>';
        foreach ($query->result() as $row) {
            $retValue .= '<option id="' . $row->$fieldValue . '" value="' . $row->$fieldValue . '" >' . $row->$fieldShow . '</option>';            
        }

        $retValue .= '</select>';

        return $retValue;
    }
    
    function sendMail($mailTo,$body) {
        include_once 'class.phpmailer.php';
        include_once 'class.smtp.php';
        $mail = new PHPMailer();
        //$body = "send mail from CRM";
        $mail->IsSMTP(); // telling the class to use SMTP
        //$mail->Host = "CKBAZSQLY101.ckb.co.id"; // SMTP server
		//$mail->Host = "10.1.2.15";
		$mail->Host = "10.10.3.18";
        //$mail->From = "cashadvance@ckb.co.id";
		$mail->From = "cashadvance@noreply.co.id";
        $mail->FromName = "Cash Advance Mail";
        $mail->Subject = "Cash Advance Mail";
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $mail->MsgHTML($body);
        $mail->AddAddress($mailTo);
		//$mail->AddBCC("awirawan@ckb.co.id");

        //$mail->AddAttachment("images/phpmailer.gif");             // attachment

		
        if (!$mail->Send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            //exit;
        } else {
            //echo "Message sent!";
        }
		
    }

    
    
    function generateGuid($include_braces = false) {
        if (function_exists('com_create_guid')) {
            if ($include_braces === true) {
                return com_create_guid();
            } else {
                return substr(com_create_guid(), 1, 36);
            }
        } else {
            mt_srand((double) microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));

            $guid = substr($charid,  0, 8) . '-' .
                    substr($charid,  8, 4) . '-' .
                    substr($charid, 12, 4) . '-' .
                    substr($charid, 16, 4) . '-' .
                    substr($charid, 20, 12);

            if ($include_braces) {
                $guid = '{' . $guid . '}';
            }

            return $guid;
        }
    }
    
}
?>
