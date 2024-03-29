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
    case "obtener_lista_horarios":obtenerListaHorarios(); break;
    case "obtener_peliculas":obtenerPeliculas(); break;
}

function consultaPelicula()//obtiene todos los datos de la película elegida
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

function obtenerCines()//obtiene todos los cines disponibles para la película elegida
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

function obtenerHorarios()//obtiene una lista de los horarios dependiendo de la película elegida
{
    $mysqli = conectaBBDD();
    $id_pelicula = limpiaPalabra($_POST['idpelicula']);
    $id_cine = limpiaPalabra($_POST['idcine']);
    $fecha_actual = date("Y-m-d");
    $fecha_limite = strtotime('+15 day', strtotime($fecha_actual));
    $fecha_limite = date('Y-m-d', $fecha_limite);
    $query = "SELECT sala, fecha, hora FROM horario h WHERE cine = $id_cine AND pelicula = $id_pelicula AND h.fecha <= '$fecha_limite' AND h.fecha >= '$fecha_actual' ORDER BY fecha asc, hora asc";
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

function obtenerListaCines()//obtiene una lista de todos los cines disponibles
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

function obtenerListaHorarios()//obtiene una lista de los horarios o las fechas para colocarla en el selector
{
    $mysqli = conectaBBDD();
    $id_cine = limpiaPalabra($_POST['idcine']);
    $fecha = limpiaPalabra($_POST['fecha']);
    $filtro = "";
    if($fecha != "" && $fecha != "-")
    {
        $filtro = "AND fecha = '$fecha' ";
    }
    
    $query = "SELECT DISTINCT fecha, hora FROM horario WHERE cine = '$id_cine' ".$filtro." GROUP BY fecha ORDER BY fecha asc, hora asc ";
    $consulta = mysqli_query($mysqli, $query);
    $numHorarios = $consulta -> num_rows;
    
    if($numHorarios > 0)
    {
        for ($i = 0; $i < $numHorarios; $i++)
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

function obtenerPeliculas()//obtiene una lista de películas con un filtro
{
    $mysqli = conectaBBDD();
    $id_cine = limpiaPalabra($_POST['idcine']);
    $fecha = limpiaPalabra($_POST['fecha']);
    $hora = limpiaPalabra($_POST['hora']);
    $estrenos = limpiaPalabra($_POST['estrenos']);
    $busqueda = limpiaPalabra($_POST['busqueda']);
    $filtro = "";

    if($estrenos != "" && $estrenos != null)
    {
        $filtro = $filtro."AND p.estado_estreno = '2' ";
    }
    if($busqueda != "" && $busqueda != null)
    {
        $filtro = $filtro."AND p.direccion LIKE '%$busqueda%' OR p.titulo LIKE '%$busqueda%' OR p.titulo_original LIKE '%$busqueda%' ";
    }
    if($id_cine != "" && $id_cine != "-")
    {
        $filtro = $filtro."AND h.cine = $id_cine ";
        if($fecha != "" && $fecha != "-")
        {
            $filtro = $filtro."AND h.fecha = '$fecha' ";
            if($hora != "" && $hora != "-")
            {
                $filtro = $filtro."AND h.hora = '$hora' ";
            }
        }
    }
    $query = "SELECT DISTINCT p.id_pelicula, p.titulo FROM pelicula p INNER JOIN horario h ON p.id_pelicula = h.pelicula WHERE 1=1 ".$filtro." ORDER BY p.estreno desc";
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