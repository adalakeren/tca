<?php include_once 'helpers.php'; ?>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of apv_core
 *
 * @author CKB
 */
class apv_core extends Controller{
    function apv_core() {
        parent::Controller();
        $this->load->database();
        $this->load->helper(array('url'));
        $this->load->library('session');
        $userLogon = $this->session->userdata('logged_in');
        if (!$userLogon) {
            redirect('admin_core/user_logout');
        }
    }    
    
    function approval(){
        $helpers = new helpers();
        
        $deptlist = $this->getDepApproval();
        $reqNameList = $this->getRequesterNameApproval();

        if (isset($_POST['txtSearch'])) {
            $getSearch = $_POST['txtSearch'];
        } else {
            if(isset($_GET['search'])){
                $getSearch = $_GET['search'];
            }else{
                $getSearch = "";
            }
        }

        if (isset($_GET['per_page'])) {
            $getPage = $_GET['per_page'];
        } else {
            $getPage = 0;
        }

        if ($getPage == 1) {
            $getPage = 0;
        }
        $start = $getPage;
        $end = 15;
        
        $throwData['header'] = "<img src=\"".base_url()."includes/icons/zones.png\" alt=\"\"/> Cash Advance Approval";
        $getTable = $this->initApprovalTable($start, $end, $getSearch);
        $throwData['txtSearch'] = $getSearch;
        $throwData['tblDatas'] = $getTable['tblRes'];

        $getTable2 = $this->initApprovalTableNonSUJ($start, $end, $getSearch);
        $throwData['tblDatas2'] = $getTable2['tblRes2'];

        $throwData['deptlist'] = $deptlist;
        $throwData['reqNameList'] = $reqNameList;
        $throwData['cmbCashType'] = $helpers->initCmbAllValue_cond2("cmbCashType", 
            "t_jenis_transaksi", "desc_trans", "id_trans", "",$this->session->userdata('userEmpID'));
        
        $this->load->view('apv_approval',$throwData);
    }
    
    function initApprovalTable($getStart, $getEnd, $getCondition){
      $helpers = new helpers();
      $tmplTbl = array (
                        'table_open'          => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:20px;margin-top:20px">',
                        'heading_row_start'   => '<tr>',
                        'heading_row_end'     => '</tr>',
                        'heading_cell_start'  => '<th>',
                        'heading_cell_end'    => '</th>',
                        'row_start'           => '<tr>',
                        'row_end'             => '</tr>',
                        'cell_start'          => '<td>',
                        'cell_end'            => '&nbsp;</td>',
                        'table_close'         => '</table>'
                      );

        $getUserSess = $this->session->userdata('userName');   
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');   
        
		
        $sqlMain = "SELECT a.*,
                             b.desc_trans,
                             d.*,
                             MONTH(a.date_request) AS bulan,
                             YEAR(a.date_request) AS tahun,
                             SUBSTRING_INDEX(a.appv_flow_status, ',', -1) AS last_approver,
                             c.apprv_id,
                             (CASE WHEN d.isSuj = 0 THEN d.origin 
                                  WHEN d.isSuj = 1 THEN e.origin_trip
                                  WHEN ISNULL(d.isSuj) THEN d.origin 
                             END) AS RealOrigin,
                            (CASE WHEN d.isSuj = 0 THEN d.destination
                                  WHEN d.isSuj = 1 THEN e.fleet_schedule 
                                  WHEN ISNULL(d.isSuj) THEN d.destination 
                             END
                            ) AS RealDestination,
                             (CASE WHEN d.isSuj = 0 THEN 'NON SUJ' WHEN d.isSuj = 1 THEN 'SUJ' WHEN ISNULL(d.isSuj) THEN '-' END) AS IsSuj, 
                             e.expired as ExpiredDate,
                             e.suj_id, 
                             (case WHEN (d.id_cost_type_rate = 0 and d.isSuj = 0) THEN 'No' WHEN (d.isSuj = 0 and d.id_cost_type_rate >= 0) THEN 'Yes' WHEN d.isSuj = 1 THEN 'No' end ) as IsExpired
                        FROM t_ca_trans a
                             LEFT JOIN t_jenis_transaksi b
                                ON a.request_type = b.id_trans
                             INNER JOIN tr_trans_approve_status c
                                ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'STD' 
                             LEFT JOIN tt_suj_transaction d
                                ON c.ca_guid = d.ca_guid
                             LEFT JOIN tr_cost_type_rates e 
                                ON d.id_cost_type_rate = e.ratesid ";

        $sqlWhere = "WHERE SUBSTRING_INDEX(a.appv_flow_status, ',', -1) = '".$getUserJobDetail."'
                    AND SUBSTRING_INDEX(a.appv_flow_status, ',', -1) <> 'END'
                    AND a.isdelete = 0
                    AND a.appv_complete = 0
                    AND d.isSuj = 1 ";
        $sqlOrder = "ORDER BY a.id_ca_trans DESC ";

        $requester_dept = $this->uri->segment(3);
        $requester_name = $this->uri->segment(4);
        $request_type = $this->uri->segment(5);

        $requester_name = str_replace("'", "\'", $requester_name);

        if (($requester_dept != null) && ($requester_dept != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.requester_dept = '". $requester_dept . "' ";
        }

        if (($requester_name != null) && ($requester_name != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.requester_name = '". $requester_name ."' ";
        }

        if (($request_type != null) && ($request_type != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.request_type = '". $request_type ."' ";
        }

        $strSqlGetData = $sqlMain . $sqlWhere . $sqlOrder;

		$getQuery = $this->db->query($strSqlGetData);
		// echo $strSqlGetData;exit();
		$getRow = $getQuery->num_rows();        
        //======================================================================
        
        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:10px">'.'<input type="checkbox" id="checkBoxAll" />'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">'.''.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px">'.'Requester'.$tmplTbl['heading_cell_end'];                
        $strTblRes .= '<th style="">'.'Type'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Amount'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Origin'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Destination'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Is Suj'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Expired Date'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Is Expired'.$tmplTbl['heading_cell_end'];
        //$strTblRes .= $tmplTbl['heading_cell_start'].'Purpose'.$tmplTbl['heading_cell_end'];    
        $strTblRes .= '<th style="width:50px">'.'Attach'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px">'.'Status'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:50px">'.'Approve'.$tmplTbl['heading_cell_end'];        
        $strTblRes .= '<th style="width:50px">'.'Reject'.$tmplTbl['heading_cell_end'];          
        $strTblRes .= $tmplTbl['row_end'];

        if($getQuery->num_rows() == 0 || $getUserJobDetail == '' || $getUserJobDetail == null){
            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td style="text-align:center;" colspan="15">'.'No Record' .$tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];
        }else{
            $tempCountdata = 0;            
            foreach ($getQuery->result() as $rows)
            {
                $editDeleteButton = "";
                $checkboxApproval = '<input type="checkbox" id="" name="apprv_id[]" class="checkApp" value="'.$rows->apprv_id.'" />';
                $btnViewStatus = '<input title="View" type="image" src="'.base_url().'includes/icons/bubbles.png" onClick="cashAdvanceView(\''.$rows->id_ca_trans.'\');" />';
                
                //temp here get approval 
                $getFlow = explode(",",$rows->appv_flow_status);
                $getAvail = 0;
                //if(in_array($getUserJobDetail,$getFlow)){
                if(trim($getFlow[count($getFlow)-1]) == trim($getUserJobDetail)){
                    
                    $getAvail = 1; //here if need approval from current user
                    $appvButton = '<input title="Approve" type="image" src="'.base_url().'includes/icons/ticks.png" onClick="javascript: if (confirm(\'Approve?\')) {approve(\''.$rows->apprv_id.' \');}" />';
                    $blockButton = '<input title="Reject" type="image" src="'.base_url().'includes/icons/block.png" onClick="javascript: if (confirm(\'Reject?\')) {reject(\''.$rows->apprv_id.' \');}" />';
                //=================                
                    $strTblRes .= $tmplTbl['row_start'];
                    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'. $checkboxApproval .$tmplTbl['cell_end'];
                    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'. $btnViewStatus .$tmplTbl['cell_end'];
                    
                    $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                    $strTblRes .= '<td style="text-align:center;background-color:#ffffcc">' . $setTCAno . '<br/><i style="color:gray">(' . $helpers->convertDateToSimple($rows->date_request) . ')</i>' . $tmplTbl['cell_end'];

                    //$strTblRes .= '<td style="text-align:center;">'. $helpers->convertDateToSimple($rows->date_request) .$tmplTbl['cell_end'];
                    
                    $strTblRes .= '<td style="text-align:center;">'.'&nbsp;'.$rows->requester_name.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">'.$rows->desc_trans.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$helpers->currencyFormat($rows->request_amount).$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->RealOrigin.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->RealDestination.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->IsSuj. (($rows->IsSuj == "SUJ") ? "(". $rows->suj_id .")" : "").$tmplTbl['cell_end']; //
                    $strTblRes .= '<td style="text-align:right;">'.$rows->ExpiredDate.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->IsExpired.$tmplTbl['cell_end'];
                    //d.isSuj, e.suj_id, e.expired 
                    //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                    $attachFile = "";
                    if ($rows->upload_f1 != "") {
                        $attachFile = '<a href="' . base_url() . '/_UploadedFiles/' . $rows->upload_f1 . '"><img src="' . base_url() . 'includes/icons/attachment16.png" alt=""/></a>';
                    }
                    $strTblRes .= '<td style="text-align:center;">' . $attachFile . $tmplTbl['cell_end'];

                    $getStatus = "<span class=\"redSpan\">Uncomplete Approval</span>";
                    if($rows->appv_complete == "1"){$getStatus = "<span class=\"greenSpan\">Approved </span><br/>(<b>Settlement Required</b>)";$editDeleteButton = '<input title="Settlement" type="image" src="'.base_url().'includes/icons/page_white_edit.png" onClick="window.location.href=\''.base_url().'index.php/ca_core/settleCash/'.$rows->id_ca_trans.' \'" />';}
                    elseif($rows->appv_complete == "2"){$getStatus = "<span class=\"redSpan\">Rejected</span>";$editDeleteButton = '<input title="Rejected" type="image" src="'.base_url().'includes/icons/block.png" />';}
                    elseif($rows->appv_complete == "3"){$getStatus = "<span class=\"redSpan\">Waiting Settle Approval</span>";$editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="'.base_url().'includes/icons/next.png" />';}
                    elseif($rows->appv_complete == "4"){$getStatus = "<span class=\"blueSpan\">Settled</span>";$editDeleteButton = '<input title="Settled" type="image" src="'.base_url().'includes/icons/ticks.png" />';}

                    $strTblRes .= '<td style="text-align:center;">'. $getStatus .$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="width:50px;text-align:center;"><div id="dvApprove'.$rows->apprv_id.'" style="padding-top:6px">'.$appvButton.'</div>'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="width:50px;text-align:center;"><div id="dvReject'.$rows->apprv_id.'" style="padding-top:6px">'.$blockButton.'</div>'.$tmplTbl['cell_end'];
                    $strTblRes .= $tmplTbl['row_end'];
                    
                    $tempCountdata++;
                }
            }
            if($tempCountdata==0){
                $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= '<td style="text-align:center;" colspan="15">'.'No Record' .$tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;  
    }

    function initApprovalTableNonSUJ($getStart, $getEnd, $getCondition){
      $helpers = new helpers();
      $tmplTbl = array (
                        'table_open'          => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:20px">',
                        'heading_row_start'   => '<tr>',
                        'heading_row_end'     => '</tr>',
                        'heading_cell_start'  => '<th>',
                        'heading_cell_end'    => '</th>',
                        'row_start'           => '<tr>',
                        'row_end'             => '</tr>',
                        'cell_start'          => '<td>',
                        'cell_end'            => '&nbsp;</td>',
                        'table_close'         => '</table>'
                      );

        $getUserSess = $this->session->userdata('userName');   
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');   
        
        
        $sqlMain = "SELECT a.*,
                             b.desc_trans,
                             d.*,
                             MONTH(a.date_request) AS bulan,
                             YEAR(a.date_request) AS tahun,
                             SUBSTRING_INDEX(a.appv_flow_status, ',', -1) AS last_approver,
                             c.apprv_id,
                             (CASE WHEN d.isSuj = 0 THEN d.origin 
                                  WHEN d.isSuj = 1 THEN e.origin_trip
                                  WHEN ISNULL(d.isSuj) THEN d.origin 
                             END) AS RealOrigin,
                            (CASE WHEN d.isSuj = 0 THEN d.destination
                                  WHEN d.isSuj = 1 THEN e.fleet_schedule 
                                  WHEN ISNULL(d.isSuj) THEN d.destination 
                             END
                            ) AS RealDestination,
                             (CASE WHEN d.isSuj = 0 THEN 'NON SUJ' WHEN d.isSuj = 1 THEN 'SUJ' WHEN ISNULL(d.isSuj) THEN '-' END) AS IsSuj, 
                             e.expired as ExpiredDate,
                             e.suj_id, 
                             (case WHEN (d.id_cost_type_rate = 0 and d.isSuj = 0) THEN 'No' WHEN (d.isSuj = 0 and d.id_cost_type_rate >= 0) THEN 'Yes' WHEN d.isSuj = 1 THEN 'No' end ) as IsExpired
                        FROM t_ca_trans a
                             LEFT JOIN t_jenis_transaksi b
                                ON a.request_type = b.id_trans
                             INNER JOIN tr_trans_approve_status c
                                ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'STD' 
                             LEFT JOIN tt_suj_transaction d
                                ON c.ca_guid = d.ca_guid
                             LEFT JOIN tr_cost_type_rates e 
                                ON d.id_cost_type_rate = e.ratesid ";

        $sqlWhere = "WHERE SUBSTRING_INDEX(a.appv_flow_status, ',', -1) = '".$getUserJobDetail."'
                    AND SUBSTRING_INDEX(a.appv_flow_status, ',', -1) <> 'END'
                    AND a.isdelete = 0
                    AND a.appv_complete = 0
                    AND (d.isSuj = 0 or d.isSuj is null) ";
        $sqlOrder = "ORDER BY a.id_ca_trans DESC ";

        $requester_dept = $this->uri->segment(3);
        $requester_name = $this->uri->segment(4);
        $request_type = $this->uri->segment(5);

        $requester_name = str_replace("'", "\'", $requester_name);

        if (($requester_dept != null) && ($requester_dept != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.requester_dept = '". $requester_dept . "' ";
        }

        if (($requester_name != null) && ($requester_name != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.requester_name = '". $requester_name ."' ";
        }

        if (($request_type != null) && ($request_type != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.request_type = '". $request_type ."' ";
        }

        $strSqlGetData = $sqlMain . $sqlWhere . $sqlOrder;

        $getQuery = $this->db->query($strSqlGetData);
        // echo $strSqlGetData;exit();
        $getRow = $getQuery->num_rows();        
        //======================================================================
        
        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:10px">'.'<input type="checkbox" id="checkBoxAll2" />'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">'.''.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px">'.'Requester'.$tmplTbl['heading_cell_end'];                
        $strTblRes .= '<th style="">'.'Type'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Amount'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Origin'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Destination'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Is Suj'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Expired Date'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Is Expired'.$tmplTbl['heading_cell_end'];
        //$strTblRes .= $tmplTbl['heading_cell_start'].'Purpose'.$tmplTbl['heading_cell_end'];    
        $strTblRes .= '<th style="width:50px">'.'Attach'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px">'.'Status'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:50px">'.'Approve'.$tmplTbl['heading_cell_end'];        
        $strTblRes .= '<th style="width:50px">'.'Reject'.$tmplTbl['heading_cell_end'];          
        $strTblRes .= $tmplTbl['row_end'];

        if($getQuery->num_rows() == 0 || $getUserJobDetail == '' || $getUserJobDetail == null){
            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td style="text-align:center;" colspan="15">'.'No Record' .$tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];
        }else{
            $tempCountdata = 0;            
            foreach ($getQuery->result() as $rows)
            {
                $editDeleteButton = "";
                $checkboxApproval = '<input type="checkbox" id="" name="apprv_id[]" class="checkApp2" value="'.$rows->apprv_id.'" />';
                $btnViewStatus = '<input title="View" type="image" src="'.base_url().'includes/icons/bubbles.png" onClick="cashAdvanceView(\''.$rows->id_ca_trans.'\');" />';
                
                //temp here get approval 
                $getFlow = explode(",",$rows->appv_flow_status);
                $getAvail = 0;
                //if(in_array($getUserJobDetail,$getFlow)){
                if(trim($getFlow[count($getFlow)-1]) == trim($getUserJobDetail)){
                    
                    $getAvail = 1; //here if need approval from current user
                    $appvButton = '<input title="Approve" type="image" src="'.base_url().'includes/icons/ticks.png" onClick="javascript: if (confirm(\'Approve?\')) {approve(\''.$rows->apprv_id.' \');}" />';
                    $blockButton = '<input title="Reject" type="image" src="'.base_url().'includes/icons/block.png" onClick="javascript: if (confirm(\'Reject?\')) {reject(\''.$rows->apprv_id.' \');}" />';
                //=================                
                    $strTblRes .= $tmplTbl['row_start'];
                    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'. $checkboxApproval .$tmplTbl['cell_end'];
                    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'. $btnViewStatus .$tmplTbl['cell_end'];
                    
                    $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                    $strTblRes .= '<td style="text-align:center;background-color:#ffffcc">' . $setTCAno . '<br/><i style="color:gray">(' . $helpers->convertDateToSimple($rows->date_request) . ')</i>' . $tmplTbl['cell_end'];

                    //$strTblRes .= '<td style="text-align:center;">'. $helpers->convertDateToSimple($rows->date_request) .$tmplTbl['cell_end'];
                    
                    $strTblRes .= '<td style="text-align:center;">'.'&nbsp;'.$rows->requester_name.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">'.$rows->desc_trans.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$helpers->currencyFormat($rows->request_amount).$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->RealOrigin.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->RealDestination.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->IsSuj. (($rows->IsSuj == "SUJ") ? "(". $rows->suj_id .")" : "").$tmplTbl['cell_end']; //
                    $strTblRes .= '<td style="text-align:right;">'.$rows->ExpiredDate.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->IsExpired.$tmplTbl['cell_end'];
                    //d.isSuj, e.suj_id, e.expired 
                    //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                    $attachFile = "";
                    if ($rows->upload_f1 != "") {
                        $attachFile = '<a href="' . base_url() . '/_UploadedFiles/' . $rows->upload_f1 . '"><img src="' . base_url() . 'includes/icons/attachment16.png" alt=""/></a>';
                    }
                    $strTblRes .= '<td style="text-align:center;">' . $attachFile . $tmplTbl['cell_end'];

                    $getStatus = "<span class=\"redSpan\">Uncomplete Approval</span>";
                    if($rows->appv_complete == "1"){$getStatus = "<span class=\"greenSpan\">Approved </span><br/>(<b>Settlement Required</b>)";$editDeleteButton = '<input title="Settlement" type="image" src="'.base_url().'includes/icons/page_white_edit.png" onClick="window.location.href=\''.base_url().'index.php/ca_core/settleCash/'.$rows->id_ca_trans.' \'" />';}
                    elseif($rows->appv_complete == "2"){$getStatus = "<span class=\"redSpan\">Rejected</span>";$editDeleteButton = '<input title="Rejected" type="image" src="'.base_url().'includes/icons/block.png" />';}
                    elseif($rows->appv_complete == "3"){$getStatus = "<span class=\"redSpan\">Waiting Settle Approval</span>";$editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="'.base_url().'includes/icons/next.png" />';}
                    elseif($rows->appv_complete == "4"){$getStatus = "<span class=\"blueSpan\">Settled</span>";$editDeleteButton = '<input title="Settled" type="image" src="'.base_url().'includes/icons/ticks.png" />';}

                    $strTblRes .= '<td style="text-align:center;">'. $getStatus .$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="width:50px;text-align:center;"><div id="dvApprove'.$rows->apprv_id.'" style="padding-top:6px">'.$appvButton.'</div>'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="width:50px;text-align:center;"><div id="dvReject'.$rows->apprv_id.'" style="padding-top:6px">'.$blockButton.'</div>'.$tmplTbl['cell_end'];
                    $strTblRes .= $tmplTbl['row_end'];
                    
                    $tempCountdata++;
                }
            }
            if($tempCountdata==0){
                $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= '<td style="text-align:center;" colspan="15">'.'No Record' .$tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes2'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;  
    }

    
    function settleapproval(){
        if (isset($_POST['txtSearch'])) {
            $getSearch = $_POST['txtSearch'];
        } else {
            if(isset($_GET['search'])){
                $getSearch = $_GET['search'];
            }else{
                $getSearch = "";
            }
        }

        if (isset($_GET['per_page'])) {
            $getPage = $_GET['per_page'];
        } else {
            $getPage = 0;
        }

        if ($getPage == 1) {
            $getPage = 0;
        }
        $start = $getPage;
        $end = 15;
        
        $deptlist = $this->getDepSettle();
        $reqnamelist = $this->getRequesterNameSettle();
        $helpers = new helpers();

        $throwData['header'] = "<img src=\"".base_url()."includes/icons/zones.png\" alt=\"\"/> Cash Advance Settlement Approval";
        $getTable = $this->initSettleApprovalTable($start, $end, $getSearch);
        $throwData['txtSearch'] = $getSearch;
        $throwData['tblDatas'] = $getTable['tblRes'];

        $getTable2 = $this->initSettleApprovalTableNonSUJ($start, $end, $getSearch);
        $throwData['tblDatas2'] = $getTable2['tblRes2'];

        $throwData['deptlist'] = $deptlist;
        $throwData['reqnamelist'] = $reqnamelist;
        $throwData['cmbCashType'] = $helpers->initCmbAllValue_cond2("cmbCashType", 
            "t_jenis_transaksi", "desc_trans", "id_trans", "",$this->session->userdata('userEmpID'));

        $this->load->view('apv_approval_settle',$throwData);
    }

    function getDepApproval()
    {
        $helpers = new helpers();

        $getUserSess = $this->session->userdata('userName');   
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail'); 

        $strSqlGetData = "SELECT DISTINCT requester_dept
                        FROM t_ca_trans a
                             LEFT JOIN t_jenis_transaksi b
                             ON a.request_type = b.id_trans
                             INNER JOIN tr_trans_approve_status c
                             ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'STD'
                        WHERE SUBSTRING_INDEX(a.appv_flow_status, ',', -1) = '".$getUserJobDetail."'
                             AND SUBSTRING_INDEX(a.appv_flow_status, ',', -1) <> 'END'
                             AND a.isdelete = 0
                             AND a.appv_complete = 0
                        ORDER BY a.id_ca_trans DESC ";

        $getQuery = $this->db->query($strSqlGetData);

        return $getQuery;
    }

    function getRequesterNameApproval()
    {
        $helpers = new helpers();

        $getUserSess = $this->session->userdata('userName');   
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail'); 

        $strSqlGetData = "SELECT DISTINCT requester_name
                        FROM t_ca_trans a
                             LEFT JOIN t_jenis_transaksi b
                                ON a.request_type = b.id_trans
                             INNER JOIN tr_trans_approve_status c
                                ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'STD' 
                             LEFT JOIN tt_suj_transaction d
                                ON c.ca_guid = d.ca_guid
                             LEFT JOIN tr_cost_type_rates e 
                                ON d.id_cost_type_rate = e.ratesid 
                        WHERE SUBSTRING_INDEX(a.appv_flow_status, ',', -1) = '".$getUserJobDetail."'
                             AND SUBSTRING_INDEX(a.appv_flow_status, ',', -1) <> 'END'
                             AND a.isdelete = 0 
                             AND a.appv_complete = 0 
                             AND a.requester_name IS NOT NULL
                        ORDER BY requester_name";

        $getQuery = $this->db->query($strSqlGetData);

        return $getQuery;
    }

    function getDepSettle()
    {
        $helpers = new helpers();

        $getUserSess = $this->session->userdata('userName');   
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');

        $strSqlGetData = "SELECT DISTINCT requester_dept
                        FROM t_ca_trans a
                             LEFT JOIN t_jenis_transaksi b
                             ON a.request_type = b.id_trans
                             INNER JOIN tr_trans_approve_status c
                             ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'SET'
                        WHERE SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) = '".$getUserJobDetail."'
                             AND SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) <> 'END'
                             AND a.isdelete = 0
                             AND a.appv_complete = 3
                             AND a.requester_name IS NOT NULL
                        ORDER BY a.id_ca_trans DESC";
                        
        $getQuery = $this->db->query($strSqlGetData);

        return $getQuery;
    }

    function getRequesterNameSettle()
    {
        $helpers = new helpers();

        $getUserSess = $this->session->userdata('userName');   
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail'); 

        $strSqlGetData = "SELECT DISTINCT requester_name
                        FROM t_ca_trans a
                             LEFT JOIN t_jenis_transaksi b
                             ON a.request_type = b.id_trans
                             INNER JOIN tr_trans_approve_status c
                             ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'SET'
                        WHERE SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) = '".$getUserJobDetail."'
                             AND SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) <> 'END'
                             AND a.isdelete = 0
                             AND a.appv_complete = 3
                             AND a.requester_name IS NOT NULL
                        ORDER BY requester_name";

        $getQuery = $this->db->query($strSqlGetData);

        return $getQuery;
    }
    
    function initSettleApprovalTable($getStart, $getEnd, $getCondition){
      $helpers = new helpers();
        
      $tmplTbl = array (
                        'table_open'          => '<table width="100%" border="0" cellpadding="0" cellspacing="0">',
                        'heading_row_start'   => '<tr>',
                        'heading_row_end'     => '</tr>',
                        'heading_cell_start'  => '<th>',
                        'heading_cell_end'    => '</th>',
                        'row_start'           => '<tr>',
                        'row_end'             => '</tr>',
                        'cell_start'          => '<td>',
                        'cell_end'            => '&nbsp;</td>',
                        'table_close'         => '</table>'
                      );

        $getUserSess = $this->session->userdata('userName');
        $userID = $this->session->userdata('userEmpID');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');   
        $whereArry = array('c.approver' => $getUserJobDetail, 'c.appr_status' => 0, 'c.approve_type'=>'SET','a.isdelete'=>0 );
        
		// $sqlMain = "SELECT a.*,
  //                    b.desc_trans,
  //                    MONTH(a.date_request) AS bulan,
  //                    YEAR(a.date_request) AS tahun,
  //                    SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) AS last_approver,
  //                    c.apprv_id,
  //                    d.*,
  //                    (CASE WHEN d.isSuj = 0 THEN d.origin 
  //                         WHEN d.isSuj = 1 THEN e.origin_trip
  //                         WHEN ISNULL(d.isSuj) THEN d.origin 
  //                    END) AS RealOrigin,
  //                   (CASE WHEN d.isSuj = 0 THEN d.destination
  //                         WHEN d.isSuj = 1 THEN e.fleet_schedule 
  //                         WHEN ISNULL(d.isSuj) THEN d.destination 
  //                    END
  //                   ) AS RealDestination
  //                    FROM t_ca_trans a
  //                    INNER JOIN t_jenis_transaksi b
  //                    ON a.request_type = b.id_trans
  //                    INNER JOIN tr_trans_approve_status c
  //                    ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'SET' 
  //                       AND c.appr_userid = '".$getUserSess ."'
  //                    INNER JOIN tt_suj_transaction d
  //                       ON c.ca_guid = d.ca_guid 
  //                    LEFT JOIN tr_cost_type_rates e 
  //                               ON d.id_cost_type_rate = e.ratesid ";

        $sqlMain = "SELECT a.*,
                     b.desc_trans,
                     MONTH(a.date_request) AS bulan,
                     YEAR(a.date_request) AS tahun,
                     SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) AS last_approver,
                     c.apprv_id,
                     d.*,
                     e.suj_id,
                    (CASE WHEN d.isSuj = 0 THEN 'NON SUJ' WHEN d.isSuj = 1 THEN 'SUJ' WHEN ISNULL(d.isSuj) THEN '-' END) AS IsSuj,
                     (CASE WHEN d.isSuj = 0 THEN d.origin 
                          WHEN d.isSuj = 1 THEN e.origin_trip
                          WHEN ISNULL(d.isSuj) THEN d.origin 
                     END) AS RealOrigin,
                    (CASE WHEN d.isSuj = 0 THEN d.destination
                          WHEN d.isSuj = 1 THEN e.fleet_schedule 
                          WHEN ISNULL(d.isSuj) THEN d.destination 
                     END
                    ) AS RealDestination
                     FROM t_ca_trans a
                     LEFT JOIN t_jenis_transaksi b
                     ON a.request_type = b.id_trans
                     INNER JOIN tr_trans_approve_status c
                     ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'SET' 
                     LEFT JOIN tt_suj_transaction d
                        ON c.ca_guid = d.ca_guid 
                     LEFT JOIN tr_cost_type_rates e 
                                ON d.id_cost_type_rate = e.ratesid ";

        $sqlWhere = "WHERE SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) = '".$getUserJobDetail."'
                             AND SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) <> 'END'
                             AND a.isdelete = 0
                             AND a.appv_complete = 3
                             AND d.isSuj = 1 ";
        $sqlOrder = "ORDER BY a.id_ca_trans DESC ";

		$requester_dept = $this->uri->segment(3);
        $requester_name = $this->uri->segment(4);
        $request_type = $this->uri->segment(5);

        $requester_name = str_replace("'", "\'", $requester_name);

        if (($requester_dept != null) && ($requester_dept != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.requester_dept = '". $requester_dept . "' ";

        }

        if (($requester_name != null) && ($requester_name != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.requester_name = '". $requester_name ."' ";
        }

        if (($request_type != null) && ($request_type != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.request_type = '". $request_type ."' ";
        }
		
        $strSqlGetData = $sqlMain . $sqlWhere . $sqlOrder;
        //echo $strSqlGetData;exit();
		$getQuery = $this->db->query($strSqlGetData);
        
        //here get rows=========================================================
        //$getRow = 0;
		/*
        $this->db->select('a.*, b.desc_trans,c.apprv_id, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->join('tr_trans_approve_status c','a.ca_guid = c.ca_guid', 'left')
                    ->where($whereArry);
        if (count($setSearhArray) > 0) {
            $setFirst = true;
            foreach ($setSearhArray as $serchKey) {
                if($setFirst){$this->db->like(array($setSearchField => $serchKey));$setFirst=false;}
                else{$this->db->or_like(array($setSearchField => $serchKey));}
            }
        }
        $this->db->order_by('input_datetime', 'desc');
        $getRow = $this->db->get()->num_rows();   
		*/
		$getRow = $getQuery->num_rows();   	
        //======================================================================

        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:10px">'.'<input type="checkbox" id="checkBoxAll" />'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">'.''.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px">'.'Requester'.$tmplTbl['heading_cell_end'];                
        $strTblRes .= '<th style="">'.'Type'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Amount'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Settle Amount'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Origin'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Destination'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Is Suj'.$tmplTbl['heading_cell_end'];
        //$strTblRes .= $tmplTbl['heading_cell_start'].'Purpose'.$tmplTbl['heading_cell_end'];  
        $strTblRes .= '<th style="width:50px">'.'Attach'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px">'.'Status'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:50px">'.'Approve'.$tmplTbl['heading_cell_end'];        
        $strTblRes .= '<th style="width:50px">'.'Reject'.$tmplTbl['heading_cell_end'];        
        $strTblRes .= $tmplTbl['row_end'];

        if($getQuery->num_rows() == 0 || $getUserJobDetail == '' || $getUserJobDetail == null){
            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td style="text-align:center;" colspan="14">'.'No Record' .$tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];
        }else{
            $tempCountdata = 0;
			// print_r($getQuery);die();
            foreach ($getQuery->result() as $rows)
            {
                $editDeleteButton = "";
                $checkboxApproval = '<input type="checkbox" id="" name="apprv_id[]" class="checkApp" value="'.$rows->apprv_id.'" />';
                $btnViewStatus = '<input title="View" type="image" src="'.base_url().'includes/icons/bubbles.png" onClick="cashAdvanceView(\''.$rows->id_ca_trans.'\');" />';
                
                //temp here get approval 
                $getFlow = explode(",",$rows->appv_flow_settle_status);
                $getAvail = 0;                
                //if(in_array($getUserJobDetail,$getFlow)){       
                
                if(trim($getFlow[count($getFlow)-1]) == trim($getUserJobDetail)){
                    $getAvail = 1; //here if need approval from current user
                    $appvButton = '<input title="Approve" type="image" src="'.base_url().'includes/icons/ticks.png" onClick="javascript: if (confirm(\'Approve?\')) {approve(\''.$rows->apprv_id.' \');}" />';
                    $blockButton = '<input title="Reject" type="image" src="'.base_url().'includes/icons/block.png" onClick="javascript: if (confirm(\'Reject?\')) {reject(\''.$rows->apprv_id.' \');}" />';
                //=================                
                    $strTblRes .= $tmplTbl['row_start'];
                    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'. $checkboxApproval .$tmplTbl['cell_end'];
                    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'. $btnViewStatus .$tmplTbl['cell_end'];
                    //$strTblRes .= '<td style="text-align:center;">'. $helpers->convertDateToSimple($rows->date_request) .$tmplTbl['cell_end'];
                    
                    $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                    $strTblRes .= '<td style="text-align:center;background-color:#ffffcc">' . $setTCAno . '<br/><i style="color:gray">(' . $helpers->convertDateToSimple($rows->date_request) . ')</i>' . $tmplTbl['cell_end'];

                    $strTblRes .= '<td style="text-align:center;">'.'&nbsp;'.$rows->requester_name.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">'.$rows->desc_trans.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$helpers->currencyFormat($rows->request_amount).' &nbsp;'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$helpers->currencyFormat($rows->settled_amount).' &nbsp;'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->RealOrigin.' &nbsp;'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->RealDestination.' &nbsp;'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->IsSuj. (($rows->IsSuj == "SUJ") ? "(". $rows->suj_id .")" : "").$tmplTbl['cell_end'];
                    //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                    $attachFile = "";
                    if($rows->upload_f1 != ""){
                        $attachFile = '<a href="'.base_url().'/_UploadedFiles/'.$rows->upload_f1.'"><img src="'.base_url().'includes/icons/attachment16.png" alt=""/></a>';
                    }
                    $strTblRes .= '<td style="text-align:center;">'. $attachFile .$tmplTbl['cell_end'];

                    $getStatus = "<span class=\"redSpan\">Uncomplete Approval</span>";
                    if($rows->appv_complete == "1"){$getStatus = "<span class=\"greenSpan\">Approved </span><br/>(<b>Settlement Required</b>)";$editDeleteButton = '<input title="Settlement" type="image" src="'.base_url().'includes/icons/page_white_edit.png" onClick="window.location.href=\''.base_url().'index.php/ca_core/settleCash/'.$rows->id_ca_trans.' \'" />';}
                    elseif($rows->appv_complete == "2"){$getStatus = "<span class=\"redSpan\">Rejected</span>";$editDeleteButton = '<input title="Rejected" type="image" src="'.base_url().'includes/icons/block.png" />';}
                    elseif($rows->appv_complete == "3"){$getStatus = "<span class=\"redSpan\">Waiting Settle Approval</span>";$editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="'.base_url().'includes/icons/next.png" />';}
                    elseif($rows->appv_complete == "4"){$getStatus = "<span class=\"blueSpan\">Settled</span>";$editDeleteButton = '<input title="Settled" type="image" src="'.base_url().'includes/icons/ticks.png" />';}

                    $strTblRes .= '<td style="text-align:center;">'. $getStatus .$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="width:50px;text-align:center;"><div id="dvApprove'.$rows->apprv_id.'" style="padding-top:6px">'.$appvButton.'</div>'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="width:50px;text-align:center;"><div id="dvReject'.$rows->apprv_id.'" style="padding-top:6px">'.$blockButton.'</div>'.$tmplTbl['cell_end'];
                    $strTblRes .= $tmplTbl['row_end'];
                    
                    $tempCountdata++;
                }
            }
            if($tempCountdata==0){
                $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= '<td style="text-align:center;" colspan="14">'.'No Record' .$tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;  
    }


    function initSettleApprovalTableNonSUJ($getStart, $getEnd, $getCondition){
      $helpers = new helpers();
        
      $tmplTbl = array (
                        'table_open'          => '<table width="100%" border="0" cellpadding="0" cellspacing="0">',
                        'heading_row_start'   => '<tr>',
                        'heading_row_end'     => '</tr>',
                        'heading_cell_start'  => '<th>',
                        'heading_cell_end'    => '</th>',
                        'row_start'           => '<tr>',
                        'row_end'             => '</tr>',
                        'cell_start'          => '<td>',
                        'cell_end'            => '&nbsp;</td>',
                        'table_close'         => '</table>'
                      );

        $getUserSess = $this->session->userdata('userName');
        $userID = $this->session->userdata('userEmpID');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');   
        $whereArry = array('c.approver' => $getUserJobDetail, 'c.appr_status' => 0, 'c.approve_type'=>'SET','a.isdelete'=>0 );
        
        // $sqlMain = "SELECT a.*,
  //                    b.desc_trans,
  //                    MONTH(a.date_request) AS bulan,
  //                    YEAR(a.date_request) AS tahun,
  //                    SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) AS last_approver,
  //                    c.apprv_id,
  //                    d.*,
  //                    (CASE WHEN d.isSuj = 0 THEN d.origin 
  //                         WHEN d.isSuj = 1 THEN e.origin_trip
  //                         WHEN ISNULL(d.isSuj) THEN d.origin 
  //                    END) AS RealOrigin,
  //                   (CASE WHEN d.isSuj = 0 THEN d.destination
  //                         WHEN d.isSuj = 1 THEN e.fleet_schedule 
  //                         WHEN ISNULL(d.isSuj) THEN d.destination 
  //                    END
  //                   ) AS RealDestination
  //                    FROM t_ca_trans a
  //                    INNER JOIN t_jenis_transaksi b
  //                    ON a.request_type = b.id_trans
  //                    INNER JOIN tr_trans_approve_status c
  //                    ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'SET' 
  //                       AND c.appr_userid = '".$getUserSess ."'
  //                    INNER JOIN tt_suj_transaction d
  //                       ON c.ca_guid = d.ca_guid 
  //                    LEFT JOIN tr_cost_type_rates e 
  //                               ON d.id_cost_type_rate = e.ratesid ";

        $sqlMain = "SELECT a.*,
                     b.desc_trans,
                     MONTH(a.date_request) AS bulan,
                     YEAR(a.date_request) AS tahun,
                     SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) AS last_approver,
                     c.apprv_id,
                     d.*,
                     e.suj_id,
                    (CASE WHEN d.isSuj = 0 THEN 'NON SUJ' WHEN d.isSuj = 1 THEN 'SUJ' WHEN ISNULL(d.isSuj) THEN '-' END) AS IsSuj,
                     (CASE WHEN d.isSuj = 0 THEN d.origin 
                          WHEN d.isSuj = 1 THEN e.origin_trip
                          WHEN ISNULL(d.isSuj) THEN d.origin 
                     END) AS RealOrigin,
                    (CASE WHEN d.isSuj = 0 THEN d.destination
                          WHEN d.isSuj = 1 THEN e.fleet_schedule 
                          WHEN ISNULL(d.isSuj) THEN d.destination 
                     END
                    ) AS RealDestination
                     FROM t_ca_trans a
                     LEFT JOIN t_jenis_transaksi b
                     ON a.request_type = b.id_trans
                     LEFT JOIN tr_trans_approve_status c
                     ON c.ca_guid = a.ca_guid AND c.approver = '".$getUserJobDetail."' AND c.approve_type = 'SET' 
                     LEFT JOIN tt_suj_transaction d
                        ON c.ca_guid = d.ca_guid 
                     LEFT JOIN tr_cost_type_rates e 
                                ON d.id_cost_type_rate = e.ratesid ";

        $sqlWhere = "WHERE SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) = '".$getUserJobDetail."'
                             AND SUBSTRING_INDEX(a.appv_flow_settle_status, ',', -1) <> 'END'
                             AND a.isdelete = 0
                             AND a.appv_complete = 3
                             AND (d.isSuj = 0 or d.isSuj IS NULL) ";
        $sqlOrder = "ORDER BY a.id_ca_trans DESC ";

        $requester_dept = $this->uri->segment(3);
        $requester_name = $this->uri->segment(4);
        $request_type = $this->uri->segment(5);

        $requester_name = str_replace("'", "\'", $requester_name);

        if (($requester_dept != null) && ($requester_dept != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.requester_dept = '". $requester_dept . "' ";

        }

        if (($requester_name != null) && ($requester_name != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.requester_name = '". $requester_name ."' ";
        }

        if (($request_type != null) && ($request_type != "ALL"))
        {
            $sqlWhere = $sqlWhere . "AND a.request_type = '". $request_type ."' ";
        }
        
        $strSqlGetData = $sqlMain . $sqlWhere . $sqlOrder;
        // echo $strSqlGetData;exit();
        $getQuery = $this->db->query($strSqlGetData);
        
        //here get rows=========================================================
        //$getRow = 0;
        /*
        $this->db->select('a.*, b.desc_trans,c.apprv_id, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                    ->from('t_ca_trans a')
                    ->join('t_jenis_transaksi b','a.request_type = b.id_trans', 'left')
                    ->join('tr_trans_approve_status c','a.ca_guid = c.ca_guid', 'left')
                    ->where($whereArry);
        if (count($setSearhArray) > 0) {
            $setFirst = true;
            foreach ($setSearhArray as $serchKey) {
                if($setFirst){$this->db->like(array($setSearchField => $serchKey));$setFirst=false;}
                else{$this->db->or_like(array($setSearchField => $serchKey));}
            }
        }
        $this->db->order_by('input_datetime', 'desc');
        $getRow = $this->db->get()->num_rows();   
        */
        $getRow = $getQuery->num_rows();    
        //======================================================================

        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:10px">'.'<input type="checkbox" id="checkBoxAll2" />'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">'.''.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px">'.'Requester'.$tmplTbl['heading_cell_end'];                
        $strTblRes .= '<th style="">'.'Type'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Amount'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Settle Amount'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Origin'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Destination'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Is Suj'.$tmplTbl['heading_cell_end'];
        //$strTblRes .= $tmplTbl['heading_cell_start'].'Purpose'.$tmplTbl['heading_cell_end'];  
        $strTblRes .= '<th style="width:50px">'.'Attach'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px">'.'Status'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:50px">'.'Approve'.$tmplTbl['heading_cell_end'];        
        $strTblRes .= '<th style="width:50px">'.'Reject'.$tmplTbl['heading_cell_end'];        
        $strTblRes .= $tmplTbl['row_end'];

        if($getQuery->num_rows() == 0 || $getUserJobDetail == '' || $getUserJobDetail == null){
            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td style="text-align:center;" colspan="14">'.'No Record' .$tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];
        }else{
            $tempCountdata = 0;
            // print_r($getQuery);die();
            foreach ($getQuery->result() as $rows)
            {
                $editDeleteButton = "";
                $checkboxApproval = '<input type="checkbox" id="" name="apprv_id[]" class="checkApp2" value="'.$rows->apprv_id.'" />';
                $btnViewStatus = '<input title="View" type="image" src="'.base_url().'includes/icons/bubbles.png" onClick="cashAdvanceView(\''.$rows->id_ca_trans.'\');" />';
                
                //temp here get approval 
                $getFlow = explode(",",$rows->appv_flow_settle_status);
                $getAvail = 0;                
                //if(in_array($getUserJobDetail,$getFlow)){       
                
                if(trim($getFlow[count($getFlow)-1]) == trim($getUserJobDetail)){
                    $getAvail = 1; //here if need approval from current user
                    $appvButton = '<input title="Approve" type="image" src="'.base_url().'includes/icons/ticks.png" onClick="javascript: if (confirm(\'Approve?\')) {approve(\''.$rows->apprv_id.' \');}" />';
                    $blockButton = '<input title="Reject" type="image" src="'.base_url().'includes/icons/block.png" onClick="javascript: if (confirm(\'Reject?\')) {reject(\''.$rows->apprv_id.' \');}" />';
                //=================                
                    $strTblRes .= $tmplTbl['row_start'];
                    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'. $checkboxApproval .$tmplTbl['cell_end'];
                    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'. $btnViewStatus .$tmplTbl['cell_end'];
                    //$strTblRes .= '<td style="text-align:center;">'. $helpers->convertDateToSimple($rows->date_request) .$tmplTbl['cell_end'];
                    
                    $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                    $strTblRes .= '<td style="text-align:center;background-color:#ffffcc">' . $setTCAno . '<br/><i style="color:gray">(' . $helpers->convertDateToSimple($rows->date_request) . ')</i>' . $tmplTbl['cell_end'];

                    $strTblRes .= '<td style="text-align:center;">'.'&nbsp;'.$rows->requester_name.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">'.$rows->desc_trans.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$helpers->currencyFormat($rows->request_amount).' &nbsp;'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$helpers->currencyFormat($rows->settled_amount).' &nbsp;'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->RealOrigin.' &nbsp;'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->RealDestination.' &nbsp;'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:right;">'.$rows->IsSuj. (($rows->IsSuj == "SUJ") ? "(". $rows->suj_id .")" : "").$tmplTbl['cell_end'];
                    //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                    $attachFile = "";
                    if($rows->upload_f1 != ""){
                        $attachFile = '<a href="'.base_url().'/_UploadedFiles/'.$rows->upload_f1.'"><img src="'.base_url().'includes/icons/attachment16.png" alt=""/></a>';
                    }
                    $strTblRes .= '<td style="text-align:center;">'. $attachFile .$tmplTbl['cell_end'];

                    $getStatus = "<span class=\"redSpan\">Uncomplete Approval</span>";
                    if($rows->appv_complete == "1"){$getStatus = "<span class=\"greenSpan\">Approved </span><br/>(<b>Settlement Required</b>)";$editDeleteButton = '<input title="Settlement" type="image" src="'.base_url().'includes/icons/page_white_edit.png" onClick="window.location.href=\''.base_url().'index.php/ca_core/settleCash/'.$rows->id_ca_trans.' \'" />';}
                    elseif($rows->appv_complete == "2"){$getStatus = "<span class=\"redSpan\">Rejected</span>";$editDeleteButton = '<input title="Rejected" type="image" src="'.base_url().'includes/icons/block.png" />';}
                    elseif($rows->appv_complete == "3"){$getStatus = "<span class=\"redSpan\">Waiting Settle Approval</span>";$editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="'.base_url().'includes/icons/next.png" />';}
                    elseif($rows->appv_complete == "4"){$getStatus = "<span class=\"blueSpan\">Settled</span>";$editDeleteButton = '<input title="Settled" type="image" src="'.base_url().'includes/icons/ticks.png" />';}

                    $strTblRes .= '<td style="text-align:center;">'. $getStatus .$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="width:50px;text-align:center;"><div id="dvApprove'.$rows->apprv_id.'" style="padding-top:6px">'.$appvButton.'</div>'.$tmplTbl['cell_end'];
                    $strTblRes .= '<td style="width:50px;text-align:center;"><div id="dvReject'.$rows->apprv_id.'" style="padding-top:6px">'.$blockButton.'</div>'.$tmplTbl['cell_end'];
                    $strTblRes .= $tmplTbl['row_end'];
                    
                    $tempCountdata++;
                }
            }
            if($tempCountdata==0){
                $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= '<td style="text-align:center;" colspan="14">'.'No Record' .$tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes2'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;  
    }

}

?>
