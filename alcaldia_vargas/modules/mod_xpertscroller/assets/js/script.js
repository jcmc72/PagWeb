/*!
 * @package XpertScroller
 * @version 3.10-1-GFF3CA2D
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
/*! Copyright 2012, Ben Lin (http://dreamerslab.com/)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Version: 1.0.13
 *
 * Requires: jQuery 1.2.3 ~ 1.8.2
 */
;(function(a){a.fn.extend({actual:function(b,l){if(!this[b]){throw'$.actual => The jQuery method "'+b+'" you called does not exist';}var f={absolute:false,clone:false,includeMargin:false};var i=a.extend(f,l);var e=this.eq(0);var h,j;if(i.clone===true){h=function(){var m="position: absolute !important; top: -1000 !important; ";e=e.clone().attr("style",m).appendTo("body");};j=function(){e.remove();};}else{var g=[];var d="";var c;h=function(){c=e.parents().andSelf().filter(":hidden");d+="visibility: hidden !important; display: block !important; ";if(i.absolute===true){d+="position: absolute !important; ";}c.each(function(){var m=a(this);g.push(m.attr("style"));m.attr("style",d);});};j=function(){c.each(function(m){var o=a(this);var n=g[m];if(n===undefined){o.removeAttr("style");}else{o.attr("style",n);}});};}h();var k=/(outer)/g.test(b)?e[b](i.includeMargin):e[b]();j();return k;}});})(jQuery);

jQuery(document).ready(function()
{
    function scrollerResize()
    {
        if( jQuery('.scroller').length > 0 )
        {
            jQuery('.scroller').each(function(i){

                var el = jQuery(this),
                    p = el.parent(),
                    next=0, prev=0, w=0;

                //calculate width
                if( p.find('.next').is(':visible') )
                {
                    next = p.find('.next').width() + 5;
                }
                if( p.find('.prev').is(':visible') )
                {
                    prev = p.find('.prev').width() + 5;
                }

                w = p.width() - (next + prev);

                // Xpert tab fix
                if( p.parents('.txtabs-content').length > 0 )
                {
                    w = p.parents('.txtabs-content').width() - (next + prev);

                    //get padding values
                    var pad =  parseInt( p.parents('.txtabs-pane-in').css('padding-left') )
                            + parseInt( p.parents('.txtabs-pane-in').css('padding-right') );
                    w = w - pad;
                }

                el.find('.pane').css('width', w);
                el.css('width', w);

            });
        }
        // Full width for vertical layout
        if( jQuery('.basic_v').length > 0 )
        {
            jQuery('.basic_v').each(function(i){

                var el = jQuery(this);
                var width = el.width();
                
                el.find('.scroller, .pane').css('width', width);
            });

        }
    }

    scrollerResize();

    jQuery(window).bind("resize", function() {
        scrollerResize();
    });

});

