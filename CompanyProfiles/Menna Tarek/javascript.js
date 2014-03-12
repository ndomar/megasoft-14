

    function add_Me() {
        var src = "Ana.jpg";
        show_image("Ana.jpg", 250,350, "ANA");
    }


    function show_image(src, width, height, alt) {
        var img = document.createElement("img");
        img.src = src;
        img.width = width;
        img.height = height;
        img.alt = alt;
        document.body.appendChild(img);
    }