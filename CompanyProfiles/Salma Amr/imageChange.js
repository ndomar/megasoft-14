var x = 1;
function changeImage()
{

element=document.getElementById('myimage')
if (x == 1)
  {
  element.src="me.jpg";
	x = 2;
  }
else
  {
  element.src="big.jpg";
	x = 1
  }
}


