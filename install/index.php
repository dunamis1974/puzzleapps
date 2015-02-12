<?
$step = ($_GET["s"])?$_GET["s"]:1;
$failedImg = "<img src=\"images/fix.png\" />";
$okImg = "<img src=\"images/ok.png\" />";

// Load Puzzle API
$_DOINSTALL = true;
$_IGNORE_SEC = true;
include_once("../core/loader.php");
?>
<html>
<head>
  <title>Installation - Puzzle Apps CMS</title>
  <meta name="AUTHOR" content="DuNaMiS">
  <link rel="stylesheet" type="text/css" href="./css/style.css"/>
</head>
<body bgcolor="#E6EAE6" leftmargin="0" topmargin="10" marginwidth="0" marginheight="10">
<table class="main_table" cellpadding="0" cellspacing="0" align="center">
<tr><td>
<img src="images/logo.jpg" />
</td></tr>
<tr><td class="steps" valign="middle" align="center">
Step <?= $step; ?> of 6
</td></tr>
<tr><td valign="top" bgcolor="White" align="center">
<form action="index.php?s=<?= ($step + 1); ?>" method="POST">
<table><tr><td>
<br />
<?

include("./steps/step_{$step}.php");

?>
</td></tr></table>
<?
if ($error == true) {
    $add_button = "disabled=\"true\"";
    echo "<center><img src=\"./images/messagebox_critical.png\" hspace=\"4\" vspace=\"4\" align=\"middle\"> Fix above errors before continue!</center>";
}

?>
<br /><br />
<hr noshade="true" size="1" />
<input type="submit" value="Next >>" <?= $add_button; ?> />
</form><br />
</td></tr>
</table>
<p /><div class="copyright" align="center">(c) 2004 Puzzle Apps CMS</div>
</body>
</html>
