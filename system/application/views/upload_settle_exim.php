<?php echo $error;?>
<!-- <?php echo form_open_multipart('ca_upload/do_upload_settle_exim');?> -->
<?php echo form_open_multipart('ca_upload/do_upload_settle_exim_2');?>
<?
if($dataExcel > 10)
    {
    echo "<script language='javascript'>";
    echo "alert('Maximum allowed 10 data!');";
    echo "</script>";
    }
?>

	<input type="file" name="userfileExim" size="20" />
	<br /><br />
	<input type="submit" value="upload" />
