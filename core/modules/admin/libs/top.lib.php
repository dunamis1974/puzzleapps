<?php

$MENU__ = BuildJSMenu();

$_BODY = "
<html>
<head>
<title>##Administration## | " . $CURRENTPLATFORM->descrption . "</title>
<meta http-equiv=\"Pragma\" content=\"no-cache\">
<meta http-equiv=\"Cache-Control\" content=\"no-cache\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" . $CURRENTENCODING . "\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"./admin/skins/{$GLOBALS["_SKIN"]}/admin.css\" media=\"screen\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"./admin/skins/{$GLOBALS["_SKIN"]}/print.css\" media=\"print\"/>
<script type=\"text/javascript\" src=\"./admin/hint/prototype/prototype.js\"></script>
<script type=\"text/javascript\" src=\"./admin/hint/scriptaculous/scriptaculous.js\"></script>
<script type=\"text/javascript\" src=\"./admin/hint/HelpBalloon.js\"></script>
<script type=\"text/javascript\" src=\"./admin/scripts/popup.js\"></script>
<script type=\"text/javascript\" src=\"./admin/scripts/admin.js\"></script>
<script type=\"text/javascript\" src=\"./admin/scripts/JSCookMenu.js\"></script>
<link rel=\"stylesheet\" href=\"./admin/scripts/ThemeOffice/theme.css\" type=\"text/css\">
<script language=\"JavaScript\" src=\"./admin/scripts/ThemeOffice/theme.js\"></script>
<script language=\"JavaScript\"><!--
var adminMenu =
[
" . $MENU__ . "
];
-->
</script>
</head>
<body leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
";

if (count($_GET) != 0) {
    $_BODY .= "<table width=\"100%\" height=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
    <tr>
        <td valign=\"top\" height=\"49\">
        <div id=\"adminMenuID\" class=\"remoteMenu\"></div>
        <script language=\"JavaScript\">
        <!--
        cmDraw ('adminMenuID', adminMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
        -->
        </script>
        <div class=\"remotePathStart\"><img src=\"" . GetIcon() . "\" align=\"absmiddle\" hspace=\"2\" /> <a href=\"index.php?admin[]=home\">##Home##</a> &gt; " . DoPath() . "</div>
        </td></tr><tr><td height=\"*\" valign=\"top\">
        <table width=\"100%\" height=\"100%\" cellspacing=\"10\" cellpadding=\"10\" border=\"0\"><tr>
            <td valign=\"top\">
        ";

} else {
    $_BODY .= "
    <table width=\"100%\" height=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
    <tr>
        <td valign=\"top\" height=\"19\">
        <div id=\"adminMenuID\" class=\"remoteMenu\"></div>
        <script language=\"JavaScript\">
        <!--
        cmDraw ('adminMenuID', adminMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
        -->
        </script>
        </td></tr><tr><td height=\"*\" valign=\"top\">
        ";


}

?>