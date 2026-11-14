<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Ustawienia dla odwiedzających';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Przycisk i panel';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Oferowane ustawienia', 'Odwiedzający widzą w panelu tylko zaznaczone ustawienia. „Czytanie na głos” korzysta tylko z głosów zainstalowanych na urządzeniu odwiedzającego, chyba że poniżej zezwolono na głosy online. „Rozmiar tekstu” wymaga rozmiarów czcionek w rem, em lub % w projekcie strony; przy rozmiarach w px powiększa się tylko panel (zob. „Powiększ całą stronę”).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Tekst i treść',
    'colour' => 'Kolory',
    'orientation' => 'Orientacja',
    // Settings
    'font' => 'Rozmiar tekstu',
    'typeface' => 'Czytelna czcionka',
    'dyslexia' => 'Czcionka dla dyslektyków',
    'readable' => 'Większe odstępy',
    'align' => 'Tekst do lewej',
    'headings' => 'Wyróżnienie nagłówków',
    'links' => 'Wyróżnienie linków',
    'noimages' => 'Ukrycie obrazów',
    'cursor' => 'Duży kursor',
    'contrast' => 'Wysoki kontrast',
    'lowcontrast' => 'Mniejszy kontrast',
    'lowsaturation' => 'Słabsze kolory',
    'grayscale' => 'Odcienie szarości',
    'motion' => 'Zatrzymanie animacji',
    'focus' => 'Wyraźny fokus',
    'guide' => 'Linijka do czytania',
    'mask' => 'Maska do czytania',
    'structure' => 'Struktura strony',
    'speak' => 'Czytanie na głos',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Szybki wybór', 'Przyciski u góry panelu, które włączają kilka ustawień naraz. Każdy włącza tylko ustawienia zaznaczone powyżej; przycisk bez żadnego z nich się nie pojawia.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Słabe widzenie',
    'dyslexia' => 'Dysleksja',
    'concentration' => 'Koncentracja',
    'epilepsy' => 'Padaczka',
    'motor' => 'Motoryka',
    'reader' => 'Czytanie na głos',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Nie ma w panelu: %s. Żadne z ustawień, które włączają, nie jest zaznaczone w „Oferowane ustawienia”; należy zaznaczyć jedno z nich, aby szybki wybór się pojawił.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Powiększ całą stronę', 'Dla stron, których projekt podaje rozmiary czcionek w pikselach (px): „Rozmiar tekstu” powiększa wtedy całą stronę, jak zoom przeglądarki, zamiast tylko rozmiaru podstawowego; panel zachowuje swój rozmiar. Wyłączone: „Rozmiar tekstu” powiększa rozmiar podstawowy, co działa tam, gdzie projekt używa rem, em lub %.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '„Powiększ całą stronę” działa tylko wtedy, gdy w „Oferowane ustawienia” zaznaczono „Rozmiar tekstu”.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Zezwól na głosy online przeglądarki', 'Czytanie na głos korzysta wtedy także z głosów online przeglądarki (np. w Chrome lub Microsoft Edge), gdy urządzenie odwiedzającego nie ma głosu dla danego języka. Przeglądarka wysyła w tym celu czytany tekst do swojego producenta; musi to być opisane w polityce prywatności. Wyłączone: tylko głosy zainstalowane na urządzeniu, tekst tam pozostaje.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '„Zezwól na głosy online przeglądarki” działa tylko wtedy, gdy w „Oferowane ustawienia” zaznaczono „Czytanie na głos”.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Głosy online są dozwolone: jeśli urządzenie odwiedzającego nie ma głosu dla danego języka, przeglądarka wysyła czytany tekst do swojego producenta. Należy to opisać w polityce prywatności (zob. dokumentację).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Położenie przycisku', 'Miejsce, w którym pojawia się pływający przycisk. Bez pływającego przycisku panel otwiera znacznik {{accessibility::link}}. Jeśli w tym samym rogu są przyciski innych rozszerzeń (ustawienia plików cookie, czat), są one ułożone razem zgodnie z ustawieniem w układzie strony: jeden przycisk ustawień z menu, jeden nad drugim lub obok siebie.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Na dole po lewej',
    'bottom-right' => 'Na dole po prawej',
    'top-left' => 'Na górze po lewej',
    'top-right' => 'Na górze po prawej',
    'none' => 'Bez pływającego przycisku',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Gdzie otwiera się panel', 'Obok przycisku, jako szuflada przy lewej lub prawej krawędzi ekranu albo na środku ekranu.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Obok przycisku',
    'drawer-left' => 'Szuflada po lewej',
    'drawer-right' => 'Szuflada po prawej',
    'center' => 'Środek ekranu',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Przyciemnienie tła', 'Wyświetla ciemną warstwę za otwartym panelem. Z reszty strony można korzystać dopiero po zamknięciu panelu; kliknięcie ciemnej warstwy go zamyka.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Deklaracja dostępności', 'Strona z deklaracją dostępności tej witryny. Panel zawiera link do niej. Jeśli jej nie ma, proszę pozostawić puste.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Tekst do skopiowania (z linkiem):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Panel dostępności: NEO Accessibility firmy NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'To rozszerzenie jest bezpłatne. Warunek: nota prawna każdej strony, na której działa publicznie, wymienia je z linkiem do https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Albo jako HTML do kodu źródłowego noty prawnej:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licencja';
