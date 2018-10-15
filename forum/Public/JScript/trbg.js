var trs = document.getElementById("lists").getElementsByTagName("tr");
for (var i = 0; i < trs.length; i++) {
    if (i % 2 == 1)
    trs[i].style.background = "#f7f7f7";
    if (i % 2 == 0)
    trs[i].style.background = "#ffffff";
}
var urls = window.location.href;
var url_array = urls.split("=");
var page = url_array[1];
if (page != undefined){
	var uclass = document.getElementsByTagName("a");
  for (var n in uclass)
  {
  	if((uclass[n].className=='fy'+page) && (uclass[n].className != undefined)){
      uclass[n].style.background = "#f8fee4";
    }
	}
}
document.getElementById("fxt").onmouseover = function() {
    if (document.getElementById("pop")) {
        document.getElementById("pop").style.display = "block";
        document.getElementById("pop").onmouseover = function() {
            this.style.display = "block";
        }
        document.getElementById("pop").onmouseout = function() {
            this.style.display = "none";
        }
    } else {
        var newchild = document.createElement("div");
        newchild.id = "pop";
        var w = getLeft(document.getElementById("fxt"));
        var h = getTop(document.getElementById("fxt")) + 32;
        newchild.style.cssText = "width:104px;height:60px;background:#f4f4f4;position:absolute;left:" + w + "px;top:" + h +"px";
        newchild.innerHTML = '<ul style="margin:0px 4px;padding:0px"><li style="list-style-type:none;width:80px;height:30px;line-height:30px"><a style="font-size:12px;"  href="' + rooturl_list + '/List' + url + 'fatie' + shtml + '">发表新帖</a></li><li style="list-style-type:none;width:80px;height:30px;line-height:30px"><a style="font-size:12px;"  href="' + rooturl_list + '/List' + url + 'vote' + shtml + '">发表投票</a></li></ul>';
        document.body.appendChild(newchild);
        document.getElementById("pop").style.display = "block";
        document.getElementById("pop").onmouseover = function() {
            this.style.display = "block";
        }
        document.getElementById("pop").onmouseout = function() {
            this.style.display = "none";
        }
    }
}
document.getElementById("fxt").onmouseout = function() {
    document.getElementById("pop").style.display = "none";
}