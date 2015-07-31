<?php
/*
 * Filename: index.php
 * Description: create forms and SQL statements automatically
 * @author Hector Mantellini (@xombra) - http://xombra.com
 * @contributor Angel Cruz (@abr4xas) - http://abr4xas.org
 * vie 31 jul 2015 02:00:05 VET
 * License: GPLv2 or later
 * @version 0.1
 */

include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Crea formulario</title>
        <link rel="shortcut icon" href="">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <style>body{padding-top:10px;}.starter-template{padding:40px 15px;}</style>

        <!--[if IE]>
<script src="https://cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
<![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="starter-template">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Creación de Formularios Dinámicos</h3>
                    </div>
                    <div class="panel-body">
                        <?php
if (empty($_POST['hacer'])) { ?>
                        <p>
                            Indique la TABLA MySql a la cual se le creará el formulario <br />
                            <?php }else{
    echo '<p>Creado formulario para la tabla [ <code>'.$_POST["nombre_tabla"].'</code> ]';
}
                            ?>
                        </p>
                        <?php
if (empty($_POST['hacer'])) {
    echo'
    <form action="'.$_SERVER['PHP_SELF'].'" method="post" name="tablas" target="_self" >';
    $sql = sprintf("SHOW TABLES");
    $query = @mysql_query($sql,$conexion);
    echo '
      <div class="form-group">
        <label for="'.$parametro[3].'">Tablas que conforman a Base de Datos: '.$parametro[3].'</label>
      </div>';
    while ($tabla = @mysql_fetch_array($query)) {
        echo '
    <div class="radio">
      <label>
        <input type="radio" name="nombre_tabla" id="optionsRadios1" value="'.$tabla[0].'"> '.$tabla[0].'
      </label>
    </div>
    ';
    }
    echo '
      <button type="submit" name="submit" class="btn btn-primary btn-sm btn-block">Submit</button>
      <input type="hidden" name="hacer" value="true">
    </form>';
}
                        ?>
                        <?php
// vemos los campos de la tabla elegida
$tabla = $_POST["nombre_tabla"];
if (empty($tabla)) {
    die("<br/><div class=\"alert alert-danger\" role=\"alert\"><strong>Error!!!</strong> <p>No eligío una tabla</p></div>"); }
$archivo = basename($_SERVER["PHP_SELF"]);
$sql = sprintf("SHOW FIELDS FROM `%s`", mysql_real_escape_string($tabla));
$query = @mysql_query($sql,$conexion);

// determinamos la cantidad de campos de la tabla

$sql = "select * from $tabla";
$query2 = @mysql_query($sql,$conexion);
$cant_campo = @mysql_num_fields($query2);
echo 'La tabla [<code>'.$tabla.'</code>] esta conformada por '.$cant_campo.' campos</p>';

// determinamos los campos autoincrementales para que no salgan en el formulario
$value = '$'."sql = sprintf(\"INSERT INTO $tabla (";
for ($ciclo=0; $ciclo < $cant_campo; $ciclo++) {
    $value .=  mysql_field_name($query2, $ciclo).', ';
    if (substr_count(mysql_field_flags($query2, $ciclo),"auto_increment"))
    { $campo_auto = mysql_field_name($query2, $ciclo);   }
}
$value = substr($value,0,strlen($value)-2).") \r\n\t VALUES ( ";

// hacemos el formulario

$formulario = "<form action=\"$archivo\" method=\"post\" name=\"$tabla\">\r";
$mysql_r = '';
while ($campo = @mysql_fetch_array($query)) {
    $nombre_campo = ucfirst(strtolower($campo[0]));
    if ($campo[0] != $campo_auto) {
        $formulario.= '
            <div class="form-group">
                <label for="'.strtolower($nombre_campo).'">'.$nombre_campo.'</label>
                <input type="text" class="form-control" name="'.strtolower($nombre_campo).'" id="'.strtolower($nombre_campo).'" placeholder="'.$nombre_campo.'">
            </div>';
        $value .= "'%s', ";
        $mysql_r .= " \t mysql_real_escape_string("."$".$campo[0]."), \r\n" ;	}
    else { $value .= "NULL, ";  }
}
$formulario.='
        <button type="submit" class="btn btn-default">Submit</button>
    ';
$formulario.='</form>';
echo '<form name="Copiar">
    <div class="form-group">
      <button class="btn btn-default" type="button" onClick="javascript:this.form.valor.focus();this.form.valor.select();">Seleccionar  Todo </button></div>';
echo '<div class="form-group">
            <textarea class="form-control" name="valor" rows="10" readonly>'.$formulario.'</textarea>
          </div>';
echo '<hr>
      <div class="form-group">
        <label for="sql">Sentencia SQL del formulario</label>
      </div>
      <div class="form-group">
            <button class="btn btn-default" type="button" onClick="javascript:this.form.valor.focus();this.form.insert.select();">Seleccionar  Todo </button>
      </div>';

$value  = substr($value,0,strlen($value)-2).")\",\r\n";
$value .= substr($mysql_r,0,strlen($mysql_r)-4).");";

echo '<div class="form-group">
    <textarea class="form-control" name="insert" cols="75" rows="10" readonly>'.$value.'</textarea>
    </div>';
$_POST["hacer"] = '';
print "</form>";


                        ?>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script type="text/javascript">

/*
                By Mike Hall (MHall75819@aol.com)
                */

var NS4 = (document.layers);    // Which browser?
var IE4 = (document.all);
var win = window;    // window to search.
var n   = 0;
function findInPage(str) {
    var txt, i, found;
    if (str == "")
        return false;
    // Find next occurance of the given string on the page, wrap around to the
    // start of the page if necessary.
    if (NS4) {
        // Look for match starting at the current point. If not found, rewind
        // back to the first match.
        if (!win.find(str))
            while(win.find(str, false, true))
                n++;
        else   n++;

        // If not found in either direction, give message.
        if (n == 0)
            alert("Not found.");
    }
    if (IE4) {
        txt = win.document.body.createTextRange();
        // Find the nth match from the top of the page.
        for (i = 0; i <= n && (found = txt.findText(str)) != false; i++) {
            txt.moveStart("character", 1);
            txt.moveEnd("textedit");
        }
        // If found, mark it and scroll it into view.
        if (found) {
            txt.moveStart("character", -1);
            txt.findText(str);
            txt.select();
            txt.scrollIntoView();
            n++;
        }
        // Otherwise, start over at the top of the page and find first match.
        else {
            if (n > 0) {
                n = 0;
                findInPage(str);
            }
            // Not found anywhere, give message.
            else
                alert("Not found.");
        }
    }
    return false;
}
        </script>
    </body>
</html>
<?php @mysql_close($conexion); ?>
