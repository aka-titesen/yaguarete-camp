<?php
// Script para generar el hash de una contraseña
$pass = 'INGRESA_TU_PASS'; // Cambia aquí por la contraseña que quieras
$hash = password_hash($pass, PASSWORD_DEFAULT);
echo "Hash para '$pass':\n$hash\n";

/*
Creación de hash para contraseñas de tipo Administrador, paso a paso:

1. Abre la temrinal de Windows (PowerShell)
2. Navega a la carpeta de tu proyecto;
    cd C:\xampp\htdocs\tu_proyecto
3. Ejecuta el script usando la ruta completa al ejecutable de PHP de XAMPP:
    C:\xampp\php\php.exe gen_hash.php
(Asegúrate de que el archivo gen_hash.php está en esa carpeta).

Esto te mostrará el hash para la contraseña ingresada.
Copia el hash generado y pégalo en la base de datos en la columna 'pass' del usuario correspondiente.

*/