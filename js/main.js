function _(x){
	return document.getElementById(x);
}

function toggleElement(x){
	var x = _(x);
	if(x.style.display == 'block'){
		x.style.display = 'none';
	}else{
		x.style.display = 'block';
	}
}

function checkUsernameInGroup(){
			// _("addButtonDiv").style.display= 'none';
			var u = _("username-addMem").value;
			if(u != ""){
				_("usernameStatusGroup").innerHTML = 'checking ...';
				var ajax = ajaxObj("POST", "php_parsers/addToGroup_system.php");
		        ajax.onreadystatechange = function() {
			        if(ajaxReturn(ajax) == true) {
			        	var dataArray = ajax.responseText.split("|");
			        	if(dataArray[0] == "user_not_found"){
			        		 _("usernameStatusGroup").innerHTML = "<h5>Username not found</h5>";
			           }else if(dataArray[0] == "user_ok"){
			           		 _("usernameStatusGroup").innerHTML = "<h5>Username found</h5>";
			           		// _("span-username").innerHTML = dataArray[1];
			           		// _("addButtonDiv").style.display= 'block';
			           }else{
			           	alert(ajax.responseText);
			           }
			        }
		        }
		        ajax.send("userNameCheckInGroup="+u);
			}
}

function addMemberInGroup(gid){
	var u = _("username-addMem").value;
	if(u != ""){
		_("usernameStatusGroup").innerHTML = 'Please wait ...';
		var ajax = ajaxObj("POST", "php_parsers/addToGroup_system.php");
		ajax.onreadystatechange = function() {
			if(ajax.responseText == "user_not_found"){
				_("usernameStatusGroup").innerHTML = "<h5>Username not found</h5>";
			}else if(ajax.responseText == "user_added"){
				_("usernameStatusGroup").innerHTML = "<h5>Successfully invited to the group</h5>";	
			}else{
				_("usernameStatusGroup").innerHTML = "<h5>"+ajax.responseText+"</h5>";	
			}
		}
		ajax.send("addUserToGroup="+u+"&gId="+gid);
	}else{
		_("usernameStatusGroup").innerHTML = "<h5>Please fill the username first</h5>";
	}
}
/**
function likeToggle(type,fileId,elem){
	var ajax = ajaxObj("POST", "php_parsers/likeDislike_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "like_success"){
				_(elem).innerHTML = '<button onclick="likeToggle()" class="likeButton">Liked</button>';
				_(disklikeBtnSpan).innerHtml = '<button onclick="likeToggle()" class="likeButton" style="margin-left: 10px;">Dislike</button>';
				alert('like success');
			} else if(ajax.responseText == "unlike_success"){
				_(elem).innerHTML = '<button onclick="likeToggle()" class="likeButton" style="margin-left: 10px;">Disliked</button>';
				_(likeBtnSpan).innerHtml = '<button onclick="likeToggle()" class="likeButton">Like</button>';
				alert('dislike success');
			} else if(ajax.responseText=="undo_success") {
				alert('undo');
			}
		}
	}
	ajax.send("type="+type+"&fileId="+fileId);
	}
**/
function likeToggle(type,fileId,elem){
	var ajax = ajaxObj("POST", "php_parsers/likeDislike_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			var dataArray = ajax.responseText.split("|");
			if(dataArray[0] == "like_success"){
				_(elem).innerHTML = '<button onclick="likeToggle(\'like\','+fileId+',\'likeBtnSpan'+fileId+'\')" class="likeButton" id="idLike'+fileId+'">Liked</button>';
				if(_('idDislike'+fileId+'').innerHTML == "Disliked"){
					_('dislikeBtnSpan'+fileId+'').innerHTML = '<button onclick="likeToggle(\'dislike\','+fileId+',\'dislikeBtnSpan'+fileId+'\')" class="likeButton" id="idDislike'+fileId+'" style="margin-left: 10px;">Dislike</button>';
				}
			} else if(dataArray[0] == "unlike_success"){
				_(elem).innerHTML = '<button onclick="likeToggle(\'dislike\','+fileId+',\'dislikeBtnSpan'+fileId+'\')" class="likeButton" id="idDislike'+fileId+'" style="margin-left: 10px;">Disliked</button>';
				if(_('idLike'+fileId+'').innerHTML == "Liked"){
					_('likeBtnSpan'+fileId+'').innerHTML = '<button onclick="likeToggle(\'like\','+fileId+',\'likeBtnSpan'+fileId+'\')" class="likeButton" id="idLike'+fileId+'">Like</button>';
				}
			} else if(dataArray[0] =="undo_success") {
				_('likeBtnSpan'+fileId+'').innerHTML = '<button onclick="likeToggle(\'like\','+fileId+',\'likeBtnSpan'+fileId+'\')" class="likeButton" id="idLike'+fileId+'">Like</button>';
				_('dislikeBtnSpan'+fileId+'').innerHTML = '<button onclick="likeToggle(\'dislike\','+fileId+',\'dislikeBtnSpan'+fileId+'\')" class="likeButton" id="idDislike'+fileId+'" style="margin-left: 10px;">Dislike</button>';
			}
		}
	}
	ajax.send("type="+type+"&fileId="+fileId);
}

function groupReqHandler(action,ugId,elem){
	_(elem).innerHTML = "processing ...";
	var ajax = ajaxObj("POST", "php_parsers/addToGroup_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "accept_ok"){
				_(elem).innerHTML = "<h3>Request Accepted!</h3><br />";
			} else if(ajax.responseText == "reject_ok"){
				_(elem).innerHTML = "<h3>Request Rejected</h3><br />";
			} else {
				_(elem).innerHTML = "<h3>"+ajax.responseText+"</h3>";
			}
		}
	}
	ajax.send("groupReqAction="+action+"&ugId="+ugId);
}

//file upload scripts here !
function uploadFileToGroup(groupid){
	_("groupUploadFormWrapper").style.display = "none";
	_("addButtonDiv-inMember").style.display = "none";
	
	var file = _("file1").files[0];
	//alert(file.name+" | "+file.size+" | "+file.type);

	var uploadType = 'd';
	var groupId = groupid;
	var fileDesc = _("about").value;
	var fileKeywords = _("usernameAddMem").value;
	//check if file is not chosen
	if(document.getElementById("file1").value == "") {
   		_("uploadStatusDiv").style.display = "block";
   		_("uploadStatus").innerHTML = "Please select a file first.";
   		_("groupUploadFormWrapper").style.display = "block";
   		_("addButtonDiv-inMember").style.display = "block";
   		return;
	}
	
	if(_("username-addMem").value.length > 25){
		_("uploadStatusDiv").style.display = "block";
   		_("uploadStatus").innerHTML = "Keywords max limit 25 exceeded.";
   		_("groupUploadFormWrapper").style.display = "block";
   		_("groupUploadFormWrapper").style.display = "block";
   		return;
	}

	var formdata = new FormData();
	formdata.append("file1", file);
	formdata.append("uploadType", uploadType);
	formdata.append("fileDesc", fileDesc);
	formdata.append("fileKeywords", fileKeywords);
	formdata.append("groupId", groupId);

	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "php_parsers/upload_system.php");
	ajax.send(formdata);
	//*/
}
function uploadFile(){
	_("uploadFormWrapper").style.display = "none";
	_("submitSection").style.display = "none";
	var file = _("file1").files[0];
	//alert(file.name+" | "+file.size+" | "+file.type);
	var uploadType = _("uploadTypeSel").value;
	var fileDesc = _("about").value;
	var fileKeywords = _("keywords").value;
	//check if file is not chosen
	if(document.getElementById("file1").value == "") {
   		_("uploadStatusDiv").style.display = "block";
   		_("uploadStatus").innerHTML = "Please select a file first.";
   		_("uploadFormWrapper").style.display = "block";
   			_("submitSection").style.display = "block";
   		exit();
	}

	if(_("keywords").value.length > 25){
		_("uploadStatusDiv").style.display = "block";
   		_("uploadStatus").innerHTML = "Keywords max limit exceeded.";
   		_("uploadFormWrapper").style.display = "block";
   			_("submitSection").style.display = "block";
   		exit();
	}

	var formdata = new FormData();
	formdata.append("file1", file);
	formdata.append("uploadType", uploadType);
	formdata.append("fileDesc", fileDesc);
	formdata.append("fileKeywords", fileKeywords);

	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "php_parsers/upload_system.php");
	ajax.send(formdata);
}
function progressHandler(event){
	_("uploadStatusDiv").style.display = "block";
	_("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
	var percent = (event.loaded / event.total) * 100;
	//_("progressBar").value = Math.round(percent);
	_("progress").style.display = "block";
	_("progress-in").style.display = "block";
	_("progress-in").style.width = Math.round(percent)+'%';
	_("uploadStatus").innerHTML = Math.round(percent)+"% uploaded...";
	_("progress-val").innerHTML = Math.round(percent)+"%";
}
function completeHandler(event){
	_("uploadStatus").innerHTML = event.target.responseText;
	//_("progressBar").value = 0;
	_("progress-in").style.width = 0+'%';
	_("progress-in").style.display = "none";
	_("progress-val").innerHTML = "0%";
	_("uploadFormWrapper").style.display = "block";
	_("submitSection").style.display = "block";
	_("progress").style.display = "none";
	_("about").value = "";
	_("keywords").value = "";
	_("fileName").style.display = "none";
}
function errorHandler(event){
	_("uploadStatus").innerHTML = "Upload Failed";
	_("uploadFormWrapper").style.display = "block";
	_("submitSection").style.display = "block";
	_("progress").style.display = "none";
}
function abortHandler(event){
	_("uploadStatus").innerHTML = "Upload Aborted";
	_("uploadFormWrapper").style.display = "block";
	_("submitSection").style.display = "block";
	_("progress").style.display = "none";
}

function deleteFile(fileId){
	var ajax = ajaxObj("POST", "php_parsers/deleteFile_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "delete_success"){
				alert('deleted');
			} else if(ajax.responseText == "delete_unsuccess"){
				alert('something went wrong');
				//
			}else{
				alert(ajax.responseText);
			}
		}
	}
	ajax.send("fileId="+fileId);
}

//ranks
//color note:
//rookie: #606EBF blue
//contirbutor : #DB8018 brown
//cloudShark: #97C242 green/blue
//cloudPro: #C42D2D red
//legend: 
function changeClassColor(className, colorCode){
	var rankColorClass = document.getElementsByClassName(className);
	for(i=0; i<rankColorClass.length; i++) {
	   	rankColorClass[i].style.color = colorCode;
	 }
}
function checkr(userRank, userId, fromProfile){
	//var rank = _('rank').innerHTML;
	var rank = userRank;
	switch(rank){
		case "Rookie":
			if(fromProfile == 1){
				_('rank').style.color = "#606EBF";
			}
			changeClassColor('rankColorClass'+userId, '#606EBF');
			break;
		case "Contributor":
			if(fromProfile == 1){
				_('rank').style.color = "#DB8018";
			}
			changeClassColor('rankColorClass'+userId, '#DB8018');
			break;
		case "CloudShark":
			if(fromProfile == 1){
				_('rank').style.color = "#97C242";
			}
			changeClassColor('rankColorClass'+userId, '#97C242');
			break;
		case "CloudPRO":
			if(fromProfile == 1){
				_('rank').style.color = "#C42D2D";
			}
			changeClassColor('rankColorClass'+userId, '#C42D2D');
			break;
		case "Legend100+":
			if(fromProfile == 1){
				_('rank').style.color = "#EB0C00";
			}
			changeClassColor('rankColorClass'+userId, '#EB0C00');
			break;
		default:
			if(fromProfile == 1){
				_('rank').style.color = "white";
			}
			changeClassColor('rankColorClass'+userId, 'white');
	}
}

function reportFile(fileId){
 alert(fileId);
}