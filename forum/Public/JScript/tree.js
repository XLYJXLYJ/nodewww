function Tree(data, el) {
    this.app=function(par,tag){return par.appendChild(document.createElement(tag))};
    this.create(document.getElementById(el),data)
};
Tree.prototype = {
    create: function (par,group){
        var host=this, length = group.length;
        for (var i = 0; i < length; i++) {
            var dl =this.app(par,'DL'), dt = this.app(dl,'DT'), dd = this.app(dl,'DD');
            var tmp=typeof(group[i]['url'])=="undefined"?"javascript:void(0)":group[i]['url'];
            dt.innerHTML='<a href='+tmp+'>'+group[i]['t']+'</a>';
            if (!group[i]['s'])continue;
            dt.group=group[i]['s'];
            dt.className+=" node-close";
            dt.onclick=function (){
                var dd= this.nextSibling;
                if (!dd.hasChildNodes()){
                     host.create(dd,this.group);
                     this.className='node-open'
                 }else{
                    var set=dd.style.display=='none'?['','node-open']:['none','node-close'];
                     dd.style.display=set[0];
                     this.className=set[1]
                 }
            }
        }
    }
};
var et=new Tree(data,'treediv');
var dts=document.getElementById("treediv").getElementsByTagName("dt");
for(var i=0;i<dts.length;i++){
if(document.all) {dts[i].click();}
else {
  	var e = document.createEvent("MouseEvents");
  	e.initEvent("click", false, false);
  	dts[i].dispatchEvent(e);
  }
}
var navH = document.getElementById("treediv").offsetTop;
window.onscroll=function(){
    var scroH = window.pageYOffset|| document.documentElement.scrollTop || document.body.scrollTop;
    if (scroH >= navH) {
       document.getElementById("treediv").style.position="fixed";
       document.getElementById("treediv").style.top=0;
    } else if (scroH < navH) {
       document.getElementById("treediv").style.position="absolute";
       document.getElementById("treediv").style.top="44px";
    }
}