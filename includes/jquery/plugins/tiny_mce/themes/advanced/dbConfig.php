<?php
/*
CREATE TABLE `TinyMCEUploadTutorial`.`imgTable` (
`ImagesID` INT( 255 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`FileName` VARCHAR( 255 ) NOT NULL ,
`Title` TEXT NOT NULL
) ENGINE = MYISAM ;
 */
mysql_connect('localhost','root','');
mysql_select_db('TinyMCEUploadTutorial');

$folderSavePath = 'J:\xampp\htdocs\tinyMCEPluginTutorial\upload';
?>
