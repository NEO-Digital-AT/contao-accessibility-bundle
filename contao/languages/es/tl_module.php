<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Ajustes para los visitantes';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Botón y panel';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Ajustes ofrecidos', 'Los visitantes solo ven en el panel los ajustes marcados. «Lectura en voz alta» usa solo voces instaladas en el dispositivo del visitante, salvo que más abajo se permitan las voces en línea. «Tamaño del texto» requiere tamaños de letra en rem, em o % en el diseño del sitio web; con tamaños en px solo crece el panel (ver «Ampliar toda la página»).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Texto y contenido',
    'colour' => 'Colores',
    'orientation' => 'Orientación',
    // Settings
    'font' => 'Tamaño del texto',
    'typeface' => 'Fuente legible',
    'dyslexia' => 'Fuente para la dislexia',
    'readable' => 'Más espacio',
    'align' => 'Alinear a la izquierda',
    'headings' => 'Resaltar títulos',
    'links' => 'Resaltar enlaces',
    'noimages' => 'Ocultar imágenes',
    'cursor' => 'Cursor grande',
    'contrast' => 'Alto contraste',
    'lowcontrast' => 'Reducir el contraste',
    'lowsaturation' => 'Atenuar los colores',
    'grayscale' => 'Escala de grises',
    'motion' => 'Detener animaciones',
    'focus' => 'Foco reforzado',
    'guide' => 'Guía de lectura',
    'mask' => 'Máscara de lectura',
    'structure' => 'Estructura de la página',
    'speak' => 'Lectura en voz alta',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Opciones rápidas', 'Botones en la parte superior del panel que activan varios ajustes a la vez. Cada uno solo activa ajustes marcados arriba; un botón sin ninguno de ellos no aparece.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Baja visión',
    'dyslexia' => 'Dislexia',
    'concentration' => 'Concentración',
    'epilepsy' => 'Epilepsia',
    'motor' => 'Motricidad',
    'reader' => 'Lectura en voz alta',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'No aparece en el panel: %s. Ninguno de los ajustes que activan está marcado en «Ajustes ofrecidos»; marque uno para mostrar la opción rápida.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Ampliar toda la página', 'Para sitios web cuyo diseño indica los tamaños de letra en píxeles (px): «Tamaño del texto» amplía entonces toda la página, como el zoom del navegador, en lugar de solo el tamaño base; el panel conserva su tamaño. Desactivado: «Tamaño del texto» amplía el tamaño base, lo que funciona donde el diseño usa rem, em o %.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '«Ampliar toda la página» solo tiene efecto si «Tamaño del texto» está marcada en «Ajustes ofrecidos».';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Permitir las voces en línea del navegador', 'La lectura en voz alta usa entonces también voces en línea del navegador (por ejemplo en Chrome o Microsoft Edge) cuando el dispositivo del visitante no tiene voz para el idioma. Para ello el navegador envía el texto leído a su fabricante; la política de privacidad debe indicarlo. Desactivado: solo voces instaladas en el dispositivo, el texto se queda allí.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '«Permitir las voces en línea del navegador» solo tiene efecto si «Lectura en voz alta» está marcada en «Ajustes ofrecidos».';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Las voces en línea están permitidas: si el dispositivo de un visitante no tiene voz para el idioma, el navegador envía el texto leído a su fabricante. Indíquelo en la política de privacidad (vea la documentación).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Posición del botón', 'Dónde aparece el botón flotante. Sin botón flotante, la etiqueta de inserción {{accessibility::link}} abre el panel. Si en la misma esquina hay botones de otras extensiones (configuración de cookies, chat), se ordenan juntos según el diseño de página: un botón de configuración con menú, apilados o uno al lado del otro.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Abajo a la izquierda',
    'bottom-right' => 'Abajo a la derecha',
    'top-left' => 'Arriba a la izquierda',
    'top-right' => 'Arriba a la derecha',
    'none' => 'Sin botón flotante',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Dónde se abre el panel', 'Junto al botón, como panel lateral en el borde izquierdo o derecho de la pantalla, o en el centro de la pantalla.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Junto al botón',
    'drawer-left' => 'Panel lateral izquierdo',
    'drawer-right' => 'Panel lateral derecho',
    'center' => 'Centro de la pantalla',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Oscurecer el fondo', 'Muestra una capa oscura detrás del panel abierto. El resto de la página solo se puede usar cuando el panel está cerrado; un clic en la capa oscura lo cierra.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Declaración de accesibilidad', 'Página con la declaración de accesibilidad de este sitio web. El panel enlaza con ella. Déjelo vacío si no existe.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Texto para copiar (con enlace):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Panel de accesibilidad: NEO Accessibility de NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Esta extensión es gratuita. Condición: el aviso legal de cada sitio web en el que funcione públicamente la menciona con un enlace a https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'O en HTML para el código fuente del aviso legal:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licencia';
