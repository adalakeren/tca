<?php echo $error;?>
<?php echo form_open_multipart('ca_upload/do_upload_settle');?>
<?
if($dataExcel > 10)
    {
    echo "<script language='javascript'>";
    echo "alert('Maximum allowed 10 data!');";
    echo "</script>";
    }
?>

	<input type="file" name="userfile" size="20" />
	<br /><br />
	<input type="submit" value="upload" />
