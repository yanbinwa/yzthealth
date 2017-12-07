lunarCalc = {
	lunarInfo: [21936, 10102, 9584, 21168, 43685, 59728, 27296, 47779, 43856, 19416, 19168, 42352, 21717, 53856, 55632, 91476, 22176, 39632, 21970, 19168, 42422, 42192, 53840, 119381, 46400, 54944, 44450, 38320, 84343, 18800, 42160, 46261, 27216, 27968, 109396, 11104, 38256, 21234, 18800, 25958, 54432, 59984, 28309, 23248, 11104, 100067, 37600, 116951, 51536, 54432, 120998, 46416, 22176, 107956, 9680, 37584, 53938, 43344, 46423, 27808, 46416, 86869, 19872, 42448, 83315, 21200, 43432, 59728, 27296, 44710, 43856, 19296, 43748, 42352, 21088, 62051, 55632, 23383, 22176, 38608,
	19925, 19152, 42192, 54484, 53840, 54616, 46400, 46496, 103846, 38320, 18864, 43380, 42160, 45690, 27216, 27968, 44870, 43872, 38256, 19189, 18800, 25776, 29859, 59984, 27480, 21952, 43872, 38613, 37600, 51552, 55636, 54432, 55888, 30034, 22176, 43959, 9680, 37584, 51893, 43344, 46240, 47780, 44368, 21977, 19360, 42416, 86390, 21168, 43312, 31060, 27296, 44368, 23378, 19296, 42726, 42208, 53856, 60005, 54576, 23200, 30371, 38608, 19415, 19152, 42192, 118966, 53840, 54560, 56645, 46496, 22224, 21938, 18864, 42359, 42160, 43600, 111189, 27936, 44448, 19299, 37759, 18936, 18800, 25776,
	26790, 59999, 27424, 42692, 43759, 37600, 53987, 51552, 54615, 54432, 55888, 23893, 22176, 42704, 21972, 21200, 43448, 43344, 46240, 46758, 44368, 21920, 43940, 42416, 21168, 45683, 26928, 29495, 27296, 44368, 19285, 19311, 42352, 21732, 53856, 59752, 54560, 55968, 27302, 22239, 19168, 43476, 42192, 53584, 62034, 54560],
	beginYear: 1891,
	solarMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
	Gan: "\u7532,\u4e59,\u4e19,\u4e01,\u620a,\u5df1,\u5e9a,\u8f9b,\u58ec,\u7678".split(","),
	Zhi: "\u5b50,\u4e11,\u5bc5,\u536f,\u8fb0,\u5df3,\u5348,\u672a,\u7533,\u9149,\u620c,\u4ea5".split(","),
	Animals: "\u9f20,\u725b,\u864e,\u5154,\u9f99,\u86c7,\u9a6c,\u7f8a,\u7334,\u9e21,\u72d7,\u732a".split(","),
	sTermInfo: [0, 21208, 42467, 63836, 85337, 107014, 128867, 150921, 173149, 195551, 218072, 240693, 263343, 285989, 308563, 331033, 353350, 375494, 397447, 419210, 440795, 462224, 483532, 504758],
	nStr1: "\u65e5,\u4e00,\u4e8c,\u4e09,\u56db,\u4e94,\u516d,\u4e03,\u516b,\u4e5d,\u5341".split(","),
	nStr2: ["\u521d", "\u5341", "\u5eff", "\u5345", "\u3000"],
	nStr3: ",\u6b63,\u4e8c,\u4e09,\u56db,\u4e94,\u516d,\u4e03,\u516b,\u4e5d,\u5341,\u51ac,\u814a".split(","),
	//nStr3: ",\u6b63\u6708,\u4e8c\u6708,\u4e09\u6708,\u56db\u6708,\u4e94\u6708,\u516d\u6708,\u4e03\u6708,\u516b\u6708,\u4e5d\u6708,\u5341\u6708,\u51ac\u6708,\u814a\u6708".split(","),
	monthName: "JAN,FEB,MAR,APR,MAY,JUN,JUL,AUG,SEP,OCT,NOV,DEC".split(","),
	lYearDays: function(a) {
		var b,
		e = 348;
		for (b = 32768; b > 8; b >>= 1) e += lunarCalc.lunarInfo[a - lunarCalc.beginYear] & b ? 1: 0;
		return e + lunarCalc.leapDays(a)
	},
	getAnimal: function(a) {
		return Animals[(a - 4) % 12]
	},
	leapDays: function(a) {
		return lunarCalc.leapMonth(a) ? lunarCalc.lunarInfo[a - lunarCalc.beginYear] & 65536 ? 30: 29: 0
	},
	leapMonth: function(a) {
		return lunarCalc.lunarInfo[a - lunarCalc.beginYear] & 15
	},
	monthDays: function(a, b) {
		return b > 100 ? 29: lunarCalc.lunarInfo[a -
		lunarCalc.beginYear] & 65536 >> b ? 30: 29
	},
	Lunar: function(a) {
		var b = 0,
		e = 0,
		d = (a - new Date(lunarCalc.beginYear, 1, 8)) / 864E5;
		this.dayCyl = d + 40;
		this.monCyl = 14;
		for (a = lunarCalc.beginYear; a < 2100 && d > 0; a++) e = lunarCalc.lYearDays(a),
		d -= e,
		this.monCyl += 12;
		d < 0 && (d += e, a--, this.monCyl -= 12);
		this.year = a;
		this.yearCyl = a - 1864;
		b = lunarCalc.leapMonth(a);
		this.isLeap = false;
		for (a = 1; a < 13 && d > 0; a++) {
			b > 0 && a == b + 1 && this.isLeap == false ? (--a, this.isLeap = true, e = lunarCalc.leapDays(this.year)) : e = lunarCalc.monthDays(this.year, a);
			if (this.isLeap ==
			true && a == b + 1) this.isLeap = false;
			d -= e;
			this.isLeap == false && this.monCyl++
		}
		if (d == 0 && b > 0 && a == b + 1) this.isLeap ? this.isLeap = false: (this.isLeap = true, --a, --this.monCyl);
		d < 0 && (d += e, --a, --this.monCyl);
		this.month = a;
		this.day = d
	},
	solarDays: function(a, b) {
		return b == 1 ? a % 4 == 0 && a % 100 != 0 || a % 400 == 0 ? 29: 28: lunarCalc.solarMonth[b]
	},
	cyclical: function(a) {
		return lunarCalc.Gan[a % 10] + lunarCalc.Zhi[a % 12]
	},
	calElement: function(a, b, e, d, f, c, h, i, k, l, m) {
		this.isToday = false;
		this.sYear = a;
		this.sMonth = b;
		this.sDay = e;
		this.week = d;
		this.lYear = f;
		this.lMonth = c;
		this.lDay = h;
		this.isLeap = i;
		this.cYear = k;
		this.cMonth = l;
		this.cDay = m
	},
	sTerm: function(a, b) {
		return (new Date(3.15569259747E10 * (a - lunarCalc.beginYear) + sTermInfo[b] * 6E4 + Date.UTC(lunarCalc.beginYear, 0, 6, 2, 5))).getUTCDate()
	},
	calendar: function(a, b) {
		var e,
		d,
		f,
		c,
		h = 1,
		i,
		k = 0,
		l = Array(3),
		m = 0;
		b >= 12 && (a++, b -= 12);
		e = new Date(a, b, 1);
		this.length = lunarCalc.solarDays(a, b);
		this.firstWeek = e.getDay();
		for (var g = 0; g < this.length; g++) {
			if (h > k) e = new Date(a, b, g + 1),
			d = new lunarCalc.Lunar(e),
			f = d.year,
			c = d.month,
			h = d.day,
			k = (i =
			d.isLeap) ? lunarCalc.leapDays(f) : lunarCalc.monthDays(f, c),
			l[m++] = g - h + 1;
			e = lunarCalc.nStr1[(g + this.firstWeek) % 7];
			var n = lunarCalc.cyclical(d.yearCyl),
			o = lunarCalc.cyclical(d.monCyl),
			p = lunarCalc.cyclical(d.dayCyl++);
			this[g] = new lunarCalc.calElement(a, b + 1, g + 1, e, f, c, h++, i, n, o, p)
		}
	},
	cDay: function(a) {
		var b;
		switch (a) {
		case 10:
			b = "\u521d\u5341";
			break;
		case 20:
			b = "\u4e8c\u5341";
			break;
		case 30:
			b = "\u4e09\u5341";
			break;
		default:
			b = this.nStr2[Math.floor(a / 10)],
			b += this.nStr1[a % 10]
		}
		return b
	},
	cld: null,
	transform: function(a, b,e, d, f) {
		var c;
		if (d == 1 && f == 1 && lunarCalc.leapMonth(a) != b) return - 1;
		for (j = -1; j < 2; j++) {
			cld = new lunarCalc.calendar(a, b + j);
			for (c = 0; c < cld.length; c++) {
				if (d == 0 && cld[c].sYear == a && cld[c].sMonth == b && cld[c].sDay == e) return cld[c];
				if (d == 1 && cld[c].lYear == a && cld[c].lMonth == b && cld[c].lDay == e) {
					if (0 == f) return cld[c];
					if (1 == f && cld[c].isLeap) return cld[c]
				}
			}
		}
		return - 1
	},
	drawTest: function(a, b, e, d, f) {
		var c;
		if (d == 1 && f == 1 && lunarCalc.leapMonth(a) != b) return - 1;
		for (j = -1; j < 2; j++) {
			cld = new lunarCalc.calendar(a, b + j);
			for (c = 0; c < cld.length; c++) {
				if (d ==
				0 && cld[c].sYear == a && cld[c].sMonth == b && cld[c].sDay == e) return cld[c].cYear + "\u5e74(" + cld[c].sYear + ")" + (cld[c].isLeap ? "\u95f0": "") + lunarCalc.nStr3[cld[c].lMonth] + lunarCalc.cDay(cld[c].lDay);
				if (d == 1 && cld[c].lYear == a && cld[c].lMonth == b && cld[c].lDay == e) {
					//if (0 == f) return cld[c].sYear + "\u5e74" + cld[c].sMonth + "\u6708" + cld[c].sDay + "\u65e5";
					//if (1 == f && cld[c].isLeap) return cld[c].sYear + "\u5e74" + cld[c].sMonth + "\u6708" + cld[c].sDay + "\u65e5"

					if (0 == f) return cld[c]
					if (1 == f && cld[c].isLeap) return cld[c]

				}
			}
		}
		return - 1
	}
};
function test() {
	alert("result:" + lunarCalc.drawTest(1987, 1, 1, 1, 0))
};
function tran() {
	alert("result:" + lunarCalc.transform(1987, 1, 1, 0, 1))
};