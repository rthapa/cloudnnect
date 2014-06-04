<?php
include_once("php_includes/check_login_status.php");
	// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: http://localhost/cloudbaxa/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="Images/favicon.ico">
	<link rel="stylesheet" type="text/css" href="Styles.css">
	<link rel="stylesheet" type="text/css" href="user-styles.css">
	<script src="js/main.js"></script>
	<script src="js/ajax.js"></script>

<script>
function _(el){
	return document.getElementById(el);
}


</script>
</head>
<body>
<?php
	if($log_username != ''){
     include_once("php_includes/template_pageTop.php"); 
	}else{
     include_once("php_includes/template_pageTop_notLogged.php"); 
	}
?>
<div class="uploadWrapper">
<div class="title">
	<h4>Upload</h4>
</div>

<div class="fileName" id="fileName">
	<h5 id="fileNameH5">blabalabla.img</h5>
</div>
<div id="uploadFormWrapper">
<form id="upload_form" enctype="multipart/form-data" method="post" >
  <input type="file" name="file1" id="file1"><br>
  <!--<input type="button" value="Upload File" onclick="uploadFile()"> -->
</form>
</div>

  <div class="progress" id="progress">
    <span class="progress-val" id="progress-val">0%</span>
    <span class="progress-bar"><span class="progress-in" id="progress-in"></span></span>
  </div>

  <div class="uploadDesc">
	<textarea id="about" rows="5" cols="40" name="desc" maxlength="255"  placeholder="File Description.."></textarea>
  </div>

  <div class="uploadKeywords">
	<textarea id="keywords" rows="1" cols="40" name="keywords" maxlength="25"  placeholder="keywords or tags.."></textarea>
  </div>

  <div class="uploadType" name="uploadType">
  	<select id="uploadTypeSel">
	  <option value="a">Public</option>
	  <option value="b">Unlisted</option>
	  <option value="c">Private</option>
	</select>
  </div>

  <div id="submitSection">
  <button onclick="uploadFile()" class="downloadButton">submit</button>
  </div>

  <div class="uploadStatusDiv" id="uploadStatusDiv">
  	  <h3 id="uploadStatus"></h3>
  	  <p id="loaded_n_total"></p>
  </div>
</div>
<script>
		//upload button auto after file has been chosen
		
		document.getElementById("file1").onchange = function() {
			_("fileName").style.display = "none";
			var file = _("file1").files[0];
			fileName = file.name;
			_("fileName").style.display = "block";
			_("fileNameH5").innerHTML = fileName;
		}

		//upload type selector
		/**
		document.getElementById("uploadTypeSel").onchange = function() {
		    //alert(uploadTypeSel.value);
		    if(uploadTypeSel.value == "group"){
		    	document.getElementById("uploadGroupSel").style.display = "block";
		    }else{
		    	document.getElementById("uploadGroupSel").style.display = "none";
		    }
		}
		**/

		//maybe with upload button?
		/**
		document.getElementById("upload-button").onclick = function() {
		    uploadFile();
		} */
</script>
</body>
</html>