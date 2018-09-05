$(function () {
	function addCookie(objName, objValue, objHours){//添加cookie 
        var str = objName + "=" + escape(objValue); 
        if (objHours > 0) {//为0时不设定过期时间，浏览器关闭时cookie自动消失 
         var date = new Date(); 
         var ms = objHours * 3600 * 1000; 
         date.setTime(date.getTime() + ms); 
         str += "; expires=" + date.toGMTString(); 
        } 
        document.cookie = str;
       } 
    function playClick(){
        var music = document.getElementById('music');
        var stateBtn = document.getElementsByClassName('stateBtn')[0];
        var play = music.getAttribute("data-state");
        if(play == 1) {
            music.pause(); 
            music.setAttribute("data-state",0);
            stateBtn.setAttribute("src",'/public/home/images/pause.png');
        }else{
            music.play();  
            music.setAttribute("data-state",1);
            stateBtn.setAttribute("src",'/public/home/images/play.png');
        } 
    };
    $(".stateBtn").on('click',function() {
        playClick();
    })
    $(".lang").on('click',function() {
        $(".lang ul").toggleClass('hide');
        $(".lang").toggleClass('landown');
        $(".lang").toggleClass('lanup');
    })
    $(".lang ul li").on('click',function() {
        $(".lang p").text($(this).text());
        $(".lang ul").toggleClass('hide');
        $(".lang").toggleClass('landown');
        $(".lang").toggleClass('lanup');
        if($(this).children("a").attr("href") == "/") {
            addCookie("lan","",720);
        }else if($(this).children("a").attr("href") == "/en") {
            addCookie("lan","en",720);
        }else if($(this).children("a").attr("href") == "/es-es") {
            addCookie("lan","es-es",720);
        }
    })

})


$(document).ready(function(){
    $('#recomond01').on('click',function() {
        $('#ads-tabs-crezed01').removeClass('fn-hide')
        $('#ads-tabs-crezed02').addClass('fn-hide')
        $('#recomond01').addClass('robot-hover')
        $('#recomond02').removeClass('robot-hover')
    })
    $('#recomond02').on('click',function() {
        $('#ads-tabs-crezed02').removeClass('fn-hide')
        $('#ads-tabs-crezed01').addClass('fn-hide')
        $('#recomond02').addClass('robot-hover')
        $('#recomond01').removeClass('robot-hover')
    })

    $('#recomond01-03').on('click',function() {
        $('#ads-tabs-crezed01-03').removeClass('fn-hide')
        $('#ads-tabs-crezed02-03').addClass('fn-hide')
        $('#recomond01-03').addClass('robot-hover-03')
        $('#recomond02-03').removeClass('robot-hover-03')
    })
    $('#recomond02-03').on('click',function() {
        $('#ads-tabs-crezed02-03').removeClass('fn-hide')
        $('#ads-tabs-crezed01-03').addClass('fn-hide')
        $('#recomond02-03').addClass('robot-hover-03')
        $('#recomond01-03').removeClass('robot-hover-03')
    })

    $('#recomond01-04').on('click',function() {
        $('#ads-tabs-crezed01-04').removeClass('fn-hide')
        $('#ads-tabs-crezed02-04').addClass('fn-hide')
        $('#ads-tabs-crezed03-04').addClass('fn-hide')
        $('#recomond01-04').addClass('robot-hover-04')
        $('#recomond02-04').removeClass('robot-hover-04')
        $('#recomond03-04').removeClass('robot-hover-04')
    })
    $('#recomond02-04').on('click',function() {
        $('#ads-tabs-crezed03-04').addClass('fn-hide')
        $('#ads-tabs-crezed02-04').removeClass('fn-hide')
        $('#ads-tabs-crezed01-04').addClass('fn-hide')
        $('#recomond03-04').removeClass('robot-hover-04')
        $('#recomond02-04').addClass('robot-hover-04')
        $('#recomond01-04').removeClass('robot-hover-04')
    })
    $('#recomond03-04').on('click',function() {
        $('#ads-tabs-crezed03-04').removeClass('fn-hide')
        $('#ads-tabs-crezed02-04').addClass('fn-hide')
        $('#ads-tabs-crezed01-04').addClass('fn-hide')
        $('#recomond03-04').addClass('robot-hover-04')
        $('#recomond02-04').removeClass('robot-hover-04')
        $('#recomond01-04').removeClass('robot-hover-04')
    })

    $('#recomond01-05').on('click',function() {
        $('#ads-tabs-crezed01-05').removeClass('fn-hide')
        $('#ads-tabs-crezed02-05').addClass('fn-hide')
        $('#recomond01-05').addClass('robot-hover-05')
        $('#recomond02-05').removeClass('robot-hover-05')
    })
    $('#recomond02-05').on('click',function() {
        $('#ads-tabs-crezed02-05').removeClass('fn-hide')
        $('#ads-tabs-crezed01-05').addClass('fn-hide')
        $('#recomond02-05').addClass('robot-hover-05')
        $('#recomond01-05').removeClass('robot-hover-05')
    })
})