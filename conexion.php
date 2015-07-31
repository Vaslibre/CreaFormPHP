<?php


// sanita de valores

function elimina_slash($valor) {
   if (get_magic_quotes_gpc()) {
      $valor= stripslashes($valor);  }
 return $valor;
}

function filter($string) {
  $replace = "%20";
  $search = array(">", "<", "|", ";", "-","'","&");
  $result = str_replace($search, $replace, $string);
 return $result;
}

// fin sanita

$matriz_ini = parse_ini_file("config.ini");
$indice = 0;
while (list($key, $val) = each($matriz_ini)) {
    $parametro[$indice] = $val;
    $indice++;
}

$conexion = @mysql_connect($parametro[0],$parametro[1], $parametro[2])
            or die("Error en conexión a Servidor de Base de datos");

@mysql_select_db($parametro[3], $conexion)
or die("Error en conexión a Base de datos");

$_SERVER["QUERY_STRING"] = filter(elimina_slash(trim(html_entity_decode($_SERVER["QUERY_STRING"],ENT_QUOTES))));

?>
