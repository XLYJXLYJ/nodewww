document.onreadystatechange = function(){
    if (document.readyState == "complete"){
        var url = window.location.href;
        url = url.substring(url.indexOf("?")+1,url.length);
       // if(url.indexOf("=")!=-1){
        	var url_array = url.split("=");
        	var page;
	        if(typeof(url_array[1])!='undefined'){
		        if(url_array[1].indexOf("#") != -1){
		        	page = url_array[1].substring(0,url_array[1].indexOf("#"));
		      	}else{
		      		page = url_array[1];
		      	}
		      	var page_tmp = page.split("&");
		        	page = page_tmp[0];
	      	}
	      	var uclass = document.getElementsByTagName("a");
	        for (var n in uclass)
	        {
	            	if((url_array[0] == 'page') && (uclass[n].className=='fy'+page) && (uclass[n].className != undefined)){
	                uclass[n].style.background = "#deccb1";
	              }
	        }
      //  }else{
        	if(url.indexOf("#") != -1){
		        	page = url.substring(0,url.indexOf("#"));
		      	}else{
		      		page = url;
		      	}
		      	var page_tmp = page.split("&");
		        	page = page_tmp[1];
		       if(typeof(page)!='undefined'){
		      	var url_tmp = page.split("_");
		      	var uclass = document.getElementsByTagName("a");
		        for (var n in uclass)
		        {
	            	if((uclass[n].className=='comments'+url_tmp[0]+"_"+url_tmp[1]+"_"+url_tmp[2]) && (uclass[n].className != undefined)){
	              	uclass[n].style.color = "#000";
	              	uclass[n].style.fontWeight = "bold";
	              }
		        }
		       }
       // }
    }
}
var xmlhttp;
if (window.ActiveXObject)
 {
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
}
 else
 {
    xmlhttp = new XMLHttpRequest();
}function check(){
	var addr = window.location.pathname;
	addr = addr.substring(0,addr.length-shtml.length);
	addr = addr.split(url);
	for(var i=0;i<addr.length;i++){
		if(addr[i]=='id'){
			addr = addr[i+1];
			break;
		}
	}
    if (document.form1.zuozhe1.value == "")
    {
        alert("您没有权限发帖，请先登陆");
        return false;
    }
    if (document.forms['form1'].elements['content'].value == "")
    {
        alert("回复内容不能为空！");
        return false;
    }
    xmlhttp.open("POST", rooturl + "/index.php/Content" + url + "setParentID" + shtml, true);
	  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	  xmlhttp.send("talk_id="+addr);
	  xmlhttp.onreadystatechange = function() {
	  if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
	  		document.forms['form1'].action=rooturl + "/index.php/List" + url + "insert2" + shtml;
	  		document.forms['form1'].target='_self';
	  		document.forms['form1'].submit();
	  	}else{
	  		return false;
	  	}
	  }
	  return false;
}
function showmarkone(id,mid,val){
		val = typeof(val)=="undefined" ? '' : val;
		mid = typeof(mid)=="undefined" ? '' : mid;
    var w = (document.body.clientWidth - 200) / 2 + "px";
    var h = (document.documentElement.clientHeight - 250) / 2 + "px";
  	document.getElementById("markone").style.cssText = "position:fixed;left:" + w + ";top:" + h + ";z-index:99;width:200px;height:250px;border:5px solid #b2b3b7;background-color:#ebe8e3;";
    document.getElementById("markone").style.display = "block";
    document.getElementById("markone_iframe").src=rooturl + "/ext/mark.php?id=" + id + "&reply_u=" + val + "&mid=" + mid;
}
function turnmarkone(){
    document.getElementById("markone").style.display = "none";
}
function showmark(m,id,mid,val){
		val = typeof(val)=="undefined" ? '' : val;
		mid = typeof(mid)=="undefined" ? '' : mid;
    var w = (document.body.clientWidth - 200) / 2 + "px";
    var h = (document.documentElement.clientHeight - 250) / 2 + "px";
  	document.getElementById("mark" + m).style.cssText = "position:fixed;left:" + w + ";top:" + h + ";z-index:99;width:200px;height:250px;border:5px solid #b2b3b7;background-color:#ebe8e3;";
    document.getElementById("mark" + m).style.display = "block";
    document.getElementById("mark_iframe"+m).src=rooturl + "/ext/mark.php?id2=" + m + "&id=" + id + "&reply_u=" + val + "&mid=" + mid;
}
function turnmark(m){
    document.getElementById("mark" + m).style.display = "none";
}