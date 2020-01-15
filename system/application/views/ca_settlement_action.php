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
            alert($('#hideIsSuj').val());

            $('.money').priceFormat({prefix: '',centsSeparator: '',thousandsSeparator: '.',centsLimit: 0});
            $('.datepick').datepicker({
                dateFormat: 'dd-mm-yy',
                showAnim: 'fadeIn',
                changeMonth: true,
                changeYear: true
            }).css('width', '90px');
            $('#txtSettleamount').blur(function(){cekAmount()}); 
            $('#txtSettleamount').blur(function(){  
                /*
                var calc = '<?=$cashAmount?>'.replace(".","") - $('#txtSettleamount').val().replace(".","");
                alert('<?=$cashAmount?>'.replace(".",""));
                $('#dvDifferent').html(calc+' IDR.');
                */
            });
            $('#btnSubmit').attr("disabled", "true");
            $('#btnCalc').click(function(){cekAmount();});
			
			$("#frmEntry").submit(function(e){
                $('#btnSubmit').attr('disabled','disabled').attr('value','Please Wait');
				return true;
            }); 
        });
        
        function cekAmount(){
            var getCashAmount = $('#txtCashAmount').val().replace(/\./g,"");
            var getAmount = $('#txtSettleamount').val().replace(/\./g,"");
            var getIsSuj = $('#hideIsSuj').val();
            if (getIsSuj == "") {
                getIsSuj = 0;
            };
            var calc = getCashAmount - getAmount;                
            $('#dvDifferent').html(addCommas(calc)+' IDR.');
            //var getAmount = $('#txtSettleamount').val();
            var getType = '<?=$cashTypeVal?>';
            if(getAmount != ""){
                
                $.ajax({
                    url: "<?= base_url() ?>index.php/ajax_manager/initLastApproval/"+getType+"/"+getAmount+"/"+getIsSuj ,
                    beforeSend:function(){$('#dvLastAppv').html("<img src=\"<?=base_url()?>/includes/images/ajax-loader.gif\" alt=\"\"/>Calculate Approval");},
                    type: "GET",
                    success: function(data){$('#dvLastAppv').html(data);cekApprovalFlow();return false;}
                });
            }            
        }
        function cekApprovalFlow(){
            var getUserLev = '<?=$userLevelVal?>';
            var getLastApprv = $('#hidApprove').val(); 
            var getIsSuj = $('#hideIsSuj').val();  
            if (getIsSuj == "") {
                getIsSuj = 0;
            };   
            if(getUserLev != ""){
                $.ajax({
                        url: "<?= base_url() ?>index.php/ajax_manager/initUserApproval/"+getUserLev+"/"+getLastApprv+"/"+getIsSuj ,
                        beforeSend:function(){$('#dvAppvType').html("<img src=\"<?=base_url()?>/includes/images/ajax-loader.gif\" alt=\"\"/>Init Approval Flow");},
                        type: "GET",
                        success: function(data){$('#dvAppvType').html(data);$('#btnSubmit').removeAttr("disabled");return false;}
                    });
            }
        }
        function addCommas(nStr)
        {
                nStr += '';
                x = nStr.split('.');
                x1 = x[0];
                x2 = x.length > 1 ? '.' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                        x1 = x1.replace(rgx, '$1' + '.' + '$2');
                }
                return x1 + x2;
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
                <div class="dvContent">
                    <form id="frmEntry" action="<?=base_url()?>index.php/ca_core/saveSettlement/<?=$caGuid;?>" method="post">
                        <table style="width: 100%; border-spacing: 10px; border-collapse: separate">
                            <tr>                                
                                <input type="hidden" id="hideIsSuj" name="hideIsSuj" value="<?php echo $isSuj?>">
                                <td style="width: 180px;text-align: right;">Date :</td>
                                <td><?=$txtDate?></td>                               
                            </tr>
                            <tr>
                                <td style="text-align: right;">Requester :</td>
                                <td><?=$txtRequester?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Request Type :</td>
                                <td><?=$cashType?> <input type="hidden" id="hidRequestType" name="hidRequestType" value="<?php echo $cashTypeVal?>"></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Amount Type :</td>
                                <td><?=$cashAmount?> IDR. </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Status :</td>
                                <td><?=$caStatus?></td>
                            </tr>
                            <!--
                            <tr>
                                <td style="text-align: right;">Last Approval :</td>
                                <td><div id="dvLastAppv"></div> </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Approval Type :</td>
                                <td><div id="dvAppvType"></div> </td>
                            </tr>
                            -->                            
                            <tr>
                                <td style="text-align: right;vertical-align: middle">Purpose :</td>
                                <td><textarea id="txtPurpose" name="txtPurpose" cols="50" rows="4" readonly><?=$txtPurpose?></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <b>Settlement</b>
                                    <hr class="hrStyle"/>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Settled Cash Amount :</td>
                                <td><input type="hidden" id="txtCashAmount" name="txtCashAmount" value="<?php echo $cashAmount; ?>"/><input type="text" id="txtSettleamount" name="txtSettleamount" value="" class="money"/> IDR. <input type="button" id="btnCalc" name="btnCalc" value="Calculate"/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">DA/Manifest No :</td>
                                <td><input type="text" id="txtManifestNo" name="txtManifestNo" value="" class=""/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;"><i>Need Returned</i> :</td>
                                <td><div id="dvDifferent"></div></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Settlement Last Approval :</td>
                                <td><div id="dvLastAppv"></div> </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Settlement Approval Type :</td>
                                <td><div id="dvAppvType"></div> </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="hrStyle"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center"> <input id="btnSubmit" type="submit" value="Submit"/> </td>
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