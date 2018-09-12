!function(){
"object"!=typeof JSON&&(window.JSON={
stringify:function(){
return"";
},
parse:function(){
return{};
}
});
var e=function(){
!function(){
var o={},e={},t={};
o.COMBO_UNLOAD=0,o.COMBO_LOADING=1,o.COMBO_LOADED=2;
var n=function(o,t,n){
if(!e[o]){
e[o]=n;
for(var r=3;r--;)try{
moon.setItem(moon.prefix+o,n.toString()),moon.setItem(moon.prefix+o+"_ver",moon_map[o]);
break;
}catch(i){
moon.clear();
}
}
},r=function(o){
if(!o||!e[o])return null;
var n=e[o];
return"function"!=typeof n||t[o]||(n=e[o]=n(r),t[o]=!0),n;
};
o.combo_status=o.COMBO_UNLOAD,o.run=function(){
var e=o.run.info,t=e&&e[0],n=e&&e[1];
if(t&&o.combo_status==o.COMBO_LOADED){
var i=r(t);
n&&n(i);
}
},o.use=function(e,t){
o.run.info=[e,t],o.run();
},window.define=n,window.seajs=o;
}(),function(){
function o(o){
var e="; "+document.cookie,t=e.split("; "+o+"=");
return 2==t.length?t.pop().split(";").shift():void 0;
}
window.__consoleList=[];
for(var e=window.console,t=function(o){
return function(){
var t=arguments;
window.__consoleList.push({
type:o,
msg:t,
time:+new Date
}),e&&e[o]&&e[o].apply(e,t);
};
},n=["log","info","error","warn","debug"],r={},i=0,a=n.length;a>i;++i){
var c=n[i];
r[c]=t(c);
}
if(window.console=r,window._console=e,window.localStorage&&window.__DEBUGINFO){
var s=o("DEBUG_SWITCH"),l=window.__DEBUGINFO;
if(("1"==s||-1!=location.href.indexOf("moon_debug=1"))&&l.js){
window.__moondebug=!0;
var u=document.createElement("script");
u.src=l.js,u.type="text/javascript",u.async=!0;
var f=document.head||document.getElementsByTagName("head")[0];
f.appendChild(u);
}
}
}(),function(){
function e(o){
return"[object Array]"===Object.prototype.toString.call(o);
}
function t(o){
return"[object Object]"===Object.prototype.toString.call(o);
}
function n(e){
var t=e.stack||e.toString()||"";
try{
t=t.replace(/http(s)?:\/\/res\.wx\.qq\.com/g,"");
for(var n=/\/([^.]+)\/js\/(\S+?)\.js(\,|:)?/g;n.test(t);)t=t.replace(n,"$2$3");
}catch(e){
t=e.stack?e.stack:"";
}
var r=[];
for(o in u)u.hasOwnProperty(o)&&r.push(o+":"+u[o]);
return r.push("STK:"+t.replace(/\n/g,"")),r.join("|");
}
function r(o){
if(!o){
var e=window.onerror;
window.onerror=function(){},o=setTimeout(function(){
window.onerror=e,o=null;
},50);
}
}
function i(o){
var e;
if(window.ActiveXObject)try{
e=new ActiveXObject("Msxml2.XMLHTTP");
}catch(t){
try{
e=new ActiveXObject("Microsoft.XMLHTTP");
}catch(n){
e=!1;
}
}else window.XMLHttpRequest&&(e=new XMLHttpRequest);
e&&(e.open("POST",location.protocol+"//mp.weixin.qq.com/mp/jsmonitor?",!0),e.setRequestHeader("cache-control","no-cache"),
e.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8"),
e.setRequestHeader("X-Requested-With","XMLHttpRequest"),e.send(o));
}
function a(o){
return function(e,t){
if("string"==typeof e)try{
e=new Function(e);
}catch(n){
throw n;
}
var i=[].slice.call(arguments,2),a=e;
return e=function(){
try{
return a.apply(this,i.length&&i||arguments);
}catch(o){
throw o.stack&&console&&console.error&&console.error("[TryCatch]"+o.stack),c&&window.__moon_report&&(window.__moon_report([{
offset:_,
log:"timeout_error;host:"+top.location.host,
e:o
}]),r(w)),o;
}
},o(e,t);
};
}
var c,s,l,u,f,w,d=/MicroMessenger/i.test(navigator.userAgent),m=window.define,p=0,_=9,g=10;
window.__initCatch=function(o){
c=o.idkey,s=o.startKey||0,l=o.limit||1,u=o.reportOpt||"",f=o.extinfo||"";
},window.__moon_report=function(o){
if(/mp\.weixin\.qq\.com/.test(location.href)&&!(Math.random()>.5)&&d&&top==window&&(t(o)&&(o=[o]),
e(o)&&""!=c)){
for(var r="",a=[],u=[],f=[],w=[],m=0;m<o.length;m++){
var p=o[m]||{};
if(!(p.offset>l)&&"number"==typeof p.offset){
var _=s+p.offset;
a[m]="[moon]"+c+"_"+_+";"+p.log+";"+n(p.e||{})||"",u[m]=_,f[m]=1;
}
}
for(var g=0;g<u.length;g++)w[g]=c+"_"+u[g]+"_"+f[g],r=r+"&log"+g+"="+a[g];
w.length>0&&i("idkey="+w.join(";")+"&lc="+a.length+r);
}
},window.setTimeout=a(window.setTimeout),window.setInterval=a(window.setInterval);
var h=window.navigator.userAgent;
if((/ip(hone|ad|od)/i.test(h)||/android/i.test(h))&&!/windows phone/i.test(h)&&window.localStorage&&window.localStorage.setItem){
var v=window.localStorage.setItem,y=0;
window.localStorage.setItem=function(o,e){
if(!(y>=10))try{
v.call(window.localStorage,o,e);
}catch(t){
t.stack&&console&&console.error&&console.error("[TryCatch]"+t.stack),window.__moon_report([{
offset:g,
log:"localstorage_error;"+t.toString(),
e:t
}]),y++,y>=3&&window.moon&&window.moon.clear&&moon.clear();
}
};
}
window.seajs&&m&&(window.define=function(){
for(var o,e=[],t=0,n=arguments.length;n>t;t++){
var i=o=arguments[t];
"function"==typeof o&&(o=function(){
try{
return i.apply(this,arguments);
}catch(o){
throw o.stack&&console&&console.error&&console.error("[TryCatch]"+o.stack),c&&window.__moon_report&&(window.__moon_report([{
offset:p,
log:"define_error",
e:o
}]),r(w)),o;
}
},o.toString=function(o){
return function(){
return o.toString();
};
}(arguments[t])),e.push(o);
}
return m.apply(this,e);
});
}(),function(o){
function e(o,e,n){
if("object"==typeof o){
var r=Object.prototype.toString.call(o).replace(/^\[object (.+)\]$/,"$1");
if(n=n||o,"Array"==r){
for(var i=0,a=o.length;a>i;++i)if(e.call(n,o[i],i,o)===!1)return;
}else{
if("Object"!==r&&t!=o)throw"unsupport type";
if(t==o){
for(var i=o.length-1;i>=0;i--){
var c=t.key(i),s=t.getItem(c);
if(e.call(n,s,c,o)===!1)return;
}
return;
}
for(var i in o)if(o.hasOwnProperty(i)&&e.call(n,o[i],i,o)===!1)return;
}
}
}
var t=o.localStorage,n=document.head||document.getElementsByTagName("head")[0],r=1,i={
prefix:"__MOON__",
loaded:[],
unload:[],
hit_num:0,
mod_num:0,
version:1003,
init:function(){
i.loaded=[],i.unload=[];
var n,r,a;
if(t){
var c="_moon_ver_key_",s=t.getItem(c);
s!=i.version&&(i.clear(),t.setItem(c,i.version));
}
if(-1!=location.search.indexOf("no_moon=1")&&i.clear(),t){
var l=1*t.getItem(i.prefix+"clean_time"),u=+new Date;
if(u-l>=1296e6){
i.clear();
try{
!!t&&t.setItem(i.prefix+"clean_time",+new Date);
}catch(f){}
}
}
e(moon_map,function(e,c){
if(r=i.prefix+c,a=!!e&&e.replace(/^http(s)?:\/\/res.wx.qq.com/,""),n=!!t&&t.getItem(r),
version=!!t&&(t.getItem(r+"_ver")||"").replace(/^http(s)?:\/\/res.wx.qq.com/,""),
i.mod_num++,n&&a==version)try{
var s="//# sourceURL="+c+"\n//@ sourceURL="+c;
o.eval.call(o,'define("'+c+'",[],'+n+")"+s),i.hit_num++;
}catch(l){
i.unload.push(a.replace(/^http(s)?:\/\/res.wx.qq.com/,""));
}else i.unload.push(a.replace(/^http(s)?:\/\/res.wx.qq.com/,""));
}),i.load(i.genUrl());
},
genUrl:function(){
var o=i.unload;
if(!o||o.length<=0)return[];
for(var e,t,n="",r=[],a={},c=-1!=location.search.indexOf("no_moon=2"),s=0,l=o.length;l>s;++s)/^\/(.*?)\//.test(o[s]),
RegExp.$1&&(t=RegExp.$1,n=a[t],n?(e=n+","+o[s],e.length>1e3||c?(r.push(n+"?v="+i.version),
n=location.protocol+"//res.wx.qq.com"+o[s],a[t]=n):(n=e,a[t]=n)):(n=location.protocol+"//res.wx.qq.com"+o[s],
a[t]=n));
for(var u in a)a.hasOwnProperty(u)&&r.push(a[u]);
return r;
},
load:function(o){
if(!o||o.length<=0)return seajs.combo_status=seajs.COMBO_LOADED,void seajs.run();
seajs.combo_status=seajs.COMBO_LOADING;
var t=0;
e(o,function(e){
i.request(e,1,function(){
t++,t==o.length&&(seajs.combo_status=seajs.COMBO_LOADED,seajs.run());
});
});
},
request:function(o,e,t){
if(o){
e=e||0;
var a=document.createElement("script");
a.src=o,a.type="text/javascript",a.async=!0,a.onerror=function(t){
if(e>=0)i.request(o,e);else if(window.__moon_report){
var n=new Error(t);
window.__moon_report([{
offset:r,
log:"load_script_error: "+o,
e:n
}]);
}
},"undefined"!=typeof moon_crossorigin&&moon_crossorigin&&a.setAttribute("crossorigin",!0),
a.onload=a.onreadystatechange=function(){
!a||a.readyState&&!/loaded|complete/.test(a.readyState)||(a.onload=a.onreadystatechange=null,
"function"==typeof t&&t());
},e--,n.appendChild(a);
}
},
setItem:function(o,e){
!!t&&t.setItem(o,e);
},
clear:function(){
t&&e(t,function(o,e){
~e.indexOf(i.prefix)&&t.removeItem(e);
});
}
};
window.moon=i;
}(window),window.moon.init();
};
e(),moon.setItem(moon.prefix+"biz_wap/moon.js",e.toString());
}();