<table align="center" width="300" cellspacing="0" cellpadding="3" class="">
<tr>
    <td class="item"><b>Platform is created</b></td>
    <td align="left"><?= ((createPlatform())?$okImg:$failedImg); ?></td>
</tr>
<tr>
    <td class="item"><b>Platform configuration is saved</b></td>
    <td align="left"><?= ((saveConfPlatform())?$okImg:$failedImg); ?></td>
</tr>
</table>
<br /><br />
<table align="center" width="300" cellspacing="0" cellpadding="3" class="">
<tr>
    <td class="title" colspan="2">Create Super User</td>
</tr>
<tr>
    <td class="" colspan="2">
    Please enter correct information to continue.<br />
    </td>
</tr>
</table>
<table align="center" width="300" cellspacing="0" cellpadding="3" class="">
<?= $TRANSLATE->Go($doFORM->start("person")); ?>
</table>

<?

function createPlatform () {
    global $UFV, $PLATFORMID, $error;
    if (!$PLATFORMID) {
        $VALIDATED = $UFV->validate($_POST, "platform");
        if (!$UFV->hasErrors()) {
            if (PLATFORM::CreatePlatform($VALIDATED)) return true;
        }
        $error = true;
        return false;
    }
    return true;
}

function saveConfPlatform () {
    global $error, $UFV;
    $_PLCONF = "../config/conf.d/platform.conf.php";
    $VALIDATED = $UFV->validate($_POST, "platform");
    if ((!$PLATFORMNAME && $VALIDATED["name"] != "") || ($PLATFORMNAME != $VALIDATED["name"])) {
        if ((!$UFV->hasErrors()) && ($fp = fopen($_PLCONF, "w"))) {
            $_DATA .= "<?\n\n";
            $_DATA .= "\$PLATFORMNAME = \"" . $VALIDATED["name"] . "\";\n";

            $_DATA .= "\n?>";
            fwrite($fp, $_DATA);
            fclose ($fp);
            return true;
        }
        $error = true;
        return false;
    } else {
        return true;
    }

}

?>