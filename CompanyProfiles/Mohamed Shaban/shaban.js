// function changeBackground() {
// 	$('body').css({'background-image': 'url(wall/' + images[cur] + ')'});
// }
function goForward(){
	$('#bg'+cur).fadeOut(1000);
	if (cur == images.length-1)
		cur = 0;
	else
		cur++;
	$('#bg'+cur).fadeIn(1000);
}

// function animate () {
// 	if(count == 740)
// 		flag = 1;
// 	else if (count == 0)
// 		flag = 0;

// 	if(flag)
// 		count = count -1;
// 	else
// 		count = count +1;
// 	document.getElementById("thName").style.marginLeft = count+'px';
// 	console.log(document.getElementById("thName").style.marginLeft);
// }
var images = ['0000e34a.jpeg', '0000e34d.jpeg', '0000e353.jpeg', '0000e356.jpeg', '0000e35b.jpeg', '0000e362.jpeg', '0000e365.jpeg', '0000e367.jpeg', '0000e36d.jpeg', '0000e375.jpeg', '0000e385.jpeg','0000e38e.jpeg','0000e5c6.jpeg', '0000e5c8.jpeg', '0000e5c9.jpeg', '0000e5ca.jpeg', '0000e5cf.jpeg', 'best-of-pod-anemone.jpg', 'best-of-pod-aurora.jpg', 'best-of-pod-cave.jpg', 'best-of-pod-lofoten.jpg', 'BingWallpaper-2014-03-06.jpg', 'Mountain.jpg', 'Mt.jpg', 'NatGeo03.jpg', 'NatGeo04.jpg', 'NatGeo11.jpg', 'NatGeo12.jpg', 'tenorio-volcano-national-park-costa-rica_65215_990x742.jpg', 'vermilion-cliffs-national-monument-arizona_65246_990x742.jpg'];
console.log(images.length)
cur = Math.floor(Math.random() * images.length);
count = 0;
flag = 0;
$('#bg'+cur).fadeIn(1000);
setInterval(function(){goForward()},5000);
