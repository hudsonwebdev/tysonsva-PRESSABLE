jQuery(document).ready(function ($) {


$(".top-level > a").mouseenter(function(){

    $(this).parent().siblings().removeClass('active');
    
    $(this).parent().addClass("active");
    
    $(this).parent().siblings().find(".sub-menu-area").removeClass('open-menu');
    $(this).parent().find(".sub-menu-area").addClass('open-menu');
});


$('.sub-menu-area').mouseleave(function(){
   
    $(".sub-menu-area").removeClass('open-menu');
});


$('#primary').click(function(){
    $(".sub-menu-area").removeClass('open-menu');
})


$('.search-link').click(function(){
    $('.menu-search').toggleClass('open');
    $(this).toggleClass('open');
})






$('.mobile-toggle').click(function(){
    $(this).toggleClass('toggle-active');
    $('.menu-section').addClass('mobile-active');
})
$('.mobile-close').click(function(){
  
    $('.menu-section').removeClass('mobile-active');
})

$('.mobile-sub-close').click(function(){
    
    $('.open-menu').removeClass('open-menu');
});
$('.arrow-left').click(function(){
    
    $('.open-menu').removeClass('open-menu');
});



$('.mobile-hide-search').click(function(){

    $('.menu-search').removeClass('open');
});


});

