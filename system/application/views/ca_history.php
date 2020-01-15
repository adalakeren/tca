<?session_start()?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title><?include_once 'inc_title.php';?></title>
    <link href="<?=base_url();?>includes/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <script type='text/javascript' src='<?=base_url();?>includes/jquery/js/jquery-1.5.2.min.js'></script>
    <script type='text/javascript' src='<?=base_url();?>includes/jquery/plugins/overlay/overlay.js'></script>
    <script type='text/javascript' src='<?=base_url();?>includes/jquery/css/aristo/jquery-ui-1.8rc3.custom.min.js'></script>
    <style type="text/css">
        @import url("<?=base_url();?>includes/css/globalReset.css");
        @import url("<?=base_url();?>includes/css/theme.css");
        @import url("<?=base_url();?>includes/css/tableSoft.css");      
        @import url("<?=base_url();?>includes/jquery/css/aristo/jquery-ui-1.8rc3.custom.css"); 
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            //$(document.body).append('<div id="modalwindowblack" style="color:white;width:350px;text-align:left;font-size:11px">'+data+'</div>');
            $('.datepick').datepicker({
                dateFormat: 'dd-mm-yy',
                showAnim: 'fadeIn',
                changeMonth: true,
                changeYear: true
            }).css('width', '90px');

            // $("#optTCA").attr("checked","checked");

            $('#btnExportExcel').click(function(){
                var searchArray = $('#txtSearch').val();
                if(searchArray == ""){searchArray = "all";}
                var getStartDate = $('#txtStartDate').val();
                var getEndDate = $('#txtEndDate').val();
                var getOptRadio = $('#txtOptRadio').val();

                window.location="<?=base_url()?>index.php/ca_core/exportExcel2/"+ getOptRadio + "/"+ getStartDate + "/"+ getEndDate + "/" + searchArray ;
            });

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
                <div id="dvLoadingStatus" style="float: left; padding-bottom: 5px; padding-left: 5px"></div>
                <div style="float: left; padding-bottom: 5px; padding-right: 5px;">
                    <!-- <a class="topLink" id="btnExportExcel" href="<?= base_url()?>index.php/ca_core/exportExcel2"><img src="<?= base_url()?>includes/icons/blue-document-excel.png" alt="exportExcel" /> Export</a>&nbsp;&nbsp; -->
                    <a class="topLink" id="btnExportExcel" href="javascript:void(0)"><img src="<?= base_url()?>includes/icons/blue-document-excel.png" alt="exportExcel" /> Export</a>&nbsp;&nbsp;
                </div>
                <div style="float: right; padding-bottom: 5px; padding-right: 5px;">
                    <form action="<?php echo base_url()?>index.php/ca_core/cahistory" method="post">
                        <label>Search by : </label>
                        <?php if($optRadio == 1) : ?>
                            <label><input type="radio" name="optradio" id="optDate" value="1" checked>Date</label>
                        <?php else : ?>
                            <label><input type="radio" name="optradio" id="optDate" value="1">Date</label>
                        <?php endif; ?>

                        <?php if($optRadio == 2) : ?>
                            <label style="margin-right: 55px"><input type="radio" name="optradio" id="optTCA" value="2" checked>Others</label>
                        <?php else : ?>
                            <label style="margin-right: 55px"><input type="radio" name="optradio" id="optTCA" value="2">Others</label>
                        <?php endif; ?>
                        <i>Date :</i>
                        <input type="text" class="datePick" id="txtStartDate" name="txtStartDate" value="<?php echo $startDate; ?>"/>
                        <i>To :</i>
                        <input type="text" class="datePick" id="txtEndDate" name="txtEndDate" style="margin-right: 55px" value="<?php echo $endDate; ?>"/>

                        <i></i> <input type="text" id="txtSearch" name="txtSearch" style="width: 150px" value="<?=$txtSearch?>"/>
                        <input type="text" id="txtOptRadio" name="txtOptRadio" style="width: 150px" value="<?=$optRadio?>"/ hidden>
                        <input type="submit" value="Search" />
                    </form>
                </div> 
                <br style="clear: both"/>
                <div class="dvContent" style="padding-right: 5px">
                    <div class="tblDatas"><?=$tblDatas?></div>
                </div>
                <div class="dvPagin">
                    <?=$paging?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="secFooter"><? include_once 'inc_footer.php';?></td>
        </tr>
    </table>
</body>
</html>