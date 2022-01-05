/**
 * Javascript for post page.
 */

window.onload=function () {
  // Scroll to bottom of chat window on load.
  var objDiv = document.getElementById("q");
//  objDiv.scrollTop = objDiv.scrollHeight;
   objDiv.focus();

   objDiv.setSelectionRange(objDiv.value.length,objDiv.value.length);
}
