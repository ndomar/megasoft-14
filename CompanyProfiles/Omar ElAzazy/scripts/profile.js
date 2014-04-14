var positions;

function enlarge(x){
	x.style.width = "110px";
	x.style.height = "110px";	
}

function normalit(x){
	x.style.width = "80px";
	x.style.height = "80px";
}

function putAtCenter(x){
	x.style.position = "absolute";
	x.style.left = "50%";
	x.style.top = "50%";

	var width = x.width / 2;
	var height = x.height / 2;

	x.style.marginTop = "-" + height + "px";
	x.style.marginLeft = "-" + width + "px";
}

function orient_tabs(){
	positions = new Array();

	var radius = 150 + 50 + 32;
	for(var i = 1; i <= 6; i++){
		var tab = document.getElementById("tab" + i);
		
		normalit(tab);
		putAtCenter(tab);
		
		tab.style.visibility = "visible";

		var angleDeg = 90 - 30 - 60 * (i - 1);
		var angleRad = Math.PI * angleDeg / 180;
		var dx = Math.round(radius * Math.cos(angleRad));
		var dy = Math.round(radius * Math.sin(angleRad));
		positions[i - 1] = [dx, dy];
		tab.style.webkitTransform = "translate(" + dx + "px," + dy + "px) rotate(360deg)";
		tab.style.MozTransform = "translate(" + dx + "px," + dy + "px) rotate(360deg)";
		tab.style.msTransform = "translate(" + dx + "px," + dy + "px) rotate(360deg)";
		tab.style.oTransform = "translate(" + dx + "px," + dy + "px) rotate(360deg)";
	}
}

function adjustAll(){

	for(var i = 1; i <= 5; i++){
		var tab = document.getElementById("tab" + i);
		alert(positions[i - 1][0] + " " + positions[6][1] + " " + positions[i - 1][1] + " " + positions[6][0]);
		var leftPercentage = (positions[i - 1][0] / positions[6][0]);
		var topPercentage = (positions[i - 1][1] / positions[6][1]);

		tab.className = "notransition";
		tab.style.webkitTransform = "none";
		tab.style.MozTransform = "none";
		tab.style.msTransform = "none";
		tab.style.oTransform = "none";

		tab.style.position = "absolute";

		tab.style.left = leftPercentage + "%";
		tab.style.top = topPercentage + "%";

		tab.className = "transition";
		tab.className = "tab";
	}
	var centerImage = document.getElementById("centerImage");
	
	centerImage.className = "notransition";
	centerImage.style.webkitTransform = "none";
	centerImage.style.MozTransform = "none";
	centerImage.style.msTransform = "none";
	centerImage.style.oTransform = "none";

	centerImage.style.width = centerImage.width * 0.5;
	centerImage.style.height = centerImage.height * 0.5;

	centerImage.style.position = "absolute";
	centerImage.style.marginLeft = "0px";
	centerImage.style.marginTop = "0px";
	centerImage.style.left = "55%";
	centerImage.style.top = "55%";

	centerImage.className = "transition";
}

function showInfo(x){

	x.onclick = function(){};
	x.style.visibility = "hidden";
	x.style.webkitTransform = "translate(0px, 0px)";
	x.style.MozTransform = "translate(0px, 0px)";
	x.style.msTransform = "translate(0px, 0px)";
	x.style.oTransform = "translate(0px, 0px)";

	var totalHeight = x.offsetTop * 2;
	var totalWidth = x.offsetLeft * 2;

	for(var i = 1; i <= 5; i++){
		var tab = document.getElementById("tab" + i);

		var dx = totalWidth / 2 - totalWidth * 1 / 20;
		var dy = totalHeight / 2 - (((6 - i) - 1) * 1 / 5 + 0.1) * totalHeight;

		positions[i - 1] = [positions[i - 1][0] + totalWidth / 2 + dx, positions[i - 1][1] + totalHeight / 2 + dy];

		tab.style.webkitTransform = " translate(" + dx + "px," + dy + "px)";
		tab.style.MozTransform = " translate(" + dx + "px," + dy + "px)";
		tab.style.msTransform = " translate(" + dx + "px," + dy + "px)";
		tab.style.oTransform = " translate(" + dx + "px," + dy + "px)";
		tab.style.position = "fixed";
	}

	var centerImage = document.getElementById("centerImage");
	
	var centerdx = 5 / 100 * totalWidth - totalWidth / 2 - 500;
	var centerdy = totalHeight * 0.1 - totalHeight / 2 - 200;

	positions[5] = [totalWidth / 2 + centerdx, totalHeight / 2 + centerdy];
	positions[6] = [totalWidth, totalHeight];

	centerImage.style.webkitTransform = "scale(0.5, 0.5) translate(" + centerdx + "px," + centerdy + "px)";
	centerImage.style.MozTransform = "scale(0.5, 0.5) translate(" + centerdx + "px," + centerdy + "px)";
	centerImage.style.msTransform = "scale(0.5, 0.5) translate(" + centerdx + "px," + centerdy + "px)";
	centerImage.style.oTransform = "scale(0.5, 0.5) translate(" + centerdx + "px," + centerdy + "px)";
	centerImage.style.position = "fixed";

	document.getElementById("aboutme").style.visibility = "visible";
	document.getElementById("role").style.visibility = "visible";
	document.getElementById("experience").style.visibility = "visible";

	document.getElementById("aboutme").className += "fadeinclass";
	document.getElementById("role").className += "fadeinclass";
	document.getElementById("experience").className += "fadeinclass";

//	window.setTimeout(adjustAll(positions), 1500);
}