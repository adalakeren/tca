<a class="topLink" id="btnExportExcel" href="javascript:void(0)"><img src="<?= base_url()?>includes/icons/blue-document-excel.png" alt="exportExcel" /> Export</a>&nbsp;&nbsp;
<?=$suj_table?>

<script type="text/javascript">
        $(document).ready(function(){
        	$('#btnExportExcel').click(function(){
                var type = "";
                var dept = "";
                var stat = "";
                
                var searchArray = "";
                if(searchArray == ""){searchArray = "all";}
                if(dept == ""){dept = "all";}
                if(stat == ""){stat = "all";}
                
                window.location="<?=base_url()?>index.php/ca_suj/exportSujList";   
            });
        })
</script>