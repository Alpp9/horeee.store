!function(t,e){"object"==typeof exports&&"object"==typeof module?module.exports=e():"function"==typeof define&&define.amd?define([],e):"object"==typeof exports?exports.GPickr=e():t.GPickr=e()}(window,(function(){return function(t){var e={};function o(n){if(e[n])return e[n].exports;var i=e[n]={i:n,l:!1,exports:{}};return t[n].call(i.exports,i,i.exports,o),i.l=!0,i.exports}return o.m=t,o.c=e,o.d=function(t,e,n){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},o.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var i in t)o.d(n,i,function(e){return t[e]}.bind(null,i));return n},o.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="dist",o(o.s=1)}([function(t,e,o){
/*! Pickr 1.4.5 MIT | https://github.com/Simonwep/pickr */
window,t.exports=function(t){var e={};function o(n){if(e[n])return e[n].exports;var i=e[n]={i:n,l:!1,exports:{}};return t[n].call(i.exports,i,i.exports,o),i.l=!0,i.exports}return o.m=t,o.c=e,o.d=function(t,e,n){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},o.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var i in t)o.d(n,i,function(e){return t[e]}.bind(null,i));return n},o.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="",o(o.s=1)}([function(t){t.exports=JSON.parse('{"a":"1.4.5"}')},function(t,e,o){"use strict";o.r(e);var n={};function i(t,e){var o=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),o.push.apply(o,n)}return o}function r(t){for(var e=1;e<arguments.length;e++){var o=null!=arguments[e]?arguments[e]:{};e%2?i(o,!0).forEach((function(e){s(t,e,o[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(o)):i(o).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(o,e))}))}return t}function s(t,e,o){return e in t?Object.defineProperty(t,e,{value:o,enumerable:!0,configurable:!0,writable:!0}):t[e]=o,t}o.r(n),o.d(n,"on",(function(){return c})),o.d(n,"off",(function(){return a})),o.d(n,"createElementFromString",(function(){return p})),o.d(n,"removeAttribute",(function(){return u})),o.d(n,"createFromTemplate",(function(){return h})),o.d(n,"eventPath",(function(){return d})),o.d(n,"resolveElement",(function(){return f})),o.d(n,"adjustableInputNumbers",(function(){return m}));const c=l.bind(null,"addEventListener"),a=l.bind(null,"removeEventListener");function l(t,e,o,n,i={}){e instanceof HTMLCollection||e instanceof NodeList?e=Array.from(e):Array.isArray(e)||(e=[e]),Array.isArray(o)||(o=[o]);for(const s of e)for(const e of o)s[t](e,n,r({capture:!1},i));return Array.prototype.slice.call(arguments,1)}function p(t){const e=document.createElement("div");return e.innerHTML=t.trim(),e.firstElementChild}function u(t,e){const o=t.getAttribute(e);return t.removeAttribute(e),o}function h(t){return function t(e,o={}){const n=u(e,":obj"),i=u(e,":ref"),r=n?o[n]={}:o;i&&(o[i]=e);for(const o of Array.from(e.children)){const e=u(o,":arr"),n=t(o,e?{}:r);e&&(r[e]||(r[e]=[])).push(Object.keys(n).length?n:o)}return o}(p(t))}function d(t){let e=t.path||t.composedPath&&t.composedPath();if(e)return e;let o=t.target.parentElement;for(e=[t.target,o];o=o.parentElement;)e.push(o);return e.push(document,window),e}function f(t){return t instanceof Element?t:"string"==typeof t?t.split(/>>/g).reduce((t,e,o,n)=>(t=t.querySelector(e),o<n.length-1?t.shadowRoot:t),document):null}function m(t,e=(t=>t)){function o(o){const n=[.001,.01,.1][Number(o.shiftKey||2*o.ctrlKey)]*(o.deltaY<0?1:-1);let i=0,r=t.selectionStart;t.value=t.value.replace(/[\d.]+/g,(t,o)=>o<=r&&o+t.length>=r?(r=o,e(Number(t),n,i)):(i++,t)),t.focus(),t.setSelectionRange(r,r),o.preventDefault(),t.dispatchEvent(new Event("input"))}c(t,"focus",()=>c(window,"wheel",o,{passive:!1})),c(t,"blur",()=>a(window,"wheel",o))}var g=o(0);const{min:v,max:b,floor:_,round:y}=Math;function w(t,e,o){e/=100,o/=100;const n=_(t=t/360*6),i=t-n,r=o*(1-e),s=o*(1-i*e),c=o*(1-(1-i)*e),a=n%6;return[255*[o,s,r,r,c,o][a],255*[c,o,o,s,r,r][a],255*[r,r,c,o,o,s][a]]}function S(t,e,o){const n=(2-(e/=100))*(o/=100)/2;return 0!==n&&(e=1===n?0:n<.5?e*o/(2*n):e*o/(2-2*n)),[t,100*e,100*n]}function C(t,e,o){let n,i,r;const s=v(t/=255,e/=255,o/=255),c=b(t,e,o),a=c-s;if(0===a)n=i=0;else{i=a/c;const r=((c-t)/6+a/2)/a,s=((c-e)/6+a/2)/a,l=((c-o)/6+a/2)/a;t===c?n=l-s:e===c?n=1/3+r-l:o===c&&(n=2/3+s-r),n<0?n+=1:n>1&&(n-=1)}return[360*n,100*i,100*(r=c)]}function k(t,e,o,n){return e/=100,o/=100,[...C(255*(1-v(1,(t/=100)*(1-(n/=100))+n)),255*(1-v(1,e*(1-n)+n)),255*(1-v(1,o*(1-n)+n)))]}function A(t,e,o){return e/=100,[t,2*(e*=(o/=100)<.5?o:1-o)/(o+e)*100,100*(o+e)]}function O(t){return C(...t.match(/.{2}/g).map(t=>parseInt(t,16)))}function j(t=0,e=0,o=0,n=1){const i=(t,e)=>(o=-1)=>e(~o?t.map(t=>Number(t.toFixed(o))):t),r={h:t,s:e,v:o,a:n,toHSVA(){const t=[r.h,r.s,r.v,r.a];return t.toString=i(t,t=>"hsva(".concat(t[0],", ").concat(t[1],"%, ").concat(t[2],"%, ").concat(r.a,")")),t},toHSLA(){const t=[...S(r.h,r.s,r.v),r.a];return t.toString=i(t,t=>"hsla(".concat(t[0],", ").concat(t[1],"%, ").concat(t[2],"%, ").concat(r.a,")")),t},toRGBA(){const t=[...w(r.h,r.s,r.v),r.a];return t.toString=i(t,t=>"rgba(".concat(t[0],", ").concat(t[1],", ").concat(t[2],", ").concat(r.a,")")),t},toCMYK(){const t=function(t,e,o){const n=w(t,e,o),i=n[0]/255,r=n[1]/255,s=n[2]/255;let c,a,l,p;return[100*(a=1===(c=v(1-i,1-r,1-s))?0:(1-i-c)/(1-c)),100*(l=1===c?0:(1-r-c)/(1-c)),100*(p=1===c?0:(1-s-c)/(1-c)),100*c]}(r.h,r.s,r.v);return t.toString=i(t,t=>"cmyk(".concat(t[0],"%, ").concat(t[1],"%, ").concat(t[2],"%, ").concat(t[3],"%)")),t},toHEXA(){const t=function(t,e,o){return w(t,e,o).map(t=>y(t).toString(16).padStart(2,"0"))}(r.h,r.s,r.v),e=r.a>=1?"":Number((255*r.a).toFixed(0)).toString(16).toUpperCase().padStart(2,"0");return e&&t.push(e),t.toString=()=>"#".concat(t.join("").toUpperCase()),t},clone:()=>j(r.h,r.s,r.v,r.a)};return r}const x=t=>Math.max(Math.min(t,1),0);function L(t){const e={options:Object.assign({lock:null,onchange:()=>0,onstop:()=>0},t),_keyboard(t){const{type:n,key:i}=t;if(document.activeElement===o.wrapper){const{lock:o}=e.options,r="ArrowUp"===i,s="ArrowRight"===i,c="ArrowDown"===i,a="ArrowLeft"===i;if("keydown"===n&&(r||s||c||a)){let t=0,n=0;"v"===o?t=r||s?1:-1:"h"===o?t=r||s?-1:1:(n=r?-1:c?1:0,t=a?-1:s?1:0),e.update(x(e.cache.x+.01*t),x(e.cache.y+.01*n))}else i.startsWith("Arrow")&&(e.options.onstop(),t.preventDefault())}},_tapstart(t){c(document,["mouseup","touchend","touchcancel"],e._tapstop),c(document,["mousemove","touchmove"],e._tapmove),t.preventDefault(),e._tapmove(t)},_tapmove(t){const{options:{lock:n},cache:i}=e,{element:r,wrapper:s}=o,c=s.getBoundingClientRect();let a=0,l=0;if(t){const e=t&&t.touches&&t.touches[0];a=t?(e||t).clientX:0,l=t?(e||t).clientY:0,a<c.left?a=c.left:a>c.left+c.width&&(a=c.left+c.width),l<c.top?l=c.top:l>c.top+c.height&&(l=c.top+c.height),a-=c.left,l-=c.top}else i&&(a=i.x*c.width,l=i.y*c.height);"h"!==n&&(r.style.left="calc(".concat(a/c.width*100,"% - ").concat(r.offsetWidth/2,"px)")),"v"!==n&&(r.style.top="calc(".concat(l/c.height*100,"% - ").concat(r.offsetHeight/2,"px)")),e.cache={x:a/c.width,y:l/c.height};const p=x(a/s.offsetWidth),u=x(l/s.offsetHeight);switch(n){case"v":return o.onchange(p);case"h":return o.onchange(u);default:return o.onchange(p,u)}},_tapstop(){e.options.onstop(),a(document,["mouseup","touchend","touchcancel"],e._tapstop),a(document,["mousemove","touchmove"],e._tapmove)},trigger(){e._tapmove()},update(t=0,o=0){const{left:n,top:i,width:r,height:s}=e.options.wrapper.getBoundingClientRect();"h"===e.options.lock&&(o=t),e._tapmove({clientX:n+r*t,clientY:i+s*o})},destroy(){const{options:t,_tapstart:o}=e;a([t.wrapper,t.element],"mousedown",o),a([t.wrapper,t.element],"touchstart",o,{passive:!1})}},{options:o,_tapstart:n,_keyboard:i}=e;return c([o.wrapper,o.element],"mousedown",n),c([o.wrapper,o.element],"touchstart",n,{passive:!1}),c(document,["keydown","keyup"],i),e}function P(t={}){t=Object.assign({onchange:()=>0,className:"",elements:[]},t);const e=c(t.elements,"click",e=>{t.elements.forEach(o=>o.classList[e.target===o?"add":"remove"](t.className)),t.onchange(e)});return{destroy:()=>a(...e)}}function E({el:t,reference:e,padding:o=8}){const n={start:"sme",middle:"mse",end:"ems"},i={top:"tbrl",right:"rltb",bottom:"btrl",left:"lrbt"},r=((t={})=>(e,o=t[e])=>{if(o)return o;const[n,i="middle"]=e.split("-"),r="top"===n||"bottom"===n;return t[e]={position:n,variant:i,isVertical:r}})();return{update(s){const{position:c,variant:a,isVertical:l}=r(s),p=e.getBoundingClientRect(),u=t.getBoundingClientRect(),h=t=>t?{t:p.top-u.height-o,b:p.bottom+o}:{r:p.right+o,l:p.left-u.width-o},d=t=>t?{s:p.left+p.width-u.width,m:-u.width/2+(p.left+p.width/2),e:p.left}:{s:p.bottom-u.height,m:p.bottom-p.height/2-u.height/2,e:p.bottom-p.height},f={};function m(e,o,n){const i="top"===n,r=i?u.height:u.width,s=window[i?"innerHeight":"innerWidth"];for(const i of e){const e=o[i],c=f[n]="".concat(e,"px");if(e>0&&e+r<s)return t.style[n]=c,!0}return!1}for(const t of[l,!l]){const e=m(i[c],h(t),t?"top":"left"),o=m(n[a],d(t),t?"left":"top");if(e&&o)return}t.style.left=f.left,t.style.top=f.top}}}var R=({components:t,strings:e,useAsButton:o,inline:n,appClass:i,theme:r,lockOpacity:s})=>{const c=t=>t?"":'style="display:none" hidden',a=h('\n      <div :ref="root" class="pickr">\n\n        '.concat(o?"":'<button type="button" :ref="button" class="pcr-button"></button>','\n\n        <div :ref="app" class="pcr-app ').concat(i||"",'" data-theme="').concat(r,'" ').concat(n?'style="position: unset"':"",' aria-label="color picker dialog" role="window">\n          <div class="pcr-selection" ').concat(c(t.palette),'>\n            <div :obj="preview" class="pcr-color-preview" ').concat(c(t.preview),'>\n              <button type="button" :ref="lastColor" class="pcr-last-color" aria-label="use previous color"></button>\n              <div :ref="currentColor" class="pcr-current-color"></div>\n            </div>\n\n            <div :obj="palette" class="pcr-color-palette">\n              <div :ref="picker" class="pcr-picker"></div>\n              <div :ref="palette" class="pcr-palette" tabindex="0" aria-label="color selection area" role="widget"></div>\n            </div>\n\n            <div :obj="hue" class="pcr-color-chooser" ').concat(c(t.hue),'>\n              <div :ref="picker" class="pcr-picker"></div>\n              <div :ref="slider" class="pcr-hue pcr-slider" tabindex="0" aria-label="hue selection slider" role="widget"></div>\n            </div>\n\n            <div :obj="opacity" class="pcr-color-opacity" ').concat(c(t.opacity),'>\n              <div :ref="picker" class="pcr-picker"></div>\n              <div :ref="slider" class="pcr-opacity pcr-slider" tabindex="0" aria-label="opacity selection slider" role="widget"></div>\n            </div>\n          </div>\n\n          <div class="pcr-swatches ').concat(t.palette?"":"pcr-last",'" :ref="swatches"></div> \n\n          <div :obj="interaction" class="pcr-interaction" ').concat(c(Object.keys(t.interaction).length),'>\n            <input :ref="result" class="pcr-result" type="text" spellcheck="false" ').concat(c(t.interaction.input),'>\n\n            <input :arr="options" class="pcr-type" data-type="HEXA" value="').concat(s?"HEX":"HEXA",'" type="button" ').concat(c(t.interaction.hex),'>\n            <input :arr="options" class="pcr-type" data-type="RGBA" value="').concat(s?"RGB":"RGBA",'" type="button" ').concat(c(t.interaction.rgba),'>\n            <input :arr="options" class="pcr-type" data-type="HSLA" value="').concat(s?"HSL":"HSLA",'" type="button" ').concat(c(t.interaction.hsla),'>\n            <input :arr="options" class="pcr-type" data-type="HSVA" value="').concat(s?"HSV":"HSVA",'" type="button" ').concat(c(t.interaction.hsva),'>\n            <input :arr="options" class="pcr-type" data-type="CMYK" value="CMYK" type="button" ').concat(c(t.interaction.cmyk),'>\n\n            <input :ref="save" class="pcr-save" value="').concat(e.save||"Save",'" type="button" ').concat(c(t.interaction.save),' aria-label="save and exit">\n            <input :ref="cancel" class="pcr-cancel" value="').concat(e.cancel||"Cancel",'" type="button" ').concat(c(t.interaction.cancel),' aria-label="cancel and exit">\n            <input :ref="clear" class="pcr-clear" value="').concat(e.clear||"Clear",'" type="button" ').concat(c(t.interaction.clear),' aria-label="clear and exit">\n          </div>\n        </div>\n      </div>\n    ')),l=a.interaction;return l.options.find(t=>!t.hidden&&!t.classList.add("active")),l.type=()=>l.options.find(t=>t.classList.contains("active")),a};function B(t,e,o){return e in t?Object.defineProperty(t,e,{value:o,enumerable:!0,configurable:!0,writable:!0}):t[e]=o,t}class D{constructor(t){B(this,"_initializingActive",!0),B(this,"_recalc",!0),B(this,"_color",j()),B(this,"_lastColor",j()),B(this,"_swatchColors",[]),B(this,"_eventListener",{init:[],save:[],hide:[],show:[],clear:[],change:[],changestop:[],cancel:[],swatchselect:[]}),this.options=t=Object.assign({appClass:null,theme:"classic",useAsButton:!1,padding:8,disabled:!1,comparison:!0,closeOnScroll:!1,outputPrecision:0,lockOpacity:!1,autoReposition:!0,container:"body",components:{interaction:{}},strings:{},swatches:null,inline:!1,sliders:null,default:"#42445a",defaultRepresentation:null,position:"bottom-middle",adjustableNumbers:!0,showAlways:!1,closeWithKey:"Escape"},t);const{swatches:e,components:o,theme:n,sliders:i,lockOpacity:r,padding:s}=t;["nano","monolith"].includes(n)&&!i&&(t.sliders="h"),o.interaction||(o.interaction={});const{preview:c,opacity:a,hue:l,palette:p}=o;o.opacity=!r&&a,o.palette=p||c||a||l,this._preBuild(),this._buildComponents(),this._bindEvents(),this._finalBuild(),e&&e.length&&e.forEach(t=>this.addSwatch(t));const{button:u,app:h}=this._root;this._nanopop=E({reference:u,padding:s,el:h}),u.setAttribute("role","button"),u.setAttribute("aria-label","toggle color picker dialog");const d=this;requestAnimationFrame((function e(){if(!h.offsetWidth&&h.parentElement!==t.container)return requestAnimationFrame(e);d.setColor(t.default),d._rePositioningPicker(),t.defaultRepresentation&&(d._representation=t.defaultRepresentation,d.setColorRepresentation(d._representation)),t.showAlways&&d.show(),d._initializingActive=!1,d._emit("init")}))}_preBuild(){const t=this.options;for(const e of["el","container"])t[e]=f(t[e]);this._root=R(t),t.useAsButton&&(this._root.button=t.el),t.container.appendChild(this._root.root)}_finalBuild(){const t=this.options,e=this._root;if(t.container.removeChild(e.root),t.inline){const o=t.el.parentElement;t.el.nextSibling?o.insertBefore(e.app,t.el.nextSibling):o.appendChild(e.app)}else t.container.appendChild(e.app);t.useAsButton?t.inline&&t.el.remove():t.el.parentNode.replaceChild(e.root,t.el),t.disabled&&this.disable(),t.comparison||(e.button.style.transition="none",t.useAsButton||(e.preview.lastColor.style.transition="none")),this.hide()}_buildComponents(){const t=this,e=this.options.components,o=(t.options.sliders||"v").repeat(2),[n,i]=o.match(/^[vh]+$/g)?o:[],r=()=>this._color||(this._color=this._lastColor.clone()),s={palette:L({element:t._root.palette.picker,wrapper:t._root.palette.palette,onstop:()=>t._emit("changestop",t),onchange(o,n){if(!e.palette)return;const i=r(),{_root:s,options:c}=t;t._recalc&&(i.s=100*o,i.v=100-100*n,i.v<0&&(i.v=0),t._updateOutput());const a=i.toRGBA().toString(0);this.element.style.background=a,this.wrapper.style.background="\n                        linear-gradient(to top, rgba(0, 0, 0, ".concat(i.a,"), transparent),\n                        linear-gradient(to left, hsla(").concat(i.h,", 100%, 50%, ").concat(i.a,"), rgba(255, 255, 255, ").concat(i.a,"))\n                    "),c.comparison?c.useAsButton||t._lastColor||(s.preview.lastColor.style.color=a):s.button.style.color=a;const l=i.toHEXA().toString();for(const e of t._swatchColors){const{el:t,color:o}=e;t.classList[l===o.toHEXA().toString()?"add":"remove"]("pcr-active")}s.preview.currentColor.style.color=a,t.options.comparison||s.button.classList.remove("clear")}}),hue:L({lock:"v"===i?"h":"v",element:t._root.hue.picker,wrapper:t._root.hue.slider,onstop:()=>t._emit("changestop",t),onchange(o){if(!e.hue||!e.palette)return;const n=r();t._recalc&&(n.h=360*o),this.element.style.backgroundColor="hsl(".concat(n.h,", 100%, 50%)"),s.palette.trigger()}}),opacity:L({lock:"v"===n?"h":"v",element:t._root.opacity.picker,wrapper:t._root.opacity.slider,onstop:()=>t._emit("changestop",t),onchange(o){if(!e.opacity||!e.palette)return;const n=r();t._recalc&&(n.a=Math.round(100*o)/100),this.element.style.background="rgba(0, 0, 0, ".concat(n.a,")"),s.palette.trigger()}}),selectable:P({elements:t._root.interaction.options,className:"active",onchange(e){t._representation=e.target.getAttribute("data-type").toUpperCase(),t._recalc&&t._updateOutput()}})};this._components=s}_bindEvents(){const{_root:t,options:e}=this,o=[c(t.interaction.clear,"click",()=>this._clearColor()),c([t.interaction.cancel,t.preview.lastColor],"click",()=>{this._emit("cancel",this),this.setHSVA(...(this._lastColor||this._color).toHSVA(),!0)}),c(t.interaction.save,"click",()=>{!this.applyColor()&&!e.showAlways&&this.hide()}),c(t.interaction.result,["keyup","input"],t=>{this.setColor(t.target.value,!0)&&!this._initializingActive&&this._emit("change",this._color),t.stopImmediatePropagation()}),c(t.interaction.result,["focus","blur"],t=>{this._recalc="blur"===t.type,this._recalc&&this._updateOutput()}),c([t.palette.palette,t.palette.picker,t.hue.slider,t.hue.picker,t.opacity.slider,t.opacity.picker],["mousedown","touchstart"],()=>this._recalc=!0)];if(!e.showAlways){const n=e.closeWithKey;o.push(c(t.button,"click",()=>this.isOpen()?this.hide():this.show()),c(document,"keyup",t=>this.isOpen()&&(t.key===n||t.code===n)&&this.hide()),c(document,["touchstart","mousedown"],e=>{this.isOpen()&&!d(e).some(e=>e===t.app||e===t.button)&&this.hide()},{capture:!0}))}if(e.adjustableNumbers){const e={rgba:[255,255,255,1],hsva:[360,100,100,1],hsla:[360,100,100,1],cmyk:[100,100,100,100]};m(t.interaction.result,(t,o,n)=>{const i=e[this.getColorRepresentation().toLowerCase()];if(i){const e=i[n],r=t+(e>=100?1e3*o:o);return r<=0?0:Number((r<e?r:e).toPrecision(3))}return t})}if(e.autoReposition&&!e.inline){let t=null;const n=this;o.push(c(window,["scroll","resize"],()=>{n.isOpen()&&(e.closeOnScroll&&n.hide(),null===t?(t=setTimeout(()=>t=null,100),requestAnimationFrame((function e(){n._rePositioningPicker(),null!==t&&requestAnimationFrame(e)}))):(clearTimeout(t),t=setTimeout(()=>t=null,100)))},{capture:!0}))}this._eventBindings=o}_rePositioningPicker(){const{options:t}=this;if(!t.inline){const{app:e}=this._root;matchMedia("(max-width: 576px)").matches?Object.assign(e.style,{margin:"auto",height:"".concat(e.getBoundingClientRect().height,"px"),top:0,bottom:0,left:0,right:0}):(Object.assign(e.style,{margin:null,right:null,top:null,bottom:null,left:null,height:null}),this._nanopop.update(t.position))}}_updateOutput(){const{_root:t,_color:e,options:o}=this;if(t.interaction.type()){const n="to".concat(t.interaction.type().getAttribute("data-type"));t.interaction.result.value="function"==typeof e[n]?e[n]().toString(o.outputPrecision):""}!this._initializingActive&&this._recalc&&this._emit("change",e)}_clearColor(t=!1){const{_root:e,options:o}=this;o.useAsButton||(e.button.style.color="rgba(0, 0, 0, 0.15)"),e.button.classList.add("clear"),o.showAlways||this.hide(),this._lastColor=null,this._initializingActive||t||(this._emit("save",null),this._emit("clear",this))}_parseLocalColor(t){const{values:e,type:o,a:n}=function(t){t=t.match(/^[a-zA-Z]+$/)?function(t){if("black"===t.toLowerCase())return"#000";const e=document.createElement("canvas").getContext("2d");return e.fillStyle=t,"#000"===e.fillStyle?null:e.fillStyle}(t):t;const e={cmyk:/^cmyk[\D]+([\d.]+)[\D]+([\d.]+)[\D]+([\d.]+)[\D]+([\d.]+)/i,rgba:/^((rgba)|rgb)[\D]+([\d.]+)[\D]+([\d.]+)[\D]+([\d.]+)[\D]*?([\d.]+|$)/i,hsla:/^((hsla)|hsl)[\D]+([\d.]+)[\D]+([\d.]+)[\D]+([\d.]+)[\D]*?([\d.]+|$)/i,hsva:/^((hsva)|hsv)[\D]+([\d.]+)[\D]+([\d.]+)[\D]+([\d.]+)[\D]*?([\d.]+|$)/i,hexa:/^#?(([\dA-Fa-f]{3,4})|([\dA-Fa-f]{6})|([\dA-Fa-f]{8}))$/i},o=t=>t.map(t=>/^(|\d+)\.\d+|\d+$/.test(t)?Number(t):void 0);let n;t:for(const i in e){if(!(n=e[i].exec(t)))continue;const r=t=>!!n[2]==("number"==typeof t);switch(i){case"cmyk":{const[,t,e,r,s]=o(n);if(t>100||e>100||r>100||s>100)break t;return{values:k(t,e,r,s),type:i}}case"rgba":{const[,,,t,e,s,c]=o(n);if(t>255||e>255||s>255||c<0||c>1||!r(c))break t;return{values:[...C(t,e,s),c],a:c,type:i}}case"hexa":{let[,t]=n;4!==t.length&&3!==t.length||(t=t.split("").map(t=>t+t).join(""));const e=t.substring(0,6);let o=t.substring(6);return o=o?parseInt(o,16)/255:void 0,{values:[...O(e),o],a:o,type:i}}case"hsla":{const[,,,t,e,s,c]=o(n);if(t>360||e>100||s>100||c<0||c>1||!r(c))break t;return{values:[...A(t,e,s),c],a:c,type:i}}case"hsva":{const[,,,t,e,s,c]=o(n);if(t>360||e>100||s>100||c<0||c>1||!r(c))break t;return{values:[t,e,s,c],a:c,type:i}}}}return{values:null,type:null}}(t),{lockOpacity:i}=this.options,r=void 0!==n&&1!==n;return e&&3===e.length&&(e[3]=void 0),{values:!e||i&&r?null:e,type:o}}_emit(t,...e){this._eventListener[t].forEach(t=>t(...e,this))}on(t,e){return"function"==typeof e&&"string"==typeof t&&t in this._eventListener&&this._eventListener[t].push(e),this}off(t,e){const o=this._eventListener[t];if(o){const t=o.indexOf(e);~t&&o.splice(t,1)}return this}addSwatch(t){const{values:e}=this._parseLocalColor(t);if(e){const{_swatchColors:t,_root:o}=this,n=j(...e),i=p('<button type="button" style="color: '.concat(n.toRGBA().toString(0),'" aria-label="color swatch"/>'));return o.swatches.appendChild(i),t.push({el:i,color:n}),this._eventBindings.push(c(i,"click",()=>{this.setHSVA(...n.toHSVA(),!0),this._emit("swatchselect",n),this._emit("change",n)})),!0}return!1}removeSwatch(t){const e=this._swatchColors[t];if(e){const{el:o}=e;return this._root.swatches.removeChild(o),this._swatchColors.splice(t,1),!0}return!1}applyColor(t=!1){const{preview:e,button:o}=this._root,n=this._color.toRGBA().toString(0);return e.lastColor.style.color=n,this.options.useAsButton||(o.style.color=n),o.classList.remove("clear"),this._lastColor=this._color.clone(),this._initializingActive||t||this._emit("save",this._color),this}destroy(){this._eventBindings.forEach(t=>a(...t)),Object.keys(this._components).forEach(t=>this._components[t].destroy())}destroyAndRemove(){this.destroy();const{root:t,app:e}=this._root;t.parentElement&&t.parentElement.removeChild(t),e.parentElement.removeChild(e),Object.keys(this).forEach(t=>this[t]=null)}hide(){return this._root.app.classList.remove("visible"),this._emit("hide",this),this}show(){return this.options.disabled||(this._root.app.classList.add("visible"),this._rePositioningPicker(),this._emit("show",this)),this}isOpen(){return this._root.app.classList.contains("visible")}setHSVA(t=360,e=0,o=0,n=1,i=!1){const r=this._recalc;if(this._recalc=!1,t<0||t>360||e<0||e>100||o<0||o>100||n<0||n>1)return!1;this._color=j(t,e,o,n);const{hue:s,opacity:c,palette:a}=this._components;return s.update(t/360),c.update(n),a.update(e/100,1-o/100),i||this.applyColor(),r&&this._updateOutput(),this._recalc=r,!0}setColor(t,e=!1){if(null===t)return this._clearColor(e),!0;const{values:o,type:n}=this._parseLocalColor(t);if(o){const t=n.toUpperCase(),{options:i}=this._root.interaction,r=i.find(e=>e.getAttribute("data-type")===t);if(r&&!r.hidden)for(const t of i)t.classList[t===r?"add":"remove"]("active");return this.setColorRepresentation(t),this.setHSVA(...o,e)}return!1}setColorRepresentation(t){return t=t.toUpperCase(),!!this._root.interaction.options.find(e=>e.getAttribute("data-type").startsWith(t)&&!e.click())}getColorRepresentation(){return this._representation}getColor(){return this._color}getSelectedColor(){return this._lastColor}getRoot(){return this._root}disable(){return this.hide(),this.options.disabled=!0,this._root.button.classList.add("disabled"),this}enable(){return this.options.disabled=!1,this._root.button.classList.remove("disabled"),this}}D.utils=n,D.libs={HSVaColor:j,Moveable:L,Nanopop:E,Selectable:P},D.create=t=>new D(t),D.version=g.a,e.default=D}]).default},function(t,e,o){"use strict";o.r(e);var n=o(0),i=o.n(n),r=()=>n.utils.createFromTemplate(`\n<div class="gpickr" :ref="root">\n\n    <div :ref="pickr"></div>\n    <div :obj="gradient" class="gpcr-interaction">\n    <div :ref="result" class="gpcr-result">\n        \n         <div :ref="mode" data-mode="linear" class="gpcr-mode"></div>\n\n            <div :ref="angle" class="gpcr-angle">\n                <div :ref="arrow"></div>\n            </div>\n\n            <div :ref="pos" class="gpcr-pos">\n                ${["tl","tm","tr","l","m","r","bl","bm","br"].map(t=>`<div data-pos="${t}"></div>`).join("")}\n            </div>\n        </div>\n\n        <div :obj="stops" class="gpcr-stops">\n            <div :ref="preview" class="gpcr-stop-preview"></div>\n            <div :ref="markers" class="gpcr-stop-marker"></div>\n        </div>\n       \n    </div>\n\n</div>\n`),s=t=>{const e=t.touches&&t.touches[0]||t;return{tap:e,x:e.clientX,y:e.clientY,target:e.target}};let c=document.createElement("p");function*a(t,e,o=-1){for(let n;n=e.exec(t);)yield~o?n[o].trim():n.map(t=>t.trim())}function l(t,e,o=-1){const n=t.match(e);return n?~o?n[o]:n:null}function p(t){const e="rgba(0, 0, 0, 0)";if(c.style.color=e,t===e)return t;c.style.color=t;const o=getComputedStyle(c).color;return o===e?null:o}function u(t){if(!(t=function(t){return c.style.backgroundImage=t,getComputedStyle(c).backgroundImage}(t)))return null;const[,e,o]=t.match(/^(\w+)-gradient\((.*)\)$/i)||[];if(!e||!o)return null;const n=[...a(o,/(rgba?\(.*?\)|#?\w+)(.*?)(?=,|$)/gi)],i=[];let r=null,s=null;for(let t=0;t<n.length;t++){const[e,o,c]=n[t],a=p(o),u=c.split(/\s+/g).map(t=>l(t,/^-?(\d*(\.\d+)?)%$/,1)).filter(Boolean).map(Number);if(!u.length&&a)i.push({loc:null,color:a});else if(u.length)for(const t of u)i.push({loc:t,color:a||s});else r||(r=e);s=a||s}i[i.length-1].loc||(i[i.length-1].loc=100);for(let t=0;t<i.length;t++){const e=i[t];if(!e.loc)if(t){let o=2,n=t+1;for(;n<i.length&&!i[n].loc;n++)o++;e.loc=i[t-1].loc+(i[n].loc-i[t-1].loc)/o}else e.loc=0}return{str:t,type:e,modifier:r,stops:i}}var h=t=>{document.body.appendChild(c);const e=u(t);return document.body.removeChild(c),e},d={angleToDegrees(t){const e=t.trim().toLowerCase().match(/^(-?\d*(\.\d+)?)(deg|rad|grad|turn)$/i);if(!e)return null;const o=Number(e[1]);switch(e[3]){case"deg":return o;case"rad":return 180/Math.PI*o;case"grad":return o/400*360;case"turn":return 360*o}return null}};function f(t,e,o){return e in t?Object.defineProperty(t,e,{value:o,enumerable:!0,configurable:!0,writable:!0}):t[e]=o,t}const{utils:m}=i.a,{on:g,off:v}=m;class b{constructor(t){f(this,"_stops",[]),f(this,"_angle",0),f(this,"_angles",[{angle:0,name:"to top"},{angle:90,name:"to right"},{angle:180,name:"to bottom"},{angle:270,name:"to left"}]),f(this,"_direction","circle at center"),f(this,"_directions",[{pos:"tl",css:"circle at left top"},{pos:"tm",css:"circle at center top"},{pos:"tr",css:"circle at right top"},{pos:"r",css:"circle at right"},{pos:"m",css:"circle at center"},{pos:"l",css:"circle at left"},{pos:"br",css:"circle at right bottom"},{pos:"bm",css:"circle at center bottom"},{pos:"bl",css:"circle at left bottom"}]),f(this,"_focusedStop",null),f(this,"_mode","linear"),f(this,"_modes",["linear","radial"]),f(this,"_root",null),f(this,"_eventListener",{init:[],change:[]}),t=Object.assign({stops:[]},t),this._root=r(t),t.el=t.el.split(/>>/g).reduce((t,e,o,n)=>(t=t.querySelector(e),o<n.length-1?t.shadowRoot:t),document),t.el.parentElement.replaceChild(this._root.root,t.el),this._pickr=i.a.create({el:this._root.pickr,theme:"nano",inline:!0,useAsButton:!0,showAlways:!0,default:"#28a85a",defaultRepresentation:"HEXA",components:{palette:!0,preview:!0,opacity:!0,hue:!0,interaction:{hex:!0,rgba:!0,cmyk:!0,input:!0}}}).on("change",t=>{this._focusedStop&&(this._focusedStop.color=t.toRGBA().toString(0),this._render())}).on("init",()=>{for(const[e,o]of t.stops)this.addStop(e,o,!0);this._bindEvents(),this._emit("init",this)})}_bindEvents(){const{gradient:t}=this._root;g(t.mode,["mousedown","touchstart"],t=>{const e=this._modes.indexOf(this._mode)+1;this._mode=this._modes[e===this._modes.length?0:e],this._render(!0),t.stopPropagation()}),g(t.stops.preview,"click",t=>{this.addStop(this._pickr.getColor().toRGBA().toString(),this._resolveColorStopPosition(t.pageX))}),g(t.result,["mousedown","touchstart"],e=>{if(e.preventDefault(),"linear"!==this._mode)return;t.angle.classList.add("gpcr-active");const o=g(window,["mousemove","touchmove"],e=>{const{x:o,y:n}=s(e),i=t.angle.getBoundingClientRect(),r=i.left+i.width/2,c=i.top+i.height/2,a=Math.atan2(o-r,n-c)-Math.PI,l=Math.abs(180*a/Math.PI),p=[1,2,4][Number(e.shiftKey||2*e.ctrlKey)];this.setLinearAngle(l-l%(45/p))}),n=g(window,["mouseup","touchend","touchcancel"],()=>{t.angle.classList.remove("gpcr-active"),v(...o),v(...n)})}),g(t.pos,["mousedown","touchstart"],t=>{const e=t.target.getAttribute("data-pos"),o=this._directions.find(t=>t.pos===e);this.setRadialPosition(o&&o.css||this._direction)})}_render(t=!1){const{stops:{preview:e},result:o,arrow:n,angle:i,pos:r,mode:s}=this._root.gradient,{_stops:c,_mode:a,_angle:l}=this;c.sort((t,e)=>t.loc-e.loc);for(const{color:t,el:e,loc:o}of c)Object.assign(e.style,{left:`${100*o}%`,color:t});n.style.transform=`rotate(${l-90}deg)`,e.style.background=`linear-gradient(to right, ${this.getStops().toString("linear")})`,o.style.background=this.getGradient().toString(),r.style.opacity="radial"===a?"":"0",r.style.visibility="radial"===a?"":"hidden",i.style.opacity="linear"===a?"":"0",i.style.visibility="linear"===a?"":"hidden",s.setAttribute("data-mode",a),!t&&this._emit("change",this)}_resolveColorStopPosition(t){const{markers:e}=this._root.gradient.stops,o=e.getBoundingClientRect();let n=(t-o.left)/o.width;return n<0&&(n=0),n>1&&(n=1),n}addStop(t,e=.5,o=!1){const{markers:n}=this._root.gradient.stops,i=m.createElementFromString('<div title="Drag down to remove the stop point." class="gpcr-marker"></div>');n.appendChild(i),this._pickr.setColor(t),t=this._pickr.getColor().toRGBA().toString(0);const r={el:i,loc:e,color:t,listener:g(i,["mousedown","touchstart"],t=>{t.preventDefault();const e=n.getBoundingClientRect();this._pickr.setColor(r.color),this._focusedStop&&this._focusedStop.el.classList.remove("active"),this._focusedStop=r,this._focusedStop.el.classList.add("active");let o=!1;const c=g(window,["mousemove","touchmove"],t=>{const{x:n,y:c}=s(t),a=Math.abs(c-e.y);o=a>50&&this._stops.length>2,i.style.opacity=o?"0":"1",o||(r.loc=this._resolveColorStopPosition(n),this._render())}),a=g(window,["mouseup","touchend","touchcancel"],()=>{v(...c),v(...a),o&&(this.removeStop(r),this._render(!0))})})};return this._focusedStop&&this._focusedStop.el.classList.remove("active"),this._focusedStop=r,this._focusedStop.el.classList.add("active"),this._stops.push(r),this._render(o),this}removeStop(t){const{_stops:e}=this,o=(()=>"number"==typeof t?e.find(t=>t.loc===t):"string"==typeof t?e.find(t=>t.color===t):"object"==typeof t?t:void 0)();e.splice(e.indexOf(o),1),o.el.remove(),v(...o.listener),this._focusedStop===o&&(this._focusedStop=e[0]),this._render()}setGradient(t){const e=h(t);if(!e||e.stops.length<2)return!1;const{type:o,stops:n,modifier:i}=e,r=[...this._stops];if(this._modes.includes(o)){this._mode=o;for(const t of n)this.addStop(t.color,t.loc/100);for(const t of r)this.removeStop(t);return"linear"===o?(this._angle=180,i&&this.setLinearAngle(i)):"radial"===o&&(this._direction="circle at center",i&&this.setRadialPosition(i)),!0}return!1}getGradient(t=this._mode){const e=this.getStops().toString(t);switch(t){case"linear":return`linear-gradient(${this._angle}deg, ${e})`;case"radial":return`radial-gradient(${this._direction}, ${e})`;case"conic":return`conic-gradient(${e})`}}getStops(){const t=this._stops.map(t=>({color:t.color,location:t.loc})),e=this._mode;return t.toString=function(t=e){switch(t){case"linear":case"radial":return this.map(t=>`${t.color} ${100*t.location}%`).join(",");case"conic":return this.map(t=>`${t.color} ${360*t.location}deg`).join(",")}},t}getLinearAngle(){return"linear"===this._mode?this._angle:-1}setLinearAngle(t){return"number"==typeof(t="number"==typeof t?t:d.angleToDegrees(t)||(this._angles.find(e=>e.name===t)||{}).angle)&&(this._angle=t,this._render(),!0)}setRadialPosition(t){const e=this._directions.find(e=>e.css===t);if(!e)return!1;this._direction=e.css;for(const t of Array.from(this._root.gradient.pos.children))t.classList[t.getAttribute("data-pos")===e.pos?"add":"remove"]("gpcr-active");return this._render(),!0}getRadialPosition(){return"radial"===this._mode?this._direction:null}_emit(t,...e){this._eventListener[t].forEach(t=>t(...e,this))}on(t,e){return"function"==typeof e&&"string"==typeof t&&t in this._eventListener&&this._eventListener[t].push(e),this}off(t,e){const o=this._eventListener[t];if(o){const t=o.indexOf(e);~t&&o.splice(t,1)}return this}}b.Pickr=i.a;e.default=b}]).default}));