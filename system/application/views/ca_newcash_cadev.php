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
            // $('#txtAmount').blur(function(){cekAmount()});

            // for hiding some columns
            $('#ContDepartment').hide();
            $('#ContUploadCA').hide();
            $('#ContDANo').hide();
            $('#ContAJU').hide();
            $('#ContItemCash').hide();
            $('#ContRefNo').hide();
            $('#ContRemarkDN').hide();
            $('#ContCostName').hide();
            $('#ContEstimateValue').hide();
            $('#ContPICTransfer').hide();
            $('#ContBankName').hide();
            $('#ContNoRekPIC').hide();
            $('#ContUploadBulkCA').hide();


            $('#cmbCashType').change(function(){
                // cekAmount()

                var t = document.getElementById("cmbCashType");
                var cashType = t.options[t.selectedIndex].text;

                if (cashType == "Advance & Settlement Operational") {
                    $('#ContDepartment').show();
                    $('#ContUploadCA').show();
                    $('#ContDANo').show();
                    $('#ContAJU').show();
                    $('#ContItemCash').show();
                    $('#ContRefNo').show();
                    $('#ContRemarkDN').show();
                    $('#ContCostName').show();
                    $('#ContEstimateValue').show();
                    $('#ContPICTransfer').show();
                    $('#ContBankName').show();
                    $('#ContNoRekPIC').show();
                    $('#ContUploadBulkCA').show();
                } else {
                    $('#ContDepartment').hide();
                    $('#ContUploadCA').hide();
                    $('#ContDANo').hide();
                    $('#ContAJU').hide();
                    $('#ContItemCash').hide();
                    $('#ContRefNo').hide();
                    $('#ContRemarkDN').hide();
                    $('#ContCostName').hide();
                    $('#ContEstimateValue').hide();
                    $('#ContPICTransfer').hide();
                    $('#ContBankName').hide();
                    $('#ContNoRekPIC').hide();
                    $('#ContUploadBulkCA').hide();
                }

            });

            $('#chkUploadCA').change(function(){initUploadOption()});

            function initUploadOption(){
            if ($('#chkUploadCA').is(':checked')) {
                $('#txtDANo').attr('readonly','readonly');
                $('#txtAJU').attr('readonly','readonly');
                $('#txtItemCash').attr('readonly','readonly');
                $('#txtRefNo').attr('readonly','readonly');
                $('#txtRemarkDN').attr('readonly','readonly');
                $('#txtCostName').attr('readonly','readonly');
                $('#txtEstimateValue').attr('readonly','readonly');
                $('#txtPICTransfer').attr('readonly','readonly');
                $('#txtBankName').attr('readonly','readonly');
                $('#txtNoRekPIC').attr('readonly','readonly');
                $('#txtDANo').attr('readonly','readonly');
                $('#txtDANo').attr('readonly','readonly');
                $('#uploadBulkCA').attr('readonly','readonly');
            }else{
                $('#txtDANo').attr('readonly',false);
                $('#txtAJU').attr('readonly',false);
                $('#txtItemCash').attr('readonly',false);
                $('#txtRefNo').attr('readonly',false);
                $('#txtRemarkDN').attr('readonly',false);
                $('#txtCostName').attr('readonly',false);
                $('#txtEstimateValue').attr('readonly',false);
                $('#txtPICTransfer').attr('readonly',false);
                $('#txtBankName').attr('readonly',false);
                $('#txtNoRekPIC').attr('readonly',false);
                $('#uploadBulkCA').attr('readonly',false);
            }               
        }

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

                            <!-- =========== penambahan baris baru ============ -->
                            <tr id="ContDepartment">
                                <td style="text-align: right;">Department :</td>
                                <td><?=$cmbCashDept?></td>
                            </tr>
                            <tr id="ContUploadCA">
                                <td style="text-align: right;width: 100px"> Bulk Upload Cash Advance :</td>
                                <td style="" colspan="3">
                                    <input type="checkbox" id="chkUploadCA" name="chkUploadCA" <?php echo $chkUploadCA?>/> <i>*Mandatory</i>
                                </td>
                            </tr>
                            <tr id="ContDANo">
                                <td style="text-align: right;">*DA Number :</td>
                                <td style="" colspan=""><input type="text" id="txtDANo" name="txtDANo" style="width: 200px;" /></td>
                            </tr>
                            <tr id="ContAJU">
                                <td style="text-align: right;">*Aju :</td>
                                <td style="" colspan=""><input type="text" id="txtAJU" name="txtAJU" style="width: 200px;" /></td>
                            </tr> 
                            <tr id="ContItemCash">
                                <td style="text-align: right;">*Item :</td>
                                <td style="" colspan=""><input type="text" id="txtItemCash" name="txtItemCash" style="width: 200px;" /></td>
                            </tr>
                            <tr id="ContRefNo">
                                <td style="text-align: right;">*Ref B/L No :</td>
                                <td style="" colspan=""><input type="text" id="txtRefNo" name="txtRefNo" style="width: 200px;" /></td>
                            </tr>
                            <tr id="ContRemarkDN">
                                <td style="text-align: right;">*Remark DN/Cost :</td>
                                <td style="" colspan=""><input type="text" id="txtRemarkDN" name="txtRemarkDN" style="width: 200px;" /></td>
                            </tr>
                            <tr id="ContCostName">
                                <td style="text-align: right;">*Cost Name :</td>
                                <td style="" colspan=""><input type="text" id="txtCostName" name="txtCostName" style="width: 200px;" /></td>
                            </tr>
                            <tr id="ContEstimateValue">
                                <td style="text-align: right;">*Estimate Value (IDR) :</td>
                                <td><input type="text" id="txtEstimateValue" name="txtEstimateValue" style="width: 100px" class="money"/></td>
                            </tr>
                            <tr id="ContPICTransfer">
                                <td style="text-align: right;">*PIC Transfer :</td>
                                <td style="" colspan=""><input type="text" id="txtPICTransfer" name="txtPICTransfer" style="width: 200px;" /></td>
                            </tr>
                            <tr id="ContBankName">
                                <td style="text-align: right;">*Bank Name :</td>
                                <td style="" colspan=""><input type="text" id="txtBankName" name="txtBankName" style="width: 200px;" /></td>
                            </tr>
                            <tr id="ContNoRekPIC">
                                <td style="text-align: right;">*No. Rekening PIC Transfer :</td>
                                <td style="" colspan=""><input type="text" id="txtNoRekPIC" name="txtNoRekPIC" style="width: 200px;" /></td>
                            </tr>
                            <tr id="ContUploadBulkCA">
                                <td style="width: 150px;text-align: right;">*File Bulk Upload CA :</td>
                                <td><input type="file" id="uploadBulkCA" name="uploadBulkCA" />  &nbsp;&nbsp;<i>*if required</i></td>
                            </tr>
                            <!--  =================================================================== -->
                            <tr>
                                <td id="result"></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Amount Type :</td>
                                <td><input type="text" id="txtAmount" name="txtAmount" style="width: 100px" class="money"/> IDR. </td>
                            </tr>
                            <?php if ($userDiv == 'PLO'){ ?>
                            <?php echo validation_errors(); ?>
                            <?php echo form_open('form/validation'); ?>
                            <tr id="ContBookingNo">
                                <td style="text-align: right;">Booking No :</td>
                                <td style="" colspan=""><input type="text" id="txtBookingNo" name="txtBookingNo" style="width: 200px;" /><span style="color: red; display: block"><i>*max 20 characters.</i></span></td>
                            </tr>
                            <?php } ?>
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