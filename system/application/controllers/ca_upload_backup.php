<?php

include_once 'helpers.php';
include_once 'ajax_manager.php';
error_reporting(0);

class ca_upload extends Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->database();
    }

    function ca_upload() {
        //parent::Controller();

        $this->load->library(array('form_validation'));
        $this->load->library('csvimport');
        $this->load->database();
        $this->load->library('session');
        $this->load->library('pagination');
        $userLogon = $this->session->userdata('logged_in');
        if (!$userLogon) {
            redirect('admin_core/user_logout');
        }
    }

    function upload_request() {

        $data["header"] = "Upload Request";
        $data["error"] = "";

        $helpers = new helpers();
        $data['cmbCashType'] = $helpers->initCmbAllValue_cond2("cmbCashType", "t_jenis_transaksi", "desc_trans", "id_trans", "", $this->session->userdata('userEmpID'));

        $this->load->view('ca_header');
        $this->load->view('ca_bread', $data);
        $this->load->view('upload_request', $data);
        $this->load->view('ca_footer');
    }

    function upload_settle() {
        $data["header"] = "Upload Settle";
        $data["error"] = "";

        $this->load->view('ca_header');
        $this->load->view('ca_bread', $data);
        $this->load->view('upload_settle');
        $this->load->view('ca_footer');
    }

    function IsSuj($tcano) {
        $tcanos = explode("-", $tcano);
        $tca_no = $tcanos[0];

        $query = "SELECT b.isSuj
			FROM t_ca_trans a
			INNER JOIN tt_suj_transaction b ON a.ca_guid = b.ca_guid
			WHERE a.id_ca_trans = " . $tca_no;

        $q = $this->db->query($query);

        foreach ($q->result() as $row) {
            $res = ($row->isSuj == 1);
            return $res;
        }

        return false;
    }

    function IsManifestExist($tcano) {
        $tcanos = explode("-", $tcano);
        $tca_no = $tcanos[0];

        $query = "SELECT no_manifest FROM tr_manifest WHERE id_ca_trans = '" . $tcano . "'";

        $q = $this->db->query($query);

        foreach ($q->result() as $row) {
            return true;
        }

        return false;
    }

    function GetSettleAmount($tcano) {
        $serverHost = "10.10.3.11";
        $usr = "sa";
        $pwd = "#sqls3rv3r4dm1n#";
        $db = "enterprise";

        $tcanos = explode("-", $tcano);
        $tca_no = $tcanos[0];

        $connectionInfo = array("UID" => $usr, "PWD" => $pwd, "Database" => $db);


        $conn = sqlsrv_connect($serverHost, $connectionInfo);

        $query = "";
        if ($this->IsSuj($tcano)) {
            $settle = 0;

            $query = "SELECT b.* FROM fms.tt_expense_header a
			INNER JOIN fms.tt_expense_detail b on a.expense_id = b.expense_id
			WHERE a.tca_no = '" . $tca_no . "' AND is_expense_group = 'Y'";

            $res = sqlsrv_query($conn, $query);

            while ($row = sqlsrv_fetch_array($res)) {
                $settle = (float) $row['c_delivery'];
                break;
            }

            $query = "SELECT b.* FROM fms.tt_expense_header a
			INNER JOIN fms.tt_expense_detail b on a.expense_id = b.expense_id
			WHERE a.tca_no = '" . $tca_no . "' AND is_expense_group = 'N'";

            $res = sqlsrv_query($conn, $query);

            while ($row = sqlsrv_fetch_array($res)) {
                $settle = $settle + (float) $row['c_delivery'];
            }

            return $settle;
        } else {
            $settle = 0;

            $query = "SELECT b.* FROM fms.tt_expense_header a
			INNER JOIN fms.tt_expense_detail b on a.expense_id = b.expense_id
			WHERE a.tca_no = '" . $tca_no . "' AND is_expense_group = 'N'";

            $res = sqlsrv_query($conn, $query);

            while ($row = sqlsrv_fetch_array($res)) {
                $settle = (float) $row['c_delivery'] + (float) $row['c_labor'] + (float) $row['c_packing'] +
                        (float) $row['c_taxes'] + (float) $row['c_trucking'] + (float) $row['c_deploy'] + (float) $row['c_insurance'];
                break;
            }

            $query = "SELECT b.* FROM fms.tt_expense_header a
			INNER JOIN fms.tt_expense_detail b on a.expense_id = b.expense_id
			WHERE a.tca_no = '" . $tca_no . "' AND is_expense_group = 'Y'";

            $res = sqlsrv_query($conn, $query);

            while ($row = sqlsrv_fetch_array($res)) {
                $settle = $settle + (float) $row['c_delivery'] + (float) $row['c_labor'] + (float) $row['c_packing'] +
                        (float) $row['c_taxes'] + (float) $row['c_trucking'] + (float) $row['c_deploy'] + (float) $row['c_insurance'];
                break;
            }

            return $settle;
        }
    }

    function GetSUJ($suj_id) {
        $area_id = $this->session->userdata('area_id');
        $query = "SELECT * FROM tr_cost_type_rates where datediff(expired, now()) >= 0 AND suj_id = $suj_id AND area_id = '$area_id' ORDER BY expired DESC LIMIT 1";
        $db = $this->load->database();

        $q = $this->db->query($query);

        foreach ($q->result() as $row) {
            return $row;
        }

        return null;
    }

    function GetSUJById($suj_id) {
        $area_id = $this->session->userdata('area_id');
        $query = "SELECT * FROM tr_cost_type_rates WHERE suj_id = $suj_id AND area_id = '$area_id' ORDER BY expired DESC LIMIT 1";
        $db = $this->load->database();

        $q = $this->db->query($query);

        foreach ($q->result() as $row) {
            return $row;
        }

        return null;
    }

    function GetTransApproveStatus($ca_guid) {
        //$getUserJobDetail = $this->session->userdata('sessionLevelDetail');
        $query = $this->db->query("SELECT apprv_id FROM tr_trans_approve_status WHERE ca_guid = '" . $ca_guid . "' "); //AND approver='".$getUserJobDetail."'");


        foreach ($query->result() as $row) {
            return $row;
        }

        return null;
    }

    function GetMainTransaction($tca_id) {
        $tcanos = explode("-", $tca_id);
        $tca_no = $tcanos[0];
        $this->load->database();
        $this->db->select('*');
        $this->db->from('t_ca_trans');
        $this->db->where('id_ca_trans = ', $tca_no);

        $query = $this->db->get();

        foreach ($query->result() as $row) {
            return $row;
        }

        return null;
    }

    function do_upload_request() {
        $config = array(
            'upload_path' => FCPATH . 'upload/',
            'allowed_types' => 'xls',
        );
        $this->load->library('upload', $config);
        $this->load->library('Spreadsheet_Excel_Reader');
        $datas["header"] = "Upload Request";
        $datas["error"] = "Upload Success";

        $ajaxManager = new ajax_manager();
        $helpers = new helpers();
        $datas['cmbCashType'] = $helpers->initCmbAllValue_cond2("cmbCashType", "t_jenis_transaksi", "desc_trans", "id_trans", "", $this->session->userdata('userEmpID'));

        $suj_not_founded = array();
        $suj_error_list = array();

        if ($this->upload->do_upload('userfile')) {
            $data = $this->upload->data();
            @chmod($data['full_path'], 0777);

            $this->spreadsheet_excel_reader->setOutputEncoding('CP1251');
            $this->spreadsheet_excel_reader->read($data['full_path']);
            $sheets = $this->spreadsheet_excel_reader->sheets[0];



            for ($i = 2; $i <= $sheets['numRows']; $i++) {
                if (isset($sheets['cells'][$i])) {


                    $sujno = $sheets['cells'][$i][1];
                    $suj = $this->GetSUJ($sheets['cells'][$i][1]);
                    $sujdetail = $this->GetSUJById($sheets['cells'][$i][1]);
                    $isSUJ = ($suj == null ) ? false : true;

                    if (!$isSUJ && ($sujno != "0")) {
                        //echo $isSUJ;
                        //echo $sujno;
                        //var_dump($suj);
                        //echo "test"; die();
                        //die();
                        if ($sujdetail != null)
                            $sujno .= " (expired : " . $sujdetail->expired . ")";
                        array_push($suj_not_founded, $sujno);
                        array_push($suj_error_list, $sheets['cells'][$i]);
                        continue;
                    }

                    if ($sujno == "0") {
                        if (!isset($sheets['cells'][$i][1])) {
                            array_push($suj_error_list, $sheets['cells'][$i]);
                            continue;
                        }

                        if (!isset($sheets['cells'][$i][2])) {
                            array_push($suj_error_list, $sheets['cells'][$i]);
                            continue;
                        }

                        if (!isset($sheets['cells'][$i][3])) {
                            array_push($suj_error_list, $sheets['cells'][$i]);
                            continue;
                        }

                        if (!isset($sheets['cells'][$i][4])) {
                            array_push($suj_error_list, $sheets['cells'][$i]);
                            continue;
                        }

                        if (!isset($sheets['cells'][$i][5])) {
                            array_push($suj_error_list, $sheets['cells'][$i]);
                            continue;
                        }

                        if (!isset($sheets['cells'][$i][6])) {
                            array_push($suj_error_list, $sheets['cells'][$i]);
                            continue;
                        }

                        if (!isset($sheets['cells'][$i][7])) {
                            array_push($suj_error_list, $sheets['cells'][$i]);
                            continue;
                        }
                    }

                    $setGuid = $helpers->generateGuid();
                    $getDate = date("Y-m-d");
                    $getRequester = $this->session->userdata('userNameFull');
                    $getReqLevelDetail = $this->session->userdata('sessionLevelDetail');
                    $getRequesterSn = $this->session->userdata('userEmpID');
                    $getUserID = $this->session->userdata('userName');
                    $getUserStat = $this->session->userdata('userStat');
                    $getUserDept = $this->session->userdata('userDept');
                    $getUserDiv = $this->session->userdata('userDiv');
                    $getReqType = isset($_POST['cmbCashType']) && ($_POST['cmbCashType'] != "") ?
                            $_POST['cmbCashType'] : 'trans14';
                    $getAmount = isset($sheets['cells'][$i][5]) ? $sheets['cells'][$i][5] : 0;
                    $getDirectToIdByUserReq = $ajaxManager->getDirectReportToId($getRequesterSn);
                    $getLastApproval = $getDirectToIdByUserReq;
                    $getLastPaymentApproval = $getDirectToIdByUserReq;
                    $getUserSess = $this->session->userdata('userName');
                    $getUserSessionLevelDetail = $this->session->userdata('sessionLevelDetail');

                    $getLastApprovalMsgDetail = $ajaxManager->initLastApproval($getReqType, $getAmount, 0, "CONTROLLER");
                    $getLastApprovalMsgDetail2 = $ajaxManager->initUserApproval($getUserSessionLevelDetail, $getLastApprovalMsgDetail['hidApprove'], 0, "CONTROLLER");
                    $getApprovalFlow = substr($getLastApprovalMsgDetail2['hidApvFlow'], 0, -1);

                    $getFirstApprover = explode(",", $getApprovalFlow);
                    // insert to t_ca_trans
                    $tblName = "t_ca_trans";
                    $data = array(
                        'ca_guid' => $setGuid,
                        'date_request' => $getDate,
                        'requester_name' => $getRequester,
                        'requester_level_detail' => $getReqLevelDetail,
                        'requester_sn' => $getRequesterSn,
                        'requester_userid' => $getUserID,
                        'requester_station' => $getUserStat,
                        'requester_dept' => $getUserDept,
                        'requester_div' => $getUserDiv,
                        'request_type' => $getReqType,
                        'request_amount' => $isSUJ ? $suj->total_cost : $getAmount,
                        'last_appv' => $getLastApproval,
                        'payment_apv' => $getLastPaymentApproval,
                        'appv_flow' => $isSUJ ? $getDirectToIdByUserReq : $getApprovalFlow,
                        'appv_flow_status' => $isSUJ ? $getDirectToIdByUserReq : $getFirstApprover[0], //here new cash complete flow
                        'input_user' => $getUserSess,
                        'input_datetime' => date("Y-m-d H:i:s"),
                        'purpose' => isset($sheets['cells'][$i][8]) ? $sheets['cells'][$i][8] : ""
                    );
                    $execData = $this->db->insert($tblName, $data);

                    //here for add approval step
                    if ($execData) {

                        if ($isSUJ) {
                            $tblName = "tr_trans_approve_status";
                            $data = array(
                                'ca_guid' => $setGuid,
                                'approver' => $getDirectToIdByUserReq,
                                'approve_type' => 'STD',
                                'inputUser' => $getUserSess,
                                'inputDateTime' => date("Y-m-d H:i:s")
                            );
                            $execData = $this->db->insert($tblName, $data);
                        } else if ($sujno == "0") {
                            $appStep = explode(",", $getApprovalFlow);
                            foreach ($appStep as $appv) {
                                $tblName = "tr_trans_approve_status";
                                $data = array(
                                    'ca_guid' => $setGuid,
                                    'approver' => $appv,
                                    'approve_type' => 'STD',
                                    'inputUser' => $getUserSess,
                                    'inputDateTime' => date("Y-m-d H:i:s")
                                );
                                $execData = $this->db->insert($tblName, $data);
                            }
                        }
                    } // end if
                    //var_dump($suj_not_founded); die();

                    if ($execData) {

                        if ($isSUJ) {
                            $getIdCostTypeRate = $suj->ratesid;
                            $fleetSchedule = $suj->fleet_schedule;
                            $amount = $suj->total_cost;
                            $data = array(
                                'ca_guid' => $setGuid,
                                'id_cost_type_rate' => $getIdCostTypeRate,
                                'insert_by' => $getRequester,
                                'insert_ts' => date("Y-m-d H:i:s"),
                                'update_by' => $getRequester,
                                'update_ts' => date("Y-m-d H:i:s"),
                                'isSuj' => 1,
                                'fleet_schedule' => $fleetSchedule
                            );
                            $execData = $this->db->insert('tt_suj_transaction', $data);
                        } else if ($sujno == "0") {


                            $origin = $sheets['cells'][$i][2];
                            $destination = $sheets['cells'][$i][3];
                            $truck_type = $sheets['cells'][$i][4];
                            $data = array(
                                'ca_guid' => $setGuid,
                                'insert_by' => $getRequester,
                                'insert_ts' => date("Y-m-d H:i:s"),
                                'update_by' => $getRequester,
                                'update_ts' => date("Y-m-d H:i:s"),
                                'isSuj' => 0,
                                'origin' => $origin,
                                'destination' => $destination,
                                'truck_type' => $truck_type
                            );
                            $execData = $this->db->insert('tt_suj_transaction', $data);
                        }
                    }

                    if ($execData) {


                        $data = array(
                            'ca_guid' => $setGuid,
                            'bank_drivername' => $sheets['cells'][$i][6],
                            'bank_driverrekno' => $sheets['cells'][$i][7],
                            'insert_by' => $getRequester,
                            'insert_ts' => date("Y-m-d H:i:s"),
                            'update_by' => $getRequester,
                            'update_ts' => date("Y-m-d H:i:s"),
                        );
                        $execData = $this->db->insert('tr_driver_account', $data);

                        if (!$execData) {
                            $datas["error"] = "Upload Error";
                            break;
                        }
                    }
                } // end if
            }
        }


        if (count($suj_not_founded) > 0) {
            $message_tca = "SUJ Code : ";
            for ($i = 0; $i < count($suj_not_founded); $i++) {
                $message_tca = $message_tca . "<b>" . $suj_not_founded[$i] . "</b>, ";
            }

            $message_tca = rtrim($message_tca, ", ");
            $datas["error"] = $message_tca . " not uploaded";
            $this->exportError($suj_error_list);
        } else if (count($suj_error_list) > 0) {
            $this->exportError($suj_error_list);
        }

        $this->load->view('ca_header');
        $this->load->view('ca_bread', $datas);
        $this->load->view('upload_request');
        $this->load->view('ca_footer');
    }

    function exportError($suj_error_list) {

        $tableData = "<table><tr>
		<td>SUJ Code</td><td>Origin</td><td>Destination</td><td>Truck Type</td><td><Advance Amount/td>
		<td>Nama Driver</td><td>No Rekening</td><td>Remark</td></tr>";

        for ($i = 0; $i < count($suj_error_list); $i++) {
            $column1 = isset($suj_error_list[$i][1]) ? $suj_error_list[$i][1] : "";
            $column2 = isset($suj_error_list[$i][2]) ? $suj_error_list[$i][2] : "";
            $column3 = isset($suj_error_list[$i][3]) ? $suj_error_list[$i][3] : "";
            $column4 = isset($suj_error_list[$i][4]) ? $suj_error_list[$i][4] : "";
            $column5 = isset($suj_error_list[$i][5]) ? $suj_error_list[$i][5] : "";
            $column6 = isset($suj_error_list[$i][6]) ? $suj_error_list[$i][6] : "";
            $column7 = isset($suj_error_list[$i][7]) ? $suj_error_list[$i][7] : "";
            $column8 = isset($suj_error_list[$i][8]) ? $suj_error_list[$i][8] : "";

            $tableData .= '<tr>
		        <td>' . $column1 . '</td>
		        <td>' . $column2 . '</td>
		        <td>' . $column3 . '</td>
		        <td>' . $column4 . '</td>
		        <td>' . $column5 . '</td>
		        <td>' . $column6 . '</td>
		        <td>' . $column7 . '</td>
		        <td>' . $column8 . '</td>
		        </tr>';
        }

        $tableData .= '</table>';

        $filename = date("Ymd") . "_request-error.xls";
        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");

        echo $tableData;
        exit(header("Content-type: application/xls"));
    }

    function do_upload_settle() {
        log_message('debug', 'gp>>> mulai do_upload_settle');
        $currentDateTime = date('ymdHis');
        log_message('debug', 'gp>>> akan simpan file ' . $currentDateTime);
        $newFileName = pathinfo($_FILES['userfile']['name'], PATHINFO_FILENAME)."_".$currentDateTime.".".pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
        log_message('debug', 'gp>>> akan simpan file ' . $newFileName);
        
        $config = array(
            'upload_path' => FCPATH . 'upload/',
            'allowed_types' => 'xls',
            'overwrite' => TRUE,
            'file_name' => $newFileName
        );
        $this->load->library('upload', $config);
        $this->load->library('Spreadsheet_Excel_Reader');

        $datas["header"] = "Upload Settle";
        $datas["error"] = "";

        $getRequesterSn = $this->session->userdata('userEmpID');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');
        $getUserSessionLevelDetail = $this->session->userdata('sessionLevelDetail');

        $nextSuperior = $this->getNextSuperior($getRequesterSn);
        $ajaxManager = new ajax_manager();
        $helpers = new helpers();
        
        log_message('debug', 'gp>>> ambil dari direct report');
        $getDirectToIdByUserReq = $ajaxManager->getDirectReportToId($getRequesterSn);
        $getSessUser = $nextSuperior[0];
        $getSessUserFull = $nextSuperior[1];
        $getUserJobDetail = $nextSuperior[2];

        log_message('debug', 'gp>>> akan simpan file' . implode(" ", $_FILES['userfile']));
        if ($this->upload->do_upload('userfile')) {
            log_message('debug', 'gp>>> file berhasil disimpan');
            $data = $this->upload->data();
            @chmod($data['full_path'], 0777);
            $this->spreadsheet_excel_reader->setOutputEncoding('CP1251');
            $this->spreadsheet_excel_reader->read($data['full_path']);
            $sheets = $this->spreadsheet_excel_reader->sheets[0];
            $tca_not_success = array();
            $tca_settle_error_list = array();
            $tcano_array = array();
            $tcano_id_array = array();
            $dataTrans = array();
            $dataExcel = 0;

            for ($i = 2; $i <= $sheets['numRows']; $i++) {
                if (isset($sheets['cells'][$i])) {
                    $dataExcel++;
                }
            }

            if ($dataExcel > 10) {
                echo '<script>alert("Maximum allowed data are 10 rows!");</script>';
            } else {
                $datas["error"] = "Upload Success";
                for ($i = 2; $i <= $sheets['numRows']; $i++) {
                    if (isset($sheets['cells'][$i])) {
                        $tcano = $sheets['cells'][$i][1];
                        $tcano = trim($tcano);
                        $tcanos = explode("-", $tcano);
                        $tca_no = $tcanos[0];
                        // validate no manifest
                        $manifestno = isset($sheets['cells'][$i][3]) ? $sheets['cells'][$i][3] : "";
                        $manifestno = trim($manifestno);

                        if (($manifestno == "") || (strlen($manifestno) != 12) || ($manifestno[0] != "9")) {
                            array_push($tca_not_success, $tcano);
                            array_push($tca_settle_error_list, $sheets['cells'][$i]);
                            continue;
                        }

                        log_message('debug', 'gp>>> ambil data dari table t_ca_trans');
                        $t_ca_trans = $this->GetMainTransaction($tcano);
                        $isSUJ = $this->IsSuj($tcano);
                        $getReqType = $t_ca_trans->request_type;
                        $getAmount = $t_ca_trans->request_amount;

                        $getLastApprovalMsgDetail = $ajaxManager->initLastApproval($getReqType, $getAmount, 0, "CONTROLLER");
                        $getLastApprovalMsgDetail2 = $ajaxManager->initUserApproval($getUserSessionLevelDetail, $getLastApprovalMsgDetail['hidApprove'], 0, "CONTROLLER");
                        $getApprovalFlow = substr($getLastApprovalMsgDetail2['hidApvFlow'], 0, -1);
                        $getFirstApprover = explode(",", $getApprovalFlow);

                        //$settled_amount = $this->GetSettleAmount($tcano);
                        $settled_amount = $isSUJ ? $t_ca_trans->request_amount : $sheets['cells'][$i][2];
                        $appv_complete = 3;
                        // validate settle
                        if ($settled_amount == null) {
                            array_push($tca_not_success, $tcano);
                            array_push($tca_settle_error_list, $sheets['cells'][$i]);
                            continue;
                        }
                        $tblName = "t_ca_trans";
                        $data = array(
                            'settled_amount' => $settled_amount,
                            'isdelete' => 0,
                            'appv_complete' => $appv_complete,
                            'appv_flow_settle' => $isSUJ ? $getDirectToIdByUserReq : $getApprovalFlow,
                            'appv_flow_settle_status' => $isSUJ ? $getDirectToIdByUserReq : $getFirstApprover[0]
                        );
                        $this->db->where('id_ca_trans = ', $tca_no);
                        log_message('debug', 'gp>>> akan update ke table t_ca_trans');
                        $execData = $this->db->update($tblName, $data);

                        if (!$execData) {
                            $datas["error"] = "Upload Error";
                            array_push($tca_not_success, $tcano);
                            array_push($tca_settle_error_list, $sheets['cells'][$i]);
                            continue;
                        } else {
                            //insert to manifest

                            if (!$this->IsManifestExist($tcano)) {
                                $tblName = "tr_manifest";
                                $data = array(
                                    'id_ca_trans' => $t_ca_trans->id_ca_trans,
                                    'ca_guid' => $t_ca_trans->ca_guid,
                                    'no_manifest' => $sheets['cells'][$i][3],
                                    'created_by' => $this->session->userdata('userEmpID')
                                );
                                log_message('debug', 'gp>>> akan insert ke tabel tr_manifest');
                                $execData = $this->db->insert($tblName, $data);
                            }

                            if ($execData) {
                                $tblName = "tr_trans_approve_status";
                                if ($isSUJ) {
                                    $data = array(
                                        //'approver' => $getUserJobDetail,
                                        'approve_type' => 'SET'
                                    );
                                    $this->db->where('ca_guid', $t_ca_trans->ca_guid);
                                    log_message('debug', 'gp>>> akan update ke tabel tr_trans_approve_status suj');
                                    $execData = $this->db->update($tblName, $data);

                                    if (!$execData) {
                                        $datas["error"] = "Upload Error";
                                        break;
                                    } else {
                                        $transapproveStatus = $this->GetTransApproveStatus($t_ca_trans->ca_guid);
                                        $ajaxManager->settleApproved($transapproveStatus->apprv_id, $getSessUser, $getSessUserFull, $getUserJobDetail);
                                    }
                                } else {
                                    $appStep = explode(",", $getApprovalFlow);
                                    foreach ($appStep as $appv) {
                                        $tblName = "tr_trans_approve_status";
                                        $data = array(
                                            //'ca_guid' => $setGuid,
                                            //'approver' => $appv,
                                            'approve_type' => 'SET',
                                            //'inputUser' => $getUserSess,
                                            'inputDateTime' => date("Y-m-d H:i:s")
                                        );
                                        $this->db->where('ca_guid', $t_ca_trans->ca_guid);
                                        $this->db->where('approver', $appv);
                                        log_message('debug', 'gp>>> akan update ke tabel tr_trans_approve_status');
                                        $execData = $this->db->update($tblName, $data);
                                    }
                                }

                                if (!$execData) {
                                    $datas["error"] = "Upload Error";
                                }
                            }
                        }
                    }
                }
            }

            if (count($tca_not_success) > 0) {
                $message_tca = "TCA NO : ";
                for ($i = 0; $i < count($tca_not_success); $i++) {
                    $message_tca = $message_tca . "<b>" . $tca_not_success[$i] . "</b>, ";
                }
                $datas["error"] = $message_tca . " not uploaded";
                $this->exportErrorSettle($tca_settle_error_list);
            }
        }
        $this->load->view('ca_header');
        $this->load->view('ca_bread', $datas);
        $this->load->view('upload_settle');
        $this->load->view('ca_footer');
    }

    function exportErrorSettle($tca_settle_error_list) {
        $tableData = "<table><tr>
		<td>Nomor TCA</td><td>Settlement Amount</td><td>Manifest</td></tr>";

        for ($i = 0; $i < count($tca_settle_error_list); $i++) {
            $column1 = isset($tca_settle_error_list[$i][1]) ? $tca_settle_error_list[$i][1] : "";
            $column2 = isset($tca_settle_error_list[$i][2]) ? $tca_settle_error_list[$i][2] : "";
            $column3 = isset($tca_settle_error_list[$i][3]) ? $tca_settle_error_list[$i][3] : "";

            $tableData .= '<tr>
		        <td>' . $column1 . '</td>
		        <td>' . $column2 . '</td>
		        <td>' . $column3 . '</td>
		        </tr>';
        }

        $tableData .= '</table>';

        $filename = date("Ymd") . "settle-error.xls";
        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");

        echo $tableData;
        exit(header("Content-type: application/xls"));
    }

    function IsValidTCA($tcano) {
        /*
          $config['hostname'] = "10.10.3.11";
          $config['username'] = "sa";
          $config['password'] = "#sqls3rv3r4dm1n#";
          $config['database'] = "fms";
          $config['dbdriver'] = "mysql";
          $config['dbprefix'] = "";
          $config['pconnect'] = FALSE;
          $config['db_debug'] = FALSE;
          $config['cache_on'] = FALSE;
          $config['cachedir'] = "";
          $config['char_set'] = "utf8";
          $config['dbcollat'] = "utf8_general_ci";
         */

        $serverHost = "10.10.3.11";
        $usr = "sa";
        $pwd = "#sqls3rv3r4dm1n#";
        $db = "enterprise";

        $tcanos = explode("-", $tcano);
        $tca_no = $tcanos[0];

        $connectionInfo = array("UID" => $usr, "PWD" => $pwd, "Database" => $db);

        $conn = sqlsrv_connect($serverHost, $connectionInfo);

        $query = "SELECT * FROM fms.tt_expense_header WHERE tca_no = '" . $tca_no . "' AND is_delete='N'";

        $res = sqlsrv_query($conn, $query);

        while ($row = sqlsrv_fetch_array($res)) {
            return true;
        }

        return false;
    }

    function getDirectReportToID($employeeid) {
        $DBca = $this->load->database();

        $sql = "SELECT * FROM t_direct_report WHERE employee_id = '" . $employeeid . "'";

        $res = $this->db->query($sql);

        foreach ($res->result() as $row) {
            return $row->direct_report_to_id;
        }
    }

    function getDirectReport($job_title) {
        $DBca = $this->load->database();

        $sql = "SELECT * FROM t_direct_report WHERE job_title_detail = '" . $job_title . "'";

        $res = $this->db->query($sql);

        foreach ($res->result() as $row) {
            return $row;
        }
    }

    function getUserData($employeeid) {
        $res = array();

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

        $setStrSqlLogin = "SELECT user_access.t_application_access.* , user_access.t_user.employee_id, user_access.t_user.valid_to AS user_validto , CONCAT_WS(' ',employee.t_personel.first_name,employee.t_personel.middle_name,employee.t_personel.last_name) AS fullname , user_access.t_user.password,user_access.t_application_access.user_level_id
                            ,employee.t_personel.area_id,employee.t_personel.station_id, employee.t_personel.division_id,employee.t_personel.department_id,employee.t_division.description divDesc,employee.t_department.description deptDesc
                            FROM  user_access.t_application_access
                            LEFT JOIN user_access.t_user ON (t_application_access.user_id = t_user.user_id)
                            LEFT JOIN employee.t_employee  ON (employee.t_employee.employee_id = user_access.t_user.employee_id)
                            LEFT JOIN employee.t_personel  ON ( employee.t_personel.employee_id = user_access.t_user.employee_id)
                            LEFT JOIN employee.t_division ON ( employee.t_division.division_id = employee.t_personel.division_id)
                            LEFT JOIN employee.t_department ON ( employee.t_department.department_id = employee.t_personel.department_id)
                            WHERE user_access.t_user.employee_id = '" . $employeeid . "'";

        $userLogin = $DBfast->query($setStrSqlLogin);

        foreach ($userLogin->result() as $row) {
            $res[0] = $row->user_id;
            $res[1] = $row->fullname;

            return $res;
        }
    }

    function getNextSuperior($employeeid) {
        $res = array();

        $report_to_id = $this->getDirectReportToID($employeeid);
        $direct_report = $this->getDirectReport($report_to_id);
        $userdata = $this->getUserData($direct_report->employee_id);

        $res[0] = $userdata[0];
        $res[1] = $userdata[1];
        $res[2] = $direct_report->job_title_detail;

        return $res;
    }

}

?>