import UIkit from "uikit";
import Icons from "uikit/dist/js/uikit-icons";
UIkit.use(Icons);
import "./components/megamenu.js";


(function ($, sr) {
  // debouncing function from John Hann
  // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
  var debounce = function (func, threshold, execAsap) {
    var timeout;

    return function debounced() {
      var obj = this,
        args = arguments;
      function delayed() {
        if (!execAsap) func.apply(obj, args);
        timeout = null;
      }

      if (timeout) clearTimeout(timeout);
      else if (execAsap) func.apply(obj, args);

      timeout = setTimeout(delayed, threshold || 100);
    };
  };
  // smartresize
  jQuery.fn[sr] = function (fn) {
    return fn ? this.bind("resize", debounce(fn)) : this.trigger(sr);
  };
})(jQuery, "smartresize");


jQuery(document).ready(function ($) {
  var headerheight = $(".header-wrap").height();

  setPageMargin();
  function setPageMargin() {

    if($(window).width()>1200){
      headerheight = $(".header-wrap").height();
      $(".nav-margin").height(headerheight);

    }else{
      $(".nav-margin").height(0);
    }
      
    
  }

  $(window).smartresize(function () {
    setPageMargin();

    $('.menu-search').removeClass('open');
    
    $('.search-link').removeClass('open');

    if($(".event-container")){
      $(".event-container").removeClass("list-view").addClass("grid-view");

      $(".list-toggle").removeClass("active");
      $(".grid-toggle").addClass("active");

      $(".event-container").removeClass("list-view").addClass("grid-view");
    }
    
  });

  colorHeader();

  $(window).scroll(function () {
    colorHeader();
  });

  function colorHeader() {
    if ($(window).scrollTop() > 50) {
      $(".header-wrap").addClass("scrolled");
    } else {
      $(".header-wrap").removeClass("scrolled");
    }
  }

 var slideCount = $(".featured-slide").length;

  var currentslide = 0;

  function nextSlide(){
    $(".featured-slide").removeClass('active');
    
    $(".slide-" + currentslide).addClass('active');
    
    currentslide++;
    if(currentslide>slideCount-1){
        currentslide=0
    }

  }

 setInterval(nextSlide, 5000);

 $(".featured-slide-trigger").click(function(e){

    e.preventDefault();

    $(".featured-slide").removeClass('active');

    var target = $(this).attr('href').split('#')[1];

    $("." + target).addClass("active");

 });


 
 var url_hsh = window.location.hash;
 var viewtarget = url_hsh.split("#")[1];

$(".view-toggle-btn").removeClass('active');
   if (viewtarget == "list") {

      $(".list-toggle").addClass("active");

      $(".event-container").removeClass("grid-view").addClass("list-view");

   } else {

      $(".grid-toggle").addClass("active");

      $(".event-container").removeClass("list-view").addClass("grid-view");


   }




 $(".view-toggle-btn").click(function(e){
  
 

  viewtarget = $(this).attr('href').split('#')[1];
  toggleEventView(viewtarget);
  
 });


 $(".filter-toggle").click(function () {
   $(".post-filter").toggle();
 });


/*
  $('.gform_wrapper').each(function() {
    var form = $(this);
    
    // Find the submit button within this form
    var submitButton = form.find('input[type="submit"], button[type="submit"]');
    
 
    // Hide the default submit button and show the custom link
    submitButton.hide();
    

    $(this).find('.tca-button').on('click', function(e) {
   
      e.preventDefault(); // Prevent the default button behavior

      // Submit the closest form to the button (using .closest())
      $(this).closest('form').submit();
  });

    // Handle keyboard accessibility (Enter and Space keys)
    $('.tca-button').on('keydown', function(event) {
      if (event.key === 'Enter' || event.key === ' ') {
        $(this).closest('form').submit();
      }
    });
  });

*/


function toggleEventView(viewtarget){

  $(".event-filter").hide();

  if(viewtarget == "grid"){

    $(".grid-toggle").addClass('active');
    $(".list-toggle").removeClass("active");

    $('.event-container').fadeOut(function(){
      $(".event-container")
        .removeClass("list-view")
        .addClass("grid-view")
        .fadeIn();
    });

  }else{

    $(".grid-toggle").removeClass("active");
    $(".list-toggle").addClass("active");

    $(".event-container").fadeOut(function () {
      $(".event-container")
        .removeClass("grid-view")
        .addClass("list-view")
        .fadeIn();
    });

  }
  

}


$(".event-card, .news-card, .cta-block").click(function (e) {
  e.preventDefault();
  var newsurl = $(this).find("a").attr("href");
  var target = $(this).find("a").attr("target");
  if(target=="_blank"){
    window.open(newsurl);
  }else{
    window.location = newsurl;
  }
});
/*
var pdfUrl = $('#input_7_5').val(); 


$(document).on('gform_confirmation_loaded', function(event, formId) {
if(formId==7){
  window.location(pdfUrl, '_self');
}
  

}); */


});







