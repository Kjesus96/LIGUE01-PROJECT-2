<!DOCTYPE HTML>
<html>
  <head>
  <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" >
    <style>
      body {
        margin: 0px;
        padding: 0px;
      }
    </style>
    <style type="text/css">
      canvas { border: 1px solid black; }
    </style>    
    <style>
       A{text-decoration: none; font-weight: bold; font-style: Normal; color: midnightblue;}
       A:hover{text-decoration: none; font-weight: bold; font-style: Normal; color: 990000; cursor: Hand; background-color: ebf3f6; }
    </style>
  </head>
  <BODY background="fondo3.png" class="img-responsive">

<?php  
$Eq = "";
extract($_GET);

if ($Eq == "") { //Falta este parámetro que debe venir en el llamado
   echo "<P>Error: No se está recibiendo el nombre de un equipo por parámetro</P>";    
   exit(); //Termina el programa
}
else {
    $Equipo = str_replace("_", " ", $Eq);
}

$Host    = "localhost";
$Usuario = "root";
$Clave   = "root";
$DB      = "futbol";

$Con = mysqli_connect($Host, $Usuario, $Clave, $DB);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit(); //Termina el programa
}  
  
$CantEquipos = 0;
  
//Determina la cantidad de equipos de la liga
$sql = "SELECT count(*) FROM equipos";   
if ($oRs = mysqli_query ($Con, $sql)) {
    if ($row = mysqli_fetch_array ($oRs)) {
        $CantEquipos = $row[0];   //Tiene la cantidad de equipos de la liga
    }
}


//Determina la cantidad de jornadas para el equipo
//$sql = "SELECT max(Jornada) FROM partidos where Local ='".$Equipo."' or Visita = '".$Equipo."'"; 
$sql = "SELECT max(Jornada) FROM partidos"; 
if ($oRs = mysqli_query ($Con, $sql)) {
    if ($row = mysqli_fetch_array ($oRs)) {
        $CantJornadas = 38;   //Tiene la cantidad de jornadas disputadas por el equipo
    }
}

//Aquí debe recuperar las posiciones del equipo
for ($jor=1; $jor<=$CantJornadas; $jor++) {
   
  //------------------------------------------------------------------------------------------
  //VACIANDO LA TABLA DE POSICIONES
  //$sql="DELETE FROM posiciones";
  //mysqli_query($Con, $sql);

  $ContReg=0;

  //OBTENIENDO INFORMACION DE LOS PARTIDOS DE CADA UNO DE LOS EQUIPOS
  $sql= "SELECT nombre FROM equipos ORDER BY nombre ASC";//sentencia para obtener los equipos desde la tabla equipos

  if($oRs = mysqli_query($Con, $sql)){
    while($row = mysqli_fetch_array($oRs)){
      $Equipos= $row["nombre"];//se obtiene el equipo
      //estas variables se crean aqui para que cada vez que el while avance se inicializen sus valores
      $PJ= 0;//partidos jugados
      $PG= 0;//ganados89
      $PE= 0;//empatados
      $PP= 0;//perdidos
      $GF= 0;//goles a favor
      $GC= 0;//goles en contra
      $PTS= 0;//puntos
      $UJUEGOS="";//ultimos partidos
      
      
      $sql1= "SELECT * FROM partidos WHERE ( Local = '$Equipos' or Visita = '$Equipos' ) and Jornada <=".$jor." ORDER BY Jornada ASC";//sentencia para obtener cada uno de los partidos del equipo

        if($oRs1 = mysqli_query($Con, $sql1)){//Sacar los datos de el equipo como local
          while($row1 = mysqli_fetch_array($oRs1)){
            $PJ++;//se suma un partido
            
            if($Equipos == $row1["Local"]){//si el nombre del equipo es igual a la variable de local, entonces el equipo jugó de local
              $GF+=$row1["GolesLocal"];//se suma los goles a favor
              $GC+=$row1["GolesVisita"];//~~ goles en contra
              
              if($row1["GolesLocal"] > $row1["GolesVisita"]){
                $PG++;
                $UJUEGOS.="G";//el ; va primero ya que luego la cadena sera invertida
              }
              if($row1["GolesLocal"] == $row1["GolesVisita"]){
                $PE++;
                $UJUEGOS.="E";
              }
              if($row1["GolesLocal"] < $row1["GolesVisita"]){
                $PP++;
                $UJUEGOS.="P";
              }
            }
            else{//sino el equipo jugo de visita
              
              $GC+=$row1["GolesLocal"];//se suma los goles en contra
              $GF+=$row1["GolesVisita"];//~~ goles a favor
              
              if($row1["GolesLocal"] < $row1["GolesVisita"]){
                $PG++;
                $UJUEGOS.="G";
              }
              if($row1["GolesLocal"] == $row1["GolesVisita"]){
                $PE++;
                $UJUEGOS.="E";
              }
              if($row1["GolesLocal"] > $row1["GolesVisita"]){
                $PP++;
                $UJUEGOS.="P";
              }
            }
          }
        }

        $PTS = $PG *3 + $PE;//calculo de los puntos
        $GD = $GF - $GC;//Calculo el gol diferencia
        //$UJUEGOS=strrev($UJUEGOS);//invierte el string
        
        //SE INSERTAN LOS DATOS EN LA TABLA DE POSICIONES
        //$sql3="INSERT INTO posiciones VALUES ('$Equipos', '$PJ', '$PG', '$PE', '$PP', '$GF', '$GC', '$GD', '$PTS', '$UJUEGOS')";
        //mysqli_query($Con, $sql3);
        $Teams[$ContReg] = array('Equipo' => $row["nombre"],
                                  'Puntos' => $PTS,
                                  'GDif' => $GD);
        ++$ContReg;
    }

        
        // Obtener una lista de columnas para hacer el ordenamiento
    foreach ($Teams as $clave => $fila) {
        $Club[$clave]   = $fila['Equipo'];
        $Puntos[$clave] = $fila['Puntos'];
        $Dif[$clave]    = $fila['GDif'];    
    }    
    //Se ejecuta el ordenamiento
    array_multisort($Puntos, SORT_DESC, $Dif, SORT_DESC, $Club, SORT_DESC, $Dif, SORT_ASC, $Teams);
    //print_r($Teams);
    //Analiza la posición en esa jornada
    for ($Contador = 0; $Contador < $CantEquipos; $Contador++) {
    
        if ($Teams[$Contador]['Equipo'] == $Equipo) {
    
            //echo $jor." ".$Contador." | ";
            $Posicion[$jor] = $Contador+1; //Llena el vector con las posiciones
            break;
        }
    }

  }

  //-----------------------------------------------------------------------------------
  //Se va a llenar el vector posiciones con la respectiva posicion de ese equipo en conforme pasan las jornadas
  
}//Fin de for
?>
    <center>
    <h1><B style=color:White><?php echo $Equipo; ?></B></h1>
    <p><img src=equipos/<?php echo $Eq; ?>.png width=150></p>
    
    <canvas class="table-dark" id="myCanvas" width="<?php echo $CantJornadas * 20 + 20; ?>" height="<?php echo $CantEquipos * 20 + 20; ?>"></canvas>
    <script>
      var Equipos  = <?php echo $CantEquipos?>;
      var Jornadas = <?php echo $CantJornadas?>;
       
      var canvas = document.getElementById('myCanvas');
      var context = canvas.getContext('2d');

      context.font = "18px serif"; //Selecciono el tipo de letra
	  context.fillStyle = 'gray';
	  context.strokeStyle="#338AC9";
      //Muestra los # de las posiciones
      for (var i=0; i<Equipos; i++) {
           context.fillText(i+1, 1, 18 + 20*i);
      }
      
      context.font = "16px serif"; //Selecciono el tipo de letra
      context.fillStyle = 'gray';
      //Muestra los # de las jornadas
      for (var i=0; i<Jornadas; i++) {
           context.fillText(i+1, 32 + 10*(2*i-1), Equipos * 20 + 18);
      }      
     
      var posJornada = [<?php for ($k=1; $k<$CantJornadas; ++$k) {
              echo $Posicion[$k].",";} echo $Posicion[$k];?>]; //Estos datos se obtienen de la BD
              
      var radius = 5;
      
      //Aquí se colocan los puntos en la cuadrícula
      for (var i=0; i<Jornadas; i++) {
         context.beginPath();
         context.arc(10*(2*i+1) + 20, 10*(2*posJornada[i]-1), radius, 0, 2 * Math.PI, false);
         if (posJornada[i] == 1)
             context.fillStyle = 'Blue';
         else
             if (posJornada[i] == Equipos)
                 context.fillStyle = 'Red';
             else
                 context.fillStyle = 'green';
         
         context.closePath(); 
         context.fill();          
      }

      context.beginPath();
      //Dibuja la cuadrícula
      for (var x=0; x<=Jornadas * 20; x=x+20){
           context.moveTo(x,0);
           context.lineTo(x,Equipos * 20 + 20);
      }   
      
      for (var y=0; y<=Equipos * 20; y=y+20){
           context.moveTo(0,y);
           context.lineTo(Jornadas * 20 + 20,y);
      } 
      context.closePath();

      context.stroke();
    </script>
    <p><a href=Posiciones.php>Posiciones</a></p>
    </center>
  </body>
<?php
mysqli_close($Con);    
?>
</html>