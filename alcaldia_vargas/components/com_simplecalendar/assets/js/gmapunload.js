// BEGIN NEW CODE TO AVOID USING BODY TAG FOR LOAD/UNLOAD
// http://www.designaesthetic.com/2007/07/06/how-to-load-and-unload-google-maps-without-using-the-body-tag/

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    };
  }
}

//addLoadEvent(GMapsOverlay.init);

// arrange for our onunload handler to 'listen' for onunload events
if (window.attachEvent) {
        window.attachEvent("onunload", function() {
                GUnload();      // Internet Explorer
        });
} else {
        window.addEventListener("unload", function() {
                GUnload(); // Firefox and standard browsers
        }, false);
}

// END NEW CODE TO AVOID USING BODY TAG FOR LOAD/UNLOAD