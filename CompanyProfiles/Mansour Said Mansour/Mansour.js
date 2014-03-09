var x =2;
function changeBackGround() {
var y;
  if(x==1)
  {
   y = "url(background.jpg)";
   x=2;
  }else if (x==2)
  {
   y="url(background2.jpg)";
   x=1;
  }
        document.body.style.backgroundImage = y;
alert("A COD bro here too, Omar :3");
}