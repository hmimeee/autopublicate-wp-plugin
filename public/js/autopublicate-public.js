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
         if (tabsNewAnim.length) {
            var activeItemNewAnim = tabsNewAnim.find('.active');
            var activeWidthNewAnimHeight = activeItemNewAnim.innerHeight();
            var activeWidthNewAnimWidth = activeItemNewAnim.innerWidth();
            var itemPosNewAnimTop = activeItemNewAnim.position();
            var itemPosNewAnimLeft = activeItemNewAnim.position();
            $(".hori-selector").css({
               "top": itemPosNewAnimTop?.top + "px",
               "left": itemPosNewAnimLeft?.left + "px",
               "height": activeWidthNewAnimHeight + "px",
               "width": activeWidthNewAnimWidth + "px"
            });
         }
      }
      $(document).ready(function () {
         setTimeout(function () { activeNavMenu(); }, 10);
      });
      $(window).on('resize', function () {
         setTimeout(function () { activeNavMenu(); }, 10);
      });
      $(".navbar-toggler").click(function () {
         $(".navbar-collapse").slideToggle(1);
         setTimeout(function () { activeNavMenu(); }, 10);
      });

      $('#avatar-input-init').on('change', (e) => {
         let input = e.target;
         if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
               $('#avatar').attr('src', e.target.result);
               document.getElementById('avatar-input').files = input.files;
            }

            reader.readAsDataURL(input.files[0]);
         }
      });

      $('body').on('change', '.payout-method', (e) => {
         if (e.target.value == 'bank') {
            $('#paypal-box').addClass('d-none');
            $('#bank-box').removeClass('d-none');
         } else {
            $('#bank-box').addClass('d-none');
            $('#paypal-box').removeClass('d-none');
         }
      });
   });

})(jQuery);
