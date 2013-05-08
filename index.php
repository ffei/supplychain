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
        currentOrigin = 0;
        currentDestination = 0;
        flag = true;
        interval = setInterval(function(){
          if(flag) {
            flag = false;
            timeWaited = 0;
            origins = [];
            destinations = [];
            for(var i = 0; i < 4; i++) {
              if(currentDestination+i < retailers.length) {
                var tempPoint = new google.maps.LatLng(retailers[currentDestination+i].latitude, retailers[currentDestination+i].longitude);
                destinations.push(tempPoint);
              }
              
            }
            for(var i = 0; i < 25; i++) {
              if(currentOrigin+i < retailers.length) {
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
            }, callback)
          } else {
            timeWaited += 200;
            if(timeWaited >= 60000) {
              flag = true;
            }
          }
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
                //console.log(i + "," + j);
                //console.log(distance);
                distanceMatrix[currentOrigin+i][currentDestination+j] = distance;
                durationMatrix[currentOrigin+i][currentDestination+j] = duration;
              } else {
                console.log((currentOrigin+i) + "," + (currentDestination+j));
                //console.log(element);
                //console.log(distanceMatrix[currentOrigin+i][currentDestination+j]);
              }
            }
          }

          currentDestination += 4;
          if(currentDestination >= retailers.length) {
            currentDestination = 0;
            currentOrigin += 25;
            if(currentOrigin >= retailers.length) {
              clearInterval(interval);
              //console.log(distanceMatrix);
              output();
            }
          }
        }
        flag = true;
      }

      function output() {
        $("body").html("<table>");
        for(var i = 0; i < retailers.length; i++) {
          $("body").append("<tr>");
          for(var j = 0; j < retailers.length; j++) {
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