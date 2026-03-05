jQuery(document).ready(function ($) {

// Desktop scroll behavior for utility menu and main menu
var desktopBreakpoint = 1200;
var lastScrollTop = 0;
var headerWrapOffset = 0;
var isHeaderFixed = false;

function handleDesktopScroll() {
    var windowWidth = $(window).width();
    var scrollTop = $(window).scrollTop();
    
    // Only apply on desktop
    if (windowWidth > desktopBreakpoint) {
        var $utilityContainer = $('.utility-container');
        var $headerWrap = $('.header-wrap');
        
        // Get the initial offset of header-wrap if not already set
        if (headerWrapOffset === 0 && $headerWrap.length) {
            headerWrapOffset = $headerWrap.offset().top;
        }
        
        // Hide utility menu when scrolling down, show when scrolling up or at top
        if (scrollTop > 50) {
            $utilityContainer.addClass('utility-hidden');
        } else {
            $utilityContainer.removeClass('utility-hidden');
        }
        
        // Fix header-wrap to top when it reaches the top of the window
        if (scrollTop >= headerWrapOffset && !isHeaderFixed) {
            $headerWrap.addClass('header-fixed');
            isHeaderFixed = true;
        } else if (scrollTop < headerWrapOffset && isHeaderFixed) {
            $headerWrap.removeClass('header-fixed');
            isHeaderFixed = false;
        }
    } else {
        // Reset on mobile/tablet
        $('.utility-container').removeClass('utility-hidden');
        $('.header-wrap').removeClass('header-fixed');
        isHeaderFixed = false;
    }
    
    lastScrollTop = scrollTop;
}

// Handle scroll event
$(window).on('scroll', function() {
    handleDesktopScroll();
});

// Handle resize event to recalculate header offset
$(window).on('resize', function() {
    headerWrapOffset = 0; // Reset to recalculate
    handleDesktopScroll();
});

// Initialize on page load
handleDesktopScroll();

$(".top-level > a").mouseenter(function(){
    if($(window).width()>1200){
    var $topLevel = $(this).parent();
    
    // Remove reverse animation class if present
    $topLevel.removeClass('line-reverse');
    
    // Remove active from siblings
    $topLevel.siblings().removeClass('active');
    
    // Add active to current item
    $topLevel.addClass("active");
    
    // Close sibling submenus and open current
    $topLevel.siblings().find(".sub-menu-area").removeClass('open-menu');
    $topLevel.find(".sub-menu-area").addClass('open-menu');



}else{

    
    $(".top-level > a").click(function(){
    
        var $topLevel = $(this).parent();
        
        // Remove reverse animation class if present
        $topLevel.removeClass('line-reverse');
        
        // Remove active from siblings
        $topLevel.siblings().removeClass('active');
        
        // Add active to current item
        $topLevel.addClass("active");
        
        // Close sibling submenus and open current
        $topLevel.siblings().find(".sub-menu-area").removeClass('open-menu');
        $topLevel.find(".sub-menu-area").addClass('open-menu');
    });


}



});


// Handle mouseleave on anchor (for cases where user leaves without opening submenu)
$(".top-level > a").mouseleave(function(){
    var $topLevel = $(this).parent();
    
    // Apply reverse immediately if not active and doesn't have current page
    // Check immediately without delay to prevent flash
    if (!$topLevel.hasClass('active') && !$topLevel.hasClass('has-current-page')) {
        reverseLineAnimation($topLevel);
    }
});


// Helper function to reverse line animation if needed
function reverseLineAnimation($topLevel) {
    // Only reverse if item doesn't have current page
    if (!$topLevel.hasClass('has-current-page')) {
        // Remove any existing reverse class to reset
        $topLevel.removeClass('line-reverse');
        
        // Force a synchronous reflow to ensure class removal is processed
        void $topLevel[0].offsetHeight;
        
        // Add reverse class immediately
        $topLevel.addClass('line-reverse');
        
        // Remove the reverse class after animation completes
        setTimeout(function() {
            $topLevel.removeClass('line-reverse');
        }, 600); // Match animation duration
    }
}

// Handle submenu mouseleave - close submenu and potentially reverse line
$('.sub-menu-area').mouseleave(function(){
    var $subMenu = $(this);
    var $topLevel = $subMenu.closest('.top-level');
    
    // Close the submenu
    $subMenu.removeClass('open-menu');
    
    // Remove active class
    $topLevel.removeClass('active');
    
    // Reverse animation if needed
    reverseLineAnimation($topLevel);
});


$('#primary').click(function(){
    $(".sub-menu-area").each(function() {
        var $subMenu = $(this);
        var $topLevel = $subMenu.closest('.top-level');
        
        if ($subMenu.hasClass('open-menu')) {
            $subMenu.removeClass('open-menu');
            $topLevel.removeClass('active');
            
            // Reverse animation if needed
            reverseLineAnimation($topLevel);
        }
    });
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

