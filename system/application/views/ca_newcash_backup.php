<?session_start()?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title><?include_once 'inc_title.php';?></title>
    <link href="<?=base_url();?>includes/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <script type='text/javascript' src='<?=base_url();?>includes/jquery/js/jquery-1.5.2.min.js'></script>
    <script type='text/javascript' src='<?=base_url();?>includes/jquery/css/aristo/jquery-ui-1.8rc3.custom.min.js'></script>
    <script type='text/javascript' src='<?=base_url();?>includes/jquery/plugins/maskMoney.js'></script>
    
    <style type="text/css">
        @import url("<?=base_url();?>includes/css/globalReset.css");
        @import url("<?=base_url();?>includes/css/theme.css");
        @import url("<?=base_url();?>includes/css/tableSoft.css");        
        @import url("<?=base_url();?>includes/jquery/css/aristo/jquery-ui-1.8rc3.custom.css");        
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.datepick').datepicker({
                dateFormat: 'dd-mm-yy',
                showAnim: 'fadeIn',
                changeMonth: true,
                changeYear: true
            }).css('width', '90px');
            $('#btnSubmit').attr('disabled','disabled');
            $('.money').priceFormat({prefix: '',centsSeparator: '',thousandsSeparator: '.',centsLimit: 0});
            $('#txtAmount').blur(function(){cekAmount()});
            $('#cmbCashType').change(function(){cekAmount()});
            $("#frmEntry").submit(function(e){
               var getSubmitVal = $('#hidSubmit').val();
                if(getSubmitVal == "1"){
                    return true;
                    $('#btnSubmit').attr('disabled','disabled').attr('value','Please Wait');
                    
                }else{
                    alert("Please Check Approval Flow,\n if doesn't exist please contact administrator.");
                    return false;
                }
            }); 
        });
        
        function cekAmount(){
            var getAmount = $('#txtAmount').val();
            var getType = $('#cmbCashType option:selected').val();
            if(getAmount != ""){
                $.ajax({
                    url: "<?= base_url() ?>index.php/ajax_manager/initLastApproval/"+getType+"/"+getAmount ,
                    beforeSend:function(){$('#dvLastAppv').html("<img src=\"<?=base_url()?>/includes/images/ajax-loader.gif\" alt=\"\"/>Calculate Approval");},
                    type: "GET",
                    success: function(data){$('#dvLastAppv').html(data);cekApprovalFlow();return false;}
                });
            }            
        }
        function cekApprovalFlow(){
            var getUserLev = $('#hidUserLevel').val();
            var getLastApprv = $('#hidApprove').val();   

            if(getUserLev != ""){
                $.ajax({
                        url: "<?= base_url() ?>index.php/ajax_manager/initUserApproval/"+getUserLev+"/"+getLastApprv ,
                        beforeSend:function(){$('#btnSubmit').attr('disabled','disabled');$('#dvAppvType').html("<img src=\"<?=base_url()?>/includes/images/ajax-loader.gif\" alt=\"\"/>Init Approval Flow");},
                        type: "GET",
                        success: function(data){$('#btnSubmit').removeAttr('disabled');$('#dvAppvType').html(data);$('#hidSubmit').val('1');return false; }
                    });
            }
        }
        function viewHelp(){
            alert("Pastikan Approval Type muncul dan alur nya benar, \nJika tidak silahkan buat ticket atau email ke awirawan@ckb.co.id \ndengan menuliskan detail kepada siapa direct report anda. \n\n MOHON ISIKAN DETAIL TICKET DENGAN INFORMASI NAMA ATASAN DENGAN JELAS.");            
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
                <div class="dvHeaderContent"><?=$header?> <hr class="hrStyle"/> </div>
                <div class="dvContent">
                    <form name="frmEntry" id="frmEntry" action="<?=base_url()?>index.php/ca_core/saveNewCash" method="post" enctype="multipart/form-data">
                        <table style="width: 100%; border-spacing: 10px; border-collapse: separate">
                            <tr>
                                <td style="width: 150px;text-align: right;">Date :</td>
                                <td><input type="text" class="datePick" id="txtCaDate" name="txtCaDate" value="<?php echo $currDate; ?>"/> </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Requester :</td>
                                <td><input readonly type="text" id="txtRequester" name="txtRequester" style="width: 200px" value="<?=$userNameFull?>"/><input type="hidden" name="hidUserLevel" id="hidUserLevel" value="<?=$userLevDetail?>"/> </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Request Type :</td>
                                <td><?=$cmbCashType?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Amount Type :</td>
                                <td><input type="text" id="txtAmount" name="txtAmount" style="width: 100px" class="money"/> IDR. </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"> Approval :</td>
                                <td><div id="dvLastAppv"></div> </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Approval Type :</td>
                                <td><div id="dvAppvType"></div> </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"></td>
                                <td><a href="javascript:void(0)" onclick="viewHelp()" title="Bantuan"><img id="btnHelp" src="<?php echo base_url()?>includes/icons/bubbles.png"/> Bantuan</a></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;vertical-align: middle">Purpose :</td>
                                <td><textarea id="txtPurpose" name="txtPurpose" cols="50" rows="5"></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="hrStyle"/>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 150px;text-align: right;">File Attachment 1 :</td>
                                <td><input type="file" id="UpldFile" name="UpldFile" />  &nbsp;&nbsp;<i>*if required</i></td>
                            </tr>                            
                            <tr>
                                <td colspan="2">
                                    <hr class="hrStyle"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center"> 
                                    <input type="submit" value="Submit Cash Advance" id="btnSubmit" name="btnSubmit"/> 
                                    <input type="hidden" name="hidSubmit" id="hidSubmit"/>
                                </td>
                            </tr>
                        </table>
                    </form>
                    
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="secFooter"><? include_once 'inc_footer.php';?></td>
        </tr>
    </table>
</body>
</html>