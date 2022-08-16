/**
 * Javascript for search page.
 */

window.addEventListener("load",function(event) {

  // Focus and select search bar on load.
  var objDiv = document.getElementById("q");
  objDiv.focus();
  objDiv.setSelectionRange(objDiv.value.length,objDiv.value.length);

},false);
