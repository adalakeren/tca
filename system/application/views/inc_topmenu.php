<script type='text/javascript' src='<?=base_url();?>includes/jquery/plugins/superfish/js/hoverIntent.js'></script>
<script type='text/javascript' src='<?=base_url();?>includes/jquery/plugins/superfish/js/superfish.js'></script>
<style type="text/css">
    @import url("<?=base_url();?>includes/jquery/plugins/superfish/css/superfish.css");
/*    .menu{border:none;border:0px;margin:0px;padding:0px;font:Verdana,Helvetica,sans-serif;font-size:10px;font-weight:bold;}.menu ul{background:transparent;height:20px;list-style:none;margin:0;padding:0;}.menu li{float:left;padding:0px;}.menu li a{background:transparent;color:black;display:block;font-weight:bold;font-style:normal;line-height:35px;margin:0px;padding:0px 18px;text-align:center;text-decoration:none;}.menu li a:hover,.menu ul li:hover a{color:chocolate;text-decoration:none}.menu li ul{opacity: .95;background:#0a1722;display:none;height:auto;padding:0px;margin:0px;border:0px;position:absolute;width:225px;z-index:200;}.menu li:hover ul{display:block;}.menu li li{display:block;float:none;margin:0px;padding:0px;width:225px;}.menu li:hover li a{font-style:normal;font-weight:normal;background:none;}.menu li ul a{display:block;height:35px;font-size:11px;font-style:normal;margin:0px;padding:0px 10px 0px 15px;text-align:left;}.menu li ul a:hover,.menu li ul li:hover a{background:#143270;border:0px;color:#ffffff;text-decoration:none;font-weight: bold}.menu p{clear:left;}*/
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $('ul.sf-menu').superfish();
    });
</script>

<div class="menu">
    <ul class="sf-menu">
        <li><a href="<?=base_url();?>index.php/main_core" style="font-style: normal;font-weight: bold">Home</a></li>
        <li><a href="<?=base_url();?>index.php/ca_core/cahistory" style="font-style: normal;font-weight: bold">Cash Advance</a>
            <ul>
                <li><a href="<?=base_url();?>index.php/ca_core/newcash" style="color: white">New Cash Advance </a></li>                
                <!--<li><a href="<?=base_url();?>index.php/ca_core/casettle">Settlement </a></li>-->
                <li><a href="<?=base_url();?>index.php/ca_core/cahistory" style="color: white">Cash Advance History</a></li>
                <!-- <li><a href="<?=base_url();?>index.php/ca_core/history" style="color: white">History</a></li> -->
                <li><a href="<?=base_url();?>index.php/ca_core/fleethistory" style="color: white">Requester Report</a></li>
                <?php
					if($_SESSION['sess_userID']=='frn0015' or $_SESSION['sess_userID']=='itambudi') {
				?>
				<li><a href="<?=base_url();?>index.php/ca_core/settlement_upload" style="color: white">Upload Settlement</a></li>
				<?php } ?>
            </ul>
        </li>
        
        <li><a href="<?=base_url();?>index.php/apv_core/approval" style="font-style: normal;font-weight: bold">Approval</a>
            <ul>
                <li><a href="<?=base_url();?>index.php/apv_core/approval" style="color: white">New Cash Advance Approval </a></li>           
                <li><a href="<?=base_url();?>index.php/apv_core/settleapproval" style="color: white">Settlement Approval </a></li>  
            </ul>
        </li>
		 <li><a href="#" style="font-style: normal;font-weight: bold">Upload</a>
            <ul>

                <li><a href="#" style="color: white">Fleet Opr </a>
                    <ul style="margin-left: 110px">
                        <li><a href="<?=base_url();?>index.php/ca_upload/upload_request" style="color: white">Upload Request </a></li>
                        <li><a href="<?=base_url();?>index.php/ca_upload/upload_settle" style="color: white">Upload Settlement</a></li>
                    </ul>
                </li>

                <!-- <li><a href="<?=base_url();?>index.php/ca_upload/upload_request" style="color: white">Upload Request </a></li> -->           
                <!-- <li><a href="<?=base_url();?>index.php/ca_upload/upload_settle" style="color: white">Upload Settlement</a></li> -->
                <?php  if($_SESSION['sess_deptID'] == 'EXM' ||  $_SESSION['sess_deptID'] == 'FLT' ||  $_SESSION['sess_deptID'] == 'OPS' || $_SESSION['sess_deptID'] == 'BTN'){ ?> 
                <!-- <li><a href="<?=base_url();?>index.php/ca_upload/upload_settle_exim" style="color: white">Upload Settlement Exim</a></li> -->
                <?php } ?>
            </ul>
        </li>
        <?
            $getSession = $_SESSION['sess_adminOption'];
            if($getSession == "1"){
        ?>
        <li><a href="<?=base_url();?>index.php/fc_core" style="font-style: normal;font-weight: bold">Finance Control</a></li>

        <!--<li><a href="<?=base_url();?>index.php/rep_core" style="font-style: normal;font-weight: bold">Reporting</a></li>-->
        <?
            }
        ?>
        <li><a href="<?=base_url();?>index.php/ca_suj" style="font-style: normal;font-weight: bold">SUJ List</a></li> 

        <li><a href="#" style="font-style: normal;font-weight: bold">Report</a>
            <ul>
                <?php  if($_SESSION['sess_deptID'] == 'EXM' ||  $_SESSION['sess_deptID'] == 'FLT' ||  $_SESSION['sess_deptID'] == 'OPS'){ ?>
                <li><a href="<?=base_url();?>index.php/ca_core/reportExim" style="color: white">Report Exim </a></li>
                <?php } ?>
                <li><a href="<?=base_url();?>index.php/ca_core/reportOutstandingAdvance" style="color: white">Report Aging PLS </a></li>
            </ul>
        </li>
    </ul> 
    <p></p>
</div>
<br style="clear: both"/>
<!--<br style="clear: both"/>-->
<hr class="hrStyle"/>

