<?php
/*
 * ARCHIVO: configuracion_hostinger.php
 * PROPÓSITO: Configuración para servidor de producción Hostinger
 * 
 * INSTRUCCIONES PARA USAR EN HOSTINGER:
 * 1. Renombra este archivo a "configuracion.php" antes de subir
 * 2. Reemplaza los valores con los datos de tu hosting
 * 3. Elimina el archivo de configuración local
 */

// ==========================================
// CONFIGURACIÓN DE HOSTINGER
// ==========================================

// PASO 1: Reemplaza estos valores con los datos de tu base de datos en Hostinger
// PASO 1: Reemplaza estos valores con los datos de tu base de datos en Hostinger
$host = 'localhost';  // Normalmente es 'localhost' en Hostinger

// PASO 2: Cambia por el nombre de tu base de datos en Hostinger
$db = 'u177763909_dayloplas';  // Formato típico de Hostinger: u[userid]_nombredb

// PASO 3: Usuario de la base de datos (mismo formato que la DB)
$user = 'u177763909_andrescastelli';  // Formato típico: u[userid]_username

// PASO 4: Contraseña de la base de datos (la que creaste en Hostinger)
$pass = 'WebDaylo2025';  // ¡CAMBIA ESTO!

// Puerto de MySQL (normalmente 3306)
$port = '3306';

// ==========================================
// CONFIGURACIÓN ADICIONAL PARA HOSTINGER
// ==========================================

// URL base del sitio (cambia por tu dominio)
$base_url = 'https://tudominio.com/';  // ¡CAMBIA ESTO!

// Zona horaria
date_default_timezone_set('America/Argentina/Mendoza');

// Configuración de errores para producción
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ==========================================
// NOTAS IMPORTANTES PARA HOSTINGER
// ==========================================
/*
 * ANTES DE SUBIR:
 * 
 * 1. CREAR BASE DE DATOS EN HOSTINGER:
 *    - Ve a tu panel de Hostinger
 *    - Busca "Bases de datos MySQL"
 *    - Crea una nueva base de datos
 *    - Anota el nombre, usuario y contraseña
 * 
 * 2. ACTUALIZAR ESTE ARCHIVO:
 *    - Reemplaza $db con el nombre real de tu base de datos
 *    - Reemplaza $user con el usuario real
 *    - Reemplaza $pass con la contraseña real
 *    - Reemplaza $base_url con tu dominio real
 * 
 * 3. RENOMBRAR ARCHIVO:
 *    - Renombra "configuracion_hostinger.php" a "configuracion.php"
 *    - Elimina el archivo de configuración local
 * 
 * 4. IMPORTAR BASE DE DATOS:
 *    - En Hostinger, ve a phpMyAdmin
 *    - Selecciona tu base de datos
 *    - Importa el archivo "dayloplas_backup.sql"
 */
?>