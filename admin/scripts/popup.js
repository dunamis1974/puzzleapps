function popup(url, w, h) {
  window.open(url, '', 'width='+w+',height='+h+',menubar=no,scrollbars=yes,toolbars=no,resizable=yes')
}

function popupnormal(url, w, h) {
  window.open(url, '', 'width='+w+',height='+h+',location,status,menubar=yes,scrollbars=yes,toolbar=yes,resizable=yes,screenX=25,screenY=25')
}