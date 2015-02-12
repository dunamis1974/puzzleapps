<?php

$_BODY .= "
<script type=\"text/javascript\" src=\"./admin/scripts/sliding_panels.js\"></script>
<script type=\"text/javascript\">

</script>
<div>
   	<div id=\"sidebarHeader0\" class=\"sidebar_header\" onclick=\"runAnimation(0)\">&nbsp;&nbsp;##Welcome##</div>
   	<div id=\"sidePanel0\" class=\"sidebar_content\" style=\"width:400px; height:1px; top:-1px; left:0px;\"><div style=\"padding:5px;\">
    ##Hello## " . $CURRENTUSER->username . ",<br />
    ##welcome to administration console of## ##PAS <i>(Puzzle Apps Application Server)</i>##.<br /><br />
    <b>##Platform##:</b> " . $CURRENTPLATFORM->descrption . "<br />
    <b>##Version##:</b> " . $GLOBALS["_PAS_CORE_VERSION"] . "<br />
    <b>##Host##:</b> " . $_SERVER["HTTP_HOST"] . "<br />
    <b>##Your IP##:</b> " . $_SERVER["REMOTE_ADDR"] . "<br />
    <b>##Your Browser##:</b> " . $_SERVER["HTTP_USER_AGENT"] . "<br />
    </div></div>
   	<script type=\"text/javascript\">
   		barStatus[0] = false;
		animationObject[0] = new AnimationObject('sidePanel0');
		animationObject[0].AddFrame(new AnimationFrame(0, -1, 400, size, 100));
		runAnimation(0);
	</script>
</div>

<div>
   	<div id=\"sidebarHeader2\" class=\"sidebar_header\" onclick=\"runAnimation(2)\">&nbsp;&nbsp;##News##</div>
   	<div id=\"sidePanel2\" class=\"sidebar_content\" style=\"width:400px; height:1px; top:-1px; left:0px;\"><div style=\"padding:5px;\">
     " . isUpdatable() . "<br /><br />
     " . isUpdatableComponents() . "
   	</div>
   	
   	<script type=\"text/javascript\">
   		barStatus[2] = false;
		animationObject[2] = new AnimationObject('sidePanel2');
		animationObject[2].AddFrame(new AnimationFrame(0, -1, 400, size, 100));
	</script>
</div>
";

/*
<div>
   	<div id=\"sidebarHeader1\" class=\"sidebar_header\" onclick=\"runAnimation(1)\">&nbsp;&nbsp;##Last changes##</div>
   	<div id=\"sidePanel1\" class=\"sidebar_content\" style=\"width:400px; height:1px; top:-1px; left:0px;\"><div style=\"padding:5px;\">
     Last changes made in the system i.e. edited texts/categories/news etc. (with history)
   	</div></div>
   	<script type=\"text/javascript\">
   		barStatus[1] = false;
		animationObject[1] = new AnimationObject('sidePanel1');
		animationObject[1].AddFrame(new AnimationFrame(0, -1, 400, size, 100));
	</script>
</div>
*/

?>