<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
</div>
<script>
    var w = window.innerWidth;
    var h = window.innerHeight;
    var urlparts = window.location.pathname.replace("/ocrt/index.php/","").split("/");
//    alert(w + 'x' + h);
//    alert(window.location.pathname);

    if (w < 600) {
        //All pages
        document.getElementById("title").style.marginLeft="5px";
        var h1 = document.getElementsByTagName("h1");
        var i;
        for (i = 0; i < h1.length; i++) {
            h1[i].style.fontSize = "large";
        }

        var h2 = document.getElementsByTagName("h2");
        var i;
        for (i = 0; i < h2.length; i++) {
            h2[i].style.fontSize = "medium";
            h2[i].style.margin = "5px";
        }

        var h3 = document.getElementsByTagName("h3");
        var i;
        for (i = 0; i < h3.length; i++) {
            h3[i].style.fontSize = "small";
            h3[i].style.margin = "5px";
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

        if (urlparts[0]==="home") {
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
                img[0].style.padding = "0px";
            }    
        }
        else if (urlparts[0]==="explore") {
            var explore = document.getElementById("explore");

            var div = explore.getElementsByTagName("div");
            var i;
            for (i = 0; i < div.length; i++) {
                div[i].style.padding = "0px";
            }    

            var p = explore.getElementsByTagName("p");
            var i;
            for (i = 0; i < p.length; i++) {
                p[i].style.width = 300;
                p[i].style.margin = "2px";
            }    

            document.getElementById("googleMap").style.width=300;
            document.getElementById("googleMap").style.height=300;
        }
    }
</script>
</body>
</html>