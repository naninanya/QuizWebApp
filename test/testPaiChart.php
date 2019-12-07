<html>

<head>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../css/paichart.css">
</head>

<body>
    <canvas id="myChart" class="paichart"></canvas>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <?php
    print "<script>";
    print "new Chart(document.getElementById(\"myChart\"), {";
    print "type: \"doughnut\",";
    print "data: {";
    print "labels: [\"A\", \"B\", \"C\", \"D\", \"未回答\"],";
    print "datasets: [";
    print "{";
    print "data: [300, 50, 100,40,100],";
    print "backgroundColor: [";
    print "\"rgb(251, 90, 72)\",";
    print "\"rgb(83, 185, 235)\",";
    print "\"rgb(102, 204, 51)\",";
    print "\"rgb(255, 182, 43)\",";
    print "\"rgb(134, 145, 152)\"";
    print "]";
    print "}";
    print "]";
    print "}";
    print "});";
    print "</script>";
    ?>
    <!-- <figure>
        <svg class="circle" width="330" height="330">
            <circle class="type type4 typeA4" cx="165" cy="165" r="115" />
            <circle class="type type3 typeA3" cx="165" cy="165" r="115" />
            <circle class="type type2 typeA2" cx="165" cy="165" r="115" />
            <circle class="type type1 typeA1" cx="165" cy="165" r="115" />
        </svg>
        </figure> -->
    <!-- <figure>
            <figcaption>SVG PIE Chart with CSS animation</figcaption>
            <svg viewBox="0 0 63.6619772368 63.6619772368">
                <circle class="pie1" cx="31.8309886184" cy="31.8309886184" r="15.9154943092" />
                <circle class="pie2" cx="31.8309886184" cy="31.8309886184" r="15.9154943092" />
                <circle class="pie3" cx="31.8309886184" cy="31.8309886184" r="15.9154943092" />
                <circle class="pie4" cx="31.8309886184" cy="31.8309886184" r="15.9154943092" />
            </svg>
            </figure> -->
</body>

</html>