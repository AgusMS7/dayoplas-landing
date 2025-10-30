<?php
/*
 * ARCHIVO: configuracion.php
 * PROPÓSITO: Configuración central de parámetros de conexión a la base de datos
 * DESCRIPCIÓN: Define las credenciales y parámetros necesarios para establecer
 * la conexión con la base de datos MySQL del sistema.
 * 
 * IMPORTANTE: Este archivo contiene información sensible y debe estar protegido
 * contra acceso no autorizado. En producción, estas credenciales deben ser
 * almacenadas de forma segura (variables de entorno, archivos protegidos, etc.)
 * 
 * USO: Este archivo es incluido por otros scripts PHP que necesitan acceso
 * a la base de datos, especialmente por model_daylo.php
 */

// ==========================================
// CONFIGURACIÓN DE BASE DE DATOS
// ==========================================

// Servidor de base de datos (localhost para desarrollo local con XAMPP)
$host = 'localhost';
$db = 'dayloplas';
$user = 'root';
$pass = '';

// Puerto de conexión MySQL (3306 es el puerto estándar)
$port = '3306';

// ==========================================
// NOTAS DE SEGURIDAD Y MEJORES PRÁCTICAS
// ==========================================
/*
 * RECOMENDACIONES PARA PRODUCCIÓN:
 * 1. Crear un usuario específico para la aplicación (no usar root)
 * 2. Asignar una contraseña segura
 * 3. Otorgar solo los permisos necesarios al usuario
 * 4. Considerar usar variables de entorno para credenciales
 * 5. Implementar SSL/TLS para conexiones a base de datos
 * 6. Restringir acceso a este archivo mediante .htaccess
 */
