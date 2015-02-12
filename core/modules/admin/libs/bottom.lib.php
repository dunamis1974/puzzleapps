<?php

if (count($_GET) != 0) $_BODY .= "</td></tr></table>";
$_BODY .= "</td></tr>\n";
if (count($_GET["do"]) == 0)
    $_BODY .= "<tr><td height=\"20\" valign=\"middle\"><br /><div align=\"center\" class=\"remoteBottom\">" . LangControls() . "</div></td></tr>";

$_BODY .= "\n</table>\n";

if ($HTMLIAREANIT) $_BODY .= "<script type=\"text/javascript\">" . $HTMLIAREANIT . "</script>";

$_BODY .= "
</body>
</html>
";

?>