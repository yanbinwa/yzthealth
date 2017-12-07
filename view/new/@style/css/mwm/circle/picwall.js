var FCAPP = FCAPP || {};
FCAPP.PICWALL = {
    CONFIG: {
        params: {
            cmd: 'query',
            appid: 'wx5a6e92c6df10109e',
            pagesize: 10
        },
        noteHeight: 0,
        picURL: 'data/goods.js'
    },
    RUNTIME: {
        curPage: 1,
        records: 0,
        total: 0,
        curScrollTop: 0,
        isSlide: false,
        PICS: [],
        SPICS: {},
        POFFSET: {}
    },
    init: function() {
        var R = PICWALL.RUNTIME,
        C = PICWALL.CONFIG;
        R.page = 1;
        if (!R.pointNav) {
            R.ulList = document.getElementById('ulList');
            R.left = document.getElementById('leftLi');
            R.right = document.getElementById('rightLi');
            R.photoDiv = document.getElementById('photoDiv');
            R.photoClick = document.getElementById('photoClick'); //closeBtn
            R.scroller = document.getElementById('scroller');
            R.theList = document.getElementById('theList');
            R.loading = document.getElementById('popFail');
            R.photoMask = document.getElementById('photoMask');
            window.onscroll = function() {
                if (R.switchSlide) {
                    return;
                }
                try {
                    clearTimeout(tm);
                } catch(e) {}
                tm = setTimeout(PICWALL.pageScroll, 200);
            };
            window.popUp = PICWALL.popUP;
            window.closePOP = PICWALL.closePOP;
        }
        PICWALL.loadPage(1);
    },
    updateImgMargin: function() {
        var R = PICWALL.RUNTIME,
        h = R.lastHeight,
        w = R.lastWidth,
        iw, ih, th, img, margin = PICWALL.CONFIG.noteHeight;
        for (var i = 0,
        il = R.PICS.length; i < il; i++) {
            var pic = R.PICS[i];
            img = pic.img;
            iw = pic.origWidth;
            ih = pic.origHeight;
            if (pic.loaded) {
                if (h > w && iw / ih > w / h) {
                    th = Math.floor(w * ih / iw);
                    img.style.width = w - 4 + 'px';
                    img.style.height = th + 'px';
                    img.style.marginTop = Math.floor((h - th - margin) / 2) + 'px';
                } else {
                    img.style.height = h - margin + 'px';
                    img.style.width = Math.floor(h * iw / ih) + 'px';
                    img.style.marginTop = '0px';
                }
            }
        }
    },
    popUP: function(idx) {
        var R = PICWALL.RUNTIME;
        R.isSlide = true;
        R.photoDiv.style.display = "";
        R.photoMask.style.display = "";
        R.photoClick.style.zIndex = "9999";
        if (!R.PICS[idx].dom) {
            R.PICS[idx].dom = document.getElementById('img_' + idx);
        }
        if (!R.PICS[idx].loaded) {
            PICWALL.loadImg(idx);
        }
        try {
            myScroll.refresh();
            myScroll.scrollToPage(idx, 0, 0);
        } catch(e) {}
    },
    closePOP: function() {
        var R = PICWALL.RUNTIME;
        R.isSlide = false;
        R.switchSlide = true;
        setTimeout(function() {
            R.switchSlide = false;
        },
        100);
        R.photoDiv.style.display = "none";
        R.photoMask.style.display = "none";
        window.scrollTo(0, R.POFFSET[myScroll.currPageX]);
    },
    initScroll: function() {
        //for pop scroll wall
        if (myScroll) {
            myScroll.destroy();
        }
        myScroll = new iScroll('wrapper', {
            zoom: false,
            snap: true,
            momentum: false,
            hScrollbar: false,
            vScrollbar: false,
            hScroll: true,
            onScrollEnd: function() {
                var R = PICWALL.RUNTIME,
                idx = myScroll.currPageX;
                if (R.PICS[idx] && !R.PICS[idx].loaded) {
                    PICWALL.loadImg(idx);
                }
                if (idx == myScroll.pagesX.length - 1) {
                    PICWALL.nextPage();
                }
            }
        });
        PICWALL.resizeLayout(true);
    },
    loadImg: function(idx) {
        var R = PICWALL.RUNTIME,
        C = PICWALL.CONFIG,
        h = R.lastHeight,
        w = R.lastWidth,
        pic = R.PICS[idx],
        margin = C.noteHeight,
        img = new Image();
        img.idx = idx;
        if (pic.loading || pic.loaded) {
            return;
        }
        img.addEventListener('load',
        function() {
            if (!pic.dom) {
                pic.dom = document.getElementById('img_' + this.idx);
            }
            if (!pic.loaded) {
                pic.dom.style.background = "none";
                pic.dom.appendChild(this);
            }
            var iw = this.width,
            ih = this.height,
            th = 0;
            if (h > w && iw / ih > w / h) {
                th = Math.floor(w * ih / iw);
                this.style.width = w - 4 + 'px';
                this.style.height = th + 'px';
                this.style.marginTop = Math.floor((h - th - margin) / 2) + 'px';
            } else {
                this.style.height = h - margin + 'px';
                this.style.width = Math.floor(h * iw / ih) + 'px';
            }
            pic.origWidth = iw;
            pic.origHeight = ih;
            pic.img = this;
            pic.loaded = true;
            pic.loading = false;
            delete pic.dom;
            delete pic.url;
            this.onload = null;
        });
        img.src = pic.url;
        pic.loading = true;
    },
    loadSImg: function(idx) {
        var R = PICWALL.RUNTIME,
        pic = R.SPICS[idx],
        img = new Image();
        img.idx = idx;
        if (pic.loading || pic.loaded) {
            return;
        }
        img.addEventListener('load',
        function() {
            var iw = this.width,
            ih = this.height,
            th = 0;
            th = Math.floor(127 * ih / iw);
            this.style.width = '127px';
            this.style.height = th + 'px';
            R.POFFSET[this.idx] += (th - 48);
            if (!pic.loaded) {
                var oimg = pic.dom.getElementsByTagName('img')[0];
                oimg.parentNode.replaceChild(this, oimg);
            }
            pic.iw = iw;
            pic.ih = ih;
            pic.loading = false;
            pic.loaded = true;
            this.onload = null;
            delete pic.src;
            delete pic.dom;
        });
        img.src = pic.src;
        pic.loading = true;
    },
    resizeLayout: function(boo) {
        var R = PICWALL.RUNTIME,
        C = PICWALL.CONFIG,
        margin = PICWALL.CONFIG.noteHeight,
        w = document.documentElement.clientWidth,
        h = document.documentElement.offsetHeight;
        if (!R.lastWidth) {
            R.lastWidth = w;
            R.lastHeight = h;
            R.clientWidth = w;
            PICWALL.resizeLayout(boo);
        } else {
            if (boo || w != R.lastWidth || h != R.lastHeight) {
                R.lastHeight = h;
                R.lastWidth = w;
                R.scroller.style.width = w * R.PICS.length + 'px';
                R.scroller.style.height = h + 'px';
                if (R.slideLi) {
                    R.slideLi.css({
                        width: w + 'px'
                    });
                }
                PICWALL.updateImgMargin();
                if (R.isSlide && myScroll) {
                    myScroll.refresh();
                    myScroll.scrollToPage(myScroll.currPageX);
                }
            }
        }
    },
    loadPage: function(page) {
        var R = PICWALL.RUNTIME,
        data = PICWALL.CONFIG.params;
        page = parseInt(page);
        data.pageindex = isNaN(page) ? 1 : page;
        //data.jsonpCallback = 'picResult';
        window.picResult = PICWALL.picResult;
        PICWALL.RUNTIME.page = page;
        document.body.className = "loading";
        $.ajax({
            url: PICWALL.CONFIG.picURL +"?"+ $.param(data),
            jsonpCallback:"picResult",
            dataType: 'jsonp',
        });
    },
    nextPage: function() {
        if (PICWALL.RUNTIME.records < PICWALL.RUNTIME.total) {
            PICWALL.RUNTIME.curPage++;
            PICWALL.loadPage(PICWALL.RUNTIME.curPage);
        }
    },
    picResult: function(data) {
        setTimeout(function(){
            document.body.className = "";
        }, 500);
        var R = PICWALL.RUNTIME,
        margin = PICWALL.CONFIG.noteHeight,
        w = R.clientWidth,
        h = document.documentElement.offsetHeight,
        idata = 'data:image/png;base64,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',
        bg = 'style="background:url(data:img/gif;base64,R0lGODlhfgIgALMLALa2tuTk5NjY2Ly8vISEhDY2NgQEBB4eHsbGxpqamlZWVv///wAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/wtYTVAgRGF0YVhNUDw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MjMxODIzNkI5QzAxMTFFMjgxNUZBQ0NGRDQ2Q0VDNUEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjMxODIzNkM5QzAxMTFFMjgxNUZBQ0NGRDQ2Q0VDNUEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyMzE4MjM2OTlDMDExMUUyODE1RkFDQ0ZENDZDRUM1QSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyMzE4MjM2QTlDMDExMUUyODE1RkFDQ0ZENDZDRUM1QSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn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) no-repeat center center;background-size:90%;" ';
        if (data.ret == 0 && data.picture) {
            var pics = data.picture;
            R.total = data.total;
            R.isLoading = false;
            for (var i = 0; i < pics.length; i++) {
                var li = document.createElement("li"),
                li1 = li.cloneNode(false);
                R.PICS[R.records] = {
                    url: pics[i].url,
                    loaded: false
                };
                li.innerHTML = ['<div class="pic">', '<a onclick="popUp(', R.records, ');" name="img_', R.records, '">', '<img src="', idata, '" width="127" height="auto" />', '</a></div>', '<h2><p>', pics[i].label1, '</p><p>', pics[i].label2, '</p></h2>'].join('');
                R.SPICS[R.records] = {
                    dom: li,
                    src: pics[i].url,
                    loaded: false
                };
                PICWALL.loadSImg(R.records);
                li1.innerHTML = '<div class="photo" ' + bg + ' id="img_' + R.records + '"></div>';
                if (R.left.offsetHeight <= R.right.offsetHeight) {
                    R.POFFSET[R.records] = R.left.offsetHeight;
                    R.left.appendChild(li);
                } else {
                    R.POFFSET[R.records] = R.right.offsetHeight;
                    R.right.appendChild(li);
                }
                R.theList.appendChild(li1);
                R.records++;
            }
            R.slideLi = $('#theList li');
            R.photoDiv.style.top = 0;
            R.photoClick.style.top = -3;
            R.photoClick.style.right = -2;
            if (!myScroll) {
                PICWALL.initScroll();
            } else {
                PICWALL.resizeLayout(true);
                var idx = myScroll.currPageX;
                myScroll.refresh();
                //myScroll.scrollToPage(idx);
            }
            R.loading.style.display = "none";
        } else {
            $('#popFail').hide();
        }
    },
    pageScroll: function() {
        var R = PICWALL.RUNTIME,
        b = document.body,
        bh = b.scrollHeight,
        bt = b.scrollTop,
        sh = screen.height;
        if (bt > R.curScrollTop && (bh - bt - sh * 2) < 0 && R.records < 200) {
            R.curScrollTop = bt;
            PICWALL.nextPage();
        }
    }
};
var PICWALL = FCAPP.PICWALL,
tm = null,
myScroll;
$(document).ready(PICWALL.init);