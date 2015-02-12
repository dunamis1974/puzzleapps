vis="";
invis="none";

function expand(divid) {
	document.getElementById("closed_"+divid).style.display = invis;
	document.getElementById("open_"+divid).style.display = vis;
}

function collapse(divid) {
	document.getElementById("open_"+divid).style.display = invis;
	document.getElementById("closed_"+divid).style.display = vis;
}

function DisplayData(divid) {
	hide_div();
	document.getElementById("data_"+divid).style.display = vis;
}

function hide_div() {
	for(var i=0; i < document.getElementsByTagName("div").length; i++) {
		var idstr = document.getElementsByTagName("div")[i].getAttribute("id");
		if ((idstr != "") && (idstr.indexOf("data_") >= 0)) { 
			document.getElementsByTagName("div")[i].style.display = invis; 
		}
	}
}
