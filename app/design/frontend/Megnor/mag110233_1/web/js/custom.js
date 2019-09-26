define([
  "jquery",
], 
function($) {
  "use strict";
  jQuery(document).ready(function(){
  	if($('body').hasClass('catalog-category-view') == true|| $('body').hasClass('blog-index-index') == true){
  		var cat_name = $('body .page-title-wrapper>h1>span').text();
  		$(".level-top>span:contains('"+cat_name+"')").filter(function() {
		    if(($(this).text() === cat_name)=== true){$(this).addClass('active-page');}
		});
  	}
  	else if($('body').hasClass('cms-home') == true){
  		$(".level-top>span:contains('Home')").addClass('active-page');
  	}
  	$(document).ready(function() {	
		$("#spinner").fadeOut("slow");
	});
  	$(".account .page-title-wrapper" ).insertBefore( "#maincontent" );
  	$(".sidebar-category.block").insertAfter("#layered-filter-block");
  	$(".action.nav-toggle").prependTo(".header-right");
  	$('.custommenu .menu-title').click(function() {
		$('.custommenu #mainmenu').slideToggle('slow');
		$('.custommenu .menu-title').toggleClass('active');
	});
	$( document ).ready(function() {sidebarmenu();accountdash();})
	$( window ).resize(function() {sidebarmenu();accountdash();})
	function sidebarmenu(){
		if(($(window).width()>767) == true && ($('body').hasClass('cms-home')) == true ){
			$(".navigation.custommenu").prependTo(".slider-menu");
		}else{
			$(".navigation.custommenu").appendTo("#store-menu");
		}
	}
	function accountdash(){
		if((($(window).width()>767 === true) && (($(window).width()<980) === true))){
		$(".account .block-collapsible-nav .title").click(function(){
			$(".block-collapsible-nav .content").toggle();
		});}
	}
	$(".custom-header-menu").appendTo(".customlink-inner ul");
	$(window).scroll(function(){
    var scroll = $(window).scrollTop();
    if(scroll>=800){
    	$(".header.content ").addClass("fixed-header-style");
    	$(".page-header").addClass("fix");
    }
    else{
    	$(".header.content").removeClass("fixed-header-style");
    	$(".page-header").removeClass("fix");
    }
	});
	if($(".products-grid .product-item-info .product-item-details>[class^='swatch-opt-']")){
		$(".products-grid .product-item-info .product-item-details>[class^='swatch-opt-']").parent().addClass("has-swatch");
	}
	$(".block-search .minisearch input").focusout(function(){
		$('#search_mini_form').removeClass("active");
		$('.field.search label').removeClass("active");
	});
	$(".page.messages").appendTo(".pagemessage.container-width");
	/*<!-- Display the countdown timer-->*/
// Set the date we're counting down to
$(".specialdates").each(function(){
		var montharr=['Jan','Feb','Mar','Apr','May','June',"July","Aug",'Sept','Oct','Nov','Dec']
		var year=$(this).find(".specialyear").text();
		var month=$(this).find(".specialM").text();
		var day=$(this).find(".specialD").text();
		var thisis=this;
		var countDownDate = new Date(montharr[month-1]+","+day+","+year).getTime();
		var x = setInterval(function() {

		  // Get todays date and time
		  var now = new Date().getTime();

		  // Find the distance between now an the count down date
		  var distance = countDownDate - now;
		  //console.log(this);
		  // Time calculations for days, hours, minutes and seconds
		  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
		  //$(".special-count").html(days + "days " + hours + "hrs "+ minutes + "mins " + seconds + "secs ");
		  $(thisis).closest(".product-item-info").find(".special-count .no-days").html(days + "<span>days</span>");
		  $(thisis).closest(".product-item-info").find(".special-count .no-hrs").html(hours + "<span>hrs</span>");
		  $(thisis).closest(".product-item-info").find(".special-count .no-mins").html(minutes + "<span>mins</span>");
		  $(thisis).closest(".product-item-info").find(".special-count .no-secs").html(seconds + "<span>secs</span>");
		  // Display the result in the element with id="demo"
		  // If the count down is finished, write some text
		  if (distance < 0) {
		    clearInterval(x);
		    $(thisis).closest(".product-item-info").find(".special-count").html("This Deal has ended please visit again for new deal");
		  }
		}, 1000);
	});
$(".tm_special .product-item-info .product-item-photo:not(:has(div))").closest(".product-item-info").addClass("no-special-product");
$(".tm_special .product-item-info.no-special-product").find(".special-count").html("<span class='expired-special'>This Deal has ended please visit again for new deal!!...</span>");
$(".tm_special .product-item-info.no-special-product").find(".actions-primary").remove();
$(".product-item-info").find(".old-price").css( "float", "none" );
/*<!-- ******************************************************************************************************************* -->*/
  });
	require(['jquery', 'owlcarousel','fancybox', 'jstree', 'flexslider', 'bxslider',], function($) {
	jQuery(document).ready(function() {

	 	jQuery(".lightbox").fancybox({
			'frameWidth' : 890,
	    	'frameHeight' : 630,
			openEffect  : 'fade',
			closeEffect : 'fade',
			helpers: {
	        	title: null
    		}
		});
		jQuery(".headerlinks_inner").click(function(){
			jQuery(".header_customlink ul").slideToggle('slow');
			jQuery(".navigation.custommenu").parent().find('#mainmenu').removeAttr('style');
			jQuery(".tm_headerlinks").removeAttr('style');
			jQuery(".tm_headerlinks_inner").removeClass('active');
		});


		$("body").append("<a class='top_button' title='Back To Top' href=''></a>");
		$(window).scroll(function () {
			if ($(this).scrollTop() > 70) {
				$('.top_button').fadeIn();
				$('.top_button_bottom').fadeIn();
			} else {
				$('.top_button').fadeOut();
				$('.top_button_bottom').fadeOut();
			}
		});
		// scroll body to 0px on click
		$('.top_button,top_button_bottom').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		if ($(window).width() < 980)
			{
				$('#blog-carousel').bxSlider({
					minSlides: 2,
					mode: 'vertical'
				});
			}
			else{

				$('#blog-carousel').bxSlider({
					minSlides: 2,
					slideMargin: 0,
			    	moveSlides:1,
			    	auto: true,
			    	mode: 'vertical'
				});
			}
		/*jQuery("#blog-carousel").owlCarousel({
			nav: true,
			loop: true,
			items: 10,
			responsive: {
			0: {
			items: 1
			},
				480: {
			items: 1
			},
				768: {
			items: 2
			},
				1024: {
			items: 2
			}
			},
			dots:true
        });*/
        jQuery("#brand-carousel").owlCarousel({
			nav: true,
			loop: true,
			autoplay:true,
			items: 5,
			autoplaySpeed:1000,
			autoplayTimeout:1000,
			responsive: {
			0: {
			items: 1
			},
				480: {
			items: 3
			},
				768: {
			items: 4
			},
				1024: {
			items: 5
			}
			},
			navText: [
			  "<i class='icon-chevron-left icon-white'><</i>",
			  "<i class='icon-chevron-right icon-white'>></i>"
			 ]
		});
	        jQuery('.block.related .products-carousel .owl-carousel').owlCarousel({
			items: 4,
	        	nav: true,
				responsive: {
					0: {
					    items: 2
					},
							480: {
					    items: 3
					},
							768: {
					    items: 3
					},
							1024: {
					    items: 3
					}
				    },
			navText: [
			  "<i class='icon-chevron-left icon-white'><</i>",
			  "<i class='icon-chevron-right icon-white'>></i>"
			 ]
		});
	         jQuery('.block.upsell .products-carousel .owl-carousel').owlCarousel({
			items: 4,
	        	nav: true,
				responsive: {
					0: {
					    items: 2
					},
							480: {
					    items: 3
					},
							768: {
					    items: 3
					},
							1024: {
					    items: 3
					}
				    },
			navText: [
			  "<i class='icon-chevron-left icon-white'><</i>",
			  "<i class='icon-chevron-right icon-white'>></i>"
			 ]
		});
        if((jQuery('.products-carousel .owl-carousel').parents().find(".crosssell").hasClass("crosssell"))==true){
	        jQuery('.crosssell .products-carousel .owl-carousel').owlCarousel({
				items: 4,
	        	nav: true,
				responsive: {
					0: {
					    items: 2
					},
							480: {
					    items: 3
					},
							768: {
					    items: 3
					},
							1024: {
					    items: 3
					}
				    },
				navText: [
				  "<i class='icon-chevron-left icon-white'><</i>",
				  "<i class='icon-chevron-right icon-white'>></i>"
				 ]
	        });
	    }else{
	        jQuery('.products-carousel .owl-carousel').owlCarousel({
				items: 6,
	        	nav: true,
				responsive: {
					0: {
					    items: 2
					},
							480: {
					    items: 3
					},
							768: {
					    items: 4
					},
							1024: {
					    items: 5
					}
				    },
				navText: [
				  "<i class='icon-chevron-left icon-white'><</i>",
				  "<i class='icon-chevron-right icon-white'>></i>"
				 ]
	        });
	    }
        jQuery('#right-banner-inner').flexslider({
		    animation: "fade",
		    pauseOnHover:true,
		    slideshowSpeed:2000,
		    controlNav:true
		});
		jQuery('.flexslider').flexslider({
		    animation: "slide",
		    controlNav: false,
		    pauseOnHover:true
		});

		
		jQuery("#category-treeview").treeview({
			animated: "slow",
			collapsed: true,
			unique: true
		});
		
		function productListAutoSet(){
			jQuery('.widget-product-carousel').owlCarousel({
				items: 5,
				nav: true,		
				responsive: {
				0: {
				    items: 2
				},
						480: {
				    items: 2
				},
						641: {
				    items: 3
				},
						768: {
				    items: 3
				},
						1024: {
				    items: 4
				},
						1201: {
				    items: 5
				},
						1281: {
				    items: 6
				}
				},
				navText: [
				"<i class='icon-chevron-left icon-white'><</i>",
				"<i class='icon-chevron-right icon-white'>></i>"
				]
			});
			/*----------------------------------------------------------------------------------------------*/
			checkClasses();
		    jQuery('.widget-product-carousel').on('translated.owl.carousel', function(event) {
		        checkClasses();
		    });

		    function checkClasses(){
		    	$(".product_tabs .tab_product").each(function(){
		    		var total = $(this).find('.owl-stage .owl-item.active').length;
		        	$(this).find('.owl-stage .owl-item').removeClass('firstActiveItem lastActiveItem');
		        $(this).find('.owl-stage .owl-item.active').each(function(index){
		            if (index === 0) {
		                $(this).addClass('firstActiveItem');
		            }
		            if (index === total - 1 && total>1) {
		                $(this).addClass('lastActiveItem');
		            }
		        });
		    	}); 
		    }
		    /*------------------------------------------------------------------------------------------------*/
			jQuery('.widget-special-product-carousel').owlCarousel({
				items: 2,
				nav: true,		
				responsive: {
				0: {
				    items: 1
				},
						480: {
				    items: 1
				},
						641: {
				    items: 1
				},
						768: {
				    items: 1
				},
						1024: {
				    items: 1
				}
				},
				navText: [
				"<i class='icon-chevron-left icon-white'><</i>",
				"<i class='icon-chevron-right icon-white'>></i>"
				]
			});
			jQuery('#category-carousel').owlCarousel({
				items: 5,
				nav: true,		
				responsive: {
				0: {
				    items: 1
				},
						480: {
				    items: 3
				},
						641: {
				    items: 3
				},
						768: {
				    items: 3
				},
					    980: {
					items: 4
				},
						1024: {
				    items: 4
				},
						1201: {
				    items: 5
				}
				},
				navText: [
				"<i class='icon-chevron-left icon-white'><</i>",
				"<i class='icon-chevron-right icon-white'>></i>"
				]
			});
			jQuery('#category-carousel-prd').owlCarousel({
				items: 5,
				nav: true,		
				responsive: {
				0: {
				    items: 1
				},
						480: {
				    items: 3
				},
						641: {
				    items: 3
				},
						768: {
				    items: 3
				},
					    980: {
					items: 4
				},
						1024: {
				    items: 4
				},
						1201: {
				    items: 5
				}
				},
				navText: [
				"<i class='icon-chevron-left icon-white'><</i>",
				"<i class='icon-chevron-right icon-white'>></i>"
				]
			});
			jQuery('#testimonial-blog').owlCarousel({
				items: 1,
				nav: true,		
				responsive: {
				0: {
				    items: 1
				},
						480: {
				    items: 1
				},
						641: {
				    items: 1
				},
						768: {
				    items: 1
				},
						1024: {
				    items: 1
				}
				},
				navText: [
				"<i class='icon-chevron-left icon-white'><</i>",
				"<i class='icon-chevron-right icon-white'>></i>"
				]
			});
		}
		jQuery(document).ready(function(){productListAutoSet();});
		jQuery(window).resize(function() {productListAutoSet();});
		jQuery(".tab_product:not(:first)").hide();
		jQuery(".tab_product:first").show();
		//when we click one of the tabs
		jQuery(".tabbernav_product  li  a").click(function(){
			//get the ID of the element we need to show
			var stringref = jQuery(this).attr("href").split('#')[1];
			//hide the tabs that doesn't match the ID
			jQuery('.tab_product:not(#'+stringref+')').hide();
			 //fix
			if (jQuery.browser.msie && jQuery.browser.version.substr(0,3) == "6.0") {
			 	jQuery('.tab_product#' + stringref).show();
			}
			else{
				//display our tab fading it in
				jQuery('.tab_product#' + stringref).fadeIn();
			}
			jQuery(".tabbernav_product a").removeClass("selected");
			jQuery(this).addClass("selected");
			
			var $owl = jQuery('#'+stringref+' .widget-product-carousel');
			$owl.trigger('destroy.owl.carousel');
			$owl.html($owl.find('.owl-stage-outer').html()).removeClass('owl-loaded');	
			productListAutoSet();
			return false;
		});

    }); // Require Ends here

	function mobileHeaderLink(){  
		jQuery(".tm_headerlinkmenu" ).addClass('toggle');	 
		jQuery(".tm_headerlinkmenu .headertoggle_img").click(function(){
			jQuery(this).parent().toggleClass('active').parent().find('.tm_headerlinks').slideToggle(0);
			jQuery(".header_customlink").parent().find('ul').removeAttr('style');
			jQuery(".navigation.custommenu").parent().find('#mainmenu').removeAttr('style');
		});
	}
	jQuery(document).ready(function(){mobileHeaderLink();});
	function footerToggleMenu(){
		if (jQuery(window).width() < 980)
		{
			jQuery(".page-footer .footer-area .mobile_togglemenu").remove();
			jQuery(".page-footer .footer-area h6").append( "<a class='mobile_togglemenu'>&nbsp;</a>" );
			jQuery(".page-footer .footer-area h6").addClass('toggle');
			jQuery(".page-footer .footer-area .mobile_togglemenu").click(function(){
				jQuery(this).parent().toggleClass('active').parent().find('ul').toggle('slow');
				jQuery(this).parent().parent().find('.payment_block').toggle('slow');
			});
		}else{
			jQuery(".page-footer .footer-area h6").parent().find('ul').removeAttr('style');
			jQuery(".page-footer .footer-area  h6").removeClass('active');
			jQuery(".page-footer .footer-area  h6").removeClass('toggle');
			jQuery(".page-footer .mobile_togglemenu").remove();
		}	
	}
	jQuery(document).ready(function(){footerToggleMenu();});
	jQuery(window).resize(function(){footerToggleMenu();}); 
		// Add random class in Manu //

		function addClassMenu(){
		
			let wi = 1;
    		var menu = 'menu-';
    		jQuery('#store-menu #mainmenu.menu.mainmenu ul').addClass('parentMenu');
    		jQuery('#store-menu #mainmenu.menu.mainmenu ul.parentMenu li ul').removeClass('parentMenu');
			jQuery('#store-menu #mainmenu.menu.mainmenu ul.parentMenu > li').each(function () {
	        	$(this).addClass(menu + wi);
	        	wi = wi + 1;
    		});
		}

		jQuery(document).load(function(){addClassMenu();});
		jQuery(document).ready(function(){addClassMenu();});

	function sidebarToggle(){
		if (jQuery(window).width() < 980){
			jQuery(".sidebar .block .mobile_togglemenu").remove();
			jQuery(".sidebar .block .block-title").append( "<a class='mobile_togglemenu'>&nbsp;</a>" );
			jQuery(".sidebar .block .block-title").addClass('toggle');
			jQuery(".sidebar .block .mobile_togglemenu").click(function(){
				jQuery(this).parent().toggleClass('active').parent().find('.block-content').slideToggle('slow');
			});
		}else{
			jQuery(".sidebar .block .block-title").parent().find('.block-content').removeAttr('style');
			jQuery(".sidebar .block .block-title").removeClass('active');
			jQuery(".sidebar .block .block-title").removeClass('toggle');
			jQuery(".sidebar .block .mobile_togglemenu").remove();
		}	
	}
	jQuery(window).resize(function(){sidebarToggle();});
	jQuery(document).ready(function(){sidebarToggle();});
	jQuery(document).load(function(){sidebarToggle();});
	jQuery(function($){
		if($(window).width()>1550){
			var max_elem = 10;
		}else if($(window).width()>1350){
			var max_elem = 8;
		}else if($(window).width()>1200){
			var max_elem = 8;
		}else if($(window).width()>979){
			var max_elem = 8;
		}else if($(window).width()>767){
			var max_elem = 8;
		}
		//else {var max_elem = 10;}
		var items = $('.navigation.custommenu .mainmenu > ul > li');
		var surplus = items.slice(max_elem, (items.length));
		surplus.wrapAll('<li class="menu-dropdown-icon level0 hiden_menu"><ul class="dropdown-inner-list">');
		$('.hiden_menu').prepend('<div class="more-wrap"><span class="more-view">More Categories</span</div>');
		});

		$(document).ready(function() { 
		    setTimeout(function() { 
		       $('#loader').fadeOut();
		       $('#loader2').fadeOut(); 
			 }, 200);		  	
		});

	function top_banner(){
	 	if(jQuery('body').hasClass('cms-home')){
			jQuery('.header-top-banner').show();
		}
		jQuery(".close-btn").on("click", function() {
			jQuery(this).fadeOut(100);
			jQuery('.header-top-banner').slideUp(1000);
		});
	}
	jQuery(document).ready(function(){top_banner();});
});
  return; //return is optional I kept it to prevent unnecessery error occurance in future.
});//Define Ends here and So does Custom.js.


