

		<?php echo $error;?>
		<?php echo form_open_multipart('ca_upload/do_upload_request');?>
			Request Type : <?=$cmbCashType?><br/><br/>
			<input type="file" class="input" name="userfile" size="20" />
			<br /><br />
			<input type="submit" value="upload" />

