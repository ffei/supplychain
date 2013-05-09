<?php

  $row = $_GET['row'];

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 100% }
    </style>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script type="text/javascript">

    var row = <?=$row ?>;
    var origL;
    var origU;

    if((row + 1) * 50 < 71) {
      origL = row * 50;
      origU = (row + 1) * 50;
    } else {
      origL = row * 50;
      origU = 71;
    }

    for(var i = 0; i < 7; i++) {
      var destL = i * 56;
      var destU = (i + 1) * 56;
      window.open("supplier_retailer.php?origL="+origL+"&origU="+origU+"&destL="+destL+"&destU="+destU);
    }
    window.open("supplier_retailer.php?origL="+origL+"&origU="+origU+"&destL=392&destU=433");

    </script>
  </head>
  <body>
  </body>
</html>