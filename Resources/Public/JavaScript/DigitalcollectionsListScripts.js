function mobileCheck(){var e,t=!1;return e=navigator.userAgent||navigator.vendor||window.opera,(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(e)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(e.substr(0,4)))&&(t=!0),t}!function(e,t,i){function n(e,t){return typeof e===t}function o(e){return e.replace(/([a-z])-([a-z])/g,(function(e,t,i){return t+i.toUpperCase()})).replace(/^-/,"")}function s(e,t){return!!~(""+e).indexOf(t)}function a(){return"function"!=typeof t.createElement?t.createElement(arguments[0]):x?t.createElementNS.call(t,"http://www.w3.org/2000/svg",arguments[0]):t.createElement.apply(t,arguments)}function l(e,i,n,o){var s,l,r,c,d="modernizr",u=a("div"),f=function(){var e=t.body;return e||((e=a(x?"svg":"body")).fake=!0),e}();if(parseInt(n,10))for(;n--;)(r=a("div")).id=o?o[n]:d+(n+1),u.appendChild(r);return(s=a("style")).type="text/css",s.id="s"+d,(f.fake?f:u).appendChild(s),f.appendChild(u),s.styleSheet?s.styleSheet.cssText=e:s.appendChild(t.createTextNode(e)),u.id=d,f.fake&&(f.style.background="",f.style.overflow="hidden",c=w.style.overflow,w.style.overflow="hidden",w.appendChild(f)),l=i(u,e),f.fake?(f.parentNode.removeChild(f),w.style.overflow=c,w.offsetHeight):u.parentNode.removeChild(u),!!l}function r(e,t){return function(){return e.apply(t,arguments)}}function c(e){return e.replace(/([A-Z])/g,(function(e,t){return"-"+t.toLowerCase()})).replace(/^ms-/,"-ms-")}function d(t,i,n){var o;if("getComputedStyle"in e){o=getComputedStyle.call(e,t,i);var s=e.console;if(null!==o)n&&(o=o.getPropertyValue(n));else if(s){s[s.error?"error":"log"].call(s,"getComputedStyle returning null, its possible modernizr test results are inaccurate")}}else o=!i&&t.currentStyle&&t.currentStyle[n];return o}function u(t,n){var o=t.length;if("CSS"in e&&"supports"in e.CSS){for(;o--;)if(e.CSS.supports(c(t[o]),n))return!0;return!1}if("CSSSupportsRule"in e){for(var s=[];o--;)s.push("("+c(t[o])+":"+n+")");return l("@supports ("+(s=s.join(" or "))+") { #modernizr { position: absolute; } }",(function(e){return"absolute"==d(e,null,"position")}))}return i}function f(e,t,l,r){function c(){f&&(delete A.style,delete A.modElem)}if(r=!n(r,"undefined")&&r,!n(l,"undefined")){var d=u(e,l);if(!n(d,"undefined"))return d}for(var f,h,p,m,v,g=["modernizr","tspan","samp"];!A.style&&g.length;)f=!0,A.modElem=a(g.shift()),A.style=A.modElem.style;for(p=e.length,h=0;p>h;h++)if(m=e[h],v=A.style[m],s(m,"-")&&(m=o(m)),A.style[m]!==i){if(r||n(l,"undefined"))return c(),"pfx"!=t||m;try{A.style[m]=l}catch(e){}if(A.style[m]!=v)return c(),"pfx"!=t||m}return c(),!1}function h(e,t,i,o,s){var a=e.charAt(0).toUpperCase()+e.slice(1),l=(e+" "+S.join(a+" ")+a).split(" ");return n(t,"string")||n(t,"undefined")?f(l,t,o,s):function(e,t,i){var o;for(var s in e)if(e[s]in t)return!1===i?e[s]:n(o=t[e[s]],"function")?r(o,i||t):o;return!1}(l=(e+" "+$.join(a+" ")+a).split(" "),t,i)}function p(e,t,n){return h(e,i,i,t,n)}var m=[],v=[],g={_version:"3.5.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,t){var i=this;setTimeout((function(){t(i[e])}),0)},addTest:function(e,t,i){v.push({name:e,fn:t,options:i})},addAsyncTest:function(e){v.push({name:null,fn:e})}},y=function(){};y.prototype=g,y=new y;var b=g._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):["",""];g._prefixes=b;var w=t.documentElement,x="svg"===w.nodeName.toLowerCase(),C="Moz O ms Webkit",$=g._config.usePrefixes?C.toLowerCase().split(" "):[];g._domPrefixes=$;var k="CSS"in e&&"supports"in e.CSS,z="supportsCSS"in e;y.addTest("supports",k||z);var S=g._config.usePrefixes?C.split(" "):[];g._cssomPrefixes=S;var _=function(t){var n,o=b.length,s=e.CSSRule;if(void 0===s)return i;if(!t)return!1;if((n=(t=t.replace(/^@/,"")).replace(/-/g,"_").toUpperCase()+"_RULE")in s)return"@"+t;for(var a=0;o>a;a++){var l=b[a];if(l.toUpperCase()+"_"+n in s)return"@-"+l.toLowerCase()+"-"+t}return!1};g.atRule=_;var E=g.testStyles=l;y.addTest("touchevents",(function(){var i;if("ontouchstart"in e||e.DocumentTouch&&t instanceof DocumentTouch)i=!0;else{var n=["@media (",b.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");E(n,(function(e){i=9===e.offsetTop}))}return i}));var T={elem:a("modernizr")};y._q.push((function(){delete T.elem}));var A={style:T.elem.style};y._q.unshift((function(){delete A.style})),g.testProp=function(e,t,n){return f([e],i,t,n)},g.testAllProps=h;var j=g.prefixed=function(e,t,i){return 0===e.indexOf("@")?_(e):(-1!=e.indexOf("-")&&(e=o(e)),t?h(e,t,i):h(e,"pfx"))};g.testAllProps=p,y.addTest("csstransforms3d",(function(){var e=!!p("perspective","1px",!0),t=y._config.usePrefixes;if(e&&(!t||"webkitPerspective"in w.style)){var i;y.supports?i="@supports (perspective: 1px)":(i="@media (transform-3d)",t&&(i+=",(-webkit-transform-3d)")),E("#modernizr{width:0;height:0}"+(i+="{#modernizr{width:7px;height:18px;margin:0;padding:0;border:0}}"),(function(t){e=7===t.offsetWidth&&18===t.offsetHeight}))}return e})),y.addTest("csstransitions",p("transition","all",!0)),y.addTest("objectfit",!!j("objectFit"),{aliases:["object-fit"]}),function(){var e,t,i,o,s,a;for(var l in v)if(v.hasOwnProperty(l)){if(e=[],(t=v[l]).name&&(e.push(t.name.toLowerCase()),t.options&&t.options.aliases&&t.options.aliases.length))for(i=0;i<t.options.aliases.length;i++)e.push(t.options.aliases[i].toLowerCase());for(o=n(t.fn,"function")?t.fn():t.fn,s=0;s<e.length;s++)1===(a=e[s].split(".")).length?y[a[0]]=o:(!y[a[0]]||y[a[0]]instanceof Boolean||(y[a[0]]=new Boolean(y[a[0]])),y[a[0]][a[1]]=o),m.push((o?"":"no-")+a.join("-"))}}(),function(e){var t=w.className,i=y._config.classPrefix||"";if(x&&(t=t.baseVal),y._config.enableJSClass){var n=new RegExp("(^|\\s)"+i+"no-js(\\s|$)");t=t.replace(n,"$1"+i+"js$2")}y._config.enableClasses&&(t+=" "+i+e.join(" "+i),x?w.className.baseVal=t:w.className=t)}(m),delete g.addTest,delete g.addAsyncTest;for(var H=0;H<y._q.length;H++)y._q[H]();e.Modernizr=y}(window,document),function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof module&&module.exports?module.exports=t():e.Colcade=t()}(window,(function(){function e(e,t){if((e=l(e))&&e.colcadeGUID){var i=n[e.colcadeGUID];return i.option(t),i}this.element=e,this.options={},this.option(t),this.create()}var t=e.prototype;t.option=function(e){this.options=function(e,t){for(var i in t)e[i]=t[i];return e}(this.options,e)};var i=0,n={};function o(t){var i=t.getAttribute("data-colcade").split(","),n={};i.forEach((function(e){var t=e.split(":"),i=t[0].trim(),o=t[1].trim();n[i]=o})),new e(t,n)}function s(e){var t=[];if(Array.isArray(e))t=e;else if(e&&"number"==typeof e.length)for(var i=0;i<e.length;i++)t.push(e[i]);else t.push(e);return t}function a(e,t){return s((t=t||document).querySelectorAll(e))}function l(e){return"string"==typeof e&&(e=document.querySelector(e)),e}return t.create=function(){this.errorCheck();var e=this.guid=++i;this.element.colcadeGUID=e,n[e]=this,this.reload(),this._windowResizeHandler=this.onWindowResize.bind(this),this._loadHandler=this.onLoad.bind(this),window.addEventListener("resize",this._windowResizeHandler),this.element.addEventListener("load",this._loadHandler,!0)},t.errorCheck=function(){var e=[];if(this.element||e.push("Bad element: "+this.element),this.options.columns||e.push("columns option required: "+this.options.columns),this.options.items||e.push("items option required: "+this.options.items),e.length)throw new Error("[Colcade error] "+e.join(". "))},t.reload=function(){this.updateColumns(),this.updateItems(),this.layout()},t.updateColumns=function(){this.columns=a(this.options.columns,this.element)},t.updateItems=function(){this.items=a(this.options.items,this.element)},t.getActiveColumns=function(){return this.columns.filter((function(e){return"none"!=getComputedStyle(e).display}))},t.layout=function(){this.activeColumns=this.getActiveColumns(),this._layout()},t._layout=function(){this.columnHeights=this.activeColumns.map((function(){return 0})),this.layoutItems(this.items)},t.layoutItems=function(e){e.forEach(this.layoutItem,this)},t.layoutItem=function(e){var t=Math.min.apply(Math,this.columnHeights),i=this.columnHeights.indexOf(t);this.activeColumns[i].appendChild(e),this.columnHeights[i]+=e.offsetHeight||1},t.append=function(e){var t=this.getQueryItems(e);this.items=this.items.concat(t),this.layoutItems(t)},t.prepend=function(e){var t=this.getQueryItems(e);this.items=t.concat(this.items),this._layout()},t.getQueryItems=function(e){e=s(e);var t=document.createDocumentFragment();return e.forEach((function(e){t.appendChild(e)})),a(this.options.items,t)},t.measureColumnHeight=function(e){var t=this.element.getBoundingClientRect();this.activeColumns.forEach((function(i,n){if(!e||i.contains(e)){var o=i.lastElementChild.getBoundingClientRect();this.columnHeights[n]=o.bottom-t.top}}),this)},t.onWindowResize=function(){clearTimeout(this.resizeTimeout),this.resizeTimeout=setTimeout(function(){this.onDebouncedResize()}.bind(this),100)},t.onDebouncedResize=function(){var e=this.getActiveColumns(),t=e.length==this.activeColumns.length,i=!0;this.activeColumns.forEach((function(t,n){i=i&&t==e[n]})),t&&i||(this.activeColumns=e,this._layout())},t.onLoad=function(e){this.measureColumnHeight(e.target)},t.destroy=function(){this.items.forEach((function(e){this.element.appendChild(e)}),this),window.removeEventListener("resize",this._windowResizeHandler),this.element.removeEventListener("load",this._loadHandler,!0),delete this.element.colcadeGUID,delete n[this.guid]},function(e){if("complete"==document.readyState)return void e();document.addEventListener("DOMContentLoaded",e)}((function(){a("[data-colcade]").forEach(o)})),e.data=function(e){var t=(e=l(e))&&e.colcadeGUID;return t&&n[t]},e.makeJQueryPlugin=function(t){(t=t||window.jQuery)&&(t.fn.colcade=function(i){var n;return"string"==typeof i?function(e,i,n){var o;return e.each((function(e,s){var a=t.data(s,"colcade");if(a){var l=a[i].apply(a,n);o=void 0===o?l:o}})),void 0!==o?o:e}(this,i,Array.prototype.slice.call(arguments,1)):(n=i,this.each((function(i,o){var s=t.data(o,"colcade");s?(s.option(n),s.layout()):(s=new e(o,n),t.data(o,"colcade",s))})),this)})},e.makeJQueryPlugin(),e})),$((function(){var e=mobileCheck()?"touchstart":"click";$(".tx-dlf-morevolumes, .tx-dlf-hidevolumes").on(e,(function(e){$(this).parent().toggleClass("tx-dlf-volumes-open").find(".tx-dlf-volume").slideToggle()})),$("aside.sidebar .tx-dlf-search").parent().addClass("tx-dlf-enable-offcanvas").append('<div class="offcanvas-toggle" />'),$transition="all .3s ease-out",setTimeout((function(){$("aside.sidebar .tx-dlf-search").parent().css({"-webkit-transition":$transition,"-o-transition":$transition,transition:$transition})}),250),$(".offcanvas-toggle").on(e,(function(e){$(this).parent().toggleClass("open")}));$(".tx-dlf-collection-list").prepend('<li class="tx-dlf-collection-col col-1"></li><li class="tx-dlf-collection-col col-2"></li><li class="tx-dlf-collection-col col-3"></li>').append($(".tx-dlf-collection-list-additionals")).randomize("li.tx-dlf-collection-item").colcade({columns:".tx-dlf-collection-col",items:".tx-dlf-collection-item"}),$(".tx-dlf-search-facets > ul > li").each((function(){var t=$(this).find("ul").children("li").length,i=$('html[lang="de-DE"]')[0]?"Zeige "+(t-5)+" weitere Facetten":"Show "+(t-5)+" more facets",n=$('html[lang="de-DE"]')[0]?"Letzten "+(t-5)+" Facetten ausblenden":"Hide "+(t-5)+" last facets";t>5&&($(this).find("ul li:gt(4)").hide(),$(this).append('<div class="facets-toggle">'+i+"</div>"),$(this).find(".facets-toggle").on(e,(function(){$(this).text($(this).parent().hasClass("facets-expanded")?i:n).parent().toggleClass("facets-expanded"),$(this).parent().find("ul li:gt(4)").slideToggle()})))}));var t=$('html[lang="de-DE"]')[0]?"Galerie":"Gallery",i=$('html[lang="de-DE"]')[0]?"Alphabetisch":"Alphabetical",n=$(".tx-dlf-collection-list li.tx-dlf-collection-item").get(),o=n;$(".tx-dlf-collection").prepend('<div class="tx-dlf-list-toggle-container"><span class="label active">'+t+'</span><div class="tx-dlf-list-toggle"><div class="toggle-state"></div></div><span class="label">'+i+"</span></div>"),$(".tx-dlf-list-toggle-container").on(e,(function(){$(this).hasClass("alphabetical")?($(".tx-dlf-collection-list li.order-label").remove(),$.each(o,(function(e,t){$(".tx-dlf-collection-list").append(t)})),$(".tx-dlf-collection-list").removeClass("alphabetical alphabetical-ready").colcade({columns:".tx-dlf-collection-col",items:".tx-dlf-collection-item"}),document.cookie="tx-dlf-galleryview-state=gallery; path=/"):($(".tx-dlf-collection-list").colcade("destroy").addClass("alphabetical"),sortAlphabetical(this,n),document.cookie="tx-dlf-galleryview-state=alphabetical; path=/"),$(this).toggleClass("alphabetical").find(".label").toggleClass("active")}));var s=$('html[lang="de-DE"]')[0]?"Erweiterte Suche<span> ausblenden</span>":"<span>Hide </span>Extended Search";$(".collections .tx-dlf-search form").append('<div class="extended-search-toggle">'+s+"</div>"),$(".extended-search-toggle").on(e,(function(){$(this).parent().toggleClass("extendend-search-active")})),$(".collections .tx-dlf-search").parent().addClass("search-form")})),$.fn.randomize=function(e){return(e?this.find(e):this).parent().each((function(){$(this).children(e).sort((function(){return Math.random()-.5})).detach().appendTo(this)})),this},sortAlphabetical=function(e,t){t.sort((function(e,t){var i=$(e).find("h4").text(),n=$(t).find("h4").text();return i<n?-1:i>n?1:0}));var i,n=!1;$.each(t,(function(e,t){$(".tx-dlf-collection-list").append(t),currentFirstChar=$(this).find("h4").text().charAt(0),i!==currentFirstChar&&isNaN(currentFirstChar)&&$(this).before('<li class="order-label"><div class="order-label-value">'+$(this).find("h4").text().charAt(0)+"</div></li>"),isNaN(currentFirstChar)||n||($(this).before('<li class="order-label"><div class="order-label-value">0–9</div></li>'),n=!0),i=$(this).find("h4").text().charAt(0),currentFirstChar=void 0})),window.setTimeout((function(){$(".tx-dlf-collection-list").addClass("alphabetical-ready")}),100)};