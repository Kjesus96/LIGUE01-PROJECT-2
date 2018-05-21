<!DOCTYPE html>
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
   <title>Ruta entre dos puntos</title>
   <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" >
   <?php
		Extract($_GET);
		//$id=$_GET[id];
		$Host    = "localhost";
		$Usuario = "root";	
		$Clave   = "root";
		$DB      = "futbol";
		
		$Con = mysqli_connect($Host, $Usuario, $Clave, $DB);
		
		//Check connection	
		if(mysqli_connect_errno()){
			echo "Failed to connect to MYSQL: ".mysqli_connect_error();
			exit();
		}
		
		$sql = "Select * from equipos where id=$id";
		if(!($oRs = mysqli_query($Con, $sql))){
			echo "Error <br>";
			exit;
		}	

		//Traemos de la base de datos la informacion del partido
		$sql = "SELECT Jornada, Fecha, Local, Visita, GolesLocal, GolesVisita FROM partidos WHERE Id='". $id."'";
		$datos = mysqli_query ($Con, $sql);
		//Almacenamos la informacion del partido
		$partido = mysqli_fetch_array ($datos);
		
		
	?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxOcypQE5UaoPA4z3NU7c76oK1nYNc3QU"></script>
</head>

<body style="font-family: Arial; font-size:13px;">
	<div class="container">
    <div class="row justify-content-md-center">
        <div class="row">
            <div class="col-4">
                <img src="equipos/<?=$partido['Local']?>.png" width='100'>
                <div class="row justify-content-md-center">
                    <h6><?=$partido['Local']?></h6>
                </div>
            </div>
            <div class="col-4">
                <div class="row justify-content-md-center">
                    <h2>
                        <?=$partido['GolesLocal']?> : <?=$partido['GolesVisita']?>
                    </h2>
                </div>
                <div class="row align-items-center">
                    <h4><?=$partido['Fecha']?></h4>
                </div>
                <div class="row justify-content-md-center">
                    <h5>Jornada: <?=$partido['Jornada']?></h5>
                </div>
            </div>
            <div class="col-4">
                <img src="equipos/<?=$partido['Visita']?>.png" width="100">
                <div class="row justify-content-md-center">
                    <h6><?=$partido['Visita']?></h6>
                </div>
            </div>
        </div>
    </div>
    <div id="duration">Duración : </div>
    <div id="distance">Distancia: </div>
    <div>
        <b>Modo de viaje:</b>
        <select id="Modo">
          <option value="WALKING">Caminando</option>
          <option value="DRIVING">Manejando</option>
          <option value="TRANSIT">Bus</option>
          <option value="BICYCLING">Bicicleta</option>
        </select>
        <br>
          <input type="submit" id="submit">
    </div>
    <div id="map" style="width: 100%; height: 700px;"></div>
	</div>
	<script type="text/javascript">

		var directionsService = new google.maps.DirectionsService();
		var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
		var france = {lat: 46.717711 , lng: 2.426866};     //Latitud del centro del país 
	
		var myOptions = {
			zoom:6,
			center: france,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
		}

      
		var map = new google.maps.Map(document.getElementById("map"), myOptions);
		directionsDisplay.setMap(map);
      
   
		<?php
			
			//Obtener el id
			$id= $_GET["id"];
			//Informacion del partido
			$sql = "SELECT Local, Visita FROM partidos WHERE id='".$id."'";
			$datos=mysqli_query($Con,$sql);
			//Almacenar datos
			$partido=mysqli_fetch_array($datos);
			//Ubicacion local
			$sql="SELECT Latitud, Longitud FROM equipos WHERE Nombre='".$partido['Local']."'";
			$datos=mysqli_query($Con,$sql);
			//Almacenar ubicacion del local
			$ubicacionLocal= mysqli_fetch_array($datos);
			//Ubicacion visita
			$sql="SELECT Latitud, Longitud FROM equipos WHERE Nombre='".$partido['Visita']."'";
			$datos=mysqli_query($Con,$sql);
			//Almacenar ubicacion del visita
			$ubicacionVisita= mysqli_fetch_array($datos);
			
			
			while ($row = mysqli_fetch_array($oRs))
			{
			$Local=str_replace(" ","_",$partido["Local"]);
		?>			
			var <?php echo $local;?>= {lat:<?php echo $row["Latitud"];?>, lng:<?php echo $row["Longitud"];?>};			
			var icon = <?php echo "'equipos/$Local.png'";?>;			
			addMarker(<?php echo $Local ?>,"<?php echo $row["Local"]?>",icon,map);
			
			
		<?php
			}
		?>   
   
		//Traza la linea
		var st=new google.maps.LatLng(<?php echo $ubicacionLocal["Latitud"];?>, <?php echo $ubicacionLocal["Longitud"];?>);    //Origen
		var en=new google.maps.LatLng(<?php echo $ubicacionVisita["Latitud"];?>, <?php echo $ubicacionVisita["Longitud"];?>);    //Destino

				
		var icons = {
			start: new google.maps.MarkerImage(
			'equipos/<?php echo $partido["Local"];?>.png',      //URL Escudo del equipo local
			new google.maps.Size( 48, 48 ),                // (width,height)
			new google.maps.Point( 0, 0 ),               // The origin point (x,y)
			new google.maps.Point( 0, 20 ),               // The anchor point (x,y)
			new google.maps.Size( 50, 50 ),
			new google.maps.Point( 9, 8 )
			),
 
			end: new google.maps.MarkerImage(
			'equipos/<?php echo $partido["Visita"];?>.png',  //URL Escudo del equipo visitante
			new google.maps.Size( 48, 48 ),                // (width,height)
			new google.maps.Point( 0, 0 ),               // The origin point (x,y)
			new google.maps.Point( 0, 20 ),               // The anchor point (x,y)
			new google.maps.Size( 50, 50 ),
			new google.maps.Point( 9, 8 )
			)
		};
   
		var request = {
			origin: st,         //Origen
			destination:en,    //Destino
			travelMode: document.getElementById('Modo').value       
		};

		directionsService.route(request, function(response, status) {
			if (status == google.maps.DirectionsStatus.OK) {
			// Display the distance:
			document.getElementById('distance').innerHTML +=
				(parseFloat(response.routes[0].legs[0].distance.value)/1000).toFixed(2) + " km";
            
			document.getElementById('duration').innerHTML +=
				(fancyTimeFormat(response.routes[0].legs[0].duration.value));
            
			directionsDisplay.setDirections(response);
			var leg = response.routes[0].legs[0];
			makeMarker( leg.start_location, icons.start, '<?php echo $partido["Local"];?> Local');
			makeMarker( leg.end_location, icons.end, '<?php echo $partido["Visita"];?> Visita');        
		}
		});
   
		function makeMarker( position, icon, title ) {
			var marker = new google.maps.Marker({
			position: position,
			map: map,
			icon: icon,
			title: title
			});
		}   
   
		function fancyTimeFormat(time){   
			// Hours, minutes and seconds
			var hrs = ~~(time / 3600);
			var mins = ~~((time % 3600) / 60);
			var secs = time % 60;

			// Output like "1:01" or "4:03:59" or "123:03:59"
			var ret = "";

			if (hrs > 0) {
				ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
			}

			ret += "" + mins + ":" + (secs < 10 ? "0" : "");
			ret += "" + secs;
			return ret;
		}   	

	</script>
</body>
</html>