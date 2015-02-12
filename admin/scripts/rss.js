var arrTitle = new Array(), arrDesc = new Array(), arrLink = new Array();

function loadXML(url, container, last) {
    if (document.implementation && document.implementation.createDocument) {
        var xmldoc = document.implementation.createDocument("", "", null);
        xmldoc.onload = function(  ) { formatRSS(xmldoc, container, last); }
        xmldoc.load(url);
    } else if (window.ActiveXObject) { 
		var xmldoc = new ActiveXObject("Microsoft.XMLDOM");   
		xmldoc.onreadystatechange = function(  ) {
			if (xmldoc.readyState == 4) formatRSS(xmldoc, container, last);
		}
	     xmldoc.load(url);                                  
    }
}

function formatRSS(xmldoc, container, last) {
	var items = xmldoc.getElementsByTagName("item");
	for(var i = 0; i < items.length; i++) {
	    var e = items[i];
		arrTitle[i] = e.getElementsByTagName("title")[0].firstChild.data;
		arrDesc[i] = e.getElementsByTagName("description")[0].firstChild.data;
		arrLink[i] = e.getElementsByTagName("link")[0].firstChild.data;
    }
	showNewsReel(container, last);
}

function showNewsReel(container, last) {
	var strNewsReel = "" ;
	for(var idx = 0; idx < arrTitle.length; idx++ ) {
		if (last && (idx == last)) break;
		strNewsReel = strNewsReel + '<div><a target="_blank" href="' + arrLink[idx] + '">' + arrTitle[idx] + '</a></div><br />';
	}
	container.innerHTML = strNewsReel;
}

function showFeed(url, container, last) {
	loadXML(url, container, last);
}
