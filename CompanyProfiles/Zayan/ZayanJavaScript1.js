var changeBackGround = function () {
    document.getElementById("Found").style.backgroundImage ="url(img/profile.jpg)";
    var x = Math.random();
    if(x<=0.33)
    {
        document.getElementById("Found").style.backgroundImage = "url(img/profile.jpg)";
    }
    else if(x<=0.66)
    {
        document.getElementById("Found").style.backgroundImage = "url(img/images.jpg)";
    }
    else
    {
        document.getElementById("Found").style.backgroundImage = "url(img/index.jpg)";
    }
}