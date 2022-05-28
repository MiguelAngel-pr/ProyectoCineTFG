<?php 
session_start(); 
include("funciones.php");
$msg = "";
$funcion = ($_POST['funcion']);
switch ($funcion)
{
    case "consulta_pelicula":consultaPelicula(); break;
    case "obtener_cines":obtenerCines(); break;
    case "obtener_horarios":obtenerHorarios(); break;
    case "obtener_lista_cines":obtenerListaCines(); break;
}

function consultaPelicula()
{
    $mysqli = conectaBBDD();
    $id_pelicula = $_POST['idpelicula'];
    $result = mysqli_query($mysqli,"SELECT * FROM pelicula WHERE id_pelicula = $id_pelicula");
    $numPeliculas = $result -> num_rows;

    if($numPeliculas > 0)
    {
        $msg = mysqli_fetch_row($result);
    }
    else
    {
        $msg = 'no';
    }
    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
}

function obtenerCines()
{
    $mysqli = conectaBBDD();
    $id_pelicula = limpiaPalabra($_POST['idpelicula']);
    $fecha_actual = date("Y-m-d");
    $query = "SELECT * FROM cine c WHERE c.id_cine in (SELECT h.cine FROM horario h WHERE h.pelicula = $id_pelicula AND h.fecha >= '$fecha_actual')";
    $consulta = mysqli_query($mysqli, $query);
    $numCines = $consulta -> num_rows;
    
    if($numCines > 0)
    {
        for ($i = 0; $i < $numCines; $i++)
        {
            $msg[] = mysqli_fetch_assoc($consulta);
        }
    }
    else
    {
        $msg = 'no';
    }
    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
}

function obtenerHorarios()
{
    $mysqli = conectaBBDD();
    $id_pelicula = limpiaPalabra($_POST['idpelicula']);
    $id_cine = limpiaPalabra($_POST['idcine']);
    $fecha_actual = date("Y-m-d");
    $fecha_limite = strtotime('+15 day', strtotime($fecha_actual));
    $fecha_limite = date('Y-m-d', $fecha_limite);
    $query = "SELECT sala, fecha, hora FROM horario h WHERE cine = $id_cine AND pelicula = $id_pelicula AND h.fecha <= '$fecha_limite' AND h.fecha >= '$fecha_actual' ORDER BY fecha asc, hora asc ";
    $consulta = mysqli_query($mysqli, $query);
    $numCines = $consulta -> num_rows;

    if($numCines > 0)
    {
        for ($i = 0; $i < $numCines; $i++)
        {
            $msg[] = mysqli_fetch_assoc($consulta);
        }
    }
    else
    {
        $msg = 'no';
    }
    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
}

function obtenerListaCines()
{
    $mysqli = conectaBBDD();
    $query = "SELECT * FROM cine";
    $consulta = mysqli_query($mysqli, $query);
    $numCines = $consulta -> num_rows;
    
    if($numCines > 0)
    {
        for ($i = 0; $i < $numCines; $i++)
        {
            $msg[] = mysqli_fetch_assoc($consulta);
        }
    }
    else
    {
        $msg = 'no';
    }
    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
}

?>