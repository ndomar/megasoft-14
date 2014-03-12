$('.sidebar').height($(document).height());

// $('.avatar').click(function(){
// 	$('.avatar').addClass('rotate');
// });

$('.info').on('mouseover', function(){
	$('.avatar').removeClass('overlay');
	$('.avatar').addClass('overlay-none');
});

$('.info').on('mouseout', function(){
	$('.avatar').removeClass('overlay-none');
	$('.avatar').addClass('overlay');
});