var myCalendar = (function(b) {
    var a = {
        0 : "日",
        1 : "一",
        2 : "二",
        3 : "三",
        4 : "四",
        5 : "五",
        6 : "六"
    };
    function c(d) {
        this.curTime = new Date();
        this.table = document.createDocumentFragment();
        for (o in d) {
            this[o] = d[o]
        }
        this.month = this.curTime.getMonth();
        this.year = this.curTime.getFullYear();
        this.date = this.curTime.getDate()
    }
    c.prototype = {
        fillDate: function(g) {
            var h = this;
            var e = h.getMonthOBJ(new Date(h.year, h.month - 1, 1));
            var f = h.getMonthOBJ(new Date(h.year, h.month + 1, 1));
            var j = h.getMonthOBJ(h.curTime);
            var d = (0 == j.lastMonthLength) ? [] : e.arr.slice( - j.lastMonthLength);
            var i = (0 == j.nextMonthLength) ? [] : f.arr.slice(0, j.nextMonthLength);
            Array.prototype.unshift.call(d, 0, 0);
            Array.prototype.splice.apply(j.arr, d);
            Array.prototype.push.apply(j.arr, i);
            h.curMonth = j;
            return h
        },
        draw: function(h) {
            var j = this;
            var l = document.createDocumentFragment();
            var f = j.curMonth.arr.slice(0);
            while (f.length > 0) {
                var k = document.createElement("tr");
                for (var g = 0,
                e; g < 7; g++) {
                    e = f.shift();
                    e.si = new Date(e.year, e.month, e.date).valueOf();
                    var d = document.createElement("td");
                    d.innerHTML = e.date;
                    k.appendChild(d);
                    h && h.call(this, d, e)
                }
                l.appendChild(k)
            }
            j.table.innerHTML = "";
            j.table.appendChild(l);
            return j
        },
        getMonthOBJ: function(e, h) {
            var g = this;
            var n = {
                arr: []
            };
            var k = e.getFullYear();
            var j = e.getMonth();
            var e = new Date(k, j + 1, 0);
            for (var f = 1,
            m = e.getDate(); f <= m; f++) {
                n.arr.push({
                    date: f,
                    month: j,
                    year: k
                })
            }
            var d = new Date(g.year, g.month, 1).getDay();
            var l = new Date(g.year, g.month, f - 1).getDay();
            n.lastMonthLength = d % 7;
            n.nextMonthLength = 6 - l;
            return n
        }
    };
    return c
})(iTemplate);