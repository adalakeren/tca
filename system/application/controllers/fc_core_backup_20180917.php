<?php include_once 'helpers.php'; ?>
<?php

class fc_core extends Controller {

    function fc_core() {
        parent::Controller();
        $this->load->database();
        $this->load->helper(array('url'));
        $this->load->library('session');
        $this->load->library('pagination');
        $userLogon = $this->session->userdata('logged_in');
        if (!$userLogon) {
            redirect('admin_core/user_logout');
        }
    }

    function index() {
        $this->fc_history();
    }

    function fc_history() {

        if (isset($_POST['cmbType'])) {
            $getType = $_POST['cmbType'];
        } else {
            if (isset($_GET['gettype'])) {
                $getType = $_GET['gettype'];
            } else {
                $getType = "1";
            }
        }
        
        if (isset($_POST['cmbAvailDept'])) {
            $getDept = $_POST['cmbAvailDept'];
        } else {
            if (isset($_GET['getdept'])) {
                $getDept = $_GET['getdept'];
            } else {
                $getDept = "";
            }
        }
        
        if (isset($_POST['cmbAvailStat'])) {
            $getStat = $_POST['cmbAvailStat'];
        } else {
            if (isset($_GET['getstat'])) {
                $getStat = $_GET['getstat'];
            } else {
                $getStat = "";
            }
        }

        if (isset($_POST['txtSearch'])) {
            $getSearch = $_POST['txtSearch'];
        } else {
            if (isset($_GET['search'])) {
                $getSearch = $_GET['search'];
            } else {
                $getSearch = "";
            }
        }

        //echo $_POST['txtDate']; die();

        if (isset($_GET['per_page'])) {
            $getPage = $_GET['per_page'];
        } else {
            $getPage = 0;
        }

        if ($getPage == 1) {
            $getPage = 0;
        }
        $start = $getPage;
        $end = 10;

        //pagination
        $getTable = $this->initHistoryTable($start, $end, $getSearch, $getType, $getDept, $getStat);
        $config['base_url'] = base_url() . 'index.php/fc_core/fc_history?paging=true&search=' . $getSearch . '&gettype=' . $getType. '&getdept=' . $getDept. '&getstat=' . $getStat; 
        $config['total_rows'] = $getTable['tblRes_numrow'];
        $config['per_page'] = $end;
		$config['num_links'] = 7;

        $this->pagination->initialize($config);
        $throwData["paging"] = $this->pagination->create_links();
        //==========================================        

        $throwData['header'] = "<img src=\"" . base_url() . "includes/icons/zones.png\" alt=\"\"/> Cash Advance Finance Module";
        $throwData['cmbType'] = $this->initCmbStatus('cmbType');
        $throwData['cmbAvailDept'] = $this->initAvailDept();
        $throwData['cmbAvailStat'] = $this->initAvailStat();
        $throwData['searchAction'] = base_url() . 'index.php/fc_core/fc_history?paging=true&search=' . $getSearch . '&gettype=' . $getType . '&getdept=' . $getDept . '&getstat=' . $getStat. 'per_page=0';
        $throwData['txtSearch'] = $getSearch;
        
        $throwData['getType'] = $getType;
        $throwData['getDept'] = $getDept;
        $throwData['getStat'] = $getStat;

        $throwData['tblDatas'] = $getTable['tblRes'];
        $this->load->view('fc_main', $throwData);
    }

    function initHistoryTable($getStart, $getEnd, $getCondition, $getType, $getDept, $getStat) {

        $helpers = new helpers();
        $tmplTbl = array(
            'table_open' => '<table width="100%" border="0" cellpadding="0" cellspacing="0">',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',
            'table_close' => '</table>'
        );

        $getUserSess = $this->session->userdata('userName');
        $getUserArea = $this->session->userdata('area_id');

        //modify here
        $config['hostname'] = "10.144.250.4"; //"CKBAZSQLY101.ckb.co.id"; //10.144.250.4
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

        // $DBfast->select("employee_id");
        // $DBfast->from("employee.t_personel");
        // $DBfast->where("area_id", $getUserArea);
        // $DBfast->group_by("employee_id");

        $where = 'b.user_id is not null';

        $DBfast->select("b.user_id");
        $DBfast->from("employee.t_personel a");
        $DBfast->join('user_access.t_user b', 'a.employee_id = b.employee_id', 'left');
        $DBfast->where("a.area_id", $getUserArea);
        $DBfast->where($where);
        $DBfast->group_by("a.employee_id");

        $listUserArea = $DBfast->get();
        $arrUserArea = array();
        foreach ($listUserArea->result() as $row) {
            // array_push($arrUserArea, $row->employee_id);
            array_push($arrUserArea, $row->user_id);
            array_push($arrUserArea, 'mfarisi');
        }
        //========= 

        $whereArry = array('a.ca_guid <>' => "", 'a.isdelete' => 0);
        
        if (isset($_POST['txtDate']) && ($_POST['txtDate'] != ''))
        {
            //array_push($whereArry, 'DATE(date_request) = ' => $_POST['txtDate']);
        }


        //here to set condition
        $setArrayLike = array();
        $setSearhArray = explode(" ", $getCondition);
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
		$setSearchField = "concat_ws(' ',LOWER(requester_name),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        //======================================================================
        //here get rows=========================================================
        //$getRow = 0;
        /*
        $this->db->select('a.*, b.desc_trans')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        if (count($setSearhArray) > 0) {
            $setFirst = true;
            foreach ($setSearhArray as $serchKey) {
                if ($setFirst) {
                    $this->db->like(array($setSearchField => strtolower($serchKey)));
                    $setFirst = false;
                } else {
                    $this->db->or_like(array($setSearchField => strtolower($serchKey)));
                }
            }
        }
        if ($getType != "99") {
            $this->db->where(array('a.appv_complete' => $getType));
        }
        if($getDept != ""){
            $this->db->where(array('a.requester_dept' => $getDept));
        }
        if($getStat != ""){
            $this->db->where(array('a.requester_station' => $getStat));
        }



        $this->db->order_by('input_datetime', 'desc');
        $getRow = $this->db->get()->num_rows();
        */
        
        //======================================================================

        $this->db->select('SQL_CALC_FOUND_ROWS a.*, b.desc_trans, c.bank_drivername, c.bank_driverrekno, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, (CASE WHEN d.isSuj = 0 THEN d.origin WHEN d.isSuj = 1 THEN e.origin_trip WHEN ISNULL(d.isSuj) THEN d.origin END) AS RealOrigin,
    (CASE WHEN d.isSuj = 0 THEN d.destination WHEN d.isSuj = 1 THEN e.fleet_schedule WHEN ISNULL(d.isSuj) THEN d.destination END) AS RealDestination, (CASE WHEN a.appv_flow_settle LIKE "%END%" THEN 1 ELSE 0 END) AS SettleStatus,', FALSE)
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                // ->join('tr_driver_account c', 'a.ca_guid = c.ca_guid', 'inner')
                ->join('tr_driver_account c', 'a.ca_guid = c.ca_guid', 'left')

                ->join('tt_suj_transaction d', 'a.ca_guid = d.ca_guid', 'left')
                ->join('tr_cost_type_rates e', 'd.id_cost_type_rate = e.ratesid', 'left')

                ->where($whereArry);
        $this->db->where_in("a.requester_userid", $arrUserArea);
        if (count($setSearhArray) > 0) {
            $setFirst = true;
            foreach ($setSearhArray as $serchKey) {
                if ($setFirst) {
                    $this->db->like(array($setSearchField => strtolower($serchKey)));
                    $setFirst = false;
                } else {
                    $this->db->or_like(array($setSearchField => strtolower($serchKey)));
                }
            }
        }
        if ($getType != "99") {
            if ($getType == "5")
            {
                $where = '(a.appv_complete = "5" or a.appv_complete = "10" or a.appv_complete = "11")';
                $this->db->where($where);
            }
            else
            {
                $this->db->where(array('a.appv_complete' => $getType));
            }
            
        }
        if($getDept != ""){
            $this->db->where(array('a.requester_dept' => $getDept));
        }
        if($getStat != ""){
            $this->db->where(array('a.requester_station' => $getStat));
        }
        $this->db->order_by('input_datetime', 'desc')
                ->limit($getEnd, $getStart);

        if (isset($_POST['txtDate']) && ($_POST['txtDate'] != ''))
        {
            $this->db->where(array('DATE(a.date_request)' => $_POST['txtDate']));
            //array_push($whereArry, 'DATE(date_request) = ' => $_POST['txtDate']);
        }

        //$this->db->where(array('appv_complete' => '1'));
        //$this->db->limit(10, 0);
        $getQuery = $this->db->get();
        //$sql = $this->db->last_query();
        //echo $sql;
        $getRow = $this->db->query('SELECT FOUND_ROWS() AS `Count`')->row()->Count;
        //echo $this->db->last_query();echo '---'.$getRow;exit();
        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:10px">'.'<input type="checkbox" id="checkBoxAll" />'.$tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px" colspan="1">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:120px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:130px">' . 'Requester' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th>' . 'Type' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th>' . 'Purpose' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Amount' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];

        $strTblRes .= '<th style="width:100px">' . 'Origin' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Destination' . $tmplTbl['heading_cell_end'];

        $strTblRes .= '<th style="width:100px">' . 'Driver Name' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Driver Rek No' . $tmplTbl['heading_cell_end'];
        //$strTblRes .= $tmplTbl['heading_cell_start'].'Purpose'.$tmplTbl['heading_cell_end']; 

        $strTblRes .= '<th style="width:50px">' . 'Attach' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:140px">' . 'Status' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['heading_cell_start'] . ' Export ' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:120px">' . 'FIN Action' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['heading_cell_start'] . 'Del' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];

        if ($getQuery->num_rows() == 0) {
            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td style="text-align:center;" colspan="18">' . 'No Record' . $tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];
        } else {
            foreach ($getQuery->result() as $rows) {
                $editDeleteButton = "";
                $btnReminder = "-";
                $reminderCounter = "-";
                //            $editDeleteButton .= '<input title="Settlement" type="image" src="'.base_url().'includes/icons/page_white_edit.png" onClick="window.location.href=\''.base_url().'index.php/MM_main/marketingEventEdit/'.$rows->id_ca_trans.' \'" />';
                //            $editDeleteButton .= '&nbsp;&nbsp;&nbsp;';
                //$editDeleteButton .= '<input title="Delete" type="image" src="'.base_url().'includes/icons/page_white_delete.png" onClick="javascript: if (confirm(\'Delete?\')) {window.location.href=\''.base_url().'index.php/ca_core/delCashadvance/'.$rows->id_ca_trans.' \';}" />';
                $checkboxPaid = '<input type="checkbox" name="id_ca_trans[]" class="checkApp" value="'.$rows->id_ca_trans.'" />';
                $btnExport = '<input title="Export" type="image" src="' . base_url() . 'includes/icons/blue-document-excel.png" onClick="javascript:window.location=\'' . base_url() . 'index.php/fc_core/exportExcelTca/' . $rows->id_ca_trans . '\';" />';
                $editDeleteButton .= '<input title="Delete" type="image" src="' . base_url() . 'includes/icons/page_white_delete.png" onClick="" />';
                $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';

                $editDelete = '<input title="Delete" type="image" src="' . base_url() . 'includes/icons/page_white_delete.png" onClick="javascript: if (confirm(\'Delete?\')) {window.location.href=\'' . base_url() . 'index.php/fc_core/delCashadvance/' . $rows->id_ca_trans . '/' . $getType . ' \';}" />';

                if ($rows->appv_complete == "5") {
                    $getReqDetail = $this->getRequesterEmail($rows->requester_sn);
                    $getApprovalInfo = "";
                    $btnReminder = '<input title="Send Reminder" type="image" src="' . base_url() . 'includes/icons/mail.png" onClick="sendReminder(\'' . $getReqDetail['userEmail'] . '\',\'' . $rows->id_ca_trans . '\');" />';
                    $reminderCounter = $rows->settle_mail_counter;
                }

                $getStatus = "<span class=\"redSpan\">TCA - Waiting Approval</span>";
                if ($rows->appv_complete == "1") {
                    $getStatus = "<span class=\"greenSpan\">TCA - Approved </span><span class=\"redSpan\"><br/>Unpaid</span><br/>(<b>Settlement Required</b>)";
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="" />';
                } elseif ($rows->appv_complete == "2") {
                    $getStatus = "<span class=\"redSpan\">TCA - Rejected</span>";
                    $editDeleteButton = '<input title="Rejected" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                } elseif ($rows->appv_complete == "3" && $rows->SettleStatus == "0") {
                    $getStatus = "<span class=\"redSpan\">Settlement - Waiting Approval</span>";
                    $editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="' . base_url() . 'includes/icons/next.png" />';
                } elseif ($rows->appv_complete == "4") {
                    $getStatus = "<span class=\"redSpan\">Settlement - Rejected</span>";
                    $editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                } elseif ($rows->appv_complete == "5" || $rows->SettleStatus == "1") {
                    $getStatus = "<span class=\"blueSpan\">Settlement - Approved</span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "6") {
                    $getStatus = "<span class=\"greenSpan\" style=\"font-weight:bold;font-style:italic\">Finance Complete Settlement</span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "7") {
                    $getStatus = "<span class=\"greenSpan\">TCA - Approved <span class=\"blueSpan\"><br/>Paid</span> </span><br/>(<b>Settlement Required</b>)";
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="" />';
                } elseif ($rows->appv_complete == "8") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">TCA Rejected by Finance </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "9") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">Settlement Rejected by Finance </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "10") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">Overpayment </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "11") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">Underpayment </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                }



                $strTblRes .= $tmplTbl['row_start'];

                //if ($rows->appv_complete == "1")
                //{
                    $strTblRes .= ($rows->appv_complete == "1") ? $tmplTbl['cell_start'].'&nbsp;'. $checkboxPaid .$tmplTbl['cell_end'] :
                    $tmplTbl['cell_start'].'&nbsp;'.$tmplTbl['cell_end'];
                //}
                //else
                //{
                //    $strTblRes .= $tmplTbl['cell_start'].'&nbsp;'.$tmplTbl['cell_end'];
                //}
                
                $strTblRes .= '<td style="padding:3px;">' .  $btnViewStatus . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;padding:3px;">' . $btnReminder . $tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $reminderCounter . $tmplTbl['cell_end'];

                //$strTblRes .= '<td style="text-align:center;">'. $helpers->convertDateToSimple($rows->date_request) .$tmplTbl['cell_end'];
                $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                $setTcaDate = '<br/><i style="color:gray">TCA @' . $helpers->convertDateToSimple($rows->date_request) . '</i>';
                $setSetDate = $rows->input_datetime_settle != "" ? '<br/><i style="color:green"> Settl. @' . $helpers->convertDateToSimple($rows->input_datetime_settle) . '</i>' : "";
                $strTblRes .= '<td style="text-align:center;background-color:#ffffcc;height:33px">' . $setTCAno . $setTcaDate . $setSetDate . $tmplTbl['cell_end'];
                
                $setReqDept = $rows->requester_dept != "" ? '<span style="color:blue" >(' . $rows->requester_dept . ')</span>' : "";
                $setReqLoc =  $rows->requester_station != "" ? '<span style="color:green" >(' . $rows->requester_station . ')</span>' : "";
                $setReqDetail = $setReqDept . '&nbsp;&nbsp;' . $setReqLoc;
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $rows->requester_name . '<br/>'. $setReqDetail . $tmplTbl['cell_end'];
                
                $strTblRes .= '<td style="text-align:center;">' . $rows->desc_trans . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->purpose . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:right;">' . $helpers->currencyFormat($rows->request_amount) . ' &nbsp;' . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:right;">' . $helpers->currencyFormat($rows->settled_amount) . ' &nbsp;' . $tmplTbl['cell_end'];

                $strTblRes .= '<td style="text-align:center;">' . $rows->RealOrigin . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->RealDestination . $tmplTbl['cell_end'];

                $strTblRes .= '<td style="text-align:center;">' . $rows->bank_drivername . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->bank_driverrekno . $tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                $attachFile = "";
                if ($rows->upload_f1 != "") {
                    $attachFile = '<a href="' . base_url() . '/_UploadedFiles/' . $rows->upload_f1 . '"><img src="' . base_url() . 'includes/icons/attachment16.png" alt=""/></a>';
                }
                $strTblRes .= '<td style="text-align:center;">' . $attachFile . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;" id="dvStatus'.$rows->id_ca_trans.'">' . $getStatus . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="width:50px;text-align:center;">' . $btnExport . $tmplTbl['cell_end'];

                $finSettleButton = " - ";
                if(intval($rows->appv_complete) == 1){
                    $finSettleButton = '<a class="finLink" style="color:red" href="javascript:void(0)" onClick="reject(\''.$rows->id_ca_trans.' \',\'fintcareject\')"> Reject TCA </a>';
                    $finSettleButton .= '<br/><a class="finLink" href="'.base_url().'index.php/fc_core/paidTca/'.$rows->id_ca_trans.'/'.$getType.'" onClick="return confirm(\'Paid TCA Request?\');"> Paid TCA </a>';
                }elseif(intval($rows->appv_complete) == 4){
                    $finSettleButton = '<a class="finLink" href="'.base_url().'index.php/fc_core/paidTca/'.$rows->id_ca_trans.'/'.$getType.'/reqsubmit" onClick="return confirm(\'Submit to user?\');"> Submit to Requestor </a>';
                }elseif(intval($rows->appv_complete) == 7){
                    $finSettleButton = '<a class="finLink" style="color:red" href="javascript:void(0)" onClick="reject(\''.$rows->id_ca_trans.' \',\'fintcareject\')"> Reject TCA </a>';
                    $finSettleButton .= '<br/><a class="finLink" href="'.base_url().'index.php/fc_core/paidTca/'.$rows->id_ca_trans.'/'.$getType.'/unpaid" onClick="return confirm(\'Unpaid TCA Request?\');"> UnPaid TCA </a>';
                }elseif(intval($rows->appv_complete) == 9){
                    $finSettleButton = '<a class="finLink" href="'.base_url().'index.php/fc_core/paidTca/'.$rows->id_ca_trans.'/'.$getType.'/reqsubmit" onClick="return confirm(\'Submit to user?\');"> Submit to Requestor </a>';
                }elseif(intval($rows->appv_complete) == 3){
                    $finSettleButton = '<a class="finLink" style="color:red" href="javascript:void(0)" onClick="reject(\''.$rows->id_ca_trans.' \',\'finsetreject\')"> Reject Settlement </a>';                    
                }elseif(intval($rows->appv_complete) == 5){
                    $finSettleButton = '<a class="finLink" style="color:red" href="javascript:void(0)" onClick="reject(\''.$rows->id_ca_trans.' \',\'finsetreject\')"> Reject Settlement </a>';
                    $finSettleButton .= '<br/><a class="finLink" style="color:blue" href="javascript:void(0)" onClick="finSettle(\'' . $getReqDetail['userEmail'] . '\',\'' . $rows->id_ca_trans . '\')" > Finance Complete </a>';
                }elseif(intval($rows->appv_complete) == 10)
                {
                    $finSettleButton .= '<a class="finLink" href="'.base_url().'index.php/fc_core/refundClaimTca/'.$rows->id_ca_trans.'/'.$getType.'" onClick="return confirm(\'Paid TCA Request?\');"> Refund TCA </a>';
                }elseif(intval($rows->appv_complete) == 11)
                {
                    $finSettleButton = '<a class="finLink" href="'.base_url().'index.php/fc_core/refundClaimTca/'.$rows->id_ca_trans.'/'.$getType.'" onClick="return confirm(\'Paid TCA Request?\');"> Claim TCA </a>';
                }
                
                if ($rows->fin_settle == 1) {
                    $finSettleButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                }

                $strTblRes .= '<td style="width:50px;text-align:center;" id="dvAction'.$rows->id_ca_trans.'">' . $finSettleButton . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="width:50px;text-align:center;">' . $editDelete . $tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;
    }

    function paidTca($getTcaId,$senderStatus,$getAction = "paid"){
        
        $getSessUser = $this->session->userdata('userName');
        $getSessUserFull = $this->session->userdata('userNameFull');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');
        
		if($getAction == "reqsubmit"){
			$data = array('appv_complete' => 7, 'tcaispaid' => 0, 'appv_flow_settle_status' => null, 'appv_flow_settle' => null, 'settled_amount' => null);
        }elseif($getAction == "paid"){
			$data = array('appv_complete' => 7, 'tcaispaid' => 0, 'tcaispaidby' => $getSessUser, 'tcaispaiddatetime' => date("Y-m-d H:i:s"));
        }else{
            $data = array('appv_complete' => 1, 'tcaispaid' => 1, 'tcaispaidby' => $getSessUser, 'tcaispaiddatetime' => date("Y-m-d H:i:s"));
        }
        
        $this->db->where('id_ca_trans', $getTcaId);
        $execData = $this->db->update('t_ca_trans', $data);
        
        redirect(base_url() . 'index.php/fc_core/fc_history?paging=true&gettype=' . $senderStatus );
    }

    function refundClaimTca($getTcaId,$senderStatus,$getAction = "refundclaim"){
        
        $getSessUser = $this->session->userdata('userName');
        $getSessUserFull = $this->session->userdata('userNameFull');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');
        
        $data = array('appv_complete' => 6);
        
        $this->db->where('id_ca_trans', $getTcaId);
        $execData = $this->db->update('t_ca_trans', $data);
        
        redirect(base_url() . 'index.php/fc_core/fc_history?paging=true&gettype=' . $senderStatus );
    }
    
    function getLastApprovalDetail($getApprovalFlow) {
        $helpers = new helpers();

        $connHera = $helpers->_initServDataFast_custom('employee'); //init HERA data

        $getLastArray = explode(",", $getApprovalFlow);
        $getLastVal = $getLastArray[count($getLastArray) - 1];

        $whereArry = array('job_title_detail' => $getLastVal);
        $getQuery = $this->db->select('*')->from('t_direct_report')->where($whereArry)->get();
        foreach ($getQuery->result() as $getRows) {
            $getUserSn = $getRows->employee_id;
            $getUserName = $getRows->name;
        }

        $whereArry = array('employee_id' => $getUserSn);
        $getDetailQuery = $connHera->select('*')->from('t_personel')->where($whereArry)->get();

        foreach ($getDetailQuery->result() as $getRows2) {
            $getUserEmail = $getRows2->email;
        }

        $throwData['userSn'] = $getUserSn;
        $throwData['userName'] = $getUserName;
        $throwData['userEmail'] = $getUserEmail;

        return $throwData;
    }

    function getRequesterEmail($getReqSn) {
        $helper = new helpers();
        $connHera = $helper->_initServDataFast_custom("employee");

        $whereArry = array('employee_id' => $getReqSn);
        $getDetailQuery = $connHera->select('*')->from('t_personel')->where($whereArry)->get();

        $getUserEmail = "";
        foreach ($getDetailQuery->result() as $getRows2) {
            $getUserEmail = $getRows2->email;
        }
        $throwData['userEmail'] = $getUserEmail;
        return $throwData;
    }

    function initCmbStatus($setName) {
        $res = '<select style="width:120px;font-size:12px" id="' . $setName . '" name="' . $setName . '">    
                    <option value="99">::All Status::</option>
                    <optgroup label="-----------------------------------"></optgroup>
                    <option value="0">TCA - Waiting Approval</option>
                    <option value="1">TCA - Approved Unpaid</option>
                    <option value="7">TCA - Approved Paid</option>
                    <option value="2">TCA - Rejected</option>
                    <option value="8">TCA - Rejected by Finance</option>
                    <option value="3">Settlement - Waiting Approval</option>
                    <option value="5">Settlement - Approved</option>
                    <option value="4">Settlement - Rejected</option>
                    <option value="9">Settlement - Rejected by Finance</option>
                    <option value="10">Overpayment</option>
                    <option value="11">Underpayment</option>
                    <option value="6">Finance Complete Settlement</option>
                </select>';

        return $res;
    }
    
    function initAvailDept($setName="cmbAvailDept") {
        $helpers = new helpers();
        
        $this->db->distinct()
                    ->select('requester_dept')
                    ->from('t_ca_trans ');        
        $getDeptQuery = $this->db->get();
        $getAvailDept = '';
        foreach ($getDeptQuery->result() as $rows) {
            $getAvailDept .= $rows->requester_dept . ',';
        }
        $getDeptQuery->free_result();
        
        $fastConn = $helpers->_initServDataFast_custom('employee');
        $fastConn->select('*')
                    ->from('t_department')
                    ->where_in('department_id',  explode(",", $getAvailDept))
                    ->order_by('description','asc')
                ;
        $getDetailDeptQuery = $fastConn->get();
        
        $res = '<select style="width:130px;font-size:12px" id="' . $setName . '" name="' . $setName . '">';
        $res .= '<option value="">:: All Departement ::</option>';
        $res .= '<optgroup label="----------------------------------------------"></optgroup>';
        foreach ($getDetailDeptQuery->result() as $rows) {
            $res .= $rows->department_id!= "" ? '<option value="'.$rows->department_id.'">' . $rows->department_id . ' - '.$rows->description .'</option>' : "";
        }
        $res .= '</select>';
        $getDetailDeptQuery->free_result();

        return $res;
    }
    
    function initAvailStat($setName="cmbAvailStat") {
        $helpers = new helpers();
        
        $this->db->distinct()
                    ->select('requester_station')
                    ->from('t_ca_trans ');        
        $getStatQuery = $this->db->get();
        $getAvailStat = '';
        foreach ($getStatQuery->result() as $rows) {
            $getAvailStat .= $rows->requester_station . ',';
        }
        $getStatQuery->free_result();
        
        $fastConn = $helpers->_initServDataFast_custom('master_param');
        $fastConn->select('*')
                    ->from('t_station')
                    ->where_in('station_id',  explode(",", $getAvailStat))
                    ->order_by('station_desc','asc')
                ;
        $getDetailDeptQuery = $fastConn->get();
        
        $res = '<select style="width:130px;font-size:12px" id="' . $setName . '" name="' . $setName . '">';
        $res .= '<option value="">:: All Station ::</option>';
        $res .= '<optgroup label="----------------------------------------------"></optgroup>';
        foreach ($getDetailDeptQuery->result() as $rows) {
            $res .= $rows->station_id!= "" ? '<option value="'.$rows->station_id.'">' . $rows->station_id . ' - '.$rows->station_desc .'</option>' : "";
        }
        $res .= '</select>';
        $getDetailDeptQuery->free_result();

        return $res;
    }

    function exportExcel($getType, $getCondition) {

//        $getType = $_POST['type'];
//        $getCondition = $_POST['searchArray'];
//        $getType = '5';
//        $getCondition = '';
        //here to set condition        
        $setArrayLike = array();
        if ($getCondition != "all") {
            $setSearhArray = explode(" ", $getCondition);
            $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose))";
        } else {
            $setSearhArray = array();
        }
        //======================================================================

        include_once 'phpExcel/PHPExcel.php';
        include_once 'phpExcel/PHPExcel/IOFactory.php';
        include_once 'phpExcel/PHPExcel/Writer/Excel5.php';
        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="trainReport_'.$getStart.'_'.$getEnd.'.xls"');
        header('Content-Disposition: attachment;filename="CA_reports.xls"');
        header('Cache-Control: max-age=0');

        $objPHPExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("fin_report.xls");

        //here for query
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left');
        if (count($setSearhArray) > 0) {
            $setFirst = true;
            foreach ($setSearhArray as $serchKey) {
                if ($setFirst) {
                    $this->db->like(array($setSearchField => $serchKey));
                    $setFirst = false;
                } else {
                    $this->db->or_like(array($setSearchField => $serchKey));
                }
            }
        }
        if ($getType != "99") {
            $this->db->where(array('a.appv_complete' => $getType));
        }
        $this->db->order_by('input_datetime', 'asc');

        $getQuery = $this->db->get();

//        echo $this->db->last_query();
        //======================================================================

        $baseRow = 5;
        $helper = new helpers();

        $getTotalAmount = 0;
        $getTotalSettle = 0;
        
        foreach ($getQuery->result() as $rowS) {

            $getStatus = "TCA - Waiting Approval";
            if ($rowS->appv_complete == "1") {
                $getStatus = "TCA - Approved Unpaid (Settlement Required)";
            } elseif ($rowS->appv_complete == "2") {
                $getStatus = "TCA - Rejected";
            } elseif ($rowS->appv_complete == "3") {
                $getStatus = "Settlement - Waiting Approval";
            } elseif ($rowS->appv_complete == "4") {
                $getStatus = "Settlement - Rejected";
            } elseif ($rowS->appv_complete == "5") {
                $getStatus = "Settlement - Approved";
            } elseif ($rowS->appv_complete == "6") {
                $getStatus = "Finance Complete Settlement";
            } elseif ($rowS->appv_complete == "7") {
                $getStatus = "TCA - Approved Paid (Settlement Required)";
            } elseif ($rowS->appv_complete == "8") {
                $getStatus = "TCA Rejected by Finance";
            } elseif ($rowS->appv_complete == "9") {
                $getStatus = "Settlement Rejected by Finance";
            }

            $setTCAno = '' . $rowS->id_ca_trans . '-TCA-' . $rowS->bulan . $rowS->tahun . '';
            
            $row = $baseRow + 1;
            $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $setTCAno);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $rowS->requester_name);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $helper->convertDateToSimple($rowS->date_request));
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $helper->convertDateToSimple($rowS->input_datetime_settle));
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, str_replace('&amp;', " & ", $rowS->desc_trans));
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, 'Rp. ' . $helper->currencyFormat($rowS->request_amount));
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, 'Rp. ' . $helper->currencyFormat($rowS->settled_amount));
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $rowS->purpose);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $getStatus);
            
            $getTotalAmount += $rowS->request_amount != "" ? $rowS->request_amount : 0;
            $getTotalSettle += $rowS->settled_amount != "" ? $rowS->settled_amount : 0;
        }
        
        $objPHPExcel->getActiveSheet()->setCellValue('G' . 1, 'Rp. ' . $helper->currencyFormat($getTotalAmount));
        $objPHPExcel->getActiveSheet()->setCellValue('H' . 1, 'Rp. ' . $helper->currencyFormat($getTotalSettle));
            
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    function exportExcelTca($getCaId) {

        //here to set condition        
//        $setArrayLike = array();
//        if($getCondition!="all"){
//            $setSearhArray = explode(" ", $getCondition); 
//            $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose))";          
//        }else{
//            $setSearhArray = array();
//        }
        //======================================================================

        // include_once 'phpExcel/PHPExcel.php';
		// include_once 'phpExcel/PHPExcel/IOFactory.php';
        // include_once 'phpExcel/PHPExcel/Writer/Excel5.php';
		
        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="trainReport_'.$getStart.'_'.$getEnd.'.xls"');
        header('Content-Disposition: attachment;filename="TCA_Export.xls"');
        header('Cache-Control: max-age=0');

        $objPHPExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("tca_template.xls");

        //here for query
        $whereArry = array('id_ca_trans' => $getCaId);
        $this->db->select('a.*, b.desc_trans')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);

        $getQuery = $this->db->get();

//        echo $this->db->last_query();
        //======================================================================
//        $baseRow = 5;
        $helper = new helpers();

        foreach ($getQuery->result() as $rowS) {

            $getStatus = "Uncomplete Approval";
            if ($rowS->appv_complete == "1") {
                $getStatus = "Approved(Settlement Required)";
            } elseif ($rowS->appv_complete == "2") {
                $getStatus = "Rejected";
            } elseif ($rowS->appv_complete == "3") {
                $getStatus = "Waiting Settle Approval";
            } elseif ($rowS->appv_complete == "4") {
                $getStatus = "Settle Rejected";
            } elseif ($rowS->appv_complete == "5") {
                $getStatus = "Settled";
            } elseif ($rowS->appv_complete == "6") {
                $getStatus = "Settled by Finance";
            } elseif ($rowS->appv_complete == "7") {
                $getStatus = "Approved(Settlement Required)";
            }

            $setTitle = "Temporary Cash Advance";
            if ($rowS->appv_complete == "5") {
                $setTitle = "Petty Cash";
            } elseif ($rowS->appv_complete == "6") {
                $setTitle = "Petty Cash";
            }
            $objPHPExcel->getActiveSheet()->setCellValue('B' . '2', $setTitle);

            $objPHPExcel->getActiveSheet()->setCellValue('C' . '3', $getStatus);
            
            $setDate = $helper->convertDateToSimple($rowS->date_request);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '4', $setDate);
            
            $setSettleDate = $rowS->input_datetime_settle != "" ? $helper->convertDateToSimple($rowS->input_datetime_settle) : " - "; 
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '5', $setSettleDate);
            
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '6', $rowS->requester_name);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '7', str_replace("&amp;", "&", $rowS->desc_trans));
			
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '8', 'Rp. ' . $helper->currencyFormat($rowS->request_amount));
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '9', 'Rp. ' . $helper->currencyFormat($rowS->settled_amount));

            //here for amount calc;
            $getCalc = intval($rowS->request_amount) - intval($rowS->settled_amount);
            $setLabelCalc = "Claim";
            if ($getCalc > 0) {
                $setLabelCalc = "Refund";
            }
            $objPHPExcel->getActiveSheet()->setCellValue('B' . '10', $setLabelCalc);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '10', 'Rp. ' . $helper->currencyFormat($getCalc));
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '11', $rowS->purpose);


            //here get Approval New
            $approval1_flow = "";
            $this->db->select('*')
                    ->from('tr_trans_approve_status')
                    ->where(array('ca_guid' => $rowS->ca_guid, 'approve_type' => 'STD'));

            $getApv1 = $this->db->get();
            foreach ($getApv1->result() as $apv1Rows) {
                $setAppvStat = " ";
                if ($apv1Rows->appr_status == "1") {
                    $setAppvStat = " (Approved) ";
                } elseif ($apv1Rows->appr_status == "2") {
                    $setAppvStat = " (Rejected) ";
                }
                $approval1_flow .= $apv1Rows->appr_name . " " . $setAppvStat . "\n ";
            }
            //here get Approval Settlement
            $approval2_flow = "";
            $this->db->select('*')
                    ->from('tr_trans_approve_status')
                    ->where(array('ca_guid' => $rowS->ca_guid, 'approve_type' => 'SET'));

            $getApv2 = $this->db->get();
            foreach ($getApv2->result() as $getApv2) {
                $setAppvStat2 = " ";
                if ($getApv2->appr_status == "1") {
                    $setAppvStat2 = " (Approved) ";
                } elseif ($getApv2->appr_status == "2") {
                    $setAppvStat2 = " (Rejected) ";
                }
                $approval2_flow .= $getApv2->appr_name . " " . $setAppvStat2 . "\n ";
            }

            $objPHPExcel->getActiveSheet()->setCellValue('C' . '14', $approval1_flow);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . '16', $approval2_flow);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    function exportExcelFMode($getType,$getDept,$getStation, $getCondition) {

        $setArrayLike = array();
        if ($getCondition != "all") {
            $setSearhArray = explode(" ", $getCondition);
            $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose))";
        } else {
            $setSearhArray = array();
        }

        //here for query
        $this->db->select('a.*, b.desc_trans, c.bank_drivername, c.bank_driverrekno, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->join('tr_driver_account c', 'a.ca_guid = c.ca_guid', 'left');
        if (count($setSearhArray) > 0) {
            $setFirst = true;
            foreach ($setSearhArray as $serchKey) {
                if ($setFirst) {
                    $this->db->like(array($setSearchField => $serchKey));
                    $setFirst = false;
                } else {
                    $this->db->or_like(array($setSearchField => $serchKey));
                }
            }
        }
        if ($getType != "99") {
            $this->db->where(array('a.appv_complete' => $getType));
        }
        if ($getDept != "all") {
            $this->db->where(array('a.requester_dept' => $getDept));
        }
        if ($getStation != "all") {
            $this->db->where(array('a.requester_station' => $getStation));
        }
        $this->db->order_by('input_datetime', 'desc');

        $getQuery = $this->db->get();

//        echo $this->db->last_query();

        //===========================
        if (version_compare(PHP_VERSION, '5') < 0) {
            include_once('tbs_class.php'); // TinyButStrong template engine for PHP 4
        } else {
            include_once('tbs_class_php5.php'); // TinyButStrong template engine
        }
        include('tbs_plugin_excel.php');
        
        $helper = new helpers();

        
        $getTotalAmount = 0;
        $getTotalSettle = 0;
		
		$tableData = '<table>
						<tr>
							<td style="background:gray" >TCA.</td>
							<td style="background:gray" >Requester</td>
							<td style="background:gray" >Department</td>
							<td style="background:gray" >Station</td>
							<td style="background:gray" >Date</td>
							<td style="background:gray" >Settlement Date </td>
							<td style="background:gray" >Type</td>
							<td style="background:gray" >Amount</td>
							<td style="background:gray" >Settle Amount</td>

                            <td style="background:gray" >Driver Name</td>
                            <td style="background:gray" >Driver Rek</td>    

							<td style="background:gray" >Purpose</td>
							<td style="background:gray" >Status</td>							
						</tr>
						';
						
        foreach ($getQuery->result() as $rowS) {

            $getStatus = "TCA - Waiting Approval";
            if ($rowS->appv_complete == "1") {
                $getStatus = "TCA - Approved Unpaid (Settlement Required)";
            } elseif ($rowS->appv_complete == "2") {
                $getStatus = "TCA - Rejected";
            } elseif ($rowS->appv_complete == "3") {
                $getStatus = "Settlement - Waiting Approval";
            } elseif ($rowS->appv_complete == "4") {
                $getStatus = "Settlement - Rejected";
            } elseif ($rowS->appv_complete == "5") {
                $getStatus = "Settlement - Approved";
            } elseif ($rowS->appv_complete == "6") {
                $getStatus = "Finance Complete Settlement";
            } elseif ($rowS->appv_complete == "7") {
                $getStatus = "TCA - Approved Paid (Settlement Required)";
            } elseif ($rowS->appv_complete == "8") {
                $getStatus = "TCA Rejected by Finance";
            } elseif ($rowS->appv_complete == "9") {
                $getStatus = "Settlement Rejected by Finance";
            } elseif ($rowS->appv_complete == "10") {
                $getStatus = "Overpayment";
            } elseif ($rowS->appv_complete == "11") {
                $getStatus = "Underpayment";
            }

            $setTCAno = '' . $rowS->id_ca_trans . '-TCA-' . $rowS->bulan . $rowS->tahun . '';
            /*
            $datas[] = array('tcano' => $setTCAno
                            , 'requester' => $rowS->requester_name
                            , 'requesterdept' => $rowS->requester_dept
                            , 'requesterstation' => $rowS->requester_station
                            , 'tglrequest' => $helper->convertDateToSimple($rowS->date_request)
                            , 'tglsettle' => $helper->convertDateToSimple($rowS->input_datetime_settle)
                            , 'tcatype' => str_replace('&amp;', " & ", $rowS->desc_trans)
                            , 'requestamount' => $rowS->request_amount
                            , 'settleamount' => $rowS->settled_amount
                            , 'purpose' => $rowS->purpose
                            , 'tcastatus' => $getStatus
                        );
            */
			$tableData .= '<tr>			
								<td>'.$setTCAno.'</td>
								<td>'.$rowS->requester_name.'</td>
								<td>'.$rowS->requester_dept.'</td>
								<td>'.$rowS->requester_station.'</td>
								<td>'.$helper->convertDateToSimple($rowS->date_request).'</td>
								<td>'.$helper->convertDateToSimple($rowS->input_datetime_settle).'</td>
								<td>'.str_replace('&amp;', " & ", $rowS->desc_trans).'</td>
								<td>'.$rowS->request_amount.'</td>
								<td>'.$rowS->settled_amount.'</td>

                                <td>'.$rowS->bank_drivername.'</td>
                                <td>'.$rowS->bank_driverrekno.'</td>

								<td>'.$rowS->purpose.'</td>
								<td>'.$getStatus.'</td>
							</tr>';			
							
            $getTotalAmount += $rowS->request_amount != "" ? $rowS->request_amount : 0;
            $getTotalSettle += $rowS->settled_amount != "" ? $rowS->settled_amount : 0;
        }
		
		$tableData .= '<tr>			
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td><b>Total : </b></td>
								<td>'.$getTotalAmount .'</td>
								<td>'.$getTotalSettle.'</td>
								<td></td>
								<td></td>
							</tr>';		
							
		$tableData .= '</table>';

        //print_r($datas);exit;
        $getQuery->free_result();
		/*
        $details = array();
		$details[] = array('totrequestamount' => $getTotalAmount, 'totsettleamount' => $getTotalSettle);
	
        
        $TBS = new clsTinyButStrong;
        $TBS->PlugIn(TBS_INSTALL, TBS_EXCEL);
        $TBS->LoadTemplate('fin_report.xml');
        $TBS->MergeBlock('datas', $datas);
        $TBS->MergeBlock('details', $details);

        $TBS->Show(TBS_EXCEL_DOWNLOAD, 'CA_reports.xls');
		*/
		
		$filename = date("Ymd")."_report_TCA_recap.xls";
		header("Content-type: application/xls");
		header("Content-Disposition: attachment; filename=$filename");
		
		echo $tableData;
    }
    
    function delCashadvance($getCaId, $getSender=null) {

//        $this->db->select('*')
//                ->from('t_ca_trans')
//                ->where(array('id_ca_trans' => $getCaId));
//        $getQuery = $this->db->get();
//
//        foreach ($getQuery->result() as $rows) {
//            $getGuid = $rows->ca_guid;
//        }
//
//        //here for delete transaction first
//        $data = array('ca_guid' => $getGuid);
//        $execTrans = $this->db->delete("tr_trans_approve_status", $data);
//        //=================================
//
//        if ($execTrans) {
//            $data = array('id_ca_trans' => $getCaId);
//            $execTrans = $this->db->delete("t_ca_trans", $data);
//        }
        
        $getSessUser = $this->session->userdata('userName');
        $getSessUserFull = $this->session->userdata('userNameFull');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');
        
        $data = array('isdelete' => 1, 'isdeleteby' => $getSessUser);
        $this->db->where('id_ca_trans', $getCaId);
        $execData = $this->db->update('t_ca_trans', $data);

        $referer = base_url() . 'index.php/fc_core/fc_history?paging=true&gettype=' . $getSender . '&per_page=0';

        redirect($referer);
    }

}

?>
