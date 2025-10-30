# 🚀 INSTRUCCIONES PARA SUBIR A HOSTINGER

## 📁 Archivos preparados para Hostinger

Esta carpeta contiene todos los archivos necesarios para que tu sitio web funcione correctamente en Hostinger.

## ⚙️ CONFIGURACIÓN OBLIGATORIA ANTES DE SUBIR

### 1. 🗄️ Configurar Base de Datos

**IMPORTANTE**: Debes modificar el archivo `conexion.php` con los datos de tu base de datos de Hostinger:

```php
// Cambiar estas líneas en conexion.php:
$host = 'TU_HOST_DE_HOSTINGER';        // Ejemplo: 'srv1234.hstgr.io'
$db = 'TU_NOMBRE_DE_BASE_DE_DATOS';    // Ejemplo: 'u123456789_dayloplas'  
$user = 'TU_USUARIO_DB';               // Ejemplo: 'u123456789_admin'
$pass = 'TU_CONTRASEÑA_DB';            // Tu contraseña de base de datos
$port = '3306';                        // Mantener 3306
```

### 2. 📊 Base de Datos

✅ **Base de datos ya configurada en Hostinger** (no es necesario importar nada)

### 3. 📂 Estructura de carpetas en Hostinger

Sube TODO el contenido de esta carpeta a:
- `public_html/` (para dominio principal)
- O `public_html/subdirectorio/` (para subdirectorio)

## ✅ ARCHIVOS INCLUIDOS

### 🔧 Archivos PHP principales:
- `index.php` ✅ (Página principal con carousel funcional)
- `detalle.php` ✅ (Páginas de detalle de cursos)
- `model_daylo.php` ✅ (Modelo de datos optimizado)
- `conexion.php` ⚠️ (CONFIGURAR con datos de Hostinger)

### 🎨 Recursos estáticos:
- `assets/` ✅ (CSS, JS, fuentes)
- `images/` ✅ (Todas las imágenes)
- `admin/` ✅ (Panel administrativo)

### 🗄️ Base de datos:
- ✅ **Ya configurada en Hostinger**

## 🔧 CAMBIOS REALIZADOS

### ✅ Carousel optimizado:
- Muestra todos los 18 cursos
- Navegación por scroll horizontal
- Botones "Ver más" funcionando
- Compatible con móviles

### ✅ Base de datos optimizada:
- Consultas LEFT JOIN para mostrar todos los cursos
- COALESCE para manejar traducciones faltantes
- Mejor rendimiento general

## 🚨 VERIFICACIÓN POST-SUBIDA

Después de subir todo, verifica:

1. **CSS cargando**: `tudominio.com/assets/css/main.css`
2. **JS cargando**: `tudominio.com/assets/js/main.js`  
3. **Imágenes**: `tudominio.com/images/logos/logo_dayloplas.png`
4. **Página principal**: El carousel debe mostrar todos los cursos
5. **Enlaces "Ver más"**: Deben abrir páginas de detalle

## 📞 SOPORTE

Si tienes problemas:
1. Verifica la configuración de `conexion.php`
2. Asegúrate de que la base de datos esté importada
3. Comprueba que todas las carpetas se subieron correctamente

---
🎓 **¡Tu sitio web de Dayloplas-IPM está listo para Hostinger!**