var Utils = (function() {
	return {
		/**
		 * 鏍煎紡鍖栦环鏍硷紝淇濈暀鍑犱綅灏忔暟
		 */
		formatMoney : function(money, digit) {
			return money.toFixed(digit);
		},
		/**
		 * 璁＄畻椤甸潰琚嵎鍏ョ殑楂樺害
		 */
		getScrollTop : function(self) {
			var doc = self ? document : parent.document;
			return doc.body.scrollTop || doc.documentElement.scrollTop;
		},
		getScrollLeft : function(self) {
			var doc = self ? document : parent.document;
			return doc.body.scrollLeft || doc.documentElement.scrollLeft;
		},
		/**
		 * 鍒ゆ柇dom瀵硅薄鏄惁琚殣钘忎簡
		 * @param dom
		 * @returns
		 */
		isHide : function(dom) {
			if(dom.style.display == "none") {
				return true;
			}
			return false;
		},
		/**
		 * 鍒囨崲class
		 * @param dom	瑕佸垏鎹lass鐨刣om
		 * @param oldClass	鏃lass
		 * @param newClass	鏂癱lass
		 */
		changeClass : function(dom, oldClass, newClass) {
			$delClass(dom, oldClass);
			$addClass(dom, newClass);
		},
		/**
		 * 璁＄畻瀛楃涓茬殑闀垮害锛屽寘鎷瑄tf-8
		 * @param str String 瑕佽绠楅暱搴︾殑瀛楃涓?
		 */
		getStrLength : function(str) {
			var len = 0;
			for(var i=0, l=str.length; i<l; i++) {
				if ((str.charCodeAt(i) & 0xff00) != 0) {
					len ++;
				}
				len ++;
			}
			len = Math.ceil(len / 2);
			return len;
		},
		/**
		 * 璁＄畻杩樺墿涓嬪灏戝瓧
		 * @param contentValue String 瑕佽绠楃殑鍐呭
		 * @param contentLengthDom document 灞曠ず鎻愮ず鐨刣om瀵硅薄
		 */
		countLetters : function(contentValue, contentLengthDom, limit, cutCount) {
			var _cut = 0;
			if(cutCount && cutCount > 0) {
				_cut = cutCount;
			}
			if(!limit) {
				limit = 140;
			}
			var len = Utils.getStrLength(contentValue) + _cut;
			if (len <= limit) {
				contentLengthDom.innerHTML = "<span>"+len+"</span>/"+limit;
			} else {
				contentLengthDom.innerHTML = "<span class='num'>"+len+"</span>/"+limit;
			}
		},
		/**
		 * 缁戝畾杈撳叆瀛楃鐨勮鏁板櫒浜嬩欢
		 * @param inputDom document 杈撳叆妗嗗璞?
		 * @param tipsDom document 杈撳叆瀛楁暟鎻愮ずdom瀵硅薄
		 * @param limit number 涓婇檺鏁板€?
		 * @param cutCount number 闇€瑕佽?鍔犲叆缁熻鐨勫瓧鏁帮紙鍗充笉鍖呮嫭鏂囨湰鍐呭锛屼絾鏄缁熻瀛楁暟鐨勬暟閲忥級
		 */
		bindCharCounter : function(inputDom, tipsDom, limit, cutCount) {
			Utils.countLetters(inputDom.value, tipsDom, limit, cutCount);
			inputDom.onkeyup = function() {
				Utils.countLetters(inputDom.value, tipsDom, limit, cutCount);
			};
			inputDom.onpaste = inputDom.onfocus = function() {	// 绮樿创鎴栬€呰仛鐒︾殑鏃跺€欙紝閮界粺璁″瓧鏁?
				setTimeout(function() {
					Utils.countLetters(inputDom.value, tipsDom, limit, cutCount);
				}, 50);
			};
		},
		/**
		 * 鍚憈extarea鎺т欢涓殑鍏夋爣鎵€鍦ㄥ鎻掑叆瀛楃涓?
		 *
		 * @param objid:textarea鎺т欢鐨刬d
		 * @param str:娆叉彃鍏ョ殑瀛楃涓?
		 */
		setTextareaValue : function(objid, str) {
			var myField = $id("" + objid);
			if (document.selection) {	// IE娴忚鍣?
				myField.focus();
				sel = document.selection.createRange();
				sel.text = str;
				sel.select();
			}else if (myField.selectionStart || myField.selectionStart == '0') {
				// 寰楀埌鍏夋爣鍓嶇殑浣嶇疆
				var startPos = myField.selectionStart;
				// 寰楀埌鍏夋爣鍚庣殑浣嶇疆
				var endPos = myField.selectionEnd;
				// 鍦ㄥ姞鍏ユ暟鎹箣鍓嶈幏寰楁粴鍔ㄦ潯鐨勯珮搴?
				var restoreTop = myField.scrollTop;
				myField.value = myField.value.substring(0, startPos) + str
						+ myField.value.substring(endPos, myField.value.length);
				// 濡傛灉婊氬姩鏉￠珮搴﹀ぇ浜?0
				if (restoreTop > 0) {
					// 杩斿洖
					myField.scrollTop = restoreTop;
				}
				myField.focus();
				myField.selectionStart = startPos + str.length;
				myField.selectionEnd = startPos + str.length;
			} else {
				myField.value += str;
				myField.focus();
			}
		},
		/**
		 * 鏍规嵁灞炴€у拰灞炴€у€煎鎵剧埗鑺傜偣
		 * @param child
		 * @param attr
		 * @param attrValue
		 * @param root	瀵绘壘鍒拌繖涓妭鐐逛负姝?
		 * @returns
		 */
		findParentByAttr : function(child, attr, attrValue, root) {
			if(child === root && child.getAttribute(attr) != attrValue) {
				return null;
			}
			var node = child.parentNode;
			if(!root) {
				root = document.body;
			}
			do {
				if(node.getAttribute(attr) == attrValue) {
					return node;
				}else {
					if(node === root) {
						return null;
					}
					node = node.parentNode;
				}
			}while(node !== null);
		},
		loadJsFile : function(url, charset) {
			var a = document.createElement('script');
			a.setAttribute('type', 'text/javascript');
			a.setAttribute('charset', charset?charset:'utf-8');
			a.setAttribute('src', url);
			document.getElementsByTagName('head')[0].appendChild(a);
		},
		addCalendar : function(option) {
			var opt = {
					inputDom : null,
					iconDom : null,
					unit : "day",
					fn : null
			};
			for(var i in option) {
				opt[i] = option[i];
			}
			if(opt.inputDom) {
				opt.inputDom.onclick = function(e) {
					e = e || window.event;
					var _self = this;
					$calendars({
				        el:this,
				        callback:function (n) {
				            _self.value = n;
				            if(opt.fn) {
								opt.fn();
							}
				        },
				        unit : opt.unit,
				        nowDate: this.value,
				        e:e
					});
					if($id("frmCalendar")) {	// 鍦↖E7涓€涓嬶紝璇ユ棩鍘嗘澶珮浜嗭紝褰卞搷甯傚锛屼慨鐞嗕竴涓?
						$id("frmCalendar").style.height = "200px";
					}
					$id("elCalendarWrap").style.zIndex = "10000";
				};
				if(opt.iconDom) {
					opt.iconDom.onclick = function(e) {
						$stopBubble(e);	// 鍋滄鍐掓场锛岄槻姝㈠脊鍑虹殑鏃ュ巻妗嗗洜澶卞幓鐒︾偣鑰屾秷澶?
						$fireEvent(opt.inputDom);
					};
				}
			}
		},
		getRadioValueByName : function(name) {
			var names = $attr("name", name, root);
			for(var i in names) {
				if(names[i].checked == true) {
					return names[i].value;
				}
			}
			return null;
		},
		getCheckboxValueByName : function(name) {
			var names = $attr("name", name, root);
			var values = [];
			for(var i in names) {
				if(names[i].checked == true) {
					values.push(names[i].value);
				}
			}
			return values;
		},
		/**
		 * 閫氳繃鍥剧墖鍘熷浘鍦板潃鏇挎崲瑕佹槸鐢ㄥ埌size鐨勫湴鍧€
		 * @param initUrl
		 * @param size
		 * @returns
		 */
		getImgUrlBySize : function(initUrl, size) {
			if(!initUrl) {
				return "";
			}
			return initUrl.replace(/(.+)(\/\d+)$/g, '$1/'+size);
		},
		getPaipaiImgUrlBySize : function(initUrl, size) {
			if(!initUrl) {
				return "";
			}
			return initUrl.replace(/(.+)(\.[\w]+)$/g, '$1.'+size+'x'+size+'$2');
		},

		getUrlBySize : function(url,size){
			if(url.indexOf('http://p.qpic.cn/ecc_merchant/') === 0){
				if('MEDIUM' === size){
					size = 450;
				}else if('SMALL' == size){
					size = 200;
				}
				return Utils.getImgUrlBySize(url,size);
			}else{
				if('MEDIUM' === size){
					size = 300;
				}else if('SMALL' == size){
					size = 200;
				}
				return Utils.getPaipaiImgUrlBySize(url,size);
			}

		},



		/**
		 *
		 * @returns
		 */
		setAnchor : function(anchor) {
			var loc = window.location.href;
			if(loc.indexOf("#") != -1) {
				window.location = loc.replace(/#.+/g, '#'+anchor);
			}else {
				window.location = loc + "#" + anchor;
			}
		},
		setGray : function(dom) {
			dom.style.color = "#999";
		},
		setBlack : function(dom) {
			dom.style.color = "#000";
		},
		changeUrlByPageNumber : function(pageNumber){
				var locationHash=window.location.hash;
				var locationUrl=window.location.href;
				if(locationHash){
					if(locationHash==""){
						window.location.href=locationUrl+"#pageNumber="+pageNumber;
					}else{
						var hashIndex=locationUrl.indexOf(locationHash);
						var requestUrl=locationUrl.substring(0,hashIndex);
						window.location.href=requestUrl+"#pageNumber="+pageNumber;
					}
				}else{
					window.location.href=locationUrl+"#pageNumber="+pageNumber;
				}
		},
		getPageNumberFromAnchor : function(){
			var locationHash=window.location.hash;
			var locationUrl=window.location.href;
			if(locationHash){
				if(locationHash==""){
					return 1;
				}else{
					var pageNumberStr="pageNumber=";
					var hashIndex=locationUrl.indexOf(locationHash);
					var anchor=locationUrl.substring(hashIndex);
					var pageNumberIndex=anchor.indexOf(pageNumberStr);
					if(pageNumberIndex>0){
						var pageNumber=anchor.substring(pageNumberIndex+pageNumberStr.length);
						var result=parseInt(pageNumber);
						if(isNaN(result)){
							return 1;
						}else{
							return result;
						}
					}else{
						return 1;
					}
				}
			}else{
				return 1;
			}
		},
		/**
		 * 鏀寔date锛宯umber绫诲瀷鐨勬牸寮忓寲
		 * @param date
		 * @param formatStr
		 * @returns
		 */
		formatDate : function(date, formatStr) {
			if(typeof date == "number") {
				date = new Date(date);
			}
			return $formatDate(date, formatStr);
		},
		/**
		 * 闃绘浜嬩欢鍐掓场
		 */
		stopBubble : function(event) {
			$stopBubble(event);
		},
		encodeHtml : function(html) {
			return $htmlEncode(html);
		},
		decodeHtml : function(content) {
			return typeof(content) != "string" ? "" : content.replace(/&lt;/g, "<")
					.replace(/&gt;/g, ">")
					.replace(/&quot;/g, "\"")
					.replace(/&#39;/g, "\'")
					.replace(/&nbsp;/g, " ")
					.replace(/&#92;/g,"\\")
					.replace(/&amp;/g, "&");
		},
		getFileSize : function(sizeNum) {
			if(typeof sizeNum != 'number') {
				return sizeNum;
			}
			if(sizeNum > 1048576) {
				return (sizeNum/1048576).toFixed(1)+"MB";
			}else if(sizeNum > 1024) {
				return (sizeNum/1024).toFixed(1)+"KB";
			}else if(sizeNum > 0) {
				return sizeNum+"B";
			}else {
				return sizeNum;
			}
		},
		getFormatTime : function(seconds) {
			var _getTen = function(s) {
				if(s<10) {
					return "0"+s;
				}else {
					return s;
				}
			};
			return Math.floor(seconds/60)+":"+_getTen(seconds%60);
		},
		addEvent : function(dom, type, fn) {
			$addEvent(dom, type, fn);
		},
		trim : function(str) {
			if(typeof str == "string") {
				return str.replace(/(^\s+)|(\s+$)/g, "");
			}else {
				return str;
			}
		},

		parseInt : function(str){
			return window.parseInt(str);
		},

		setPageHeight : function(height) {
		},
		// 鏄惁琚祵鍏ヤ簡iframe
		isEmbeddedIframe : function() {
			return window.self != window.parent;
		},
		isColorValue : function(colorValue) {
			var reg = /^#[A-Fa-f0-9]{0,6}$/;
			return reg.test(colorValue);
		},
		/**
		 * 娴呭眰澶嶅埗瀵硅薄
		 * @param o
		 * @returns
		 */
		object : function(o) {
			var F = function() {};
			F.prototype = o;
			return new F();
		},
		extend : function(subType, superType) {
			var prototype = Utils.object(superType.prototype);
			prototype.constructor = subType;
			subType.prototype = prototype;
		},
		isEmpty : function(obj) {
			for(var name in obj) {
		        return false;
		    }
		    return true;
		},
		getUrlParam : function(name, url) {
			var search = url || window.location.search;
			var reg = new RegExp('[\\?&]'+name+'=([^&^#]+)', 'g');
			reg.test(search);
			return RegExp.$1;
		}
	};
})();

if (window.define) {
	define(function(require, exports) {
	  var legos=require('legos');
	  exports.utils=window.Utils;
	});
}