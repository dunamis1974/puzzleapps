var inpreparation=false;
var inpreparation_sub=false;


var isIE = false, isNS = false, isDOM = false, isNS4 = false;
var vis = "", invis = "";
if (document.all) {isIE = true; vis="visible";invis="hidden";}
if (document.layers){isNS = true; isNS4=true; vis="show";invis="hide";}
if (document.getElementById && !isIE) {isDOM=true; invis ="hidden"; vis = "visible"}

function show_div(who) {
  inpreparation = false;
  hide_div();
  if (isDOM) {
    document.getElementById(who).style.visibility=vis; 
  } else {
    if(isIE) { 
      document.getElementById(who).style.visibility=vis; 
    } else { 
      eval('document.layers.'+who+'.visibility=\''+vis+'\'');
    }
  }
}

function hide_div() {
  if (isDOM) {
    for(var i=0; i<document.getElementsByTagName("div").length; i++) {
      var idstr = document.getElementsByTagName("div")[i].getAttribute("id");
      if (idstr != "" && idstr != null && (idstr.indexOf("Ltop_") >= 0 || idstr.indexOf("Lleft_") >= 0)) { 
        document.getElementById(idstr).style.visibility=invis; 
      }
    }	
  } else {
    if(isIE) {
      for(var i=0; i<document.getElementsByTagName("div").length; i++) {
        var idstr = document.getElementsByTagName("div")[i].getAttribute("id");
        if ((idstr != "") && (idstr.indexOf("Ltop_") >= 0 || idstr.indexOf("Lleft_") >= 0)) { 
          document.getElementsByTagName("div")[i].style.visibility = invis; 
        }
      }	
    } else {
      for(var i=0; i<document.layers.length; i++) { 
        if (document.layers[i].id.indexOf("new_")==-1) { 
          document.layers[i].visibility = invis; 
        }
      }
    }
  }
}

if (navigator.appName != "Netscape") {
 document.onmouseup = hide_div;
} else {
 window.captureEvents(Event.MOUSEUP);
 window.onmouseup = hide_div;
}

function cpre() {
  inpreparation = false;
  cpre_sub();
}

function prepared_toclose() {
  if (inpreparation) { hide_div(); }
}

function preparetoclose() {
  inpreparation=true;
  setTimeout(prepared_toclose, 2000);
}


function show_sub(who) {
  inpreparation_sub = false;
  hide_sub();
  if (isDOM) {  
    document.getElementById(who).style.visibility=vis; 
  } else {
    if(isIE) { 
      document.getElementById(who).style.visibility=vis; 
    } else { 
      eval('document.layers.'+who+'.visibility=\''+vis+'\'');
    }
  }
}

function hide_sub() {
  if (isDOM) {
    for(var i=0; i<document.getElementsByTagName("div").length; i++) {
      var idstr = document.getElementsByTagName("div")[i].getAttribute("id");
      if (idstr != "" && idstr != null && (idstr.indexOf("Lsub_") >= 0)) { 
        document.getElementById(idstr).style.visibility=invis; 
      }
    }	
  } else {
    if(isIE) {
      for(var i=0; i<document.getElementsByTagName("div").length; i++) {
        var idstr = document.getElementsByTagName("div")[i].getAttribute("id");
        if ((idstr != "") && (idstr.indexOf("Lsub_") >= 0)) { 
          document.getElementsByTagName("div")[i].style.visibility = invis; 
        }
      }	
    } else {
      for(var i=0; i<document.layers.length; i++) { 
        if (document.layers[i].id.indexOf("new_")==-1) { 
          document.layers[i].visibility = invis; 
        }
      }
    }
  }
}



if (navigator.appName != "Netscape") {
 document.onmouseup = hide_sub;
} else {
 window.captureEvents(Event.MOUSEUP);
 window.onmouseup = hide_sub;
}

function cpre_sub() {
  inpreparation_sub = false;
}

function prepared_toclose_sub() {
  if (inpreparation_sub) { hide_sub(); }
}

function preparetoclose_sub() {
  inpreparation_sub=true;
  setTimeout(prepared_toclose_sub, 1000);
}

