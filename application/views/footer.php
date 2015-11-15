<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
</div>
<script>
    var w = window.innerWidth;
    var h = window.innerHeight;
    var pathname = window.location.pathname.replace("/ocrt","").replace("/index.php/","");
    var urlparts = pathname.split("/");
    var page = urlparts[0];

    if (w < 600) {
        //All pages
        document.body.style.backgroundColor = "SaddleBrown";    
        document.body.style.backgroundImage = "none";    
        
        document.getElementById("title").style.marginLeft="5px";
        var h1 = document.getElementsByTagName("h1");
        var i;
        for (i = 0; i < h1.length; i++) {
            h1[i].style.fontSize = "large";
            h1[i].style.margin = "5px";
        }

        var h2 = document.getElementsByTagName("h2");
        var i;
        for (i = 0; i < h2.length; i++) {
            h2[i].style.fontSize = "medium";
        }

        var h3 = document.getElementsByTagName("h3");
        var i;
        for (i = 0; i < h3.length; i++) {
            h3[i].style.fontSize = "small";
        }

        var h4 = document.getElementsByTagName("h4");
        var i;
        for (i = 0; i < h4.length; i++) {
            h4[i].style.fontSize = "small";
        }

        var nav = document.getElementById("navigation");
        nav.style.marginLeft="5px";
        var ul = nav.getElementsByTagName("ul");
        ul[0].style.paddingLeft="0px";

        var li = ul[0].getElementsByTagName("li");
        var i;
        for (i = 0; i < li.length; i++) {
            li[i].style.marginLeft = "5px";
            li[i].style.padding = "0px";
            var a = li[i].getElementsByTagName("a");
            a[0].style.fontSize="medium";
        }    

        if (page==="home" || page === "") {
            var home = document.getElementById("home");
            home.style.padding="0px";

            var div = home.getElementsByTagName("div");
            var i;
            for (i = 0; i < div.length; i++) {
                div[i].style.marginLeft="10px";
                var a = div[i].getElementsByTagName("a");
                var img = a[0].getElementsByTagName("img");
                img[0].style.width = "100px";
                img[0].style.height = "100px";
                img[0].style.borderRadius = "5px";
                img[0].style.padding = "0px";
            }    
        }
        else if (page==="explore") {
            var explore = document.getElementById("explore");
            explore.style.width = 400;

            var instructions = document.getElementById("instructions");
            instructions.style.width = 400;
            instructions.style.marginBottom = "10px";

            var div = explore.getElementsByTagName("div");
            div[0].style.padding = "5px";
//            var i;
//            for (i = 0; i < div.length; i++) {
//                div[i].style.padding = "5px";
//            }    

            var p = explore.getElementsByTagName("p");
            var i;
            for (i = 0; i < p.length; i++) {
                p[i].style.width = 400;
            }    

            document.getElementById("googleMap").style.width=400;
            document.getElementById("googleMap").style.height=400;

            var pointInformation = document.getElementById("pointInformation");
            pointInformation.style.width = 400;
            pointInformation.style.paddingLeft = "10px";
            pointInformation.style.paddingRight = "0px";
            pointInformation.style.marginBottom = "10px";
            
            var filters = document.getElementById("filters");
            filters.style.width = 400;
            filters.style.paddingLeft = "10px";
            filters.style.paddingRight = "0px";
            filters.style.marginBottom = "10px";
        }
        else if (page==="train") {
            var train = document.getElementById("train");
            train.style.width = 400;
        }
    }
</script>
</body>
</html>