;(function($){
	var defaults ={
		maxdis:320,
		mindis:170,
		wraperClass:'timeline-wrap',
		fixerClass:'timeline-fixer',
		vScollerClass:'vertical-scoller',
		allowVerticalScroll:true
	};

	$.fn.timeLineG=function(options){
		new TimeLine(this.first(),options);
		return this.first();
	}
	
	var TimeLine = function($element, options){
		this.config = $.extend({}, defaults, options);
		this.container = $element;
		this.offset=0;
		this.init();
	};

	TimeLine.prototype.init = function()
	{
		var self=this,
			cfg = self.config;

		var wrapper = self.container.wrap('<div class="'+cfg.fixerClass+'">');
		wrapper.wrap('<div style="overflow:hidden" class="'+cfg.wraperClass+'"/>');

		var isTouch = 'ontouchstart' in window || 'onmsgesturechange' in window;
		if (isTouch && cfg.vScollerClass && !cfg.allowVerticalScroll) {
			var scroller = $('<div />')
				.addClass(cfg.vScollerClass)
				.insertAfter(self.container.parents('.' + cfg.wraperClass));
		}

		self.container.find('.year, .event').each(function(i,e){
			self.increaseOffset();
			var $elem = $(e);
			$elem.css('left',self.offset);
			
			var $circle=$elem.find('.sircle');
			$circle.data('top',$circle.css('top'))
				.css({'top':0})
				.removeClass('open-e');

			var $line=$elem.find('.line');
			$line.data('top',$line.css('top'))
				.css({'height':0,'top':0});

			$elem.find('.block-e').css({'display':'none'});
		});

		self.increaseOffset();
		self.container.css('width',self.offset);

		self.container
			.draggable({ axis: "x", cursor: "move" })
			.bind('dragstop', function(e, ui) {
				var r = ui.position.left + $(this).width();
				if(r < $(window).width())
					self.container.animate({ 'left': ($(window).width()-$(this).width())+'px' });
				if(ui.position.left > 0)
					self.container.animate({ 'left': '0px' });
			});

		if (isTouch && cfg.allowVerticalScroll) {
			self.container.bind('drag', function(e, ui){
				if (ui.position.top != 0) {
					$('body,html').scrollTop($(window).scrollTop() - ui.position.top);
				}
			})
		}

		$(".event .sircle").on('click',function(){
			var $this = $(this);
			if ($this.hasClass('open-e')) {
				$this.delay(200).animate({'top':0}).removeClass('open-e');
				$this.siblings('.line').delay(200).animate({'height':0,'top':0})
				$this.siblings('.block-e').fadeOut(200)
			} else {
				$this.animate({'top':$this.data('top')}).addClass('open-e');
				$this.siblings('.line').animate({'height':100,'top':$this.siblings('.line').data('top')})
				$this.siblings('.block-e').delay(300).fadeIn()
			}
		});

		var mass = self.container.find(".event .sircle");
		for (var i=0, l=mass.length;i<l;){
			mass.eq(i).trigger('click');
			if (!self.config.openAll) {
				i += Math.round(1+Math.random()*2);
			} else {
				i++;
			}
		}
	};

	TimeLine.prototype.increaseOffset = function(){
		this.offset+=this.config.mindis + Math.random()*(this.config.maxdis-this.config.mindis); 
	};
}(jQuery));