
(function(){var root=(typeof self=='object'&&self.self==self&&self)||(typeof global=='object'&&global.global==global&&global)||this||{};Function.prototype.bind=Function.prototype.bind||function(context){if(typeof this!=="function"){throw new Error("Function.prototype.bind - what is trying to be bound is not callable");}
var self=this;var args=Array.prototype.slice.call(arguments,1);var fNOP=function(){};var fBound=function(){var bindArgs=Array.prototype.slice.call(arguments);self.apply(this instanceof fNOP?this:context,args.concat(bindArgs));}
fNOP.prototype=this.prototype;fBound.prototype=new fNOP();return fBound;}
var util={extend:function(target){for(var i=1,len=arguments.length;i<len;i++){for(var prop in arguments[i]){if(arguments[i].hasOwnProperty(prop)){target[prop]=arguments[i][prop]}}}
return target},addEvent:function(elem,type,fn){if(document.addEventListener){elem.addEventListener(type,fn,false);return fn;}else if(document.attachEvent){var bound=function(){return fn.apply(elem,arguments)}
elem.attachEvent('on'+type,bound);return bound;}},removeEvent:function(elem,type,fn){if(document.removeEventListener){elem.removeEventListener(type,fn,false)}
else{elem.detachEvent("on"+type,fn)}}}
function Lazy(opts){this.opts=util.extend({},this.constructor.defaultOpts,opts)
this.init();}
Lazy.VERSION='1.0.0';Lazy.defaultOpts={delay:250,useDebounce:false}
var proto=Lazy.prototype;proto.init=function(){this.calulateView();this.bindScrollEvent();};proto.calulateView=function(){this.view={top:0-(parseInt(this.opts.top,10)||0),bottom:(root.innerHeight||document.documentElement.clientHeight)+(parseInt(this.opts.bottom,10)||0),left:0-(parseInt(this.opts.left,10)||0),right:(root.innerWidth||document.documentElement.clientWidth)+(parseInt(this.opts.right,10)||0)}};proto.bindScrollEvent=function(){var scrollEvent=util.addEvent(root,'scroll',this.handleLazyLoad.bind(this))
var loadEvent=util.addEvent(root,'load',this.handleLazyLoad.bind(this))
this.event={scrollEvent:scrollEvent,loadEvent:loadEvent}};var timer=null;proto.handleLazyLoad=function(){var self=this;if(!this.opts.useDebounce&&!!timer){return;}
clearTimeout(timer);timer=setTimeout(function(){timer=null;self.render()},this.opts.delay);};proto.isHidden=function(element){return(element.offsetParent===null);};proto.checkInView=function(element){if(this.isHidden(element)){return false;}
var rect=element.getBoundingClientRect();return(rect.right>=this.view.left&&rect.bottom>=this.view.top&&rect.left<=this.view.right&&rect.top<=this.view.bottom);};proto.render=function(){var nodes=document.querySelectorAll('[data-image], [data-lazy-background]');var length=nodes.length;for(var i=0;i<length;i++){elem=nodes[i];if(this.checkInView(elem)){if(elem.getAttribute('data-lazy-background')!==null){elem.style.backgroundImage='url('+elem.getAttribute('data-lazy-background')+')';}else if(elem.src!==(src=elem.getAttribute('data-image'))){elem.src=src;}
elem.removeAttribute('data-image');elem.removeAttribute('data-lazy-background');if(this.opts.onload&&typeof this.opts.onload==='function'){this.opts.onload(elem);}}}
if(!length){this.unbindScrollEvent();}};proto.unbindScrollEvent=function(){util.removeEvent(root,'scroll',this.event.scrollEvent)
util.removeEvent(root,'load',this.event.loadEvent)};if(typeof exports!='undefined'&&!exports.nodeType){if(typeof module!='undefined'&&!module.nodeType&&module.exports){exports=module.exports=Lazy;}
exports.Lazy=Lazy;}else{root.Lazy=Lazy;}}());