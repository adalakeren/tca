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
        $(document).ready(function() {
            $('#checkBoxAll').click(function(){
                if ($(this).is(":checked"))
                    $('.checkApp').attr('checked', true);
                else
                    $('.checkApp').attr('checked', false);
           });

            $('#checkBoxAll2').click(function(){
                if ($(this).is(":checked"))
                    $('.checkApp2').attr('checked', true);
                else
                    $('.checkApp2').attr('checked', false);
            });

            $('#btn_ApproveCheck').click(function(){
                if (confirm("Are you sure you want to approve this?"))
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
                            approve(id[i]);
                        }
                    }
                }
                else
                {
                    return false;
                }
           });

            $('#btn_filter').click(function(){
                var dep = $('#dept_list').val();
                var req = $('#reqname_list').val();
                var type = $('#cmbCashType').val();

                if (dep == "") dep = "ALL";
                if (req == "") req = "ALL";
                if (type == "") req = "ALL";

                window.document.location.href = "<?php echo base_url(); ?>" + 
                    "index.php/apv_core/approval/" + dep + "/" + req + "/" + type;
            });
           
        });

        function openPage(ddl)
        {
            //console.log($(ddl).val());
            var ddlvalue = $(ddl).val();

            window.document.location.href = "<?php echo base_url(); ?>index.php/apv_core/approval/" + ddlvalue;
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
        function approve(getId){
            var getReason = "";
            $.ajax({
                url: "<?= base_url() ?>index.php/ajax_manager/approval" , 
                data: ({setType : 'approve', setId : getId, setReason : getReason}),
                beforeSend:function(){$('#dvApprove'+getId).html("<img src=\"<?=base_url()?>/includes/images/ajax-loader.gif\" alt=\"\"/>");},
                type: "POST",
                success: function(data){$('#dvReject'+getId).html("");$('#dvApprove'+getId).html("<img src=\"<?=base_url()?>/includes/icons/ticks.png\" alt=\"\"/>");return false;}
            });               
        }
        function reject(getId){
            var getReason = prompt("Reject Reason", "");
            if(getReason){
                $.ajax({
                    url: "<?= base_url() ?>index.php/ajax_manager/approval" , 
                    data: ({setType : 'reject', setId : getId, setReason : getReason}),
                    beforeSend:function(){$('#dvReject'+getId).html("<img src=\"<?=base_url()?>/includes/images/ajax-loader.gif\" alt=\"\"/>");},
                    type: "POST",
                    success: function(data){$('#dvApprove'+getId).html("");$('#dvReject'+getId).html("<img src=\"<?=base_url()?>/includes/icons/block.png\" alt=\"\"/>");return false;}
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
                    <form action="" method="post">
                        <input type="text" id="txtSearch" name="txtSearch" style="width: 250px" value="<?=$txtSearch?>"/>
                        <input type="submit" value="Search" />
                    </form>
                </div> 
                <br style="clear: both"/>
                <div class="dvContent" style="padding-right: 5px">
                    <div class="tblDatas">
                    <input type="button" id="btn_ApproveCheck" name="buttonApprove" value="Approve" />
                    <select id="dept_list">
                        <option value="">--Filter Departement--</option>
                        <option value="ALL">ALL</option>
                        <?php
                        foreach($deptlist->result_array() as $d)
                        {?>
                        <option value="<?php echo $d['requester_dept'];?>"><?php echo $d['requester_dept'];?></option>
                        <?php
                        }?>
                    </select>
                    <select id="reqname_list">
                        <option value="">--Filter Requester--</option>
                        <option value="ALL">ALL</option>
                        <?php
                        foreach($reqNameList->result_array() as $d)
                        {?>
                        <option value="<?php echo $d['requester_name'];?>"><?php echo $d['requester_name'];?></option>
                        <?php
                        }?>
                    </select>
                    <?=$cmbCashType?>
                    <input type="button" id="btn_filter" value="Filter" />
                    <h3 style="margin-top: 20px">Table SUJ</h3>
                    <?=$tblDatas?>

                    <h3 style="margin-top: 50px;margin-bottom: 20px">Table Non SUJ</h3>
                    <?=$tblDatas2?>
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