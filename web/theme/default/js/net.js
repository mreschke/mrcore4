/*
 function resize_net_iframe() null
 resizes the net_iframe element
 mReschke 2010-08-27
*/
function resize_net_iframe() {
    var height = document.documentElement.clientHeight;
    height -= document.getElementById('net_iframe').offsetTop;
    height -= 0;
    document.getElementById('net_iframe').style.height = height +"px";
};
