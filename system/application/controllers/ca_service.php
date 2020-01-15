<?php include_once 'helpers.php'; ?>
<?php

class ca_service extends Controller {

    function ca_service() {
        parent::Controller();
        $this->load->database();             
    }
    
    function getUserDetail($getEmpId){
        //here for get user detail
        $helper = new helpers();
        
        $fastConn = $helper->_initServDataFast_custom("employee");
        $fastConn->select('*')
                ->from('t_personel a')
                ->where(array('a.employee_id' => $getEmpId));
        
        $getFastQuery = $fastConn->get();
        
        $throwResult[] = "";
        foreach ($getFastQuery->result() as $rows){
            $throwResult['fullname'] = $rows->first_name . ' ' . $rows->middle_name . ' ' .$rows->last_name;
            $throwResult['sapid'] = $rows->sap_id ;
            $throwResult['jobtitleid'] = $rows->job_title_id ;
            $throwResult['jobtitledetailid'] = $rows->job_title_detail_id ;
        }
        
        return $throwResult;
        //========================
    }
    
    function initApproval($getEmpId){
        //format result
        /*
        {
            "rows":"1",
            "datas":[{
                "guid":"BA074C03-9A0D-9C17-0F14-836A3D8E693F",
                "record":[
                    {"label":"Requester","value":"Berlian Kumala Sakti"},
                    {"label":"Date","value":"2011-10-03"},
                    {"label":"Amount","value":"700000"}
                ],
                "detaillink":{
                    "link":"cadetail",
                    "param1":"BA074C03-9A0D-9C17-0F14-836A3D8E693F"
                },
                "approvelink":{
                    "link":"caapprove",
                    "param1":"BA074C03-9A0D-9C17-0F14-836A3D8E693F",
                    "param2":"PGS"
                },
                "rejectlink":{
                    "link":"careject",
                    "param1":"BA074C03-9A0D-9C17-0F14-836A3D8E693F",
                    "param2":"PGS"
                }
            }]
        }
        */
        
        //header('Content-Type: plain/text');
        
        $getThrowData = $this->getUserDetail($getEmpId);
//        $getThrowData['jobtitledetailid'] = "PGS";
//        $getThrowData['fullname'] = "TEsT";
        
        $helpers = new helpers();
        
        /*
		$this->db->select('*')
                ->from('t_ca_trans a')
                ->join('tr_trans_approve_status b', 'a.ca_guid = b.ca_guid and b.appr_status = 0 and b.approver = SUBSTRING(a.appv_flow_status, -4)' , 'left')
                ->where_not_in('a.appv_complete', array(1,2,5,6))
                ->where('SUBSTRING(a.appv_flow_status, -3) = SUBSTRING(\''. $getThrowData['jobtitledetailid'].'\',-3)')
                ;
		*/
	$this->db->select('*, a.id_ca_trans as id_ca_transcode, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('tr_trans_approve_status b', 'a.ca_guid = b.ca_guid and b.appr_status = 0 and b.approver = \''. $getThrowData['jobtitledetailid'].'\'' , 'left')
                //->where_not_in('a.appv_complete', array(1,2,5,6))
				->where_in('a.appv_complete', array(0,3))
                ->where('(SUBSTRING_INDEX(a.appv_flow_status, \',\',-1) = \''. $getThrowData['jobtitledetailid'].'\' OR SUBSTRING_INDEX(a.appv_flow_settle_status, \',\',-1) =\''. $getThrowData['jobtitledetailid'].'\' )')
                ->where('b.apprv_id IS NOT NULL')
				->where('a.isdelete = 0')
                ;
        $getQuery = $this->db->get();
		
	//echo $this->db->last_query();
        $getCount = $getQuery->num_rows();
       
        $jsonResDetail = "";
        foreach($getQuery->result() as $rows){
            
            //handler for settlement action or normal action
            $approvalType = "TCA";
            $approvalTypeLink = "caApproval";               
            if($rows->appv_complete == 3){
                $approvalType = "Settlement TCA";
                $approvalTypeLink = "caSetlApproval";
            }
            //==================================================================
            
            $setTCAno = '' . $rows->id_ca_transcode . '-TCA-' . $rows->bulan . $rows->tahun . '';
            
            $jsonResDetail .= '{ 
                            "guid" : "'.$rows->ca_guid.'"
                            ,"record":[
                                        {"label":"TCA","value":"'.$setTCAno.'"},
                                        {"label":"Requester","value":"'.$rows->requester_name.'"},
                                        {"label":"Date","value":"'.$rows->date_request.'"},
                                        {"label":"Amount","value":" Rp.'. $helpers->currencyFormat($rows->request_amount).'"},
                                        {"label":"Settle Amount","value":" Rp.'.$helpers->currencyFormat($rows->settled_amount).'"},
                                        {"label":"Purpose","value":"'.$rows->purpose.'"},
                                        {"label":"Approval Type","value":"'.$approvalType.'"}
                                       ]                            
                            , "detaillink" : {"link" : "cadetail" , "param1" : "'.$rows->ca_guid.'"}
				, "approvelink" : {"link" : "'.$approvalTypeLink.'/approve/'.$getEmpId.'/-/'.$rows->apprv_id.'/-"}
				, "rejectlink" : {"link" : "'.$approvalTypeLink.'/reject/'.$getEmpId.'/-/'.$rows->apprv_id.'/"}
                            }';  
            $jsonResDetail .= ',';
        }
        $jsonRes = '{"rows" : "'.$getCount.'" , "datas" : [' . substr($jsonResDetail, 0, strlen($jsonResDetail)-1) .']}';
        
        echo trim($jsonRes);
    }
    
    function caApproval($getType,$getSessUser,$getSessUserFull,$getId,$getReason){
        
		$getUserDetail = $this->getUserDetail($getSessUser);
		
        switch ($getType) {
            case "approve":
                $data = array(                    
                    'appr_status' => 1,            
                    'appr_userid' => $getSessUser,            
                    'appr_name' => $getUserDetail['fullname'],            
                    'appr_date' => date("Y-m-d H:i:s"),            
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );
				
                $whereArray = array('apprv_id'=> $getId,'approve_type' => 'STD');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);
                
                
                //here next approval
                //select first
                $whereArry = array('a.apprv_id' => $getId,'a.approve_type' => 'STD');
                $this->db->select('a.apprv_id, b.*')
                            ->from('tr_trans_approve_status a')
                            ->join('t_ca_trans b','a.ca_guid = b.ca_guid', 'left')                            
                            ->where($whereArry)
                            ->order_by('input_datetime','desc');

                $getQuery = $this->db->get();
                foreach($getQuery->result() as $rows){
                    $getGuid = $rows->ca_guid;
                    $getAppFlow = $rows->appv_flow;
                    $getAppFlowStatus = $rows->appv_flow_status;
                }
                
                $getAppFlowArray = explode(",",$getAppFlow);
                $getAppFlowStatusArray = explode(",",$getAppFlowStatus);   
//                echo count($getAppFlowArray);
//                echo '<br/>'.count($getAppFlowStatusArray);
                //============
                if(count($getAppFlowArray) != count($getAppFlowStatusArray)){
                    $setNewAppFlowStatus = $getAppFlowStatus.",".$getAppFlowArray[count($getAppFlowStatusArray)];
                    $data = array(                                                                          
                        'appv_flow_status' => $setNewAppFlowStatus                                
                    );
//                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "STD");
                }else{
                    $setNewAppFlowStatus = $getAppFlowStatus;
//                    echo $setNewAppFlowStatus;
//                    echo $getAppFlowStatus;
                    
                    $data = array(                    
                        'appv_complete' => 1,                                
                        'appv_flow_status' => $setNewAppFlowStatus. ',END',                                
                    );
                    
//                    $getEmailTo = $this->getRequesterMail($getGuid);                    
//                    $this->sendEmailRemiderApproval($getEmailTo, $getGuid, 'APV');
                }
                $this->db->where('ca_guid', $getGuid);
                $execData = $this->db->update('t_ca_trans', $data);
		  if($execData){echo '{"success": true}';}else{echo '{"success": false}';}
                //==================
               
//                //send email
//                if(count($getAppFlowArray) != count($getAppFlowStatusArray)){                    
//                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "STD");
//                }else{                
//                    $getEmailTo = $this->getRequesterMail($getGuid);                    
//                    $this->sendEmailRemiderApproval($getEmailTo, $getGuid, 'APV');
//                }
//                //==================
                break;
                 
                
            case "reject":
                
                $data = array(                    
                    'appr_status' => 2,            
                    'reject_userid' => $getSessUser,            
                    'reject_name' => $getUserDetail['fullname'],            
                    'reject_note' => $getReason,            
                    'reject_date' => date("Y-m-d H:i:s"),            
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );

                $whereArray = array('apprv_id'=> $getId,'approve_type' => 'STD');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);

                if($execData){      
                    //here get GUID first
                    $whereArry = array('a.apprv_id' => $getId,'a.approve_type' => 'STD');
                    $getQueryGuid = $this->db->select('a.*')->from('tr_trans_approve_status a')->where($whereArry)->get();
                    foreach($getQueryGuid->result() as $rows){
                        $getGuid = $rows->ca_guid;
                    }
                    //===================
                    $data = array(
                        'appv_complete' => 2                        
                    );
                    $this->db->where('ca_guid', $getGuid);
                    $execData = $this->db->update('t_ca_trans', $data);  

                    if($execData){echo '{"success": true}';}else{echo '{"success": false}';}

                    //$getEmailTo = $this->getRequesterMail($getGuid);                    
                    //$this->sendEmailRemiderApproval($getEmailTo, $getGuid, 'REJ');
                    
                }
                break;

        }
    }
    
    function caSetlApproval($getType,$getSessUser,$getSessUserFull,$getId,$getReason=""){
//        $getType = $_POST['setType'];
//        $getId = $_POST['setId'];
//        $getReason = $_POST['setReason'];
//        
//        $getSessUser = $this->session->userdata('userName');   
//        $getSessUserFull = $this->session->userdata('userNameFull');   
//        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');   
        
		$getUserDetail = $this->getUserDetail($getSessUser);
		
        switch ($getType) {
            case "approve":
                $data = array(                    
                    'appr_status' => 1,            
                    'appr_userid' => $getSessUser,            
                    'appr_name' => $getUserDetail['fullname'],            
                    'appr_date' => date("Y-m-d H:i:s"),            
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );

                $whereArray = array('apprv_id'=> $getId,'approve_type' => 'SET');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);
                
                
                //here next approval
                //select first
                $whereArry = array('a.apprv_id' => $getId,'a.approve_type' => 'SET');
                $this->db->select('a.apprv_id, b.*')
                            ->from('tr_trans_approve_status a')
                            ->join('t_ca_trans b','a.ca_guid = b.ca_guid', 'left')                            
                            ->where($whereArry)
                            ->order_by('input_datetime','desc');

                $getQuery = $this->db->get();
                foreach($getQuery->result() as $rows){
                    $getGuid = $rows->ca_guid;
                    $getAppFlow = $rows->appv_flow_settle;
                    $getAppFlowStatus = $rows->appv_flow_settle_status;
                }
                
                $getAppFlowArray = explode(",",$getAppFlow);
                $getAppFlowStatusArray = explode(",",$getAppFlowStatus);   
//                echo count($getAppFlowArray);
//                echo '<br/>'.count($getAppFlowStatusArray);
                //============
                if(count($getAppFlowArray) != count($getAppFlowStatusArray)){
                    $setNewAppFlowStatus = $getAppFlowStatus.",".$getAppFlowArray[count($getAppFlowStatusArray)];
                    $data = array(                                                                                                                               
                        'appv_flow_settle_status' => $setNewAppFlowStatus                                
                    );
//                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "SET");
                }else{
                    $setNewAppFlowStatus = $getAppFlowStatus;
                    $data = array(                    
                        'appv_settle_complete' => 1,                                
                        'appv_complete' => 5,                                
                        'appv_flow_settle' => $setNewAppFlowStatus,                                
                    );
//                    $getEmailTo = $this->getRequesterMail($getGuid);
//                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid,'APV');
                }
                $this->db->where('ca_guid', $getGuid);
                $execData = $this->db->update('t_ca_trans', $data);
                
//                //send email                
//                if(count($getAppFlowArray) != count($getAppFlowStatusArray)){                    
//                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "SET");
//                }else{    
//                    $getEmailTo = $this->getRequesterMail($getGuid);
//                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid,'APV');
//                }
//                //==================
				if($execData){echo '{"success": true}';}else{echo '{"success": false}';}
                break;
                
            case "reject":
                
                $data = array(                    
                    'appr_status' => 2,
                    'reject_userid' => $getSessUser,            
                    'reject_name' => $getUserDetail['fullname'],            
                    'reject_note' => $getReason,            
                    'reject_date' => date("Y-m-d H:i:s"),            
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );

                $whereArray = array('apprv_id'=> $getId,'approve_type' => 'SET');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);
                
                if($execData){      
                    //here get GUID first
                    $whereArry = array('a.apprv_id' => $getId,'a.approve_type' => 'SET');
                    $getQueryGuid = $this->db->select('a.*')->from('tr_trans_approve_status a')->where($whereArry)->get();
                    foreach($getQueryGuid->result() as $rows){
                        $getGuid = $rows->ca_guid;
                    }
                    //===================
                    $data = array(
                        'appv_settle_complete' => 2,
                        'appv_complete' => 4                        
                    );
                    $this->db->where('ca_guid', $getGuid);
                    $execData = $this->db->update('t_ca_trans', $data); 
                    
                    $getEmailTo = $this->getRequesterMail($getGuid);
                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid,'REJ');
                }
				if($execData){echo '{"success": true}';}else{echo '{"success": false}';}
				
                break;
        }
    }
    
    
    //ajax manager source ============================================================
    function sendEmailRemider(){
        $helpers = new helpers();    
        
        $setDate = '';
        $setFrom = '';
        $setType = '';
        $setAmount = '';
        $setSettleAmount = '';
        $setPurpose = '';

        //here get query 
        $getCaId = $_POST['caid'];
        $whereArry = array('a.id_ca_trans'=>$getCaId);       
        $this->db->select('a.*, b.desc_trans')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->where($whereArry);
        $getQuery = $this->db->get();
        
        foreach($getQuery->result() as $rows){
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;
        }
        //==============
        
        $setBody = '<table style="font-family:verdana">
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    <b>New Reminder Message From Cash Advance System</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">'.$setDate.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">'.$setFrom.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">'.$setType.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">'.$setAmount.' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Settle Amount :</td>
                                <td style="">'.$setSettleAmount.' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">'.$setPurpose.'</td>
                            </tr>                            
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Is Waiting for your Approval</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Thank You</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    Please Access <a href="'.base_url().'" >Cash Advance System</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center;font-size:10px">
                                    *do not reply this email
                                </td>
                            </tr>
                        </table>';
        
//        echo $setBody;
        $getEmailTo = $_POST['emailto'];   
//        $getEmailTo = "awirawan@ckb.co.id";        
        $helpers->sendMail($getEmailTo, $setBody);
    }
    
    function sendEmailRemiderSettle(){
        $helpers = new helpers();    
        
        $setDate = '';
        $setFrom = '';
        $setType = '';
        $setAmount = '';
        $setSettleAmount = '';
        $setPurpose = '';

        
        $getEmailTo = $_POST['emailto'];
        $getCaId = $_POST['caid'];
//        $getEmailTo = "awirawan@ckb.co.id";    
        //here get query 
        
        
        $whereArry = array('a.id_ca_trans'=>$getCaId);       
        $this->db->select('a.*, b.desc_trans')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->where($whereArry);
        $getQuery = $this->db->get();
        
        foreach($getQuery->result() as $rows){
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;
            $getStartVal = $rows->request_amount;
            $getSettleVal = $rows->settled_amount;
        }
        //==============
        
            
        $dearMail = "Dear <b>TCA Requester</b>,";
        $setMessage = "<span style=\"color:red;background:red\">Must Returned</span>";     
        $setSettleVal = intval($getStartVal)-intval($getSettleVal);
        if(intval($getSettleVal) > intval($getStartVal)){
            $setMessage = "<span style=\"color:green;\">Need more Cash</span>";  
            $getMailADM = $this->getAdminMail('FINADM');              
            $getEmailTo = $getMailADM.','.$getEmailTo;
            $dearMail = "Dear <b>Finance Admin</b>,";
        }
        
//        echo $setBody;

        
        
        $setBody = $dearMail.'
                    <table style="font-family:verdana">
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    Reminder '. $rows->settle_mail_counter.'<br/><br/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    <b>New Reminder Message From Cash Advance System</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;height:50px">
                                    <b style="color:red">Settlement Required</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">'.$setDate.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">'.$setFrom.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">'.$setType.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">'.$setAmount.' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Settle Amount :</td>
                                <td style="">'.$setSettleAmount.' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right"> <b>'.$setMessage.' </b>:</td>
                                <td style="">'.$helpers->currencyFormat($setSettleVal).' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">'.$setPurpose.'</td>
                            </tr>                                                        
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Thank You</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    Please Access <a href="'.base_url().'" >Cash Advance System</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center;font-size:10px">
                                    *do not reply this email
                                </td>
                            </tr>
                        </table>';
        

        $helpers->sendMail($getEmailTo, $setBody);
        
        $strUpdateCounter = "UPDATE t_ca_trans SET settle_mail_counter = settle_mail_counter+1 WHERE id_ca_trans=".$getCaId;
        $execData = $this->db->query($strUpdateCounter);
    }
    
    function sendEmailRemiderSettle2($getEmailTo,$getCaGuid,$getType){
        $helpers = new helpers();    
        
        $setDate = '';
        $setFrom = '';
        $setType = '';
        $setAmount = '';
        $setSettleAmount = '';
        $setPurpose = '';

        $whereArry = array('a.ca_guid'=>$getCaGuid);       
        $this->db->select('a.*, b.desc_trans')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->where($whereArry);
        $getQuery = $this->db->get();
        
        foreach($getQuery->result() as $rows){
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;
            $getStartVal = $rows->request_amount;
            $getSettleVal = $rows->settled_amount;
        }
        //==============
        
        $setEmailStatus = "TCA Approved";
        if($getType=="REJ"){
            $setEmailStatus = "TCA Rejected";
        }
            
        $dearMail = "Dear <b>TCA Requester</b>,";
        $setMessage = "<span style=\"color:red;background:red\">Must Returned</span>";     
        $setSettleVal = intval($getStartVal)-intval($getSettleVal);
        if(intval($getSettleVal) > intval($getStartVal)){
            $setMessage = "<span style=\"color:green;\">Need more Cash</span>";  
            $getMailADM = $this->getAdminMail('FINADM');              
            $getEmailTo = $getMailADM.','.$getEmailTo;
            $dearMail = "Dear <b>Finance Admin</b>,";
        }
        
//        echo $setBody;

        
        
        $setBody = $dearMail.'
                    <table style="font-family:verdana">
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    Reminder '. $rows->settle_mail_counter.'<br/><br/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    <b>New Reminder Message From Cash Advance System</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;height:50px">
                                    <b style="color:red">'.$setEmailStatus.'</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">'.$setDate.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">'.$setFrom.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">'.$setType.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">'.$setAmount.' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Settle Amount :</td>
                                <td style="">'.$setSettleAmount.' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right"> <b>'.$setMessage.' </b>:</td>
                                <td style="">'.$helpers->currencyFormat($setSettleVal).' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">'.$setPurpose.'</td>
                            </tr>                                                        
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Thank You</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    Please Access <a href="'.base_url().'" >Cash Advance System</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center;font-size:10px">
                                    *do not reply this email
                                </td>
                            </tr>
                        </table>';
        

        $helpers->sendMail($getEmailTo, $setBody);
        
        $strUpdateCounter = "UPDATE t_ca_trans SET settle_mail_counter = settle_mail_counter+1 WHERE ca_guid='$getCaGuid'";
        $execData = $this->db->query($strUpdateCounter);
    }
    
    function getAdminMail($getType){
        $getUserEmail = '';
        $whereArry = array('user_status'=>$getType);
        $getDetailQuery = $this->db->select('*')->from('t_user_admin')->where($whereArry)->get();
        
        foreach($getDetailQuery->result() as $getRows2){
            $getUserEmail = $getRows2->user_email;
        }
        
        return $getUserEmail;
    }
   
    function getRequesterMail($getGuid){
        $whereArr = array('a.ca_guid'=>$getGuid);
        $this->db->select('*')
                ->from('t_ca_trans a')
                ->where($whereArr);
        
        $getUserQuery = $this->db->get();        
        foreach($getUserQuery->result() as $rows){
            $getuserSn = $rows->requester_sn;
        }
        
        //here get email from personeel
        $helper = new helpers();
        $connHera = $helper->_initServDataFast_custom('employee');
        $whereArr = array('a.employee_id'=>$getuserSn);
        $connHera->select('*')
                ->from('t_personel a')
                ->where($whereArr);
        $getMailQuery = $connHera->get();
        
        foreach($getMailQuery->result() as $rows2){
            $getMail = $rows2->email;
        }
        
        return $getMail;
        
    }
    
    function approveSettleFin(){
        $getEmailConfirm = $_POST['emailto'];
        $getCaId = $_POST['caid'];
        
        $getSessUser = $this->session->userdata('userName');  
        
        $data = array(  
            'appv_complete' => 6,
            'fin_settle' => 1,                                    
            'fin_settle_user' => $getSessUser,
            'fin_settle_datetime' => date("Y-m-d H:i:s")
        );

        $whereArray = array('id_ca_trans'=> $getCaId);
        $this->db->where($whereArray);
        $execData = $this->db->update('t_ca_trans', $data);
//        
//        echo $this->db->last_query();
    }
    
    function sendEmailToNextAppv($getID,$getCaGuid,$getType){
        
        $setWhere = array('a.job_title_detail'=>$getID);
        $this->db->select('a.*,b.email')
                ->from('t_direct_report a')
                ->join('t_personel b', 'b.employee_id=a.employee_id','inner')
                ->where($setWhere);
        
        $getQuery = $this->db->get();
        $getEmail = "";
        foreach($getQuery->result() as $row){
            $getEmail = $row->email;
        }
        
        
        //here email core ======================================================
        $helpers = new helpers();    
        
        $setDate = '';
        $setFrom = '';
        $setType = '';
        $setAmount = '';
        $setSettleAmount = '';
        $setPurpose = '';

        //here get query 
        //$getCaId = $_POST['caid'];
        $whereArry = array('a.ca_guid'=>$getCaGuid);       
        $this->db->select('a.*, b.desc_trans')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->where($whereArry);
        $getQuery = $this->db->get();
        
        foreach($getQuery->result() as $rows){
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;
        }
        //==============
        $setHeader = "New Cash Advance";
        if($getType == "SET"){
            $setHeader = "New Settlement Approval";
        }
        
        $setBody = '<table style="font-family:verdana">
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    <b>New Reminder Message From Cash Advance System</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;color:red">
                                    <b>'.$setHeader.'</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">'.$setDate.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">'.$setFrom.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">'.$setType.'</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">'.$setAmount.' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Settle Amount :</td>
                                <td style="">'.$setSettleAmount.' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">'.$setPurpose.'</td>
                            </tr>                            
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Is Waiting for your Approval</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Thank You</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    Please Access <a href="'.base_url().'" >Cash Advance System</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center;font-size:10px">
                                    *do not reply this email
                                </td>
                            </tr>
                        </table>';
        
//        echo $setBody;
        $getEmailTo = $getEmail;   
//        $getEmailTo = "awirawan@ckb.co.id";        
        $helpers->sendMail($getEmailTo, $setBody);
        //=====================================================================
        
    }
    
    //==========================================================================
    
    //this prototype for call service (JSON Result)
    function callService(){
        //$fp = fopen("http://10.30.20.19/cashadvance/index.php/ca_service/initApproval", "r");
        $fp = file_get_contents("http://10.30.20.19/cashadvance/index.php/ca_service/initApproval/a/a", "r");
        $jsonRes = json_decode($fp);
        print_r($jsonRes);
        echo $jsonRes->datas[0]->record[1]->value;
        //echo $fp;
    }
    //====================================
    
}
?>