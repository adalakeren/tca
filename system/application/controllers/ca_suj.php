<?php

class ca_suj extends Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->database();
	}

	function ca_suj() {
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

	function index(){

		$data["header"] = "SUJ List";
		$data["error"] = "";

		$data["suj_table"] = $this->initSujList();

		$this->load->view('ca_header');
		$this->load->view('ca_bread', $data);
		$this->load->view('sujlist', $data);
		$this->load->view('ca_footer');
		
	}

	function initSujList()
	{        
      $tmplTbl = array (
        'table_open'          => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px">',
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

      $strTblRes = "<div class='tblDatas'>".$tmplTbl['table_open'];

      $connDB = $this->_connectEnterprise();

      $query = $this->db->query("SELECT * FROM tr_cost_type_rates ORDER BY suj_id");

      $strTblRes .= $tmplTbl['heading_row_start'];
      $strTblRes .= $tmplTbl['heading_cell_start'].'SUJ ID'.$tmplTbl['heading_cell_end'];
      $strTblRes .= $tmplTbl['heading_cell_start'].'AREA ID'.$tmplTbl['heading_cell_end'];
      $strTblRes .= $tmplTbl['heading_cell_start'].'Deployment' . $tmplTbl['heading_cell_end'];
      $strTblRes .= $tmplTbl['heading_cell_start'].'Fleet Schedule'.$tmplTbl['heading_cell_end'];
      $strTblRes .= $tmplTbl['heading_cell_start'].'Origin Trip'.$tmplTbl['heading_cell_end'];
      $strTblRes .= $tmplTbl['heading_cell_start'].'Truck Type'.$tmplTbl['heading_cell_end'];
      $strTblRes .= $tmplTbl['heading_cell_start'].'Total Cost'.$tmplTbl['heading_cell_end'];
      $strTblRes .= $tmplTbl['heading_cell_start'].'Expired'.$tmplTbl['heading_cell_end'];       
      $strTblRes .= $tmplTbl['heading_row_end'];

      foreach ($query->result() as $row)
	  {
      $truckType = $this->getTruckType($connDB, $row->truck_type);

	  	$strTblRes .= $tmplTbl['row_start'];
        $strTblRes .= $tmplTbl['cell_start'].$row->suj_id.$tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['cell_start'].$row->area_id.$tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['cell_start'].$row->deployment.$tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['cell_start'].$row->fleet_schedule.$tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['cell_start'].$row->origin_trip.$tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['cell_start'].$truckType.$tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['cell_start'].$row->total_cost.$tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['cell_start'].$row->expired.$tmplTbl['cell_end'];
        $strTblRes .= $tmplTbl['row_end'];
	  }
        
      $strTblRes .= $tmplTbl['table_close']."</div>";

      return $strTblRes;

	}

  function exportSujList()
  {
    $connDB = $this->_connectEnterprise();
    
    $query = $this->db->query("SELECT * FROM tr_cost_type_rates ORDER BY suj_id");
    
    $tableData = '<table>
                <tr><td>SUJ ID</td><td>AREA ID</td><td>Deployment</td><td>Flee Schedule</td><td>Origin Trip</td><td>Truck Type</td><td>Total Cost</td><td>Expired</td></tr>';

    foreach ($query->result() as $row)
    {
      $truckType = $this->getTruckType($connDB, $row->truck_type);

      $tableData .= '<tr>
        <td>'.$row->suj_id.'</td>
        <td>'.$row->area_id.'</td>
        <td>'.$row->deployment.'</td>
        <td>'.$row->fleet_schedule.'</td>
        <td>'.$row->origin_trip.'</td>
        <td>'.$truckType.'</td>
        <td>'.$row->total_cost.'</td>
        <td>'.$row->expired.'</td>
        </tr>';
    }

    $tableData .= '</table>';

    $filename = date("Ymd")."_SUJ-List.xls";
    header("Content-type: application/xls");
    header("Content-Disposition: attachment; filename=$filename");
        
    echo $tableData;
  }

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

  function getTruckType($conn, $truck_type)
  {   
      $truckType = "";

      $query = "SELECT model_desc FROM fms.tr_model WHERE id = ".$truck_type;

      $res = sqlsrv_query($conn,$query);

      while ($row = sqlsrv_fetch_array($res)) 
      {
          $truckType = $row['model_desc'];
      }

      return $truckType;
  }

}

?>