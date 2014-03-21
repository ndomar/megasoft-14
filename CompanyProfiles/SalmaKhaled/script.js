
var a = document.createElement('a');
var linkText = document.createTextNode("Facebook");
a.appendChild(linkText);
a.title = "Facebook";
a.href = "https://www.facebook.com/salma.khaled28";
document.getElementById("Y").appendChild(a);

function myFunction() {

document.getElementById("about_me").innerHTML = "<p > Salma </br> Sagittarius  </br> love books , travelling , </br> ballet , music </br> and you know...</br>sometimes I code </br> </p>";
}

function myFunction2()
{

var r=confirm("Are you sure :O !");
if (r==true)
  {
document.getElementById("bg").style.backgroundImage = 'url(bg2.jpg)';  
 document.getElementById("X").innerHTML=" ";
  }
else
  {
  document.getElementById("X").innerHTML="pressed cancel. your loss not mine ";
  }

}
function myFunction3() {
document.getElementById("about_me").innerHTML=" <ul>  <p> Java <br>   ASP.net <br> C# <br>Visual studio <br> HTML <br> Javascript <br>  </p></ul> ";
}
