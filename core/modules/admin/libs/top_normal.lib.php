<?php

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
</head>
<body leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">
<div class=\"remotePathNormal\"><img src=\"" . GetIcon() . "\" align=\"middle\" hspace=\"2\" />// " . DoPath() . "</div>
<table width=\"100%\" cellspacing=\"3\" cellpadding=\"3\">
  <tr><td nowrap valign=\"top\"><br /><br /></td></tr>
  <tr>
    <td nowrap valign=\"top\">
      <table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\"><tr>
        <td valign=\"top\"><p />
";

?>