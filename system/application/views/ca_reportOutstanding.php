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

        // penambahan yudhis
        function toggle() {
            var elements = document.getElementById("tblDatas1").querySelectorAll(".hidden_row");
            if( document.getElementById("hidden_rows").style.display=='none' ){
               for(var i=0; i<elements.length; i++) {
                    elements[i].style.display = 'table-row';
                }
            }else{
               for(var i=0; i<elements.length; i++) {
                    elements[i].style.display = 'none';
                }
            }
        }

        function toggle2() {
            var elements2 = document.getElementById("tblDatas2").querySelectorAll(".hidden_row");
            if( document.getElementById("hidden_rows2").style.display=='none' ){
               for(var i=0; i<elements2.length; i++) {
                    elements2[i].style.display = 'table-row';
                }
            }else{
               for(var i=0; i<elements2.length; i++) {
                    elements2[i].style.display = 'none';
                }
            }
        }

        function toggle3() {
            var elements3 = document.getElementById("tblDatas3").querySelectorAll(".hidden_row");
            if( document.getElementById("hidden_rows3").style.display=='none' ){
               for(var i=0; i<elements3.length; i++) {
                    elements3[i].style.display = 'table-row';
                }
            }else{
               for(var i=0; i<elements3.length; i++) {
                    elements3[i].style.display = 'none';
                }
            }
        }
        // ==========================================================
        
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
                    <form action="<?php echo base_url()?>index.php/ca_core/cahistory" method="post">
                        <!-- <a class="topLink" id="btnExportExcel" href="<?= base_url()?>index.php/ca_core/exportExcelExim"><img src="<?= base_url()?>includes/icons/blue-document-excel.png" alt="exportExcel" /> Export</a>&nbsp;&nbsp;
                        <input type="text" id="txtSearch" name="txtSearch" style="width: 250px" value="<?=$txtSearch?>"/>
                        <input type="submit" value="Search" /> -->
                    </form>
                </div> 
                <br style="clear: both"/>
                <div class="dvDepartment">
                    Department :<h3>PROJECT</h3><br/><br/>
                </div>
                <?php if ($tblDatas == ''){ ?>
                <?php } else{ ?>
                <div class="dvPICName">
                    <p><h4>Imam Supriyanto </h4><br/></p>
                    <a class="topLink" id="btnExportExcel" href="<?= base_url()?>index.php/ca_core/exportExcelAging1"><img src="<?= base_url()?>includes/icons/blue-document-excel.png" alt="exportExcel" /> Export</a>
                </div>
                <div class="dvContent" style="padding-right: 5px">
                    <div id="tblDatas1"><?=$tblDatas?></div>
                    <input onClick="toggle();" type="button" value="Detail" style="margin-top: 10px" /><br/>
                </div>
                <div class="dvPagin">
                    <?=$paging?>
                </div>
                <?php } ?>

                <!-- penambahan dari yudhis -->
                <?php if ($tblDatas2 == ''){ ?>
                <?php } else{ ?>
                <div class="dvPICName2" style="margin-top: 15px">
                    <p><h4>Tamma Putranianto</h4><br/></p>
                    <a class="topLink" id="btnExportExcel" href="<?= base_url()?>index.php/ca_core/exportExcelAging2"><img src="<?= base_url()?>includes/icons/blue-document-excel.png" alt="exportExcel" /> Export</a>
                </div>
                <div class="dvContent2" style="padding-right: 5px">
                    <div id="tblDatas2"><?=$tblDatas2?></div>
                    <input onClick="toggle2();" type="button" value="Detail" style="margin-top: 10px" /><br/>
                </div>
                <div class="dvPagin2">
                    <?=$paging2?>
                </div>
                <?php } ?>

                <?php if ($tblDatas3 == ''){ ?>
                <?php } else{ ?>
                <div class="dvPICName3" style="margin-top: 15px">
                    <p><h4>Zacky Zul Azhar</h4><br/></p>
                    <a class="topLink" id="btnExportExcel" href="<?= base_url()?>index.php/ca_core/exportExcelAging3"><img src="<?= base_url()?>includes/icons/blue-document-excel.png" alt="exportExcel" /> Export</a>
                </div>
                <div class="dvContent3" style="padding-right: 5px">
                    <div id="tblDatas3"><?=$tblDatas3?></div>
                    <input onClick="toggle3();" type="button" value="Detail" style="margin-top: 10px" /><br/>
                </div>
                <div class="dvPagin3">
                    <?=$paging3?>
                </div>
                <?php } ?>
                <!-- ================================================= -->

            </td>
        </tr>
        <tr>
            <td colspan="2" class="secFooter"><? include_once 'inc_footer.php';?></td>
        </tr>
    </table>
</body>
</html>