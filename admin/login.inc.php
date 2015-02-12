<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $GLOBALS["CURRENTENCODING"]; ?>">
    <title><?= $GLOBALS["CURRENTPLATFORM"]->descrption; ?> - CMS Administration</title>
    <link rel="stylesheet" type="text/css" href="./skins/<?= $GLOBALS["_SKIN"]; ?>/login.css" />
    <script src="./skins/<?= $GLOBALS["_SKIN"]; ?>/login.js" type="text/javascript"></script>
</head>
<body onLoad="document.loginform.email.focus()">
<div class="head"><img src="./skins/<?= $GLOBALS["_SKIN"]; ?>/images/logo.jpg" /></div>
<?php



if ($CURRENTUSER->_loggedin) {
    $msg = "##You do not have sufficient permissions to access this area!##<br /><a href=\"{$_SYSINDEX}?do=logout\">##Click here to logout##</a>";
} else if ($_SESSION["LOGIN_ERROR"] && $_SESSION["LOGIN_ERROR_MSG"]) {
    $msg = "##Access denied!##<br />##{$_SESSION["LOGIN_ERROR_MSG"]}##</a>";
    unset($_SESSION["LOGIN_ERROR"], $_SESSION["LOGIN_ERROR_MSG"]);
} else if ($_SESSION["LOGIN_ERROR"]) {
    unset($_SESSION["LOGIN_ERROR"]);
    $msg = "##Access denied!##<br />##Wrong username/password.##</a>";
}

if ($msg) {
    echo "<div class=\"block_note_\"><span class=\"note__\">" . $TRANSLATE->Go($msg) . "</span></div><p />";
}

?>
<div class="content">
    <div class="t"><div class="b"><div class="l"><div class="r"><div class="bl"><div class="br"><div class="tl"><div class="tr"><br />
        <form action="" method="post" name="loginform">
            <input type="hidden" name="do" value="login" />
            <div align="center" class="login" style="white-space: nowrap;"><?php echo $TRANSLATE->Go("##Please provide your login information:##"); ?></div>
            <table align="center" border="0" bgcolor="#ffffff">
                <tr><td align="right" class="field_name"><?php echo $TRANSLATE->Go("##Username:##"); ?></td><td align="left"><input type="text" name="email" size="15" class="val" id="username" /></td></tr>
                <tr><td align="right"class="field_name"><?php echo $TRANSLATE->Go("##Password:##"); ?></td><td align="left"><input type="password" name="password" size="15" class="val" id="password" /></td></tr>
                <?php
                if ($GLOBALS["_CAPTCHA"]) {
                    global $_SECIMG, $_SECFIELD;
                    $_SECURE = Modules::LoadModule("securimage");
                ?>
                <tr><td align="right"class="field_name"><?php echo $TRANSLATE->Go("##Security Code:##"); ?></td><td><?php echo $_SECIMG; ?></td></tr>
                <tr><td align="right"class="field_name"><?php echo $TRANSLATE->Go("##Verify Code:##"); ?></td><td align="left"><?php echo $_SECFIELD; ?></td></tr>
                <?php 
                }
                ?>
                <tr><td colspan="2" align="center"><input type="submit" value="<?php echo $TRANSLATE->Go("##Login##"); ?>" class="button" /> <input type="button" value="<?php echo $TRANSLATE->Go("##Cancel##"); ?>" class="button" onclick="history.go(-1);"  /></td></tr>
            </table>
        </form>
    </div></div></div></div></div></div></div></div> 
</div>
</body>
</html>