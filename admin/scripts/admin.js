function ShowHide(id) {
    if (document.getElementById('data_' + id).style.display == "none") {
        document.getElementById('data_' + id).style.display = "";
        document.getElementById('img_' + id).src = './admin/images/16x16/minus.png';
    } else {
        document.getElementById('data_' + id).style.display = "none";
        document.getElementById('img_' + id).src = './admin/images/16x16/plus.png';
    }
}

function initHint () {
    var allElements = document.getElementsByClassName("hint");
    for (var i = 0; (element = allElements[i]) != null; i++) {
        var elementId = element.id;
        var elementTitle = element.title.split(":");
        new HelpBalloon({
            title: elementTitle[0],
            content: elementTitle[1],
            icon: $(elementId),
            useEvent: ['mouseover']
        });
    }
}

window.onload = initHint;