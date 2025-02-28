function enterFullscreen(e){e=void 0===e||e,setTimeout((function(){window.dispatchEvent(new Event("resize"))}),220),$("body").addClass("fullscreen"),$("a.fullscreen").addClass("active"),Cookies.set("tx-dlf-pageview-zoomFullscreen","true",{sameSite:"lax"})}function exitFullscreen(e){e=void 0===e||e,setTimeout((function(){window.dispatchEvent(new Event("resize"))}),220),$("body").removeClass("fullscreen"),$("a.fullscreen").removeClass("active"),e&&Cookies.remove("tx-dlf-pageview-zoomFullscreen")}!function(e,t,n){function o(e,t){return typeof e===t}function a(e){return e.replace(/([a-z])-([a-z])/g,(function(e,t,n){return t+n.toUpperCase()})).replace(/^-/,"")}function l(e,t){return!!~(""+e).indexOf(t)}function i(){return"function"!=typeof t.createElement?t.createElement(arguments[0]):b?t.createElementNS.call(t,"http://www.w3.org/2000/svg",arguments[0]):t.createElement.apply(t,arguments)}function s(e,n,o,a){var l,s,r,c,d="modernizr",u=i("div"),f=function(){var e=t.body;return e||((e=i(b?"svg":"body")).fake=!0),e}();if(parseInt(o,10))for(;o--;)(r=i("div")).id=a?a[o]:d+(o+1),u.appendChild(r);return(l=i("style")).type="text/css",l.id="s"+d,(f.fake?f:u).appendChild(l),f.appendChild(u),l.styleSheet?l.styleSheet.cssText=e:l.appendChild(t.createTextNode(e)),u.id=d,f.fake&&(f.style.background="",f.style.overflow="hidden",c=x.style.overflow,x.style.overflow="hidden",x.appendChild(f)),s=n(u,e),f.fake?(f.parentNode.removeChild(f),x.style.overflow=c,x.offsetHeight):u.parentNode.removeChild(u),!!s}function r(e,t){return function(){return e.apply(t,arguments)}}function c(e){return e.replace(/([A-Z])/g,(function(e,t){return"-"+t.toLowerCase()})).replace(/^ms-/,"-ms-")}function d(t,n,o){var a;if("getComputedStyle"in e){a=getComputedStyle.call(e,t,n);var l=e.console;if(null!==a)o&&(a=a.getPropertyValue(o));else if(l){l[l.error?"error":"log"].call(l,"getComputedStyle returning null, its possible modernizr test results are inaccurate")}}else a=!n&&t.currentStyle&&t.currentStyle[o];return a}function u(t,o){var a=t.length;if("CSS"in e&&"supports"in e.CSS){for(;a--;)if(e.CSS.supports(c(t[a]),o))return!0;return!1}if("CSSSupportsRule"in e){for(var l=[];a--;)l.push("("+c(t[a])+":"+o+")");return s("@supports ("+(l=l.join(" or "))+") { #modernizr { position: absolute; } }",(function(e){return"absolute"==d(e,null,"position")}))}return n}function f(e,t,s,r){function c(){f&&(delete P.style,delete P.modElem)}if(r=!o(r,"undefined")&&r,!o(s,"undefined")){var d=u(e,s);if(!o(d,"undefined"))return d}for(var f,p,m,h,g,v=["modernizr","tspan","samp"];!P.style&&v.length;)f=!0,P.modElem=i(v.shift()),P.style=P.modElem.style;for(m=e.length,p=0;m>p;p++)if(h=e[p],g=P.style[h],l(h,"-")&&(h=a(h)),P.style[h]!==n){if(r||o(s,"undefined"))return c(),"pfx"!=t||h;try{P.style[h]=s}catch(e){}if(P.style[h]!=g)return c(),"pfx"!=t||h}return c(),!1}function p(e,t,n,a,l){var i=e.charAt(0).toUpperCase()+e.slice(1),s=(e+" "+T.join(i+" ")+i).split(" ");return o(t,"string")||o(t,"undefined")?f(s,t,a,l):function(e,t,n){var a;for(var l in e)if(e[l]in t)return!1===n?e[l]:o(a=t[e[l]],"function")?r(a,n||t):a;return!1}(s=(e+" "+y.join(i+" ")+i).split(" "),t,n)}function m(e,t,o){return p(e,n,n,t,o)}var h=[],g=[],v={_version:"3.5.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,t){var n=this;setTimeout((function(){t(n[e])}),0)},addTest:function(e,t,n){g.push({name:e,fn:t,options:n})},addAsyncTest:function(e){g.push({name:null,fn:e})}},$=function(){};$.prototype=v,$=new $;var C=v._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):["",""];v._prefixes=C;var x=t.documentElement,b="svg"===x.nodeName.toLowerCase(),w="Moz O ms Webkit",y=v._config.usePrefixes?w.toLowerCase().split(" "):[];v._domPrefixes=y;var k="CSS"in e&&"supports"in e.CSS,S="supportsCSS"in e;$.addTest("supports",k||S);var T=v._config.usePrefixes?w.split(" "):[];v._cssomPrefixes=T;var M=function(t){var o,a=C.length,l=e.CSSRule;if(void 0===l)return n;if(!t)return!1;if((o=(t=t.replace(/^@/,"")).replace(/-/g,"_").toUpperCase()+"_RULE")in l)return"@"+t;for(var i=0;a>i;i++){var s=C[i];if(s.toUpperCase()+"_"+o in l)return"@-"+s.toLowerCase()+"-"+t}return!1};v.atRule=M;var z=v.testStyles=s;$.addTest("touchevents",(function(){var n;if("ontouchstart"in e||e.DocumentTouch&&t instanceof DocumentTouch)n=!0;else{var o=["@media (",C.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");z(o,(function(e){n=9===e.offsetTop}))}return n}));var E={elem:i("modernizr")};$._q.push((function(){delete E.elem}));var P={style:E.elem.style};$._q.unshift((function(){delete P.style})),v.testProp=function(e,t,o){return f([e],n,t,o)},v.testAllProps=p;var F=v.prefixed=function(e,t,n){return 0===e.indexOf("@")?M(e):(-1!=e.indexOf("-")&&(e=a(e)),t?p(e,t,n):p(e,"pfx"))};v.testAllProps=m,$.addTest("csstransforms3d",(function(){var e=!!m("perspective","1px",!0),t=$._config.usePrefixes;if(e&&(!t||"webkitPerspective"in x.style)){var n;$.supports?n="@supports (perspective: 1px)":(n="@media (transform-3d)",t&&(n+=",(-webkit-transform-3d)")),z("#modernizr{width:0;height:0}"+(n+="{#modernizr{width:7px;height:18px;margin:0;padding:0;border:0}}"),(function(t){e=7===t.offsetWidth&&18===t.offsetHeight}))}return e})),$.addTest("csstransitions",m("transition","all",!0)),$.addTest("objectfit",!!F("objectFit"),{aliases:["object-fit"]}),function(){var e,t,n,a,l,i;for(var s in g)if(g.hasOwnProperty(s)){if(e=[],(t=g[s]).name&&(e.push(t.name.toLowerCase()),t.options&&t.options.aliases&&t.options.aliases.length))for(n=0;n<t.options.aliases.length;n++)e.push(t.options.aliases[n].toLowerCase());for(a=o(t.fn,"function")?t.fn():t.fn,l=0;l<e.length;l++)1===(i=e[l].split(".")).length?$[i[0]]=a:(!$[i[0]]||$[i[0]]instanceof Boolean||($[i[0]]=new Boolean($[i[0]])),$[i[0]][i[1]]=a),h.push((a?"":"no-")+i.join("-"))}}(),function(e){var t=x.className,n=$._config.classPrefix||"";if(b&&(t=t.baseVal),$._config.enableJSClass){var o=new RegExp("(^|\\s)"+n+"no-js(\\s|$)");t=t.replace(o,"$1"+n+"js$2")}$._config.enableClasses&&(t+=" "+n+e.join(" "+n),b?x.className.baseVal=t:x.className=t)}(h),delete v.addTest,delete v.addAsyncTest;for(var _=0;_<$._q.length;_++)$._q[_]();e.Modernizr=$}(window,document),function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e=e||self,function(){var n=e.Cookies,o=e.Cookies=t();o.noConflict=function(){return e.Cookies=n,o}}())}(this,(function(){"use strict";function e(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)e[o]=n[o]}return e}var t={read:function(e){return e.replace(/(%[\dA-F]{2})+/gi,decodeURIComponent)},write:function(e){return encodeURIComponent(e).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,decodeURIComponent)}};return function n(o,a){function l(t,n,l){if("undefined"!=typeof document){"number"==typeof(l=e({},a,l)).expires&&(l.expires=new Date(Date.now()+864e5*l.expires)),l.expires&&(l.expires=l.expires.toUTCString()),t=encodeURIComponent(t).replace(/%(2[346B]|5E|60|7C)/g,decodeURIComponent).replace(/[()]/g,escape),n=o.write(n,t);var i="";for(var s in l)l[s]&&(i+="; "+s,!0!==l[s]&&(i+="="+l[s].split(";")[0]));return document.cookie=t+"="+n+i}}return Object.create({set:l,get:function(e){if("undefined"!=typeof document&&(!arguments.length||e)){for(var n=document.cookie?document.cookie.split("; "):[],a={},l=0;l<n.length;l++){var i=n[l].split("="),s=i.slice(1).join("=");'"'===s[0]&&(s=s.slice(1,-1));try{var r=t.read(i[0]);if(a[r]=o.read(s,r),e===r)break}catch(e){}}return e?a[e]:a}},remove:function(t,n){l(t,"",e({},n,{expires:-1}))},withAttributes:function(t){return n(this.converter,e({},this.attributes,t))},withConverter:function(t){return n(e({},this.converter,t),this.attributes)}},{attributes:{value:Object.freeze(a)},converter:{value:Object.freeze(o)}})}(t,{path:"/"})})),window.DigitalCollections=function(e){return e.isInTheaterMode=function(){return $("body.fullscreen").length>0},e.toggleTheaterMode=function(t){e.isInTheaterMode()?exitFullscreen(t):enterFullscreen(t)},e}(window.DigitalCollections||{}),$((function(){$("html").removeClass("no-js"),$(".offcanvas-toggle").on("click",(function(e){$(this).parent().toggleClass("open")})),$("li.submenu > a").on("click",(function(e){$("li.submenu.open a").not(this).parent().removeClass("open"),$(this).parent().toggleClass("open")})),$("nav .nav-toggle").on("click",(function(e){$(this).toggleClass("active"),$("nav .secondary-nav").toggleClass("open")})),$(".calendar-view div.issues h4").on("click",(function(e){var t=$(this).parents("div.issues").find("div ul li a");1==t.length?window.open(t[0].href,"_self"):($(".calendar-view table td.open").not($(this).parents("td")).removeClass("open"),$(this).parents("td").toggleClass("open"))})),$(".tx-dlf-calendar, .tx-dlf-calendar-years").parents("body").addClass("calendar"),$(".tx-dlf-pagegrid-list").parents("body").addClass("gridview"),$(".tx-dlf-calendar .calendar-list-selection a.select-calendar-view").hasClass("active")&&$(".tx-dlf-calendar .calendar-list-selection a.select-calendar-view").removeClass("active"),$(".tx-dlf-calendar .calendar-list-selection a.select-calendar-view, .tx-dlf-calendar .calendar-view").addClass("active"),$(".tx-dlf-calendar .calendar-list-selection a").on("click",(function(e){if(!$(this).hasClass("active")){var t="."+$(this).attr("class").replace("select-","");$(".tx-dlf-calendar .active").removeClass("active"),$(this).addClass("active"),$(t).addClass("active")}})),$(".provider img").length>0?$(".provider img").each((function(){(void 0!==this.naturalWidth&&0==this.naturalWidth||"uninitialized"==this.readyState)&&$(this).parents(".document-functions").addClass("missing-provider-image")})):$(".provider").parents(".document-functions").addClass("missing-provider-image"),$(".pages select option[selected]")[0]&&$("dl.mobile-meta").append('<dt class="mobile-page-number">No.</dt><dd class="mobile-page-number">'+$(".pages select option[selected]").text()+"</dd>"),$(".provider").append('<div class="mobile-controls" />'),$(".view-functions .pages form, .view-functions .zoom a.fullscreen, .fulltext-search-toggle").clone().appendTo(".provider .mobile-controls"),shortenMobileMetaElement=$(".provider dl.mobile-meta dd.tx-dlf-title a"),shortenMobileMetaTitle=shortenMobileMetaElement.text(),shortenMobileMetaTitle.length>140&&(shortenMobileMetaTitle=shortenMobileMetaTitle.substr(0,140)+"...",shortenMobileMetaElement.text(shortenMobileMetaTitle)),$(".submenu.downloads ul li")[0]||$(".submenu.downloads>a").replaceWith((function(){return $('<span title="'+$(this).attr("title")+'" class="'+$(this).attr("class")+'" id="'+$(this).attr("id")+'">'+$(this).html()+"</span>")})),$(".tx-dlf-metadata dl.tx-dlf-metadata-titledata").length>1&&(metadataToggleLabelMore=$('html[lang^="de"]')[0]?"mehr Metadaten":"more Metadata",metadataToggleLabelLess=$('html[lang^="de"]')[0]?"weniger Metadaten":"less Metadata",$(".control-bar .metadata-wrapper").append('<div class="metadata-toggle">'+metadataToggleLabelMore+"</div>"),$(".metadata-toggle").on("click",(function(){$(".control-bar").hasClass("all-metadata")?(Cookies.remove("tx-dlf-allmetadata"),$(this).text(metadataToggleLabelMore)):(Cookies.set("tx-dlf-allmetadata","true",{sameSite:"lax"}),$(this).text(metadataToggleLabelLess)),$(".control-bar").toggleClass("all-metadata").find("dl:nth-child(n+3)").slideToggle()}))),$("a.fullscreen").on("click",(function(){window.DigitalCollections.toggleTheaterMode()})),Cookies.get("tx-dlf-pageview-zoomFullscreen")&&($("body").addClass("fullscreen static"),$(".zoom .fullscreen").addClass("active")),$("ul.toc ul li.current")[0]&&(tocPlaceholderLabel=$('html[lang^="de"]')[0]?"Einige Einträge sind ausgeblendet":"Some entires are hidden",tocPlaceholderTitle=$('html[lang^="de"]')[0]?"Hier klicken um alle Einträge zu zeigen":"Click to show all entries",$("ul.toc ul li.current").first().prevAll(":eq(4)").prevUntil(":nth-child(2)").hide(),$("ul.toc ul li:nth-child(2)").after('<li class="placeholder" title="'+tocPlaceholderTitle+'">'+tocPlaceholderLabel+"</li>"),$("ul.toc ul li.placeholder").on("click",(function(){$(this).remove(),$("ul.toc ul li").slideDown()}))),$(".tx-dlf-toolsFulltextsearch form")[0]?$(".fulltext-search-toggle").on("click",(function(){$("body").toggleClass("search-indocument-active"),$(".tx-dlf-toolsFulltextsearch").css({top:$(this).offset().top-60+"px"}),$("body.search-indocument-active #tx-dlf-search-in-document-query").trigger("focus")})):$(".fulltext-search-toggle").addClass("disabled"),Modernizr.touchevents?($(".fwds, .backs").on("touchstart",(function(){$(this).addClass("over"),triggeredElement=$(this),setTimeout((function(){triggeredElement.addClass("enable-touchevent")}),250)})).on("touchend",(function(){localStorage.txDlfFromPage=$(this).attr("class").split(" ")[0]})),$("body").on("touchstart",(function(e){target=$(e.target),target.closest(".page-control")[0]||($(".fwds, .backs").removeClass("over enable-touchevent"),localStorage.clear())})),localStorage.txDlfFromPage&&($("."+localStorage.txDlfFromPage).addClass("no-transition over enable-touchevent"),localStorage.clear())):($(".fwds, .backs").on("mouseenter",(function(){$(this).addClass("over")})).on("mouseleave",(function(){$(this).removeClass("over")})).on("click",(function(){localStorage.txDlfFromPage=$(this).attr("class").split(" ")[0]})),localStorage.txDlfFromPage&&($("."+localStorage.txDlfFromPage).addClass("no-transition over"),localStorage.clear())),$(".tx-dlf-map").children()[0]||($(".tx-dlf-map").remove(),emptyMessage=$('html[lang^="de"]')[0]?"Kein Band ausgew&auml;hlt. Klicken Sie hier um zum ersten Band dieses Werks zu gelangen.":"No volume selected. Click to jump to the first available volume.",$(".tx-dlf-pageview").append('<div class="tx-dlf-empty"><a class="tx-dlf-emptyToFirstVol" href="'+$(".tx-dlf-toc ul li ul li:first-child a").attr("href")+'"><span class="error-arrow">&larr;</span>'+emptyMessage+"</a></div>")),$("dl.tx-dlf-metadata-titledata").find("dt:contains(mmlung), dt:contains(llection)").nextUntil("dt","dd").addClass("tx-dlf-metadata-collection"),setTimeout((function(){localStorage.clear(),$(".fwds, .backs").removeClass("no-transition"),$("body").removeClass("static")}),1e3)})),$(document).keyup((function(e){if("Escape"===e.key){if(window.DigitalCollections.isInTheaterMode())return exitFullscreen();$(".document-functions .search.open")[0]&&$(".document-functions .search").removeClass("open")}if("f"===e.key&&!$("#tx-dlf-search-in-document-query").is(":focus"))return enterFullscreen()})),window.addEventListener("dlf-theater-mode",(e=>{if(void 0!==e.detail)switch(e.detail.action){case"toggle":window.DigitalCollections.toggleTheaterMode(e.detail.persist)}else console.warn("dlf-theater-mode: No parameter given")}));