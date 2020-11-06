
// Draggable elements by class name, from their title element
var cardClassName  = "card-block";
// In the newer version of Fordson theme the name of the html element has changed...
cardClassName = "card-body";
var titleClassName = "card-title";
var blockClassName = "block";
var blockList = document.getElementsByClassName(blockClassName);
var mdposterColumnOne = document.querySelector('#mod_mdposter-content > div > div:first-child');
var mdposterColumnTwo = document.querySelector('#mod_mdposter-content > div > div:nth-child(2)');

for ( var i = 0; i < blockList.length; i++){
    var cardElement  = blockList[i].getElementsByClassName(cardClassName) [0];
    var titleElement = blockList[i].getElementsByClassName(titleClassName)[0];
    
    // Resizing
    blockList[i].style.resize = 'both';
    blockList[i].style.overflow = 'auto';
  
    // Enable dragging
    dragElement(cardElement, blockList[i], titleElement);
}

if (mdposterColumnOne && mdposterColumnTwo) {
    // Store initial widths
    mdposterColumnOne.dataset.initialSize = mdposterColumnOne.offsetWidth;
    mdposterColumnTwo.dataset.initialSize = mdposterColumnTwo.offsetWidth;

}



function dragElement(elmnt, block, titleElement) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

  if (titleElement){
    /* if present, the header is where you move the DIV from:*/
    titleElement.onmousedown = dragMouseDown;
  } else {
    /* otherwise, move the DIV from anywhere inside the DIV:*/
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
   // elmnt.style.position = 'relative';
   // block.style.position = "relative";

    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;

    var yint = parseInt($(block).css('top'),  10);
    var xint = parseInt($(block).css('left'), 10);
    block.style.top  = (yint - pos2) + "px";
    block.style.left = (xint - pos1) + "px";
 }

  function closeDragElement() {
    /* stop moving when mouse button is released:*/
    document.onmouseup = null;
    document.onmousemove = null;

    block.style.resize   = 'both';
    block.style.overflow = 'auto';
  }
}



