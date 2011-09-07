jQuery.noConflict();
(function($){

	// Global variables
	var base = $("base").attr("href");
	var bsFlag = false;
	var menuHeight = 0;
	var ShadowboxFlag = false;
	var menuClickedFlag = false;
	/** 
	 * Run on pageload (document ready)
	 */
	$(document).ready(function() {	

		var pathIsHashed = goToBase();				
		
		if(pathIsHashed === true){
			$.cookie("ajax", "true");
			getMenuHeight();
			setupSuperfishMenu();			
			initLinkHasher();	
			$.address.change(function(event) {											
				loadPage(event.value);														
			});  							
		}
	});
	
	function initShadowbox(){		
		Shadowbox.init({ skipSetup: true }); 

		var isBlog = $("#main .post img").size();
		if(isBlog>0){
			Shadowbox.setup('.post a:has(img)'); // target images in posts		
		}else{
			Shadowbox.setup(); // target default (rel=shadowbox)
		}
	}
	
	function initColumns(){
		$('body.page-template-columns #content .entry-content').columnize({columns: 2});
	}
	
	/**
	 * Set background images
	 * bgImage: string
	 */
	function setBgImage(bgImage){		
		if(bsFlag == false){
			bs = $.backstretch(bgImage, {speed: 800}); //full size background image		
			bsFlag = true;
		}else{
			bs.set(bgImage);			
		}
	}		
	
	
	/*
	 * functions to be run before the page loads
	 */
	function beforePageLoad(page){		
	
		//remove leading slash, else return false
		var firstCharacterOfPage = page.substring(0, 1);	
		if(firstCharacterOfPage == "/"){
			page = page.substring(1);
		}else{
			return false;
		}						
		
		//remove class from previously selected menu item
		$("#primary-menu li").removeClass("current-menu-item");

		//add class to currently selected menu item
		var addr = base + page;
		$("#primary-menu li a[href='" + addr  + "']").parent("li").addClass("current-menu-item");							
		
		//update menu
		showCurrentSubmenu();
		setMenuHeight();
		
		//prepare for next page - remove current content and add loader
		$('#content').fadeOut("fast").queue(function(){
			$(this).html('<div id="loader">Loading... </div>').fadeIn("fast").dequeue();
		});						
	}
	
	/*
	 * When the hash changes in address bar
	 * And upon entry to website (frontpage, bookmark to subpage etc)
	 */
	function loadPage(page) {		
	
		beforePageLoad(page);
											
		//remove everything after hash (#) if it exists
		var positionOfHash = page.indexOf("#");
		if(positionOfHash > -1){
			page = page.substring(0, positionOfHash);
		}
		//add argument seperator (? or &)
		var separator = page.indexOf("?") == -1 ? "?" : "&";								
		
		//stitch url together
		var url = base + page + separator + "ajax=true";	
		
		//load content
		$.get(url, function(content) {					
			replaceContent(content);
		}).error(function() { 
			//page could not be loaded
			console.log("An error occured - the page could not be loaded.");
		});											
	}
	
	function replaceContent(content){	
			$('#main').html(content);
			
			if(typeof data == "undefined"){
				console.log("An error occured - data variable not found");
				return false;
			}							
			
			//set pagetitle
			document.title = data["pageTitle"];				
			
			//set body class
			var pageClasses = data["pageClasses"];
			
			//set page background if supplied
			if (typeof data["pageImage"] != "undefined") {
				setBgImage(data["pageImage"]);
			}																								

			// The element to be expanded	
			var main = $("#main");
			var content = $('#content');	 
			 
			//inital width
			var cur = {width: main.width()+'px'};
		 	content.hide();

			//Add new class with possibly new width
			$("body")
				.removeClass()	//remove all classes
				.addClass(pageClasses);		

			//final width
			var next = {width: main.width()+'px'};
			var ts = cur.width;

			//make animation if widths differ
			if(cur.width != next.width){
			
				// animate header (restore initial width, then animate to final width)
				$("#header").css(cur).animate(next, 600, 'swing');	
							
				// animate #main (container of #content)
				main.css(cur).animate(next, 600, 'swing', function(){
					/* after successful animation */
					main.css("width", "");	//remove inline styling 
					afterPageLoad();
				});	    	

			}else{
				afterPageLoad();
			} 			
	}
	
	
	/*
	 * function to be run, after the page animations has run
	 */					
	function afterPageLoad(){		
		initAjaxForm();		
		initImageHover();		
		initShadowbox();
		
		//fade text and content in				
		$('#content').fadeIn(400);	
		
		// columize text - MUST BE AFTER FADEIN OF TEXT. CANNOT COLUMIZE HIDDEN TEXT!
		initColumns();	
	}
	
	/*
	 * if the client supports javascript (he does if he reads this) and his location isn't ajaxified with hashes (eg. /#/collections but instead /collections) 
	 * he will be redirected
	 */
	function goToBase(){
		var currentUrl = window.location.href;
		var nextUrl = currentUrl.replace(base, ''); 
		var positionOfHash = nextUrl.indexOf("#");		
		
		if(nextUrl != "" && positionOfHash != 0){		
			window.location.href =  "#/" + nextUrl; //base +
			return false;
		}else{
			return true;
		}
	}
	
	/*
	 * image hover effect
	 */
	function initImageHover(){
		$('.imageHover').hover(function() { 
			$('img', this).stop().animate({"opacity": 0.5}); 
		},function() { 
			$('img', this).stop().animate({"opacity": 1}); 
		});	
	}
	
	/*
	 * ajaxify forms 
	 */
	function initAjaxForm(){
		var myForm = $("form");			
		myForm.ajaxForm({
			success: function(content){								
				var hasFullDoc = $("#main", content).size();				
				if(hasFullDoc > 0){
					// replace entire document
					document.open();
					document.write(content);
					document.close();
								
				}else{
					//replace #content only
					replaceContent(content);				
				}
			},
			error: function(data){

				var error_msg;							
				if(data.status == 500 || data.status == 403){
					var dummy = $('<div />').html(data.responseText);
					var error_msg = dummy.find('p').text();	
					alert(error_msg);
				}else{
					console.log("Error retrieving page");
				}
			}
		});	
	}		

	/*
	 * Change hash on menu click
	 */
	function initLinkHasher(){
		$('#primary-menu a, a[href*="ajax=true"], .ngg-albumoverview a, .comments-link a, #nav-above a, .post .entry-title a').address(function() { 								
			//set flag to disable hover animation
			menuClickedFlag = true;
			
			var url = $(this).attr('href');
			return url.replace(base, '');  
		});  
	}
	

/*
 * primary menu
 ****************************************************************************/
 
 	/**
	 * Initializes and setups the superfish menu with callback
	 */
	function setupSuperfishMenu(){
		$('div.menu ul:first-child').superfish( {
			delay: 100,
			animation: { opacity: 'show', height: 'show' },
			speed: 300,
			autoArrows: false,
			dropShadows: false,
			onHide: function() {
				showCurrentSubmenu();
				}
			}
		);
	}		
	
	function setMenuHeight(){		

		// only applies to frontpage - make menu animation
		if($.address.path() == "/"){
			$("body.home #primary-menu").hover(		
				/* hover in */
				function(){					
					$("body.home #primary-menu").stop().animate({height: menuHeight}, 400, 'swing');	
				/*hover out */
				}, function(){
					if(menuClickedFlag == false){
						$("body.home #primary-menu").stop().animate({height: "30px"}, 400, 'swing', function(){
							$(this).css("height","");
						});
					}	
				});								
		}else{
			$("#primary-menu").height(menuHeight + "px");					
		}		
	}
	
	function getMenuHeight(){
		var max_height=0;
		var current_height;
		
		// find the longest submenu
		$("#primary-menu li ul.sub-menu").each(function () {
			current_height = $(this).outerHeight();
			if ( current_height > max_height ) {
				max_height = current_height;
			}
		});	
		menuHeight = max_height + 30;
	}	
	
	/*
	 * This function overides some dropdown core functionality. It enables the active submenu to become visible.
	 */
	function showCurrentSubmenu(){
		// figure out whether current page is a parent or child (depth)
		var current;
        var currentParent = $("#primary-menu li.current-menu-item ul.sub-menu");
        var currentChild = $("#primary-menu li ul.sub-menu li.current-menu-item").parent(".sub-menu");
		if (currentParent.length != 0) {
			current = currentParent;
		} else if (currentChild.length != 0) {
			current = currentChild;
		}
		
		// show current if it is hidden and other menu items are not being hovered
		if ($(current).is(':visible') == false && $('.sfHover').length == 0){
			var css = {"visibility":"visible", "left" : 0, "top": "2em"};
			$(current).css(css).fadeIn(300);
		}		
	}	
	
 	
	
})(jQuery);
