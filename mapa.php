<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>FRANCE MAP</title>
	<style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
		width: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
	<?php
		Extract($_GET);
		$Host    = "localhost";
		$Usuario = "root";	
		$Clave   = "root";
		$DB      = "futbol";
		$LeagueName="Ligue 1";
		
		//echo"<h2><center>$LeagueName</center></h2\n>";
		echo "<center>
		<form action='posiciones.php'>
		  <input type=image src='logo.png' width=150 height=200><br/><br/>
		  </form>
		</center>\n";
		echo "<div id=map></div>	\n";
		
		$Con = mysqli_connect($Host, $Usuario, $Clave, $DB);
		
		//Check connection	
		if(mysqli_connect_errno()){
			echo "Failed to connect to MYSQL: ".mysqli_connect_error();
			exit();
		}
		
		$sql = "Select * from equipos";
		if(!($oRs = mysqli_query($Con, $sql))){
			echo "Error <br>";
			exit;
		}	
	?>	
	
    <script src="https://maps.googleapis.com/maps/api/js?key= AIzaSyDxOcypQE5UaoPA4z3NU7c76oK1nYNc3QU"></script>
		
    <script>
      function initialize() 
		{
			var france = { lat: 46.717711, lng: 2.426866 };     //Latitud del centro del país    
			var map = new google.maps.Map(document.getElementById('map'),{ zoom: 6, center: france });

			// This event listener calls addMarker() when the map is clicked.
			google.maps.event.addListener(map, 'click', function(event) {
			  addMarker(event.latLng, map);
			});	 
		
			/*var Airdrieonians= {lat:54.902145, lng:-5.012481};			
			var icon = 'equipos/Airdrieonians.png';		
			addMarker(Airdrieonians,"Airdrieonians",icon,map);*/
			
			/*var icon = {
			url: "equipos/Paris_Saint_Germain.png", // url
			scaledSize: new google.maps.Size(24, 24), // scaled size
			origin: new google.maps.Point(0,0), // origin
			anchor: new google.maps.Point(0, 0) // anchor
			};*/
		
			// importante
			//var Paris_Saint_Germain= {lat:48.841389, lng:2.25306};			
			//var icon = 'equipos/Paris_Saint_Germain.png';		
			//addMarker(Paris_Saint_Germain,"Paris Saint Germain",icon,map);
		<?php
		while ($row = mysqli_fetch_array($oRs))
		{
			$N=str_replace(" ","_",$row["Nombre"]);
	?>			
			var <?php echo $N;?>= {lat:<?php echo $row["Latitud"];?>, lng:<?php echo $row["Longitud"];?>};				
			var icon = {
			url: <?php echo "'equipos/$N.png'";?>, // url
			scaledSize: new google.maps.Size(24, 24), // scaled size
			origin: new google.maps.Point(0,0), // origin
			anchor: new google.maps.Point(0, 0) // anchor
			};			
			addMarker(<?php echo $N ?>,"<?php echo $row["Nombre"]?>",icon,map);
			
			<?php
		}
			?> 
		}
		
		
      // Adds a marker to the map.
      function addMarker(location, label, icon, map) {
        // Add the marker at the clicked location, and add the next-available label
        // from the array of alphabetical characters.
        var marker = new google.maps.Marker({
          position: location,
		  icon: icon,
          map: map
        });
      }

      google.maps.event.addDomListener(window, 'load', initialize);

    </script>
	
  </head>
  
  <BODY>
    <div id="map"></div>
  </body>  
</html>