<?php

// Test pages for tests/e2e and tools/docs-images: /a11y-test (German) and
// /en/a11y-test (English) with landmarks, headings, justified and centred
// text, images with and without alt text, a video that plays by itself, a CSS
// animation, a fixed box, a form and a table, plus the media files in
// files/a11y-test/. Needs a root page per language ("de", "en") whose page
// layout holds the panel module. Run again to replace the content.
//
//   php tests/e2e/seed-test-pages.php /path/to/contao-project

declare(strict_types=1);

use Contao\ManagerBundle\HttpKernel\ContaoKernel;

$project = rtrim($argv[1] ?? '', '/');

if ('' === $project || !is_file($project.'/vendor/autoload.php')) {
    fwrite(STDERR, "Usage: php seed-test-pages.php <contao-project-dir>\n");
    exit(1);
}

require $project.'/vendor/autoload.php';

$kernel = ContaoKernel::fromInput($project, new Symfony\Component\Console\Input\ArgvInput([]));
$kernel->boot();
$db = $kernel->getContainer()->get('database_connection');
$t = time();

// Media: two pictures (GD) and a short video (ffmpeg, if installed).
$media = $project.'/files/a11y-test';

if (!is_dir($media)) {
    mkdir($media, 0775, true);
}

foreach (['landscape.png' => [40, 110, 170], 'pattern.png' => [230, 200, 60]] as $name => [$r, $g, $b]) {
    $image = imagecreatetruecolor(640, 360);
    imagefill($image, 0, 0, imagecolorallocate($image, $r, $g, $b));
    $white = imagecolorallocate($image, 255, 255, 255);
    imagesetthickness($image, 6);

    for ($x = 0; $x < 640; $x += 40) {
        imageline($image, $x, 0, $x + 200, 360, $white);
    }

    imagefilledellipse($image, 320, 180, 120, 120, $white);
    imagepng($image, $media.'/'.$name);
}

if (!is_file($media.'/loop.webm')) {
    exec('ffmpeg -loglevel error -y -f lavfi -i testsrc=size=320x180:rate=15 -t 4 -c:v libvpx-vp9 -b:v 200k -an '.escapeshellarg($media.'/loop.webm'), $output, $code);

    if (0 !== $code) {
        fwrite(STDERR, "ffmpeg missing: put a short video at files/a11y-test/loop.webm\n");
    }
}

// Public folder; contao:symlinks links it into public/files/.
touch($media.'/.public');

$texts = [
    'de' => [
        'lang' => 'de', 'title' => 'Testseite Barrierefreiheit', 'fixed' => 'Fest oben rechts',
        'toc' => 'Inhalt', 'one' => 'Erster Abschnitt', 'two' => 'Zweiter Abschnitt', 'sub' => 'Ein Unterabschnitt',
        'aside' => 'Randnotiz', 'intro' => 'Einleitung', 'form' => 'Kontakt', 'name' => 'Name', 'send' => 'Senden',
        'justified' => 'Dieser Absatz steht im Blocksatz mit automatischer Silbentrennung. Donaudampfschifffahrtsgesellschaftskapitän und Rechtsschutzversicherungsgesellschaften sind lange Wörter, die der Browser trennen kann, wenn die Spalte schmal ist. Ein zweiter Satz sorgt für genug Text, damit mehrere Zeilen entstehen und der Blocksatz sichtbar wird.',
        'centred' => 'Dieser Absatz ist zentriert. Die Einstellung „Text linksbündig“ stellt ihn nach links.',
        'alt' => 'Blaue Fläche mit weißen Streifen und einem Kreis', 'caption' => 'Bildunterschrift des ersten Bildes.',
        'li1' => 'Erster Punkt der Liste', 'li2' => 'Zweiter Punkt mit einem', 'link' => 'Link zum zweiten Abschnitt',
        'p3' => 'Ein Satz mit einem', 'ext' => 'Link nach außen', 'and' => 'und einem', 'button' => 'Knopf',
        'note' => 'Eine Randnotiz mit einem kurzen Hinweis.', 'quote' => 'Ein Zitat, das vorgelesen werden kann.',
        'table' => 'Eine kleine Tabelle', 'col' => 'Spalte', 'val' => 'Wert',
        'para' => 'Absatz %d zum Scrollen. Er enthält genug Text, damit die Seite länger als der Bildschirm wird und feste Elemente beim Scrollen geprüft werden können.',
        'video' => 'Testvideo, läuft von selbst',
    ],
    'en' => [
        'lang' => 'en', 'title' => 'Accessibility test page', 'fixed' => 'Fixed top right',
        'toc' => 'Contents', 'one' => 'First section', 'two' => 'Second section', 'sub' => 'A subsection',
        'aside' => 'Side note', 'intro' => 'Introduction', 'form' => 'Contact', 'name' => 'Name', 'send' => 'Send',
        'justified' => 'This paragraph is justified with automatic hyphenation. Incomprehensibilities and internationalisation are long words that the browser can hyphenate when the column is narrow. A second sentence adds enough text for several lines, so the justified text becomes visible.',
        'centred' => 'This paragraph is centred. The setting "Align text left" moves it to the left.',
        'alt' => 'Blue area with white stripes and a circle', 'caption' => 'Caption of the first image.',
        'li1' => 'First item of the list', 'li2' => 'Second item with a', 'link' => 'link to the second section',
        'p3' => 'A sentence with a', 'ext' => 'link to another site', 'and' => 'and a', 'button' => 'button',
        'note' => 'A side note with a short hint.', 'quote' => 'A quotation that can be read aloud.',
        'table' => 'A small table', 'col' => 'Column', 'val' => 'Value',
        'para' => 'Paragraph %d for scrolling. It holds enough text to make the page longer than the screen, so fixed elements can be checked while scrolling.',
        'video' => 'Test video, plays by itself',
    ],
];

foreach ($texts as $lang => $x) {
    $x['root'] = (int) $db->fetchOne("SELECT id FROM tl_page WHERE type = 'root' AND language = ? ORDER BY sorting", [$x['lang']]);

    if (!$x['root']) {
        fwrite(STDERR, "No root page with language {$x['lang']}, skipped\n");
        continue;
    }

    $page = $db->fetchOne('SELECT id FROM tl_page WHERE alias = ? AND pid = ?', ['a11y-test', $x['root']]);

    if (!$page) {
        $db->insert('tl_page', ['pid' => $x['root'], 'sorting' => 4096, 'tstamp' => $t, 'title' => $x['title'], 'alias' => 'a11y-test', 'type' => 'regular', 'published' => 1, 'hide' => 1, 'robots' => 'noindex,nofollow']);
        $page = (int) $db->lastInsertId();
        $db->insert('tl_article', ['pid' => $page, 'sorting' => 128, 'tstamp' => $t, 'title' => $x['title'], 'alias' => 'a11y-test-'.$lang, 'author' => 1, 'inColumn' => 'main', 'published' => 1]);
    }

    $article = (int) $db->fetchOne('SELECT id FROM tl_article WHERE pid = ?', [$page]);
    $paras = '';

    for ($i = 1; $i <= 6; ++$i) {
        $paras .= '<p>'.sprintf($x['para'], $i).'</p>';
    }

    $html = '<style>@keyframes t-spin{to{transform:rotate(360deg)}}.t-fixed{position:fixed;top:0;right:0;z-index:50;padding:8px 12px;background:#6a1b9a;color:#fff;font-weight:700}.t-anim{width:40px;height:40px;margin:1rem 0;background:#c62828;animation:t-spin 1s linear infinite}.t-box{max-width:42rem;margin:0 auto;padding:1rem;font-family:Georgia,serif;font-size:1rem}.t-box img{max-width:100%;height:auto}</style>'
        .'<div class="t-fixed">'.$x['fixed'].'</div>'
        .'<div class="t-box">'
        .'<h1>'.$x['title'].'</h1>'
        .'<nav aria-label="'.$x['toc'].'"><ul><li><a href="#t-one">'.$x['one'].'</a></li><li><a href="#t-two">'.$x['two'].'</a></li></ul></nav>'
        .'<section aria-label="'.$x['intro'].'" id="t-one">'
        .'<h2>'.$x['one'].'</h2>'
        .'<p style="text-align:justify;hyphens:auto">'.$x['justified'].'</p>'
        .'<p style="text-align:center">'.$x['centred'].'</p>'
        .'<figure><img src="/files/a11y-test/landscape.png" alt="'.$x['alt'].'" width="640" height="360"><figcaption>'.$x['caption'].'</figcaption></figure>'
        .'<img src="/files/a11y-test/pattern.png" alt="" width="320" height="180">'
        .'<ul><li>'.$x['li1'].'</li><li>'.$x['li2'].' <a href="#t-two">'.$x['link'].'</a></li></ul>'
        .'<h3>'.$x['sub'].'</h3>'
        .'<p>'.$x['p3'].' <a href="https://example.com">'.$x['ext'].'</a> '.$x['and'].' <button type="button">'.$x['button'].'</button>.</p>'
        .'<div class="t-anim" aria-hidden="true"></div>'
        .'<div class="t-waapi" aria-hidden="true" style="width:40px;height:40px;background:#2e7d32"></div>'
        .'<video src="/files/a11y-test/loop.webm" autoplay muted loop playsinline width="320" height="180" aria-label="'.$x['video'].'"></video>'
        .'</section>'
        .'<aside aria-label="'.$x['aside'].'"><h2>'.$x['aside'].'</h2><p>'.$x['note'].'</p></aside>'
        .'<section aria-labelledby="t-two-h" id="t-two"><h2 id="t-two-h">'.$x['two'].'</h2>'
        .'<form aria-label="'.$x['form'].'" onsubmit="return false"><label>'.$x['name'].' <input id="t-name"></label> <button>'.$x['send'].'</button></form>'
        .'<blockquote><p>'.$x['quote'].'</p></blockquote>'
        .'<table><caption>'.$x['table'].'</caption><tr><th scope="col">'.$x['col'].'</th><th scope="col">'.$x['val'].'</th></tr><tr><td>A</td><td>1</td></tr></table>'
        .$paras
        .'</section></div>';

    $db->delete('tl_content', ['pid' => $article, 'ptable' => 'tl_article']);
    $db->insert('tl_content', ['pid' => $article, 'ptable' => 'tl_article', 'sorting' => 128, 'tstamp' => $t, 'type' => 'html', 'html' => $html]);
    echo "$lang: page $page\n";
}

echo "Media in files/a11y-test; run contao:symlinks so they are served.\n";
