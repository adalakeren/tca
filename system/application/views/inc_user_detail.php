<?
if (!isset($_SESSION['sess_userID'])) {

    header('Location: ' . base_url() . 'index.php/admin_core/user_logout');
} else {

?>
<table style="border-spacing: 4px;width: 150px;">
    <tr>
        <td rowspan="4" style="width: 20px"><img src="<?=base_url()?>includes/images/user-silhouette.png" alt=""/></td>
        <td style="text-align: left">Welcome, <br/><b><?=$_SESSION['sess_userName']?></b></td>
    </tr>
    <tr>
        <td style="text-align: left">(<?=$_SESSION['sess_deptName']?>)</td>
    </tr>
    <tr>
        <td style="text-align: left;padding-top: 10px">Level status : <b><?=$_SESSION['sess_userLevDetail']?></b></td>
    </tr>
    <tr>
        <td style="text-align: left;padding-top: 10px">Area Id : <b><?=$_SESSION['area_id']?></b></td>
    </tr>
    <tr>
        <td style="text-align: left">
            <?
                //if($_SESSION['sess_adminOption'] != '0'){echo '<i>Administrator Mode</i>';}else{echo '<i>Easy User Mode</i>';}
            ?>            
            &nbsp;
        </td>
    </tr>
    <tr>
        <td style="text-align: left">
            <?
                //if($_SESSION['sess_adminOption'] != '0'){echo '<a class="linkMenuDefault" href="'.base_url().'index.php/adm_core">->Administrator menu </a>';}
            ?>
            &nbsp;
        </td>
    </tr>
    <tr>
        <td colspan="2"><hr class="hrStyle"/></td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: left;"><a class="linkLogout" href="<?=base_url()?>index.php/admin_core/user_logout"> Logout </a> </td>
    </tr>
</table>
<br/><br/><br/>
<?
}
?>