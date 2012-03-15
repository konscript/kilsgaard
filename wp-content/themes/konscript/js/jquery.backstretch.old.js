/*
 * jQuery Backstretch
 * Version 1.1.3
 * http://srobbin.com/jquery-plugins/jquery-backstretch/
 *
 * Add a dynamically-resized background image to the page
 *
 * Copyright (c) 2010 Scott Robbin (srobbin.com)
 * Dual licensed under the MIT and GPL licenses.
*/

(function($) {

$.backstretch = function(image, options, callback) {
    var src = image;
    var settings = {
        centeredX: true, // Should we center the image on the X axis?
        centeredY: true, // Should we center the image on the Y axis?
        speed: 0         // fadeIn speed for background after image loads (e.g. "fast" or 500)
    };

    // hack to acccount for iOS position:fixed shortcomings
    var rootElement = ("onorientationchange" in window) ? $(document) : $(window), 
        imgRatio, bgImg, bgWidth, bgHeight, bgOffset, bgCSS;

    // Extend the settings with those the user has provided
    if(options && typeof options == "object") $.extend(settings, options);

    // Initialize
    $(document).ready(_init);

    // For chaining
    return {
        set : function(id) {
            set(id);
        }     
    };

    function set(image) {    
        if(image != src){
            src = image;
            _init();
        }
    }

    function _init() {
        // Make sure we dont have a backstretch already
        if($('#backstretch').size() > 0) {
            $('#backstretch').attr("id", "").fadeOut(settings.speed).queue(function(){
                $(this).remove().dequeue();
            });
        }
        // Prepend image, wrapped in a DIV, with some positioning and zIndex voodoo
        if(src) {
            var container = $("<div />").attr("id", "backstretch")
                    .css({
                        left: 0, 
                        top: 0, 
                        position: "fixed", 
                        overflow: "hidden", 
                        zIndex: -9999
                    }),
                img = $("<img />")
                    .css({
                        position: "relative", 
                        display: "none"
                    })
                    .load(function(e) {
                        var self = $(this);
                        var elm = e.target;
                        var width = elm.width;
                        var height = elm.height;                        
                        imgRatio = width / height;

                        _adjustBG(function() {
                            self.fadeIn(settings.speed, function(){
                                if(typeof callback == "function") callback();
                            });
                       });
                    })
                    .appendTo(container);

            $("body").prepend(container);
            img.attr("src", src); // Hack for IE img onload event

            // Adjust the background size when the window is resized or
            // orientation has changed (iOS)
            $(window).resize(_adjustBG);
        }
    }

    function _adjustBG(fn) {
        try {
            bgCSS = {left: 0, top: 0}
            bgWidth = rootElement.width();
            bgHeight = bgWidth / imgRatio;

            // Make adjustments based on image ratio
            if(bgHeight >= rootElement.height()) {
                bgOffset = (bgHeight - rootElement.height()) /2;
                if(settings.centeredY) $.extend(bgCSS, {top: "-" + bgOffset + "px"});
            } else {
                bgHeight = rootElement.height();
                bgWidth = bgHeight * imgRatio;
                bgOffset = (bgWidth - rootElement.width()) / 2;
                if(settings.centeredX) $.extend(bgCSS, {left: "-" + bgOffset + "px"});
            }

            $("#backstretch img").width( bgWidth ).height( bgHeight ).css(bgCSS);
        } catch(err) {
            // IE7 seems to trigger _adjustBG before the image is loaded.
            // This try/catch block is a hack to let it fail gracefully.
        }

        // Executed the passed in function, if necessary
        if (typeof fn == "function") fn();
    }
};

})(jQuery);
