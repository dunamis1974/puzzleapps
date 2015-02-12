<table align="center" width="300" cellspacing="0" cellpadding="3" class="">
<tr>
    <td class="item"><b>SuperUser is created</b></td>
    <td align="left"><pre><?= ((createSuperUser())?$okImg:$failedImg); ?></td>
</tr>
<tr>
    <td class="" colspan="2" align="center"><br /><br />
    Click next to enter admin interface.<br /><br /><br />
    </td>
</tr>
</table>
</form>
<form action="../admin" method="GET">

<?
function createSuperUser() {
    global $error, $UFV;

    //$_POST["_objectname"] = "person";
    $VALIDATED = $UFV->validate($_POST, "person");
    if (!$UFV->hasErrors()) {
        $O = new CORE($VALIDATED);
        $O->insert();
        $P = new PERSON($O->id);
        $P->_update_person();
        $P->MakeItSU();
        return true;
    }
    $error = true;
    return false;
}

?>