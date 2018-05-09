<!DOCTYPE html>
<html>
<head>
	<title>Calendario</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" >
<style type="text/css">
	body {
	  	font-family:Monaco,Georgia,Times,serif;
	  	font-size: 100%;
	}

	h1 {
		text-align: center;
  	}
	       A{text-decoration: none; font-weight: bold; font-style: Normal; color: midnightblue;}
       A:hover{text-decoration: none; font-weight: bold; font-style: Normal; color: 990000; cursor: Hand; background-color: ebf3f6; }
</style>
</head>
<BODY background="fondo3.png" class="img-responsive">
 <center>
    <?php
    $Eq = "";       
    extract($_GET);
    $Equipo = str_replace("_", " ", $Eq);

    $Host = "localhost";
    $Usuario = "root";
    $Clave = "root";
    $DB = "futbol";

    $Con = mysqli_connect($Host, $Usuario, $Clave, $DB);

    if(mysqli_connect_errno()){
      echo "Fallo la conexion: " . mysqli_connect_errno();
     
    }

    if($Equipo == ""){
    	echo "debe seleccionar un equipo";
        exit();
    }
	echo "<img src='logo.png' width=150 height=200><br/><br/>";
    echo "<h1 style=color:white><center>RESULTADOS</center></h1>";

    if($oRs = mysqli_query($Con, "SELECT ID,JORNADA,FECHA,LOCAL,VISITA,GOLESLOCAL,GOLESVISITA FROM partidos WHERE Local = '$Equipo' or Visita = '$Equipo'")){ //contiene el arreglo de oRs
    	echo 	'
      <table class=table border=1 cellspacing=0 cellpadding=0 bordercolor="338AC9" style=width:50> 
      <thead class=thead-dark>
	  <tr>
      <th><center>ID</center></th>
	  <th><center>Jornada</center></th>  
      <th><center>Fecha</center></th>
      <th><center>Local</center></th>
      <th><center>Visita</center></th>
	  <th><center>GolesLocal</center></th>
      <th><center>GolesVisita</center></th> 
	  <th> </th>
	  </thead>	  
      </tr>';

    	while($row = mysqli_fetch_array($oRs)){
    	echo "<tr class=table-light> 
          <td><center>$row[0]</center></td>  
          <td><center>$row[1]</center></td> 
          <td><center>$row[2]</center></td>  
          <td><center>$row[3]</center></td>  
          <td><center>$row[4]</center></td>
          <td><center>$row[5]</center></td>
          <td><center>$row[6]</center></td>
		  <td><center><input type=image SRC='google_maps.png' WIDTH=25 HEIGHT=25></input></center></td>";   
          echo "</tr>";
    	}
    	echo "</table>";

    }
    mysqli_close($Con);
    ?>
	<p><a href=Posiciones.php>Posiciones</a></p>
  </center>
</body>
</html>