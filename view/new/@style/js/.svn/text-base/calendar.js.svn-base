var CalendarList = {
	g_Lunar: {
		y: 2004,
		m: 3,
		d: 20,
		f: 0
	},
	year: null,
	month: null,
	day: null,
	radio: null,
	age: null,
	animal: null,
	star: null,
	redo: false,
	redonum: 0,
	inited: false,
	stars: [{
		mb: 1,
		db: 20,
		me: 2,
		de: 18,
		name: "\u6c34\u74f6\u5ea7"
	},
	{
		mb: 2,
		db: 19,
		me: 3,
		de: 20,
		name: "\u53cc\u9c7c\u5ea7"
	},
	{
		mb: 3,
		db: 21,
		me: 4,
		de: 19,
		name: "\u767d\u7f8a\u5ea7"
	},
	{
		mb: 4,
		db: 20,
		me: 5,
		de: 20,
		name: "\u91d1\u725b\u5ea7"
	},
	{
		mb: 5,
		db: 21,
		me: 6,
		de: 21,
		name: "\u53cc\u5b50\u5ea7"
	},
	{
		mb: 6,
		db: 22,
		me: 7,
		de: 22,
		name: "\u5de8\u87f9\u5ea7"
	},
	{
		mb: 7,
		db: 23,
		me: 8,
		de: 22,
		name: "\u72ee\u5b50\u5ea7"
	},
	{
		mb: 8,
		db: 23,
		me: 9,
		de: 22,
		name: "\u5904\u5973\u5ea7"
	},
	{
		mb: 9,
		db: 23,
		me: 10,
		de: 22,
		name: "\u5929\u79e4\u5ea7"
	},
	{
		mb: 10,
		db: 23,
		me: 11,
		de: 12,
		name: "\u5929\u874e\u5ea7"
	},
	{
		mb: 11,
		db: 23,
		me: 12,
		de: 21,
		name: "\u5c04\u624b\u5ea7"
	},
	{
		mb: 12,
		db: 22,
		me: 1,
		de: 19,
		name: "\u6469\u7faf\u5ea7"
	}],
	lunar: function() {
		var b = new Date,
		a = this.year,
        k = _obj.nowData.getFullYear(),
		    l=b.getFullYear(),
            n = Math.min(k, l); 
		a.empty(); 
		for (var b = n, c = 0; c < 10; c++) {
			
			var e = document.createElement("option");
			e.appendChild(document.createTextNode(lunarCalc.cyclical(b - 4) + "\u5e74(" + b + ")"));
			e.setAttribute("value", b);
			a.append(e);
			b++; 
		}
		this.lunar_setting();
		try {
			this.year.val(this.g_Lunar.y)
		} catch(f) {
			CalendarList.redo = true
		}
		this.year.change();
		this.month.change()
	},
	lunar_setting: function() {
		this.year.bind("change", this.lunar_y);
		this.month.bind("change", this.lunar_m);
		this.day.bind("change", this.lunar_d)
	},
	lunar_y: function() {
		if (!CalendarList.inited) {
			try {
				CalendarList.year.val(CalendarList.g_Lunar.y)
			} catch(b) {}
			CalendarList.inited = true
		}
		var a = lunarCalc.leapMonth(this.value),
		c = CalendarList.month;
		c.empty();
		for (var e = 1; e <= 12; e++) {
			var f =
			document.createElement("option");
			f.appendChild(document.createTextNode(lunarCalc.nStr3[e]));
			f.setAttribute("value", e);
			c.append(f);
			e == a && (f = document.createElement("option"), f.appendChild(document.createTextNode("\u95f0" + lunarCalc.nStr3[e])), f.setAttribute("value", e + 100), c.append(f))
		}
		try {
		
			CalendarList.g_Lunar.y = this.value, 
			CalendarList.month.val(CalendarList.g_Lunar.m),
			CalendarList.changeAge(),
			CalendarList.changeAnimal()
		} catch(h) {
			CalendarList.redo = true
		}
	},
	lunar_m: function() {
		var b = lunarCalc.monthDays(CalendarList.year.val(),
		this.value),
		a = CalendarList.day;
		a.empty();
		for (var c = 1; c <= b; c++) {
			var e = document.createElement("option");
			e.appendChild(document.createTextNode(lunarCalc.cDay(c)));
			e.setAttribute("value", c);
			a.append(e)
		}
		try {
			CalendarList.g_Lunar.m = this.value, 
			CalendarList.day.val(CalendarList.g_Lunar.d),
			CalendarList.changeAge()
		} catch(f) {
			CalendarList.redo = true
		}
	},
	lunar_d: function() {
		CalendarList.g_Lunar.d = CalendarList.day.val();
		CalendarList.changeAge()
	},
	radioselect: function() {
		this.radio.change(function() {
			if (CalendarList.g_Lunar.f !=
			this.value) this.value == 1 ? (CalendarList.year.unbind("change", this.calendar_y), CalendarList.month.unbind("change", this.calendar_m), CalendarList.day.unbind("change", this.calendar_d)) : (CalendarList.year.unbind("change", this.lunar_y), CalendarList.month.unbind("change", this.lunar_m), CalendarList.day.unbind("change", this.lunar_d)),
			CalendarList.g_Lunar.f = this.value,
			CalendarList.update_g_lunar(this.value),
			this.value == 1 ? CalendarList.lunar() : CalendarList.calendar()
		})
	},
	update_g_lunar: function(b) {
		var a;
		b == 1 ? (a =
		lunarCalc.transform(CalendarList.year.val(), parseInt(CalendarList.month.val()), CalendarList.day.val(), 0, 0), CalendarList.g_Lunar.y = a.lYear, CalendarList.g_Lunar.m = a.lMonth, CalendarList.g_Lunar.d = a.lDay, a.isLeap && (CalendarList.g_Lunar.m += 100)) : (a = lunarCalc.transform(CalendarList.year.val(), CalendarList.month.val() % 100, CalendarList.day.val(), 1, Math.floor(CalendarList.month.val() / 100)), CalendarList.g_Lunar.y = a.sYear, CalendarList.g_Lunar.m = a.sMonth, CalendarList.g_Lunar.d = a.sDay);
		CalendarList.g_Lunar.f =
		b
	},
	calendar: function() {
	    var b = new Date,
    a = this.year,
    k = _obj.nowData.getFullYear(),
        l = b.getFullYear(),
        n = Math.min(k, l);
	    a.empty();
	    for (var b = n, c = 0; c < 10; c++) {
		
			var e = document.createElement("option");
			e.appendChild(document.createTextNode(b));
			//e.appendChild(document.createTextNode(b + "\u5e74"));
			e.setAttribute("value", b);
			a.append(e);
           	b++; 
		}
		this.calendar_setting();
		try {
			this.year.val(this.g_Lunar.y)
		} catch(f) {
			CalendarList.redo = true
		}
		a.change();
		this.month.change()
	},
	calendar_setting: function() {
		this.year.bind("change", this.calendar_y);
		this.month.bind("change", this.calendar_m);
		this.day.bind("change", this.calendar_d)
	},
	calendar_y: function() {
		obj = CalendarList.month;
		obj.empty();
		for (i = 1; i <= 12; i++) {
			var b = document.createElement("option");
			b.appendChild(document.createTextNode(i));
			//b.appendChild(document.createTextNode(i + "\u6708"));
			b.setAttribute("value", i);
			obj.append(b)
		}
		setTimeout(function () {
			obj.change();
		},50);
		try { 
			CalendarList.month.val(CalendarList.g_Lunar.m),
			CalendarList.g_Lunar.y = CalendarList.year.val(),
			CalendarList.changeAge(),
			CalendarList.changeAnimal()
		} catch(a) {
			CalendarList.redo = true
		}
	},
	calendar_m: function() {
		var b = lunarCalc.solarDays(CalendarList.year.val(), CalendarList.month.val() - 1);
		b == "undefined" &&
		(b = 30);
		obj = CalendarList.day;
		obj.empty();
		for (var a = 1; a <= b; a++) {
			var c = document.createElement("option");
			c.appendChild(document.createTextNode(a));
			//c.appendChild(document.createTextNode(a + "\u65e5"));
			c.setAttribute("value", a);
			obj.append(c)
		}
		try {
			CalendarList.day.val(CalendarList.g_Lunar.d),
			CalendarList.g_Lunar.m = CalendarList.month.val(),
			CalendarList.changeAge(),
			CalendarList.changeAnimal()
		} catch(e) {
			CalendarList.redo = true
		}
	},
	calendar_d: function() {
		CalendarList.g_Lunar.d = CalendarList.day.val();
		CalendarList.changeAge()
	},
	ageselect: function() {
		this.age.bind("change",
		function() {
			var b = this.value,
			a = new Date,
			c = a.getFullYear();
			0 == CalendarList.g_Lunar.f && (a.getMonth(), a.getDate());
			CalendarList.year.val(c - b);
			CalendarList.g_Lunar.y = c - b
		})
	},
	changeAge: function() {
		//暂用不上注释掉
		/*var b = new Date,
		a = b.getFullYear(),
		c = b.getMonth() + 1,
		b = b.getDate();
		CalendarList.g_Lunar.f == 0 ? (a -= CalendarList.g_Lunar.y, a != 0 && !(CalendarList.g_Lunar.m < c) && (c == CalendarList.g_Lunar.m ? CalendarList.g_Lunar.d > b && (a -= 1) : CalendarList.g_Lunar.m > c && (a -= 1))) : (dayinfo = lunarCalc.transform(a, c, b, 0, 0), y = dayinfo.lYear, m = dayinfo.lMonth,
		d = dayinfo.lDay, a = y - CalendarList.g_Lunar.y, a != 0 && !(CalendarList.g_Lunar.m < m) && (m == CalendarList.g_Lunar.m ? CalendarList.g_Lunar.d > d && (a -= 1) : CalendarList.g_Lunar.m > m && (a -= 1)));
		CalendarList.age.val(a);*/
		CalendarList.changeStar()
	},
	changeAnimal: function() {

		var _Animal=['鼠','牛','虎','兔','龙','蛇','马','羊','猴','鸡','狗','猪']	//生肖，自己增加的

		if (CalendarList.g_Lunar.f == 0) {
			var temp = lunarCalc.transform(parseInt(CalendarList.g_Lunar.y), parseInt(CalendarList.g_Lunar.m),parseInt(CalendarList.g_Lunar.d),0,0);
			//var b = (lunarCalc.drawTest(CalendarList.g_Lunar.y - 0, CalendarList.g_Lunar.m - 0, CalendarList.g_Lunar.d - 0, 0, 0).replace(/\D/g, "") - 4) % 12 + 1;
			//$("#getYear").html(_Animal[(temp.lYear-1888)%12])
			CalendarList.animal.html(_Animal[(temp.lYear-1888)%12])
		} else{
			//$("#getYear").html(_Animal[(CalendarList.g_Lunar.y - 1888) % 12])
			CalendarList.animal.html(_Animal[(CalendarList.g_Lunar.y - 1888) % 12]);
		} 
		CalendarList.changeStar();

	},
	changeStar: function() {
		var b,
		a,
		c = 0;
		CalendarList.g_Lunar.f == 1 ? (a = lunarCalc.transform(CalendarList.year.val(), CalendarList.month.val() % 100, CalendarList.day.val(), 1, Math.floor(CalendarList.month.val() / 100)), b = a.sMonth, a = a.sDay) : (b = CalendarList.g_Lunar.m, a = CalendarList.g_Lunar.d);
		for (c = 0; c < 12; c++) {
			var e = CalendarList.stars[c];
			if (e.mb == b && e.db <= a) {
				c++;
				break
			} else if (b > e.mb && e.me == b && e.de >= a) {
				c++;
				break
			}
		}
		//$("#birthday_m_d").html(e.name)
		CalendarList.star.html(e.name)
	},
	init: function(b, a, c, e, f, h, k, l, j, n, g,callback) {
	    this.year = b;  
		this.month = a;
		this.day = c;
		this.radio =e;
		this.age = f;
		this.animal = h;
		this.star = k;
		this.g_Lunar.y = l;
		
		this.g_Lunar.m = g == 2 ? j + 100: j;
		this.g_Lunar.d = parseInt(n, 10);
		this.g_Lunar.f = g == 0 ? 0: 1;
		0 == g ? this.calendar() : this.lunar();
		CalendarList.changeAnimal();
		//this.radioselect();
		//this.ageselect();
		callback&&callback();
	}
};