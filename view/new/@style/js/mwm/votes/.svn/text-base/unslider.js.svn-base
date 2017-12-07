/**
* Unslider by @idiot and @damirfoy
* Contributors:
* - @ShamoX
*
*/

(function($, f) {
var Unslider = function() {
// Object clone
var _ = this;

// Set some options
_.o = {
speed: 500, // animation speed, false for no transition (integer or boolean)
delay: 3000, // delay between slides, false for no autoplay (integer or boolean)
init: 0, // init delay, false for no delay (integer or boolean)
pause: !f, // pause on hover (boolean)
loop: !f, // infinitely looping (boolean)
keys: f, // keyboard shortcuts (boolean)
dots: f, // display ????o? pagination (boolean)
arrows: f, // display prev/next arrows (boolean)
prev: '←', // text or html inside prev button (string)
next: '→', // same as for prev option
fluid: f, // is it a percentage width? (boolean)
starting: f, // invoke before animation (function with argument)
complete: f, // invoke after animation (function with argument)
items: '>ul', // slides container selector
item: '>li', // slidable items selector
easing: 'swing',// easing function to use for animation
autoplay: true // enable autoplay on initialisation
};

_.init = function(el, o) {
// Check whether we're passing any options in to Unslider
_.o = $.extend(_.o, o);

_.el = el;
_.ul = el.find(_.o.items);
_.max = [el.outerWidth() | 0, el.outerHeight() | 0];
_.li = _.ul.find(_.o.item).each(function(index) {
var me = $(this),
width = me.outerWidth(),
height = me.outerHeight();

// Set the max values
if (width > _.max[0]) _.max[0] = width;
if (height > _.max[1]) _.max[1] = height;
});


// Cached vars
var o = _.o,
ul = _.ul,
li = _.li,
len = li.length;

// Current indeed
_.i = 0;

// Set the main element
el.css({width: _.max[0], height: li.first().outerHeight(), overflow: 'hidden'});

// Set the relative widths
ul.css({position: 'relative', left: 0, width: (len * 100) + '%'});
li.css({'float': 'left', width: (_.max[0]) + 'px'});

// Autoslide
o.autoplay && setTimeout(function() {
if (o.delay | 0) {
_.play();

if (o.pause) {
el.on('mouseover mouseout', function(e) {
_.stop();
e.type == 'mouseout' && _.play();
});
};
};
}, o.init | 0);

// Keypresses
if (o.keys) {
$(document).keydown(function(e) {
var key = e.which;

if (key == 37)
_.prev(); // Left
else if (key == 39)
_.next(); // Right
else if (key == 27)
_.stop(); // Esc
});
};

// Dot pagination
o.dots && nav('dot');

// Arrows support
o.arrows && nav('arrow');

// Patch for fluid-width sliders. Screw those guys.
if (o.fluid) {
$(window).resize(function() {
_.r && clearTimeout(_.r);

_.r = setTimeout(function() {
var styl = {height: li.eq(_.i).outerHeight()},
width = el.outerWidth();

ul.css(styl);
styl['width'] = Math.min(Math.round((width / el.parent().width()) * 100), 100) + '%';
el.css(styl);
}, 50);
}).resize();
};

// Swipe support
if ($.event.special['swipe'] || $.Event('swipe')) {
el.on('swipeleft swiperight swipeLeft swipeRight', function(e) {
e.type.toLowerCase() == 'swipeleft' ? _.next() : _.prev();
});
};

return _;
};

// Move Unslider to a slide index
_.to = function(index, callback) {
if (_.t) {
_.stop();
_.play();
}
var o = _.o,
el = _.el,
ul = _.ul,
li = _.li,
current = _.i,
target = li.eq(index);

$.isFunction(o.starting) && !callback && o.starting(el, li.eq(current));

// To slide or not to slide
if ((!target.length || index < 0) && o.loop == f) return;

// Check if it's out of bounds
if (!target.length) index = 0;
if (index < 0) index = li.length - 1;
target = li.eq(index);

var speed = callback ? 5 : o.speed | 0,
easing = o.easing,
obj = {height: target.outerHeight()};

if (!ul.queue('fx').length) {
// Handle those pesky dots
el.find('.dot').eq(index).addClass('active').siblings().removeClass('active');

el.animate(obj, speed, easing) && ul.animate($.extend({left: '-' + index + '00%'}, obj), speed, easing, function(data) {
_.i = index;

$.isFunction(o.complete) && !callback && o.complete(el, target);
});
};
};

// Autoplay functionality
_.play = function() {
_.t = setInterval(function() {
_.to(_.i + 1);
}, _.o.delay | 0);
};

// Stop autoplay
_.stop = function() {
_.t = clearInterval(_.t);
return _;
};

// Move to previous/next slide
_.next = function() {
return _.stop().to(_.i + 1);
};

_.prev = function() {
return _.stop().to(_.i - 1);
};

// Create dots and arrows
function nav(name, html) {
if (name == 'dot') {
html = '<ol class="dots">';
$.each(_.li, function(index) {
html += '<li class="' + (index == _.i ? name + ' active' : name) + '">' + ++index + '</li>';
});
html += '</ol>';
} else {
html = '<div class="';
html = html + name + 's">' + html + name + ' prev">' + _.o.prev + '</div>' + html + name + ' next">' + _.o.next + '</div></div>';
};

_.el.addClass('has-' + name + 's').append(html).find('.' + name).click(function() {
var me = $(this);
me.hasClass('dot') ? _.stop().to(me.index()) : me.hasClass('prev') ? _.prev() : _.next();
});
};
};

// Create a jQuery plugin
$.fn.unslider = function(o) {
var len = this.length;

// Enable multiple-slider support
return this.each(function(index) {
// Cache a copy of $(this), so it
var me = $(this),
key = 'unslider' + (len > 1 ? '-' + ++index : ''),
instance = (new Unslider).init(me, o);

// Invoke an Unslider instance
me.data(key, instance).data('key', key);
});
};

Unslider.version = "1.0.0";
})(jQuery, false);
/**
 *   Unslider by @idiot
 */
 
(function($, f) {
	//  If there's no jQuery, Unslider can't work, so kill the operation.
	if(!$) return f;
	
	var Unslider = function() {
		//  Set up our elements
		this.el = f;
		this.items = f;
		
		//  Dimensions
		this.sizes = [];
		this.max = [0,0];
		
		//  Current inded
		this.current = 0;
		
		//  Start/stop timer
		this.interval = f;
				
		//  Set some options
		this.opts = {
			speed: 500,
			delay: 3000, // f for no autoplay
			complete: f, // when a slide's finished
			keys: !f, // keyboard shortcuts - disable if it breaks things
			dots: f, // display 鈥⑩€⑩€⑩€鈥?pagination
			fluid: f // is it a percentage width?,
		};
		
		//  Create a deep clone for methods where context changes
		var _ = this;

		this.init = function(el, opts) {
			this.el = el;
			this.ul = el.children('ul');
			this.max = [el.outerWidth(), el.outerHeight()];			
			this.items = this.ul.children('li').each(this.calculate);
			
			//  Check whether we're passing any options in to Unslider
			this.opts = $.extend(this.opts, opts);
			
			//  Set up the Unslider
			this.setup();
			
			return this;
		};
		
		//  Get the width for an element
		//  Pass a jQuery element as the context with .call(), and the index as a parameter: Unslider.calculate.call($('li:first'), 0)
		this.calculate = function(index) {
			var me = $(this),
				width = me.outerWidth(), height = me.outerHeight();
			
			//  Add it to the sizes list
			_.sizes[index] = [width, height];
			
			//  Set the max values
			if(width > _.max[0]) _.max[0] = width;
			if(height > _.max[1]) _.max[1] = height;
		};
		
		//  Work out what methods need calling
		this.setup = function() {
			//  Set the main element
			this.el.css({
				overflow: 'hidden',
				width: _.max[0],
				height: this.items.first().outerHeight()
			});
			
			//  Set the relative widths
			this.ul.css({width: (this.items.length * 100) + '%', position: 'relative'});
			this.items.css('width', (100 / this.items.length) + '%');
			
			if(this.opts.delay !== f) {
				this.start();
				this.el.hover(this.stop, this.start);
			}
			
			//  Custom keyboard support
			this.opts.keys && $(document).keydown(this.keys);
			
			//  Dot pagination
			this.opts.dots && this.dots();
			
			//  Little patch for fluid-width sliders. Screw those guys.
			if(this.opts.fluid) {
				var resize = function() {
					_.el.css('width', Math.min(Math.round((_.el.outerWidth() / _.el.parent().outerWidth()) * 100), 100) + '%');
				};
				
				resize();
				$(window).resize(resize);
			}
			
			if(this.opts.arrows) {
				this.el.parent().append('<p class="arrows"><span class="prev">鈫?/span><span class="next">鈫?/span></p>')
					.find('.arrows span').click(function() {
						$.isFunction(_[this.className]) && _[this.className]();
					});
			};
			
			//  Swipe support
			if($.event.swipe) {
				this.el.on('swipeleft', _.prev).on('swiperight', _.next);
			}
		};
		
		//  Move Unslider to a slide index
		this.move = function(index, cb) {
			//  If it's out of bounds, go to the first slide
			if(!this.items.eq(index).length) index = 0;
			if(index < 0) index = (this.items.length - 1);
			
			var target = this.items.eq(index);
			var obj = {height: target.outerHeight()};
			var speed = cb ? 5 : this.opts.speed;
			
			if(!this.ul.is(':animated')) {			
				//  Handle those pesky dots
				_.el.find('.dot:eq(' + index + ')').addClass('active').siblings().removeClass('active');

				this.el.animate(obj, speed) && this.ul.animate($.extend({left: '-' + index + '00%'}, obj), speed, function(data) {
					_.current = index;
					$.isFunction(_.opts.complete) && !cb && _.opts.complete(_.el);
				});
			}
		};
		
		//  Autoplay functionality
		this.start = function() {
			_.interval = setInterval(function() {
				_.move(_.current + 1);
			}, _.opts.delay);
		};
		
		//  Stop autoplay
		this.stop = function() {
			_.interval = clearInterval(_.interval);
			return _;
		};
		
		//  Keypresses
		this.keys = function(e) {
			var key = e.which;
			var map = {
				//  Prev/next
				37: _.prev,
				39: _.next,
				
				//  Esc
				27: _.stop
			};
			
			if($.isFunction(map[key])) {
				map[key]();
			}
		};
		
		//  Arrow navigation
		this.next = function() { return _.stop().move(_.current + 1) };
		this.prev = function() { return _.stop().move(_.current - 1) };
		
		this.dots = function() {
			//  Create the HTML
			var html = '<ol class="dots">';
				$.each(this.items, function(index) { html += '<li class="dot' + (index < 1 ? ' active' : '') + '">' + (index + 1) + '</li>'; });
				html += '</ol>';
			
			//  Add it to the Unslider
			this.el.addClass('has-dots').append(html).find('.dot').click(function() {
				_.move($(this).index());
			});
		};
	};
	
	//  Create a jQuery plugin
	$.fn.unslider = function(o) {
		var len = this.length;
		
		//  Enable multiple-slider support
		return this.each(function(index) {
			//  Cache a copy of $(this), so it 
			var me = $(this);
			var instance = (new Unslider).init(me, o);
			
			//  Invoke an Unslider instance
			me.data('unslider' + (len > 1 ? '-' + (index + 1) : ''), instance);
		});
	};
})(window.jQuery, false);