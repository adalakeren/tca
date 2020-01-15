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

            // penambahan yudhis
            var t = document.getElementById("cmbCashType");
            var cashType = t.options[t.selectedIndex].text;
            if (cashType != "Advance & Settlement Operational") {
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
            } else{
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
            }

            $('#cmbCashType').change(function(){
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
                    // $('#ContUploadBulkCA').show();
                    $('#ContChkAddTypePLS').show();
                    $('#ContEstimateDatePLS').hide();
                    $('#ContChkAddTypePP4').hide();
                    $('#ContEstimateDatePP4').hide();
                } else if (cashType == "Advance & Settlement - Uang Jalan") {
                    $('#ContChkAddTypePP4').show();
                    $('#ContEstimateDatePP4').hide();
                    $('#ContChkAddTypePLS').hide();
                    $('#ContEstimateDatePLS').hide();
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
                    // $('#ContUploadBulkCA').hide();
                    $('#ContChkAddTypePLS').hide();
                    $('#ContEstimateDatePLS').hide();
                    $('#ContChkAddTypePP4').hide();
                    $('#ContEstimateDatePP4').hide();
                }

            });

            // $('#cmbCashType').change(function(){
            //     var t = document.getElementById("cmbCashType");
            //     var cashType = t.options[t.selectedIndex].text;

            //     if (cashType == "Advance & Settlement Uang Jalan") {
            //         $('#ContChkAddTypePP4').show();
            //         $('#ContEstimateDatePP4').show();
            //     }
            // });

            // $('#ContChkAddTypePLS').hide();
            $('#ContEstimateDatePLS').hide();
            $('#ContChkAddTypePP4').hide();
            $('#ContEstimateDatePP4').hide();
            //Penambahan checkbox PLS
            $('#chkPLS').change(function(){initPLSOption()});
            $('#chkLuarKota').change(function(){initPLSOption()});

            $('#chkPP4L').change(function(){initPP4Option()});
            $('#chkPP4K').change(function(){initPP4Option()});


            // $('#uploadBulkCA').attr('disabled','disabled');
            $('#ContUploadBulkCA').hide();
            $('#chkUploadCA').change(function(){initUploadOption()});
            // ============================================================

        });
        
        // penambahan
        function initUploadOption(){
                if ($('#chkUploadCA').is(':checked')) {
                    $('#txtDANo').attr('readonly','readonly');
                    $('#txtDANo').css('background-color', '#c2c3c4');

                    $('#txtAJU').attr('readonly','readonly');
                    $('#txtAJU').css('background-color', '#c2c3c4');

                    $('#txtItemCash').attr('readonly','readonly');
                    $('#txtItemCash').css('background-color', '#c2c3c4');

                    $('#txtRefNo').attr('readonly','readonly');
                    $('#txtRefNo').css('background-color', '#c2c3c4');

                    $('#txtRemarkDN').attr('readonly','readonly');
                    $('#txtRemarkDN').css('background-color', '#c2c3c4');

                    $('#txtCostName').attr('readonly','readonly');
                    $('#txtCostName').css('background-color', '#c2c3c4');

                    $('#txtEstimateValue').attr('readonly','readonly');
                    $('#txtEstimateValue').css('background-color', '#c2c3c4');

                    $('#txtPICTransfer').attr('readonly','readonly');
                    $('#txtPICTransfer').css('background-color', '#c2c3c4');

                    $('#txtBankName').attr('readonly','readonly');
                    $('#txtBankName').css('background-color', '#c2c3c4');

                    $('#txtNoRekPIC').attr('readonly','readonly');
                    $('#txtNoRekPIC').css('background-color', '#c2c3c4');

                    // $('#uploadBulkCA').attr('disabled', false);
                    $('#ContUploadBulkCA').show();
                }else{
                    $('#txtDANo').attr('readonly',false);
                    $('#txtDANo').css('background-color', '');

                    $('#txtAJU').attr('readonly',false);
                    $('#txtAJU').css('background-color', '');

                    $('#txtItemCash').attr('readonly',false);
                    $('#txtItemCash').css('background-color', '');

                    $('#txtRefNo').attr('readonly',false);
                    $('#txtRefNo').css('background-color', '');

                    $('#txtRemarkDN').attr('readonly',false);
                    $('#txtRemarkDN').css('background-color', '');

                    $('#txtCostName').attr('readonly',false);
                    $('#txtCostName').css('background-color', '');

                    $('#txtEstimateValue').attr('readonly',false);
                    $('#txtEstimateValue').css('background-color', '');

                    $('#txtPICTransfer').attr('readonly',false);
                    $('#txtPICTransfer').css('background-color', '');

                    $('#txtBankName').attr('readonly',false);
                    $('#txtBankName').css('background-color', '');

                    $('#txtNoRekPIC').attr('readonly',false);
                    $('#txtNoRekPIC').css('background-color', '');

                    // $('#uploadBulkCA').attr('disabled','disabled');
                    $('#ContUploadBulkCA').hide();
                }               
            }


        function initPLSOption(){
            if ($('#chkPLS').is(':checked')==true) {
                $('#chkNonPLS').attr('disabled',true);
                $('#ContEstimateDatePLS').show();
            }else if ($('#chkPLS').is(':checked')==false) {
                $('#ContEstimateDatePLS').hide();
                $('#chkNonPLS').attr('disabled', false);
            } else if($('#chkNonPLS').is(':checked')==true) {
                $('#chkPLS').attr('disabled',true);
                $('#ContEstimateDatePLS').hide();
            }  
        }

        function initPP4Option(){
            if ($('#chkPP4L').is(':checked')==true) {
                $('#chkPP4K').attr('disabled',true);
                $('#ContEstimateDatePP4').show();
            }else if ($('#chkPP4L').is(':checked')==false) {
                $('#chkPP4K').attr('disabled',false);
                $('#ContEstimateDatePP4').hide();
            } else if($('#chkPP4K').is(':checked')==true) {
                $('#chkPP4L').attr('disabled',true);
                $('#ContEstimateDatePP4').hide();
            }     
        }

        // =====================================================================
        
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
            $('#dlg_bantuan').dialog({resizable:false,modal:true,title:"Informasi"});
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

                            <tr id="ContChkAddTypePLS">
                                <td style="text-align: right;width: 100px;color:red">TCA Type (*project-only):</td>
                                <td style="" colspan="3">
                                    <input type="checkbox" id="chkPLS" name="chkPLS" <?php echo $chkUploadCA?>/> <i style="margin-right: 10px">PLS</i>
                                    <input type="checkbox" id="chkNonPLS" name="chkNonPLS" <?php echo $chkUploadCA?>/> <i>NON PLS</i>
                                </td>
                            </tr>

                            <tr id="ContEstimateDatePLS">
                                <td style="width: 150px;text-align: right;">Estimate Date :</td>
                                <td><input type="text" class="datePick" id="txtEstimateDatePLS" name="txtEstimateDatePLS" value="<?php echo $currDate; ?>"/> </td>
                            </tr>

                            <tr id="ContChkAddTypePP4">
                                <td style="text-align: right;width: 100px;color:red">TCA Type (*project-only):</td>
                                <td style="" colspan="3">
                                    <input type="checkbox" id="chkPP4L" name="chkPP4L" <?php echo $chkUploadCA?>/> <i style="margin-right: 10px">PP > 4 Hari</i>
                                    <input type="checkbox" id="chkPP4K" name="chkPP4K" <?php echo $chkUploadCA?>/> <i>PP <= 4 Hari</i>
                                </td>
                            </tr>

                            <tr id="ContEstimateDatePP4">
                                <td style="width: 150px;text-align: right;">Estimate Date :</td>
                                <td><input type="text" class="datePick" id="txtEstimateDatePP4" name="txtEstimateDatePP4" value="<?php echo $currDate; ?>"/> </td>
                            </tr>

                            <!-- =========== penambahan baris baru ============ -->
                            <?php if ($userDept == "EXM" || $userDept == "FLT" || $userDept == "OPS")  { ?>
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
                            <?php } ?>
                            <!--  =================================================================== -->

                            <tr>
                                <td style="text-align: right;">Amount Type :</td>
                                <td><input type="text" id="txtAmount" name="txtAmount" style="width: 100px" class="money"/> IDR. </td>
                            </tr>
                            <?php if ($userDiv == 'PLO'){ ?>
                            <tr id="ContBookingNo">
                                <td style="text-align: right;">Booking No :</td>
                                <td style="" colspan=""><input type="text" id="txtBookingNo" name="txtBookingNo" maxlength="20" style="width: 200px;" /><span style="color: red; display: block"><i>*max 20 characters.</i></span></td>
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
<div id="dlg_bantuan"  style="width:400px;height:200px;padding:10px;display: none; ">
    <p>Pastikan Approval Type muncul dan alur nya benar, 
            Jika tidak silahkan buat ticket atau email ke IT@ckb.co.id 
            dengan menuliskan detail kepada siapa direct report anda.
            <br>
            <br>
            MOHON ISIKAN DETAIL TICKET DENGAN INFORMASI NAMA ATASAN DENGAN JELAS.</p>
</div>