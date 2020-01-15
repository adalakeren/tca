<?php include_once 'helpers.php'; ?>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of main_core
 *
 * @author CKB
 */
class main_core extends Controller {
    function main_core() {
        parent::Controller();
        $this->load->database();
        $this->load->helper(array('url'));
        $this->load->library('pagination');
        $this->load->library('session');
        
        $userLogon = $this->session->userdata('logged_in');
        if (!$userLogon) {
            redirect('admin_core/user_logout');
        }
            
    }
    function index(){
		$getUserJobDetail = $this->session->userdata('sessionAdmin');   
		
        $throwData['getNewCashApproval'] = $this->initCekApprovalNeed();
        $throwData['getSettleApproval'] = $this->initCekSetApprovalNeed();

        $throwData['getNewCashApprovalNonSUJ'] = $this->initCekApprovalNeedNonSUJ();
        $throwData['getSettleApprovalNonSUJ'] = $this->initCekSetApprovalNeedNonSUJ();
		
		// if($getUserJobDetail == '1'){
		// 	//$this->debugApproval();
		// }
        $this->load->view('main_home',$throwData);
    }
    
    function initCekApprovalNeed(){
        $helpers = new helpers();
        
        $res = '';
        $getUserSess = $this->session->userdata('userName');   
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail') != "" ? $this->session->userdata('sessionLevelDetail') : '-'; 

        // penambahan yudhis
        $getUserLevelId = $this->session->userdata('level_id');
		
		/*
        $whereArry = array('c.approver' => $getUserJobDetail, 'c.appr_status' => 0,'c.approve_type'=>'STD','a.isdelete'=>0 );
        
        $this->db->select('a.*, b.desc_trans,c.apprv_id, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->join('tr_trans_approve_status c','a.ca_guid = c.ca_guid', 'left')
                    ->where($whereArry)
                    ->order_by('input_datetime','desc')
                    //->limit(10)
                    ;
        
        $getQuery = $this->db->get();
		*/
		
		$strSqlGetData = "SELECT a.*,b.desc_trans,MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, SUBSTRING_INDEX(a.appv_flow_status, ',', -1) AS last_approver, c.isSuj  
							FROM t_ca_trans a 
							LEFT JOIN t_jenis_transaksi b ON a.request_type = b.id_trans

                            -- penambahan yudhis
                            LEFT JOIN tt_suj_transaction c on a.ca_guid = c.ca_guid

							WHERE SUBSTRING_INDEX(a.appv_flow_status, ',', -1) = '".$getUserJobDetail."'
							AND SUBSTRING_INDEX(a.appv_flow_status, ',', -1) <> 'END'
							AND a.isdelete = 0
							AND a.appv_complete = 0
                            
                            -- penambahan yudhis
                            AND c.isSuj = 1

							ORDER BY a.id_ca_trans DESC";
		$getQuery = $this->db->query($strSqlGetData);
		
        $tempCountdata = 0;  
        $res .= '<br/>' ;
		
		// echo $this->db->last_query();
		
        foreach ($getQuery->result() as $rows) {
            //temp here get approval 
            $getFlow = explode(",", $rows->appv_flow_status);
            $getAvail = 0;
            //if(in_array($getUserJobDetail,$getFlow)){
            if (trim($getFlow[count($getFlow) - 1]) == trim($getUserJobDetail)) {

                // penambahan yudhis
                if ($rows->isSuj == 1 && $getUserLevelId == "MGR"){
                    // do nothing
                } else{
                    $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';
                    $setTCAno = '<span style="font-weight:bold;color:blue" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                
                    $res .= '' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold;text-decoration:underline" href="'.base_url().'index.php/apv_core/approval">' . $rows->requester_name . ' (' . $setTCAno .')'.'</a>';
                    //$res .= '<br/>' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold" href="'.base_url().'index.php/apv_core/approval">' . $rows->requester_name . '</a>';
                    $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Amount</b> : " . $helpers->currencyFormat($rows->request_amount);
                    $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Type</b> : " . $rows->desc_trans;
                    $res .= "<hr class='hrStyle'/>";
                    $tempCountdata++;
                }

             //    $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';
             //    $setTCAno = '<span style="font-weight:bold;color:blue" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                
             //    $res .= '' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold;text-decoration:underline" href="'.base_url().'index.php/apv_core/approval">' . $rows->requester_name . ' (' . $setTCAno .')'.'</a>';
             //    //$res .= '<br/>' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold" href="'.base_url().'index.php/apv_core/approval">' . $rows->requester_name . '</a>';
             //    $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Amount</b> : " . $helpers->currencyFormat($rows->request_amount);
             //    $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Type</b> : " . $rows->desc_trans;
	            // $res .= "<hr class='hrStyle'/>";
             //    $tempCountdata++;
            }
        }
        if ($tempCountdata == 0) {            
            $res = '<br/><i>No Outstanding Record</i>' ;    
        }
       
        
        return $res;
    }
    
    function initCekSetApprovalNeed(){
        $helpers = new helpers();
        
        $res = '';
        $getUserSess = $this->session->userdata('userName');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail') != "" ? $this->session->userdata('sessionLevelDetail') : '-'; 
		/*
		$whereArry = array('c.approver' => $getUserJobDetail, 'c.appr_status' => 0, 'c.approve_type'=>'SET','a.isdelete'=>0 );
        
        $this->db->select('a.*, b.desc_trans,c.apprv_id, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->join('tr_trans_approve_status c','a.ca_guid = c.ca_guid', 'left')
                    ->where($whereArry)
                    ->order_by('input_datetime','desc')
                    //->limit(10)
                ;
        
        $getQuery = $this->db->get();
        */
		
		$strSqlGetData = "SELECT a.*,b.desc_trans,MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) AS last_approver  
							FROM t_ca_trans a 
							LEFT JOIN t_jenis_transaksi b ON a.request_type = b.id_trans

                            -- penambahan yudhis
                            LEFT JOIN tt_suj_transaction c on a.ca_guid = c.ca_guid

							WHERE SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) = '".$getUserJobDetail."'
							AND SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) <> 'END'
							AND a.isdelete = 0
							AND a.appv_complete = 3

                            -- penambahan yudhis
                            -- AND (c.isSuj = 1 or c.isSuj is null)
                            AND c.isSuj = 1

							ORDER BY a.id_ca_trans DESC";
							// die($strSqlGetData);
		$getQuery = $this->db->query($strSqlGetData);

        // echo $this->db->last_query();
		
        $tempCountdata = 0;
        $res .= '<br/>' ;
        foreach ($getQuery->result() as $rows) {
            //temp here get approval 
            $getFlow = explode(",", $rows->appv_flow_settle_status);
            $getAvail = 0;
            //if(in_array($getUserJobDetail,$getFlow)){       

            if (trim($getFlow[count($getFlow) - 1]) == trim($getUserJobDetail)) {
                $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';
                $setTCAno = '<span style="font-weight:bold;color:blue" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                
                $res .= '' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold;text-decoration:underline" href="'.base_url().'index.php/apv_core/settleapproval">' . $rows->requester_name . ' (' . $setTCAno .')'.'</a>';
                $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Amount</b> : " . $helpers->currencyFormat($rows->request_amount);
                $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Settlement Amount</b> : " . $helpers->currencyFormat($rows->settled_amount);
                $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Type</b> : " . $rows->desc_trans;
                $res .= "<hr class='hrStyle'/>";
                $tempCountdata++;
            }
        }
        if ($tempCountdata == 0) {            
            $res = '<br/><i>No Outstanding Record</i>' ;    
        }

        return $res;
    }


    function initCekApprovalNeedNonSUJ(){
        $helpers = new helpers();
        
        $res = '';
        $getUserSess = $this->session->userdata('userName');   
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail') != "" ? $this->session->userdata('sessionLevelDetail') : '-'; 

        $getLevelId = $this->session->userdata('level_id');

        // penambahan yudhis
        $getUserLevelId = $this->session->userdata('level_id');
        
        /*
        $whereArry = array('c.approver' => $getUserJobDetail, 'c.appr_status' => 0,'c.approve_type'=>'STD','a.isdelete'=>0 );
        
        $this->db->select('a.*, b.desc_trans,c.apprv_id, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->join('tr_trans_approve_status c','a.ca_guid = c.ca_guid', 'left')
                    ->where($whereArry)
                    ->order_by('input_datetime','desc')
                    //->limit(10)
                    ;
        
        $getQuery = $this->db->get();
        */
        
        // if ($getLevelId == "MGR") {
        //     // echo "masuk kondisi";

        // $strSqlGetData = "SELECT a.*,b.desc_trans,MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, SUBSTRING_INDEX(a.appv_flow_status, ',', -1) AS last_approver, c.isSuj  
        //                     FROM t_ca_trans a 
        //                     LEFT JOIN t_jenis_transaksi b ON a.request_type = b.id_trans

        //                     -- penambahan yudhis
        //                     LEFT JOIN tt_suj_transaction c on a.ca_guid = c.ca_guid

        //                     WHERE a.appv_flow_status like '%".$getUserJobDetail."%'
        //                     AND SUBSTRING_INDEX(a.appv_flow_status, ',', -1) <> 'END'
        //                     AND a.isdelete = 0
        //                     AND a.appv_complete = 1
        //                     AND (c.isSuj = 0 or c.isSuj is null)
        //                     ORDER BY a.id_ca_trans DESC";
        // } else{
            $strSqlGetData = "SELECT a.*,b.desc_trans,MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, SUBSTRING_INDEX(a.appv_flow_status, ',', -1) AS last_approver, c.isSuj  
                            FROM t_ca_trans a 
                            LEFT JOIN t_jenis_transaksi b ON a.request_type = b.id_trans

                            -- penambahan yudhis
                            LEFT JOIN tt_suj_transaction c on a.ca_guid = c.ca_guid

                            WHERE SUBSTRING_INDEX(a.appv_flow_status, ',', -1) = '".$getUserJobDetail."'
                            AND SUBSTRING_INDEX(a.appv_flow_status, ',', -1) <> 'END'
                            AND a.isdelete = 0
                            AND a.appv_complete = 0
                            AND (c.isSuj = 0 or c.isSuj is null)
                            ORDER BY a.id_ca_trans DESC";
        // }
        $getQuery = $this->db->query($strSqlGetData);
        
        $tempCountdata = 0;  
        $res .= '<br/>' ;
        
        // echo $this->db->last_query();
        
        foreach ($getQuery->result() as $rows) {
            //temp here get approval 
            $getFlow = explode(",", $rows->appv_flow_status);
            $getAvail = 0;
            //if(in_array($getUserJobDetail,$getFlow)){
            if (trim($getFlow[count($getFlow) - 1]) == trim($getUserJobDetail)) {

                // penambahan yudhis
                if ($rows->isSuj == 1 && $getUserLevelId == "MGR"){
                    // do nothing
                } else{
                    $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';
                    $setTCAno = '<span style="font-weight:bold;color:blue" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                
                    $res .= '' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold;text-decoration:underline" href="'.base_url().'index.php/apv_core/approval">' . $rows->requester_name . ' (' . $setTCAno .')'.'</a>';
                    //$res .= '<br/>' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold" href="'.base_url().'index.php/apv_core/approval">' . $rows->requester_name . '</a>';
                    $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Amount</b> : " . $helpers->currencyFormat($rows->request_amount);
                    $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Type</b> : " . $rows->desc_trans;
                    $res .= "<hr class='hrStyle'/>";
                    $tempCountdata++;
                }

             //    $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';
             //    $setTCAno = '<span style="font-weight:bold;color:blue" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                
             //    $res .= '' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold;text-decoration:underline" href="'.base_url().'index.php/apv_core/approval">' . $rows->requester_name . ' (' . $setTCAno .')'.'</a>';
             //    //$res .= '<br/>' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold" href="'.base_url().'index.php/apv_core/approval">' . $rows->requester_name . '</a>';
             //    $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Amount</b> : " . $helpers->currencyFormat($rows->request_amount);
             //    $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Type</b> : " . $rows->desc_trans;
                // $res .= "<hr class='hrStyle'/>";
             //    $tempCountdata++;
            }
        }
        if ($tempCountdata == 0) {            
            $res = '<br/><i>No Outstanding Record</i>' ;    
        }
       
        
        return $res;
    }

    function initCekSetApprovalNeedNonSUJ(){
        $helpers = new helpers();
        
        $res = '';
        $getUserSess = $this->session->userdata('userName');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail') != "" ? $this->session->userdata('sessionLevelDetail') : '-'; 
        /*
        $whereArry = array('c.approver' => $getUserJobDetail, 'c.appr_status' => 0, 'c.approve_type'=>'SET','a.isdelete'=>0 );
        
        $this->db->select('a.*, b.desc_trans,c.apprv_id, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->join('tr_trans_approve_status c','a.ca_guid = c.ca_guid', 'left')
                    ->where($whereArry)
                    ->order_by('input_datetime','desc')
                    //->limit(10)
                ;
        
        $getQuery = $this->db->get();
        */
        
        $strSqlGetData = "SELECT a.*,b.desc_trans,MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) AS last_approver  
                            FROM t_ca_trans a 
                            LEFT JOIN t_jenis_transaksi b ON a.request_type = b.id_trans

                            -- penambahan yudhis
                            LEFT JOIN tt_suj_transaction c on a.ca_guid = c.ca_guid

                            WHERE SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) = '".$getUserJobDetail."'
                            AND SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) <> 'END'
                            AND a.isdelete = 0
                            AND a.appv_complete = 3

                            -- penambahan yudhis
                            AND (c.isSuj is null or c.isSuj = 0)
                            
                            ORDER BY a.id_ca_trans DESC";
                            // die($strSqlGetData);
        $getQuery = $this->db->query($strSqlGetData);

        // echo $this->db->last_query();
        
        $tempCountdata = 0;
        $res .= '<br/>' ;
        foreach ($getQuery->result() as $rows) {
            //temp here get approval 
            $getFlow = explode(",", $rows->appv_flow_settle_status);
            $getAvail = 0;
            //if(in_array($getUserJobDetail,$getFlow)){       

            if (trim($getFlow[count($getFlow) - 1]) == trim($getUserJobDetail)) {
                $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';
                $setTCAno = '<span style="font-weight:bold;color:blue" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                
                $res .= '' . $btnViewStatus . "&nbsp;<b>From</b> : " . '<a class="topLink" style="font-weight:bold;text-decoration:underline" href="'.base_url().'index.php/apv_core/settleapproval">' . $rows->requester_name . ' (' . $setTCAno .')'.'</a>';
                $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Amount</b> : " . $helpers->currencyFormat($rows->request_amount);
                $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Settlement Amount</b> : " . $helpers->currencyFormat($rows->settled_amount);
                $res .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Type</b> : " . $rows->desc_trans;
                $res .= "<hr class='hrStyle'/>";
                $tempCountdata++;
            }
        }
        if ($tempCountdata == 0) {            
            $res = '<br/><i>No Outstanding Record</i>' ;    
        }

        return $res;
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
                                    from t_personel a left join t_job_title_detail b on a.job_title_detail_id = b.job_title_detail_id
                                    where a.job_title_detail_id is not null and a.job_title_detail_id <> ''";
        
        $getFastQuery = $fastConn->query($strSqlSyncDirectReport);
        if($getFastQuery->num_rows() > 0){            
            $execDelete = $this->db->query("DELETE FROM t_direct_report");
        }
        foreach($getFastQuery->result() as $fastRows){
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
		$getFastQuery->free_result();
        
        if($execData){
			/*
			$strSqlUpdate = "Update t_direct_report set direct_report_to_id = 'DIR' where sn_sap = '20361'"; //update approval pa UF
			$this->db->query($strSqlUpdate);
			
			$strSqlUpdate = "Update t_direct_report set level_id = 'MGR' where sn_sap = '20506'"; //update Level pa Chandra
			$this->db->query($strSqlUpdate);			
			$strSqlUpdate = "Update t_direct_report set direct_report_to_id = 'APD' where sn_sap = '20492'"; //update approval pa Gunadi -> Pa Chandra
			$this->db->query($strSqlUpdate);
			*/
			
            //echo "Update Data Complete";
        }
        
    }
	
	function adminAccessArray($getUserId){
		$adminArray = array('awirawan','');
		$resVal = FALSE;
		if (in_array($getUserId, $adminArray)) {
			$resVal = TRUE;
		}
		return $resVal;
	}
    
}

?>
