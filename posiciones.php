<HTML>
<HEAD><TITLE>Posiciones Ligue1</TITLE><meta http-equiv="Content_Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" >
</HEAD>
<BODY background="fondo3.png" class="img-responsive">
<?php

$CantidadJuegos=14;
$Torneo="Ligue1";

extract($_GET);

$Orden = "";
$Host    = "localhost";
$Usuario = "root";
$Clave   = "root";
$DB      = "futbol";



$Con = mysqli_connect($Host, $Usuario, $Clave, $DB);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
if($Torneo == "Ligue1")
{
$PJ = 0;
$PG = 0;
$PE = 0;
$PP = 0;
$GF = 0;
$GC = 0;
$PT = 0;
$Contador = 0;
$ListaEquipos = 0;
$UJuego = "";
$MaxJornada = 0;
$sql = "SELECT max(Jornada) FROM partidos"; 
if ($oRs = mysqli_query ($Con, $sql)) {
    if ($row = mysqli_fetch_array ($oRs)) {
        $MaxJornada = $row[0];   //Tiene la cantidad de jornadas disputadas por el equipo
    }
}

// Aquí deben hacer un ciclo leyendo los equipos de la tabla "equipos"
mysqli_query($Con,"TRUNCATE TABLE posiciones");
$ListaEquipos = mysqli_query($Con,"SELECT nombre FROM equipos");
while($row = mysqli_fetch_array($ListaEquipos)){
// ------------------------------ inicio del ciclo --------------
  $Equipo = $row["nombre"]; //El equipo se toma de la tabla equipos

  $sql2 = "SELECT * FROM partidos where Local ='".$Equipo."' or Visita = '".$Equipo."' order by Jornada desc";
  if ($oR2s = mysqli_query ($Con, $sql2)) {

  //El query funcionó bien
      while ($row2 = mysqli_fetch_array ($oR2s)) {          
         $PJ++;
         //Partidos empatados
         if (($row2["GolesLocal"] == $row2["GolesVisita"]) && ($Equipo == $row2["Local"])) {
             $PE++;
             $GF = $GF + $row2["GolesLocal"];
             $GC = $GC + $row2["GolesVisita"];
             $UJuego = $UJuego . "1";
             
         }
          
        if (($row2["GolesLocal"] == $row2["GolesVisita"]) && ($Equipo == $row2["Visita"])) {
             $PE++;
             $GF = $GF + $row2["GolesVisita"];
             $GC = $GC + $row2["GolesLocal"];
             $UJuego = $UJuego . "1";
   
         }

         //Partidos ganados
         if (($row2["GolesLocal"] > $row2["GolesVisita"]) && ($Equipo == $row2["Local"]))
         {
            $PG++;
            $GF = $GF + $row2["GolesLocal"];
            $GC = $GC + $row2["GolesVisita"];
            $UJuego = $UJuego . "3";
         }

         if (($row2["GolesLocal"] < $row2["GolesVisita"]) && ($Equipo == $row2["Visita"]))
         {
            $PG++;
            $GF = $GF + $row2["GolesVisita"];
            $GC = $GC + $row2["GolesLocal"];
            $UJuego = $UJuego . "3";
         }
         
         //Partidos perdidos
         if (($row2["GolesLocal"] < $row2["GolesVisita"]) && ($Equipo == $row2["Local"]))
         {
            $PP++;
            $GF = $GF + $row2["GolesLocal"];
            $GC = $GC + $row2["GolesVisita"];
            $UJuego = $UJuego . "0";
         }

         if (($row2["GolesLocal"] > $row2["GolesVisita"]) && ($Equipo == $row2["Visita"]))
         {
            $PP++;
            $GF = $GF + $row2["GolesVisita"];
            $GC = $GC + $row2["GolesLocal"];
            $UJuego = $UJuego . "0";
         }

          $Contador++;
      } 
    }//fin del if
    
$PT = $PG * 3 + $PE; //Calculo los puntos
$GDif = $GF - $GC;     //Calculo el gol diferencia

// Aquí deben hacer el insert en la tabla "posiciones"
mysqli_query($Con, "INSERT INTO posiciones VALUES ('$Equipo','$PJ','$PG','$PE','$PP','$GF','$GC','$GDif','$PT','$UJuego')");

$PJ=0;$PG=0;$PE=0;$PP=0;$GF=0;$GC=0;$PT=0;$GDif=0;$UJuego="";$Contador=0;    
}//fin del while
// ------------------------------ fin del ciclo --------------

// Aquí se construye la página que muestra la tabla de posiciones
$posicionesTabla = mysqli_query($Con, "SELECT * FROM posiciones order by Puntos desc, Dif desc");  

$a;
$sql = "SELECT max(Jornada) FROM partidos"; 
if ($oRs = mysqli_query ($Con, $sql)) {
    if ($row = mysqli_fetch_array ($oRs)) {
        $a = $row[0];   //Tiene la cantidad de jornadas disputadas por el equipo
    }
}

for($i = 0; $i < 10; $i++){
    $a--;
}

//se despliega el resultado  
echo "<center>
	  <form action='mapa.php'>
      <input type=image src='logo.png' width=150 height=200><br/><br/>
	  </form>
      <table border=1 cellspacing=0 cellpadding=2 class=table bordercolor=338AC9 style=width:50,height:75% > 
	  <thead class=thead-dark>
      <tr>
      <th>   </th>
      <th><center>Pos</center></th>
      <th><center>Equipo</center></th>
      <th><center>PJ</center></th>  
      <th><center>PG</center></th>  
      <th><center>PE</center></th>  
      <th><center>PP</center></th>  
      <th><center>GF</center></th>  
      <th><center>GC</center></th>  
      <th><center>Dif</center></th>  
      <th><center>Pts</center></th> 
      <th style=width:25%><center><A HREF=posiciones.php?CantidadJuegos=".($CantidadJuegos+1)."&Torneo=Ligue1><IMG SRC='izquierda.png' WIDTH=20 HEIGHT=20></A>Últimos ".($MaxJornada-$CantidadJuegos)." juegos<A HREF=posiciones.php?CantidadJuegos=".($CantidadJuegos-1)."&Torneo=Ligue1><IMG SRC='derecha.png' WIDTH=20 HEIGHT=20></center></A></th>
	  </thead>
      </tr>";  
$Contador = 0;
while ($row = mysqli_fetch_row($posicionesTabla)){ 
    $Contador++;  
    $row2[0] = str_replace("_", " ", $row[0]); 
    echo "<tr class=table-light>  
          <td><center><img src='equipos/$row[0].png' width=32 height=32></center></td>"; 
		  $row[0] = str_replace(" ", "_", $row[0]);
          echo "
          <td><center><A HREF=Grafico.php?Eq=".($row[0])." style=color:#000000>$Contador</A></center></td>";
          //$row[0] = str_replace("_", " ", $row[0]);
          $row[0] = str_replace(" ", "_", $row[0]);
          echo "
          <td><center><A HREF=VerEquipos.php?Eq=".($row[0])." style=color:#000000>$row2[0]</A></center></td>";
          $row[0]= str_replace("_"," ", $row[0]);	
          echo "
          <td><center>$row[1]</center></td>  
          <td><center>$row[2]</center></td> 
          <td><center>$row[3]</center></td>  
          <td><center>$row[4]</center></td>  
          <td><center>$row[5]</center></td>
          <td><center>$row[6]</center></td>  
          <td><center>$row[7]</center></td>  
          <td><center>$row[8]</center></td>"; 
          echo "<td>";
          $vec = $row[9];
           $Jor = 0;
          for($i=0;$i<strlen($vec)+1;$i++){ 
              if($i > $CantidadJuegos){
                $t = $MaxJornada - $i;
                $Fe = mysqli_query($Con, "SELECT Fecha, Jornada, GolesLocal, GolesVisita FROM partidos WHERE (Local ='$row[0]' OR Visita='$row[0]') AND Jornada='$Jor' Order by Jornada ASC");
                $row4 = $Fe->fetch_assoc();
                if(substr($vec,$t,1) == "0")
                {                    
                    echo "<img src='status/perdio.png' WIDTH=20 HEIGHT=20 title=Fecha&nbsp;".$row4['Fecha']."&nbsp;Jornada:&nbsp;".$row4['Jornada']."&#13;Marcador:&nbsp;".$row4['GolesLocal']."&nbsp;-&nbsp;".$row4['GolesVisita'].">";  
                } 
                else if(substr($vec,$t,1) == "1")
                {
                    echo "<img src='status/empate.png' WIDTH=20 HEIGHT=20 title=Fecha&nbsp;".$row4['Fecha']."&nbsp;Jornada:&nbsp;".$row4['Jornada']."&#13;Marcador:&nbsp;".$row4['GolesLocal']."&nbsp;-&nbsp;".$row4['GolesVisita'].">";  
                } 
                else if(substr($vec,$t,1) == "3")
                {
                    echo "<img src='status/gano.png' WIDTH=20 HEIGHT=20 title=Fecha&nbsp;".$row4['Fecha']."&nbsp;Jornada:&nbsp;".$row4['Jornada']."&#13;Marcador:&nbsp;".$row4['GolesLocal']."&nbsp;-&nbsp;".$row4['GolesVisita'].">"; 
                }
              }
              $Jor++;              
           }
           "</td>";    
          echo "</tr>";  
}  
echo "</table></center>"; 

mysqli_close($Con);
}
else
{
  echo "Torneo invalido
  <A HREF=posiciones.php?Torneo=Ligue1>Torneo Ligue1</A>";
}
?>
</BODY>
</HTML>