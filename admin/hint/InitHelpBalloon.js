
function initHint () {
    var allElements = document.getElementsByClassName("hint");
    for (var i = 0; (element = allElements[i]) != null; i++) {
        var elementId = element.id;
        var elementTitle = element.title.split(":");
        element.title = '';
        new HelpBalloon({
            title: elementTitle[0],
            content: elementTitle[1],
            icon: $(elementId),
            useEvent: ['mouseover']
        });
    }
}

window.onload = initHint;

