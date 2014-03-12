function myFunction1() {
var x = document.getElementById("A");
if(x.innerHTML == "- I am a 6th semester CSEN student at GUC.<br>- Currently I am JTA in the <br>    concepts of programming languages course. <br>- I love sports in general. ") {
x.innerHTML="";
} else {
x.innerHTML ="- I am a 6th semester CSEN student at GUC.<br>- Currently I am JTA in the <br>    concepts of programming languages course. <br>- I love sports in general. ";
}
document.getElementById("B").innerHTML="";
document.getElementById("C").innerHTML="";
}

function myFunction2() {
document.getElementById("A").innerHTML="";
var x = document.getElementById("B");
if(x.innerHTML == "Employee in Component 1.") {
x.innerHTML="";
} else {
x.innerHTML="Employee in Component 1.";
}
document.getElementById("C").innerHTML="";
}

function myFunction3() {
document.getElementById("A").innerHTML="";
document.getElementById("B").innerHTML="";
var x = document.getElementById("C");
if(x.innerHTML == "<ul> <li> Java <br> </li>  <li> C/C++ <br> </li>  <li> CP/CHR <br> </li>  <li> Prolog <br></li> <li> Haskell <br></li>  <li> ASP.net <br></li> <li> C# <br></li>  <li>Visual studio </li></ul>"){
x.innerHTML = "";
}else{
x.innerHTML="<ul> <li> Java <br> </li>  <li> C/C++ <br> </li>  <li> CP/CHR <br> </li>  <li> Prolog <br></li> <li> Haskell <br></li>  <li> ASP.net <br></li> <li> C# <br></li>  <li>Visual studio </li></ul>";
}
}

function myFunction4() {
window.location = "https://www.facebook.com/heba.aamer.355";
}