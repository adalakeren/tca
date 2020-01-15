<?session_start()?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title><?include_once 'inc_title.php';?></title>
    <link href="<?=base_url();?>includes/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <script type='text/javascript' src='<?=base_url();?>includes/jquery/js/jquery-1.5.2.min.js'></script>
    <script type='text/javascript' src='<?=base_url();?>includes/jquery/plugins/overlay/overlay.js'></script>
    <style type="text/css">
        @import url("<?=base_url();?>includes/css/globalReset.css");
        @import url("<?=base_url();?>includes/css/theme.css");
        @import url("<?=base_url();?>includes/css/tableSoft.css");      
        
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            //$(document.body).append('<div id="modalwindowblack" style="color:white;width:350px;text-align:left;font-size:11px">'+data+'</div>');            
        });
        function cashAdvanceView(getID){
            
            $.ajax({
                url: "<?=base_url()?>index.php/ajax_manager/initCAView",
                type: "POST",
                beforeSend : function(){$('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ajax-loader.gif" alt=""/> Loading Status..')},                
                data: ({getids : getID}),
                success: function(data){
                    $('#dvLoadingStatus').html('');
                    $(document.body).append('<div id="modalwindowblack" style="color:black;width:500px;text-align:left;font-size:11px"><div id="dvOverlayData"></div></div>');                                       
                    $('#dvOverlayData').html(data);
                    $('#modalwindowblack').openOverlay({sColor:'#000',iOpacity:90}).click(function(){$.fn.openOverlay('close')});
                }
            });
        }
        
        function sendReminder(getMailTo,getId){
            var getConfirm = confirm('Send reminder to \n ' + getMailTo + ' ?');
            if(getConfirm){
                $.ajax({
                    url: "<?=base_url()?>index.php/ajax_manager/sendEmailRemider",
                    type: "POST",
                    beforeSend : function(){$('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ajax-loader.gif" alt=""/> Sending Reminder Email..')},                
                    data: ({emailto : getMailTo,caid : getId}),
                    success: function(data){
                        $('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ticks.png" alt=""/>Reminder Email Sent...');    
                        return false;
                    }
                });
            }else{
                return false;
            }
        }
        
        function escalate(getId){
            var getConfirm = confirm('Escalate Cash Advance ?');
            if(getConfirm){
                $.ajax({
                    url: "<?=base_url()?>index.php/ca_core/escalateTicket/" + getId,
                    type: "POST",
                    beforeSend : function(){$('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ajax-loader.gif" alt=""/> Escalating Cash Advance.')},                
                    data: ({}),
                    success: function(data){
                        $('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ticks.png" alt=""/>Cash Advance Escalated');    
                        return false;
                    }
                });
                //location.href = "<?=base_url()?>index.php/ca_core/escalateTicket/" + getId;
            }else{
                return false;
            }
        }
        function escalateSettle(getId){
            var getConfirm = confirm('Escalate Settlement Cash Advance ?');
            if(getConfirm){
                $.ajax({
                    url: "<?=base_url()?>index.php/ca_core/escalateTicketSettle/" + getId,
                    type: "POST",
                    beforeSend : function(){$('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ajax-loader.gif" alt=""/> Escalating Cash Advance.')},                
                    data: ({}),
                    success: function(data){
                        $('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ticks.png" alt=""/>Cash Advance Escalated');    
                        return false;
                    }
                });
                //location.href = "<?=base_url()?>index.php/ca_core/escalateTicket/" + getId;
            }else{
                return false;
            }
        }
        
    </script>
</head>
<body>
<?php
	// print_r($this->session);die();
?>
    <table style="width: 100%;height: 100%">
        <tr>
            <td colspan="" class="secHeader">
                <img src="<?=base_url();?>includes/images/ckbLogo.png" alt=""/>
            </td>
            <td colspan="" class="secHeader" style="text-align: right">
                <img src="<?=base_url();?>includes/images/tcaLogo.png" alt=""/>
            </td>
        </tr>
        <tr>
            <td colspan="2"  class="secMenu">

            </td>
        </tr>
        <tr>
            <td class="secContent1">
                <? include_once 'inc_user_detail.php'; ?>
            </td>
            <td class="secContent2">
                <div class="dvMainMenu"><? include_once 'inc_topmenu.php'; ?></div>
                <div class="dvHeaderContent"><?=$header?></div>
                <br style="clear: both"/>
                <div class="dvContent">
					<form action="<?=base_url();?>index.php/ca_core/settlement_upld_save" method="post" enctype="multipart/form-data">
					<table style="width: 100%; border-spacing: 20px; border-collapse: separate">
						<tr>
							<td style="width:180px;">User ID</td>
							<td style="width:1px;">:</td>
							<td><?=$this->session->userdata('userName');?></td>
						</tr>
						<tr>
							<td style="width:180px;">User Name</td>
							<td style="width:1px;">:</td>
							<td><?=$this->session->userdata('userNameFull');?></td>
						</tr>
						<tr>
							<td style="width:180px;">Excel Files</td>
							<td style="width:1px;">:</td>
							<td><input type="file" id="excelfiles" name="excelfiles"></td>
						</tr>
						<tr>
							<td colspan=3><input type="submit" value="Proses"></td>
						</tr>
					</table>
                </div>
            </td>
        </tr>
        <!--<tr>
            <td colspan="2" class="secFooter"><? include_once 'inc_footer.php';?></td>
        </tr>-->
    </table>
</body>
</html>