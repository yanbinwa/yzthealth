(function(n, t) {
    function vt(t) {
        n.extend(!0, g, t)
    }
    function wi(u, f, e) {
        function ri(n) {
            l ? (ht(), kt(), ti(), v(n)) : ui()
        }
        function ui() {
            ur = f.theme ? "ui": "fc";
            u.addClass("fc");
            f.isRTL && u.addClass("fc-rtl");
            f.theme && u.addClass("ui-widget");
            l = n("<div class='fc-content' style='position:relative'/>").prependTo(u);
            w = new bi(s, f);
            k = w.render();
            k && u.prepend(k);
            wt(f.defaultView);
            n(window).resize(dt);
            yt() || vt()
        }
        function vt() {
            setTimeout(function() { ! o.start && yt() && v()
            },
            0)
        }
        function fi() {
            n(window).unbind("resize", dt);
            w.destroy();
            l.remove();
            u.removeClass("fc fc-rtl ui-widget")
        }
        function it() {
            return et.offsetWidth !== 0
        }
        function yt() {
            return n("body")[0].offsetWidth !== 0
        }
        function wt(t) {
            if (!o || t != o.name) {
                y++;
                ut();
                var i = o,
                r;
                i ? ((i.beforeHide || ni)(), b(l, l.height()), i.element.hide()) : b(l, 1);
                l.css("overflow", "hidden");
                o = ot[t];
                o ? o.element.show() : o = ot[t] = new h[t](r = st = n("<div class='fc-view fc-view-" + t + "' style='position:absolute'/>").appendTo(l), s);
                i && w.deactivateButton(i.name);
                w.activateButton(t);
                v();
                l.css("overflow", "");
                i && b(l, 1);
                r || (o.afterShow || ni)();
                y--
            }
        }
        function v(n) {
            var i, r;
            it() && (y++, ut(), d === t && ht(), i = !1, !o.start || n || a < o.start || a >= o.end ? (o.render(a, n || 0), ct(!0), i = !0) : o.sizeDirty ? (o.clearEvents(), ct(), i = !0) : o.eventsDirty && (o.clearEvents(), i = !0), o.sizeDirty = !1, o.eventsDirty = !1, ei(i), lt = u.outerWidth(), w.updateTitle(o.title), r = new Date, r >= o.start && r < o.end ? w.disableButton("today") : w.enableButton("today"), y--, o.trigger("viewDisplay", et))
        }
        function bt() {
            kt();
            it() && (ht(), ct(), ut(), o.clearEvents(), o.renderEvents(at), o.sizeDirty = !1)
        }
        function kt() {
            n.each(ot,
            function(n, t) {
                t.sizeDirty = !0
            })
        }
        function ht() {
            d = f.contentHeight ? f.contentHeight: f.height ? f.height - (k ? k.height() : 0) - p(l) : Math.round(l.width() / Math.max(f.aspectRatio, .5))
        }
        function ct(n) {
            y++;
            o.setHeight(d, n);
            st && (st.css("position", "relative"), st = null);
            o.setWidth(l.width(), n);
            y--
        }
        function dt() {
            if (!y) if (o.start) {
                var n = ++ii;
                setTimeout(function() {
                    n == ii && !y && it() && lt != (lt = u.outerWidth()) && (y++, bt(), o.trigger("windowResize", et), y--)
                },
                200)
            } else vt()
        }
        function ei(n) { ! f.lazyFetching || ir(o.visStart, o.visEnd) ? gt() : n && rt()
        }
        function gt() {
            rr(o.visStart, o.visEnd)
        }
        function oi(n) {
            at = n;
            rt()
        }
        function si(n) {
            rt(n)
        }
        function rt(n) {
            ti();
            it() && (o.clearEvents(), o.renderEvents(at, n), o.eventsDirty = !1)
        }
        function ti() {
            n.each(ot,
            function(n, t) {
                t.eventsDirty = !0
            })
        }
        function hi(n, i, r) {
            o.select(n, i, r === t ? !0 : r)
        }
        function ut() {
            o && o.unselect()
        }
        function ci() {
            v( - 1)
        }
        function li() {
            v(1)
        }
        function ai() {
            nt(a, -1);
            v()
        }
        function vi() {
            nt(a, 1);
            v()
        }
        function yi() {
            a = new Date;
            v()
        }
        function pi(n, t, r) {
            n instanceof Date ? a = i(n) : pt(a, n, t, r);
            v()
        }
        function wi(n, i, u) {
            n !== t && nt(a, n);
            i !== t && tt(a, i);
            u !== t && r(a, u);
            v()
        }
        function di() {
            return i(a)
        }
        function gi() {
            return o
        }
        function nr(n, i) {
            if (i === t) return f[n]; (n == "height" || n == "contentHeight" || n == "aspectRatio") && (f[n] = i, bt())
        }
        function tr(n, t) {
            if (f[n]) return f[n].apply(t || et, Array.prototype.slice.call(arguments, 2))
        }
        var s = this;
        s.options = f;
        s.render = ri;
        s.destroy = fi;
        s.refetchEvents = gt;
        s.reportEvents = oi;
        s.reportEventChange = si;
        s.rerenderEvents = rt;
        s.changeView = wt;
        s.select = hi;
        s.unselect = ut;
        s.prev = ci;
        s.next = li;
        s.prevYear = ai;
        s.nextYear = vi;
        s.today = yi;
        s.gotoDate = pi;
        s.incrementDate = wi;
        s.formatDate = function(n, t) {
            return c(n, t, f)
        };
        s.formatDates = function(n, t, i) {
            return ft(n, t, i, f)
        };
        s.getDate = di;
        s.getView = gi;
        s.option = nr;
        s.trigger = tr;
        ki.call(s, f, e);
        var ir = s.isFetchNeeded,
        rr = s.fetchEvents,
        et = u[0],
        w,
        k,
        l,
        ur,
        o,
        ot = {},
        lt,
        d,
        st,
        ii = 0,
        y = 0,
        a = new Date,
        at = [],
        g;
        pt(a, f.year, f.month, f.date);
        f.droppable && n(document).bind("dragstart",
        function(t, i) {
            var u = t.target,
            e = n(u),
            r;
            e.parents(".fc").length || (r = f.dropAccept, (n.isFunction(r) ? r.call(u, e) : e.is(r)) && (g = u, o.dragStart(g, t, i)))
        }).bind("dragstop",
        function(n, t) {
            g && (o.dragStop(g, n, t), g = null)
        })
    }
    function bi(t, i) {
        function o() {
            r = i.theme ? "ui": "fc";
            var t = i.header;
            if (t) return u = n("<table class='fc-header' style='width:100%'/>").append(n("<tr/>").append(e("left")).append(e("center")).append(e("right")))
        }
        function s() {
            u.remove()
        }
        function e(u) {
            var f = n("<td class='fc-header-" + u + "'/>"),
            e = i.header[u];
            return e && n.each(e.split(" "),
            function(u) {
                u > 0 && f.append("<span class='fc-header-space'/>");
                var e;
                n.each(this.split(","),
                function(u, o) {
                    var c;
                    if (o == "title") f.append("<span class='fc-header-title'><h2>&nbsp;<\/h2><\/span>"),
                    e && e.addClass(r + "-corner-right"),
                    e = null;
                    else if (t[o] ? c = t[o] : h[o] && (c = function() {
                        s.removeClass(r + "-state-hover");
                        t.changeView(o)
                    }), c) {
                        var l = i.theme ? ht(i.buttonIcons, o) : null,
                        a = ht(i.buttonText, o),
                        s = n("<span class='fc-button fc-button-" + o + " " + r + "-state-default'><span class='fc-button-inner'><span class='fc-button-content'>" + (l ? "<span class='fc-icon-wrap'><span class='ui-icon ui-icon-" + l + "'/><\/span>": a) + "<\/span><span class='fc-button-effect'><span><\/span><\/span><\/span><\/span>");
                        s && (s.click(function() {
                            s.hasClass(r + "-state-disabled") || c()
                        }).mousedown(function() {
                            s.not("." + r + "-state-active").not("." + r + "-state-disabled").addClass(r + "-state-down")
                        }).mouseup(function() {
                            s.removeClass(r + "-state-down")
                        }).hover(function() {
                            s.not("." + r + "-state-active").not("." + r + "-state-disabled").addClass(r + "-state-hover")
                        },
                        function() {
                            s.removeClass(r + "-state-hover").removeClass(r + "-state-down")
                        }).appendTo(f), e || s.addClass(r + "-corner-left"), e = s)
                    }
                });
                e && e.addClass(r + "-corner-right")
            }),
            f
        }
        function c(n) {
            u.find("h2").html(n)
        }
        function l(n) {
            u.find("span.fc-button-" + n).addClass(r + "-state-active")
        }
        function a(n) {
            u.find("span.fc-button-" + n).removeClass(r + "-state-active")
        }
        function v(n) {
            u.find("span.fc-button-" + n).addClass(r + "-state-disabled")
        }
        function y(n) {
            u.find("span.fc-button-" + n).removeClass(r + "-state-disabled")
        }
        var f = this,
        u, r;
        f.render = o;
        f.destroy = s;
        f.updateTitle = c;
        f.activateButton = l;
        f.deactivateButton = a;
        f.disableButton = v;
        f.enableButton = y;
        u = n([])
    }
    function ki(r, u) {
        function st(n, t) {
            return ! h || n < h || t > a
        }
        function ht(n, t) {
            var u, r, i;
            for (h = n, a = t, e = [], u = ++b, r = s.length, v = r, i = 0; i < r; i++) k(s[i], u)
        }
        function k(n, t) {
            g(n,
            function(i) {
                if (t == b) {
                    if (i) {
                        for (var r = 0; r < i.length; r++) i[r].source = n,
                        c(i[r]);
                        e = e.concat(i)
                    }
                    v--;
                    v || l(e)
                }
            })
        }
        function g(t, u) {
            for (var l = f.sourceFetchers,
            s, e, v, o = 0; o < l.length; o++) {
                if (s = l[o](t, h, a, u), s === !0) return;
                if (typeof s == "object") {
                    g(s, u);
                    return
                }
            }
            if (e = t.events, e) n.isFunction(e) ? (tt(), e(i(h), i(a),
            function(n) {
                u(n);
                it()
            })) : n.isArray(e) ? u(e) : u();
            else if (v = t.url, v) {
                var b = t.success,
                k = t.error,
                nt = t.complete,
                c = n.extend({},
                t.data || {}),
                y = w(t.startParam, r.startParam),
                p = w(t.endParam, r.endParam);
                y && (c[y] = Math.round( + h / 1e3));
                p && (c[p] = Math.round( + a / 1e3));
                tt();
                n.ajax(n.extend({},
                vi, t, {
                    data: c,
                    success: function(t) {
                        t = t || [];
                        var i = d(b, this, arguments);
                        n.isArray(i) && (t = i);
                        u(t)
                    },
                    error: function() {
                        d(k, this, arguments);
                        u()
                    },
                    complete: function() {
                        d(nt, this, arguments);
                        it()
                    }
                }))
            } else u()
        }
        function ct(n) {
            n = nt(n);
            n && (v++, k(n, b))
        }
        function nt(t) {
            return n.isFunction(t) || n.isArray(t) ? t = {
                events: t
            }: typeof t == "string" && (t = {
                url: t
            }),
            typeof t == "object" ? (wt(t), s.push(t), t) : void 0
        }
        function lt(t) {
            s = n.grep(s,
            function(n) {
                return ! ut(n, t)
            });
            e = n.grep(e,
            function(n) {
                return ! ut(n.source, t)
            });
            l(e)
        }
        function at(n) {
            for (var f = e.length,
            t, r = bt().defaultEventEnd, o = n.start - n._start, u = n.end ? n.end - (n._end || r(n)) : 0, i = 0; i < f; i++) t = e[i],
            t._id == n._id && t != n && (t.start = new Date( + t.start + o), t.end = n.end ? t.end ? new Date( + t.end + u) : new Date( + r(t) + u) : null, t.title = n.title, t.url = n.url, t.allDay = n.allDay, t.className = n.className, t.editable = n.editable, t.color = n.color, t.backgroudColor = n.backgroudColor, t.borderColor = n.borderColor, t.textColor = n.textColor, c(t));
            c(n);
            l(e)
        }
        function vt(n, t) {
            c(n);
            n.source || (t && (p.events.push(n), n.source = p), e.push(n));
            l(e)
        }
        function yt(t) {
            var r, i;
            if (t) for (n.isFunction(t) || (r = t + "", t = function(n) {
                return n._id == r
            }), e = n.grep(e, t, !0), i = 0; i < s.length; i++) n.isArray(s[i].events) && (s[i].events = n.grep(s[i].events, t, !0));
            else for (e = [], i = 0; i < s.length; i++) n.isArray(s[i].events) && (s[i].events = []);
            l(e)
        }
        function pt(t) {
            return n.isFunction(t) ? n.grep(e, t) : t ? (t += "", n.grep(e,
            function(n) {
                return n._id == t
            })) : e
        }
        function tt() {
            ot++||et("loading", null, !0)
        }
        function it() {--ot || et("loading", null, !1)
        }
        function c(n) {
            var u = n.source || {},
            f = w(u.ignoreTimezone, r.ignoreTimezone);
            n._id = n._id || (n.id === t ? "_fc" + yi++:n.id + "");
            n.date && (n.start || (n.start = n.date), delete n.date);
            n._start = i(n.start = rt(n.start, f));
            n.end = rt(n.end, f);
            n.end && n.end <= n.start && (n.end = null);
            n._end = n.end ? i(n.end) : null;
            n.allDay === t && (n.allDay = w(u.allDayDefault, r.allDayDefault));
            n.className ? typeof n.className == "string" && (n.className = n.className.split(/\s+/)) : n.className = []
        }
        function wt(n) {
            var i, t;
            for (n.className ? typeof n.className == "string" && (n.className = n.className.split(/\s+/)) : n.className = [], i = f.sourceNormalizers, t = 0; t < i.length; t++) i[t](n)
        }
        function ut(n, t) {
            return n && t && ft(n) == ft(t)
        }
        function ft(n) {
            return (typeof n == "object" ? n.events || n.url: "") || n
        }
        var o = this,
        y;
        o.isFetchNeeded = st;
        o.fetchEvents = ht;
        o.addEventSource = ct;
        o.removeEventSource = lt;
        o.updateEvent = at;
        o.renderEvent = vt;
        o.removeEvents = yt;
        o.clientEvents = pt;
        o.normalizeEvent = c;
        var et = o.trigger,
        bt = o.getView,
        l = o.reportEvents,
        p = {
            events: []
        },
        s = [p],
        h,
        a,
        b = 0,
        v = 0,
        ot = 0,
        e = [];
        for (y = 0; y < u.length; y++) nt(u[y])
    }
    function nt(n, t, i) {
        return n.setFullYear(n.getFullYear() + t),
        i || s(n),
        n
    }
    function tt(n, t, r) {
        if ( + n) {
            var f = n.getMonth() + t,
            u = i(n);
            for (u.setDate(1), u.setMonth(f), n.setMonth(f), r || s(n); n.getMonth() != u.getMonth();) n.setDate(n.getDate() + (n < u ? 1 : -1))
        }
        return n
    }
    function r(n, t, r) {
        if ( + n) {
            var f = n.getDate() + t,
            u = i(n);
            u.setHours(9);
            u.setDate(f);
            n.setDate(f);
            r || s(n);
            it(n, u)
        }
        return n
    }
    function it(n, t) {
        if ( + n) while (n.getDate() != t.getDate()) n.setTime( + n + (n < t ? 1 : -1) * wr)
    }
    function u(n, t) {
        return n.setMinutes(n.getMinutes() + t),
        n
    }
    function s(n) {
        return n.setHours(0),
        n.setMinutes(0),
        n.setSeconds(0),
        n.setMilliseconds(0),
        n
    }
    function i(n, t) {
        return t ? s(new Date( + n)) : new Date( + n)
    }
    function yt() {
        var t = 0,
        n;
        do n = new Date(1970, t++, 1);
        while (n.getHours());
        return n
    }
    function o(n, t, i) {
        for (t = t || 1; ! n.getDay() || i && n.getDay() == 1 || !i && n.getDay() == 6;) r(n, t);
        return n
    }
    function e(n, t) {
        return Math.round((i(n, !0) - i(t, !0)) / pi)
    }
    function pt(n, i, r, u) {
        i !== t && i != n.getFullYear() && (n.setDate(1), n.setMonth(0), n.setFullYear(i));
        r !== t && r != n.getMonth() && (n.setDate(1), n.setMonth(r));
        u !== t && n.setDate(u)
    }
    function rt(n, i) {
        return typeof n == "object" ? n: typeof n == "number" ? new Date(n * 1e3) : typeof n == "string" ? n.match(/^\d+(\.\d+)?$/) ? new Date(parseFloat(n) * 1e3) : (i === t && (i = !0), wt(n, i) || (n ? new Date(n) : null)) : null
    }
    function wt(n, t) {
        var i = n.match(/^([0-9]{4})(-([0-9]{2})(-([0-9]{2})([T ]([0-9]{2}):([0-9]{2})(:([0-9]{2})(\.([0-9]+))?)?(Z|(([-+])([0-9]{2})(:?([0-9]{2}))?))?)?)?)?$/),
        r,
        u,
        f;
        return i ? (r = new Date(i[1], 0, 1), t || !i[13] ? (u = new Date(i[1], 0, 1, 9, 0), i[3] && (r.setMonth(i[3] - 1), u.setMonth(i[3] - 1)), i[5] && (r.setDate(i[5]), u.setDate(i[5])), it(r, u), i[7] && r.setHours(i[7]), i[8] && r.setMinutes(i[8]), i[10] && r.setSeconds(i[10]), i[12] && r.setMilliseconds(Number("0." + i[12]) * 1e3), it(r, u)) : (r.setUTCFullYear(i[1], i[3] ? i[3] - 1 : 0, i[5] || 1), r.setUTCHours(i[7] || 0, i[8] || 0, i[10] || 0, i[12] ? Number("0." + i[12]) * 1e3: 0), i[14] && (f = Number(i[16]) * 60 + (i[18] ? Number(i[18]) : 0), f *= i[15] == "-" ? 1 : -1, r = new Date( + r + f * 6e4))), r) : null
    }
    function ut(n) {
        var t, i;
        return typeof n == "number" ? n * 60 : typeof n == "object" ? n.getHours() * 60 + n.getMinutes() : (t = n.match(/(\d+)(?::(\d+))?\s*(\w+)?/), t ? (i = parseInt(t[1], 10), t[3] && (i %= 12, t[3].toLowerCase().charAt(0) == "p" && (i += 12)), i * 60 + (t[2] ? parseInt(t[2], 10) : 0)) : void 0)
    }
    function c(n, t, i) {
        return ft(n, null, t, i)
    }
    function ft(n, t, i, r) {
        var v, h;
        r = r || g;
        for (var e = n,
        a = t,
        l = i.length,
        o, u, y, s = "",
        f = 0; f < l; f++) if (o = i.charAt(f), o == "'") {
            for (u = f + 1; u < l; u++) if (i.charAt(u) == "'") {
                e && (s += u == f + 1 ? "'": i.substring(f + 1, u), f = u);
                break
            }
        } else if (o == "(") {
            for (u = f + 1; u < l; u++) if (i.charAt(u) == ")") {
                h = c(e, i.substring(f + 1, u), r);
                parseInt(h.replace(/\D/, ""), 10) && (s += h);
                f = u;
                break
            }
        } else if (o == "[") {
            for (u = f + 1; u < l; u++) if (i.charAt(u) == "]") {
                v = i.substring(f + 1, u);
                h = c(e, v, r);
                h != c(a, v, r) && (s += h);
                f = u;
                break
            }
        } else if (o == "{") e = t,
        a = n;
        else if (o == "}") e = n,
        a = t;
        else {
            for (u = l; u > f; u--) if (y = kr[i.substring(f, u)]) {
                e && (s += y(e, r));
                f = u - 1;
                break
            }
            u == f && e && (s += o)
        }
        return s
    }
    function v(n) {
        return n.end ? di(n.end, n.allDay) : r(i(n.start), 1)
    }
    function di(n, t) {
        return n = i(n),
        t || n.getHours() || n.getMinutes() ? r(n, 1) : s(n)
    }
    function gi(n, t) {
        return (t.msLength - n.msLength) * 100 + (n.event.start - t.event.start)
    }
    function bt(n, t) {
        return n.end > t.start && n.start < t.end
    }
    function et(n, t, r, u) {
        for (var v = [], y = n.length, c, e, o, s, h, l, a, f = 0; f < y; f++) c = n[f],
        e = c.start,
        o = t[f],
        o > r && e < u && (e < r ? (s = i(r), l = !1) : (s = e, l = !0), o > u ? (h = i(u), a = !1) : (h = o, a = !0), v.push({
            event: c,
            start: s,
            end: h,
            isStart: l,
            isEnd: a,
            msLength: h - s
        }));
        return v.sort(gi)
    }
    function ot(n) {
        for (var i = [], o = n.length, u, t, e, f, r = 0; r < o; r++) {
            for (u = n[r], t = 0;;) {
                if (e = !1, i[t]) for (f = 0; f < i[t].length; f++) if (bt(i[t][f], u)) {
                    e = !0;
                    break
                }
                if (!e) break;
                t++
            }
            i[t] ? i[t].push(u) : i[t] = [u]
        }
        return i
    }
    function kt(i, r, u) {
        i.unbind("mouseover").mouseover(function(i) {
            for (var f = i.target,
            o, s, e; f != this;) o = f,
            f = f.parentNode; (s = o._fci) !== t && (o._fci = t, e = r[s], u(e.event, e.element, e), n(i.target).trigger(i));
            i.stopPropagation()
        })
    }
    function y(t, i, r) {
        for (var u = 0,
        f; u < t.length; u++) f = n(t[u]),
        f.width(Math.max(0, i - st(f, r)))
    }
    function dt(t, i, r) {
        for (var u = 0,
        f; u < t.length; u++) f = n(t[u]),
        f.height(Math.max(0, i - p(f, r)))
    }
    function st(n, t) {
        return nr(n) + ir(n) + (t ? tr(n) : 0)
    }
    function nr(t) {
        return (parseFloat(n.css(t[0], "paddingLeft", !0)) || 0) + (parseFloat(n.css(t[0], "paddingRight", !0)) || 0)
    }
    function tr(t) {
        return (parseFloat(n.css(t[0], "marginLeft", !0)) || 0) + (parseFloat(n.css(t[0], "marginRight", !0)) || 0)
    }
    function ir(t) {
        return (parseFloat(n.css(t[0], "borderLeftWidth", !0)) || 0) + (parseFloat(n.css(t[0], "borderRightWidth", !0)) || 0)
    }
    function p(n, t) {
        return rr(n) + ur(n) + (t ? gt(n) : 0)
    }
    function rr(t) {
        return (parseFloat(n.css(t[0], "paddingTop", !0)) || 0) + (parseFloat(n.css(t[0], "paddingBottom", !0)) || 0)
    }
    function gt(t) {
        return (parseFloat(n.css(t[0], "marginTop", !0)) || 0) + (parseFloat(n.css(t[0], "marginBottom", !0)) || 0)
    }
    function ur(t) {
        return (parseFloat(n.css(t[0], "borderTopWidth", !0)) || 0) + (parseFloat(n.css(t[0], "borderBottomWidth", !0)) || 0)
    }
    function b(n, t) {
        t = typeof t == "number" ? t + "px": t;
        n.each(function(n, i) {
            i.style.cssText += ";min-height:" + t + ";_height:" + t
        })
    }
    function ni() {}
    function ti(n, t) {
        return n - t
    }
    function ii(n) {
        return Math.max.apply(Math, n)
    }
    function l(n) {
        return (n < 10 ? "0": "") + n
    }
    function ht(n, i) {
        if (n[i] !== t) return n[i];
        for (var f = i.split(/(?=[A-Z])/), r = f.length - 1, u; r >= 0; r--) if (u = n[f[r].toLowerCase()], u !== t) return u;
        return n[""]
    }
    function a(n) {
        return n.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/'/g, "&#039;").replace(/"/g, "&quot;").replace(/\n/g, "<br />")
    }
    function ri(n) {
        return n.id + "/" + n.className + "/" + n.style.cssText.replace(/(^|;)\s*(top|left|width|height)\s*:[^;]*/ig, "")
    }
    function ct(n) {
        n.attr("unselectable", "on").css("MozUserSelect", "none").bind("selectstart.ui",
        function() {
            return ! 1
        })
    }
    function k(n) {
        n.children().removeClass("fc-first fc-last").filter(":first-child").addClass("fc-first").end().filter(":last-child").addClass("fc-last")
    }
    function lt(n, t) {
        n.each(function(n, i) {
            i.className = i.className.replace(/^fc-\w*/, "fc-" + pr[t.getDay()])
        })
    }
    function ui(n, t) {
        var i = n.source || {},
        u = n.color,
        f = i.color,
        e = t("eventColor"),
        o = n.backgroundColor || u || i.backgroundColor || f || t("eventBackgroundColor") || e,
        s = n.borderColor || u || i.borderColor || f || t("eventBorderColor") || e,
        h = n.textColor || i.textColor || t("eventTextColor"),
        r = [];
        return o && r.push("background-color:" + o),
        s && r.push("border-color:" + s),
        h && r.push("color:" + h),
        r.join(";")
    }
    function d(t, i, r) {
        if (n.isFunction(t) && (t = [t]), t) {
            for (var f, u = 0; u < t.length; u++) f = t[u].apply(i, r) || f;
            return f
        }
    }
    function w() {
        for (var n = 0; n < arguments.length; n++) if (arguments[n] !== t) return arguments[n]
    }
    function fr(n, t) {
        function e(n, t) {
            var e, v;
            t && (tt(n, t), n.setDate(1));
            e = i(n, !0);
            e.setDate(1);
            var y = tt(i(e), 1),
            l = i(e),
            c = i(y),
            p = f("firstDay"),
            a = f("weekends") ? 0 : 1;
            a && (o(l), o(c, -1, !0));
            r(l, -((l.getDay() - Math.max(p, a) + 7) % 7));
            r(c, (7 - c.getDay() + Math.max(p, a)) % 7);
            v = Math.round((c - l) / (pi * 7));
            f("weekMode") == "fixed" && (r(c, (6 - v) * 7), v = 6);
            u.title = h(e, f("titleFormat"));
            u.start = e;
            u.end = y;
            u.visStart = l;
            u.visEnd = c;
            s(6, v, a ? 5 : 7, !0)
        }
        var u = this;
        u.render = e;
        at.call(u, n, t, "month");
        var f = u.opt,
        s = u.renderBasic,
        h = t.formatDate
    }
    function er(n, t) {
        function e(n, t) {
            t && r(n, t * 7);
            var e = r(i(n), -((n.getDay() - f("firstDay") + 7) % 7)),
            a = r(i(e), 7),
            c = i(e),
            l = i(a),
            v = f("weekends");
            v || (o(c), o(l, -1, !0));
            u.title = h(c, r(i(l), -1), f("titleFormat"));
            u.start = e;
            u.end = a;
            u.visStart = c;
            u.visEnd = l;
            s(1, 1, v ? 7 : 5, !1)
        }
        var u = this;
        u.render = e;
        at.call(u, n, t, "basicWeek");
        var f = u.opt,
        s = u.renderBasic,
        h = t.formatDates
    }
    function or(n, t) {
        function e(n, t) {
            t && (r(n, t), f("weekends") || o(n, t < 0 ? -1 : 1));
            u.title = h(n, f("titleFormat"));
            u.start = u.visStart = i(n, !0);
            u.end = u.visEnd = r(i(u.start), 1);
            s(1, 1, 1, !1)
        }
        var u = this;
        u.render = e;
        at.call(u, n, t, "basicDay");
        var f = u.opt,
        s = u.renderBasic,
        h = t.formatDate
    }
    function at(t, u, f) {
        function vi(n, t, i, r) {
            c = t;
            h = i;
            yi();
            var u = !it;
            u ? pi(n, r) : lr();
            wi(u)
        }
        function yi() {
            vt = l("isRTL");
            vt ? (v = -1, w = h - 1) : (v = 1, w = 0);
            ui = l("firstDay");
            fi = l("weekends") ? 0 : 1;
            nt = l("theme") ? "ui": "fc";
            oi = l("columnFormat")
        }
        function pi(i, r) {
            for (var s = nt + "-widget-header",
            c = nt + "-widget-content",
            e, o, u = "<table class='fc-border-separate' style='width:100%' cellspacing='0'><thead><tr>",
            f = 0; f < h; f++) u += "<th class='fc- " + s + "'/>";
            for (u += "<\/tr><\/thead><tbody>", f = 0; f < i; f++) {
                for (u += "<tr class='fc-week" + f + "'>", e = 0; e < h; e++) u += "<td class='fc- " + c + " fc-day" + (f * h + e) + "'><div>" + (r ? "<div class='fc-day-number'/>": "") + "<div class='fc-day-content'><div style='position:relative'>&nbsp;<\/div><\/div><\/div><\/td>";
                u += "<\/tr>"
            }
            u += "<\/tbody><\/table>";
            o = n(u).appendTo(t);
            d = o.find("thead");
            tt = d.find("th");
            it = o.find("tbody");
            a = it.find("tr");
            g = it.find("td");
            ni = g.filter(":first-child");
            ti = a.eq(0).find("div.fc-day-content div");
            k(d.add(d.find("tr")));
            k(a);
            a.eq(0).addClass("fc-first");
            yt(g);
            ii = n("<div style='position:absolute;z-index:8;top:0;left:0'/>").appendTo(t)
        }
        function wi(t) {
            var f = t || c == 1,
            e = o.start.getMonth(),
            h = s(new Date),
            i,
            r,
            u;
            f && tt.each(function(t, u) {
                i = n(u);
                r = et(t);
                i.html(yr(r, oi));
                lt(i, r)
            });
            g.each(function(t, u) {
                i = n(u);
                r = et(t);
                r.getMonth() == e ? i.removeClass("fc-other-month") : i.addClass("fc-other-month"); + r == +h ? i.addClass(nt + "-state-highlight fc-today") : i.removeClass(nt + "-state-highlight fc-today");
                i.find("div.fc-day-number").text(r.getDate());
                f && lt(i, r)
            });
            a.each(function(t, i) {
                u = n(i);
                t < c ? (u.show(), t == c - 1 ? u.addClass("fc-last") : u.removeClass("fc-last")) : u.hide()
            })
        }
        function bi(t) {
            ri = t;
            var r = ri - d.height(),
            i,
            u,
            f;
            l("weekMode") == "variable" ? i = u = Math.floor(r / (c == 1 ? 2 : 6)) : (i = Math.floor(r / c), u = r - i * (c - 1));
            ni.each(function(t, r) {
                t < c && (f = n(r), b(f.find("> div"), (t == c - 1 ? u: i) - p(f)))
            })
        }
        function ki(n) {
            ht = n;
            ft.clear();
            at = Math.floor(ht / h);
            y(tt.slice(0, -1), at)
        }
        function yt(n) {
            n.click(di).mousedown(vr)
        }
        function di(n) {
            if (!l("selectable")) {
                var t = parseInt(this.className.match(/fc\-day(\d+)/)[1]),
                i = et(t);
                ot("dayClick", this, i, !0, n)
            }
        }
        function pt(n, t, u) {
            var f, y, s, l, a, p, b;
            for (u && rt.build(), f = i(o.visStart), y = r(i(f), h), s = 0; s < c; s++) l = new Date(Math.max(f, n)),
            a = new Date(Math.min(y, t)),
            l < a && (vt ? (p = e(a, f) * v + w + 1, b = e(l, f) * v + w + 1) : (p = e(l, f), b = e(a, f)), yt(wt(s, p, s, b - 1))),
            r(f, 7),
            r(y, 7)
        }
        function wt(n, i, r, u) {
            var f = rt.rect(n, i, r, u, t);
            return ar(f, t)
        }
        function gi(n) {
            return i(n)
        }
        function nr(n, t) {
            pt(n, r(i(t), 1), !0)
        }
        function tr() {
            st()
        }
        function ir(n, t, i) {
            var r = bt(n),
            u = g[r.row * h + r.col];
            ot("dayClick", u, n, t, i)
        }
        function rr(n, t) {
            ut.start(function(n) {
                st();
                n && wt(n.row, n.col, n.row, n.col)
            },
            t)
        }
        function ur(n, t, i) {
            var r = ut.stop(),
            u;
            st();
            r && (u = kt(r), ot("drop", n, u, !0, t, i))
        }
        function fr(n) {
            return i(n.start)
        }
        function er(n) {
            return ft.left(n)
        }
        function or(n) {
            return ft.right(n)
        }
        function bt(n) {
            return {
                row: Math.floor(e(n, o.visStart) / 7),
                col: gt(n.getDay())
            }
        }
        function kt(n) {
            return dt(n.row, n.col)
        }
        function dt(n, t) {
            return r(i(o.visStart), n * 7 + t * v + w)
        }
        function et(n) {
            return dt(Math.floor(n / h), n % h)
        }
        function gt(n) {
            return (n - Math.max(ui, fi) + h) % h * v + w
        }
        function hr(n) {
            return a.eq(n)
        }
        function cr() {
            return {
                left: 0,
                right: ht
            }
        }
        var o = this;
        o.renderBasic = vi;
        o.setHeight = bi;
        o.setWidth = ki;
        o.renderDayOverlay = pt;
        o.defaultSelectionEnd = gi;
        o.renderSelection = nr;
        o.clearSelection = tr;
        o.reportDayClick = ir;
        o.dragStart = rr;
        o.dragStop = ur;
        o.defaultEventEnd = fr;
        o.getHoverListener = function() {
            return ut
        };
        o.colContentLeft = er;
        o.colContentRight = or;
        o.dayOfWeekCol = gt;
        o.dateCell = bt;
        o.cellDate = kt;
        o.cellIsAllDay = function() {
            return ! 0
        };
        o.allDayRow = hr;
        o.allDayBounds = cr;
        o.getRowCnt = function() {
            return c
        };
        o.getColCnt = function() {
            return h
        };
        o.getColWidth = function() {
            return at
        };
        o.getDaySegmentContainer = function() {
            return ii
        };
        ei.call(o, t, u, f);
        hi.call(o);
        si.call(o);
        sr.call(o);
        var l = o.opt,
        ot = o.trigger,
        lr = o.clearEvents,
        ar = o.renderOverlay,
        st = o.clearOverlays,
        vr = o.daySelectionMousedown,
        yr = u.formatDate,
        d, tt, it, a, g, ni, ti, ii, ht, ri, at, c, h, rt, ut, ft, vt, v, w, ui, fi, nt, oi;
        ct(t.addClass("fc-grid"));
        rt = new ci(function(t, i) {
            var f, r, u;
            tt.each(function(t, e) {
                f = n(e);
                r = f.offset().left;
                t && (u[1] = r);
                u = [r];
                i[t] = u
            });
            u[1] = r + f.outerWidth();
            a.each(function(i, e) {
                i < c && (f = n(e), r = f.offset().top, i && (u[1] = r), u = [r], t[i] = u)
            });
            u[1] = r + f.outerHeight()
        });
        ut = new li(rt);
        ft = new ai(function(n) {
            return ti.eq(n)
        })
    }
    function sr() {
        function s(n, t) {
            p(n);
            ft(f(n), t)
        }
        function h() {
            w();
            nt().empty()
        }
        function f(u) {
            for (var p = rt(), w = ut(), h = i(t.visStart), a = r(i(h), w), b = n.map(u, v), c, f, l, o, s, y = [], e = 0; e < p; e++) {
                for (c = ot(et(u, b, h, a)), f = 0; f < c.length; f++) for (l = c[f], o = 0; o < l.length; o++) s = l[o],
                s.row = e,
                s.level = f,
                y.push(s);
                r(h, 7);
                r(a, 7)
            }
            return y
        }
        function c(n, t, i) {
            a(n) && l(n, t);
            i.isEnd && y(n) && st(n, t, i);
            b(n, t)
        }
        function l(n, t) {
            var s = tt(),
            f;
            t.draggable({
                zIndex: 9,
                delay: 50,
                opacity: u("dragOpacity"),
                revertDuration: u("dragRevertDuration"),
                start: function(h, c) {
                    e("eventDragStart", t, n, h, c);
                    d(n, t);
                    s.start(function(e, s, h, c) {
                        t.draggable("option", "revert", !e || !h && !c);
                        o();
                        e ? (f = h * 7 + c * (u("isRTL") ? -1 : 1), it(r(i(n.start), f), r(v(n), f))) : f = 0
                    },
                    h, "drag")
                },
                stop: function(i, r) {
                    s.stop();
                    o();
                    e("eventDragStop", t, n, i, r);
                    f ? g(this, n, f, 0, n.allDay, i, r) : (t.css("filter", ""), k(n, t))
                }
            })
        }
        var t = this;
        t.renderEvents = s;
        t.compileDaySegs = f;
        t.clearEvents = h;
        t.bindDaySeg = c;
        oi.call(t);
        var u = t.opt,
        e = t.trigger,
        a = t.isEventDraggable,
        y = t.isEventResizable,
        p = t.reportEvents,
        w = t.reportEventClear,
        b = t.eventElementHandlers,
        k = t.showEvents,
        d = t.hideEvents,
        g = t.eventDrop,
        nt = t.getDaySegmentContainer,
        tt = t.getHoverListener,
        it = t.renderDayOverlay,
        o = t.clearOverlays,
        rt = t.getRowCnt,
        ut = t.getColCnt,
        ft = t.renderDaySegs,
        st = t.resizableDayEvent
    }
    function hr(n, t) {
        function e(n, t) {
            t && r(n, t * 7);
            var e = r(i(n), -((n.getDay() - f("firstDay") + 7) % 7)),
            a = r(i(e), 7),
            c = i(e),
            l = i(a),
            v = f("weekends");
            v || (o(c), o(l, -1, !0));
            u.title = h(c, r(i(l), -1), f("titleFormat"));
            u.start = e;
            u.end = a;
            u.visStart = c;
            u.visEnd = l;
            s(v ? 7 : 5)
        }
        var u = this;
        u.render = e;
        fi.call(u, n, t, "agendaWeek");
        var f = u.opt,
        s = u.renderAgenda,
        h = t.formatDates
    }
    function cr(n, t) {
        function e(n, t) {
            t && (r(n, t), f("weekends") || o(n, t < 0 ? -1 : 1));
            var e = i(n, !0),
            c = r(i(e), 1);
            u.title = h(n, f("titleFormat"));
            u.start = u.visStart = e;
            u.end = u.visEnd = c;
            s(1)
        }
        var u = this;
        u.render = e;
        fi.call(u, n, t, "agendaDay");
        var f = u.opt,
        s = u.renderAgenda,
        h = t.formatDate
    }
    function fi(f, o, h) {
        function uu(n) {
            a = n;
            fu();
            st ? uf() : eu();
            ou()
        }
        function fu() {
            gt = l("theme") ? "ui": "fc";
            tu = l("weekends") ? 0 : 1;
            nu = l("firstDay"); (iu = l("isRTL")) ? (g = -1, nt = a - 1) : (g = 1, nt = 0);
            et = ut(l("minTime"));
            pi = ut(l("maxTime"));
            ru = l("columnFormat")
        }
        function eu() {
            for (var e = gt + "-widget-header",
            s = gt + "-widget-content",
            o, c, h, y = l("slotMinutes") % 15 == 0, t = "<table style='width:100%' class='fc-agenda-days fc-border-separate' cellspacing='0'><thead><tr><th class='fc-agenda-axis " + e + "'>&nbsp;<\/th>", r = 0; r < a; r++) t += "<th class='fc- fc-col" + r + " " + e + "'/>";
            for (t += "<th class='fc-agenda-gutter " + e + "'>&nbsp;<\/th><\/tr><\/thead><tbody><tr><th class='fc-agenda-axis " + e + "'>&nbsp;<\/th>", r = 0; r < a; r++) t += "<td class='fc- fc-col" + r + " " + s + "'><div><div class='fc-day-content'><div style='position:relative'>&nbsp;<\/div><\/div><\/div><\/td>";
            for (t += "<td class='fc-agenda-gutter " + s + "'>&nbsp;<\/td><\/tr><\/tbody><\/table>", st = n(t).appendTo(f), wt = st.find("thead"), ri = wt.find("th").slice(1, -1), bt = st.find("tbody"), tt = bt.find("td").slice(0, -1), yr = tt.find("div.fc-day-content div"), gi = tt.eq(0), pr = gi.find("> div"), k(wt.add(wt.find("tr"))), k(bt.add(bt.find("tr"))), ht = wt.find("th:first"), at = st.find(".fc-agenda-gutter"), d = n("<div style='position:absolute;z-index:2;left:0;width:100%'/>").appendTo(f), l("allDaySlot") ? (nr = n("<div style='position:absolute;z-index:8;top:0;left:0'/>").appendTo(d), t = "<table style='width:100%' class='fc-agenda-allday' cellspacing='0'><tr><th class='" + e + " fc-agenda-axis'>" + l("allDayText") + "<\/th><td><div class='fc-day-content'><div style='position:relative'/><\/div><\/td><th class='" + e + " fc-agenda-gutter'>&nbsp;<\/th><\/tr><\/table>", ui = n(t).appendTo(d), fi = ui.find("tr"), rr(fi.find("td")), ht = ht.add(ui.find("th:first")), at = at.add(ui.find("th.fc-agenda-gutter")), d.append("<div class='fc-agenda-divider " + e + "'><div class='fc-agenda-divider-inner'/><\/div>")) : nr = n([]), v = n("<div style='position:absolute;width:100%;overflow-x:hidden;overflow-y:auto'/>").appendTo(d), b = n("<div style='position:relative;width:100%;overflow:hidden'/>").appendTo(v), wr = n("<div style='position:absolute;z-index:8;top:0;left:0'/>").appendTo(b), t = "<table class='fc-agenda-slots' style='width:100%' cellspacing='0'><tbody>", o = yt(), c = u(i(o), pi), u(o, et), tr = 0, r = 0; o < c; r++) h = o.getMinutes(),
            t += "<tr class='fc-slot" + r + " " + (h ? "fc-minor": "") + "'><th class='fc-agenda-axis " + e + "'>" + (!y || !h ? vr(o, l("axisFormat")) : "&nbsp;") + "<\/th><td class='" + s + "'><div style='position:relative'>&nbsp;<\/div><\/td><\/tr>",
            u(o, l("slotMinutes")),
            tr++;
            t += "<\/tbody><\/table>";
            it = n(t).appendTo(b);
            br = it.find("div:first");
            wi(it.find("td"));
            ht = ht.add(it.find("th:first"))
        }
        function ou() {
            for (var r, t, i, u = s(new Date), n = 0; n < a; n++) i = bi(n),
            r = ri.eq(n),
            r.html(vr(i, ru)),
            t = tt.eq(n),
            +i == +u ? t.addClass(gt + "-state-highlight fc-today") : t.removeClass(gt + "-state-highlight fc-today"),
            lt(r.add(t), i)
        }
        function su(n, i) {
            n === t && (n = dr);
            dr = n;
            ir = {};
            var r = bt.position().top,
            u = v.position().top,
            f = Math.min(n - r, it.height() + u + 1);
            pr.height(f - p(gi));
            d.css("top", r);
            v.height(f - u - 1);
            kt = br.height() + 1;
            i && cu()
        }
        function hu(t) {
            kr = t;
            yi.clear();
            rt = 0;
            y(ht.width("").each(function(t, i) {
                rt = Math.max(rt, n(i).outerWidth())
            }), rt);
            var i = v[0].clientWidth;
            vi = v.width() - i;
            vi ? (y(at, vi), at.show().prev().removeClass("fc-last")) : at.hide().prev().addClass("fc-last");
            oi = Math.floor((i - rt) / a);
            y(ri.slice(0, -1), oi)
        }
        function cu() {
            function n() {
                v.scrollTop(u)
            }
            var t = yt(),
            r = i(t),
            u;
            r.setHours(l("firstHour"));
            u = ot(t, r) + 1;
            n();
            setTimeout(n, 0)
        }
        function lu() {
            gr = v.scrollTop()
        }
        function au() {
            v.scrollTop(gr)
        }
        function rr(n) {
            n.click(ur).mousedown(of)
        }
        function wi(n) {
            n.click(ur).mousedown(nf)
        }
        function ur(n) {
            var r, f;
            if (!l("selectable")) {
                var i = Math.min(a - 1, Math.floor((n.pageX - st.offset().left - rt) / oi)),
                t = bi(i),
                u = this.parentNode.className.match(/fc-slot(\d+)/);
                u ? (r = parseInt(u[1]) * l("slotMinutes"), f = Math.floor(r / 60), t.setHours(f), t.setMinutes(r % 60 + et), ii("dayClick", tt[i], t, !1, n)) : ii("dayClick", tt[i], t, !0, n)
            }
        }
        function fr(n, t, r) {
            r && ft.build();
            var o = i(c.visStart),
            u,
            f;
            iu ? (u = e(t, o) * g + nt + 1, f = e(n, o) * g + nt + 1) : (u = e(n, o), f = e(t, o));
            u = Math.max(0, u);
            f = Math.min(a, f);
            u < f && rr(er(0, u, 0, f - 1))
        }
        function er(n, t, i, r) {
            var u = ft.rect(n, t, i, r, d);
            return ar(u, d)
        }
        function or(n, t) {
            for (var e, o, u = i(c.visStart), h = r(i(u), 1), f = 0; f < a; f++) {
                if (e = new Date(Math.max(u, n)), o = new Date(Math.min(h, t)), e < o) {
                    var l = f * g + nt,
                    s = ft.rect(0, l, 0, l, b),
                    v = ot(u, e),
                    y = ot(u, o);
                    s.top = v;
                    s.height = y - v;
                    wi(ar(s, b))
                }
                r(u, 1);
                r(h, 1)
            }
        }
        function vu(n) {
            return yi.left(n)
        }
        function yu(n) {
            return yi.right(n)
        }
        function pu(n) {
            return {
                row: Math.floor(e(n, c.visStart) / 7),
                col: ki(n.getDay())
            }
        }
        function pt(n) {
            var i = bi(n.col),
            t = n.row;
            return l("allDaySlot") && t--,
            t >= 0 && u(i, et + t * l("slotMinutes")),
            i
        }
        function bi(n) {
            return r(i(c.visStart), n * g + nt)
        }
        function ni(n) {
            return l("allDaySlot") && !n.row
        }
        function ki(n) {
            return (n - Math.max(nu, tu) + a) % a * g + nt
        }
        function ot(n, r) {
            if (n = i(n, !0), r < u(i(n), et)) return 0;
            if (r >= u(i(n), pi)) return it.height();
            var f = l("slotMinutes"),
            s = r.getHours() * 60 + r.getMinutes() - et,
            e = Math.floor(s / f),
            o = ir[e];
            return o === t && (o = ir[e] = it.find("tr:eq(" + e + ") td div")[0].offsetTop),
            Math.max(0, Math.round(o - 1 + kt * (s % f / f)))
        }
        function wu() {
            return {
                left: rt,
                right: kr - vi
            }
        }
        function bu() {
            return fi
        }
        function ku(n) {
            var t = i(n.start);
            return n.allDay ? t: u(t, l("defaultEventMinutes"))
        }
        function du(n, t) {
            return t ? i(n) : u(i(n), l("slotMinutes"))
        }
        function gu(n, t, u) {
            u ? l("allDaySlot") && fr(n, r(i(t), 1), !0) : sr(n, t)
        }
        function sr(t, i) {
            var f = l("selectHelper"),
            u,
            s;
            if (ft.build(), f) {
                if (u = e(t, c.visStart) * g + nt, u >= 0 && u < a) {
                    var r = ft.rect(0, u, 0, u, b),
                    o = ot(t, t),
                    h = ot(t, i);
                    h > o && (r.top = o, r.height = h - o, r.left += 2, r.width -= 5, n.isFunction(f) ? (s = f(t, i), s && (r.position = "absolute", r.zIndex = 8, w = n(s).css(r).appendTo(b))) : (r.isStart = !0, r.isEnd = !0, w = n(sf({
                        title: "",
                        start: t,
                        end: i,
                        className: ["fc-select-helper"],
                        editable: !1
                    },
                    r)), w.css("opacity", l("dragOpacity"))), w && (wi(w), b.append(w), y(w, r.width, !0), dt(w, r.height, !0)))
                }
            } else or(t, i)
        }
        function hr() {
            di();
            w && (w.remove(), w = null)
        }
        function nf(t) {
            if (t.which == 1 && l("selectable")) {
                ef(t);
                var r;
                vt.start(function(n, t) {
                    if (hr(), n && n.col == t.col && !ni(n)) {
                        var f = pt(t),
                        e = pt(n);
                        r = [f, u(i(f), l("slotMinutes")), e, u(i(e), l("slotMinutes"))].sort(ti);
                        sr(r[0], r[3])
                    } else r = null
                },
                t);
                n(document).one("mouseup",
                function(n) {
                    vt.stop();
                    r && ( + r[0] == +r[1] && cr(r[0], !1, n), ff(r[0], r[3], !1, n))
                })
            }
        }
        function cr(n, t, i) {
            ii("dayClick", tt[ki(n.getDay())], n, t, i)
        }
        function tf(n, t) {
            vt.start(function(n) {
                if (di(), n) if (ni(n)) er(n.row, n.col, n.row, n.col);
                else {
                    var t = pt(n),
                    r = u(i(t), l("defaultEventMinutes"));
                    or(t, r)
                }
            },
            t)
        }
        function rf(n, t, i) {
            var r = vt.stop();
            di();
            r && ii("drop", n, pt(r), ni(r), t, i)
        }
        var c = this;
        c.renderAgenda = uu;
        c.setWidth = hu;
        c.setHeight = su;
        c.beforeHide = lu;
        c.afterShow = au;
        c.defaultEventEnd = ku;
        c.timePosition = ot;
        c.dayOfWeekCol = ki;
        c.dateCell = pu;
        c.cellDate = pt;
        c.cellIsAllDay = ni;
        c.allDayRow = bu;
        c.allDayBounds = wu;
        c.getHoverListener = function() {
            return vt
        };
        c.colContentLeft = vu;
        c.colContentRight = yu;
        c.getDaySegmentContainer = function() {
            return nr
        };
        c.getSlotSegmentContainer = function() {
            return wr
        };
        c.getMinMinute = function() {
            return et
        };
        c.getMaxMinute = function() {
            return pi
        };
        c.getBodyContent = function() {
            return b
        };
        c.getRowCnt = function() {
            return 1
        };
        c.getColCnt = function() {
            return a
        };
        c.getColWidth = function() {
            return oi
        };
        c.getSlotHeight = function() {
            return kt
        };
        c.defaultSelectionEnd = du;
        c.renderDayOverlay = fr;
        c.renderSelection = gu;
        c.clearSelection = hr;
        c.reportDayClick = cr;
        c.dragStart = tf;
        c.dragStop = rf;
        ei.call(c, f, o, h);
        hi.call(c);
        si.call(c);
        lr.call(c);
        var l = c.opt,
        ii = c.trigger,
        uf = c.clearEvents,
        ar = c.renderOverlay,
        di = c.clearOverlays,
        ff = c.reportSelection,
        ef = c.unselect,
        of = c.daySelectionMousedown,
        sf = c.slotSegHtml,
        vr = o.formatDate,
        st, wt, ri, bt, tt, yr, gi, pr, d, nr, ui, fi, v, b, wr, it, br, ht, at, w, kr, dr, rt, oi, vi, kt, gr, a, tr, ft, vt, yi, ir = {},
        gt, nu, tu, iu, g, nt, et, pi, ru;
        ct(f.addClass("fc-agenda"));
        ft = new ci(function(t, i) {
            function o(n) {
                return Math.max(h, Math.min(c, n))
            }
            var u, r, e, f;
            ri.each(function(t, f) {
                u = n(f);
                r = u.offset().left;
                t && (e[1] = r);
                e = [r];
                i[t] = e
            });
            e[1] = r + u.outerWidth();
            l("allDaySlot") && (u = fi, r = u.offset().top, t[0] = [r, r + u.outerHeight()]);
            var s = b.offset().top,
            h = v.offset().top,
            c = h + v.outerHeight();
            for (f = 0; f < tr; f++) t.push([o(s + kt * f), o(s + kt * (f + 1))])
        });
        vt = new li(ft);
        yi = new ai(function(n) {
            return yr.eq(n)
        })
    }
    function lr() {
        function vt(n, t) {
            ei(n);
            for (var f = n.length,
            r = [], u = [], i = 0; i < f; i++) n[i].allDay ? r.push(n[i]) : u.push(n[i]);
            e("allDaySlot") && (yi(d(r), t), hi());
            bt(pt(u), t)
        }
        function yt() {
            si();
            ci().empty();
            it().empty()
        }
        function d(t) {
            for (var o = ot(et(t, n.map(t, v), f.visStart, f.visEnd)), h = o.length, e, r, u, s = [], i = 0; i < h; i++) for (e = o[i], r = 0; r < e.length; r++) u = e[r],
            u.row = 0,
            u.level = i,
            s.push(u);
            return s
        }
        function pt(t) {
            for (var w = l(), y = ut(), b = li(), a = u(i(f.visStart), y), k = n.map(t, wt), s, e, v, h, c, p = [], o = 0; o < w; o++) {
                for (s = ot(et(t, k, a, u(i(a), b - y))), ar(s), e = 0; e < s.length; e++) for (v = s[e], h = 0; h < v.length; h++) c = v[h],
                c.col = o,
                c.level = e,
                p.push(c);
                r(a, 1, !0)
            }
            return p
        }
        function wt(n) {
            return n.end ? i(n.end) : u(i(n.start), e("defaultEventMinutes"))
        }
        function bt(i, r) {
            var s, k = i.length,
            u, h, ut, at, et, v, y, ot, c, ht, vt, yt = "",
            pt, f, w, wt = {},
            bt = {},
            d, a, ct, lt, tt = it(),
            dt,
            b,
            rt,
            gt = l();
            for ((dt = e("isRTL")) ? (b = -1, rt = gt - 1) : (b = 1, rt = 0), s = 0; s < k; s++) u = i[s],
            h = u.event,
            ut = ft(u.start, u.start),
            at = ft(u.start, u.end),
            et = u.col,
            v = u.level,
            y = u.forward || 0,
            ot = ai(et * b + rt),
            c = vi(et * b + rt) - ot,
            c = Math.min(c - 6, c * .95),
            ht = v ? c / (v + y + 1) : y ? (c / (y + 1) - 6) * 2 : c,
            vt = ot + c / (v + y + 1) * v * b + (dt ? c - ht: 0),
            u.top = ut,
            u.left = vt,
            u.outerWidth = ht,
            u.outerHeight = at - ut,
            yt += g(h, u);
            for (tt[0].innerHTML = yt, pt = tt.children(), s = 0; s < k; s++) u = i[s],
            h = u.event,
            f = n(pt[s]),
            w = o("eventRender", h, h, f),
            w === !1 ? f.remove() : (w && w !== !0 && (f.remove(), f = n(w).css({
                position: "absolute",
                top: u.top,
                left: u.left
            }).appendTo(tt)), u.element = f, h._id === r ? nt(h, f, u) : f[0]._fci = s, bi(h, f));
            for (kt(tt, i, nt), s = 0; s < k; s++) u = i[s],
            (f = u.element) && (a = wt[d = u.key = ri(f[0])], u.vsides = a === t ? wt[d] = p(f, !0) : a, a = bt[d], u.hsides = a === t ? bt[d] = st(f, !0) : a, ct = f.find("div.fc-event-content"), ct.length && (u.contentTop = ct[0].offsetTop));
            for (s = 0; s < k; s++) u = i[s],
            (f = u.element) && (f[0].style.width = Math.max(0, u.outerWidth - u.hsides) + "px", lt = Math.max(0, u.outerHeight - u.vsides), f[0].style.height = lt + "px", h = u.event, u.contentTop !== t && lt - u.contentTop < 10 && (f.find("div.fc-event-time").text(di(h.start, e("timeFormat")) + " - " + h.title), f.find("div.fc-event-title").remove()), o("eventAfterRender", h, h, f))
        }
        function g(n, t) {
            var r = "<",
            f = n.url,
            u = ui(n, e),
            o = u ? " style='" + u + "'": "",
            i = ["fc-event", "fc-event-skin", "fc-event-vert"];
            return h(n) && i.push("fc-event-draggable"),
            t.isStart && i.push("fc-corner-top"),
            t.isEnd && i.push("fc-corner-bottom"),
            i = i.concat(n.className),
            n.source && (i = i.concat(n.source.className || [])),
            r += f ? "a href='" + a(n.url) + "'": "div",
            r += " class='" + i.join(" ") + "' style='position:absolute;z-index:8;top:" + t.top + "px;left:" + t.left + "px;" + u + "'><div class='fc-event-inner fc-event-skin'" + o + "><div class='fc-event-head fc-event-skin'" + o + "><div class='fc-event-time'>" + a(k(n.start, n.end, e("timeFormat"))) + "<\/div><\/div><div class='fc-event-content'><div class='fc-event-title'>" + a(n.title) + "<\/div><\/div><div class='fc-event-bg'><\/div><\/div>",
            t.isEnd && c(n) && (r += "<div class='ui-resizable-handle ui-resizable-s'>=<\/div>"),
            r + ("<\/" + (f ? "a": "div") + ">")
        }
        function gt(n, t, i) {
            h(n) && ni(n, t, i.isStart);
            i.isEnd && c(n) && pi(n, t, i);
            tt(n, t)
        }
        function nt(n, t, i) {
            var r = t.find("div.fc-event-time");
            h(n) && ti(n, t, r);
            i.isEnd && c(n) && ii(n, t, r);
            tt(n, t)
        }
        function ni(n, t, u) {
            function l() {
                h || (t.width(a).height("").draggable("option", "grid", null), h = !0)
            }
            var a, f, h = !0,
            c, g = e("isRTL") ? -1 : 1,
            p = rt(),
            k = ht(),
            d = y(),
            nt = ut();
            t.draggable({
                zIndex: 9,
                opacity: e("dragOpacity", "month"),
                revertDuration: e("dragRevertDuration"),
                start: function(y, w) {
                    o("eventDragStart", t, n, y, w);
                    b(n, t);
                    a = t.width();
                    p.start(function(o, a, y, p) {
                        s();
                        o ? (f = !1, c = p * g, o.row ? u ? h && (t.width(k - 10), dt(t, d * Math.round((n.end ? (n.end - n.start) / br: e("defaultEventMinutes")) / e("slotMinutes"))), t.draggable("option", "grid", [k, 1]), h = !1) : f = !0 : (lt(r(i(n.start), c), r(v(n), c)), l()), f = f || h && !c) : (l(), f = !0);
                        t.draggable("option", "revert", f)
                    },
                    y, "drag")
                },
                stop: function(i, r) {
                    if (p.stop(), s(), o("eventDragStop", t, n, i, r), f) l(),
                    t.css("filter", ""),
                    w(n, t);
                    else {
                        var u = 0;
                        h || (u = Math.round((t.offset().top - wi().offset().top) / d) * e("slotMinutes") + nt - (n.start.getHours() * 60 + n.start.getMinutes()));
                        ct(this, n, c, u, h, i, r)
                    }
                }
            })
        }
        function ti(n, t, f) {
            function nt(t) {
                var o = u(i(n.start), t),
                r;
                n.end && (r = u(i(n.end), t));
                f.text(k(o, r, e("timeFormat")))
            }
            function tt() {
                h && (f.css("display", ""), t.draggable("option", "grid", [ut, g]), h = !1)
            }
            var p, h = !1,
            a, c, d, ft = e("isRTL") ? -1 : 1,
            it = rt(),
            et = l(),
            ut = ht(),
            g = y();
            t.draggable({
                zIndex: 9,
                scroll: !1,
                grid: [ut, g],
                axis: et == 1 ? "y": !1,
                opacity: e("dragOpacity"),
                revertDuration: e("dragRevertDuration"),
                start: function(u, l) {
                    o("eventDragStart", t, n, u, l);
                    b(n, t);
                    p = t.position();
                    c = d = 0;
                    it.start(function(u, o, c, l) {
                        t.draggable("option", "revert", !u);
                        s();
                        u && (a = l * ft, e("allDaySlot") && !u.row ? (h || (h = !0, f.hide(), t.draggable("option", "grid", null)), lt(r(i(n.start), a), r(v(n), a))) : tt())
                    },
                    u, "drag")
                },
                drag: function(n, t) {
                    c = Math.round((t.position.top - p.top) / g) * e("slotMinutes");
                    c != d && (h || nt(c), d = c)
                },
                stop: function(i, r) {
                    var u = it.stop();
                    s();
                    o("eventDragStop", t, n, i, r);
                    u && (a || c || h) ? ct(this, n, a, h ? 0 : c, h, i, r) : (tt(), t.css("filter", ""), t.css(p), nt(0), w(n, t))
                }
            })
        }
        function ii(n, t, i) {
            var r, f, s = y();
            t.resizable({
                handles: {
                    s: "div.ui-resizable-s"
                },
                grid: s,
                start: function(i, u) {
                    r = f = 0;
                    b(n, t);
                    t.css("z-index", 9);
                    o("eventResizeStart", this, n, i, u)
                },
                resize: function(o, h) {
                    r = Math.round((Math.max(s, t.height()) - h.originalSize.height) / s);
                    r != f && (i.text(k(n.start, !r && !n.end ? null: u(fi(n), e("slotMinutes") * r), e("timeFormat"))), f = r)
                },
                stop: function(i, u) {
                    o("eventResizeStop", this, n, i, u);
                    r ? ki(this, n, 0, e("slotMinutes") * r, i, u) : (t.css("z-index", 8), w(n, t))
                }
            })
        }
        var f = this;
        f.renderEvents = vt;
        f.compileDaySegs = d;
        f.clearEvents = yt;
        f.slotSegHtml = g;
        f.bindDaySeg = gt;
        oi.call(f);
        var e = f.opt,
        o = f.trigger,
        h = f.isEventDraggable,
        c = f.isEventResizable,
        fi = f.eventEnd,
        ei = f.reportEvents,
        si = f.reportEventClear,
        tt = f.eventElementHandlers,
        hi = f.setHeight,
        ci = f.getDaySegmentContainer,
        it = f.getSlotSegmentContainer,
        rt = f.getHoverListener,
        li = f.getMaxMinute,
        ut = f.getMinMinute,
        ft = f.timePosition,
        ai = f.colContentLeft,
        vi = f.colContentRight,
        yi = f.renderDaySegs,
        pi = f.resizableDayEvent,
        l = f.getColCnt,
        ht = f.getColWidth,
        y = f.getSlotHeight,
        wi = f.getBodyContent,
        bi = f.reportEventElement,
        w = f.showEvents,
        b = f.hideEvents,
        ct = f.eventDrop,
        ki = f.eventResize,
        lt = f.renderDayOverlay,
        s = f.clearOverlays,
        at = f.calendar,
        di = at.formatDate,
        k = at.formatDates
    }
    function ar(n) {
        for (var i, r, f, e, u, t = n.length - 1; t > 0; t--) for (f = n[t], i = 0; i < f.length; i++) for (e = f[i], r = 0; r < n[t - 1].length; r++) u = n[t - 1][r],
        bt(e, u) && (u.forward = Math.max(u.forward || 0, (e.forward || 0) + 1))
    }
    function ei(n, f, e) {
        function l(n, t) {
            var i = v[n];
            return typeof i == "object" ? ht(i, t || e) : i
        }
        function h(n, t) {
            return f.trigger.apply(f, [n, t || o].concat(Array.prototype.slice.call(arguments, 2), [o]))
        }
        function tt(n) {
            return y(n) && !l("disableDragging")
        }
        function it(n) {
            return y(n) && !l("disableResizing")
        }
        function y(n) {
            return w(n.editable, (n.source || {}).editable, l("editable"))
        }
        function rt(n) {
            s = {};
            for (var r = n.length,
            t, i = 0; i < r; i++) t = n[i],
            s[t._id] ? s[t._id].push(t) : s[t._id] = [t]
        }
        function p(n) {
            return n.end ? i(n.end) : at(n)
        }
        function ut(n, t) {
            nt.push(t);
            c[n._id] ? c[n._id].push(t) : c[n._id] = [t]
        }
        function ft() {
            nt = [];
            c = {}
        }
        function et(n, t) {
            t.click(function(i) {
                if (!t.hasClass("ui-draggable-dragging") && !t.hasClass("ui-resizable-resizing")) return h("eventClick", this, n, i)
            }).hover(function(t) {
                h("eventMouseover", this, n, t)
            },
            function(t) {
                h("eventMouseout", this, n, t)
            })
        }
        function ot(n, t) {
            b(n, t, "show")
        }
        function st(n, t) {
            b(n, t, "hide")
        }
        function b(n, t, i) {
            for (var u = c[n._id], f = u.length, r = 0; r < f; r++) t && u[r][0] == t[0] || u[r][i]()
        }
        function ct(n, t, i, r, u, f, e) {
            var c = t.allDay,
            o = t._id;
            k(s[o], i, r, u);
            h("eventDrop", n, t, i, r, u,
            function() {
                k(s[o], -i, -r, c);
                a(o)
            },
            f, e);
            a(o)
        }
        function lt(n, t, i, r, u, f) {
            var e = t._id;
            d(s[e], i, r);
            h("eventResize", n, t, i, r,
            function() {
                d(s[e], -i, -r);
                a(e)
            },
            u, f);
            a(e)
        }
        function k(n, i, f, e) {
            f = f || 0;
            for (var o, h = n.length,
            s = 0; s < h; s++) o = n[s],
            e !== t && (o.allDay = e),
            u(r(o.start, i, !0), f),
            o.end && (o.end = u(r(o.end, i, !0), f)),
            g(o, v)
        }
        function d(n, t, i) {
            i = i || 0;
            for (var f, o = n.length,
            e = 0; e < o; e++) f = n[e],
            f.end = u(r(p(f), t, !0), i),
            g(f, v)
        }
        var o = this;
        o.element = n;
        o.calendar = f;
        o.name = e;
        o.opt = l;
        o.trigger = h;
        o.isEventDraggable = tt;
        o.isEventResizable = it;
        o.reportEvents = rt;
        o.eventEnd = p;
        o.reportEventElement = ut;
        o.reportEventClear = ft;
        o.eventElementHandlers = et;
        o.showEvents = ot;
        o.hideEvents = st;
        o.eventDrop = ct;
        o.eventResize = lt;
        var at = o.defaultEventEnd,
        g = f.normalizeEvent,
        a = f.reportEventChange,
        s = {},
        nt = [],
        c = {},
        v = f.options
    }
    function oi() {
        function rt(n, t) {
            var a = nt(),
            d,
            tt = s(),
            it = k(),
            g = 0,
            r,
            rt,
            u,
            f,
            ut = n.length,
            i,
            e,
            o;
            for (a[0].innerHTML = h(n), c(n, a.children()), ft(n), et(n, a, t), l(n), v(n), y(n), d = p(), r = 0; r < tt; r++) {
                for (rt = 0, u = [], f = 0; f < it; f++) u[f] = 0;
                while (g < ut && (i = n[g]).row == r) {
                    for (e = ii(u.slice(i.startCol, i.endCol)), i.top = e, e += i.outerHeight, o = i.startCol; o < i.endCol; o++) u[o] = e;
                    g++
                }
                d[r].height(ii(u))
            }
            b(n, w(d))
        }
        function ut(t, i, r) {
            var o = n("<div/>"),
            u,
            s = nt(),
            f,
            a = t.length,
            e;
            for (o[0].innerHTML = h(t), u = o.children(), s.append(u), c(t, u), l(t), v(t), y(t), b(t, w(p())), u = [], f = 0; f < a; f++) e = t[f].element,
            e && (t[f].row === i && e.css("top", r), u.push(e[0]));
            return n(u)
        }
        function h(n) {
            for (var p = f("isRTL"), nt = n.length, t, i, l, r, w = dt(), b = w.left, k = w.right, e, s, h, y, v, u = "", c = 0; c < nt; c++) t = n[c],
            i = t.event,
            r = ["fc-event", "fc-event-skin", "fc-event-hori"],
            ht(i) && r.push("fc-event-draggable"),
            p ? (t.isStart && r.push("fc-corner-right"), t.isEnd && r.push("fc-corner-left"), e = o(t.end.getDay() - 1), s = o(t.start.getDay()), h = t.isEnd ? d(e) : b, y = t.isStart ? g(s) : k) : (t.isStart && r.push("fc-corner-left"), t.isEnd && r.push("fc-corner-right"), e = o(t.start.getDay()), s = o(t.end.getDay() - 1), h = t.isStart ? d(e) : b, y = t.isEnd ? g(s) : k),
            r = r.concat(i.className),
            i.source && (r = r.concat(i.source.className || [])),
            l = i.url,
            v = ui(i, f),
            u += l ? "<a href='" + a(l) + "'": "<div",
            u += " class='" + r.join(" ") + "' style='position:absolute;z-index:8;left:" + h + "px;" + v + "'><div class='fc-event-inner fc-event-skin'" + (v ? " style='" + v + "'": "") + ">",
            !i.allDay && t.isStart && (u += "<span class='fc-event-time'>" + a(fi(i.start, i.end, f("timeFormat"))) + "<\/span>"),
            u += "<span class='fc-event-title'>" + a(i.title) + "<\/span><\/div>",
            t.isEnd && lt(i) && (u += "<div class='ui-resizable-handle ui-resizable-" + (p ? "w": "e") + "'>&nbsp;&nbsp;&nbsp;<\/div>"),
            u += "<\/" + (l ? "a": "div") + ">",
            t.left = h,
            t.outerWidth = y - h,
            t.startCol = e,
            t.endCol = s + 1;
            return u
        }
        function c(t, i) {
            for (var h = t.length,
            o, s, u, r, f = 0; f < h; f++) o = t[f],
            s = o.event,
            u = n(i[f]),
            r = e("eventRender", s, s, u),
            r === !1 ? u.remove() : (r && r !== !0 && (r = n(r).css({
                position: "absolute",
                left: o.left
            }), u.replaceWith(r), u = r), o.element = u)
        }
        function ft(n) {
            for (var u = n.length,
            i, r, t = 0; t < u; t++) i = n[t],
            r = i.element,
            r && vt(i.event, r)
        }
        function et(n, t, i) {
            for (var o = n.length,
            u, f, e, r = 0; r < o; r++) u = n[r],
            f = u.element,
            f && (e = u.event, e._id === i ? tt(e, f, u) : f[0]._fci = r);
            kt(t, n, tt)
        }
        function l(n) {
            for (var s = n.length,
            r, u, e, f, o = {},
            i = 0; i < s; i++) r = n[i],
            u = r.element,
            u && (e = r.key = ri(u[0]), f = o[e], f === t && (f = o[e] = st(u, !0)), r.hsides = f)
        }
        function v(n) {
            for (var u = n.length,
            i, r, t = 0; t < u; t++) i = n[t],
            r = i.element,
            r && (r[0].style.width = Math.max(0, i.outerWidth - i.hsides) + "px")
        }
        function y(n) {
            for (var s = n.length,
            r, u, e, f, o = {},
            i = 0; i < s; i++) r = n[i],
            u = r.element,
            u && (e = r.key, f = o[e], f === t && (f = o[e] = gt(u)), r.outerHeight = u[0].offsetHeight + f)
        }
        function p() {
            for (var i = s(), t = [], n = 0; n < i; n++) t[n] = bt(n).find("td:first div.fc-day-content > div");
            return t
        }
        function w(n) {
            for (var r = n.length,
            i = [], t = 0; t < r; t++) i[t] = n[t][0].offsetTop;
            return i
        }
        function b(n, t) {
            for (var o = n.length,
            i, u, f, r = 0; r < o; r++) i = n[r],
            u = i.element,
            u && (u[0].style.top = t[i.row] + (i.top || 0) + "px", f = i.event, e("eventAfterRender", f, f, u))
        }
        function ot(t, o, h) {
            var c = f("isRTL"),
            l = c ? "w": "e",
            v = o.find("div.ui-resizable-" + l),
            a = !1;
            ct(o);
            o.mousedown(function(n) {
                n.preventDefault()
            }).click(function(n) {
                a && (n.preventDefault(), n.stopImmediatePropagation())
            });
            v.mousedown(function(f) {
                function nt(i) {
                    e("eventResizeStop", this, t, i);
                    n("body").css("cursor", "");
                    w.stop();
                    it();
                    y && wt(this, t, y, 0, i);
                    setTimeout(function() {
                        a = !1
                    },
                    0)
                }
                if (f.which == 1) {
                    a = !0;
                    var w = u.getHoverListener(),
                    tt = s(),
                    rt = k(),
                    b = c ? -1 : 1,
                    d = c ? rt - 1 : 0,
                    ft = o.css("top"),
                    y,
                    v,
                    g = n.extend({},
                    t),
                    p = ni(t.start);
                    oi();
                    n("body").css("cursor", l + "-resize").one("mouseup", nt);
                    e("eventResizeStart", this, t, f);
                    w.start(function(n, u) {
                        var e, f, o, s;
                        n && (e = Math.max(p.row, n.row), f = n.col, tt == 1 && (e = 0), e == p.row && (f = c ? Math.min(p.col, f) : Math.max(p.col, f)), y = e * 7 + f * b + d - (u.row * 7 + u.col * b + d), o = r(at(t), y, !0), y ? (g.end = o, s = v, v = ut(ti([g]), h.row, ft), v.find("*").css("cursor", l + "-resize"), s && s.remove(), pt(t)) : v && (yt(t), v.remove(), v = null), it(), ei(t.start, r(i(o), 1)))
                    },
                    f)
                }
            })
        }
        var u = this;
        u.renderDaySegs = rt;
        u.resizableDayEvent = ot;
        var f = u.opt,
        e = u.trigger,
        ht = u.isEventDraggable,
        lt = u.isEventResizable,
        at = u.eventEnd,
        vt = u.reportEventElement,
        yt = u.showEvents,
        pt = u.hideEvents,
        wt = u.eventResize,
        s = u.getRowCnt,
        k = u.getColCnt,
        si = u.getColWidth,
        bt = u.allDayRow,
        dt = u.allDayBounds,
        d = u.colContentLeft,
        g = u.colContentRight,
        o = u.dayOfWeekCol,
        ni = u.dateCell,
        ti = u.compileDaySegs,
        nt = u.getDaySegmentContainer,
        tt = u.bindDaySeg,
        fi = u.calendar.formatDates,
        ei = u.renderDayOverlay,
        it = u.clearOverlays,
        oi = u.clearSelection
    }
    function si() {
        function h(n, t, r) {
            i();
            t || (t = l(n, r));
            o(n, t, r);
            u(n, t, r)
        }
        function i(n) {
            f && (f = !1, s(), e("unselect", null, n))
        }
        function u(n, t, i, r) {
            f = !0;
            e("select", null, n, t, i, r)
        }
        function c(f) {
            var h = t.cellDate,
            l = t.cellIsAllDay,
            c = t.getHoverListener(),
            a = t.reportDayClick,
            v,
            e;
            if (f.which == 1 && r("selectable")) {
                i(f);
                v = this;
                c.start(function(n, t) {
                    s();
                    n && l(n) ? (e = [h(t), h(n)].sort(ti), o(e[0], e[1], !0)) : e = null
                },
                f);
                n(document).one("mouseup",
                function(n) {
                    c.stop();
                    e && ( + e[0] == +e[1] && a(e[0], !0, n), u(e[0], e[1], !0, n))
                })
            }
        }
        var t = this;
        t.select = h;
        t.unselect = i;
        t.reportSelection = u;
        t.daySelectionMousedown = c;
        var r = t.opt,
        e = t.trigger,
        l = t.defaultSelectionEnd,
        o = t.renderSelection,
        s = t.clearSelection,
        f = !1;
        r("selectable") && r("unselectAuto") && n(document).mousedown(function(t) {
            var u = r("unselectCancel");
            u && n(t.target).parents(u).length || i(t)
        })
    }
    function hi() {
        function u(r, u) {
            var f = i.shift();
            return f || (f = n("<div class='fc-cell-overlay' style='position:absolute;z-index:3'/>")),
            f[0].parentNode != u[0] && f.appendTo(u),
            t.push(f.css(r).show()),
            f
        }
        function f() {
            for (var n; n = t.shift();) i.push(n.hide().unbind())
        }
        var r = this,
        t, i;
        r.renderOverlay = u;
        r.clearOverlays = f;
        t = [];
        i = []
    }
    function ci(n) {
        var r = this,
        t, i;
        r.build = function() {
            t = [];
            i = [];
            n(t, i)
        };
        r.cell = function(n, r) {
            for (var o = t.length,
            s = i.length,
            f = -1,
            e = -1,
            u = 0; u < o; u++) if (r >= t[u][0] && r < t[u][1]) {
                f = u;
                break
            }
            for (u = 0; u < s; u++) if (n >= i[u][0] && n < i[u][1]) {
                e = u;
                break
            }
            return f >= 0 && e >= 0 ? {
                row: f,
                col: e
            }: null
        };
        r.rect = function(n, r, u, f, e) {
            var o = e.offset();
            return {
                top: t[n][0] - o.top,
                left: i[r][0] - o.left,
                width: i[f][1] - i[r][0],
                height: t[u][1] - t[n][0]
            }
        }
    }
    function li(t) {
        function u(n) {
            vr(n);
            var u = t.cell(n.pageX, n.pageY); (!u != !r || u && (u.row != r.row || u.col != r.col)) && (u ? (i || (i = u), e(u, i, u.row - i.row, u.col - i.col)) : e(u, i), r = u)
        }
        var o = this,
        f, e, i, r;
        o.start = function(o, s, h) {
            e = o;
            i = r = null;
            t.build();
            u(s);
            f = h || "mousemove";
            n(document).bind(f, u)
        };
        o.stop = function() {
            return n(document).unbind(f, u),
            r
        }
    }
    function vr(n) {
        n.pageX === t && (n.pageX = n.originalEvent.pageX, n.pageY = n.originalEvent.pageY)
    }
    function ai(n) {
        function e(t) {
            return f[t] = f[t] || n(t)
        }
        var i = this,
        f = {},
        r = {},
        u = {};
        i.left = function(n) {
            return r[n] = r[n] === t ? e(n).position().left: r[n]
        };
        i.right = function(n) {
            return u[n] = u[n] === t ? i.left(n) + e(n).width() : u[n]
        };
        i.clear = function() {
            f = {};
            r = {};
            u = {}
        }
    }
    var g = {
        defaultView: "month",
        aspectRatio: 1.35,
        header: {
            left: "title",
            center: "",
            right: "today prev,next"
        },
        weekends: !0,
        allDayDefault: !0,
        ignoreTimezone: !0,
        lazyFetching: !0,
        startParam: "start",
        endParam: "end",
        titleFormat: {
            month: "MMMM yyyy",
            week: "MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}",
            day: "dddd, MMM d, yyyy"
        },
        columnFormat: {
            month: "ddd",
            week: "ddd M/d",
            day: "dddd M/d"
        },
        timeFormat: {
            "": "H:mm"
        },
        isRTL: !1,
        firstDay: 0,
        monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        monthNamesShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        dayNames: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
        dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
        buttonText: {
            prev: "&nbsp;&#9668;&nbsp;",
            next: "&nbsp;&#9658;&nbsp;",
            prevYear: "&nbsp;&lt;&lt;&nbsp;",
            nextYear: "&nbsp;&gt;&gt;&nbsp;",
            today: "今天",
            month: "月",
            week: "周",
            day: "天"
        },
        theme: !1,
        buttonIcons: {
            prev: "circle-triangle-w",
            next: "circle-triangle-e"
        },
        unselectAuto: !0,
        dropAccept: "*"
    },
    yr = {
        header: {
            left: "next,prev today",
            center: "",
            right: "title"
        },
        buttonText: {
            prev: "&nbsp;&#9658;&nbsp;",
            next: "&nbsp;&#9668;&nbsp;",
            prevYear: "&nbsp;&gt;&gt;&nbsp;",
            nextYear: "&nbsp;&lt;&lt;&nbsp;"
        },
        buttonIcons: {
            prev: "circle-triangle-e",
            next: "circle-triangle-w"
        }
    },
    f = n.fullCalendar = {
        version: "1.5.4"
    },
    h = f.views = {},
    vi,
    yi;
    n.fn.fullCalendar = function(i) {
        var f, r, u;
        return typeof i == "string" ? (f = Array.prototype.slice.call(arguments, 1), this.each(function() {
            var u = n.data(this, "fullCalendar"),
            e;
            u && n.isFunction(u[i]) && (e = u[i].apply(u, f), r === t && (r = e), i == "destroy" && n.removeData(this, "fullCalendar"))
        }), r !== t ? r: this) : (u = i.eventSources || [], delete i.eventSources, i.events && (u.push(i.events), delete i.events), i = n.extend(!0, {},
        g, i.isRTL || i.isRTL === t && g.isRTL ? yr: {},
        i), this.each(function(t, r) {
            var f = n(r),
            e = new wi(f, i, u);
            f.data("fullCalendar", e);
            e.render()
        }), this)
    };
    f.sourceNormalizers = [];
    f.sourceFetchers = [];
    vi = {
        dataType: "json",
        cache: !1
    };
    yi = 1;
    f.addDays = r;
    f.cloneDate = i;
    f.parseDate = rt;
    f.parseISO8601 = wt;
    f.parseTime = ut;
    f.formatDate = c;
    f.formatDates = ft;
    var pr = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"],
    pi = 864e5,
    wr = 36e5,
    br = 6e4,
    kr = {
        s: function(n) {
            return n.getSeconds()
        },
        ss: function(n) {
            return l(n.getSeconds())
        },
        m: function(n) {
            return n.getMinutes()
        },
        mm: function(n) {
            return l(n.getMinutes())
        },
        h: function(n) {
            return n.getHours() % 12 || 12
        },
        hh: function(n) {
            return l(n.getHours() % 12 || 12)
        },
        H: function(n) {
            return n.getHours()
        },
        HH: function(n) {
            return l(n.getHours())
        },
        d: function(n) {
            return n.getDate()
        },
        dd: function(n) {
            return l(n.getDate())
        },
        ddd: function(n, t) {
            return t.dayNamesShort[n.getDay()]
        },
        dddd: function(n, t) {
            return t.dayNames[n.getDay()]
        },
        M: function(n) {
            return n.getMonth() + 1
        },
        MM: function(n) {
            return l(n.getMonth() + 1)
        },
        MMM: function(n, t) {
            return t.monthNamesShort[n.getMonth()]
        },
        MMMM: function(n, t) {
            return t.monthNames[n.getMonth()]
        },
        yy: function(n) {
            return (n.getFullYear() + "").substring(2)
        },
        yyyy: function(n) {
            return n.getFullYear()
        },
        t: function(n) {
            return n.getHours() < 12 ? "a": "p"
        },
        tt: function(n) {
            return n.getHours() < 12 ? "am": "pm"
        },
        T: function(n) {
            return n.getHours() < 12 ? "A": "P"
        },
        TT: function(n) {
            return n.getHours() < 12 ? "AM": "PM"
        },
        u: function(n) {
            return c(n, "yyyy-MM-dd'T'HH:mm:ss'Z'")
        },
        S: function(n) {
            var t = n.getDate();
            return t > 10 && t < 20 ? "th": ["st", "nd", "rd"][t % 10 - 1] || "th"
        }
    };
    f.applyAll = d;
    h.month = fr;
    h.basicWeek = er;
    h.basicDay = or;
    vt({
        weekMode: "fixed"
    });
    h.agendaWeek = hr;
    h.agendaDay = cr;
    vt({
        allDaySlot: !0,
        allDayText: "全天",
        firstHour: 6,
        slotMinutes: 30,
        defaultEventMinutes: 120,
        axisFormat: "H:mm",
        timeFormat: {
            agenda: "H:mm{ - H:mm}"
        },
        dragOpacity: {
            agenda: .5
        },
        minTime: 0,
        maxTime: 24
    })
})(jQuery);