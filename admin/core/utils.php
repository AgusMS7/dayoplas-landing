<?php
/*
 * ARCHIVO: utils.php
 * PROPÓSITO: Funciones de utilidad comunes para el panel administrativo
 * DESCRIPCIÓN: Contiene funciones auxiliares reutilizables que simplifican
 * tareas comunes como escape de HTML y detección de páginas activas.
 * 
 * Estas funciones son usadas frecuentemente en las vistas del panel
 * administrativo para mejorar la seguridad y la experiencia de usuario.
 */

/**
 * FUNCIÓN: e() - Escape de HTML
 * 
 * PROPÓSITO: Escapa caracteres especiales de HTML para prevenir ataques XSS
 * (Cross-Site Scripting) y asegurar que el contenido se muestre correctamente.
 * 
 * PARÁMETROS:
 * @param string $s - Cadena de texto a escapar
 * 
 * RETORNA:
 * @return string - Cadena de texto con caracteres especiales escapados
 * 
 * CARACTERÍSTICAS:
 * - Convierte caracteres como <, >, &, ', " a sus entidades HTML equivalentes
 * - Usa codificación UTF-8 para soporte de caracteres especiales
 * - Convierte tanto comillas simples como dobles (ENT_QUOTES)
 * 
 * EJEMPLOS DE USO:
 * echo e($usuario['nombre']); // Evita XSS si el nombre contiene HTML
 * <input value="<?= e($dato_form) ?>"> // Seguro en atributos HTML
 */
function e(string $s): string { 
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); 
}

/**
 * FUNCIÓN: active_class() - Detector de página activa
 * 
 * PROPÓSITO: Determina si una URL específica está actualmente activa
 * para aplicar estilos CSS que marquen visualmente la página actual
 * en menús de navegación.
 * 
 * PARÁMETROS:
 * @param string $needle - Fragmento de URL a buscar en la URI actual
 * 
 * RETORNA:
 * @return string - 'is-active' si la URL coincide, cadena vacía si no coincide
 * 
 * FUNCIONAMIENTO:
 * - Compara la URI actual ($_SERVER['REQUEST_URI']) con el fragmento proporcionado
 * - Usa str_contains() para verificar si el fragmento está presente en la URI
 * - Retorna la clase CSS 'is-active' para elementos del menú activo
 * 
 * EJEMPLOS DE USO:
 * <a class="menu-item <?= active_class('/usuarios/') ?>">Usuarios</a>
 * <li class="nav-item <?= active_class('/cursos/') ?>">Cursos</li>
 * 
 * CASOS DE USO TÍPICOS:
 * - Resaltar el elemento de menú correspondiente a la página actual
 * - Aplicar estilos diferentes a la sección activa del panel admin
 * - Mejorar la navegación y orientación del usuario
 */
function active_class(string $needle): string {
    return str_contains($_SERVER['REQUEST_URI'] ?? '', $needle) ? 'is-active' : '';
}

/*
 * NOTAS IMPORTANTES:
 * 
 * 1. SEGURIDAD:
 *    - La función e() es CRÍTICA para prevenir ataques XSS
 *    - SIEMPRE usar e() al mostrar datos de usuario o base de datos en HTML
 *    - No confiar nunca en datos externos sin escapar
 * 
 * 2. RENDIMIENTO:
 *    - Ambas funciones son muy ligeras y optimizadas
 *    - Se pueden llamar frecuentemente sin impacto significativo
 * 
 * 3. COMPATIBILIDAD:
 *    - Requiere PHP 8.0+ por el uso de str_contains()
 *    - Para versiones anteriores usar strpos() !== false
 * 
 * 4. MEJORAS FUTURAS SUGERIDAS:
 *    - Agregar función para formateo de fechas
 *    - Incluir helper para generar URLs del panel
 *    - Añadir función para truncar texto con ...
 */
?>
