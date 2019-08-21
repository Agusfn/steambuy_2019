<?php

// Lo mismo que error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);


$p = file_get_contents("https://www.cuentadigital.com/exportacion.php?control=ef67b67798e79b6ebd0250074755b12d"); // Cuenta 1: agusfn
var_dump($_HTTP_RESPONSE_HEADER);

echo $p;

?>