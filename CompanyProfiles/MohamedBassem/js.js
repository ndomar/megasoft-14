$("document").ready( function() {
	
	center("#main");
	center("#profile-img");
	levitateImg();
	lastTimeIWasHappy();
});

$(window).resize(function() {
  	center("#main");
	center("#profile-img");
});

function center(x){
	
	var mainWidth = parseInt($(x).css("width"));
	var windowWidth = $(window).width();
	var leftMargin = (windowWidth - mainWidth)/2;
	$(x).css("margin-left",leftMargin+"");
}

function levitateImg(){

	setInterval(function(){
		$("#profile-img").css({
			"webkitTransform":"translate(0px,-5px)",
			"MozTransform":"translate(0px,-5px)",
			"msTransform":"translate(0px,-5px)",
			"OTransform":"translate(0px,-5px)",
			"transform":"translate(0px,-5px)"
		});
		setTimeout(function(){
			$("#profile-img").css({
				"webkitTransform":"translate(0px,5px)",
				"MozTransform":"translate(0px,5px)",
				"msTransform":"translate(0px,5px)",
				"OTransform":"translate(0px,5px)",
				"transform":"translate(0px,5px)"
			});
		},2000);
	},4000);
}

function lastTimeIWasHappy(){
	var contestDate = new Date(2013,10,28).getTime();
	var today = new Date().getTime();
	var diff = today - contestDate;
	diff = diff/1000/60/60/24/30;
	diff = diff.toFixed(5);
	$("#duration").html(diff+"");

}