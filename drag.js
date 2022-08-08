// Make the DIV element draggable

var elmnt;
var elmntHeader;
var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
var offsetLeft = 0, offsetTop = 0;

window.onload = function() {

  elmnt = document.getElementById('content');
  elmntHeader = document.getElementById('contentheader');
  dragElement(elmnt);

  function dragElement(elmnt) {
    if (elmntHeader) {
      // if present, the header is where you move the DIV from:
      elmntHeader.onmousedown = dragMouseDown;
    } else {
      // otherwise, move the DIV from anywhere inside the DIV:
      elmnt.onmousedown = dragMouseDown;
    }
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    offsetLeft = elmnt.offsetLeft;
    offsetTop = elmnt.offsetTop;
    // call a function whenever the cursor moves:
    document.onmouseup = closeDragElement;
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    // set the element's new position:
    elmnt.style.left = (offsetLeft - pos1) +  "px";
    elmnt.style.top = (offsetTop - pos2) + "px";
  }

  function closeDragElement() {
    // stop moving when mouse button is released:
    document.onmouseup = null;
    document.onmousemove = null;
  }
}
