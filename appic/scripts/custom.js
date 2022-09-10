jQuery(document).ready(function($){
	//Tooltip
	$('.custom-tooltip').tooltip();

	//Mobile menu
	$('#navigation').find('li ul.sub-menu').each(function(){
		$(this).parent().prepend('<span class="sub-nav-toggle plus"></span>');
	});

	$('#navigation').mobileMenu({
		triggerMenu:'#navigation-toggle',
		subMenuTrigger: ".sub-nav-toggle",
		animationSpeed: 500 
	});

	//1-st selector 3D-hover for iPhone, iPad, iPod; 2-nd for general devices
	$('.ch-item, .ch-second-item').on("mouseenter mouseleave", function(e){
		e.preventDefault();
		$(this).toggleClass('hover');
	});

	// initialization of the fixed top menu
	var navMenuSrollHandler = {
		isFixed: false,
		init:function(){
			this.navWrap = $('.main-nav-wrap');
			this.replacer = $('<div data-role="nav-wrap-replacer" />')
				.css({display:'none'})
				.insertAfter(this.navWrap);

			this.delta = this.navWrap.parent().height() - this.navWrap.height();

			this.btn = $('<div/>',{
					'class':'top-scroll'
				})
				.append('<a href="#"/>')
				.hide()
				.appendTo('body')
				.click(function () {
					$('body,html').animate({scrollTop: 0}, 800);
					return false;
				});

			$(window).scroll(this._scrollingCallback)
				.trigger('scroll');
		},
		_scrollingCallback: function () {
			var self = navMenuSrollHandler;
			if ($(window).scrollTop() > self.delta) {
				if (!self.isFixed) {
					self.replacer.css({
						height:self.navWrap.height(),
						display:'block'
					});

					self.navWrap.addClass('fixed-pos');
					self.isFixed = true;
					if ('fixed' != self.navWrap.css('position')) {
						self.replacer.hide();
					}
					self.btn.fadeIn();
				}
			} else if (self.isFixed) {
				self.navWrap.removeClass('fixed-pos');
				self.isFixed = false;
				self.replacer.hide();
				self.btn.fadeOut();
			}
		}
	};
	navMenuSrollHandler.init();

	$('.accordion').on('show hide', function (n) {
		$(n.target)
			.siblings('.accordion-heading')
			.find('.accordion-toggle')
			.toggleClass('accordion-minus accordion-plus'); 
	});

	//to support responsive videos on any page
	$('.container.page-content').fitVids();
});

/**
 * Initilize sharrre buttions.
 * @param  object config
 * @return void
 */
var initSharrres = function(config)
{
	if (!config || typeof config != 'object' || !config.itemsSelector) {
		//throw 'Parameters error.';
		return;
	}

	var curlUrl = config.urlCurl ? config.urlCurl : '',
		elements = jQuery(config.itemsSelector);

	if (elements.length < 1) {
		return;
	}

	var initSharreBtn = function(){
		var el = jQuery(this),
			curId = el.data('btntype'),
			curConf = {
				urlCurl: curlUrl,
				enableHover: false,
				enableTracking: true,
				url: document.location.href,
				click: function(api, options){
					api.simulateClick();
					api.openPopup(curId);
				}
			};
		curConf.share = {};
		curConf.share[curId] = true;

		el.sharrre(curConf);
	};
	elements.each(initSharreBtn);

	// to prevent jumping to the top of page on click event
	setTimeout(function(){
		jQuery('a.share,a.count', config.itemsSelector).attr('href','javascript:void(0)');
	},1500);
};

// sets the cookie
var theme_setCookie = function(cname,cvalue,exdays)
{
	var d = new Date();
	d.setTime(d.getTime()+((exdays > 0 ? exdays : 14)*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}
