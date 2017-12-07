/*!artTemplate - Template Engine*/var template=function(e,t){return template[typeof t=="object"?"render":"compile"].apply(template,arguments)};(function(e,t){"use strict";e.version="2.0.0",e.openTag="<%",e.closeTag="%>",e.isEscape=!0,e.parser=null,e.render=function(e,t){var n=s(e);return n===undefined?o({id:e,name:"Render Error",message:"No Template"}):n(t)},e.compile=function(t,r){function c(n){try{return new f(n)+""}catch(i){return s?(i.id=t||r,i.name="Render Error",i.source=r,o(i)):e.compile(t,r,!0)(n)}}var i=arguments,s=i[2],a="anonymous";typeof r!="string"&&(s=i[1],r=i[0],t=a);try{var f=u(r,s)}catch(l){return l.id=t||r,l.name="Syntax Error",o(l)}return c.prototype=f.prototype,c.toString=function(){return f.toString()},t!==a&&(n[t]=c),c},e.helper=function(t,n){e.prototype[t]=n},e.onerror=function(e){var n="[template]:\n"+e.id+"\n\n[name]:\n"+e.name;e.message&&(n+="\n\n[message]:\n"+e.message),e.line&&(n+="\n\n[line]:\n"+e.line,n+="\n\n[source]:\n"+e.source.split(/\n/)[e.line-1].replace(/^[\s\t]+/,"")),e.temp&&(n+="\n\n[temp]:\n"+e.temp),t.console&&console.error(n)};var n={},r="__proto__"in{},i="document"in t,s=function(t){var r=n[t];if(r===undefined&&i){var s=document.getElementById(t);if(s){var o=s.value||s.innerHTML;return e.compile(t,o.replace(/^\s*|\s*$/g,""))}}else if(n.hasOwnProperty(t))return r},o=function(t){function n(){return n+""}return e.onerror(t),n.toString=function(){return"{No Template}"},n},u=function(){e.prototype={$render:e.render,$escapeHTML:function(e){return typeof e=="string"?e.replace(/&(?![\w#]+;)|[<>"']/g,function(e){return{"<":"&#60;",">":"&#62;",'"':"&#34;","'":"&#39;","&":"&#38;"}[e]}):e},$getValue:function(e){return typeof e=="string"||typeof e=="number"?e:typeof e=="function"?e():""}};var t=Array.prototype.forEach||function(e,t){var n=this.length>>>0;for(var r=0;r<n;r++)r in this&&e.call(t,this[r],r,this)},n=function(e,n){t.call(e,n)},i="break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined",s=new RegExp(["/\\*(.|\n)*?\\*/|//[^\n]*\n|//[^\n]*$","'[^']*'|\"[^\"]*\"","\\.[s	\n]*[\\$\\w]+","\\b"+i.replace(/,/g,"\\b|\\b")+"\\b"].join("|"),"g"),o=function(e){return e=e.replace(s,",").replace(/[^\w\$]+/g,",").replace(/^,|^\d+|,\d+|,$/g,""),e?e.split(","):[]};return function(t,i){function S(e){return c+=e.split(/\n/).length-1,e=e.replace(/('|"|\\)/g,"\\$1").replace(/\r/g,"\\r").replace(/\n/g,"\\n"),e=m[1]+"'"+e+"'"+m[2],e+"\n"}function x(t){var n=c;a?t=a(t):i&&(t=t.replace(/\n/g,function(){return c++,"$line="+c+";"}));if(t.indexOf("=")===0){var r=t.indexOf("==")!==0;t=t.replace(/^=*|[\s;]*$/g,"");if(r&&e.isEscape){var s=t.replace(/\s*\([^\)]+\)/,"");!p.hasOwnProperty(s)&&!/^(include|print)$/.test(s)&&(t="$escapeHTML($getValue("+t+"))")}else t="$getValue("+t+")";t=m[1]+t+m[2]}return i&&(t="$line="+n+";"+t),T(t),t+"\n"}function T(e){e=o(e),n(e,function(e){h.hasOwnProperty(e)||(N(e),h[e]=!0)})}function N(e){var t;e==="print"?t=y:e==="include"?(d.$render=p.$render,t=b):(t="$data."+e,p.hasOwnProperty(e)&&(d[e]=p[e],e.indexOf("$")===0?t="$helpers."+e:t=t+"===undefined?$helpers."+e+":"+t)),v+=e+"="+t+","}var s=e.openTag,u=e.closeTag,a=e.parser,f=t,l="",c=1,h={$data:!0,$helpers:!0,$out:!0,$line:!0},p=e.prototype,d={},v="var $helpers=this,"+(i?"$line=0,":""),m=r?["$out='';","$out+=",";","$out"]:["$out=[];","$out.push(",");","$out.join('')"],g=r?"if(content!==undefined){$out+=content;return content}":"$out.push(content);",y="function(content){"+g+"}",b="function(id,data){if(data===undefined){data=$data}var content=$helpers.$render(id,data);"+g+"}";n(f.split(s),function(e,t){e=e.split(u);var n=e[0],r=e[1];e.length===1?l+=S(n):(l+=x(n),r&&(l+=S(r)))}),f=l,i&&(f="try{"+f+"}catch(e){"+"e.line=$line;"+"throw e"+"}"),f="'use strict';"+v+m[0]+f+"return new String("+m[3]+")";try{var w=new Function("$data",f);return w.prototype=d,w}catch(E){throw E.temp="function anonymous($data) {"+f+"}",E}}}()})(template,this),typeof module!="undefined"&&module.exports&&(module.exports=template)
