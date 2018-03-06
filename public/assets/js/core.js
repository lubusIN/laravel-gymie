(function($) {
	"use strict";
	/* 
	==================================================
	Panel
	================================================== */
	//Panel Collapse
	$('.panel-collapse').click(function(){
		var $panel = $(this).parent().parent().next('.panel-body');
		if($panel.is(':visible')) 
		{
		  $(this).children('i').removeClass('ion-arrow-up-b');
		  $(this).children('i').addClass('ion-arrow-down-b');
		}
		else 
		{
		  $(this).children('i').removeClass('ion-arrow-down-b');
		  $(this).children('i').addClass('ion-arrow-up-b');
		}            
		$panel.slideToggle(400);
		return false;
	}); 
		
	//Panel Refresh
	$('.panel-refresh').click(function(){
		var $panel = $(this).parents(".panel").first();
		$panel.addClass('panel-refresh');
		$panel.append( $( '<div class="loader"><i class="fa fa-circle-o-notch fa-spin"></i></div>' ) );
		setTimeout(function () {
			$panel.removeClass('panel-refresh');
			$panel.find('.loader').remove();
		}, 1500);
		return false;
	}); 
		
	//Panel Close
	$(".panel-close").click(function() {
		var parent = $(this).parents(".panel").first();
		var effect  = $(this).data("effect");
		if(effect) {
			parent.addClass('animated ' + $(this).data("effect"));
		} else {
			parent.addClass('animated fadeOut');
		}
		setTimeout(function () {
			parent.fadeOut("fast"); 
		}, 500);
		return false;
	});
	
	//Panel Scroll
	$(".panel-scroll").slimscroll({
		alwaysVisible: false,
		size: "5px",
		height: "320px"
	}).css("width", "100%");

	//Panel Scroll 2 for sms log tab
	$(".panel-scroll-2").slimscroll({
		alwaysVisible: false,
		size: "5px",
		height: "196px"
	}).css("width", "100%");
		
	/* 
	==================================================
	Chat Widget
	================================================== */
	//Chat Open
	$('.leftside .widget .list > a').click( function() {
		$(".list").addClass("no-display");
		$(".chat").addClass("display-block");
		
		$(".fixed-leftside .sidebar").slimScroll({ scrollTo: '500px' });
		return false;
	});
	
	//Chat Demo
	$('.chat .form-control').change(function () {
		var chatval = this.value;
		$(".chat ul").append('<li class="right animated fadeIn"><div class="clearfix"><div class="message">' + chatval + '</div></div><span>now</span></li>');
		$(this).val('');
		var scrollTo_val = $('.fixed-leftside .sidebar').prop('scrollHeight') + 'px';
		$(".fixed-leftside .sidebar").slimScroll({ scrollTo: scrollTo_val });
	});
	
	//Chat Close
	$('.leftside .widget .close-chat').click( function() {
		$(".chat").removeClass("display-block");
		$(".list").removeClass("no-display");
		return false;
	});
	
	/* 
	==================================================
	Helpers
	================================================== */
    //Hoverable dropdown
	$('.navbar .dropdown-hover').hover(function(){ 
		$('.dropdown-toggle', this).trigger('click'); 
	});	
		
    //Enable sidebar toggle
	$('.sidebar-toggle').click( function() {
		$("body").toggleClass("sidebar-sm");
		$(".sidebar-sm .sidebar").slimscroll({
			color: "rgba(255,255,255,0.5)",
			size: "3px",
			touchScrollStep: 80,
			height: ($(window).height() - $("header").height() - $(".leftside .footer").innerHeight()) + "px",
		});
		return false;
    });
	
	//Sidebar Scroll
	$(".fixed-leftside .sidebar").slimscroll({
		color: "rgba(255,255,255,0.5)",
		size: "5px",
		touchScrollStep: 80,
		height: ($(window).height() - $("header").height() - $(".leftside .footer").innerHeight()) + "px",
	});
	
	//Todo
	$('.todo .form-control').change(function () {
		var chatval = this.value;
		$("ul.todo").prepend('<li><div class="checkbox checkbox-theme"><input type="checkbox" id="checkbox" value="1"><label for="checkbox">' + chatval + '</label></div></li>');
		$(this).val('');
	});
	
	//Dropdown Animated
	$('.dropdown').on('show.bs.dropdown', function () {
		$(this).find('.dropdown-menu').fadeIn(150);
	});
	$('.dropdown').on('hide.bs.dropdown', function () {
		$(this).find('.dropdown-menu').fadeOut(150);
	});	
	$('.dropdown.dropdown-tasks').on('show.bs.dropdown', function () {
		setTimeout(function(){
			$('.progress-animated .progress-bar').each(function() {
				var me = $(this);
				var perc = me.attr("aria-valuenow");
				var current_perc = 0;
				var progress = setInterval(function() {
					if (current_perc>=perc) {
						clearInterval(progress);
					} else {
						current_perc +=1;
						me.css('width', (current_perc)+'%');
					}
				}, 0);
			});
		}, 0);
	});
	
	//Dropdown-menu Scroll
	$(".navbar .dropdown-menu ul.scroll").slimscroll({
        alwaysVisible: false,
        size: "3px",
        height: "350px", 
		touchScrollStep: 80,
    }).css("width", "100%");
	
    //Tooltip
    $("[data-toggle='tooltip']").tooltip();
	
	/* 
	==================================================
	Sidebar Navigation
	================================================== */
    $.fn.sub = function() {
        return this.each(function() {  
			var btn = $(this).children("a").first(); 
			var menu = $(this).children("ul").first();  
			var active = $(this).hasClass('open');
			if (active) {
				menu.show();
				btn.children(".ion-chevron-right").first().removeClass("ion-chevron-right").addClass("ion-chevron-down");
			}
			btn.click(function(e) {
				e.preventDefault();
				if (active) {
					menu.slideUp(200);
					active = false;
					btn.children(".ion-chevron-down").first().removeClass("ion-chevron-down").addClass("ion-chevron-right");
					btn.parent("li").removeClass("open");
				} else {
					menu.slideDown(200);
					active = true;
					btn.children(".ion-chevron-right").first().removeClass("ion-chevron-right").addClass("ion-chevron-down");
					btn.parent("li").addClass("open");
				}
			});
    });
	};
	//Sidebar Nav
	 $(".leftside .nav-dropdown").sub();
})(jQuery);
