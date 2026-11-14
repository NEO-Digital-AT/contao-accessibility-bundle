<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Réglages pour les visiteurs';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Bouton et panneau';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Réglages proposés', 'Les visiteurs ne voient dans le panneau que les réglages cochés. « Lecture à voix haute » utilise uniquement les voix installées sur l’appareil du visiteur, sauf si les voix en ligne sont autorisées ci-dessous. « Taille du texte » suppose des tailles de police en rem, em ou % dans le design du site ; avec des tailles en px, seul le panneau grandit (voir « Agrandir toute la page »).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Texte et contenu',
    'colour' => 'Couleurs',
    'orientation' => 'Orientation',
    // Settings
    'font' => 'Taille du texte',
    'typeface' => 'Police lisible',
    'dyslexia' => 'Police pour la dyslexie',
    'readable' => 'Plus d’espacement',
    'align' => 'Aligner le texte à gauche',
    'headings' => 'Mettre en évidence les titres',
    'links' => 'Mettre en évidence les liens',
    'noimages' => 'Masquer les images',
    'cursor' => 'Grand curseur',
    'contrast' => 'Contraste élevé',
    'lowcontrast' => 'Réduire le contraste',
    'lowsaturation' => 'Atténuer les couleurs',
    'grayscale' => 'Niveaux de gris',
    'motion' => 'Arrêter les animations',
    'focus' => 'Focus renforcé',
    'guide' => 'Guide de lecture',
    'mask' => 'Masque de lecture',
    'structure' => 'Structure de la page',
    'speak' => 'Lecture à voix haute',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Options rapides', 'Boutons en haut du panneau qui activent plusieurs réglages à la fois. Chacun n’active que des réglages cochés ci-dessus ; un bouton sans aucun d’eux n’apparaît pas.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Malvoyance',
    'dyslexia' => 'Dyslexie',
    'concentration' => 'Concentration',
    'epilepsy' => 'Épilepsie',
    'motor' => 'Motricité',
    'reader' => 'Lecture à voix haute',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s : %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Absent du panneau : %s. Aucun des réglages qu’elles activent n’est coché sous « Réglages proposés » ; cochez-en un pour afficher l’option rapide.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Agrandir toute la page', 'Pour les sites dont le design indique les tailles de police en pixels (px) : « Taille du texte » agrandit alors toute la page, comme le zoom du navigateur, au lieu de la seule taille de base ; le panneau garde sa taille. Désactivé : « Taille du texte » agrandit la taille de base, ce qui fonctionne là où le design utilise rem, em ou %.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '« Agrandir toute la page » n’a d’effet que si « Taille du texte » est coché sous « Réglages proposés ».';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Autoriser les voix en ligne du navigateur', 'La lecture à voix haute utilise alors aussi les voix en ligne du navigateur (par exemple dans Chrome ou Microsoft Edge) lorsque l’appareil du visiteur n’a pas de voix pour la langue. Le navigateur envoie alors le texte lu à son éditeur ; la politique de confidentialité doit le mentionner. Désactivé : uniquement les voix installées sur l’appareil, le texte y reste.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '« Autoriser les voix en ligne du navigateur » n’a d’effet que si « Lecture à voix haute » est coché sous « Réglages proposés ».';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Les voix en ligne sont autorisées : si l’appareil d’un visiteur n’a pas de voix pour la langue, le navigateur envoie le texte lu à son éditeur. Mentionnez-le dans la politique de confidentialité (voir la documentation).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Position du bouton', 'Endroit où le bouton flottant apparaît. Sans bouton flottant, la balise d’insertion {{accessibility::link}} ouvre le panneau. Si des boutons d’autres extensions (paramètres des cookies, chat) se trouvent dans le même coin, ils sont disposés ensemble comme réglé dans la mise en page : un bouton de paramètres avec un menu, empilés ou côte à côte.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'En bas à gauche',
    'bottom-right' => 'En bas à droite',
    'top-left' => 'En haut à gauche',
    'top-right' => 'En haut à droite',
    'none' => 'Pas de bouton flottant',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Où le panneau s’ouvre', 'À côté du bouton, comme volet sur le bord gauche ou droit de l’écran, ou au centre de l’écran.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'À côté du bouton',
    'drawer-left' => 'Volet à gauche',
    'drawer-right' => 'Volet à droite',
    'center' => 'Centre de l’écran',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Assombrir l’arrière-plan', 'Affiche un fond sombre derrière le panneau ouvert. Le reste de la page n’est utilisable qu’après la fermeture du panneau ; un clic sur le fond sombre le ferme.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Déclaration d’accessibilité', 'Page contenant la déclaration d’accessibilité de ce site. Le panneau renvoie vers elle. Laissez vide s’il n’y en a pas.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Texte à copier (avec lien) :';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Panneau d’accessibilité : NEO Accessibility de NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Cette extension est gratuite. Condition : les mentions légales de chaque site sur lequel elle fonctionne publiquement la citent avec un lien vers https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Ou en HTML pour le code source des mentions légales :';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licence';
