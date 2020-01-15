<?php include_once 'helpers.php'; ?>
<?php include_once 'ajax_manager.php'; ?>
<?php

class ca_core extends Controller {

    function ca_core() {
        parent::Controller();
        $this->load->library(array('form_validation'));
        $this->load->library('csvimport');
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
        $this->load->view('template');
    }

    //tambahan by alfan
    function uploadmastercostrate(){
        $this->load->view('ca_upload_master_cost_rate');
    }
    

    function import_data() {
        error_reporting(0);
  //  echo 'test';
        if($_POST['upload']){
      $ekstensi_diperbolehkan = array('xls');
      $nama = $_FILES['file']['name'];
      //echo 'Error during file upload' . $_FILES['file']['error'];
      $x = explode('.', $nama);
      $ekstensi = strtolower(end($x));
      //echo $nama;die;
      $ukuran = $_FILES['file']['size'];
      $file_tmp = $_FILES['file']['tmp_name'];  
 
      if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
        if($ukuran < 1044070){      
          move_uploaded_file($file_tmp, 'file/'.$nama);
          $this->load->library('Spreadsheet_Excel_Reader');
                               $this->spreadsheet_excel_reader->setOutputEncoding('CP1251'); 
                               $this->spreadsheet_excel_reader->read($file_tmp);
                               $sheets = $this->spreadsheet_excel_reader->sheets[0];

                               //error_reporting(0);

                               
                               
                               for ($i = 1; $i <= $sheets['numRows']; $i++) {

                                   $expired = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($sheets['cells'][$i][10])); 
                                   $data_excel = array(
                                        'suj_id' => $sheets['cells'][$i][1],
                    'area_id' => $sheets['cells'][$i][2],
                                        'deployment' => $sheets['cells'][$i][3],
                                        'fleet_schedule' => $sheets['cells'][$i][4],
                                        'origin_trip' => $sheets['cells'][$i][5],
                                        'truck_type' => $sheets['cells'][$i][6],
                                        'distance' => $sheets['cells'][$i][7],
                                        'rationbbm' => $sheets['cells'][$i][8],
                                        'total_cost' => $sheets['cells'][$i][9],
                                        'expired' => $sheets['cells'][$i][10]
                                   ); 

                                   $this->db->insert('tr_cost_type_rates', $data_excel);                                
                                               
                               }     
                                
                                //print_r($data_excel);                             

                               @unlink($data['full_path']); //if data already exist, posible to reupload
                               if($this->db->affected_rows() > 0){
                                   @unlink($data['full_path']); //if data already exist, posible to reupload
                                  echo 'File successfully uploaded : uploads/' . $_FILES['file']['name'] .' and total rows inserted:'; 

                               }else{
                                   echo 'the file failed to insert to database';
                               }
        }else{
          echo 'UKURAN FILE TERLALU BESAR';
        }
      }else{
        echo 'EKSTENSI FILE YANG DI UPLOAD TIDAK DI PERBOLEHKAN';
      }
    }
        $this->uploadmastercostrate();
          /*  $config = array(
                'upload_path'   => FCPATH.'upload/',
        //'upload_path'   => 'D:\\Temp\\upload\\',
                'allowed_types' => 'xls',
            );
        
               if (isset($_FILES['file']['name'])) {
                   if (0 < $_FILES['file']['error']) {
                       echo 'Error during file upload' . $_FILES['file']['error'];
                   } else {
                       if (file_exists('upload/' . $_FILES['file']['name'])) {
                           echo 'File already exists : upload/' . $_FILES['file']['name'];
                       } else {
                           $this->load->library('upload', $config);
               
               //$this->upload->initialize($config);
               //print_r($config);
               //die;
             //  echo 
                           if (!$this->upload->do_upload('file')) {
               //  var_dump($this->file_type);
                //die;
                               echo $this->upload->display_errors();
                           } else {
                               

                               $data = $this->upload->data();
                               @chmod($data['full_path'], 0777);

                               $this->load->library('Spreadsheet_Excel_Reader');
                               $this->spreadsheet_excel_reader->setOutputEncoding('CP1251'); 
                               $this->spreadsheet_excel_reader->read($data['full_path']);
                               $sheets = $this->spreadsheet_excel_reader->sheets[0];

                               //error_reporting(0);

                               
                               
                               for ($i = 1; $i <= $sheets['numRows']; $i++) {

                                   $expired = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($sheets['cells'][$i][9])); 
                                   $data_excel = array(
                                        'suj_id' => $sheets['cells'][$i][1],
                                        'deployment' => $sheets['cells'][$i][2],
                                        'fleet_schedule' => $sheets['cells'][$i][3],
                                        'origin_trip' => $sheets['cells'][$i][4],
                                        'truck_type' => $sheets['cells'][$i][5],
                                        'distance' => $sheets['cells'][$i][6],
                                        'rationbbm' => $sheets['cells'][$i][7],
                                        'total_cost' => $sheets['cells'][$i][$sheets['cells'][$i][8]],
                                        'expired' => $expired
                                   ); 

                                   $this->db->insert('tr_cost_type_rates', $data_excel);                                
                                               
                               }     
                                
                                //print_r($data_excel);                             

                               @unlink($data['full_path']); //if data already exist, posible to reupload
                               if($this->db->affected_rows() > 0){
                                   @unlink($data['full_path']); //if data already exist, posible to reupload
                                  echo 'File successfully uploaded : uploads/' . $_FILES['file']['name'] .' and total rows inserted:'; 

                               }else{
                                   echo 'the file failed to insert to database';
                               }
                   
                           }
                       }
                   }
               } else {
                   echo 'Silahkan pilih data';
               }
         */
    }

    

    function newcash() {
        $this->load->library('session');
        $helpers = new helpers();

        $chkUploadCA = "";

        $throwData['header'] = "<img src=\"" . base_url() . "includes/icons/page_white_edit.png\" alt=\"\"/> New Cash Advance";
        $throwData['userLevDetail'] = $this->session->userdata('sessionLevelDetail');
        $throwData['userNameFull'] = $this->session->userdata('userNameFull');
        $throwData['currDate'] = date("d-m-Y");
        //$throwData['cmbCashType'] = $helpers->initCmbAllValue_cond("cmbCashType", "t_jenis_transaksi", "desc_trans", "id_trans", "");
        $throwData['cmbCashType'] = $helpers->initCmbAllValue_cond2("cmbCashType", "t_jenis_transaksi", "desc_trans", "id_trans", "",$this->session->userdata('userEmpID'));
        $throwData['cmbCity'] = $helpers->initMasterCity();
        $throwData['cmbDest'] = $helpers->initMasterDestination();
        $throwData['cmbTruck']= $helpers->initMasterTrucTypeTest();
        $throwData['area_id']= $this->session->userdata['area_id'];

        $throwData['userDiv'] = $this->session->userdata('userDiv');
        $throwData['userDept'] = $this->session->userdata('userDept');

        $throwData['chkUploadCA'] = $chkUploadCA;
        $throwData['cmbCashDept'] = $helpers->initMasterDepartment();

        $this->load->view('ca_newcash', $throwData);
    }

    function settleCash($getId) {
        //modofy by alfan
        $setWhereArry = array('id_ca_trans' => $getId);
        $this->db->select('a.*,b.desc_trans,c.isSuj')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->join('tt_suj_transaction c', 'a.ca_guid = c.ca_guid', 'left')
                ->where($setWhereArry);

        $getQuery = $this->db->get();

        $helpers = new helpers();
        $throwData['header'] = "Settle Cash Advance";

        foreach ($getQuery->result() as $rows) {
            $throwData['txtDate'] = $helpers->convertDateToSimple($rows->date_request);
            $throwData['txtRequester'] = $rows->requester_name;
            $throwData['cashType'] = $rows->desc_trans;
      $throwData['cashTypeVal'] = $rows->request_type;
      
            $throwData['cashTypeVal'] = $rows->request_type;
            $throwData['userLevelVal'] = $this->session->userdata('sessionLevelDetail'); 
            $throwData['lastApproval'] = $rows->last_appv;

            $throwData['cashAmount'] = $helpers->currencyFormat($rows->request_amount);
            $throwData['txtPurpose'] = $rows->purpose;

            $throwData['caGuid'] = $rows->ca_guid;

            $getStatus = "<span class=\"redSpan\">Uncomplete</span>";
            if ($rows->appv_complete == "1" || $rows->appv_complete == "7") {
                $getStatus = " <span class=\"greenSpan\">Approved</span> <br/>(<b>Settlement Required</b>)";
                $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/MM_main/marketingEventEdit/' . $rows->id_ca_trans . ' \'" />';
            } elseif ($rows->appv_complete == "2") {
                $getStatus = "<span class=\"redSpan\">Rejected</span>";
                $editDeleteButton = '<input title="Rejected" type="image" src="' . base_url() . 'includes/icons/block.png" />';
            } elseif ($rows->appv_complete == "3") {
                $getStatus = "<span class=\"blueSpan\">Settled</span>";
                $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/accept.png" />';
            }

            $throwData['caStatus'] = $getStatus;

            //aditional by Alfan
            $throwData['isSuj'] = $rows->isSuj;
        }

        $throwData['caId'] = $getId;

//        $helpers = new helpers();
//        $throwData['header'] = "Settle Cash Advance";
//        $throwData['userLevDetail'] = $this->session->userdata('sessionLevelDetail');
//        $throwData['userNameFull'] = $this->session->userdata('userNameFull');
//        
//        $throwData['cmbCashType'] = $helpers->initCmbAllValue_cond("cmbCashType", "t_jenis_transaksi", "desc_trans", "id_trans", "");

        $this->load->view('ca_settlement_action', $throwData);
    }

    function saveNewCash() {

        $getUserID = $this->session->userdata('userName');
        $getUserStat = $this->session->userdata('userStat');
        $getUserDiv = $this->session->userdata('userDiv');
        $getUserDept = $this->session->userdata('userDept');
        $getUserSessionLevelDetail = $this->session->userdata('sessionLevelDetail');

        $helpers = new helpers();
        $ajaxManager = new ajax_manager();
        $setGuid = $helpers->generateGuid();

        $getDate = date("Y-m-d", strtotime($_POST['txtCaDate']));
        $getRequester = $_POST['txtRequester'];
        $getReqLevelDetail = $_POST['hidUserLevel'];
        $getRequesterSn = $this->session->userdata('userEmpID');
        $getReqType = $_POST['cmbCashType'];
        $getAmount = str_replace(".", "", $_POST['txtAmount']);

        if ($getUserDiv == 'PLO'){
            $getBookingNo = $_POST['txtBookingNo'];
        } else{
            $getBookingNo = "";
        }

        if (isset($_POST['chkUploadCA'])){
            $getUploadCA = "";
            $getDANo = "";
            $getAjuNo = "";
            $getItemCash = "";
            $getRefNo = "";
            $getRemarkDN = "";
            $getCostName = "";
            // $getEstimateValue = "NULL";
            $getPICTransfer = "";
            $getBankName = "";
            $getNoRekPIC = "";
        } else{
            $getDANo = $_POST['txtDANo'];
            $getAjuNo = $_POST['txtAJU'];
            $getItemCash = $_POST['txtItemCash'];
            $getRefNo = $_POST['txtRefNo'];
            $getRemarkDN = $_POST['txtRemarkDN'];
            $getCostName = $_POST['txtCostName'];
            $getEstimateValue = str_replace(".", "", $_POST['txtEstimateValue']);
            $getPICTransfer = $_POST['txtPICTransfer'];
            $getBankName = $_POST['txtBankName'];
            $getNoRekPIC = $_POST['txtNoRekPIC'];
        }
    
    /*
        $getLastApproval = $_POST['hidApprove'];
        $getLastPaymentApproval = $_POST['hidApprovePay'];
        $getApprovalFlow = substr($_POST['hidApvFlow'], 0, (strlen($_POST['hidApvFlow']) - 1));
    */
    
        $getLastApprovalMsgDetail  = $ajaxManager->initLastApproval($getReqType,$getAmount, 0, "CONTROLLER");
        $getLastApprovalMsgDetail2  = $ajaxManager->initUserApproval($getUserSessionLevelDetail,$getLastApprovalMsgDetail['hidApprove'], 0, "CONTROLLER");
        $getLastApproval = $getLastApprovalMsgDetail['hidApprove'];
        $getLastPaymentApproval = $getLastApprovalMsgDetail['hidApprovePay'];
        $getApprovalFlow = substr($getLastApprovalMsgDetail2['hidApvFlow'], 0,-1);
    
        $getPurpose = str_replace("'","\\'",$_POST['txtPurpose']);
        $getUserSess = $this->session->userdata('userName');


        //here upload engine====================================================
        $uploaddir = '_UploadedFiles/';
        //$uploadfileCustomName = "upldMM_Event_" . date("dMY_Hm") . "_";
        $uploadfileCustomName = $getRequesterSn . "_";
        $uploadfile = $uploaddir . $uploadfileCustomName . basename($_FILES['UpldFile']['name']);

        $fileNameReal = "";
        $fileNameAtServer = "";

        if ($_FILES['UpldFile']['name'] == null) {
            $fileNameReal = '';
            $fileNameAtServer = '';
        } else {
            if (move_uploaded_file($_FILES['UpldFile']['tmp_name'], $uploadfile)) {
                $fileNameReal = basename($_FILES['UpldFile']['name']);
                $fileNameAtServer = basename($uploadfileCustomName . $_FILES['UpldFile']['name']);
            }
        }
        //======================================================================

        // additional by yudhis ====================================================
        // $uploaddir2 = '_UploadedFiles/';
        $uploaddir2 = 'upload/';
        //$uploadfileCustomName = "upldMM_Event_" . date("dMY_Hm") . "_";
        // $uploadfileCustomName2 = $getRequesterSn . "_";
        $uploadfileCustomName2 = "";
        // $uploadfile2 = $uploaddir2 . $uploadfileCustomName2 . basename($_FILES['uploadBulkCA']['name']);

        // $fileNameReal2 = "";
        // $fileNameAtServer2 = "";

        if ($_FILES['uploadBulkCA']['name'] == null) {
            $fileNameReal2 = '';
            $fileNameAtServer2 = '';
        } else {
            $uploadfile2 = $uploaddir2 . $uploadfileCustomName2 . basename($_FILES['uploadBulkCA']['name']);
            if (move_uploaded_file($_FILES['uploadBulkCA']['tmp_name'], $uploadfile2)) {
                $fileNameReal2 = basename($_FILES['uploadBulkCA']['name']);
                $fileNameAtServer2 = basename($uploadfileCustomName2 . $_FILES['uploadBulkCA']['name']);
            }
        }
        //======================================================================

        //here override get first user approver
        $getFirstApprover = explode(",", $getApprovalFlow);

        //=======================================
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
            'request_amount' => $getAmount,
            'last_appv' => $getLastApproval,
            'payment_apv' => $getLastPaymentApproval,
            'appv_flow' => $getApprovalFlow,
            'appv_flow_status' => $getFirstApprover[0], //here new cash complete flow
//            'appv_flow_settle' => $getApprovalFlow, //here settled cash complete flow
            'purpose' => $getPurpose,
            'upload_f1' => $fileNameAtServer,
            'input_user' => $getUserSess,
            'input_datetime' => date("Y-m-d H:i:s"),

            'booking_no' => $getBookingNo
        );

        // print_r($data);exit;
        $execData = $this->db->insert($tblName, $data);

        //here for add approval step
        if ($execData) {

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
        //==========================

        //here for insert to second table t_ca_trans_exim
        if ($execData) {
          $tblName = "t_ca_trans_exim";
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
              'request_amount' => $getAmount,
              'last_appv' => $getLastApproval,
              'payment_apv' => $getLastPaymentApproval,
              'appv_flow' => $getApprovalFlow,
              'appv_flow_status' => $getFirstApprover[0], //here new cash complete flow
  //            'appv_flow_settle' => $getApprovalFlow, //here settled cash complete flow
              'purpose' => $getPurpose,
              'upload_f1' => $fileNameAtServer,
              'upload_f2' => $fileNameAtServer2,
              'input_user' => $getUserSess,
              'input_datetime' => date("Y-m-d H:i:s"),

              'da_no' => $getDANo,
              'aju_no' => $getAjuNo,
              'item_ca' => $getItemCash,
              'ref_no' => $getRefNo,
              'remark_dn' => $getRemarkDN,
              'cost_name' => $getCostName,
              'estimate_value' => (!empty($getEstimateValue)) ? $getEstimateValue : NULL,
              'pic_transfer' => $getPICTransfer,
              'bank_name' => $getBankName,
              'rek_no' => $getNoRekPIC,
              'booking_no' => $getBookingNo
          );

          // print_r($data);exit;
          $execData = $this->db->insert($tblName, $data);
        }
        // ============================================================

        if ($execData) {
            $this->sendEmailToNextAppv($appStep[0], $setGuid, "STD");
        }
        // redirect('ca_core/cahistory');

        if (isset($_POST['chkUploadCA'])){
            redirect('ca_core/readExcel');
        } else{
            redirect('ca_core/cahistory');          
        }

    }


    // additional by yudhis
    function readExcel(){
      $getUserID = $this->session->userdata('userName');

      $date_request = date('Y-m-d');
      // $file = './_UploadedFiles/request_materai.xls';
      $file = './upload/Bulk_CA_'. date("Ymd") .'.xls';
 
      //load the excel library
      // $this->load->library('excel');
       
      //read file from path      
      $objPHPExcel = PHPExcel_IOFactory::load($file);

       
      //get only the Cell Collection
      $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
       
      //extract to a PHP readable array format
      foreach ($cell_collection as $cell) {
          $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
          $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
          $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();

          $numRow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
       
          //The header will/should be in row 1 only. of course, this can be modified to suit your need.
          if ($row == 1) {
              $header[$row][$column] = $data_value; 
              // echo $header[$row][$column];
          } else {
              $arr_data[$row][$column] = $data_value;
              // echo $arr_data[$row][$column];
          }
      }
       
      //send the data in an array format
      $data['header'] = $header;
      $data['values'] = $arr_data;

      $totalRow = $numRow;
      echo $totalRow;

      for ($i = 2; $i <= $totalRow; $i++) {

        $da_no = $data['values'][$i]['A'];
        $len_da_no = strlen($da_no);
        if ($len_da_no == "12" && is_numeric($da_no)){

        }

        $remark_dn = $data['values'][$i]['E'];
        if ($remark_dn == "DN"){
          $remark_dn_new = "DA MGF";
        } elseif ($remark_dn == "Cost") {
          $remark_dn_new = "DA Clearance";
        } else {
          $remark_dn_new = $data['values'][$i]['E'];
        }

        $data_excel = array(
          // 'da_no' => $data['values'][$i]['A']
          'date_request' => $date_request,
          'input_user' => $getUserID,
          'input_datetime' => $date_request,
          'da_no' => $data['values'][$i]['A'],
          'aju_no' => $data['values'][$i]['B'],
          'item_ca' => $data['values'][$i]['C'],
          'ref_no' => $data['values'][$i]['D'],
          // 'remark_dn' => $data['values'][$i]['E'],
          'remark_dn' => $remark_dn_new,
          'cost_name' => $data['values'][$i]['F'],
          'estimate_value' => $data['values'][$i]['G'],
          'pic_transfer' => $data['values'][$i]['H'],
          'bank_name' => $data['values'][$i]['I'],
          'rek_no' => $data['values'][$i]['J']
        );

        // print_r($data_excel);
        $this->db->insert('t_ca_trans', $data_excel);
      }

      if($this->db->affected_rows() > 0){
          echo 'the file has been insert to database.';
       } else{
          echo 'the file failed to insert to database!';
       }

       redirect('ca_core/cahistory');
      
      // $costname = $data['values']['2']['F'];
      // if ($costname == "atk"){
      //   echo 'berhasil masuk kondisi';
      // } else {
      //   echo 'tidak berhasil masuk kondisi';
      // }

    }
    // ============================================================================================================


    function saveSettlement($getId,$amount=null,$isRedirect=null) {
        //additional by Alfan
        $getIsSuj = $_POST['hideIsSuj'];
        if ($getIsSuj == "") {
            $getIsSuj = 0;
        }
        $getRequesterSn = "";

        $helpers = new helpers();
        $ajaxManager = new ajax_manager();

        $res = $this->db->query('select requester_sn,request_type from t_ca_trans where ca_guid = "'.$getId.'" limit 1;');
        if($res->num_rows() > 0)
        {
          foreach($res->result() as $row)
          {
            $hidRequestType = $row->request_type;
            $getRequesterSn = $row->requester_sn;
          }
        } else {
          $hidRequestType = null;
        }

        //aditional by Alfan
        $getDirectToIdByUserReq = $ajaxManager->getDirectReportToId($getRequesterSn);

        $getUserID = $this->session->userdata('userName');
        $getUserStat = $this->session->userdata('userStat');
        $getUserDiv = $this->session->userdata('userDiv');
        $getUserDept = $this->session->userdata('userDept');
        $getUserSessionLevelDetail = $this->session->userdata('sessionLevelDetail');
    
        //here delete last trans settlement
        $strSqlDelete = "DELETE FROM tr_trans_approve_status WHERE ca_guid = '".$getId."' AND approve_type = 'SET'";
        $this->db->query($strSqlDelete);    
        //===================================

        if($amount==null)
          $getSettleAmount = str_replace(".", "", $_POST['txtSettleamount']);
        else
          $getSettleAmount = $amount;

        if($hidRequestType == null)
          $getReqType = $_POST['hidRequestType'];
        else
          $getReqType = $hidRequestType;
    
        //$getAppFlowSettle = substr($_POST['hidApvFlow'], 0, (strlen($_POST['hidApvFlow']) - 1));
        //$getUserSess = $this->session->userdata('userName');
    
        $getLastApprovalMsgDetail  = $ajaxManager->initLastApproval($getReqType,$getSettleAmount,$getIsSuj, "CONTROLLER");
        $getLastApprovalMsgDetail2  = $ajaxManager->initUserApproval($getUserSessionLevelDetail,$getLastApprovalMsgDetail['hidApprove'], $getIsSuj, "CONTROLLER");
        
        $getAppFlowSettle = substr($getLastApprovalMsgDetail2['hidApvFlow'], 0,-1);
        $getUserSess = $this->session->userdata('userName');

        // echo $getAppFlowSettle;exit;

        // $t_ca_user_access = $this->GetRequesterAccess($getRequesterSn);
        // $getRowUserAccess = $t_ca_user_access->rowCount;

        // echo $getRowUserAccess;exit;

        $appStep = explode(",", $getAppFlowSettle);
        $data = array(
            'settled_amount' => $getSettleAmount,
            'appv_flow_settle' => ($getIsSuj == 1) ? $getDirectToIdByUserReq :  $getAppFlowSettle,
            'appv_flow_settle_status' => ($getIsSuj == 1) ? $getDirectToIdByUserReq : $appStep[0],
            'appv_complete' => 3,
            'input_user_settle' => $getUserSess,
            'input_datetime_settle' => date("Y-m-d H:i:s")
        );


        $this->db->where('ca_guid', $getId);
        $execData = $this->db->update('t_ca_trans', $data);

        //here for add approval step
        if ($execData) {
            //$appStep = explode(",", $getAppFlowSettle);

            foreach ($appStep as $appv) {
              if ($appv != "") {
                // echo 'masuk kondisi';exit;
                  $tblName = "tr_trans_approve_status";
                  $data = array(
                      'ca_guid' => $getId,
                      'approver' => $appv,
                      'approve_type' => 'SET',
                      'inputUser' => $getUserSess,
                      'inputDateTime' => date("Y-m-d H:i:s")
                  );
                  // print_r($data);die();
                  $execData = $this->db->insert($tblName, $data);
                }
            }

        }

        // if ($execData) {
            // $this->sendEmailToNextAppv($appStep[0], $getId, "SET");
        // }
        //==========================
        // echo $getId.' -> '.$amount.'<br/>';
    
        if($isRedirect==null)
          redirect('ca_core/cahistory');
    }


    // function GetRequesterAccess($requester_userid) {
    //     $requester_userid = $requester_userid;
    //     $select = array('count(*) as rowCount');
    //     $this->load->database();
    //     $this->db->select($select);
    //     $this->db->from('t_ca_user_access');
    //     $this->db->where('requester_userid = ', $requester_userid);

    //     $query = $this->db->get();

    //     foreach ($query->result() as $row) {
    //         return $row;
    //     }

    //     return null;
    // }


    function delCashadvance($getID) {
        
        $getSessUser = $this->session->userdata('userName');
        $getSessUserFull = $this->session->userdata('userNameFull');
        $getUserJobDetail = $this->session->userdata('sessionLevelDetail');
        
        $data = array('isdelete' => 1, 'isdeleteby' => $getSessUser);
        $this->db->where('id_ca_trans', $getID);
        $execData = $this->db->update('t_ca_trans', $data);
        
//        $this->db->where('id_ca_trans', $getID);
//        $this->db->delete('t_ca_trans');

        redirect('ca_core/cahistory');
    }

    function exportExcelFMode($getType, $getCondition) {
        $getDept = "all";
        $getStation = "all";

        $connDB = $this->_connectEnterprise();

        $setArrayLike = array();
        if ($getCondition != "all") {
            $setSearhArray = explode(" ", $getCondition);
            $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose))";
        } else {
            $setSearhArray = array();
        }

        //here for query
        $this->db->select("a.*, b.desc_trans, MONTH(a.date_request) as bulan , c.bank_driverid,
      c.bank_drivername, c.bank_driverrekno, f.no_manifest,
      YEAR(a.date_request) as tahun,(CASE WHEN d.isSuj = 0 THEN d.origin 
        WHEN d.isSuj = 1 THEN e.origin_trip
        WHEN ISNULL(d.isSuj) THEN d.origin 
       END) AS RealOrigin,
      (CASE WHEN d.isSuj = 0 THEN d.destination
          WHEN d.isSuj = 1 THEN e.fleet_schedule 
          WHEN ISNULL(d.isSuj) THEN d.destination 
       END
      ) AS RealDestination,
      (CASE WHEN d.isSuj = 0 THEN 'NON SUJ' WHEN d.isSuj = 1 THEN 'SUJ' WHEN ISNULL(d.isSuj) THEN '-' END) AS IsSuj, 
      e.suj_id,e.truck_type")
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
        ->join('tt_suj_transaction d', 'a.ca_guid = d.ca_guid', 'left')
        ->join('tr_driver_account c', 'a.ca_guid = c.ca_guid', 'left')  
        ->join('tr_cost_type_rates e', 'd.id_cost_type_rate = e.ratesid', 'left')
        ->join('tr_manifest f', 'a.id_ca_trans = f.id_ca_trans', 'left');
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
        
        $getUserSess = $this->session->userdata('userName');
        $this->db->where(array('a.ca_guid <>' => "", 'a.isdelete' => 0, 'a.requester_userid' => $getUserSess, 'year(a.input_datetime)'=> 2018));

        $this->db->order_by('input_datetime', 'desc');

        $getQuery = $this->db->get();

       // echo $this->db->last_query();exit;

        //===========================
        // if (version_compare(PHP_VERSION, '5') < 0) {
        //     include_once('tbs_class.php'); // TinyButStrong template engine for PHP 4
        // } else {
        //     include_once('tbs_class_php5.php'); // TinyButStrong template engine
        // }
        // include('tbs_plugin_excel.php');
        
        $helper = new helpers();

        
        $getTotalAmount = 0;
        $getTotalSettle = 0;
    
        $tableData = '<table>
                <tr>
                  <td style="background:gray" >TCA ID.</td>
                  <td style="background:gray" >Request Date</td>
                  <td style="background:gray" >Requester</td>
                  <td style="background:gray" >Type</td>
                  <td style="background:gray" >Purpose</td>
                  <td style="background:gray" >Amount</td>
                  <td style="background:gray" >Settle Amount</td>
                  <td style="background:gray" >Manifest No</td>
                  <td style="background:gray" >Origin</td>
                  <td style="background:gray" >Destination</td>
                  <td style="background:gray" >Truck Type</td>
                  <td style="background:gray" >Driver Id</td>
                  <td style="background:gray" >Driver Name</td>
                  <td style="background:gray" >Driver Rek</td>
          <td style="background:gray" >Is Suj</td> 
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

                // penambahan yudhis
                $truckType = "";
                if($rowS->truck_type == NULL || $rowS->truck_type == ""){
                  $truckType = "-";
                } else{
                  $truckType = $this->getTruckType($connDB, $rowS->truck_type);
                }
                // ==============================================================            

                $requestAmount = $helper->currencyFormat($rowS->request_amount);
                // echo $requestAmount;exit;

                $tableData .= '<tr>     
                                <td>'.$rowS->id_ca_trans.'</td>
                                <td>'.$rowS->date_request.'</td>
                                <td>'.$rowS->requester_name.'</td>
                                <td>'.$rowS->desc_trans.'</td>
                                <td>'.$rowS->purpose.'</td>
                                <td>'.$rowS->request_amount.'</td>
                                <td>'.$rowS->settled_amount.'</td>
                                <td>'.$rowS->no_manifest.'</td>
                                <td>'.$rowS->RealOrigin.'</td>
                                <td>'.$rowS->RealDestination.'</td>
                                <td>'.$truckType.'</td>
                                <td>'.$rowS->bank_driverid.'</td>
                                <td>'.$rowS->bank_drivername.'</td>
                                <td>'.$rowS->bank_driverrekno.'</td>
                                <td>'.$rowS->IsSuj. (($rowS->IsSuj == "SUJ") ? " (". $rowS->suj_id .")" : "").'</td>
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
                    <td><b>Total : </b></td>
                    <td>'.$helper->currencyFormat($getTotalAmount) .'</td>
                    <td>'.$helper->currencyFormat($getTotalSettle).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>';   
                  
        $tableData .= '</table>';

            //print_r($datas);exit;
            // $getQuery->free_result();
        // echo $tableData;exit;

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


    function exportExcelFMode_2($getType = "99", $getCondition = "all") {
        $getDept = "all";
        $getStation = "all";

        $connDB = $this->_connectEnterprise();

        $setArrayLike = array();
        if ($getCondition != "all") {
            $setSearhArray = explode(" ", $getCondition);
            $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose))";
        } else {
            $setSearhArray = array();
        }

        //here for query
        $this->db->select("a.*, b.desc_trans, MONTH(a.date_request) as bulan ,
      c.bank_drivername, c.bank_driverrekno, f.no_manifest,
      YEAR(a.date_request) as tahun,(CASE WHEN d.isSuj = 0 THEN d.origin 
        WHEN d.isSuj = 1 THEN e.origin_trip
        WHEN ISNULL(d.isSuj) THEN d.origin 
       END) AS RealOrigin,
      (CASE WHEN d.isSuj = 0 THEN d.destination
          WHEN d.isSuj = 1 THEN e.fleet_schedule 
          WHEN ISNULL(d.isSuj) THEN d.destination 
       END
      ) AS RealDestination,
      (CASE WHEN d.isSuj = 0 THEN 'NON SUJ' WHEN d.isSuj = 1 THEN 'SUJ' WHEN ISNULL(d.isSuj) THEN '-' END) AS IsSuj, 
      e.suj_id,e.truck_type")
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
        ->join('tt_suj_transaction d', 'a.ca_guid = d.ca_guid', 'left')
        ->join('tr_driver_account c', 'a.ca_guid = c.ca_guid', 'inner')
        ->join('tr_cost_type_rates e', 'd.id_cost_type_rate = e.ratesid', 'left')
        ->join('tr_manifest f', 'a.id_ca_trans = f.id_ca_trans', 'left');
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
        
        $getUserSess = $this->session->userdata('userName');
        $this->db->where(array('a.ca_guid <>' => "", 'a.isdelete' => 0, 'a.requester_userid' => $getUserSess));

        $this->db->order_by('input_datetime', 'desc');

        $getQuery = $this->db->get();

//        echo $this->db->last_query();

        //===========================
        // if (version_compare(PHP_VERSION, '5') < 0) {
        //     include_once('tbs_class.php'); // TinyButStrong template engine for PHP 4
        // } else {
        //     include_once('tbs_class_php5.php'); // TinyButStrong template engine
        // }
        // include('tbs_plugin_excel.php');
        
        $helper = new helpers();

        
        $getTotalAmount = 0;
        $getTotalSettle = 0;
    
        $tableData = '<table>
                <tr>
                  <td style="background:gray" >TCA ID.</td>
                  <td style="background:gray" >Request Date</td>
                  <td style="background:gray" >Requester</td>
                  <td style="background:gray" >Type</td>
                  <td style="background:gray" >Purpose</td>
                  <td style="background:gray" >Amount</td>
                  <td style="background:gray" >Settle Amount</td>
                  <td style="background:gray" >Manifest No</td>
                  <td style="background:gray" >Origin</td>
                  <td style="background:gray" >Destination</td>
                  <td style="background:gray" >Truck Type</td>
                  <td style="background:gray" >Driver Name</td>
                  <td style="background:gray" >Driver Rek</td>
          <td style="background:gray" >Is Suj</td> 
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

                // penambahan yudhis
                $truckType = "";
                if($rowS->truck_type == NULL || $rowS->truck_type == ""){
                  $truckType = "-";
                } else{
                  $truckType = $this->getTruckType($connDB, $rowS->truck_type);
                }
                // ==============================================================            

                $requestAmount = $helper->replaceCurrRep_IDR($rowS->request_amount);
                $settleAmount = $helper->replaceCurrRep_IDR($rowS->settled_amount);
                // echo $requestAmount;exit;

                $tableData .= '<tr>     
                                <td>'.$rowS->id_ca_trans.'</td>
                                <td>'.$rowS->date_request.'</td>
                                <td>'.$rowS->requester_name.'</td>
                                <td>'.$rowS->desc_trans.'</td>
                                <td>'.$rowS->purpose.'</td>
                                <td>'.$requestAmount.'</td>
                                <td>'.$settleAmount.'</td>
                                <td>'.$rowS->no_manifest.'</td>
                                <td>'.$rowS->RealOrigin.'</td>
                                <td>'.$rowS->RealDestination.'</td>
                                <td>'.$truckType.'</td>
                                <td>'.$rowS->bank_drivername.'</td>
                                <td>'.$rowS->bank_driverrekno.'</td>
                                <td>'.$rowS->IsSuj. (($rowS->IsSuj == "SUJ") ? " (". $rowS->suj_id .")" : "").'</td>
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
                    <td><b>Total : </b></td>
                    <td>'.$helper->currencyFormat($getTotalAmount) .'</td>
                    <td>'.$helper->currencyFormat($getTotalSettle).'</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>';   
                  
        $tableData .= '</table>';

            //print_r($datas);exit;
            // $getQuery->free_result();
        // echo $tableData;exit;

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


    function fleethistory(){
      if (isset($_POST['cmbType'])) {
            $getType = $_POST['cmbType'];
        } else {
            if (isset($_GET['gettype'])) {
                $getType = $_GET['gettype'];
            } else {
                $getType = "99";
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

        if (isset($_GET['per_page'])) {
            $getPage = $_GET['per_page'];
        } else {
            $getPage = 0;
        }

        if ($getPage == 1) {
            $getPage = 0;
        }
        $start = $getPage;
        $end = 30;

        //pagination
        $getTable = $this->initHistTable($start, $end, $getSearch, $getType, $getDept, $getStat);
        // $config['base_url'] = base_url() . 'index.php/fc_core/fc_history?paging=true&search=' . $getSearch . '&gettype=' . $getType. '&getdept=' . $getDept. '&getstat=' . $getStat; 
        $config['base_url'] = base_url() . 'index.php/ca_core/fleethistory?paging=true&search=' . $getSearch; 
        $config['total_rows'] = $getTable['tblRes_numrow'];
        $config['per_page'] = $end;
        $config['num_links'] = 7;

        $this->pagination->initialize($config);
        $throwData["paging"] = $this->pagination->create_links();
        //==========================================        

        $throwData['header'] = "<img src=\"" . base_url() . "includes/icons/zones.png\" alt=\"\"/> Fleet Module";
        $throwData['cmbType'] = $this->initCmbStatus('cmbType');
        $throwData['cmbAvailDept'] = $this->initAvailDept();
        $throwData['cmbAvailStat'] = $this->initAvailStat();
        $throwData['searchAction'] = base_url() . 'index.php/ca_core/fleethistory?paging=true&search=' . $getSearch . '&gettype=' . $getType . 'per_page=0';
        $throwData['txtSearch'] = $getSearch;
        
        $throwData['getType'] = $getType;
        $throwData['getDept'] = $getDept;
        $throwData['getStat'] = $getStat;

        $throwData['tblDatas'] = $getTable['tblRes'];
        $this->load->view('fl_main', $throwData);
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

    function initHistTable($getStart, $getEnd, $getCondition, $getType, $getDept, $getStat) {

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
       //$getUserID = $this->session->userdata('userName');
        $getUserDiv = $this->session->userdata('userDiv');

        $connDB = $this->_connectEnterprise();

        $thisYear = date('Y');

        $whereArry = array('a.ca_guid <>' => "", 'a.isdelete' => 0, 'a.requester_userid' => $getUserSess, 'year(a.input_datetime)' => 2018);

        //here to set condition
        $setArrayLike = array();
        $setSearhArray = explode(" ", $getCondition);
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(settled_amount),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        $setSearchField = "concat_ws(' ',LOWER(requester_name),concat(LOWER(a.id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        //======================================================================
        //here get rows=========================================================
        //$getRow = 0;
        $this->db->select('a.*, b.desc_trans')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        // if (count($setSearhArray) > 0) {
        //     $setFirst = true;
        //     foreach ($setSearhArray as $serchKey) {
        //         if ($setFirst) {
        //             $this->db->like(array($setSearchField => strtolower($serchKey)));
        //             $setFirst = false;
        //         } else {
        //             $this->db->or_like(array($setSearchField => strtolower($serchKey)));
        //         }
        //     }
        // }

        if($getCondition != ""){
          $this->db->like(array($setSearchField => $getCondition));
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
        
        //======================================================================

        $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(c.bank_drivername),concat(LOWER(a.id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        $this->db->select("a.*, b.desc_trans, MONTH(a.date_request) as bulan , c.bank_driverid,
                          c.bank_drivername, c.bank_driverrekno, f.no_manifest,
                          YEAR(a.date_request) as tahun,(CASE WHEN d.isSuj = 0 THEN d.origin 
                            WHEN d.isSuj = 1 THEN e.origin_trip
                            WHEN ISNULL(d.isSuj) THEN d.origin 
                           END) AS RealOrigin,
                          (CASE WHEN d.isSuj = 0 THEN d.destination
                              WHEN d.isSuj = 1 THEN e.fleet_schedule 
                              WHEN ISNULL(d.isSuj) THEN d.destination 
                           END
                          ) AS RealDestination,
                          (CASE WHEN d.isSuj = 0 THEN 'NON SUJ' WHEN d.isSuj = 1 THEN 'SUJ' WHEN ISNULL(d.isSuj) THEN '-' END) AS IsSuj,
                          (CASE WHEN a.appv_flow_settle LIKE '%END%' THEN 1 ELSE 0 END) AS SettleStatus, 
                          e.suj_id,
                          e.truck_type")
                  ->from('t_ca_trans a')
                          ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                  ->join('tt_suj_transaction d', 'a.ca_guid = d.ca_guid', 'left')
                  ->join('tr_driver_account c', 'a.ca_guid = c.ca_guid', 'left')
                  ->join('tr_cost_type_rates e', 'd.id_cost_type_rate = e.ratesid', 'left')
                  ->join('tr_manifest f', 'a.id_ca_trans = f.id_ca_trans', 'left')
                  ->where($whereArry);
        // if (count($setSearhArray) > 0) {
        //     $setFirst = true;
        //     foreach ($setSearhArray as $serchKey) {
        //         if ($setFirst) {
        //             $this->db->like(array($setSearchField => strtolower($serchKey)));
        //             $setFirst = false;
        //         } else {
        //             $this->db->or_like(array($setSearchField => strtolower($serchKey)));
        //         }
        //     }
        // }

        if($getCondition != ""){
          $this->db->like(array($setSearchField => $getCondition));
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
        $this->db->order_by('input_datetime', 'desc')
                ->limit($getEnd, $getStart);
                   // ->limit(20);

        $getQuery = $this->db->get();
        // echo $this->db->last_query();

        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px" colspan="1">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:120px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:130px">' . 'Requester' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th>' . 'Type' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th>' . 'Purpose' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Amount' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Manifest No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Origin' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Destination' . $tmplTbl['heading_cell_end'];    
        $strTblRes .= '<th style="width:100px">' . 'Truck Type' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Driver Id' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Driver Name' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Driver Rek' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">'.'Is Suj'.$tmplTbl['heading_cell_end'];

        if ($getUserDiv == "PLO"){
            $strTblRes .= '<th style="width:150px">' . 'Booking No' . $tmplTbl['heading_cell_end'];
        }

        $strTblRes .= '<th style="width:50px">' . 'Attach' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:140px">' . 'Status' . $tmplTbl['heading_cell_end'];
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

                // penambahan yudhis
                $truckType = "";
                if($rows->truck_type == NULL || $rows->truck_type == ""){
                  $truckType = "-";
                } else{
                  $truckType = $this->getTruckType($connDB, $rows->truck_type);
                }
                // ==============================================================

                $strTblRes .= $tmplTbl['row_start'];
                
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
                $strTblRes .= '<td style="text-align:center;">' . $rows->no_manifest . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->RealOrigin . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->RealDestination . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $truckType . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->bank_driverid . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->bank_drivername . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->bank_driverrekno . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:right;">'.$rows->IsSuj. (($rows->IsSuj == "SUJ") ? " (". $rows->suj_id .")" : "").$tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                if ($getUserDiv == "PLO"){
                    $strTblRes .= '<td style="text-align:center;">' . $rows->booking_no . $tmplTbl['cell_end'];
                }

                $attachFile = "";
                if ($rows->upload_f1 != "") {
                    $attachFile = '<a href="' . base_url() . '/_UploadedFiles/' . $rows->upload_f1 . '"><img src="' . base_url() . 'includes/icons/attachment16.png" alt=""/></a>';
                }
                $strTblRes .= '<td style="text-align:center;">' . $attachFile . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $getStatus . $tmplTbl['cell_end'];
          
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;
    }

    // penambahan yudhis
    function getTruckType($conn, $truck_type)
    {   
        $truckType = "";

        $query = "SELECT model_desc FROM fms.tr_model WHERE id = ".$truck_type ;

        $res = sqlsrv_query($conn,$query);
        // var_dump($res);

        while ($row = sqlsrv_fetch_array($res)) 
        {
            $truckType = $row['model_desc'];
        }

        return $truckType;
    }

    // penambahan yudhis
    function _connectEnterprise()
    {
        $serverHost ="10.10.3.11";
        $usr="sa";
        $pwd="#sqls3rv3r4dm1n#";
        $db="enterprise";

        $connectionInfo = array("UID" => $usr, "PWD" => $pwd, "Database" => $db);

        $conn = sqlsrv_connect($serverHost, $connectionInfo);

        return $conn;
    }

    // penambahan yudhis
    function reportOutstandingAdvance() {
        $getUserSess = $this->session->userdata('userName');

        if (isset($_POST['txtSearch'])) {
            $getSearch = $_POST['txtSearch'];
        } else {
            if (isset($_GET['search'])) {
                $getSearch = $_GET['search'];
            } else {
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

        //pagination
        $getTable = $this->initReportOutstanding($start, $end, $getSearch);
        $getTable2 = $this->initReportOutstanding_2($start, $end, $getSearch);
        $getTable3 = $this->initReportOutstanding_3($start, $end, $getSearch);

        $config['base_url'] = base_url() . 'index.php/ca_core/reportOutstandingAdvance?paging=true&search=' . $getSearch;
        $config['total_rows'] = $getTable['tblRes_numrow'];
        $config['per_page'] = '15';

        $this->pagination->initialize($config);
        $throwData["paging"] = $this->pagination->create_links();

        //==========================================        
        $config2['base_url'] = base_url() . 'index.php/ca_core/reportOutstandingAdvance?paging=true&search=' . $getSearch;
        $config2['total_rows'] = $getTable2['tblRes_numrow2'];
        $config2['per_page'] = '15';

        $this->pagination->initialize($config2);
        $throwData["paging2"] = $this->pagination->create_links();
        // ========================================================

        //==========================================        
        $config3['base_url'] = base_url() . 'index.php/ca_core/reportOutstandingAdvance?paging=true&search=' . $getSearch;
        $config3['total_rows'] = $getTable3['tblRes_numrow3'];
        $config3['per_page'] = '15';

        $this->pagination->initialize($config3);
        $throwData["paging3"] = $this->pagination->create_links();
        // ========================================================

        // print_r($getTable);exit;
        if ($getTable['tblRes_numrow'] == '0'){
            if ($getUserSess == "isu0217"){
                $throwData['tblDatas'] = $getTable['tblRes'];
            } else{
                $throwData['tblDatas'] = '';
            }
        } else{
            $throwData['tblDatas'] = $getTable['tblRes'];
        }
        if ($getTable2['tblRes_numrow2'] == '0'){
            if ($getUserSess == "tpu0015"){
                $throwData['tblDatas2'] = $getTable2['tblRes2'];
            } else{
                $throwData['tblDatas2'] = '';
            }
        } else{
            $throwData['tblDatas2'] = $getTable2['tblRes2'];
        }
        if ($getTable3['tblRes_numrow3'] == '0'){
            if ($getUserSess == "xupj11zzz"){
                $throwData['tblDatas3'] = $getTable3['tblRes3'];
            } else{
                $throwData['tblDatas3'] = '';
            }
        } else{
            $throwData['tblDatas3'] = $getTable3['tblRes3'];
        }

        $throwData['header'] = "<img src=\"" . base_url() . "includes/icons/zones.png\" alt=\"\"/> Report Aging PLS";
        $throwData['txtSearch'] = $getSearch;
//        $getTable = $this->initHistoryTable(0, 10, "");
//        $throwData['tblDatas'] = $getTable['tblRes'];
        $this->load->view('ca_reportOutstanding', $throwData);
    }

    function initReportOutstanding($getStart, $getEnd, $getCondition) {

        $helpers = new helpers();
        $tmplTbl = array(
            'table_open' => '<table id="table_detail" width="2500" border="0" cellpadding="0" cellspacing="0" style="margin-top:10px">',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );

        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.requester_div' => 'PLO', 'a.isdelete' => 0 );

        //here to set condition
        $setArrayLike = array();
        $setSearhArray = explode(" ", $getCondition);
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        //======================================================================
        
        // print_r($getUserSess);exit;
        if ($getUserSess == "isu0217"){
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJO4%' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";          
        } else{
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJO4%' and a.requester_userid = '" .$getUserSess."' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";
        }

        // $this->db->where($whereCon1);
        $this->db->_protect_identifiers=false;

        $this->db->distinct();
        $this->db->select('a.id_ca_trans, a.requester_name, a.requester_div, a.appv_flow, a.purpose, a.date_request, b.inputDateTime as approval_date, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, a.booking_no, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7 DAY) and CURDATE() ) then a.request_amount end) is null, 0, a.request_amount) as one_to_7_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 14 DAY) and DATE_SUB(CURDATE(), INTERVAL 8 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as eight_to_14_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 21 DAY) and DATE_SUB(CURDATE(), INTERVAL 15 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as fifteen_to_21_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 30 DAY) and DATE_SUB(CURDATE(), INTERVAL 22 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as twentytwo_to_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 60 DAY) and DATE_SUB(CURDATE(), INTERVAL 31 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 90 DAY) and DATE_SUB(CURDATE(), INTERVAL 61 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_60_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 120 DAY) and DATE_SUB(CURDATE(), INTERVAL 91 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_90_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7300 DAY) and DATE_SUB(CURDATE(), INTERVAL 121 DAY) ) then a.request_amount end) is NULL, 0, a.request_amount) as upto_120_days')
                ->from('t_ca_trans a')
                ->join('tr_trans_approve_status b', 'a.ca_guid = b.ca_guid and b.approve_type = "STD"', 'left')                
                ->where($whereCon1);
        $this->db->group_by('a.id_ca_trans');
        // $this->db->order_by('a.id_ca_trans', 'desc');

        if($getCondition != ""){
          $this->db->like(array($setSearchField => $getCondition));
        }
 

        // $this->db->order_by('a.input_datetime', 'desc')
        //         ->limit($getEnd, $getStart);
        $this->db->order_by('a.input_datetime', 'desc');

        $getQuery = $this->db->get();
        $getRow = $getQuery->num_rows();
        // echo $this->db->last_query();exit;
        // echo $getRow;exit;

        $getTotalAmount = 0;
        $getTotalAmount2 = 0;
        $getTotalAmount3 = 0;
        $getTotalAmount4 = 0;
        $getTotalAmount5 = 0;
        $getTotalAmount6 = 0;
        $getTotalAmount7 = 0;
        $getTotalAmount8 = 0;


        if ($getRow == 0) {
            if ($getUserSess == "isu0217"){
                //here for heading
                $strTblRes = $tmplTbl['table_open'];
                $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= '<th style="width:180px">' . 'TCA No' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . 'Booking No' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:200px">' . 'Requester' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . 'Request Date' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . 'Purpose' . $tmplTbl['heading_cell_end'];
                $strTblRes .= '<th style="width:150px">' . 'Approval Date' . $tmplTbl['heading_cell_end'];
                $strTblRes .= $tmplTbl['row_end'];

                $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= '<td style="text-align:center;" colspan="14">' . 'No Record' . $tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            } else{
                // display nothing
                $strTblRes = '';
            }

            
        } else {
        
            //here for heading
            $strTblRes = $tmplTbl['table_open'];
            $strTblRes .= $tmplTbl['row_start'];

            $strTblRes .= '<th style="width:180px">' . 'TCA No' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . 'Booking No' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:200px">' . 'Requester' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . 'Request Date' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . 'Purpose' . $tmplTbl['heading_cell_end'];
            $strTblRes .= '<th style="width:150px">' . 'Approval Date' . $tmplTbl['heading_cell_end'];

            $strTblRes .= $tmplTbl['row_end'];

            // if ($getQuery->num_rows() == 0) {
            //     $strTblRes .= $tmplTbl['row_start'];
            //     $strTblRes .= '<td style="text-align:center;" colspan="14">' . 'No Record' . $tmplTbl['cell_end'];
            //     $strTblRes .= $tmplTbl['row_end'];
            // } else {
                foreach ($getQuery->result() as $rowsSum) {
                    $getTotalAmount += $rowsSum->one_to_7_days != "" ? $rowsSum->one_to_7_days : 0;
                    $getTotalAmount2 += $rowsSum->eight_to_14_days != "" ? $rowsSum->eight_to_14_days : 0;
                    $getTotalAmount3 += $rowsSum->fifteen_to_21_days != "" ? $rowsSum->fifteen_to_21_days : 0;
                    $getTotalAmount4 += $rowsSum->twentytwo_to_30_days != "" ? $rowsSum->twentytwo_to_30_days : 0;
                    $getTotalAmount5 += $rowsSum->upto_30_days != "" ? $rowsSum->upto_30_days : 0;
                    $getTotalAmount6 += $rowsSum->upto_60_days != "" ? $rowsSum->upto_60_days : 0;
                    $getTotalAmount7 += $rowsSum->upto_90_days != "" ? $rowsSum->upto_90_days : 0;
                    $getTotalAmount8 += $rowsSum->upto_120_days != "" ? $rowsSum->upto_120_days : 0;
                }

                // $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= '<tr id="toggler">';
                $strTblRes .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
                $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end']; 


                foreach ($getQuery->result() as $rows) {
                    $btnEscalate = "";
                    $editDeleteButton = "";
                    $btnReminder = "-";
                  
                    $editDeleteButton .= '<input title="Delete" type="image" src="' . base_url() . 'includes/icons/page_white_delete.png" onClick="javascript: if (confirm(\'Delete?\')) {window.location.href=\'' . base_url() . 'index.php/ca_core/delCashadvance/' . $rows->id_ca_trans . ' \';}" />';
                    $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';


                    // for get interval date to validate row color
                    $date1      = $helpers->convertDateToSimple($rows->approval_date) ;
                    $date2      = date('Y-m-d');
                    $datediff = (strtotime($date2) - strtotime($date1));
                    $intervalDay = floor($datediff / (60 * 60 * 24));
                    //===================================================

                    
                    
                    // $strTblRes .= $tmplTbl['row_start'];
                    $strTblRes .= '<tr id="hidden_rows" class="hidden_row" style="display: none">';

                    $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';

                    $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $setTCAno . $tmplTbl['cell_end'];

                    // if ($intervalDay > "1" && $intervalDay <= "7") {
                    //     $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                    //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //     // print_r('warna hijau');
                    //   } elseif ($intervalDay >= "8" && $intervalDay <= "14") {
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         // print_r('warna kuning');
                    //   } elseif ($intervalDay >= "15" && $intervalDay <= "21") {
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         // print_r('warna kuning');
                    //   } elseif ($intervalDay >= "22" && $intervalDay <= "30") {
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         // print_r('warna kuning');
                    //   } elseif ($intervalDay >= "30" && $intervalDay < "60") {
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         // print_r('warna kuning');
                    //   } elseif ($intervalDay >= "60" && $intervalDay < "90") {
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         // print_r('warna kuning');
                    //   } elseif ($intervalDay >= "90" && $intervalDay < "120") {
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         // print_r('warna kuning');
                    //   } elseif ($intervalDay >= "120") {
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                    //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                    //         // print_r('warna kuning');
                    //   } 


                    $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->one_to_7_days) . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->eight_to_14_days) . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->fifteen_to_21_days) . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->twentytwo_to_30_days) . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_30_days) . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_60_days) . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_90_days) . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_120_days) . $tmplTbl['cell_end'];

                    $strTblRes .= '<td style="text-align:center;">' . $rows->booking_no . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $rows->requester_name . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->date_request) . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $rows->purpose . $tmplTbl['cell_end'];
                    $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->approval_date) . $tmplTbl['cell_end'];
                    
                    $strTblRes .= $tmplTbl['row_end'];

                    // $getTotalAmount += $rows->one_to_7_days != "" ? $rows->one_to_7_days : 0;
                    // $getTotalAmount2 += $rows->eight_to_14_days != "" ? $rows->eight_to_14_days : 0;
                    // $getTotalAmount3 += $rows->fifteen_to_21_days != "" ? $rows->fifteen_to_21_days : 0;
                    // $getTotalAmount4 += $rows->twentytwo_to_30_days != "" ? $rows->twentytwo_to_30_days : 0;
                    // $getTotalAmount5 += $rows->upto_30_days != "" ? $rows->upto_30_days : 0;
                    // $getTotalAmount6 += $rows->upto_60_days != "" ? $rows->upto_60_days : 0;
                    // $getTotalAmount7 += $rows->upto_90_days != "" ? $rows->upto_90_days : 0;
                    // $getTotalAmount8 += $rows->upto_120_days != "" ? $rows->upto_120_days : 0;
                }

                // $strTblRes .= $tmplTbl['row_start'];
                // $strTblRes .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
                // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
                // $strTblRes .= $tmplTbl['row_end'];  

        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;
    }

    function initReportOutstanding_2($getStart, $getEnd, $getCondition) {

        $helpers = new helpers();
        $tmplTbl = array(
            'table_open' => '<table id="table_detail" width="2500" border="0" cellpadding="0" cellspacing="0" style="margin-top:10px">',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );

        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.requester_div' => 'PLO', 'a.isdelete' => 0 );

        //here to set condition
        $setArrayLike = array();
        $setSearhArray = explode(" ", $getCondition);
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        //======================================================================
        

        if ($getUserSess == "tpu0015"){
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJO3%' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";          
        } else{
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJO3%' and a.requester_userid = '" .$getUserSess."' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";
        }

        // $this->db->where($whereCon1);
        $this->db->_protect_identifiers=false;

        $this->db->distinct();
        $this->db->select('a.id_ca_trans, a.requester_name, a.requester_div, a.appv_flow, a.purpose, a.date_request, b.inputDateTime as approval_date, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, a.booking_no, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7 DAY) and CURDATE() ) then a.request_amount end) is null, 0, a.request_amount) as one_to_7_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 14 DAY) and DATE_SUB(CURDATE(), INTERVAL 8 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as eight_to_14_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 21 DAY) and DATE_SUB(CURDATE(), INTERVAL 15 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as fifteen_to_21_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 30 DAY) and DATE_SUB(CURDATE(), INTERVAL 22 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as twentytwo_to_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 60 DAY) and DATE_SUB(CURDATE(), INTERVAL 31 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 90 DAY) and DATE_SUB(CURDATE(), INTERVAL 61 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_60_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 120 DAY) and DATE_SUB(CURDATE(), INTERVAL 91 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_90_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7300 DAY) and DATE_SUB(CURDATE(), INTERVAL 121 DAY) ) then a.request_amount end) is NULL, 0, a.request_amount) as upto_120_days')
                ->from('t_ca_trans a')
                ->join('tr_trans_approve_status b', 'a.ca_guid = b.ca_guid and b.approve_type = "STD"', 'left')                
                ->where($whereCon1);
        $this->db->group_by('a.id_ca_trans');
        // $this->db->order_by('a.id_ca_trans', 'desc');

        if($getCondition != ""){
          $this->db->like(array($setSearchField => $getCondition));
        }
 

        // $this->db->order_by('a.input_datetime', 'desc')
        //         ->limit($getEnd, $getStart);
        $this->db->order_by('a.input_datetime', 'desc');

        $getQuery = $this->db->get();
        $getRow2 = $getQuery->num_rows();
        // echo $this->db->last_query();exit;
        // echo $getRow2;exit;

        $getTotalAmount = 0;
        $getTotalAmount2 = 0;
        $getTotalAmount3 = 0;
        $getTotalAmount4 = 0;
        $getTotalAmount5 = 0;
        $getTotalAmount6 = 0;
        $getTotalAmount7 = 0;
        $getTotalAmount8 = 0;


        if ($getRow2 == 0) {
            if ($getUserSess == "tpu0015"){
                //here for heading
                $strTblRes2 = $tmplTbl['table_open'];
                $strTblRes2 .= $tmplTbl['row_start'];
                $strTblRes2 .= '<th style="width:180px">' . 'TCA No' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . 'Booking No' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:200px">' . 'Requester' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . 'Request Date' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . 'Purpose' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= '<th style="width:150px">' . 'Approval Date' . $tmplTbl['heading_cell_end'];
                $strTblRes2 .= $tmplTbl['row_end'];

                $strTblRes2 .= $tmplTbl['row_start'];
                $strTblRes2 .= '<td style="text-align:center;" colspan="14">' . 'No Record' . $tmplTbl['cell_end'];
                $strTblRes2 .= $tmplTbl['row_end'];
            } else{
                // display nothing
                $strTblRes2 = '';
            }

            
        } else {

        
          //here for heading
          $strTblRes2 = $tmplTbl['table_open'];
          $strTblRes2 .= $tmplTbl['row_start'];

          $strTblRes2 .= '<th style="width:180px">' . 'TCA No' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . 'Booking No' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:200px">' . 'Requester' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . 'Request Date' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . 'Purpose' . $tmplTbl['heading_cell_end'];
          $strTblRes2 .= '<th style="width:150px">' . 'Approval Date' . $tmplTbl['heading_cell_end'];

          $strTblRes2 .= $tmplTbl['row_end'];

          // if ($getQuery->num_rows() == 0) {
          //     $strTblRes2 .= $tmplTbl['row_start'];
          //     $strTblRes2 .= '<td style="text-align:center;" colspan="14">' . 'No Record' . $tmplTbl['cell_end'];
          //     $strTblRes2 .= $tmplTbl['row_end'];
          // } else {
          foreach ($getQuery->result() as $rowsSum) {
              $getTotalAmount += $rowsSum->one_to_7_days != "" ? $rowsSum->one_to_7_days : 0;
              $getTotalAmount2 += $rowsSum->eight_to_14_days != "" ? $rowsSum->eight_to_14_days : 0;
              $getTotalAmount3 += $rowsSum->fifteen_to_21_days != "" ? $rowsSum->fifteen_to_21_days : 0;
              $getTotalAmount4 += $rowsSum->twentytwo_to_30_days != "" ? $rowsSum->twentytwo_to_30_days : 0;
              $getTotalAmount5 += $rowsSum->upto_30_days != "" ? $rowsSum->upto_30_days : 0;
              $getTotalAmount6 += $rowsSum->upto_60_days != "" ? $rowsSum->upto_60_days : 0;
              $getTotalAmount7 += $rowsSum->upto_90_days != "" ? $rowsSum->upto_90_days : 0;
              $getTotalAmount8 += $rowsSum->upto_120_days != "" ? $rowsSum->upto_120_days : 0;
          }

              $strTblRes2 .= $tmplTbl['row_start'];
              $strTblRes2 .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              $strTblRes2 .= $tmplTbl['row_end']; 

              foreach ($getQuery->result() as $rows) {
                  $btnEscalate = "";
                  $editDeleteButton = "";
                  $btnReminder = "-";
                
                  $editDeleteButton .= '<input title="Delete" type="image" src="' . base_url() . 'includes/icons/page_white_delete.png" onClick="javascript: if (confirm(\'Delete?\')) {window.location.href=\'' . base_url() . 'index.php/ca_core/delCashadvance/' . $rows->id_ca_trans . ' \';}" />';
                  $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';


                  // for get interval date to validate row color
                  $date1      = $helpers->convertDateToSimple($rows->approval_date) ;
                  $date2      = date('Y-m-d');
                  $datediff = (strtotime($date2) - strtotime($date1));
                  $intervalDay = floor($datediff / (60 * 60 * 24));
                  //===================================================


                  // $getTotalAmount += $rows->one_to_7_days != "" ? $rows->one_to_7_days : 0;
                  // $getTotalAmount2 += $rows->eight_to_14_days != "" ? $rows->eight_to_14_days : 0;
                  // $getTotalAmount3 += $rows->fifteen_to_21_days != "" ? $rows->fifteen_to_21_days : 0;
                  // $getTotalAmount4 += $rows->twentytwo_to_30_days != "" ? $rows->twentytwo_to_30_days : 0;
                  // $getTotalAmount5 += $rows->upto_30_days != "" ? $rows->upto_30_days : 0;
                  // $getTotalAmount6 += $rows->upto_60_days != "" ? $rows->upto_60_days : 0;
                  // $getTotalAmount7 += $rows->upto_90_days != "" ? $rows->upto_90_days : 0;
                  // $getTotalAmount8 += $rows->upto_120_days != "" ? $rows->upto_120_days : 0;

                  
                  
                  // $strTblRes2 .= $tmplTbl['row_start'];
                  $strTblRes2 .= '<tr id="hidden_rows2" class="hidden_row" style="display: none">';

                  $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';

                  $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . $setTCAno . $tmplTbl['cell_end'];

                  // if ($intervalDay > "1" && $intervalDay <= "7") {
                  //     $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                  //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //     // print_r('warna hijau');
                  //   } elseif ($intervalDay >= "8" && $intervalDay <= "14") {
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         // print_r('warna kuning');
                  //   } elseif ($intervalDay >= "15" && $intervalDay <= "21") {
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         // print_r('warna kuning');
                  //   } elseif ($intervalDay >= "22" && $intervalDay <= "30") {
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         // print_r('warna kuning');
                  //   } elseif ($intervalDay >= "30" && $intervalDay < "60") {
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         // print_r('warna kuning');
                  //   } elseif ($intervalDay >= "60" && $intervalDay < "90") {
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         // print_r('warna kuning');
                  //   } elseif ($intervalDay >= "90" && $intervalDay < "120") {
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         // print_r('warna kuning');
                  //   } elseif ($intervalDay >= "120") {
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                  //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                  //         // print_r('warna kuning');
                  //   } 


                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->one_to_7_days) . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->eight_to_14_days) . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->fifteen_to_21_days) . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->twentytwo_to_30_days) . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_30_days) . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_60_days) . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_90_days) . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_120_days) . $tmplTbl['cell_end'];

                  $strTblRes2 .= '<td style="text-align:center;">' . $rows->booking_no . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $rows->requester_name . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->date_request) . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $rows->purpose . $tmplTbl['cell_end'];
                  $strTblRes2 .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->approval_date) . $tmplTbl['cell_end'];
                  
                  $strTblRes2 .= $tmplTbl['row_end'];

              }

              // $strTblRes2 .= $tmplTbl['row_start'];
              // $strTblRes2 .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              // $strTblRes2 .= '<td>' . '' . $tmplTbl['cell_end'];
              // $strTblRes2 .= $tmplTbl['row_end'];  

        }
        $strTblRes2 .= $tmplTbl['table_close'];

        $throwData['tblRes2'] = $strTblRes2;
        $throwData['tblRes_numrow2'] = $getRow2;

        return $throwData;
    }

    // ========================================================

    function initReportOutstanding_3($getStart, $getEnd, $getCondition) {

        $helpers = new helpers();
        $tmplTbl = array(
            'table_open' => '<table width="2500" border="0" cellpadding="0" cellspacing="0" style="margin-top:10px">',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );

        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.requester_div' => 'PLO', 'a.isdelete' => 0 );

        //here to set condition
        $setArrayLike = array();
        $setSearhArray = explode(" ", $getCondition);
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        //======================================================================
        
        if ($getUserSess == "xupj11zzz"){
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJOM%' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";          
        } else{
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJOM%' and a.requester_userid = '" .$getUserSess."' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";
        }

        // $this->db->where($whereCon1);
        $this->db->_protect_identifiers=false;

        $this->db->distinct();
        $this->db->select('a.id_ca_trans, a.requester_name, a.requester_div, a.appv_flow, a.purpose, a.date_request, b.inputDateTime as approval_date, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, a.booking_no, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7 DAY) and CURDATE() ) then a.request_amount end) is null, 0, a.request_amount) as one_to_7_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 14 DAY) and DATE_SUB(CURDATE(), INTERVAL 8 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as eight_to_14_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 21 DAY) and DATE_SUB(CURDATE(), INTERVAL 15 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as fifteen_to_21_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 30 DAY) and DATE_SUB(CURDATE(), INTERVAL 22 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as twentytwo_to_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 60 DAY) and DATE_SUB(CURDATE(), INTERVAL 31 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 90 DAY) and DATE_SUB(CURDATE(), INTERVAL 61 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_60_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 120 DAY) and DATE_SUB(CURDATE(), INTERVAL 91 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_90_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7300 DAY) and DATE_SUB(CURDATE(), INTERVAL 121 DAY) ) then a.request_amount end) is NULL, 0, a.request_amount) as upto_120_days')
                ->from('t_ca_trans a')
                ->join('tr_trans_approve_status b', 'a.ca_guid = b.ca_guid and b.approve_type = "STD"', 'left')                
                ->where($whereCon1);
        $this->db->group_by('a.id_ca_trans');
        // $this->db->order_by('a.id_ca_trans', 'desc');

        if($getCondition != ""){
          $this->db->like(array($setSearchField => $getCondition));
        }
 

        // $this->db->order_by('a.input_datetime', 'desc')
        //         ->limit($getEnd, $getStart);
        $this->db->order_by('a.input_datetime', 'desc');

        $getQuery = $this->db->get();
        $getRow3 = $getQuery->num_rows();
        // echo $this->db->last_query();exit;
        // echo $getRow2;exit;

        $getTotalAmount = 0;
        $getTotalAmount2 = 0;
        $getTotalAmount3 = 0;
        $getTotalAmount4 = 0;
        $getTotalAmount5 = 0;
        $getTotalAmount6 = 0;
        $getTotalAmount7 = 0;
        $getTotalAmount8 = 0;


        if ($getRow3 == 0) {
            if ($getUserSess == "xupj11zzz"){
                //here for heading
                $strTblRes3 = $tmplTbl['table_open'];
                $strTblRes3 .= $tmplTbl['row_start'];
                $strTblRes3 .= '<th style="width:180px">' . 'TCA No' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . 'Booking No' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:200px">' . 'Requester' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . 'Request Date' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . 'Purpose' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= '<th style="width:150px">' . 'Approval Date' . $tmplTbl['heading_cell_end'];
                $strTblRes3 .= $tmplTbl['row_end'];

                $strTblRes3 .= $tmplTbl['row_start'];
                $strTblRes3 .= '<td style="text-align:center;" colspan="14">' . 'No Record' . $tmplTbl['cell_end'];
                $strTblRes3 .= $tmplTbl['row_end'];
            } else{
                // display nothing
                $strTblRes3 = '';
            }

            
        } else {

        
          //here for heading
          $strTblRes3 = $tmplTbl['table_open'];
          $strTblRes3 .= $tmplTbl['row_start'];

          $strTblRes3 .= '<th style="width:180px">' . 'TCA No' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:200px">' . 'Booking No' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:200px">' . 'Requester' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . 'Request Date' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . 'Purpose' . $tmplTbl['heading_cell_end'];
          $strTblRes3 .= '<th style="width:150px">' . 'Approval Date' . $tmplTbl['heading_cell_end'];

          $strTblRes3 .= $tmplTbl['row_end'];

          // if ($getQuery->num_rows() == 0) {
          //     $strTblRes3 .= $tmplTbl['row_start'];
          //     $strTblRes3 .= '<td style="text-align:center;" colspan="13">' . 'No Record' . $tmplTbl['cell_end'];
          //     $strTblRes3 .= $tmplTbl['row_end'];
          // } else {
          foreach ($getQuery->result() as $rowsSum) {
            $getTotalAmount += $rowsSum->one_to_7_days != "" ? $rowsSum->one_to_7_days : 0;
              $getTotalAmount2 += $rowsSum->eight_to_14_days != "" ? $rowsSum->eight_to_14_days : 0;
              $getTotalAmount3 += $rowsSum->fifteen_to_21_days != "" ? $rowsSum->fifteen_to_21_days : 0;
              $getTotalAmount4 += $rowsSum->twentytwo_to_30_days != "" ? $rowsSum->twentytwo_to_30_days : 0;
              $getTotalAmount5 += $rowsSum->upto_30_days != "" ? $rowsSum->upto_30_days : 0;
              $getTotalAmount6 += $rowsSum->upto_60_days != "" ? $rowsSum->upto_60_days : 0;
              $getTotalAmount7 += $rowsSum->upto_90_days != "" ? $rowsSum->upto_90_days : 0;
              $getTotalAmount8 += $rowsSum->upto_120_days != "" ? $rowsSum->upto_120_days : 0;
          }

            $strTblRes3 .= $tmplTbl['row_start'];
            $strTblRes3 .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            $strTblRes3 .= $tmplTbl['row_end']; 


            foreach ($getQuery->result() as $rows) {
                $btnEscalate = "";
                $editDeleteButton = "";
                $btnReminder = "-";
              
                $editDeleteButton .= '<input title="Delete" type="image" src="' . base_url() . 'includes/icons/page_white_delete.png" onClick="javascript: if (confirm(\'Delete?\')) {window.location.href=\'' . base_url() . 'index.php/ca_core/delCashadvance/' . $rows->id_ca_trans . ' \';}" />';
                $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';


                // for get interval date to validate row color
                $date1      = $helpers->convertDateToSimple($rows->approval_date) ;
                $date2      = date('Y-m-d');
                $datediff = (strtotime($date2) - strtotime($date1));
                $intervalDay = floor($datediff / (60 * 60 * 24));
                //===================================================

                
                
                // $strTblRes3 .= $tmplTbl['row_start'];
                $strTblRes3 .= '<tr id="hidden_rows3" class="hidden_row" style="display: none">';

                $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';

                $strTblRes3 .= '<td style="text-align:center;">' . '&nbsp;' . $setTCAno . $tmplTbl['cell_end'];

                // if ($intervalDay > "1" && $intervalDay <= "7") {
                //     $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //     $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //     // print_r('warna hijau');
                //   } elseif ($intervalDay >= "8" && $intervalDay <= "14") {
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         // print_r('warna kuning');
                //   } elseif ($intervalDay >= "15" && $intervalDay <= "21") {
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         // print_r('warna kuning');
                //   } elseif ($intervalDay >= "22" && $intervalDay <= "30") {
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         // print_r('warna kuning');
                //   } elseif ($intervalDay >= "30" && $intervalDay < "60") {
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         // print_r('warna kuning');
                //   } elseif ($intervalDay >= "60" && $intervalDay < "90") {
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         // print_r('warna kuning');
                //   } elseif ($intervalDay >= "90" && $intervalDay < "120") {
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         // print_r('warna kuning');
                //   } elseif ($intervalDay >= "120") {
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . '&nbsp;' . "0" . $tmplTbl['cell_end'];
                //         $strTblRes2 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->total_amount) . $tmplTbl['cell_end'];                        
                //         // print_r('warna kuning');
                //   } 


                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->one_to_7_days) . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->eight_to_14_days) . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->fifteen_to_21_days) . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->twentytwo_to_30_days) . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_30_days) . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_60_days) . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_90_days) . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_120_days) . $tmplTbl['cell_end'];

                $strTblRes3 .= '<td style="text-align:center;">' . $rows->booking_no . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $rows->requester_name . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->date_request) . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $rows->purpose . $tmplTbl['cell_end'];
                $strTblRes3 .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->approval_date) . $tmplTbl['cell_end'];
                
                $strTblRes3 .= $tmplTbl['row_end'];

                // $getTotalAmount += $rows->one_to_7_days != "" ? $rows->one_to_7_days : 0;
                // $getTotalAmount2 += $rows->eight_to_14_days != "" ? $rows->eight_to_14_days : 0;
                // $getTotalAmount3 += $rows->fifteen_to_21_days != "" ? $rows->fifteen_to_21_days : 0;
                // $getTotalAmount4 += $rows->twentytwo_to_30_days != "" ? $rows->twentytwo_to_30_days : 0;
                // $getTotalAmount5 += $rows->upto_30_days != "" ? $rows->upto_30_days : 0;
                // $getTotalAmount6 += $rows->upto_60_days != "" ? $rows->upto_60_days : 0;
                // $getTotalAmount7 += $rows->upto_90_days != "" ? $rows->upto_90_days : 0;
                // $getTotalAmount8 += $rows->upto_120_days != "" ? $rows->upto_120_days : 0;
            }

            // $strTblRes3 .= $tmplTbl['row_start'];
            // $strTblRes3 .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            // $strTblRes3 .= '<td>' . '' . $tmplTbl['cell_end'];
            // $strTblRes3 .= $tmplTbl['row_end'];  

        }
        $strTblRes3 .= $tmplTbl['table_close'];

        $throwData['tblRes3'] = $strTblRes3;
        $throwData['tblRes_numrow3'] = $getRow3;

        return $throwData;
    }
    // ======================================================================


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

    function cahistory() {

        if (isset($_POST['txtSearch'])) {
            $getSearch = $_POST['txtSearch'];
        } else {
            if (isset($_GET['search'])) {
                $getSearch = $_GET['search'];
            } else {
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

        //pagination
        $getTable = $this->initHistoryTable($start, $end, $getSearch);
        $config['base_url'] = base_url() . 'index.php/ca_core/cahistory?paging=true&search=' . $getSearch;
        $config['total_rows'] = $getTable['tblRes_numrow'];
        $config['per_page'] = '15';

        $this->pagination->initialize($config);
        $throwData["paging"] = $this->pagination->create_links();
        //==========================================        



        $throwData['tblDatas'] = $getTable['tblRes'];

        $throwData['header'] = "<img src=\"" . base_url() . "includes/icons/zones.png\" alt=\"\"/> Cash Advance History";
        $throwData['txtSearch'] = $getSearch;
//        $getTable = $this->initHistoryTable(0, 10, "");
//        $throwData['tblDatas'] = $getTable['tblRes'];
        $this->load->view('ca_history', $throwData);
    }

    function initHistoryTable($getStart, $getEnd, $getCondition) {

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
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );

        $getUserSess = $this->session->userdata('userName');
        $getUserDiv = $this->session->userdata('userDiv');
        $whereArry = array('a.input_user' => $getUserSess, 'a.isdelete' => 0 );

        //here to set condition
        $setArrayLike = array();
        $setSearhArray = explode(" ", $getCondition);
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        //======================================================================
        //here get rows=========================================================
        //$getRow = 0;
        $this->db->select('a.*, b.desc_trans')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        
    if($getCondition != ""){
      $this->db->like(array($setSearchField => $getCondition));
    }
    /*
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
    */

        $this->db->order_by('input_datetime', 'desc');
        $getRow = $this->db->get()->num_rows();
        //======================================================================

        // $querySelect = ' a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,';
        // $querySelect .= '(CASE '; 
        // $querySelect .= 'WHEN c.isSuj = 0 THEN "NON SUJ" ';
        // $querySelect .= 'WHEN c.isSuj = 1 THEN "SUJ" ';
        // $querySelect .= 'WHEN ISNULL(c.isSuj) THEN "-" ';
        // $querySelect .= 'END) AS IsSuj ';  
        // $querySelect .= ',d.expired as ExpiredDate,';
        // $querySelect .= '(case ';
        // $querySelect .= 'WHEN c.isSuj = 0 THEN "Yes"';
        // $querySelect .= 'WHEN c.isSuj = 1 THEN "No" '; 
        // $querySelect .= 'end ) as IsExpired ';

        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,(CASE WHEN c.isSuj = 0 THEN "NON SUJ" WHEN c.isSuj = 1 THEN "SUJ" WHEN ISNULL(c.isSuj) THEN "-" END) AS IsSuj, d.expired as ExpiredDate,(case WHEN (c.id_cost_type_rate = 0 and c.isSuj = 0) THEN "No" WHEN (c.isSuj = 0 and c.id_cost_type_rate >= 0) THEN "Yes" WHEN c.isSuj = 1 THEN "No" end ) as IsExpired')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->join('tt_suj_transaction c', 'a.ca_guid = c.ca_guid', 'left')
                ->join('tr_cost_type_rates d', 'c.id_cost_type_rate = d.ratesid', 'left')
                ->where($whereArry);

    if($getCondition != ""){
      $this->db->like(array($setSearchField => $getCondition));
    }
    /*
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
    */
        $this->db->order_by('input_datetime', 'desc')
                ->limit($getEnd, $getStart);

        $getQuery = $this->db->get();

        //echo $this->db->last_query();
        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:130px">' . 'Requester' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:250px">' . 'Type' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Amount' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
        //$strTblRes .= $tmplTbl['heading_cell_start'].'Purpose'.$tmplTbl['heading_cell_end']; 

        if ($getUserDiv == "PLO"){
            $strTblRes .= '<th style="width:150px">' . 'Booking No' . $tmplTbl['heading_cell_end'];
        }

        $strTblRes .= '<th style="width:50px">' . 'Attach' . $tmplTbl['heading_cell_end'];

        //tambahan by alfan
        $strTblRes .= '<th style="width:100px" colspan="1">' . 'Is SUJ' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px" colspan="1">' . 'Expired Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px" colspan="1">' . 'Is Expired' . $tmplTbl['heading_cell_end'];

        $strTblRes .= '<th style="width:160px" colspan="1">' . 'Status' . $tmplTbl['heading_cell_end'];
        //$strTblRes .= '<th style="width:10px" colspan="1">' . '&nbsp;Std&nbsp;' . $tmplTbl['heading_cell_end'];
        //$strTblRes .= '<th style="width:10px" colspan="1">' . '&nbsp;Set&nbsp;' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['heading_cell_start'] . ' ' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];

        if ($getQuery->num_rows() == 0) {
            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td style="text-align:center;" colspan="13">' . 'No Record' . $tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];
        } else {
            foreach ($getQuery->result() as $rows) {
                $btnEscalate = "";
                $editDeleteButton = "";
                $btnReminder = "-";
                //            $editDeleteButton .= '<input title="Settlement" type="image" src="'.base_url().'includes/icons/page_white_edit.png" onClick="window.location.href=\''.base_url().'index.php/MM_main/marketingEventEdit/'.$rows->id_ca_trans.' \'" />';
                //            $editDeleteButton .= '&nbsp;&nbsp;&nbsp;';
                $editDeleteButton .= '<input title="Delete" type="image" src="' . base_url() . 'includes/icons/page_white_delete.png" onClick="javascript: if (confirm(\'Delete?\')) {window.location.href=\'' . base_url() . 'index.php/ca_core/delCashadvance/' . $rows->id_ca_trans . ' \';}" />';
                $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';

                if ($rows->appv_complete == "0" || $rows->appv_complete == "3") {
                    if ($rows->appv_complete == "3") {
                        $btnEscalate = '<input title="Escalate" type="image" src="' . base_url() . 'includes/icons/next.png" onClick="escalateSettle(\'' . $rows->id_ca_trans . '\');" />';
                    } else {
                        $btnEscalate = '<input title="Escalate" type="image" src="' . base_url() . 'includes/icons/next.png" onClick="escalate(\'' . $rows->id_ca_trans . '\');" />';
                    }

                    if ($rows->appv_complete == "0") {
                        $getApprovalInfo = $this->getLastApprovalDetail($rows->appv_flow_status);
                    } elseif ($rows->appv_complete == "3") {
                        $getApprovalInfo = $this->getLastApprovalDetail($rows->appv_flow_settle_status);
                    }
                    $btnReminder = '<input title="Send Reminder" type="image" src="' . base_url() . 'includes/icons/mail.png" onClick="sendReminder(\'' . $getApprovalInfo['userEmail'] . '\',\'' . $rows->id_ca_trans . '\');" />';
                }

               
                $getStatus = "<span class=\"redSpan\">TCA - Waiting Approval</span>";
                if ($rows->appv_complete == "1") {
                    $getStatus = "<span class=\"greenSpan\">TCA - Approved </span><span class=\"redSpan\"><br/>Unpaid</span><br/>(<b>Settlement Required</b>)";
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    // if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "2") {
                    $getStatus = "<span class=\"redSpan\">TCA - Rejected</span>";
                    $editDeleteButton = '<input title="Rejected" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                } elseif ($rows->appv_complete == "3") {
                    $getStatus = "<span class=\"redSpan\">Settlement - Waiting Approval</span>";
                    $editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="' . base_url() . 'includes/icons/next.png" />';
                } elseif ($rows->appv_complete == "4") {
                    $getStatus = "<span class=\"redSpan\">Settlement - Rejected</span>";
                    //$editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "5") {
                    $getStatus = "<span class=\"blueSpan\">Settlement - Approved </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "6") {
                    $getStatus = "<span class=\"blueSpan\">Finance Settlement</span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "7") {
                    $getStatus = "<span class=\"greenSpan\">TCA - Approved <span class=\"blueSpan\"><br/>Paid</span> </span><br/>(<b>Settlement Required</b>)";
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    // if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "8") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">TCA Rejected by Finance </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                } elseif ($rows->appv_complete == "9") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">Settlement Rejected by Finance </span>";
                    //$editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    // if ($rows->request_type == "trans11") $editDeleteButton = "";
                }
                
                
                $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= $tmplTbl['cell_start'] . '&nbsp;' . $btnViewStatus . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $btnReminder . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $btnEscalate . $tmplTbl['cell_end'];

                //$setTCAno = '<span style="font-weight:bold;" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                //$strTblRes .= '<td style="text-align:center;background-color:#ffffcc">' . $setTCAno . '<br/><i style="color:gray">(' . $helpers->convertDateToSimple($rows->date_request) . ')</i>' . $tmplTbl['cell_end'];

                $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                $setTcaDate = '<br/><i style="color:gray">TCA @' . $helpers->convertDateToSimple($rows->date_request) . '</i>';
                $setSetDate = $rows->input_datetime_settle != "" ? '<br/><i style="color:green"> Settl. @' . $helpers->convertDateToSimple($rows->input_datetime_settle) . '</i>' : "";
                $strTblRes .= '<td style="text-align:center;background-color:#ffffcc;height:33px">' . $setTCAno . $setTcaDate . $setSetDate . $tmplTbl['cell_end'];
               
                
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $rows->requester_name . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->desc_trans . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:right;">' . $helpers->currencyFormat($rows->request_amount) . ' &nbsp;' . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:right;">' . $helpers->currencyFormat($rows->settled_amount) . ' &nbsp;' . $tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                if ($getUserDiv == "PLO"){
                    $strTblRes .= '<td style="text-align:center;">' . $rows->booking_no . $tmplTbl['cell_end'];
                }

                $attachFile = "";
                if ($rows->upload_f1 != "") {
                    $attachFile = '<a href="' . base_url() . '/_UploadedFiles/' . $rows->upload_f1 . '"><img src="' . base_url() . 'includes/icons/attachment16.png" alt=""/></a>';
                }
                $strTblRes .= '<td style="text-align:center;">' . $attachFile . $tmplTbl['cell_end'];

                //tambahan by ALFAN
                $strTblRes .= '<td style="text-align:center;">' . $rows->IsSuj . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->ExpiredDate . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->IsExpired . $tmplTbl['cell_end'];

                $strTblRes .= '<td style="text-align:center;">' . $getStatus . $tmplTbl['cell_end'];

                //$getLevelApp = $this->getLevelApp($rows->appv_flow, $rows->ca_guid, "STD");
                //$getLevelSetApp = $this->getLevelApp($rows->appv_flow, $rows->ca_guid, "SET");

                //$strTblRes .= '<td style="text-align:center;">' . $getLevelApp . $tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:center;">' . $getLevelSetApp . $tmplTbl['cell_end'];
                //if ($rows->request_type == "trans11") $editDeleteButton = "";
                $strTblRes .= '<td style="width:50px;text-align:center;">' . $editDeleteButton . $tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;
    }


    // penambahan yudhis
    function history() {
        if (isset($_POST['cmbType'])) {
            $getType = $_POST['cmbType'];
        } else {
            if (isset($_GET['gettype'])) {
                $getType = $_GET['gettype'];
            } else {
                $getType = "99";
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

        //pagination
        $getTable = $this->initHistoryCATable($start, $end, $getSearch, $getType);
        $config['base_url'] = base_url() . 'index.php/ca_core/history?paging=true&search=' . $getSearch. '&gettype=' . $getType;
        $config['total_rows'] = $getTable['tblRes_numrow'];
        $config['per_page'] = '15';

        $this->pagination->initialize($config);
        $throwData["paging"] = $this->pagination->create_links();
        //==========================================        

        $throwData['cmbType'] = $this->initCmbCondition('cmbType');

        $throwData['tblDatas'] = $getTable['tblRes'];

        $throwData['header'] = "<img src=\"" . base_url() . "includes/icons/zones.png\" alt=\"\"/> History";
        $throwData['txtSearch'] = $getSearch;
        $throwData['searchAction'] = base_url() . 'index.php/ca_core/history?paging=true&search=' . $getSearch . '&gettype=' . $getType . 'per_page=0';

        $throwData['getType'] = $getType;
        // print_r($getType);
//        $getTable = $this->initHistoryTable(0, 10, "");
//        $throwData['tblDatas'] = $getTable['tblRes'];
        $this->load->view('history_ca', $throwData);
    }
    // =======================================================


    // penambahan yudhis
    function initHistoryCATable($getStart, $getEnd, $getCondition, $getType) {

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
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );

        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.input_user' => $getUserSess, 'a.isdelete' => 0 );


        //here to set condition
        $setArrayLike = array();
        $setSearhArray = explode(" ", $getCondition);
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        //======================================================================
        //here get rows=========================================================
        //$getRow = 0;
        // $this->db->select('a.*, b.desc_trans')
        $this->db->select('a.id_ca_trans, a.appv_complete, a.appv_flow_status, a.appv_flow_settle_status, a.request_type, a.date_request, a.input_datetime, a.input_datetime_settle, a.requester_name, a.request_amount, a.settled_amount, a.upload_f1, b.desc_trans')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        
    if($getCondition != ""){
      $this->db->like(array($setSearchField => $getCondition));
    }
    /*
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
    */

        $this->db->order_by('input_datetime', 'desc');
        $getRow = $this->db->get()->num_rows();
        //======================================================================

        // $querySelect = ' a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,';
        // $querySelect .= '(CASE '; 
        // $querySelect .= 'WHEN c.isSuj = 0 THEN "NON SUJ" ';
        // $querySelect .= 'WHEN c.isSuj = 1 THEN "SUJ" ';
        // $querySelect .= 'WHEN ISNULL(c.isSuj) THEN "-" ';
        // $querySelect .= 'END) AS IsSuj ';  
        // $querySelect .= ',d.expired as ExpiredDate,';
        // $querySelect .= '(case ';
        // $querySelect .= 'WHEN c.isSuj = 0 THEN "Yes"';
        // $querySelect .= 'WHEN c.isSuj = 1 THEN "No" '; 
        // $querySelect .= 'end ) as IsExpired ';

        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
        $this->db->distinct();
        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,(CASE WHEN c.isSuj = 0 THEN "NON SUJ" WHEN c.isSuj = 1 THEN "SUJ" WHEN ISNULL(c.isSuj) THEN "-" END) AS IsSuj, d.expired as ExpiredDate,(case WHEN (c.id_cost_type_rate = 0 and c.isSuj = 0) THEN "No" WHEN (c.isSuj = 0 and c.id_cost_type_rate >= 0) THEN "Yes" WHEN c.isSuj = 1 THEN "No" end ) as IsExpired, e.appr_date as approve_date')
        $this->db->select('a.id_ca_trans, a.appv_complete, a.appv_flow_status, a.appv_flow_settle_status, a.request_type, a.date_request, a.input_datetime, a.input_datetime_settle, a.requester_name, a.request_amount, a.settled_amount, a.upload_f1, b.desc_trans, e.appr_date as approve_date, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,(CASE WHEN c.isSuj = 0 THEN "NON SUJ" WHEN c.isSuj = 1 THEN "SUJ" WHEN ISNULL(c.isSuj) THEN "-" END) AS IsSuj, d.expired as ExpiredDate,(case WHEN (c.id_cost_type_rate = 0 and c.isSuj = 0) THEN "No" WHEN (c.isSuj = 0 and c.id_cost_type_rate >= 0) THEN "Yes" WHEN c.isSuj = 1 THEN "No" end ) as IsExpired')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->join('tt_suj_transaction c', 'a.ca_guid = c.ca_guid', 'left')
                ->join('tr_cost_type_rates d', 'c.id_cost_type_rate = d.ratesid', 'left')
                ->join('tr_trans_approve_status e', 'a.ca_guid = e.ca_guid', 'left')
                ->where($whereArry);
        $this->db->group_by('a.id_ca_trans');
        $this->db->order_by('e.appr_date', 'desc')
                 ->limit('2');


        // print_r($getType);

        //get condition filter combobox 
        if($getType == 1) {
          $whereCon1 = "(a.settled_amount = 0 OR a.settled_amount IS NULL)";
          $this->db->where($whereCon1);
          // $whereArry .= array('a.settled_amount' => 0 );
          // print_r('condition one');
        }
        if($getType == 2) {
          $this->db->where(array('a.settled_amount >' => 0 ));
          // $whereArry .= array('a.settled_amount >' => 0 );
          // print_r('condition two');
        } 
        if($getType == 3) {
          $this->db->where(array('a.appv_complete' => 3 ));
          // $whereArry .= array('a.settled_amount >' => 0 );
          // print_r('condition three');
        } 
        //======================================================

    if($getCondition != ""){
      $this->db->like(array($setSearchField => $getCondition));
    }

    /*
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
    */
        $this->db->order_by('input_datetime', 'desc')
                ->limit($getEnd, $getStart);

        
        $getQuery = $this->db->get();


        //echo $this->db->last_query();
        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:130px">' . 'Requester' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:250px">' . 'Type' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Amount' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
        //$strTblRes .= $tmplTbl['heading_cell_start'].'Purpose'.$tmplTbl['heading_cell_end']; 

        $strTblRes .= '<th style="width:50px">' . 'Attach' . $tmplTbl['heading_cell_end'];

        //tambahan by alfan
        $strTblRes .= '<th style="width:100px" colspan="1">' . 'Is SUJ' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px" colspan="1">' . 'Expired Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px" colspan="1">' . 'Is Expired' . $tmplTbl['heading_cell_end'];

        $strTblRes .= '<th style="width:160px" colspan="1">' . 'Status' . $tmplTbl['heading_cell_end'];

        $strTblRes .= '<th style="width:100px" colspan="1">' . 'Approval Date' . $tmplTbl['heading_cell_end'];
        //$strTblRes .= '<th style="width:10px" colspan="1">' . '&nbsp;Std&nbsp;' . $tmplTbl['heading_cell_end'];
        //$strTblRes .= '<th style="width:10px" colspan="1">' . '&nbsp;Set&nbsp;' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= $tmplTbl['heading_cell_start'] . ' ' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];

        if ($getQuery->num_rows() == 0) {
            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td style="text-align:center;" colspan="14">' . 'No Record' . $tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];
        } else {
            foreach ($getQuery->result() as $rows) {
                $btnEscalate = "";
                $editDeleteButton = "";
                $btnReminder = "-";
                //            $editDeleteButton .= '<input title="Settlement" type="image" src="'.base_url().'includes/icons/page_white_edit.png" onClick="window.location.href=\''.base_url().'index.php/MM_main/marketingEventEdit/'.$rows->id_ca_trans.' \'" />';
                //            $editDeleteButton .= '&nbsp;&nbsp;&nbsp;';
                $editDeleteButton .= '<input title="Delete" type="image" src="' . base_url() . 'includes/icons/page_white_delete.png" onClick="javascript: if (confirm(\'Delete?\')) {window.location.href=\'' . base_url() . 'index.php/ca_core/delCashadvance/' . $rows->id_ca_trans . ' \';}" />';
                $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';

                if ($rows->appv_complete == "0" || $rows->appv_complete == "3") {
                    if ($rows->appv_complete == "3") {
                        $btnEscalate = '<input title="Escalate" type="image" src="' . base_url() . 'includes/icons/next.png" onClick="escalateSettle(\'' . $rows->id_ca_trans . '\');" />';
                    } else {
                        $btnEscalate = '<input title="Escalate" type="image" src="' . base_url() . 'includes/icons/next.png" onClick="escalate(\'' . $rows->id_ca_trans . '\');" />';
                    }

                    if ($rows->appv_complete == "0") {
                        $getApprovalInfo = $this->getLastApprovalDetail($rows->appv_flow_status);
                    } elseif ($rows->appv_complete == "3") {
                        $getApprovalInfo = $this->getLastApprovalDetail($rows->appv_flow_settle_status);
                    }
                    $btnReminder = '<input title="Send Reminder" type="image" src="' . base_url() . 'includes/icons/mail.png" onClick="sendReminder(\'' . $getApprovalInfo['userEmail'] . '\',\'' . $rows->id_ca_trans . '\');" />';
                }

               
                $getStatus = "<span class=\"redSpan\">TCA - Waiting Approval</span>";
                if ($rows->appv_complete == "1") {
                    $getStatus = "<span class=\"greenSpan\">TCA - Approved </span><span class=\"redSpan\"><br/>Unpaid</span><br/>(<b>Settlement Required</b>)";
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "2") {
                    $getStatus = "<span class=\"redSpan\">TCA - Rejected</span>";
                    $editDeleteButton = '<input title="Rejected" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                } elseif ($rows->appv_complete == "3") {
                    $getStatus = "<span class=\"redSpan\">Settlement - Waiting Approval</span>";
                    $editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="' . base_url() . 'includes/icons/next.png" />';
                } elseif ($rows->appv_complete == "4") {
                    $getStatus = "<span class=\"redSpan\">Settlement - Rejected</span>";
                    //$editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "5") {
                    $getStatus = "<span class=\"blueSpan\">Settlement - Approved </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "6") {
                    $getStatus = "<span class=\"blueSpan\">Finance Settlement</span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "7") {
                    $getStatus = "<span class=\"greenSpan\">TCA - Approved <span class=\"blueSpan\"><br/>Paid</span> </span><br/>(<b>Settlement Required</b>)";
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "8") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">TCA Rejected by Finance </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                } elseif ($rows->appv_complete == "9") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">Settlement Rejected by Finance </span>";
                    //$editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                }


                // for get interval date to validate row color
                $date1      = $helpers->convertDateToSimple($rows->approve_date) ;
                $date2      = date('Y-m-d');
                $datediff = (strtotime($date2) - strtotime($date1));
                $intervalDay = floor($datediff / (60 * 60 * 24));
                //===================================================

                // print_r($date1);
                // print_r($intervalDay);exit;

                // for get color per row
                if ($getType == 1){
                  if ($intervalDay > "1" && $intervalDay <= "7") {
                    $strTblRes .= '<tr style="background-color:#75ff68">';
                    // print_r('warna hijau');
                  } elseif ($intervalDay > "7" && $intervalDay <= "14") {
                    $strTblRes .= '<tr style="background-color:#eaff68">';
                    // print_r('warna kuning');
                  } elseif ($intervalDay > "14" && $intervalDay < "7300") {
                    $strTblRes .= '<tr style="background-color:#ff7272">';
                  } 
                } else {
                  $strTblRes .= $tmplTbl['row_start']; 
                }


                // $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= $tmplTbl['cell_start'] . '&nbsp;' . $btnViewStatus . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $btnReminder . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $btnEscalate . $tmplTbl['cell_end'];

                //$setTCAno = '<span style="font-weight:bold;" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                //$strTblRes .= '<td style="text-align:center;background-color:#ffffcc">' . $setTCAno . '<br/><i style="color:gray">(' . $helpers->convertDateToSimple($rows->date_request) . ')</i>' . $tmplTbl['cell_end'];

                $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                $setTcaDate = '<br/><i style="color:gray">TCA @' . $helpers->convertDateToSimple($rows->date_request) . '</i>';
                $setSetDate = $rows->input_datetime_settle != "" ? '<br/><i style="color:green"> Settl. @' . $helpers->convertDateToSimple($rows->input_datetime_settle) . '</i>' : "";
                $strTblRes .= '<td style="text-align:center;background-color:#ffffcc;height:33px">' . $setTCAno . $setTcaDate . $setSetDate . $tmplTbl['cell_end'];
               
                
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $rows->requester_name . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->desc_trans . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:right;">' . $helpers->currencyFormat($rows->request_amount) . ' &nbsp;' . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:right;">' . $helpers->currencyFormat($rows->settled_amount) . ' &nbsp;' . $tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                $attachFile = "";
                if ($rows->upload_f1 != "") {
                    $attachFile = '<a href="' . base_url() . '/_UploadedFiles/' . $rows->upload_f1 . '"><img src="' . base_url() . 'includes/icons/attachment16.png" alt=""/></a>';
                }
                $strTblRes .= '<td style="text-align:center;">' . $attachFile . $tmplTbl['cell_end'];

                //tambahan by ALFAN
                $strTblRes .= '<td style="text-align:center;">' . $rows->IsSuj . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->ExpiredDate . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->IsExpired . $tmplTbl['cell_end'];

                $strTblRes .= '<td style="text-align:center;">' . $getStatus . $tmplTbl['cell_end'];

                $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->approve_date) . $tmplTbl['cell_end'];

                //$getLevelApp = $this->getLevelApp($rows->appv_flow, $rows->ca_guid, "STD");
                //$getLevelSetApp = $this->getLevelApp($rows->appv_flow, $rows->ca_guid, "SET");

                //$strTblRes .= '<td style="text-align:center;">' . $getLevelApp . $tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:center;">' . $getLevelSetApp . $tmplTbl['cell_end'];
                //if ($rows->request_type == "trans11") $editDeleteButton = "";
                // $strTblRes .= '<td style="width:50px;text-align:center;">' . $editDeleteButton . $tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;
    }
    // ===================================================================


    // penambahan yudhis
    function initCmbCondition($setName) {
        $res = '<select style="width:120px;font-size:12px;height:21px" id="' . $setName . '" name="' . $setName . '">    
                    <option value="0">::All condition::</option>
                    <optgroup label="-----------------------------------"></optgroup>
                    <option value="1">Unpaid</option>
                    <option value="2">Paid</option>
                    <option value="3">Waiting Approval</option>
                </select>';

        return $res;
    }
    // ==========================================================

    // penambahan yudhis
    function reportExim() {

        if (isset($_POST['txtSearch'])) {
            $getSearch = $_POST['txtSearch'];
        } else {
            if (isset($_GET['search'])) {
                $getSearch = $_GET['search'];
            } else {
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

        //pagination
        $getTable = $this->initReportEximTable($start, $end, $getSearch);
        $config['base_url'] = base_url() . 'index.php/ca_core/reportExim?paging=true&search=' . $getSearch;
        $config['total_rows'] = $getTable['tblRes_numrow'];
        $config['per_page'] = '15';

        $this->pagination->initialize($config);
        $throwData["paging"] = $this->pagination->create_links();
        //==========================================        



        $throwData['tblDatas'] = $getTable['tblRes'];

        $throwData['header'] = "<img src=\"" . base_url() . "includes/icons/zones.png\" alt=\"\"/> Report Exim";
        $throwData['txtSearch'] = $getSearch;
//        $getTable = $this->initHistoryTable(0, 10, "");
//        $throwData['tblDatas'] = $getTable['tblRes'];
        $this->load->view('ca_reportExim', $throwData);
    }


    function initReportEximTable($getStart, $getEnd, $getCondition) {

        $helpers = new helpers();
        $tmplTbl = array(
            'table_open' => '<table width="2500" border="0" cellpadding="0" cellspacing="0">',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );

        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.input_user' => $getUserSess, 'a.isdelete' => 0, 'a.appv_complete' => 1);

        //here to set condition
        $setArrayLike = array();
        $setSearhArray = explode(" ", $getCondition);
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose))";
        //$setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(request_amount),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";
        $setSearchField = "concat_ws(' ',LOWER(requester_name),LOWER(desc_trans),LOWER(purpose),concat(LOWER(id_ca_trans),'-TCA-',LOWER(MONTH(a.date_request)),LOWER(YEAR(a.date_request))))";

        //======================================================================
        //here get rows=========================================================
        //$getRow = 0;
        $this->db->select('a.*, b.desc_trans')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->where($whereArry);
        
        if($getCondition != ""){
          $this->db->like(array($setSearchField => $getCondition));
        }
        /*
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
        */

        $this->db->order_by('input_datetime', 'desc');
        $getRow = $this->db->get()->num_rows();
        //======================================================================

        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,(CASE WHEN c.isSuj = 0 THEN "NON SUJ" WHEN c.isSuj = 1 THEN "SUJ" WHEN ISNULL(c.isSuj) THEN "-" END) AS IsSuj, d.expired as ExpiredDate,(case WHEN (c.id_cost_type_rate = 0 and c.isSuj = 0) THEN "No" WHEN (c.isSuj = 0 and c.id_cost_type_rate >= 0) THEN "Yes" WHEN c.isSuj = 1 THEN "No" end ) as IsExpired')
        //         ->from('t_ca_trans a')
        //         ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
        //         ->join('tt_suj_transaction c', 'a.ca_guid = c.ca_guid', 'left')
        //         ->join('tr_cost_type_rates d', 'c.id_cost_type_rate = d.ratesid', 'left')
        //         ->where($whereArry);


        $this->db->select('a.id_ca_trans, a.appv_complete, a.appv_flow_status, a.appv_flow_settle_status, a.request_type, a.date_request, a.input_datetime, a.input_datetime_settle, a.requester_name, a.request_amount, a.settled_amount, a.upload_f1, e.da_no, e.aju_no, e.item_ca, e.ref_no, e.remark_dn, e.cost_name, e.estimate_value, e.pic_transfer, e.bank_name, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,(CASE WHEN c.isSuj = 0 THEN "NON SUJ" WHEN c.isSuj = 1 THEN "SUJ" WHEN ISNULL(c.isSuj) THEN "-" END) AS IsSuj, d.expired as ExpiredDate,(case WHEN (c.id_cost_type_rate = 0 and c.isSuj = 0) THEN "No" WHEN (c.isSuj = 0 and c.id_cost_type_rate >= 0) THEN "Yes" WHEN c.isSuj = 1 THEN "No" end ) as IsExpired')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->join('tt_suj_transaction c', 'a.ca_guid = c.ca_guid', 'left')
                ->join('tr_cost_type_rates d', 'c.id_cost_type_rate = d.ratesid', 'left')
                ->join('t_ca_trans_exim e', 'a.ca_guid = e.ca_guid', 'left')
                ->where($whereArry);

        if($getCondition != ""){
          $this->db->like(array($setSearchField => $getCondition));
        }
        /*
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
        */
        $this->db->order_by('input_datetime', 'desc')
                ->limit($getEnd, $getStart);

        $getQuery = $this->db->get();

        //echo $this->db->last_query();
        //here for heading
        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="width:10px">' . '' . $tmplTbl['heading_cell_end'];

        // $strTblRes .= '<th style="width:100px">' . 'TCA ID' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:200px">' . 'Date Created' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:130px">' . 'Date Check/Money/Giro Recieve' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:180px">' . 'TCA No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:250px">' . 'DA No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Charge No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Importir' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Aju' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Item' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Ref B/L No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Remarks DN/Cost' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Cost Name' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Estimate Value (IDR)' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Vendor Name' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Date Settlement Recieve' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Actual Value (IDR)' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Refund/Claim' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Status' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Date Estimate Settle' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'PIC Area' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Remarks' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:100px">' . 'Commodity (Parts/Unit/Engine)' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="width:100px">' . 'Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="width:100px">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
        //$strTblRes .= $tmplTbl['heading_cell_start'].'Purpose'.$tmplTbl['heading_cell_end']; 

        // $strTblRes .= '<th style="width:160px" colspan="1">' . 'Status' . $tmplTbl['heading_cell_end'];
        
        $strTblRes .= $tmplTbl['row_end'];

        $x = 0;

        if ($getQuery->num_rows() == 0) {
            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td style="text-align:center;" colspan="21">' . 'No Record' . $tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];
        } else {
            foreach ($getQuery->result() as $rows) {
                $btnEscalate = "";
                $editDeleteButton = "";
                $btnReminder = "-";
                //            $editDeleteButton .= '<input title="Settlement" type="image" src="'.base_url().'includes/icons/page_white_edit.png" onClick="window.location.href=\''.base_url().'index.php/MM_main/marketingEventEdit/'.$rows->id_ca_trans.' \'" />';
                //            $editDeleteButton .= '&nbsp;&nbsp;&nbsp;';
                $editDeleteButton .= '<input title="Delete" type="image" src="' . base_url() . 'includes/icons/page_white_delete.png" onClick="javascript: if (confirm(\'Delete?\')) {window.location.href=\'' . base_url() . 'index.php/ca_core/delCashadvance/' . $rows->id_ca_trans . ' \';}" />';
                $btnViewStatus = '<input title="View" type="image" src="' . base_url() . 'includes/icons/bubbles.png" onClick="cashAdvanceView(\'' . $rows->id_ca_trans . '\');" />';

                if ($rows->appv_complete == "0" || $rows->appv_complete == "3") {
                    if ($rows->appv_complete == "3") {
                        $btnEscalate = '<input title="Escalate" type="image" src="' . base_url() . 'includes/icons/next.png" onClick="escalateSettle(\'' . $rows->id_ca_trans . '\');" />';
                    } else {
                        $btnEscalate = '<input title="Escalate" type="image" src="' . base_url() . 'includes/icons/next.png" onClick="escalate(\'' . $rows->id_ca_trans . '\');" />';
                    }

                    if ($rows->appv_complete == "0") {
                        $getApprovalInfo = $this->getLastApprovalDetail($rows->appv_flow_status);
                    } elseif ($rows->appv_complete == "3") {
                        $getApprovalInfo = $this->getLastApprovalDetail($rows->appv_flow_settle_status);
                    }
                    $btnReminder = '<input title="Send Reminder" type="image" src="' . base_url() . 'includes/icons/mail.png" onClick="sendReminder(\'' . $getApprovalInfo['userEmail'] . '\',\'' . $rows->id_ca_trans . '\');" />';
                }

               
                $getStatus = "<span class=\"redSpan\">TCA - Waiting Approval</span>";
                if ($rows->appv_complete == "1") {
                    $getStatus = "<span class=\"greenSpan\">TCA - Approved </span><span class=\"redSpan\"><br/>Unpaid</span><br/>(<b>Settlement Required</b>)";
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "2") {
                    $getStatus = "<span class=\"redSpan\">TCA - Rejected</span>";
                    $editDeleteButton = '<input title="Rejected" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                } elseif ($rows->appv_complete == "3") {
                    $getStatus = "<span class=\"redSpan\">Settlement - Waiting Approval</span>";
                    $editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="' . base_url() . 'includes/icons/next.png" />';
                } elseif ($rows->appv_complete == "4") {
                    $getStatus = "<span class=\"redSpan\">Settlement - Rejected</span>";
                    //$editDeleteButton = '<input title="Waiting Settle Approval" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "5") {
                    $getStatus = "<span class=\"blueSpan\">Settlement - Approved </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "6") {
                    $getStatus = "<span class=\"blueSpan\">Finance Settlement</span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/ticks.png" />';
                } elseif ($rows->appv_complete == "7") {
                    $getStatus = "<span class=\"greenSpan\">TCA - Approved <span class=\"blueSpan\"><br/>Paid</span> </span><br/>(<b>Settlement Required</b>)";
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                } elseif ($rows->appv_complete == "8") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">TCA Rejected by Finance </span>";
                    $editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                } elseif ($rows->appv_complete == "9") {
                    $getStatus = "<span class=\"redSpan\" style=\"font-weight:bold;font-style:italic\">Settlement Rejected by Finance </span>";
                    //$editDeleteButton = '<input title="Settled" type="image" src="' . base_url() . 'includes/icons/block.png" />';
                    $editDeleteButton = '<input title="Settlement" type="image" src="' . base_url() . 'includes/icons/page_white_edit.png" onClick="window.location.href=\'' . base_url() . 'index.php/ca_core/settleCash/' . $rows->id_ca_trans . ' \'" />';
                    if ($rows->request_type == "trans11") $editDeleteButton = "";
                }

                if ($rows->settled_amount > 0){
                  $status_tca = "Complete";
                } else {
                  $status_tca = "Not Complete";
                }
                
                
                $class = ($x%2 == 0)? 'whiteBackgroud' : 'grayBackground';

                // $strTblRes .= $tmplTbl['row_start'];
                $strTblRes .= '<tr class="'.$class.'">';

                $x++;
                // $strTblRes .= $tmplTbl['cell_start'] . '&nbsp;' . $btnViewStatus . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $btnReminder . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $btnEscalate . $tmplTbl['cell_end'];

                //$setTCAno = '<span style="font-weight:bold;" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                //$strTblRes .= '<td style="text-align:center;background-color:#ffffcc">' . $setTCAno . '<br/><i style="color:gray">(' . $helpers->convertDateToSimple($rows->date_request) . ')</i>' . $tmplTbl['cell_end'];

                $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';
                $setTcaDate = '<br/><i style="color:gray">TCA @' . $helpers->convertDateToSimple($rows->date_request) . '</i>';
                $setSetDate = $rows->input_datetime_settle != "" ? '<br/><i style="color:green"> Settl. @' . $helpers->convertDateToSimple($rows->input_datetime_settle) . '</i>' : "";
                // $strTblRes .= '<td style="text-align:center;background-color:#ffffcc;height:33px">' . $setTCAno . $setTcaDate . $setSetDate . $tmplTbl['cell_end'];
               

                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $helpers->convertDateToSimple($rows->input_datetime) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $setTCAno . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->da_no . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->aju_no . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->item_ca . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->ref_no . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->remark_dn . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->cost_name . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->estimate_value) . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $status_tca . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . $rows->pic_transfer . ' / <br>' . $rows->bank_name . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];
                $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . "UNKNOWN" . $tmplTbl['cell_end'];


                // $strTblRes .= '<td style="text-align:right;">' . $helpers->currencyFormat($rows->request_amount) . ' &nbsp;' . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="text-align:right;">' . $helpers->currencyFormat($rows->settled_amount) . ' &nbsp;' . $tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:left;">'.'&nbsp;'.$rows->purpose.$tmplTbl['cell_end'];

                $attachFile = "";
                if ($rows->upload_f1 != "") {
                    $attachFile = '<a href="' . base_url() . '/_UploadedFiles/' . $rows->upload_f1 . '"><img src="' . base_url() . 'includes/icons/attachment16.png" alt=""/></a>';
                }
                // $strTblRes .= '<td style="text-align:center;">' . $attachFile . $tmplTbl['cell_end'];

                //tambahan by ALFAN
                // $strTblRes .= '<td style="text-align:center;">' . $rows->IsSuj . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="text-align:center;">' . $rows->ExpiredDate . $tmplTbl['cell_end'];
                // $strTblRes .= '<td style="text-align:center;">' . $rows->IsExpired . $tmplTbl['cell_end'];

                // $strTblRes .= '<td style="text-align:center;">' . $getStatus . $tmplTbl['cell_end'];

                //$getLevelApp = $this->getLevelApp($rows->appv_flow, $rows->ca_guid, "STD");
                //$getLevelSetApp = $this->getLevelApp($rows->appv_flow, $rows->ca_guid, "SET");

                //$strTblRes .= '<td style="text-align:center;">' . $getLevelApp . $tmplTbl['cell_end'];
                //$strTblRes .= '<td style="text-align:center;">' . $getLevelSetApp . $tmplTbl['cell_end'];
                //if ($rows->request_type == "trans11") $editDeleteButton = "";
                // $strTblRes .= '<td style="width:50px;text-align:center;">' . $editDeleteButton . $tmplTbl['cell_end'];
                $strTblRes .= $tmplTbl['row_end'];
            }
        }
        $strTblRes .= $tmplTbl['table_close'];

        $throwData['tblRes'] = $strTblRes;
        $throwData['tblRes_numrow'] = $getRow;

        return $throwData;
    }

    // ========================================================


    function getLevelApp($getComplFlow, $getGuid, $getApvType) {
        $resFlow = "";

        $setWhere = array('a.ca_guid' => $getGuid, 'a.approve_type' => $getApvType);
        $this->db->select('a.*')
                ->from('tr_trans_approve_status a')
                ->where($setWhere);

        $getQuery = $this->db->get();
        $getCount = 0;
        $getCountAll = 0;
        foreach ($getQuery->result() as $rows) {
            if ($rows->appr_status == 1) {
                $getCount++;
            }
            $getCountAll++;
        }
        $countComplFlow = count(explode(",", $getComplFlow));
        if ($getCount > 0) {
//            $resFlow = "APV_$getCount <br/><b> Of <br/>APV_$getCountAll</b>";
//            $resFlow = "APV_$getCount";
            $resFlow = "$getCount/<b style=\"color:darkBlue\">$getCountAll<b>";
        }




//        
//        if($countComplFlow == $getCount) {
//            $resFlow = "APV";
//        } else {
//            $resFlow = "APV$getCount";
//        }
//        if($getCount != 0){
//            $resFlow = "Wait";
//        }else{
//            
//        }


        return $resFlow;
    }

    function getLastApprovalDetail($getApprovalFlow) {
        $helpers = new helpers();

        $getUserSn = "";
        $getUserName = "";
        $getUserEmail = "";

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

    function escalateTicket($getID) {
        //here get approval flow from id
        $getGuid = "";
        $getAppFlow_array = "";
        $getAppStatus_array = "";
        $getAppFlow = "";
        $getAppStatus = "";
        $getEscalateHasTop = "";

        $this->db->select('*')
                ->from('t_ca_trans')
                ->where(array('id_ca_trans' => $getID));
        $getQueryApvFlow = $this->db->get();

        foreach ($getQueryApvFlow->result() as $apvRows) {
            $getGuid = $apvRows->ca_guid;
            $getAppFlow = $apvRows->appv_flow;
            $getAppStatus = $apvRows->appv_flow_status;
            $getAppFlow_array = explode(",", $apvRows->appv_flow);
            $getAppStatus_array = explode(",", $apvRows->appv_flow_status);

            $getEscalateHasTop = $apvRows->has_toplevel_esc;
        }

        if (intval($getEscalateHasTop) != 1) {

            $getLastFlow = $getAppFlow_array[count($getAppFlow_array) - 1];
            $getLastAppStatus = $getAppStatus_array[count($getAppStatus_array) - 1];

            //=================================================
            //here for escalate to
            $getEscalateToId = "";
            $getEscalateToName = "";
            $this->db->select('*')
                    ->from('t_direct_report')
                    ->where(array('job_title_detail' => $getLastAppStatus));
            $getQueryApvNext = $this->db->get();

            foreach ($getQueryApvNext->result() as $apvRows) {
                $getEscalateToName = $apvRows->name;
                $getEscalateToId = $apvRows->direct_report_to_id;
            }
            //=================================================

            $newAppvFlow = str_replace($getLastAppStatus, $getEscalateToId, $getAppFlow);
            $newAppvStatusFlow = str_replace($getLastAppStatus, $getEscalateToId, $getAppStatus);

            $setNewApproval = "";
            $setNewStatusApproval = "";
            if (count($getAppFlow_array) == count($getAppStatus_array)) {
                $setNewApproval = $getAppFlow . ',' . $getEscalateToId;
                $setNewStatusApproval = $getAppStatus . ',' . $getEscalateToId;
                //echo "up one level (close)<br/><br/>";
            } else {
                $setNewApproval = $getAppFlow;
                $setNewStatusApproval = $getAppStatus . ',' . $getEscalateToId;
            }

            /*
              echo $getAppFlow.'<br/>';
              echo $getAppStatus.'<br/>';
              echo $getLastFlow.'<br/>';
              echo $getLastAppStatus.'<br/><br/>';

              echo $setNewApproval.'<br/>';
              echo $setNewStatusApproval.'<br/>';

              exit;
             */

            $tblName = 't_ca_trans';
            $data = array(
                'appv_flow' => $setNewApproval,
                'appv_flow_status' => $setNewStatusApproval
            );
            if (count($getAppFlow_array) == count($getAppStatus_array)) {
                $data2 = array('has_toplevel_esc' => 1);
                $data = array_merge($data, $data2);
            }
            $this->db->where('id_ca_trans', $getID);
            $execData = $this->db->update($tblName, $data);


            if ($execData) {
                $tblName = "tr_trans_approve_status";

                $dataOverride = array('appr_status' => 3);
                $this->db->where(array('approver' => $getLastAppStatus, 'ca_guid' => $getGuid, 'approve_type' => 'STD'));
                $execData = $this->db->update($tblName, $dataOverride);

                if (count($getAppFlow_array) == count($getAppStatus_array)) {
                    $data = array(
                        'ca_guid' => $getGuid,
                        'approver' => $getEscalateToId,
                        'approve_type' => 'STD'
                    );
                    $execData = $this->db->insert($tblName, $data);
                }
            }
        } else {
            //echo "cannot escalate again";
        }
    }

    function escalateTicketSettle($getID) {
        //here get approval flow from id
        $getGuid = "";
        $getAppFlow_array = "";
        $getAppStatus_array = "";
        $getAppFlow = "";
        $getAppStatus = "";
        $getEscalateHasTop = "";

        $this->db->select('*')
                ->from('t_ca_trans')
                ->where(array('id_ca_trans' => $getID));
        $getQueryApvFlow = $this->db->get();

        foreach ($getQueryApvFlow->result() as $apvRows) {
            $getGuid = $apvRows->ca_guid;
            $getAppFlow = $apvRows->appv_flow_settle;
            $getAppStatus = $apvRows->appv_flow_settle_status;
            $getAppFlow_array = explode(",", $apvRows->appv_flow_settle);
            $getAppStatus_array = explode(",", $apvRows->appv_flow_settle_status);

            $getEscalateHasTop = $apvRows->has_toplevel_esc_settle;
        }

        if (intval($getEscalateHasTop) != 1) {

            $getLastFlow = $getAppFlow_array[count($getAppFlow_array) - 1];
            $getLastAppStatus = $getAppStatus_array[count($getAppStatus_array) - 1];

            //=================================================
            //here for escalate to
            $getEscalateToId = "";
            $getEscalateToName = "";
            $this->db->select('*')
                    ->from('t_direct_report')
                    ->where(array('job_title_detail' => $getLastAppStatus));
            $getQueryApvNext = $this->db->get();

            foreach ($getQueryApvNext->result() as $apvRows) {
                $getEscalateToName = $apvRows->name;
                $getEscalateToId = $apvRows->direct_report_to_id;
            }
            //=================================================

            $newAppvFlow = str_replace($getLastAppStatus, $getEscalateToId, $getAppFlow);
            $newAppvStatusFlow = str_replace($getLastAppStatus, $getEscalateToId, $getAppStatus);

            $setNewApproval = "";
            $setNewStatusApproval = "";
            if (count($getAppFlow_array) == count($getAppStatus_array)) {
                $setNewApproval = $getAppFlow . ',' . $getEscalateToId;
                $setNewStatusApproval = $getAppStatus . ',' . $getEscalateToId;
                //echo "up one level (close)<br/><br/>";
            } else {
                $setNewApproval = $getAppFlow;
                $setNewStatusApproval = $getAppStatus . ',' . $getEscalateToId;
            }

            /*
            echo $getAppFlow.'<br/>';
            echo $getAppStatus.'<br/>';
            echo $getLastFlow.'<br/>';
            echo $getLastAppStatus.'<br/><br/>';

            echo $setNewApproval.'<br/>';
            echo $setNewStatusApproval.'<br/>';

            exit;
           */

            $tblName = 't_ca_trans';
            $data = array(
                'appv_flow_settle' => $setNewApproval,
                'appv_flow_settle_status' => $setNewStatusApproval
            );
            if (count($getAppFlow_array) == count($getAppStatus_array)) {
                $data2 = array('has_toplevel_esc_settle' => 1);
                $data = array_merge($data, $data2);
            }
            $this->db->where('id_ca_trans', $getID);
            $execData = $this->db->update($tblName, $data);


            if ($execData) {
                $tblName = "tr_trans_approve_status";

                $dataOverride = array('appr_status' => 3);
                $this->db->where(array('approver' => $getLastAppStatus, 'ca_guid' => $getGuid, 'approve_type' => 'SET'));
                $execData = $this->db->update($tblName, $dataOverride);

                if (count($getAppFlow_array) == count($getAppStatus_array)) {
                    $data = array(
                        'ca_guid' => $getGuid,
                        'approver' => $getEscalateToId,
                        'approve_type' => 'SET'
                    );
                    $execData = $this->db->insert($tblName, $data);
                }
            }
        } else {
            //echo "cannot escalate again";
        }
    }

    function exportExcel() {
        
        $getUserSess = $this->session->userdata('userName');
        
        include_once 'phpExcel/PHPExcel.php';
        include_once 'phpExcel/PHPExcel/IOFactory.php';
        include_once 'phpExcel/PHPExcel/Writer/Excel5.php';
        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="trainReport_'.$getStart.'_'.$getEnd.'.xls"');
        header('Content-Disposition: attachment;filename="CA_reports_'.$getUserSess.'.xls"');
        header('Cache-Control: max-age=0');

        $objPHPExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objPHPExcel = $objReader->load("fin_report.xls");

        //here for query
        
        
        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ;
        $this->db->where(array('a.input_user' => $getUserSess, 'a.isdelete' => 0));
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
  
  function exportExcel2() {
        
        $getUserSess = $this->session->userdata('userName');
        
        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
        //         ->from('t_ca_trans a')
        //         ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left');
        // $this->db->where(array('a.input_user' => $getUserSess, 'a.isdelete' => 0));
        // $this->db->order_by('input_datetime', 'desc');

        $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,(CASE WHEN c.isSuj = 0 THEN "NON SUJ" WHEN c.isSuj = 1 THEN "SUJ" WHEN ISNULL(c.isSuj) THEN "-" END) AS IsSuj, d.expired as ExpiredDate,(case WHEN (c.id_cost_type_rate = 0 and c.isSuj = 0) THEN "NO" WHEN (c.isSuj = 0 and c.id_cost_type_rate >= 0) THEN "Yes" WHEN c.isSuj = 1 THEN "No" end ) as IsExpired')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->join('tt_suj_transaction c', 'a.ca_guid = c.ca_guid', 'left')
                ->join('tr_cost_type_rates d', 'c.id_cost_type_rate = d.ratesid', 'left')
                ->where(array('a.input_user' => $getUserSess, 'a.isdelete' => 0));
        $this->db->order_by('id_ca_trans', 'desc');

        $getQuery = $this->db->get();

//        echo $this->db->last_query();
        //======================================================================

        $baseRow = 5;
        $helper = new helpers();

        $getTotalAmount = 0;
        $getTotalSettle = 0;
        
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
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );
    
    $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'TCA' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Requester' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Settlement Date ' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Type' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'Amount' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'Purpose' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'Status' . $tmplTbl['heading_cell_end'];
        
        //tambahan by alfan
        $strTblRes .= '<th style="background:gray; color:white">' . 'Is SUJ' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Expired Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Is Expired' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];
    
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
            
      $strTblRes .= $tmplTbl['row_start'];
            // $strTblRes .= '<td>' . $setTCAno . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . $rowS->id_ca_trans . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . $rowS->requester_name . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . $helper->convertDateToSimple($rowS->date_request) . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . $helper->convertDateToSimple($rowS->input_datetime_settle) . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . str_replace('&amp;', " & ", $rowS->desc_trans) . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($rowS->request_amount) . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($rowS->settled_amount) . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . $rowS->purpose . $tmplTbl['cell_end'];
      $strTblRes .= '<td>' . $getStatus . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->IsSuj . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->ExpiredDate . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->IsExpired . $tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];           
            
            $getTotalAmount += $rowS->request_amount != "" ? $rowS->request_amount : 0;
            $getTotalSettle += $rowS->settled_amount != "" ? $rowS->settled_amount : 0;
        }
    
    $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
    $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
    $strTblRes .= '<td>' . ''. $tmplTbl['cell_end'];
    $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
    $strTblRes .= '<td style="font-weight:bold">' . 'Total : ' . $tmplTbl['cell_end'];
    $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
    $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalSettle) . $tmplTbl['cell_end'];
    $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
    $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['row_end'];      
    
    $filename = date("Ymd")."_sapfile.xls";
    header("Content-type: application/xls");
    header("Content-Disposition: attachment; filename=$filename");
    
    echo $strTblRes;     
        
    }


    // penambahan yudhis
    function exportExcelExim() {
        
        $getUserSess = $this->session->userdata('userName');
        
        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
        //         ->from('t_ca_trans a')
        //         ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left');
        // $this->db->where(array('a.input_user' => $getUserSess, 'a.isdelete' => 0));
        // $this->db->order_by('input_datetime', 'desc');

        $this->db->select('a.id_ca_trans, a.appv_complete, a.appv_flow_status, a.appv_flow_settle_status, a.request_type, a.date_request, a.input_datetime, a.input_datetime_settle, a.requester_name, a.request_amount, a.settled_amount, a.upload_f1, e.da_no, e.aju_no, e.item_ca, e.ref_no, e.remark_dn, e.cost_name, e.estimate_value, e.pic_transfer, e.bank_name, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun,(CASE WHEN c.isSuj = 0 THEN "NON SUJ" WHEN c.isSuj = 1 THEN "SUJ" WHEN ISNULL(c.isSuj) THEN "-" END) AS IsSuj, d.expired as ExpiredDate,(case WHEN (c.id_cost_type_rate = 0 and c.isSuj = 0) THEN "No" WHEN (c.isSuj = 0 and c.id_cost_type_rate >= 0) THEN "Yes" WHEN c.isSuj = 1 THEN "No" end ) as IsExpired')
                ->from('t_ca_trans a')
                ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left')
                ->join('tt_suj_transaction c', 'a.ca_guid = c.ca_guid', 'left')
                ->join('tr_cost_type_rates d', 'c.id_cost_type_rate = d.ratesid', 'left')
                ->join('t_ca_trans_exim e', 'a.ca_guid = e.ca_guid', 'left')
                ->where(array('a.input_user' => $getUserSess, 'a.isdelete' => 0, 'a.appv_complete' => 1));
        $this->db->order_by('id_ca_trans', 'desc');

        $getQuery = $this->db->get();

//        echo $this->db->last_query();
        //======================================================================

        $baseRow = 5;
        $helper = new helpers();

        $getTotalAmount = 0;
        $getTotalSettle = 0;
        
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
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );
    
        // $strTblRes = $tmplTbl['table_open'];
        // $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'TCA' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Requester' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Date' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Settlement Date ' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Type' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Purpose' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Status' . $tmplTbl['heading_cell_end'];
            
        // //tambahan by alfan
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Is SUJ' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Expired Date' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Is Expired' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= $tmplTbl['row_end'];

        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Date Created' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Date Check/Money/Giro Recieved' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'TCA No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'DA No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Charge No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Importir' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Aju' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Item' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Ref B/L No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Remarks DN/Cost' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Cost Name' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Estimate Value (IDR)' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Vendor Name' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Date Settlement Recieved' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Actual Value (IDR)' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Refund/Claim' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Status' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Date Estimate Settle' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'PIC Area' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Remarks' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'Commodity' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];

    
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


            if ($rowS->settled_amount > 0){
                $status_tca = "Complete";
            } else {
                $status_tca = "Not Complete";
            }

            $setTCAno = '' . $rowS->id_ca_trans . '-TCA-' . $rowS->bulan . $rowS->tahun . '';
            
            // $strTblRes .= $tmplTbl['row_start'];
            // $strTblRes .= '<td>' . $setTCAno . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . $rowS->requester_name . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . $helper->convertDateToSimple($rowS->date_request) . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . $helper->convertDateToSimple($rowS->input_datetime_settle) . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . str_replace('&amp;', " & ", $rowS->desc_trans) . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($rowS->request_amount) . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($rowS->settled_amount) . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . $rowS->purpose . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . $getStatus . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . $rowS->IsSuj . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . $rowS->ExpiredDate . $tmplTbl['cell_end'];
            // $strTblRes .= '<td>' . $rowS->IsExpired . $tmplTbl['cell_end'];
            // $strTblRes .= $tmplTbl['row_end'];


            $strTblRes .= $tmplTbl['row_start'];
            $strTblRes .= '<td>' . $helper->convertDateToSimple($rowS->input_datetime) . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $setTCAno . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->da_no . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->aju_no . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->item_ca . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->ref_no . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->remark_dn . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->cost_name . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'Rp. ' . $helpers->currencyFormat($rowS->estimate_value) . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $status_tca . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . $rowS->pic_transfer . '/ &nbsp;' . $rowS->bank_name . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= '<td>' . 'UNKNOWN' . $tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];           
            
            // $getTotalAmount += $rowS->request_amount != "" ? $rowS->request_amount : 0;
            // $getTotalSettle += $rowS->settled_amount != "" ? $rowS->settled_amount : 0;
        }
    
        // $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . ''. $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td style="font-weight:bold">' . 'Total : ' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalSettle) . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= $tmplTbl['row_end'];      
    
        $filename = date("Ymd")."_report_exim.xls";
        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo $strTblRes;     
        
    }
    // =======================================================================


    // penambahan yudhis
    function exportExcelAging1() {
        
        
        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.requester_div' => 'PLO', 'a.isdelete' => 0 );
        
        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
        //         ->from('t_ca_trans a')
        //         ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left');
        // $this->db->where(array('a.input_user' => $getUserSess, 'a.isdelete' => 0));
        // $this->db->order_by('input_datetime', 'desc');

        // print_r($getUserSess);exit;
        if ($getUserSess == "isu0217"){
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJO4%' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";          
        } else{
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJO4%' and a.requester_userid = '" .$getUserSess."' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";
        }

        $this->db->_protect_identifiers=false;

        $this->db->distinct();
        $this->db->select('a.id_ca_trans, a.requester_name, a.requester_div, a.appv_flow, a.purpose, a.date_request, b.inputDateTime as approval_date, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, a.booking_no, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7 DAY) and CURDATE() ) then a.request_amount end) is null, 0, a.request_amount) as one_to_7_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 14 DAY) and DATE_SUB(CURDATE(), INTERVAL 8 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as eight_to_14_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 21 DAY) and DATE_SUB(CURDATE(), INTERVAL 15 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as fifteen_to_21_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 30 DAY) and DATE_SUB(CURDATE(), INTERVAL 22 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as twentytwo_to_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 60 DAY) and DATE_SUB(CURDATE(), INTERVAL 31 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 90 DAY) and DATE_SUB(CURDATE(), INTERVAL 61 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_60_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 120 DAY) and DATE_SUB(CURDATE(), INTERVAL 91 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_90_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7300 DAY) and DATE_SUB(CURDATE(), INTERVAL 121 DAY) ) then a.request_amount end) is NULL, 0, a.request_amount) as upto_120_days')
                ->from('t_ca_trans a')
                ->join('tr_trans_approve_status b', 'a.ca_guid = b.ca_guid and b.approve_type = "STD"', 'left')                
                ->where($whereCon1);
        $this->db->group_by('a.id_ca_trans');
        $this->db->order_by('a.input_datetime', 'desc');

        $getQuery = $this->db->get();

//        echo $this->db->last_query();
        //======================================================================

        $baseRow = 5;
        $helper = new helpers();

        // $getTotalAmount = 0;
        // $getTotalSettle = 0;

        $getTotalAmount = 0;
        $getTotalAmount2 = 0;
        $getTotalAmount3 = 0;
        $getTotalAmount4 = 0;
        $getTotalAmount5 = 0;
        $getTotalAmount6 = 0;
        $getTotalAmount7 = 0;
        $getTotalAmount8 = 0;
        
        $helpers = new helpers();
        $tmplTbl = array(
            'table_open' => '<table width="100%" border="1" cellpadding="0" cellspacing="0">',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );
    
        // $strTblRes = $tmplTbl['table_open'];
        // $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'TCA' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Requester' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Date' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Settlement Date ' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Type' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Purpose' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Status' . $tmplTbl['heading_cell_end'];
            
        // //tambahan by alfan
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Is SUJ' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Expired Date' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Is Expired' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= $tmplTbl['row_end'];

        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:180px;background:gray">' . 'TCA No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Booking No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:200px;background:gray">' . 'Requester' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Request Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Purpose' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Approval Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];

        foreach ($getQuery->result() as $rowsSum) {
            $getTotalAmount += $rowsSum->one_to_7_days != "" ? $rowsSum->one_to_7_days : 0;
            $getTotalAmount2 += $rowsSum->eight_to_14_days != "" ? $rowsSum->eight_to_14_days : 0;
            $getTotalAmount3 += $rowsSum->fifteen_to_21_days != "" ? $rowsSum->fifteen_to_21_days : 0;
            $getTotalAmount4 += $rowsSum->twentytwo_to_30_days != "" ? $rowsSum->twentytwo_to_30_days : 0;
            $getTotalAmount5 += $rowsSum->upto_30_days != "" ? $rowsSum->upto_30_days : 0;
            $getTotalAmount6 += $rowsSum->upto_60_days != "" ? $rowsSum->upto_60_days : 0;
            $getTotalAmount7 += $rowsSum->upto_90_days != "" ? $rowsSum->upto_90_days : 0;
            $getTotalAmount8 += $rowsSum->upto_120_days != "" ? $rowsSum->upto_120_days : 0;
        }


        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['row_end']; 


        foreach ($getQuery->result() as $rows) {
            $strTblRes .= '<tr id="hidden_rows2" class="hidden_row">';

            $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';

            $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $setTCAno . $tmplTbl['cell_end'];


            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->one_to_7_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->eight_to_14_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->fifteen_to_21_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->twentytwo_to_30_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_30_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_60_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_90_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_120_days) . $tmplTbl['cell_end'];

            $strTblRes .= '<td style="text-align:center;">' . $rows->booking_no . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $rows->requester_name . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->date_request) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $rows->purpose . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->approval_date) . $tmplTbl['cell_end'];
            
            $strTblRes .= $tmplTbl['row_end'];
        }

    
        // $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . ''. $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td style="font-weight:bold">' . 'Total : ' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalSettle) . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= $tmplTbl['row_end'];      

        // echo $strTblRes;exit;
    
        $filename = date("Ymd")."_report_Aging_PLS.xls";
        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo $strTblRes;     
        
    }
    // =======================================================================


    // penambahan yudhis
    function exportExcelAging2() {
        
        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.requester_div' => 'PLO', 'a.isdelete' => 0 );
        
        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
        //         ->from('t_ca_trans a')
        //         ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left');
        // $this->db->where(array('a.input_user' => $getUserSess, 'a.isdelete' => 0));
        // $this->db->order_by('input_datetime', 'desc');

        // print_r($getUserSess);exit;
        if ($getUserSess == "tpu0015"){
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJO3%' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";          
        } else{
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJO3%' and a.requester_userid = '" .$getUserSess."' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";
        }

        $this->db->_protect_identifiers=false;

        $this->db->distinct();
        $this->db->select('a.id_ca_trans, a.requester_name, a.requester_div, a.appv_flow, a.purpose, a.date_request, b.inputDateTime as approval_date, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, a.booking_no, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7 DAY) and CURDATE() ) then a.request_amount end) is null, 0, a.request_amount) as one_to_7_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 14 DAY) and DATE_SUB(CURDATE(), INTERVAL 8 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as eight_to_14_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 21 DAY) and DATE_SUB(CURDATE(), INTERVAL 15 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as fifteen_to_21_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 30 DAY) and DATE_SUB(CURDATE(), INTERVAL 22 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as twentytwo_to_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 60 DAY) and DATE_SUB(CURDATE(), INTERVAL 31 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 90 DAY) and DATE_SUB(CURDATE(), INTERVAL 61 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_60_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 120 DAY) and DATE_SUB(CURDATE(), INTERVAL 91 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_90_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7300 DAY) and DATE_SUB(CURDATE(), INTERVAL 121 DAY) ) then a.request_amount end) is NULL, 0, a.request_amount) as upto_120_days')
                ->from('t_ca_trans a')
                ->join('tr_trans_approve_status b', 'a.ca_guid = b.ca_guid and b.approve_type = "STD"', 'left')                
                ->where($whereCon1);
        $this->db->group_by('a.id_ca_trans');
        $this->db->order_by('a.input_datetime', 'desc');

        $getQuery = $this->db->get();

//        echo $this->db->last_query();
        //======================================================================

        $baseRow = 5;
        $helper = new helpers();

        // $getTotalAmount = 0;
        // $getTotalSettle = 0;

        $getTotalAmount = 0;
        $getTotalAmount2 = 0;
        $getTotalAmount3 = 0;
        $getTotalAmount4 = 0;
        $getTotalAmount5 = 0;
        $getTotalAmount6 = 0;
        $getTotalAmount7 = 0;
        $getTotalAmount8 = 0;
        
        $helpers = new helpers();
        $tmplTbl = array(
            'table_open' => '<table width="100%" border="1" cellpadding="0" cellspacing="0">',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );
    
        // $strTblRes = $tmplTbl['table_open'];
        // $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'TCA' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Requester' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Date' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Settlement Date ' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Type' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Purpose' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Status' . $tmplTbl['heading_cell_end'];
            
        // //tambahan by alfan
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Is SUJ' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Expired Date' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Is Expired' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= $tmplTbl['row_end'];

        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:180px;background:gray">' . 'TCA No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Booking No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:200px;background:gray">' . 'Requester' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Request Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Purpose' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Approval Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];

        foreach ($getQuery->result() as $rowsSum) {
            $getTotalAmount += $rowsSum->one_to_7_days != "" ? $rowsSum->one_to_7_days : 0;
            $getTotalAmount2 += $rowsSum->eight_to_14_days != "" ? $rowsSum->eight_to_14_days : 0;
            $getTotalAmount3 += $rowsSum->fifteen_to_21_days != "" ? $rowsSum->fifteen_to_21_days : 0;
            $getTotalAmount4 += $rowsSum->twentytwo_to_30_days != "" ? $rowsSum->twentytwo_to_30_days : 0;
            $getTotalAmount5 += $rowsSum->upto_30_days != "" ? $rowsSum->upto_30_days : 0;
            $getTotalAmount6 += $rowsSum->upto_60_days != "" ? $rowsSum->upto_60_days : 0;
            $getTotalAmount7 += $rowsSum->upto_90_days != "" ? $rowsSum->upto_90_days : 0;
            $getTotalAmount8 += $rowsSum->upto_120_days != "" ? $rowsSum->upto_120_days : 0;
        }


        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['row_end']; 


        foreach ($getQuery->result() as $rows) {
            $strTblRes .= '<tr id="hidden_rows2" class="hidden_row">';

            $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';

            $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $setTCAno . $tmplTbl['cell_end'];


            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->one_to_7_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->eight_to_14_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->fifteen_to_21_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->twentytwo_to_30_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_30_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_60_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_90_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_120_days) . $tmplTbl['cell_end'];

            $strTblRes .= '<td style="text-align:center;">' . $rows->booking_no . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $rows->requester_name . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->date_request) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $rows->purpose . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->approval_date) . $tmplTbl['cell_end'];
            
            $strTblRes .= $tmplTbl['row_end'];
        }

    
        // $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . ''. $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td style="font-weight:bold">' . 'Total : ' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalSettle) . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= $tmplTbl['row_end'];      

        // echo $strTblRes;exit;
    
        $filename = date("Ymd")."_report_Aging_PLS_2.xls";
        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo $strTblRes;     
        
    }
    // =======================================================================


    // penambahan yudhis
    function exportExcelAging3() {
        
        $getUserSess = $this->session->userdata('userName');
        $whereArry = array('a.requester_div' => 'PLO', 'a.isdelete' => 0 );
        
        // $this->db->select('a.*, b.desc_trans, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun')
        //         ->from('t_ca_trans a')
        //         ->join('t_jenis_transaksi b', 'a.request_type = b.id_trans', 'left');
        // $this->db->where(array('a.input_user' => $getUserSess, 'a.isdelete' => 0));
        // $this->db->order_by('input_datetime', 'desc');

        // print_r($getUserSess);exit;
        if ($getUserSess == "xupj11zzz"){
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJOM%' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";          
        } else{
            $whereCon1 = "a.requester_div = 'PLO' and appv_flow like '%PJOM%' and a.requester_userid = '" .$getUserSess."' and (settled_amount is null or settled_amount = 0) and a.appv_complete = 1";
        }

        $this->db->_protect_identifiers=false;

        $this->db->distinct();
        $this->db->select('a.id_ca_trans, a.requester_name, a.requester_div, a.appv_flow, a.purpose, a.date_request, b.inputDateTime as approval_date, MONTH(a.date_request) as bulan , YEAR(a.date_request) as tahun, a.booking_no, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7 DAY) and CURDATE() ) then a.request_amount end) is null, 0, a.request_amount) as one_to_7_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 14 DAY) and DATE_SUB(CURDATE(), INTERVAL 8 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as eight_to_14_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 21 DAY) and DATE_SUB(CURDATE(), INTERVAL 15 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as fifteen_to_21_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 30 DAY) and DATE_SUB(CURDATE(), INTERVAL 22 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as twentytwo_to_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 60 DAY) and DATE_SUB(CURDATE(), INTERVAL 31 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_30_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 90 DAY) and DATE_SUB(CURDATE(), INTERVAL 61 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_60_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 120 DAY) and DATE_SUB(CURDATE(), INTERVAL 91 DAY) ) then a.request_amount end) is null, 0, a.request_amount) as upto_90_days, IF ((case when ( b.appr_date between DATE_SUB(CURDATE(), INTERVAL 7300 DAY) and DATE_SUB(CURDATE(), INTERVAL 121 DAY) ) then a.request_amount end) is NULL, 0, a.request_amount) as upto_120_days')
                ->from('t_ca_trans a')
                ->join('tr_trans_approve_status b', 'a.ca_guid = b.ca_guid and b.approve_type = "STD"', 'left')                
                ->where($whereCon1);
        $this->db->group_by('a.id_ca_trans');
        $this->db->order_by('a.input_datetime', 'desc');

        $getQuery = $this->db->get();

//        echo $this->db->last_query();
        //======================================================================

        $baseRow = 5;
        $helper = new helpers();

        // $getTotalAmount = 0;
        // $getTotalSettle = 0;

        $getTotalAmount = 0;
        $getTotalAmount2 = 0;
        $getTotalAmount3 = 0;
        $getTotalAmount4 = 0;
        $getTotalAmount5 = 0;
        $getTotalAmount6 = 0;
        $getTotalAmount7 = 0;
        $getTotalAmount8 = 0;
        
        $helpers = new helpers();
        $tmplTbl = array(
            'table_open' => '<table width="100%" border="1" cellpadding="0" cellspacing="0">',
            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );
    
        // $strTblRes = $tmplTbl['table_open'];
        // $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'TCA' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Requester' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Date' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Settlement Date ' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Type' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Settle Amount' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Purpose' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Status' . $tmplTbl['heading_cell_end'];
            
        // //tambahan by alfan
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Is SUJ' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Expired Date' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= '<th style="background:gray; color:white">' . 'Is Expired' . $tmplTbl['heading_cell_end'];
        // $strTblRes .= $tmplTbl['row_end'];

        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="width:180px;background:gray">' . 'TCA No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '1 - 7 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '8 - 14 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '15 - 21 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '22 - 30 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 30 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 60 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 90 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . '> 120 Days' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Booking No' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:200px;background:gray">' . 'Requester' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Request Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Purpose' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="width:150px;background:gray">' . 'Approval Date' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];

        foreach ($getQuery->result() as $rowsSum) {
            $getTotalAmount += $rowsSum->one_to_7_days != "" ? $rowsSum->one_to_7_days : 0;
            $getTotalAmount2 += $rowsSum->eight_to_14_days != "" ? $rowsSum->eight_to_14_days : 0;
            $getTotalAmount3 += $rowsSum->fifteen_to_21_days != "" ? $rowsSum->fifteen_to_21_days : 0;
            $getTotalAmount4 += $rowsSum->twentytwo_to_30_days != "" ? $rowsSum->twentytwo_to_30_days : 0;
            $getTotalAmount5 += $rowsSum->upto_30_days != "" ? $rowsSum->upto_30_days : 0;
            $getTotalAmount6 += $rowsSum->upto_60_days != "" ? $rowsSum->upto_60_days : 0;
            $getTotalAmount7 += $rowsSum->upto_90_days != "" ? $rowsSum->upto_90_days : 0;
            $getTotalAmount8 += $rowsSum->upto_120_days != "" ? $rowsSum->upto_120_days : 0;
        }


        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<td style="font-weight:bold; text-align: right">' . ' Grand Total : ' . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount2) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount3) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount4) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount5) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount6) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount7) . $tmplTbl['cell_end'];
        $strTblRes .= '<td style="font-weight:bold">' . 'Rp. ' . $helpers->currencyFormat($getTotalAmount8) . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['row_end']; 


        foreach ($getQuery->result() as $rows) {
            $strTblRes .= '<tr id="hidden_rows2" class="hidden_row">';

            $setTCAno = '<span style="font-weight:bold" >' . $rows->id_ca_trans . '-TCA-' . $rows->bulan . $rows->tahun . '</span>';

            $strTblRes .= '<td style="text-align:center;">' . '&nbsp;' . $setTCAno . $tmplTbl['cell_end'];


            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->one_to_7_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->eight_to_14_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->fifteen_to_21_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->twentytwo_to_30_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_30_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_60_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_90_days) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->currencyFormat($rows->upto_120_days) . $tmplTbl['cell_end'];

            $strTblRes .= '<td style="text-align:center;">' . $rows->booking_no . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $rows->requester_name . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->date_request) . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $rows->purpose . $tmplTbl['cell_end'];
            $strTblRes .= '<td style="text-align:center;">' . $helpers->convertDateToSimple($rows->approval_date) . $tmplTbl['cell_end'];
            
            $strTblRes .= $tmplTbl['row_end'];
        }

    
        // $strTblRes .= $tmplTbl['row_start'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . ''. $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td style="font-weight:bold">' . 'Total : ' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalAmount) . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . 'Rp. ' . $helper->currencyFormat($getTotalSettle) . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= '<td>' . '' . $tmplTbl['cell_end'];
        // $strTblRes .= $tmplTbl['row_end'];      

        // echo $strTblRes;exit;
    
        $filename = date("Ymd")."_report_Aging_PLS_3.xls";
        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo $strTblRes;     
        
    }
    // =======================================================================


    //Additional by alfan
    function export_master_cost_rate_to_excel(){

        $this->db->select('suj_id, area_id, deployment, fleet_schedule, origin_trip, truck_type, distance, rationbbm, total_cost, expired')->from('tr_cost_type_rates');
        $this->db->order_by('suj_id, area_id', 'asc');

        $getQuery = $this->db->get();

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
            'cell_end' => '&nbsp;</td>',
            'table_close' => '</table>'
        );

        $strTblRes = $tmplTbl['table_open'];
        $strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'SUJ ID' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'AREA ID' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'DEPLOYMENT' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'FLEET_SCHEDULE' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'ORIGIN TRIP' . $tmplTbl['heading_cell_end'];
    $strTblRes .= '<th style="background:gray; color:white">' . 'TRUCK TYPE' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'DISTANCE' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'RATION BBM' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'TOTAL COST ' . $tmplTbl['heading_cell_end'];
        $strTblRes .= '<th style="background:gray; color:white">' . 'EXPIRED' . $tmplTbl['heading_cell_end'];
        $strTblRes .= $tmplTbl['row_end'];

        
        foreach ($getQuery->result() as $rowS) {
            
            $strTblRes .= $tmplTbl['row_start'];            
            $strTblRes .= '<td align="center">' . $rowS->suj_id . $tmplTbl['cell_end'];
            $strTblRes .= '<td align="center">' . $rowS->area_id . $tmplTbl['cell_end'];
            $strTblRes .= '<td align="center">' . $rowS->deployment . $tmplTbl['cell_end'];
            $strTblRes .= '<td align="center">' . $rowS->fleet_schedule . $tmplTbl['cell_end'];
            $strTblRes .= '<td align="center">' . $rowS->origin_trip . $tmplTbl['cell_end'];
      $strTblRes .= '<td align="center">' . $rowS->truck_type . $tmplTbl['cell_end'];
      $strTblRes .= '<td align="center">' . $rowS->distance . $tmplTbl['cell_end'];
      $strTblRes .= '<td align="center">' . $rowS->rationbbm . $tmplTbl['cell_end'];
      $strTblRes .= '<td align="center">' . $rowS->total_cost . $tmplTbl['cell_end'];
      $strTblRes .= '<td align="center">' . $rowS->expired . $tmplTbl['cell_end'];
            $strTblRes .= $tmplTbl['row_end'];         

        }

        //print_r($data_excel);

        $strTblRes .= $tmplTbl['table_close'];


        $filename = date("Ymd")."_sapfile.xls";
        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo $strTblRes;     
    }
  
  function settlement_upload()
  {
    $data['header'] = 'Upload Settlement';
    $this->load->view('settlement_upload',$data);
  }
  
  function settlement_upld_save()
  {
    // set_time_limit(0);
    ini_set('max_execution_time', 3600);
    $type = $_FILES['excelfiles']['type'];
    $size = $_FILES['excelfiles']['size'];
    $name = $_FILES['excelfiles']['name'];
    $tmp  = $_FILES['excelfiles']['tmp_name'];
    if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    {
      move_uploaded_file($tmp, './excelfiles/'.$name);
      $objPHPExcel = PHPExcel_IOFactory::load('./excelfiles/'.$name);
      $result = $objPHPExcel->getActiveSheet()->getCellCollection();
      $tmp='';$i=1;
      foreach ($result as $cell) 
      {
        $getId = '';$amount='';
        if($objPHPExcel->getActiveSheet()->getCell('A'.$i) != '')
        {
          $getId = $this->getGUID($objPHPExcel->getActiveSheet()->getCell('A'.$i));
          $amount = $objPHPExcel->getActiveSheet()->getCell('B'.$i);
          $this->saveSettlement($getId,$amount,'no');
        }
        $i++;
      }
      redirect('ca_core/cahistory');
    }
    else {
      die('file not excel 2007 files');
    }
  }
  function getGUID($tcanum)
  {
    $dev = explode('-',$tcanum);
    if(count($dev) > 0)
      $tcanum = $dev[0];
    else
      $tcanum = $tcanum;

    $qry = $this->db->query('select ca_guid from t_ca_trans where id_ca_trans = "'.$tcanum.'" limit 1;');
    if($qry->num_rows() > 0)
    {
      foreach($qry->result() as $row)
      {
        $tmp = $row->ca_guid;
      }
    } else 
    {
      $tmp = '';
    }
    return $tmp;
  }
}

?>
