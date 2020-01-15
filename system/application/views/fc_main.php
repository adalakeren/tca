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
            $('#dvFinSettle').dialog({autoOpen:false,resizable:false,modal:true,title:"Finance Settlement"});
            $('#cmbType').val('<?=$getType?>');
            $('#cmbAvailDept').val('<?=$getDept?>');
            $('#cmbAvailStat').val('<?=$getStat?>');
            $('#btnExportExcel').css('cursor','pointer');
            $('#btnExportExcel').click(function(){
                var type = $('#cmbType option:selected').val();
                var dept = $('#cmbAvailDept option:selected').val();
                var stat = $('#cmbAvailStat option:selected').val();
                
                var searchArray = $('#txtSearch').val();
                if(searchArray == ""){searchArray = "all";}
                if(dept == ""){dept = "all";}
                if(stat == ""){stat = "all";}
                
                window.location="<?=base_url()?>index.php/fc_core/exportExcelFMode/"+ type + "/" + dept +"/" + stat +"/" + searchArray ;   
            });

            $('#checkBoxAll').click(function(){
                if ($(this).is(":checked"))
                    $('.checkApp').attr('checked', true);
                else
                    $('.checkApp').attr('checked', false);
           });

            $('#btn_paid').click(function(){
                if (confirm("Are you sure you want to Paid this?"))
                {
                    var id = [];

                    $(':checkbox:checked').each(function(i){
                        id[i] = $(this).val();
                    });


                    if(id.length === 0 )
                    {
                        alert("Please select at least one checkbox");
                    }
                    else
                    {
                        for(var i=0; i < id.length; i++)
                        {
                            Paid(id[i]);
							$('#dvAction' + id[i]).html("");
							$('#dvStatus' + id[i]).html("Paid");
							//if (i == (id.length-1)) window.document.location.reload();
                        }
                        //window.document.location.reload();
                    }
                }
                else
                {
                    return false;
                }
           });
            
        });

        function Paid(getId){
            var getReason = "";
            $.ajax({
                url: "<?= base_url() ?>index.php/fc_core/paidTca/"+getId+"/1", 
                type: "POST",
                success: function(data){
					$('#dvAction' + getId).html("");
					$('#dvStatus' + getId).html("Paid");
                    return false;
                }
            });               
        }

        function cashAdvanceView(getID){
            
            $.ajax({
                url: "<?=base_url()?>index.php/ajax_manager/initCAView",
                type: "POST",
                beforeSend : function(){$('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ajax-loader.gif" alt=""/> Loading Status..')},                
                data: ({getids : getID}),
                success: function(data){
                    $('#dvLoadingStatus').html('');
                    $(document.body).append('<div id="modalwindowblack" style="color:white;width:500px;text-align:left;font-size:11px"><div id="dvOverlayData"></div></div>');                                       
                    $('#dvOverlayData').html(data);
                    $('#modalwindowblack').openOverlay({sColor:'#000',iOpacity:90}).click(function(){$.fn.openOverlay('close')});
                }
            });
        }
        
        function sendReminder(getMailTo,getCaid){
            var getConfirm = confirm('Send reminder to \n ' + getMailTo + ' ?');
            if(getConfirm){
                $.ajax({
                    url: "<?=base_url()?>index.php/ajax_manager/sendEmailRemiderSettle",
                    type: "POST",
                    beforeSend : function(){$('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ajax-loader.gif" alt=""/> Sending Reminder Email..')},                
                    data: ({emailto : getMailTo,caid : getCaid}),
                    success: function(data){
                        $('#dvLoadingStatus').html('<img src="<?=base_url()?>includes/icons/ticks.gif" alt=""/>Reminder Email Sent...');    
                        return false;
                    }
                });
            }else{
                return false;
            }
        }
        
        function finSettle(getMailTo,getCaid){
            $('#chk1').attr("checked", false);
            $('#chk2').attr("checked", false);
            $('#chk3').attr("checked", false);            
            $('#hidCaId').val(getCaid);
            $('#hidEmail').val(getMailTo);
            $('#dvFinSettle').dialog("open");
        }
        
        function approveSettle(){
            var getCaid = $('#hidCaId').val();
            var getMailTo = $('#hidEmail').val()
            
            if($('#chk1').attr("checked") && $('#chk2').attr("checked") && $('#chk3').attr("checked")){
                if(confirm("Approved")){
                    $.ajax({
                        url: "<?=base_url()?>index.php/ajax_manager/approveSettleFin",
                        type: "POST",
                        data: ({emailto : getMailTo,caid : getCaid}),
                        success: function(data){
                            alert("Settle Complete");
                            $('#dvFinSettle').dialog("close");
                            window.location.reload()
                            return false;
                        }
                    });
                }
            }else{
                alert("Check Was not Complete");
            }

        }
        
        function reject(getId,gettype){
            var getReason = prompt("Reject Reason", "");
            if(getReason){
                $.ajax({
                    url: "<?= base_url() ?>index.php/ajax_manager/approval" , 
                    data: ({setType : gettype, setId : getId, setReason : getReason}),
                    beforeSend:function(){$('#dvReject'+getId).html("<img src=\"<?=base_url()?>/includes/images/ajax-loader.gif\" alt=\"\"/>");},
                    type: "POST",
                    success: function(data){location.reload(true);}
                });
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
                <div style="float: right; padding-bottom: 5px; padding-right: 5px">                   
                    <form action="<?php echo $searchAction?>" method="post">
                        <a class="topLink" id="btnExportExcel" href="javascript:void(0)"><img src="<?= base_url()?>includes/icons/blue-document-excel.png" alt="exportExcel" /> Export</a>&nbsp;&nbsp;
                        <input type="date" id="txtDate" name="txtDate" style="width: 135px" />
                        <?=$cmbAvailDept?> |
                        <?=$cmbAvailStat?> |
                        <?=$cmbType?> |
                        <input type="text" id="txtSearch" name="txtSearch" style="width: 250px" value="<?=$txtSearch?>"/>
                        <input type="submit" value="Search" />
                    </form>
                </div> 
                <br style="clear: both"/>
                <div class="dvContent" style="padding-right: 5px">
                    <input type="button" id="btn_paid" name="buttonPaid" value="Paid" />
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
    <div id="dvFinSettle">
        <br/>
        <table style="width: 100%; border-collapse: separate; border-spacing: 5px">            
            <tr>
                <td style="width: 100px;text-align: right"> Documents :</td>
                <td> 
                    <ul>
                        <li><input type="checkbox" id="chk1"/> Amount </li>
                        <li><input type="checkbox" id="chk2"/> Attachment </li>
                        <li><input type="checkbox" id="chk3"/> Claim/Refund </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;padding-top: 10px"> <input type="button" id="btnAppv" name="btnAppv" value="Approve" onclick="approveSettle()"/></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;padding-top: 10px"> <input type="hidden" id="hidCaId" name="hidCaId"/> <input type="hidden" id="hidEmail" name="hidEmail"/> </td>
            </tr>
        </table>
    </div>
</body>
</html>