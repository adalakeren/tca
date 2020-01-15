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
                
                <div id="dvLoadingStatus" style="padding-bottom: 5px; padding-left: 5px"></div><br/>
                <div class="dvContent">
                    <div id="dvNewCashAppv" style="padding-left: 10px">
                        
                        <table>
                            <tr>
                                <td><span style="font-size: 11px;font-weight: bold"><a class="topLink" href="<?=base_url()?>index.php/apv_core/approval">Outstanding Cash Advance Approval - SUJ</a></span>  <hr class="hrStyle"/></td>                                
                                <td style="width: 50px"></td>                                
                                <td><span style="font-size: 11px;font-weight: bold"><a class="topLink" href="<?=base_url()?>index.php/apv_core/settleapproval">Outstanding Settlement Approval - SUJ</a></span>  <hr class="hrStyle"/></td>                                
                            </tr>                            
                            <tr>
                                <td><?=$getNewCashApproval?></td>                                
                                <td></td>                                
                                <td><?=$getSettleApproval?></td>                                
                            </tr>                            
                        </table>
                       

                        <!-- <?php echo $_SESSION['level_id']; ?> -->

                        <table style="margin-top: 50px;margin-bottom: 50px">
                            <tr>
                                <td><span style="font-size: 11px;font-weight: bold"><a class="topLink" href="<?=base_url()?>index.php/apv_core/approval">Outstanding Cash Advance Approval - Non SUJ</a></span>  <hr class="hrStyle"/></td>                                
                                <td style="width: 50px"></td>                                
                                <td><span style="font-size: 11px;font-weight: bold"><a class="topLink" href="<?=base_url()?>index.php/apv_core/settleapproval">Outstanding Settlement Approval - Non SUJ</a></span>  <hr class="hrStyle"/></td>                                
                            </tr>                            
                            <tr>
                                <td><?=$getNewCashApprovalNonSUJ?></td>                                
                                <td></td>                                
                                <td><?=$getSettleApprovalNonSUJ?></td>                                
                            </tr>                            
                        </table>          

                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="secFooter"><? include_once 'inc_footer.php';?></td>
        </tr>
    </table>
</body>
</html>