var entangle = "#entangle"
var whatIsEntangle = "#what-is-entangle"
var register = "#register"
var aboutUs = "#about-us"

$(function(){
    fixDimensions();
    $('.single-page-nav').singlePageNav({
        offset: $('.single-page-nav').outerHeight(),
        filter: ':not(.external)',
        updateHash: true,
        beforeStart: function() {
            console.log('begin scrolling');
        },
        onComplete: function() {
            console.log('done scrolling');
        }
    });
});

function fixDimensions(){
	$(".section").css("min-height",$(window).height()+"px");
}