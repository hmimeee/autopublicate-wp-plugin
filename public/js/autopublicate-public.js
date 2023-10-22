/*
   
   Table Of Content
   
   1. Preloader
   2. Smooth Scroll
   3. Scroll To Top
   4. Tooltip
   5. Popover
   6. Ajaxchimp for Subscribe Form
   7. Video and Google Map Popup
   8. Magnific Popup
   9. Image Carousel/Slider
  10. Load More Post
  11. Load More Portfolio
  12. End Box (Popup When Scroll Down)
 

*/


(function ($) {
   'use strict';

   jQuery(document).ready(function () {
      // ---------Responsive-navbar-active-animation-----------
      function activeNavMenu() {
         var tabsNewAnim = $('#ap-navbar');
         var activeItemNewAnim = tabsNewAnim.find('.active');
         var activeWidthNewAnimHeight = activeItemNewAnim.innerHeight();
         var activeWidthNewAnimWidth = activeItemNewAnim.innerWidth();
         var itemPosNewAnimTop = activeItemNewAnim.position();
         var itemPosNewAnimLeft = activeItemNewAnim.position();
         $(".hori-selector").css({
            "top": itemPosNewAnimTop.top + "px",
            "left": itemPosNewAnimLeft.left + "px",
            "height": activeWidthNewAnimHeight + "px",
            "width": activeWidthNewAnimWidth + "px"
         });
      }
      $(document).ready(function () {
         setTimeout(function () { activeNavMenu(); });
      });
      $(window).on('resize', function () {
         setTimeout(function () { activeNavMenu(); }, 1);
      });
      $(".navbar-toggler").click(function () {
         $(".navbar-collapse").slideToggle(1);
         setTimeout(function () { activeNavMenu(); });
      });
   });

})(jQuery);
