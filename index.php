<?php

  $origL = $_GET['origL'];
  $origU = $_GET['origU'];
  $destL = $_GET['destL'];
  $destU = $_GET['destU'];

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
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-fXgZnKis_FNVA4yqCaQ0LM4IRcOJZEM&sensor=false&language=zh">
    </script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script type="text/javascript">

    var origL = <?=$origL ?>;
    var origU = <?=$origU ?>;
    var destL = <?=$destL ?>;
    var destU = <?=$destU ?>;


      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(35 , 105),
          zoom: 5,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map-canvas"),
            mapOptions);
      }
      google.maps.event.addDomListener(window, 'load', initialize);

      var retailers = [];
      var distanceMatrix = [];
      var durationMatrix = [];
      var service = new google.maps.DistanceMatrixService();

      $(document).ready(function(){
        $.get("retailer.php", function(resp){
          var resp_json = JSON.parse(resp);
          for(var temp in resp_json) {
            var retailer = new Object();
            retailer.id = resp_json[temp].id;
            retailer.demand = resp_json[temp].demand;
            retailer.longitude = resp_json[temp].longitude;
            retailer.latitude = resp_json[temp].latitude;
            retailers.push(retailer);
          }
          for(var i = 0; i < retailers.length; i++) {
            distanceMatrix[i] = new Array(retailers.length);
            durationMatrix[i] = new Array(retailers.length);
          }
          //console.log(retailers);
          getMatrix();
        })
      })

      var currentOrigin;
      var currentDestination;
      var flag;
      var interval;
      var timeWaited;
      var origins = [];
      var destinations = [];
      function getMatrix() {
        currentOrigin = origL;
        currentDestination = destL;
        flag = true;
        interval = setInterval(function(){
          if(flag) {
            //console.log("A new batch.");
            flag = false;
            timeWaited = 0;
            origins = [];
            destinations = [];
            for(var i = 0; i < 4; i++) {
              if(currentDestination+i < destU) {
                var tempPoint = new google.maps.LatLng(retailers[currentDestination+i].latitude, retailers[currentDestination+i].longitude);
                destinations.push(tempPoint);
              }
              
            }
            for(var i = 0; i < 25; i++) {
              if(currentOrigin+i < origU) {
                var tempPoint = new google.maps.LatLng(retailers[currentOrigin+i].latitude, retailers[currentOrigin+i].longitude);
                origins.push(tempPoint);
              }
            }
            service.getDistanceMatrix({
              origins: origins,
              destinations: destinations,
              travelMode: google.maps.TravelMode.DRIVING,
              avoidHighways: false,
              avoidTolls: false
            }, callback);
            console.log("Request sent.");
          } /*else {
            timeWaited += 200;
            if(timeWaited >= 10000) {
              flag = true;
            }
          }*/
        }, 200);
        
      }

      function callback(response, status) {
        if (status == google.maps.DistanceMatrixStatus.OK) {
          var origins = response.originAddresses;
          var destinations = response.destinationAddresses;

          for (var i = 0; i < origins.length; i++) {
            var results = response.rows[i].elements;
            for (var j = 0; j < results.length; j++) {
              var element = results[j];
              if (element.status == google.maps.DistanceMatrixStatus.OK) {
                var distance = element.distance.value;
                var duration = element.duration.text;
                console.log((currentOrigin+i) + "," + (currentDestination+j));
                //console.log(distance);
                distanceMatrix[currentOrigin+i][currentDestination+j] = distance;
                durationMatrix[currentOrigin+i][currentDestination+j] = duration;
              } else {
                console.log((currentOrigin+i) + "," + (currentDestination+j));
                console.log(element);
              }
            }
          }

          currentDestination += 4;
          if(currentDestination >= destU) {
            currentDestination = 0;
            currentOrigin += 25;
            if(currentOrigin >= origU) {
              clearInterval(interval);
              //console.log(distanceMatrix);
              output();
            }
          }
          console.log("Current: "+currentOrigin+","+currentDestination);
          setTimeout(function(){
            flag = true;
          }, 10000)
          //console.log(flag);
        } else {
          console.log(status);
          setTimeout(function(){
            flag = true;
          }, 1000)
        }
        
        
      }

      function output() {
        $("body").html("<table>");
        for(var i = origL; i < origU; i++) {
          $("body").append("<tr>");
          for(var j = destL; j < destU; j++) {
            $("body").append("<th>" + distanceMatrix[i][j] + "</th>");
          }
          $("body").append("</tr>");
        }
        $("body").append("</table>")
      }


    </script>
  </head>
  <body>
    <div id="map-canvas"/>
  </body>
</html>