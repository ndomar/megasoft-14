var click=false;
var fb="https://www.facebook.com/No.Eight.8";
function infoDisplay() {
	if(click){
		click=!click;
		location.reload();
	}else{
		var c=document.getElementById("child");
		var p=document.getElementById("parent");
		p.removeChild(c);
		document.getElementById("title").innerHTML = "Megasoft-14";
		document.getElementById("link").innerHTML = "<a href=\""+fb+"\">facebook</a>";
		document.getElementById("info").innerHTML = "<B>Name:</B><br>Kareem Wahby <br><br><B>Age:</B><br> 21 years old<br><br> <B>Company Position</B>:<br>Employee in C4 <br><br> <B>About me:</B><br>I'm an active person who like listening to rock and metal.<br>I'm aslo a gamer and i like reading. <br><br> <B>Programing Skills:</B> <ul> <li>Java</li> <li>C</li><li>html & css</li><li>javaScript</li><li>haskell</li><li>prolog</li><li>MSSQL</li><li>Visual Basic</li></ul> <B>Contact Me:</B> <br> <b>E-mail:</b>  Kareeem.Wahby@gmail.com <br> " ;
		click=!click;
	}
}
