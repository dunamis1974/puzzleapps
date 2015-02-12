
document.writeln('<div id="contextmenu__" class="context__" onMouseover="highlight__(event)" onMouseout="lowlight__(event)" onClick="jumpto__(event)" style="display: none; position: absolute; top: 0px; left: 0px; z-index: 10000">.</div>');

if (typeof lang == "undefined") {
    document.writeln('<script src="./admin/scripts/lang/en.lang.js" type="text/javascript"></script>');
}

var systemimages = './admin/images/';
var sysindex = './index.php';

var ie = (document.all && document.getElementById)? true : false;
var ns = (document.getElementById && !document.all)? true : false;

if (ie || ns) var menuobj = document.getElementById("contextmenu__");

function showobject__(e) {
    var elm = ie? event.srcElement : e.target;
    while (elm) {
        if (elm.nodeName == 'BODY' || elm.nodeName == 'HTML') break;
        if (elm.getAttribute("edit")) break;
        elm = elm.parentNode;
    }
    if (!elm.getAttribute("edit")) return;
    oldClass = elm.className;
    oid = elm.getAttribute("edit");
    if (oid == "zone")
        elm.className = oldClass + " highlite_zone__";
    else
        elm.className = oldClass + " highlite__";
}

function hideobject__(e) {
    var elm = ie? event.srcElement : e.target;
    while (elm) {
        if (elm.nodeName == 'BODY' || elm.nodeName == 'HTML') break;
        if (elm.getAttribute("edit")) break;
        elm = elm.parentNode;
    }
    if (!elm.getAttribute("edit")) return;
    elm.className = oldClass;
}

function showmenu__ (e) {
    var elm = ie? event.srcElement : e.target;
    while (elm) {
        if (elm.nodeName == 'BODY' || elm.nodeName == 'HTML') break;
        if (elm.getAttribute("edit")) break;
        elm = elm.parentNode;
    }

    if (!elm.getAttribute("edit")) return false;

    ohidden = elm.getAttribute("hidden");
    otype = elm.getAttribute("type");
    oactions = elm.getAttribute("actions");
    oparent = elm.getAttribute("parent");
    ozone = elm.getAttribute("zone");
    oid = elm.getAttribute("edit");

    hhidden = ohidden?" # <i>" + lang["hidden"] + "</i>" : "";

    menucontent = selectcontent__(otype, oid, oactions, oparent, ozone);
    document.getElementById("contextmenu__").innerHTML = "<div class='contexthead__'><b> " + lang["id"] + ": " + oid + " : " + ozone + "</b>" + hhidden + "</div>" + menucontent;


    var rightedge = ie? document.body.clientWidth - event.clientX : window.innerWidth - e.clientX;
    var bottomedge = ie? document.body.clientHeight - event.clientY : window.innerHeight - e.clientY;

    if (rightedge < menuobj.offsetWidth) {
        xx = (ie? document.body.scrollLeft + event.clientX - menuobj.offsetWidth : window.pageXOffset + e.clientX - menuobj.offsetWidth) + "px";
        menuobj.style.left = xx;
    } else {
        xx = (ie? document.body.scrollLeft + event.clientX : window.pageXOffset + e.clientX) + "px";
        menuobj.style.left = xx;
    }

    if (bottomedge < menuobj.offsetHeight) {
        yy = (ie? document.body.scrollTop + event.clientY-menuobj.offsetHeight : window.pageYOffset + e.clientY - menuobj.offsetHeight) + "px";
        menuobj.style.top = yy;
    } else {
        yy = (ie? document.body.scrollTop + event.clientY : window.pageYOffset + e.clientY) + "px";
        menuobj.style.top = yy;
    }
    
    menuobj.style.visibility = "visible";

    return false;
}

function hidemenu__(e) {
    menuobj.style.visibility = "hidden";
    return;
}

function highlight__(e) {
    var firingobj=ie? event.srcElement : e.target;
    if (firingobj.className == "cmenuitems__" || ns && firingobj.parentNode.className == "cmenuitems__"){
        if (ns && firingobj.parentNode.className == "cmenuitems__") firingobj = firingobj.parentNode;
        firingobj.className = "cmenuitems_hover__";
    }
    return;
}

function lowlight__(e) {
    var firingobj=ie? event.srcElement : e.target
    if (firingobj.className == "cmenuitems_hover__" || ns && firingobj.parentNode.className == "cmenuitems_hover__") {
        if (ns && firingobj.parentNode.className == "cmenuitems_hover__") firingobj = firingobj.parentNode
        firingobj.className = "cmenuitems__";
    }
}

function jumpto__(e) {
    var firingobj=ie? event.srcElement : e.target;
    if (firingobj.className == "cmenuitems_hover__" || ns&&firingobj.parentNode.className == "cmenuitems_hover__") {
        if (ns&&firingobj.parentNode.className == "cmenuitems_hover__") firingobj = firingobj.parentNode;

        otarget = firingobj.getAttribute("go");

        if (otarget) {
            window.open(firingobj.getAttribute("url"), otarget);
        } else {
            window.open(firingobj.getAttribute("url"), '', 'width=780,height=550,menubar=no,scrollbars=yes,toolbars=no,resizable=yes')
        }
    }
}

function selectcontent__(otype, oid, oactions, oparent, ozone) {
    omenu = "";
    c_title = "";
    
    actions = oactions.split("|");

    for (i = 0; i < actions.length; i++) {
        c = actions[i];
        if (oid == "zone") {
            c_title = c;
            if (typeof lang[c] != "undefined")
                c_title = lang[c];
            omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=content&admin[]=pools&do=add&zone=" + ozone + "&odd=" + c + "&parent=" + oparent + "'><img src='" + systemimages + "obj.png' width='11' height='11' hspace='3' align='absmiddle' class='cmenuimage__' />" + lang["add"] + " " + c_title + "</div>";
        } else {
            if (c == "admin") omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=home'  go='_blank'>" + lang["administration"] + "</div>";

            else if (c == "up") omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=content&admin[]=pools&do=up&id=" + oid + "&zone=" + ozone + "&parent=" + oparent + "' go='_self'><img src='" + systemimages + "up.png' border='0' width='11' height='11' hspace='3' align='absmiddle' target='_self' />" + lang["move up"] + "</div>";
            else if (c == "left") omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=content&admin[]=pools&do=up&id=" + oid + "&zone=" + ozone + "&parent=" + oparent + "' go='_self'><img src='" + systemimages + "left.png' border='0' width='11' height='11' hspace='3' align='absmiddle' target='_self' />" + lang["move left"] + "</div>";
            else if (c == "down") omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=content&admin[]=pools&do=down&id=" + oid + "&zone=" + ozone + "&parent=" + oparent + "' go='_self'><img src='" + systemimages + "down.png' border='0' width='11' height='11' hspace='3' align='absmiddle' target='_self' />" + lang["move down"] + "</div>";
            else if (c == "right") omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=content&admin[]=pools&do=down&id=" + oid + "&zone=" + ozone + "&parent=" + oparent + "' go='_self'><img src='" + systemimages + "right.png' border='0' width='11' height='11' hspace='3' align='absmiddle' target='_self' />" + lang["move right"] + "</div>";

            else if (c == "edit") omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=content&admin[]=pools&do=edit&id=" + oid + "&odd=" + otype + "'><img src='" + systemimages + "edit.png' border='0' width='11' height='11' hspace='3' align='absmiddle' />" + lang["edit"] + "</div>";
            else if (c == "del") omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=content&admin[]=pools&do=delete&id=" + oid + "' go='_self'><img src='" + systemimages + "delete.png' border='0' width='11' height='11' hspace='3' align='absmiddle' target='_self' />" + lang["delete"] + "</div>";
            else if (c == "move") omenu = omenu + "<div class='cmenuitems__' url='" + sysindex + "?admin[]=content&admin[]=pools&do=move&id=" + oid + "&parent=" + oparent + "'><img src='" + systemimages + "move.png' border='0' width='11' height='11' hspace='3' align='absmiddle' />" + lang["move object"] + "</div>";

            else if (c == "logout") omenu = omenu + "<div class='cmenuitems__' url='./?do=logout&back=true' go='_self'>" + lang["logout"] + "</div>";
        }
    }

    return omenu;
}

if (ie || ns) {
    menuobj.style.display='';
    document.oncontextmenu=showmenu__;
    document.onclick=hidemenu__;
    document.onmouseover=showobject__;
    document.onmouseout=hideobject__;
}

