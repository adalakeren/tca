<?php include_once 'helpers.php'; ?>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ajax_manager
 *
 * @author CKB
 */
class ajax_manager extends Controller {

    function ajax_manager() {
        parent::Controller();
        $this->load->database();
        $this->load->helper(array('url'));
        $this->load->library('session');
    }


    function initLastApproval($getReqType, $getAmount, $isSuj = 0, $getSender = "VIEW") {
         
        $getAmount = str_replace(".", "", $getAmount);
        $getRes = "";
        $whereArry = array('a.id_trans' => $getReqType);
        $getValueQuery = $this->db->select('*')->from('t_nilai_transaksi a')->where($whereArry)->order_by('jml_nilai', 'asc')->get();

        // print_r($this->db);die();
        $getNilTransID = "";
        $getLastApprove = "";
        $getLastApproveName = "";
        $getLastApprovePay = "";
        $getLastApprovePayName = "";
        foreach ($getValueQuery->result() as $getRow) {
            switch ($getRow->equality_operator) {

                case "<=":
                    if (intval($getAmount) <= intval($getRow->jml_nilai)) {
                        //echo $getRow->equality_operator.$getRow->jml_nilai;
                        $getLastApprove = $getRow->last_lvl_apprv;
                        if ($isSuj == 1) {
                            $getLastApproveName = "SPV";                                                    # code...
                        }else{
                            $getLastApproveName = $getRow->last_lvl_apprv_name;    
                        }
                        $getLastApprovePay = $getRow->last_lvl_apv_payment;
                        if ($isSuj == 1) {
                            $getLastApprovePayName = "Not Required";
                        }else{
                            $getLastApprovePayName = $getRow->last_lvl_apv_payment_name;
                        }
                        $getNilTransID = $getRow->id_nilai_trans;
                    }
                    break;
                case ">":
                    if (intval($getAmount) > intval($getRow->jml_nilai)) {
                        //echo $getRow->equality_operator.$getRow->jml_nilai;
                        $getLastApprove = $getRow->last_lvl_apprv;
                        if ($isSuj == 1) {
                            $getLastApproveName = "SPV";                                                    # code...
                        }else{
                            $getLastApproveName = $getRow->last_lvl_apprv_name;
                        }   
                        $getLastApprovePay = $getRow->last_lvl_apv_payment;
                        if ($isSuj == 1) {
                            $getLastApprovePayName = "Not Required";
                        }else{
                            $getLastApprovePayName = $getRow->last_lvl_apv_payment_name;
                        }
                        $getNilTransID = $getRow->id_nilai_trans;
                    }
                    break;
                default:
                    echo "0";
                    break;
            }
            if ($getNilTransID != "") {
                break;
            }
        }
        if ($getLastApprovePayName == "") {
            $getLastApprovePayName = "Not Required";
        }
        // die($getLastApprovePayName);
        $getRes = "Last Approve Required = <b><i>" . $getLastApproveName . "</i></b> &nbsp;*<i style=\"color:red\">Will Escalate if doesn't exist</i>
                    <br/>Last Payment Approval = <b><i>" . $getLastApprovePayName . "</i></b> &nbsp;*<i style=\"color:red\">Will Escalate if doesn't exist</i>";
        // $getRes .= "<input type=\"hidden\" name=\"hidApprove\" id=\"hidApprove\" value=\"" . $getLastApprove . "\" />";
        // $getRes .= "<input type=\"hidden\" name=\"hidApprovePay\" id=\"hidApprovePay\" value=\"" . $getLastApprovePay . "\" />";
        if ($isSuj == 1) {
            $getLastApprove = "SPV";
            $getLastApprovePay = "SPV";
            $getRes .= "<input type=\"hidden\" name=\"hidApprove\" id=\"hidApprove\" value=\"" . $getLastApprove . "\" />";
            $getRes .= "<input type=\"hidden\" name=\"hidApprovePay\" id=\"hidApprovePay\" value=\"" . $getLastApprovePay . "\" />"; 
        }else{
            $getRes .= "<input type=\"hidden\" name=\"hidApprove\" id=\"hidApprove\" value=\"" . $getLastApprove . "\" />";
            $getRes .= "<input type=\"hidden\" name=\"hidApprovePay\" id=\"hidApprovePay\" value=\"" . $getLastApprovePay . "\" />";
        }

        if($getSender == "CONTROLLER"){
            $throwData['hidApprove'] =  $getLastApprove;
            $throwData['hidApprovePay'] =  $getLastApprovePay;

            return $throwData;
        }else{
            echo $getRes;
        }
        
    }

    //additional by Alfan to get direct to report id
    function getDirectReportToId($employeeId){

        $res = "";
        //echo $getCurrLevID;
        $whereArry = array('a.employee_id' => $employeeId);
        $getReportToQuery = $this->db->select('a.direct_report_to_id')
                        ->from('t_direct_report a')                        
                        ->where($whereArry)->get();
        // print_r($this->db);die();
        foreach ($getReportToQuery->result() as $nextRow) {
            $res = $nextRow->direct_report_to_id;  //here for ID
        }
        return $res;
    }
    //=======================================================

    //additional by yudhis to get direct to report id
    function getDirectReportToId2($getLastApproval){

        $res = "";
        //echo $getCurrLevID;
        $whereArry = array('a.job_title_detail' => $getLastApproval);
        $getReportToQuery = $this->db->select('a.direct_report_to_id')
                        ->from('t_direct_report a')                        
                        ->where($whereArry)->get();
        // print_r($this->db);die();
        foreach ($getReportToQuery->result() as $nextRow) {
            $res = $nextRow->direct_report_to_id;  //here for ID
        }
        return $res;
    }
    //=======================================================

    //additional by yudhis to get direct to report id
    function getDirectLevelId($getLastApproval){

        $res = "";
        //echo $getCurrLevID;
        $whereArry = array('a.job_title_detail' => $getLastApproval);
        $getReportToQuery = $this->db->select('a.level_id')
                        ->from('t_direct_report a')                        
                        ->where($whereArry)->get();
        // print_r($this->db);die();
        foreach ($getReportToQuery->result() as $nextRow) {
            $res = $nextRow->level_id;  //here for ID
        }
        return $res;
    }
    //=======================================================

    //aditional by Alfan to get master tr_cost_type_rate
    function getMasterCostTypeRate_OLD($origin, $dest, $truckType){

        // $this->db->from('tr_cost_type_rate');
        // $this->db->where('origin',$origin);
        // $this->db->where('destination',$dest);
        // $this->db->where('truck_type',$truckType);
        // $query = $this->db->get()->row(1);

        // $whereArry = array('origin' => $origin, 'destination' => $dest, 'truck_type' => $truckType);
        // $query = $this->db->select('*')
        //             ->from('tr_cost_type_rate')
        //             ->where($whereArry)
        //             ->get()
        //             ->result()
        //             ->row(1);
            //print_r('hasil query ' .$query)

        $res = 'select id_cost_type_rate, origin, destination, truck_type, amount, expired, DATEDIFF(expired, CURDATE()) as Days from tr_cost_type_rate ';
        $res .=  'WHERE origin = "' .$origin. '"' ;
        $res .=  ' and  destination = "' .$dest. '"' ;
        $res .= ' and truck_type = ' .$truckType.  ' order by expired desc limit 1';

        //print_r($res);

        $query = $this->db->query($res);        
        echo json_encode($query->result());

        //echo json_encode($query);
    }

    function getMasterCostTypeRate($param){

        
        $res  = "select l.* ";
        $res .= "from tr_cost_type_rates l ";
        $res .= "inner join ( ";
        $res .= "select ";
        $res .= "suj_id, max(expired) as latest ";
        $res .= "from tr_cost_type_rates ";
        $res .= "where fleet_schedule like '%" .$param  ."%' ";
        $res .= " group by suj_id ";
        $res .= ") r ";
        $res .= " on l.expired = r.latest and l.suj_id = r.suj_id ";
        $res .= " order by expired desc ";

        $query = $this->db->query($res);                
        echo json_encode($query->result());
    }
    
    function getTrCostTypeRate($id){
        $area_id = $this->input->post('areaid');
        $area_id = strip_tags($area_id, '<p>');
        $res = "select * from tr_cost_type_rates where suj_id=$id AND expired >= curdate() AND area_id = '$area_id'";
        
        $query = $this->db->query($res);
        echo json_encode($query->result());
    }
    
    function getTrCostTypeRateNoExpired($id){
        $area_id = $this->input->post('areaid');
        $area_id = strip_tags($area_id, '<p>');
        $res = "select * from tr_cost_type_rates where suj_id=$id AND area_id = '$area_id'";
        
        $query = $this->db->query($res);
        echo json_encode($query->result());
    }
    
    function GetSUJ()
    {
        session_start();
        $suj_id = $this->input->post('suj_id');
        $truck_type = $this->input->post('truck_type');
        $area_id = $this->input->post('area_id');
        
        $res = "select * from tr_cost_type_rates where suj_id=$suj_id AND " . 
            "truck_type='$truck_type' AND expired >= curdate() AND area_id = '".$area_id."'";
        //echo $res;
        $query = $this->db->query($res);
        echo json_encode($query->result());
    }

    function getTotalCostFromMaster($id){
        
         $res = 'select ratesid,suj_id,fleet_schedule, truck_type, MAX(expired ), distance, rationbbm, total_cost, DATEDIFF(expired, CURDATE()) as Days from tr_cost_type_rates ';
        $res .=  'WHERE ratesid = ' .$id;

        $query = $this->db->query($res);                
        echo json_encode($query->result());

    }

    function initUserApproval($getUserLevID, $getLastApproval,$isSuj = 0, $getSender = "VIEW") {
        //here inisiate first what level for las approval
        $lastLevelApproval = $this->initApprovalLevelNumber($getLastApproval);
        //echo $lastLevelApproval .'<br/><br/>';
        //======================================================================
        //here to get count all level(for iterator)
        $getItCount = $this->db->select('*')->from('t_type_otorisator')->get()->num_rows();
        //======================================================================

        $setAppvRes = "<b>You</b> -> ";
        $getCurrLevelSeq = 0;
        $getNextAppv = $getUserLevID;
        $tmpAppFlow = "";
        
        $getNextAppvArry = $this->initUserApprovalSub($getNextAppv, $getLastApproval, $getCurrLevelSeq);
        // print_r($getNextAppvArry);die();
        
        $getNextAppv = $getNextAppvArry[0];
        $setAppvRes .= $getNextAppvArry[1] . "(" . $getNextAppvArry[0] . ")" . " -> ";
        $getCurrLevelSeq = $getNextAppvArry[2];
        $tmpAppFlow .= $getNextAppvArry[0] . ",";
        
        if ($isSuj != 1) {
            for ($setIterator = 0; $setIterator <= $getItCount; $setIterator++) {
             if ($getNextAppv != $getLastApproval) {
                 if ($getCurrLevelSeq > $lastLevelApproval) {
                    // // echo $getCurrLevelSeq.' '.$lastLevelApproval.'|';
                     $getNextAppvArry = $this->initUserApprovalSub($getNextAppv, $getLastApproval, $getCurrLevelSeq);
                     $getNextAppv = $getNextAppvArry[0];
                     $setAppvRes .= $getNextAppvArry[1] . "(" . $getNextAppvArry[0] . ")" . " -> ";
                     $getCurrLevelSeq = $getNextAppvArry[2];
                     $tmpAppFlow .= $getNextAppvArry[0] . ",";
                 } else {
                     break;
                 }
             } else {
                 break;
             }
         }
        }
         
        $setAppvRes .= "<b>Done</b>";
        $setAppvRes .= '<input type="hidden" name="hidApvFlow" id="hidApvFlow" value="' . $tmpAppFlow . '"/>';
        
        if($getSender == "CONTROLLER"){
            $throwData['hidApvFlow'] =  $tmpAppFlow;
            return $throwData;
        }else{
            echo $setAppvRes;
        }
        
    }

    function initUserApprovalSub($getCurrLevID, $getLastApproval, $getCurrLevelSeq) {

        $res = array();
        //echo $getCurrLevID;
        $whereArry = array('a.job_title_detail' => $getCurrLevID);
        $getReportToQuery = $this->db->select('a.*, b.name nameto,c.otor_seq')
                        ->from('t_direct_report a')
                        ->join('t_direct_report b', 'a.direct_report_to_id = b.job_title_detail', 'left')
                        ->join('t_type_otorisator c', 'b.level_id = c.id_type_otorisator', 'left')
                        ->where($whereArry)->get();
        // print_r($this->db);die();      
        foreach ($getReportToQuery->result() as $nextRow) {
            $res[0] = $nextRow->direct_report_to_id;  //here for ID
            $res[1] = $nextRow->nameto;    //Here for name
            $res[2] = $nextRow->otor_seq;    //Here get Current Level ID
            $res[3] = $nextRow->level_id;    //Here get Current Level ID
        }

//      echo $this->db->last_query();

        return $res;
    }

    function initApprovalLevelNumber($getLevel) {
        $res = "";
        $whereArry = array('a.id_type_otorisator' => $getLevel);
        $getLevelQuery = $this->db->select('a.*')
                        ->from('t_type_otorisator a')
                        ->where($whereArry)->get();
        foreach ($getLevelQuery->result() as $lvlRow) {
            $res = $lvlRow->otor_seq;
        }

        return $res;
    }

    function approval() {
        $getType = $_POST['setType'];
        $getId = $_POST['setId'];
        $getReason = $_POST['setReason'];

        $getSessUser = $this->session->userdata('userName');
        $getSessUserFull = $this->session->userdata('userNameFull');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');

        switch ($getType) {
            case "approve":
                $data = array(
                    'appr_status' => 1,
                    'appr_userid' => $getSessUser,
                    'appr_name' => $getSessUserFull,
                    'appr_date' => date("Y-m-d H:i:s"),
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );

                $whereArray = array('apprv_id' => $getId, 'approve_type' => 'STD');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);


                //here next approval
                //select first
                $whereArry = array('a.apprv_id' => $getId, 'a.approve_type' => 'STD');
                $this->db->select('a.apprv_id, b.*')
                        ->from('tr_trans_approve_status a')
                        ->join('t_ca_trans b', 'a.ca_guid = b.ca_guid', 'left')
                        ->where($whereArry)
                        ->order_by('input_datetime', 'desc');

                $getQuery = $this->db->get();
                foreach ($getQuery->result() as $rows) {
                    $getGuid = $rows->ca_guid;
                    $getAppFlow = $rows->appv_flow;
                    $getAppFlowStatus = $rows->appv_flow_status;

                    

                }

                $getAppFlowArray = explode(",", $getAppFlow);
                $getAppFlowStatusArray = explode(",", $getAppFlowStatus);
//                echo count($getAppFlowArray);
//                echo '<br/>'.count($getAppFlowStatusArray);
                //============
                if (count($getAppFlowArray) != count($getAppFlowStatusArray)) {
                    $setNewAppFlowStatus = $getAppFlowStatus . "," . $getAppFlowArray[count($getAppFlowStatusArray)];
                    $data = array(
                        'appv_flow_status' => $setNewAppFlowStatus
                    );
//                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "STD");
                } else {
                    $setNewAppFlowStatus = $getAppFlowStatus;
                    echo $setNewAppFlowStatus;
                    echo $getAppFlowStatus;

                    $data = array(
                        'appv_complete' => 1,
                        'appv_flow_status' => $setNewAppFlowStatus. ',END',
                    );

//                    $getEmailTo = $this->getRequesterMail($getGuid);                    
//                    $this->sendEmailRemiderApproval($getEmailTo, $getGuid, 'APV');
                }
                $this->db->where('ca_guid', $getGuid);
                $execData = $this->db->update('t_ca_trans', $data);
                //==================
                //send email
                if (count($getAppFlowArray) != count($getAppFlowStatusArray)) {
                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "STD");
                } else {
                    $getEmailTo = $this->getRequesterMail($getGuid);
                    $this->sendEmailRemiderApproval($getEmailTo, $getGuid, 'APV');
                }
                //==================
                break;


            case "reject":

                $data = array(
                    'appr_status' => 2,
                    'reject_userid' => $getSessUser,
                    'reject_name' => $getSessUserFull,
                    'reject_note' => $getReason,
                    'reject_date' => date("Y-m-d H:i:s"),
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );

                $whereArray = array('apprv_id' => $getId, 'approve_type' => 'STD');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);

                if ($execData) {
                    //here get GUID first
                    $whereArry = array('a.apprv_id' => $getId, 'a.approve_type' => 'STD');
                    $getQueryGuid = $this->db->select('a.*')->from('tr_trans_approve_status a')->where($whereArry)->get();
                    foreach ($getQueryGuid->result() as $rows) {
                        $getGuid = $rows->ca_guid;
                    }
                    //===================
                    $data = array(
                        'appv_complete' => 2
                    );
                    $this->db->where('ca_guid', $getGuid);
                    $execData = $this->db->update('t_ca_trans', $data);

                    $getEmailTo = $this->getRequesterMail($getGuid);
                    $this->sendEmailRemiderApproval($getEmailTo, $getGuid, 'REJ');
                }
                break;

            case "fintcareject":

                $data = array('appv_complete' => 8, 'fin_rejectnote' => $getReason, 'fin_rejectby' => $getSessUser, 'fin_rejectbyfull' => $getSessUserFull, 'fin_rejectdatetime' => date("Y-m-d H:i:s"));
                $this->db->where('id_ca_trans', $getId);
                $execData = $this->db->update('t_ca_trans', $data);
                //$getEmailTo = $this->getRequesterMail($getGuid);                    
                //$this->sendEmailRemiderApproval($getEmailTo, $getGuid, 'REJ');
                break;

            case "finsetreject":

                //here get GUID first
                $whereArry = array('a.id_ca_trans' => $getId);
                $getQueryGuid = $this->db->select('a.*')->from('t_ca_trans a')->where($whereArry)->get();
                foreach ($getQueryGuid->result() as $rows) {
                    $getGuid = $rows->ca_guid;
                }
                
                //===================

                $data = array('appv_complete' => 9, 'fin_rejectnote' => $getReason, 'fin_rejectby' => $getSessUser, 'fin_rejectbyfull' => $getSessUserFull, 'fin_rejectdatetime' => date("Y-m-d H:i:s"));
                $this->db->where('id_ca_trans', $getId);
                $execData = $this->db->update('t_ca_trans', $data);
                
                //here for delete approval 
                $this->db->where(array('ca_guid'=> $getGuid, 'approve_type' => 'SET'));
                $execDelData = $this->db->delete('tr_trans_approve_status');
                //
                
                //$getEmailTo = $this->getRequesterMail($getGuid);                    
                //$this->sendEmailRemiderApproval($getEmailTo, $getGuid, 'REJ');

                break;
        }
    }

    function sendEmailRemiderApproval($getEmailTo, $getCaGuid, $getType) {
        $helpers = new helpers();

        $setTCAId = '';

        $setDate = '';
        $setFrom = '';
        $setType = '';
        $setAmount = '';
        $setSettleAmount = '';
        $setPurpose = '';

        $whereArry = array('a.ca_guid' => $getCaGuid);
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        $getQuery = $this->db->get();

        foreach ($getQuery->result() as $rows) {
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;
            $getStartVal = $rows->request_amount;
            $getSettleVal = $rows->settled_amount;

            $setTCAId = $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun;
        }
        //==============

        $setEmailStatus = "TCA Approved,<span style=\"color:blue\">Settlement Required</span>";
        if ($getType == "REJ") {
            $setEmailStatus = "TCA Rejected";
        }

        $dearMail = "Dear <b>TCA Requester</b>,";
        $setMessage = "<span style=\"color:red;background:red\">Must Returned</span>";
        $setSettleVal = intval($getStartVal) - intval($getSettleVal);
        if (intval($getSettleVal) > intval($getStartVal)) {
            $setMessage = "<span style=\"color:green;\">Need more Cash</span>";
            $getMailADM = $this->getAdminMail('FINADM');
            $getEmailTo = $getMailADM . ',' . $getEmailTo;
            $dearMail = "Dear <b>Finance Admin</b>,";
        }

//        echo $setBody;

        $setBody = $dearMail . '
                    <table style="font-family:verdana">                            
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    <b>New Reminder Message From Cash Advance System</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;height:50px">
                                    <b style="color:red">' . $setEmailStatus . '</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">TCA ID :</td>
                                <td style="font-weight: bold; color:green">' . $setTCAId . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">' . $setDate . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">' . $setFrom . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">' . $setType . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">' . $setAmount . ' IDR</td>
                            </tr>                            
                            
                            <tr>
                                <td style="text-align:right"> <b>' . $setMessage . ' </b>:</td>
                                <td style="">' . $helpers->currencyFormat($setSettleVal) . ' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">' . $setPurpose . '</td>
                            </tr>                                                        
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Thank You</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    Please Access <a href="' . base_url() . '" >Cash Advance System</a>
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

//        $strUpdateCounter = "UPDATE t_ca_trans SET settle_mail_counter = settle_mail_counter+1 WHERE id_ca_trans=".$getCaId;
//        $execData = $this->db->query($strUpdateCounter);
    }

    function settleApproved($idno, $getSessUser, $getSessUserFull, $getUserJobDetail) {
        $getType = 'approve';
        $getId = $idno;
        $getReason = '';

        //$getSessUser = $this->session->userdata('userName');
        //$getSessUserFull = $this->session->userdata('userNameFull');
        //$getUserJobDetail = $this->session->userdata('sessionLevelDetail');

        switch ($getType) {
            case "approve":
                $data = array(
                    'appr_status' => 1,
                    'appr_userid' => $getSessUser,
                    'appr_name' => $getSessUserFull,
                    'appr_date' => date("Y-m-d H:i:s"),
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );
                //echo $getId;
                $whereArray = array('apprv_id' => $getId, 'approve_type' => 'SET');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);


                //here next approval
                //select first
                $whereArry = array('a.apprv_id' => $getId, 'a.approve_type' => 'SET');
                $this->db->select('a.apprv_id, b.*')
                        ->from('tr_trans_approve_status a')
                        ->join('t_ca_trans b', 'a.ca_guid = b.ca_guid', 'left')
                        ->where($whereArry)
                        ->order_by('input_datetime', 'desc');

                $getQuery = $this->db->get();
                foreach ($getQuery->result() as $rows) {
                    $getGuid = $rows->ca_guid;
                    $getAppFlow = $rows->appv_flow_settle;
                    $getAppFlowStatus = $rows->appv_flow_settle_status;
                    $getIdCaTrans = $rows->id_ca_trans;
                }
                
                $getAppFlowArray = explode(",", $getAppFlow);
                $getAppFlowStatusArray = explode(",", $getAppFlowStatus);
//                echo count($getAppFlowArray);
//                echo '<br/>'.count($getAppFlowStatusArray);
                //============
                /*
                if (count($getAppFlowArray) != count($getAppFlowStatusArray)) {
                    $setNewAppFlowStatus = $getAppFlowStatus . "," . $getAppFlowArray[count($getAppFlowStatusArray)];
                    $data = array(
                        'appv_flow_settle_status' => $setNewAppFlowStatus
                    );
//                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "SET");
                } else {

                */
                    $setNewAppFlowStatus = $getAppFlowStatus;
                    $data = array(
                        'appv_settle_complete' => 1,
                        'appv_complete' => 5,
                        'appv_flow_settle_status' => $setNewAppFlowStatus. ',END',
                        'final_settledate' => date("Y-m-d H:i:s") ,
                    );
//                    $getEmailTo = $this->getRequesterMail($getGuid);
//                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid,'APV');
                //}
                $this->db->where('id_ca_trans', $getIdCaTrans);
                $execData = $this->db->update('t_ca_trans', $data);

                if (count($getAppFlowArray) != count($getAppFlowStatusArray)) {
                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "SET");
                } else {
                    $getEmailTo = $this->getRequesterMail($getGuid);
                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid, 'APV', $getIdCaTrans);
                }

                //==================
                break;

            case "reject":

                $data = array(
                    'appr_status' => 2,
                    'reject_userid' => $getSessUser,
                    'reject_name' => $getSessUserFull,
                    'reject_note' => $getReason,
                    'reject_date' => date("Y-m-d H:i:s"),
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );

                $whereArray = array('apprv_id' => $getId, 'approve_type' => 'SET');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);

                if ($execData) {
                    //here get GUID first
                    $whereArry = array('a.apprv_id' => $getId, 'a.approve_type' => 'SET');
                    $getQueryGuid = $this->db->select('a.*')->from('tr_trans_approve_status a')->where($whereArry)->get();
                    foreach ($getQueryGuid->result() as $rows) {
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
                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid, 'REJ');
                }

                break;
        }
    }

    function settleApproval() {
        $getType = $_POST['setType'];
        $getId = $_POST['setId'];
        $getReason = $_POST['setReason'];

        $getSessUser = $this->session->userdata('userName');
        $getSessUserFull = $this->session->userdata('userNameFull');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');

        switch ($getType) {
            case "approve":
                $data = array(
                    'appr_status' => 1,
                    'appr_userid' => $getSessUser,
                    'appr_name' => $getSessUserFull,
                    'appr_date' => date("Y-m-d H:i:s"),
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );
                //echo $getId;
                $whereArray = array('apprv_id' => $getId, 'approve_type' => 'SET');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);


                //here next approval
                //select first
                $whereArry = array('a.apprv_id' => $getId, 'a.approve_type' => 'SET');
                $this->db->select('a.apprv_id, b.*')
                        ->from('tr_trans_approve_status a')
                        ->join('t_ca_trans b', 'a.ca_guid = b.ca_guid', 'left')
                        ->where($whereArry)
                        ->order_by('input_datetime', 'desc');

                $getQuery = $this->db->get();
                foreach ($getQuery->result() as $rows) {
                    $getGuid = $rows->ca_guid;
                    $getAppFlow = $rows->appv_flow_settle;
                    $getAppFlowStatus = $rows->appv_flow_settle_status;

                    if ((int)$rows->request_amount > (int)$rows->settled_amount)
                    {
                        $appv_complete = 10;
                    }
                    else if ((int)$rows->request_amount < (int)$rows->settled_amount)
                    {
                        $appv_complete = 11;
                    }
                    else 
                    {
                        $appv_complete = 5;
                    }
                
                }

                // echo $appv_complete;exit;

                echo $whereArry;
                $getAppFlowArray = explode(",", $getAppFlow);
                $getAppFlowStatusArray = explode(",", $getAppFlowStatus);
//                echo count($getAppFlowArray);
//                echo '<br/>'.count($getAppFlowStatusArray);
                //============

                
                if (count($getAppFlowArray) != count($getAppFlowStatusArray)) {
                    $setNewAppFlowStatus = $getAppFlowStatus . "," . $getAppFlowArray[count($getAppFlowStatusArray)];
                    $data = array(
                        'appv_flow_settle_status' => $setNewAppFlowStatus
                    );
//                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "SET");
                } else {
                    $setNewAppFlowStatus = $getAppFlowStatus;
                    $data = array(
                        // 'appv_settle_complete' => 1,
                        // 'appv_complete' => $appv_complete,
                        'appv_settle_complete' => 3,
                        'appv_complete' => 5,
                        // 'appv_flow_settle' => $setNewAppFlowStatus. ',END',
                        'appv_flow_settle_status' => $setNewAppFlowStatus. ',END',
                        'final_settledate' => date("Y-m-d H:i:s")
                    );
//                    $getEmailTo = $this->getRequesterMail($getGuid);
//                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid,'APV');
                }
                $this->db->where('ca_guid', $getGuid);
                $execData = $this->db->update('t_ca_trans', $data);

                if (count($getAppFlowArray) != count($getAppFlowStatusArray)) {
                    $this->sendEmailToNextAppv($getAppFlowArray[count($getAppFlowStatusArray)], $getGuid, "SET");
                } else {
                    $getEmailTo = $this->getRequesterMail($getGuid);
                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid, 'APV');
                }

                //==================
                break;

            case "reject":

                $data = array(
                    'appr_status' => 2,
                    'reject_userid' => $getSessUser,
                    'reject_name' => $getSessUserFull,
                    'reject_note' => $getReason,
                    'reject_date' => date("Y-m-d H:i:s"),
                    'inputUser' => $getSessUser,
                    'inputDateTime' => date("Y-m-d H:i:s")
                );

                $whereArray = array('apprv_id' => $getId, 'approve_type' => 'SET');
                $this->db->where($whereArray);
                $execData = $this->db->update('tr_trans_approve_status', $data);

                if ($execData) {
                    //here get GUID first
                    $whereArry = array('a.apprv_id' => $getId, 'a.approve_type' => 'SET');
                    $getQueryGuid = $this->db->select('a.*')->from('tr_trans_approve_status a')->where($whereArry)->get();
                    foreach ($getQueryGuid->result() as $rows) {
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
                    $this->sendEmailRemiderSettle2($getEmailTo, $getGuid, 'REJ');
                }

                break;
        }
    }

    function initCAView() {

        $res = "";
        $getCaID = $_POST['getids'];
        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.id_ca_trans' => $getCaID);
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, c.isSuj, (CASE WHEN d.isSuj = 0 THEN d.origin WHEN d.isSuj = 1 THEN e.origin_trip WHEN ISNULL(d.isSuj) THEN d.origin END) AS RealOrigin,
    (CASE WHEN d.isSuj = 0 THEN d.destination WHEN d.isSuj = 1 THEN e.fleet_schedule WHEN ISNULL(d.isSuj) THEN d.destination END) AS RealDestination,f.bank_drivername')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                //aditional by ALfan
                ->join('tt_suj_transaction c', 'a.ca_guid = c.ca_guid', 'left')
                //======================================

                //additional by yudhis
                ->join('tt_suj_transaction d', 'a.ca_guid = d.ca_guid', 'left')
                ->join('tr_cost_type_rates e', 'd.id_cost_type_rate = e.ratesid', 'left')
                ->join('tr_driver_account f', 'a.ca_guid = f.ca_guid', 'left')
                //======================================


                ->where($whereArry)
                ->order_by('input_datetime', 'desc');
				

        $getQuery = $this->db->get();
		//echo $this->db->last_query();
		//die();

        $setApproval = "";
        $setSettleApproval = "";

        $statusSuj = "";

        foreach ($getQuery->result() as $rows) {

            //aditional by alfan
            switch ($rows->isSuj) {
                case 0:
                    $statusSuj = "Non SUJ";
                    break;
                case 1:
                    $statusSuj = "SUJ";
                    break;
                default:
                    $statusSuj = "";
                    break;
            }

            $helpers = new helpers();
            //here for get approval flow
            $getApproval = $rows->appv_flow;

            $getQuery = $this->db->select('*')
                    ->from('tr_trans_approve_status a')
                    ->where('a.ca_guid', $rows->ca_guid)
                    ->where('a.approve_type', 'STD')
                    ->get();
            foreach ($getQuery->result() as $getRow) {
                $getUserLevel = $this->getUserDetailLevel($getRow->approver);
                if ($getRow->appr_status == 1) {
                    //$setApproval .= $getUserLevel . ' (<b><span class="greenSpan">Approve</span></b>)';
                    $getUserLevel = $getUserLevel != "" ? $getUserLevel : $getRow->appr_name;
                    $setApproval .= $getUserLevel . ' (<b><span class="greenSpan">Approve</span></b>)';
                } elseif ($getRow->appr_status == 2) {
                    $setApproval .= $getUserLevel . ' (<b style="color:red">Rejected</b>) <br/>&nbsp;&nbsp;&nbsp; <b>>Note :</b> ' . $getRow->reject_note;
                } elseif ($getRow->appr_status == 3) {
                    $setApproval .= $getUserLevel . ' (<b style="color:red">Escalated</b>)';
                } else {
                    $setApproval .= $getUserLevel . ' (<b><span class="blueSpan">Waiting for Approval</span></b>)';
                }
                $setApproval .= '<br/>';
            }

            //==========================
            //here for get settle approval flow

            $getQuery = $this->db->select('*')
                    ->from('tr_trans_approve_status a')
                    ->where('a.ca_guid', $rows->ca_guid)
                    ->where('a.approve_type', 'SET')
                    ->get();
            if ($getQuery->num_rows() != 0) {
                foreach ($getQuery->result() as $getRow) {
                    $getUserLevel = $this->getUserDetailLevel($getRow->approver);
                    if ($getRow->appr_status == 1) {
                        $getUserLevel = $getUserLevel != "" ? $getUserLevel : $getRow->appr_name;
                        $setSettleApproval .= $getUserLevel . ' (<b><span class="greenSpan">Approve</span></b>)';
                    } elseif ($getRow->appr_status == 2) {
                        $setSettleApproval .= $getUserLevel . ' (<b style="color:red">Rejected</b>) <br/>&nbsp;&nbsp;&nbsp; <b>>Note :</b> ' . $getRow->reject_note;
                    } elseif ($getRow->appr_status == 3) {
                        $setSettleApproval .= $getUserLevel . ' (<b style="color:red">Escalated</b>)';
                    } else {
                        $setSettleApproval .= $getUserLevel . ' (<b><span class="blueSpan">Waiting for Approval</span></b>)';
                    }
                    $setSettleApproval .= '<br/>';
                }
            } else {
                $setSettleApproval .= '<b><i>Waiting for uncomplete Approval</i></b>';
            }

            $setTCAno = '<span style="font-weight:bold;color:blue" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';

            //==========================

            $res = '<table style="color:black;border-collapse:separate;border-spacing:3px;width:100%">';
            $res .= '<tr>';
            $res .= '<td colspan="2"> <img src="' . base_url() . 'includes/images/ckbLogo.png" alt=""/> <b>Temporary Cash Advance</b><hr class="hrStyle" style="border-color:black"/><br/> </td>';
            $res .= '</tr>';
//            $res .= '<tr>';
//            $res .= '<td colspan="2" style="color:blue"><b><i>.::Temporary Cash Advance::.</i></b><hr class="hrStyle" style="border-color:black"/><br/></td>';
//            $res .= '</tr>';
            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold;width:100px">TCA No :</td>';
            $res .= '<td>' . $setTCAno . '</td>';
            $res .= '</tr>';
            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold;">Request Date :</td>';
            $res .= '<td>' . $helpers->convertDateToSimple($rows->date_request) . '</td>';
            $res .= '</tr>';
            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold">Type :</td>';
            $res .= '<td>' . $rows->desc_trans . '</td>';
            $res .= '</tr>';
            //Aditional by Alfan
            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold">Cost Type :</td>';
            $res .= '<td>' . $statusSuj . '</td>';
            $res .= '</tr>';
            //=======================================================================================================
            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold">Amount :</td>';
            $res .= '<td style="font-weight:bold">' . $helpers->currencyFormat($rows->request_amount) . ' IDR </td>';
            $res .= '</tr>';

            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold">Purpose :</td>';
            $res .= '<td>' . $rows->purpose . '</td>';
            $res .= '</tr>';

            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold">Origin :</td>';
            $res .= '<td>' . $rows->RealOrigin . '</td>';
            $res .= '</tr>';

            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold">Destination :</td>';
            $res .= '<td>' . $rows->RealDestination . '</td>';
            $res .= '</tr>';

            $res .= '<tr>';
            $res .= '<td style="text-align:right;font-weight:bold">Driver Name :</td>';
            $res .= '<td>' . $rows->bank_drivername . '</td>';
            $res .= '</tr>';

            if ($rows->settled_amount != 0) {
                $res .= '<tr>';
                $res .= '<td colspan="2"><hr class="hrStyle"/></td>';
                $res .= '</tr>';
                $res .= '<tr>';
                $res .= '<td style="text-align:right;font-weight:bold">Settlement :</td>';
                $res .= '<td style="font-weight:bold;color:blue">' . $helpers->currencyFormat($rows->settled_amount) . ' IDR </td>';
                $res .= '</tr>';
                $res .= '<tr>';
                $res .= '<td style="text-align:right;font-weight:bold;">Settle Date :</td>';
                $res .= '<td style="font-weight:bold;color:green">' . $helpers->convertDateToSimple($rows->input_datetime_settle) . '</td>';
                $res .= '</tr>';
                $res .= '<tr>';
            }
            $res .= '<tr>';
            $res .= '<td colspan="2"><hr class="hrStyle"/></td>';
            $res .= '</tr>';
            $res .= '<tr>';
            $res .= '<td colspan="2" style="color:blue;font-weight:bold">Approval Flow : </td>';
            $res .= '</tr>';
            $res .= '<tr>';
            $res .= '<td colspan="2">' . $setApproval . '</td>';
            $res .= '</tr>';
            $res .= '</tr>';
            $res .= '<tr>';
            $res .= '<td colspan="2" style="color:blue;font-weight:bold"><br/>Settlement Approval Flow : </td>';
            $res .= '</tr>';
            $res .= '<tr>';
            $res .= '<td colspan="2">' . $setSettleApproval . '</td>';
            $res .= '</tr>';
            $res .= '</table>';
        }

        $res .= "<br/><br/>(<span style=\"color:red\"><b>Click me to close</b></span>)";

        echo $res;
    }

    function getUserDetailLevel($getUserLevelDetail) {
        $res = '';
        $getQuery = $this->db->select('*')->from('t_direct_report a')->where('a.job_title_detail', $getUserLevelDetail)->get();
        foreach ($getQuery->result() as $getRow) {
            $res = $getRow->name;
        }
        return $res;
    }

    function sendEmailRemider() {
        $helpers = new helpers();

        $setTCAId = '';
        $setDate = '';
        $setFrom = '';
        $setType = '';
        $setAmount = '';
        $setSettleAmount = '';
        $setPurpose = '';

        //here get query 
        $getCaId = $_POST['caid'];
        $whereArry = array('a.id_ca_trans' => $getCaId);
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        $getQuery = $this->db->get();

        foreach ($getQuery->result() as $rows) {
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;

            $setTCAId = $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun;
        }
        //==============

        $setBody = '<table style="font-family:verdana">
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    <b>New Reminder Message From Cash Advance System</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">TCA ID :</td>
                                <td style="font-weight: bold; color:green">' . $setTCAId . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">' . $setDate . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">' . $setFrom . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">' . $setType . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">' . $setAmount . ' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Settle Amount :</td>
                                <td style="">' . $setSettleAmount . ' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">' . $setPurpose . '</td>
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
                                    Please Access <a href="' . base_url() . '" >Cash Advance System</a>
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
//        $getEmailTo = "chandra.adriansyah@ckb.co.id";        
        $helpers->sendMail($getEmailTo, $setBody);
    }

    function sendEmailRemiderSettle() {
        $helpers = new helpers();

        $setTCAId = '';
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


        $whereArry = array('a.id_ca_trans' => $getCaId);
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        $getQuery = $this->db->get();

        foreach ($getQuery->result() as $rows) {
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;
            $getStartVal = $rows->request_amount;
            $getSettleVal = $rows->settled_amount;

            $setTCAId = $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun;
        }
        //==============


        $dearMail = "Dear <b>TCA Requester</b>,";
        $setMessage = "<span style=\"color:red;background:red\">Must Returned</span>";
        $setSettleVal = intval($getStartVal) - intval($getSettleVal);
        if (intval($getSettleVal) > intval($getStartVal)) {
            $setMessage = "<span style=\"color:green;\">Need more Cash</span>";
            $getMailADM = $this->getAdminMail('FINADM');
            $getEmailTo = $getMailADM . ',' . $getEmailTo;
            $dearMail = "Dear <b>Finance Admin</b>,";
        }

//        echo $setBody;



        $setBody = $dearMail . '
                    <table style="font-family:verdana">
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    Reminder ' . $rows->settle_mail_counter . '<br/><br/>
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
                                <td style="text-align:right">TCA ID :</td>
                                <td style="font-weight: bold; color:green">' . $setTCAId . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">' . $setDate . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">' . $setFrom . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">' . $setType . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">' . $setAmount . ' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Settle Amount :</td>
                                <td style="">' . $setSettleAmount . ' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right"> <b>' . $setMessage . ' </b>:</td>
                                <td style="">' . $helpers->currencyFormat($setSettleVal) . ' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">' . $setPurpose . '</td>
                            </tr>                                                        
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Thank You</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    Please Access <a href="' . base_url() . '" >Cash Advance System</a>
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

        $strUpdateCounter = "UPDATE t_ca_trans SET settle_mail_counter = settle_mail_counter+1 WHERE id_ca_trans=" . $getCaId;
        $execData = $this->db->query($strUpdateCounter);
    }

    function sendEmailRemiderSettle2($getEmailTo, $getCaGuid, $getType, $idCaTrans=null) {
        $helpers = new helpers();

        $setTCAId = '';
        $setDate = '';
        $setFrom = '';
        $setType = '';
        $setAmount = '';
        $setSettleAmount = '';
        $setPurpose = '';

        if ($idCaTrans == null) 
        {
            $whereArry = array('a.ca_guid' => $getCaGuid);
            $whereQuery = " ca_guid ='".$getCaGuid."'";
        }
        else
        {
            $whereArry = array('a.id_ca_trans' => $idCaTrans);
            $whereQuery = " id_ca_trans ='".$idCaTrans."'";
        }
        
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        $getQuery = $this->db->get();

        foreach ($getQuery->result() as $rows) {
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;
            $getStartVal = $rows->request_amount;
            $getSettleVal = $rows->settled_amount;

            $setTCAId = $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun;
        }
        //==============

        $setEmailStatus = "TCA Approved";
        if ($getType == "REJ") {
            $setEmailStatus = "TCA Rejected";
        }

        $dearMail = "Dear <b>TCA Requester</b>,";
        $setMessage = "<span style=\"color:red;background:red\">Must Returned</span>";
        $setSettleVal = intval($getStartVal) - intval($getSettleVal);
        if (intval($getSettleVal) > intval($getStartVal)) {
            $setMessage = "<span style=\"color:green;\">Need more Cash</span>";
            $getMailADM = $this->getAdminMail('FINADM');
            $getEmailTo = $getMailADM . ',' . $getEmailTo;
            $dearMail = "Dear <b>Finance Admin</b>,";
        }

//        echo $setBody;



        $setBody = $dearMail . '
                    <table style="font-family:verdana">
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    Reminder ' . $rows->settle_mail_counter . '<br/><br/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;">
                                    <b>New Reminder Message From Cash Advance System</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">TCA ID :</td>
                                <td style="font-weight: bold; color:green">' . $setTCAId . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center; background: #008266;height:50px">
                                    <b style="color:red">' . $setEmailStatus . '</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">' . $setDate . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">' . $setFrom . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">' . $setType . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">' . $setAmount . ' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Settle Amount :</td>
                                <td style="">' . $setSettleAmount . ' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right"> <b>' . $setMessage . ' </b>:</td>
                                <td style="">' . $helpers->currencyFormat($setSettleVal) . ' IDR</td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">' . $setPurpose . '</td>
                            </tr>                                                        
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    <b>Thank You</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center">
                                    Please Access <a href="' . base_url() . '" >Cash Advance System</a>
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
        $queryGetMailCounter = "SELECT settle_mail_counter from t_ca_trans where".$whereQuery;
        $query = $this->db->query($queryGetMailCounter);

        $strUpdateCounter = "UPDATE t_ca_trans SET settle_mail_counter = ".$query->first_row()->settle_mail_counter."+1 WHERE".$whereQuery;
        $execData = $this->db->query($strUpdateCounter);
        //print_r($this->db->last_query());die();
    }

    function getAdminMail($getType) {
        $getUserEmail = '';
        $whereArry = array('user_status' => $getType);
        $getDetailQuery = $this->db->select('*')->from('t_user_admin')->where($whereArry)->get();

        foreach ($getDetailQuery->result() as $getRows2) {
            $getUserEmail = $getRows2->user_email;
        }

        return $getUserEmail;
    }

    function getRequesterMail($getGuid) {
        $whereArr = array('a.ca_guid' => $getGuid);
        $this->db->select('*')
                ->from('t_ca_trans a')
                ->where($whereArr);

        $getUserQuery = $this->db->get();
        foreach ($getUserQuery->result() as $rows) {
            $getuserSn = $rows->requester_sn;
        }

        //here get email from personeel
        $helper = new helpers();
        $connHera = $helper->_initServDataFast_custom('employee');
        $whereArr = array('a.employee_id' => $getuserSn);
        $connHera->select('*')
                ->from('t_personel a')
                ->where($whereArr);
        $getMailQuery = $connHera->get();

        foreach ($getMailQuery->result() as $rows2) {
            $getMail = $rows2->email;
        }

        return $getMail;
    }

    function approveSettleFin() {
        $getEmailConfirm = $_POST['emailto'];
        $getCaId = $_POST['caid'];

        $getSessUser = $this->session->userdata('userName');

        $data = array(
            'appv_complete' => 6,
            'fin_settle' => 1,
            'fin_settle_user' => $getSessUser,
            'fin_settle_datetime' => date("Y-m-d H:i:s")
        );

        $whereArray = array('id_ca_trans' => $getCaId);
        $this->db->where($whereArray);
        $execData = $this->db->update('t_ca_trans', $data);
//        
//        echo $this->db->last_query();
    }

    function sendEmailToNextAppv($getID, $getCaGuid, $getType) {

        $setWhere = array('a.job_title_detail' => $getID);
        $this->db->select('a.*,b.email')
                ->from('t_direct_report a')
                ->join('t_personel b', 'b.employee_id=a.employee_id', 'inner')
                ->where($setWhere);

        $getQuery = $this->db->get();
        $getEmail = "";
        foreach ($getQuery->result() as $row) {
            $getEmail = $row->email;
        }


        //here email core ======================================================
        $helpers = new helpers();

        $setTCAId = '';
        $setDate = '';
        $setFrom = '';
        $setType = '';
        $setAmount = '';
        $setSettleAmount = '';
        $setPurpose = '';

        //here get query 
        //$getCaId = $_POST['caid'];
        $whereArry = array('a.ca_guid' => $getCaGuid);
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        $getQuery = $this->db->get();

        foreach ($getQuery->result() as $rows) {
            $setDate = $helpers->convertDateToSimple($rows->date_request);
            $setFrom = $rows->requester_name;
            $setType = $rows->desc_trans;
            $setAmount = $helpers->currencyFormat($rows->request_amount);
            $setSettleAmount = $helpers->currencyFormat($rows->settled_amount);
            $setPurpose = $rows->purpose;

            $setTCAId = $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun;
        }
        //==============
        $setHeader = "New Cash Advance";
        if ($getType == "SET") {
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
                                    <b>' . $setHeader . '</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right">TCA ID :</td>
                                <td style="font-weight: bold; color:green">' . $setTCAId . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Date :</td>
                                <td style="">' . $setDate . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">From :</td>
                                <td style="">' . $setFrom . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Type :</td>
                                <td style="">' . $setType . '</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Amount :</td>
                                <td style="">' . $setAmount . ' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Settle Amount :</td>
                                <td style="">' . $setSettleAmount . ' IDR</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Purpose :</td>
                                <td style="">' . $setPurpose . '</td>
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
                                    Please Access <a href="' . base_url() . '" >Cash Advance System</a>
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

}

?>
