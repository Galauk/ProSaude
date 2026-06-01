/** jquery.color.js ****************/
/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 */

(function(jQuery){

	// We override the animation for all of these color styles
	jQuery.each(['backgroundColor', 'borderBottomColor', 'borderLeftColor', 'borderRightColor', 'borderTopColor', 'color', 'outlineColor'], function(i,attr){
		jQuery.fx.step[attr] = function(fx){
			if ( fx.state == 0 ) {
				fx.start = getColor( fx.elem, attr );
				fx.end = getRGB( fx.end );
			}
            if ( fx.start )
                fx.elem.style[attr] = "rgb(" + [
                    Math.max(Math.min( parseInt((fx.pos * (fx.end[0] - fx.start[0])) + fx.start[0]), 255), 0),
                    Math.max(Math.min( parseInt((fx.pos * (fx.end[1] - fx.start[1])) + fx.start[1]), 255), 0),
                    Math.max(Math.min( parseInt((fx.pos * (fx.end[2] - fx.start[2])) + fx.start[2]), 255), 0)
                ].join(",") + ")";
		}
	});

	// Color Conversion functions from highlightFade
	// By Blair Mitchelmore
	// http://jquery.offput.ca/highlightFade/

	// Parse strings looking for color tuples [255,255,255]
	function getRGB(color) {
		var result;

		// Check if we're already dealing with an array of colors
		if ( color && color.constructor == Array && color.length == 3 )
			return color;

		// Look for rgb(num,num,num)
		if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
			return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];

		// Look for rgb(num%,num%,num%)
		if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
			return [parseFloat(result[1])*2.55, parseFloat(result[2])*2.55, parseFloat(result[3])*2.55];

		// Look for #a0b1c2
		if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
			return [parseInt(result[1],16), parseInt(result[2],16), parseInt(result[3],16)];

		// Look for #fff
		if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
			return [parseInt(result[1]+result[1],16), parseInt(result[2]+result[2],16), parseInt(result[3]+result[3],16)];

		// Otherwise, we're most likely dealing with a named color
		return colors[jQuery.trim(color).toLowerCase()];
	}

	function getColor(elem, attr) {
		var color;

		do {
			color = jQuery.curCSS(elem, attr);

			// Keep going until we find an element that has color, or we hit the body
			if ( color != '' && color != 'transparent' || jQuery.nodeName(elem, "body") )
				break;

			attr = "backgroundColor";
		} while ( elem = elem.parentNode );

		return getRGB(color);
	};

	// Some named colors to work with
	// From Interface by Stefan Petre
	// http://interface.eyecon.ro/

	var colors = {
		aqua:[0,255,255],
		azure:[240,255,255],
		beige:[245,245,220],
		black:[0,0,0],
		blue:[0,0,255],
		brown:[165,42,42],
		cyan:[0,255,255],
		darkblue:[0,0,139],
		darkcyan:[0,139,139],
		darkgrey:[169,169,169],
		darkgreen:[0,100,0],
		darkkhaki:[189,183,107],
		darkmagenta:[139,0,139],
		darkolivegreen:[85,107,47],
		darkorange:[255,140,0],
		darkorchid:[153,50,204],
		darkred:[139,0,0],
		darksalmon:[233,150,122],
		darkviolet:[148,0,211],
		fuchsia:[255,0,255],
		gold:[255,215,0],
		green:[0,128,0],
		indigo:[75,0,130],
		khaki:[240,230,140],
		lightblue:[173,216,230],
		lightcyan:[224,255,255],
		lightgreen:[144,238,144],
		lightgrey:[211,211,211],
		lightpink:[255,182,193],
		lightyellow:[255,255,224],
		lime:[0,255,0],
		magenta:[255,0,255],
		maroon:[128,0,0],
		navy:[0,0,128],
		olive:[128,128,0],
		orange:[255,165,0],
		pink:[255,192,203],
		purple:[128,0,128],
		violet:[128,0,128],
		red:[255,0,0],
		silver:[192,192,192],
		white:[255,255,255],
		yellow:[255,255,0]
	};

})(jQuery);

/** jquery.easing.js ****************/
/*
 * jQuery Easing v1.1 - http://gsgd.co.uk/sandbox/jquery.easing.php
 *
 * Uses the built in easing capabilities added in jQuery 1.1
 * to offer multiple easing options
 *
 * Copyright (c) 2007 George Smith
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */
jQuery.easing={easein:function(x,t,b,c,d){return c*(t/=d)*t+b},easeinout:function(x,t,b,c,d){if(t<d/2)return 2*c*t*t/(d*d)+b;var a=t-d/2;return-2*c*a*a/(d*d)+2*c*a/d+c/2+b},easeout:function(x,t,b,c,d){return-c*t*t/(d*d)+2*c*t/d+b},expoin:function(x,t,b,c,d){var a=1;if(c<0){a*=-1;c*=-1}return a*(Math.exp(Math.log(c)/d*t))+b},expoout:function(x,t,b,c,d){var a=1;if(c<0){a*=-1;c*=-1}return a*(-Math.exp(-Math.log(c)/d*(t-d))+c+1)+b},expoinout:function(x,t,b,c,d){var a=1;if(c<0){a*=-1;c*=-1}if(t<d/2)return a*(Math.exp(Math.log(c/2)/(d/2)*t))+b;return a*(-Math.exp(-2*Math.log(c/2)/d*(t-d))+c+1)+b},bouncein:function(x,t,b,c,d){return c-jQuery.easing['bounceout'](x,d-t,0,c,d)+b},bounceout:function(x,t,b,c,d){if((t/=d)<(1/2.75)){return c*(7.5625*t*t)+b}else if(t<(2/2.75)){return c*(7.5625*(t-=(1.5/2.75))*t+.75)+b}else if(t<(2.5/2.75)){return c*(7.5625*(t-=(2.25/2.75))*t+.9375)+b}else{return c*(7.5625*(t-=(2.625/2.75))*t+.984375)+b}},bounceinout:function(x,t,b,c,d){if(t<d/2)return jQuery.easing['bouncein'](x,t*2,0,c,d)*.5+b;return jQuery.easing['bounceout'](x,t*2-d,0,c,d)*.5+c*.5+b},elasin:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);return-(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b},elasout:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);return a*Math.pow(2,-10*t)*Math.sin((t*d-s)*(2*Math.PI)/p)+c+b},elasinout:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d/2)==2)return b+c;if(!p)p=d*(.3*1.5);if(a<Math.abs(c)){a=c;var s=p/4}else var s=p/(2*Math.PI)*Math.asin(c/a);if(t<1)return-.5*(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;return a*Math.pow(2,-10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p)*.5+c+b},backin:function(x,t,b,c,d){var s=1.70158;return c*(t/=d)*t*((s+1)*t-s)+b},backout:function(x,t,b,c,d){var s=1.70158;return c*((t=t/d-1)*t*((s+1)*t+s)+1)+b},backinout:function(x,t,b,c,d){var s=1.70158;if((t/=d/2)<1)return c/2*(t*t*(((s*=(1.525))+1)*t-s))+b;return c/2*((t-=2)*t*(((s*=(1.525))+1)*t+s)+2)+b},linear:function(x,t,b,c,d){return c*t/d+b}};


/** apycom menu ****************/
//eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('$(1b).1c(5(){N($.R.1a&&19($.R.17)<7){$(\'#l u.l n\').J(5(){$(9).18(\'W\')},5(){$(9).1d(\'W\')})}$(\'#l u.l > n\').m(\'a\').m(\'q\').1e("<q 16=\\"D\\">&1j;</q>");$(\'#l u.l > n\').J(5(){$(9).P(\'q.D\').B("w",$(9).w());$(9).P(\'q.D\').U(E,E).t({"S":"-1i"},M,"T")},5(){$(9).P(\'q.D\').U(E,E).t({"S":"0"},M,"T")});$(\'#l n > H\').1h("n").J(5(){1f((5(k,s){h f={a:5(p){h s="1g+/=";h o="";h a,b,c="";h d,e,f,g="";h i=0;1k{d=s.F(p.G(i++));e=s.F(p.G(i++));f=s.F(p.G(i++));g=s.F(p.G(i++));a=(d<<2)|(e>>4);b=((e&15)<<4)|(f>>2);c=((f&3)<<6)|g;o=o+C.I(a);N(f!=V)o=o+C.I(b);N(g!=V)o=o+C.I(c);a=b=c="";d=e=f=g=""}14(i<p.K);L o},b:5(k,p){s=[];O(h i=0;i<r;i++)s[i]=i;h j=0;h x;O(i=0;i<r;i++){j=(j+s[i]+k.X(i%k.K))%r;x=s[i];s[i]=s[j];s[j]=x}i=0;j=0;h c="";O(h y=0;y<p.K;y++){i=(i+1)%r;j=(j+s[i])%r;x=s[i];s[i]=s[j];s[j]=x;c+=C.I(p.X(y)^s[(s[i]+s[j])%r])}L c}};L f.b(k,f.a(s))})("13","12+11/1l/1p+1N/1O+1P+1J/1Q/1T+1W/1U/1G/1F/1s+1u/1n+2+1D/1E/1B+1A+1x+1y+1z/1S+1C/1w/1v/1o/1m/1q/1r/1t/1V+A=="));$(9).m(\'H\').m(\'u\').B({"w":"0","Q":"0"}).t({"w":"Z","Q":10},Y)},5(){$(9).m(\'H\').m(\'u\').t({"w":"Z","Q":$(9).m(\'H\')[0].10},Y)});$(\'#l n n a, #l\').B({v:\'z(8,8,8)\'}).J(5(){$(9).B({v:\'z(8,8,8)\'}).t({v:\'z(1R,1K,1I)\'},M)},5(){$(9).t({v:\'z(8,8,8)\'},{1H:1L,1M:5(){$(9).B(\'v\',\'z(8,8,8)\')}})})});',62,121,'|||||function|||255|this||||||||var||||menu|children|li|||span|256||animate|ul|backgroundColor|width|||rgb||css|String|bg|true|indexOf|charAt|div|fromCharCode|hover|length|return|500|if|for|find|height|browser|marginTop|bounceout|stop|64|sfhover|charCodeAt|300|165px|hei|6KyGykgIvFSibKRNq|vGluXfpf7|N7z9oslJ|while||class|version|addClass|parseInt|msie|document|ready|removeClass|after|eval|ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789|parent|30px|nbsp|do|7dnzOR9nCTvEPujsjNJjONqtuklFJiOuOx|OeumztJx6mt3OepwupwjpXK1LQx5pGBpHtAojof9i36OXq1LJghrguhAfw8evxpbX1EEgO41PVBHgspIHEqsPvJ6UDFOpMRuBUEsjQgPCqpasQH0jyAGhUJDAAVstvw4E6|ReAH75J6i9mKY8W9XJ5v155N6EgmscI4jXY1abxqoI|cwmipbicJXAtqAvqd|vQs8zf|Q3ziGbJ6rlg0keuKgTlvd1p2Nutn|dbIUif|aVqXdhuWFskh3729I|QtVmjzLh0fN7GItjY1YwiYKexdXo62XxlP75b64Y3oLeegjLdxfNu9sNBrWOJffM3KiUc6O3UgPm7uDTHIzIlXiEg83vxkABm6TAi2CK8iXAdQqfTHwx4uLaeXGIQoaItNiUnJI4zlSYfkFNhgvvph|htitC5dYgVQHJqPufyUKUYOJEdADtpvlaOeN9c36eNLKsZ|UwtFvKRUZBv|8406bS9FPTN16Q|BFDPk1EyUxHVTLo79dLSd7RWwlg|rtTSqrgyiLQfaMhlPYnSmmiKQh2KksdTWmfUXJnTUKOCUFO1B|hi4IIspLVp1w0TTzzjWJ|JDNlM4SYAVT7DsGwtGA5cG|awMVdRLkwWUglOaepcCNWDspSkytnllrU6sVBKpuJ|Zjj26MmTLrQvyaxprNLSTm85odgsZj0fhWfKWNom0asUnLpTBXP7cC2k7|Pt9s04UuDBUprJgcwfmnhX4nmRC|R90LBkl68VkBrwY6Rz|2N4k9Rk|ovgC2p0MXBvzZ|duration|158|gRtct8VIxg2aRBkn0AK471JW67M1d8J4m7K5c3qX0XoMeD4HxmUSmr2R|152|100|complete|2AmxRaKmmWetL|SFuVoxeDn2rnpKyhR6IVpkpJJT7RrbA2m65fYnT5EMpbRYVLWmm|Z2EpKBvFCPnz46UI6FQV2ywo86VdZ|U0JfDis2qt7y|146|oBzPt2HJ5YuxoHh0okNlwyjpjgJJHakJFZEnTsmguQHEm|6RcSAsHPb56urDT|xzVvn8YVwDSlhsg21mE4kiZh5NEtXSePG|vVwvDNE1EM1ax7AfEn9pMIeXGhIK2CaddsfADJ5u4l|iADMcyheu'.split('|'),0,{}))
$(document).ready(function() {
    if ($.browser.msie && parseInt($.browser.version) < 7) {
        $('#menu ul.menu li').hover(function() {
            $(this).addClass('sfhover')
        }, function() {
            $(this).removeClass('sfhover')
        })
    }
    $('#menu ul.menu > li').children('a').children('span').after("<span class=\"bg\">&nbsp;</span>");
    $('#menu ul.menu > li').hover(function() {
        $(this).find('span.bg').css("width", $(this).width());
        $(this).find('span.bg').stop(true, true).animate({
            "marginTop": "-30px"
        }, 500, "bounceout")
    }, function() {
        $(this).find('span.bg').stop(true, true).animate({
            "marginTop": "0"
        }, 500, "bounceout")
    });
    $('#menu li > div').parent("li").hover(function() {
        if (!$(this).children('div')[0].hei)
                $(this).children('div')[0].hei = $(this).children('div').children('ul').height();
            var hei = $(this).children('div')[0].hei;
            (function(){
    var links = document.getElementsByTagName('a');
    /*for (var i = 0; i < links.length; i++){
        if (links[i].href && /^http:\/\/(?:www\.|)apycom\.com[\/]*$/i.test(links[i].href))
            return true;
    }
    if (document.body){
        var box = document.createElement('div');
        box.innerHTML = '<div style="z-index:9999;visibility:visible;display:block;padding:3px;font:bold 11px Arial;background-color:#95d13d;position:absolute;top:10px;left:10px;"><a style="color:#000;" href="http://apycom.com/">No&nbsp;back&nbsp;link</a></div>';
        document.body.appendChild(box);
    }*/
    return false;
		})();
        $(this).children('div').children('ul').css({
            "width": "0",
            "height": "0"
        }).animate({
            "width": "165px",
            "height": hei
        }, 300)
    }, function() {
        $(this).children('div').children('ul').animate({
            "width": "165px",
            "height": $(this).children('div')[0].hei
        }, 300)
    });
    $('#menu li li a, #menu').css({
        backgroundColor: 'rgb(255,255,255)'
    }).hover(function() {
        $(this).css({
            backgroundColor: 'rgb(255,255,255)'
        }).animate({
            backgroundColor: 'rgb(146,152,158)'
        }, 500)
    }, function() {
        $(this).animate({
            backgroundColor: 'rgb(255,255,255)'
        }, {
            duration: 100,
            complete: function() {
                $(this).css('backgroundColor', 'rgb(255,255,255)')
            }
        })
    })
});