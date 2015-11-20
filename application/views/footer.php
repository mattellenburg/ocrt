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
        document.getElementById("title").style.marginLeft="5px";
        var h1 = document.getElementsByTagName("h1");
        h1[0].style.fontSize = "large";
        h1[0].style.margin = "5px";

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
                div[i].style.marginLeft="0px";
                var a = div[i].getElementsByTagName("a");
                var img = a[0].getElementsByTagName("img");
                img[0].style.width = "100px";
                img[0].style.height = "100px";
                img[0].style.borderRadius = "5px";
                img[0].style.padding = "0px";
                var h2 = a[0].getElementsByTagName("h2");
                h2[0].style.width = "100px";
                h2[0].style.height = "20px";
                h2[0].style.borderRadius = "5px";
                h2[0].style.padding = "0px";
            }    
        }
        else if (page==="explore") {
            var explore = document.getElementById("explore");
            explore.style.padding="0px";

            var map = document.getElementById("map");
            map.style.marginBottom="50px";
            map.style.height="500px";

            var pointInformation = document.getElementById("pointInformation");
            pointInformation.style.marginBottom="50px";
            pointInformation.style.height="500px";

            var filters = document.getElementById("filters");
            filters.style.marginBottom="50px";
            filters.style.height="500px";

            var content = document.getElementsByClassName("content");
            var i;
            for (i = 0; i < content.length; i++) {
                content[i].style.height="450px";
            }
        }
    }
</script>
</body>
</html>