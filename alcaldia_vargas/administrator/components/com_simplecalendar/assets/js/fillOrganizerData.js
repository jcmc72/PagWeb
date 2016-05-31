    // AJAX request
    // Tutorial: http://www.xul.fr/en-xml-ajax.html

	var myRequest = false;
	var myPath = "";
	var myArray = new Array();
	var confirmText = "Overwrite?";
	
	// http://www.gotknowhow.com/articles/how-to-get-the-base-url-with-javascript
	function fxGetBaseURL() {
	    var url = location.href;  // entire url including querystring - also: window.location.href;
	    var baseURL = url.substring(0, url.indexOf('/', 14));


	    if (baseURL.indexOf('http://localhost') != -1) {
	        // Base Url for localhost
	        var url = location.href;  // window.location.href;
	        var pathname = location.pathname;  // window.location.pathname;
	        var index1 = url.indexOf(pathname);
	        var index2 = url.indexOf("/", index1 + 1);
	        var baseLocalUrl = url.substr(0, index2);

	        return baseLocalUrl + "/";
	    }
	    else {
	        // Root Url for domain name
	        return baseURL + "/";
	    }

	}
	
	function fillOrganizerData() {
		var e = document.getElementById("jform_organizer_id");
		var id = e.options[e.selectedIndex].value;
		if ( id != 0 ) {
			var isOk = confirm("" + confirmText + "");

			if(isOk) {
				 myRequest = false;
			     if (window.XMLHttpRequest) {
			          myRequest = new XMLHttpRequest();
			          if (myRequest.overrideMimeType) {
			               myRequest.overrideMimeType("text/plain");
			          }
			     } else if (window.ActiveXObject) {
			          try {
			               myRequest = new
			                    ActiveXObject("Msxml2.XMLHTTP");
			          } catch (e) {
			               try {
			                    myRequest = new
			                         ActiveXObject("Microsoft.XMLHTTP");
			               } catch (e) {}
			          }
			     }
			     if (!myRequest) {
			          alert("Error: Cannot create XMLHTTP object");
			          return false;
			     }
			     
			     myRequest.onreadystatechange = displayReturn;
			     
			     myRequest.open("GET", "index.php?option=com_simplecalendar&view=organizer&task=edit&id="+ id +"&format=raw&" + token + "=1", "false");
			     
			     myRequest.send(null);
			}
		}
	}
	
	function displayReturn() {
	    if (myRequest.readyState == 4) {
	          if (myRequest.status == 200) {
	               var httpResponse = myRequest.responseText
	               var myArray = new Array();
	               myArray = httpResponse.split("<br>");
	               document.getElementById("jform_contact_name").value = myArray["0"];
	               document.getElementById("jform_contact_email").value = myArray["1"];
	               document.getElementById("jform_contact_website").value = myArray["2"];
	               document.getElementById("jform_contact_telephone").value = myArray["3"];
	               document.getElementById("jform_latlon").value = myArray["4"];
	               document.getElementById("jform_address").value = myArray["5"];
	          } else {
	               alert("There was a problem with the request. Status code: " + myRequest.status);
	          }
    	 }
	}
	
	