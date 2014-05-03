var entangle = "#entangle"
var whatIsEntangle = "#what-is-entangle"
var register = "#register"
var aboutUs = "#about-us"

$(function(){

    fixDimensions();
    centerVertically();
    $('.single-page-nav').singlePageNav({
        offset: $('.single-page-nav').outerHeight(),
        filter: ':not(.external)',
        updateHash: true,
        beforeStart: function() {
        },
        onComplete: function() {
        }
    });
});

function fixDimensions(){
	$(".fulled").css("min-height",$(window).height()+"px");
	$(".halfed").css("min-height",$(window).height()/2+"px");
}

function centerVertically(){
	$(".center-vertically").each(function(){
		
		var myHeight = $(this).height();
		var parentHeight = Math.max($(this).parent().height(),parseInt($(this).parent().css("min-height")));
		var margin = (parentHeight - myHeight)/2;
		console.log(margin);
		$(this).css("padding-top",margin+"px");
	});
}