var elem1 = 1;
var elem2 = 1;
var elem3 = 1;
var elem4 = 1;
function element1() {
	if(elem1 == 1){
		document.getElementById("AboutMe").innerHTML ="Math lover <br>MegaSoft coder <br>JTA at GUC <br> I love RedHeads in general. ";
		document.getElementById("Hamada").innerHTML="";
		document.getElementById("Languages").innerHTML="";
		document.getElementById("ContactMe").innerHTML="";
	}
	else{
		document.getElementById("AboutMe").innerHTML ="";
		document.getElementById("Hamada").innerHTML="";
		document.getElementById("Languages").innerHTML="";
		document.getElementById("ContactMe").innerHTML="";
	}
	elem1 = 3-elem1;
}

function element2() {
	if(elem2 == 1){
		document.getElementById("AboutMe").innerHTML ="";
		document.getElementById("Hamada").innerHTML="Life Saviour :)";
		document.getElementById("Languages").innerHTML="";
		document.getElementById("ContactMe").innerHTML="";
	}
	else{
		document.getElementById("AboutMe").innerHTML ="";
		document.getElementById("Hamada").innerHTML="";
		document.getElementById("Languages").innerHTML="";
		document.getElementById("ContactMe").innerHTML="";
	}
	elem2 = 3-elem2;
}

function element3() {
	if(elem3 ==1 ){
		document.getElementById("AboutMe").innerHTML="";
		document.getElementById("Hamada").innerHTML="";
		document.getElementById("Languages").innerHTML="<ul> <li> Java <br> </li>  <li> C/C++ <br> </li> <li> Prolog <br></li> <li> Haskell <br></li> <li> C# <br></li>  <li>Visual studio </li></ul>";
		document.getElementById("ContactMe").innerHTML="";
	}
	else{
		document.getElementById("AboutMe").innerHTML="";
		document.getElementById("Hamada").innerHTML="";
		document.getElementById("Languages").innerHTML="";
		document.getElementById("ContactMe").innerHTML="";
	}
	elem3 = 3- elem3;
}
function element4() {
	if(elem4 == 1){
		document.getElementById("AboutMe").innerHTML="";
		document.getElementById("Hamada").innerHTML="";
		document.getElementById("Languages").innerHTML="";
		//------------------------------------------------------
		var a = document.createElement('a');
		var linkText = document.createTextNode("linkedIn");
		a.appendChild(linkText);
		a.title = "";
		a.href = "http://www.linkedin.com/pub/maisara-farahat/72/5aa/613";
		document.body.appendChild(a);
		
		//-----------------------------
		var a = document.createElement('a');
		var linkText = document.createTextNode("--------twitter ");
		a.appendChild(linkText);
		a.title = "";
		a.href = "https://twitter.com/maisarafarahat";
		document.body.appendChild(a);

		//-----------------------------------
		var a = document.createElement('a');
		var linkText = document.createTextNode("--------google+");
		a.appendChild(linkText);
		a.title = "";
		a.href = "https://plus.google.com/117005808041534608474";
		document.body.appendChild(a);
		//---------------------------------------------------

	}
	else{
		document.getElementById("AboutMe").innerHTML="";
		document.getElementById("Hamada").innerHTML="";
		document.getElementById("Languages").innerHTML="";
		document.getElementById("ContactMe").innerHTML="" ;
	}
	elem4 = 3-elem4;

}