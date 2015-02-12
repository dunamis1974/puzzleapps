<?php

function build_seccode ($name, $data) {
    $_SECIMG = "<img src=\"./admin/securimage/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>\" id=\"__secimg\" /><br />";

    $_SECIMG .= "<input type=\"text\" name=\"{$name}\" size=\"5\" class=\"input\" id=\"{$name}\" />";
    $_SECIMG .= "<a href=\"./admin/securimage/securimage_play.php\"><img src=\"./admin/images/16x16/sound.png\" border=\"0\" align=\"top\" hspace=\"3\" /></a>";
    $_SECIMG .= " <a href=\"#\" onclick=\"document.getElementById('__secimg').src = './admin/securimage/securimage_show.php?sid=' + Math.random(); return false;\"><img src=\"./admin/images/16x16/reload.png\" border=\"0\" align=\"top\" /></a>";
    $_SECIMG .= "<br />";
    return $_SECIMG;
}

function validate_seccode ($value) {
    global $RUNNINGNDIR;
    
    include_once($RUNNINGNDIR . "/admin/securimage/securimage.php");
    echo $RUNNINGNDIR . "/admin/securimage/securimage.php";
    $img = new Securimage();

    $valid = $img->check($value);
    
    if($valid != true) {
        return 12;
    }
    
    return 0;
}

?>