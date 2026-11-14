<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Panel;

/**
 * The small inline script for the <head> that applies the visitor's stored
 * choices before the page is painted, so the page never shows its default look
 * first and then jumps.
 *
 * It reads localStorage, sets data-a11y-<flag> on <html> for every offered
 * switch that is on and scales the root font size. Only offered settings are
 * applied: a setting the website no longer offers has no switch in the panel,
 * so the visitor could not turn it off again. For the same reason read aloud is
 * left out where the browser has no speech output. The logic mirrors applySettings()
 * and normaliseFont() in public/accessibility.js, which wires the panel later
 * (deferred).
 *
 * Text size: by default the root font size is scaled (works where the design
 * uses rem, em or %). With $zoom (module option "Enlarge the whole page", for
 * designs with sizes in px) the page is zoomed instead: data-a11y-font-zoom and
 * --nax-font-zoom on <html>; accessibility.css zooms the content, not the panel.
 */
final class HeadScript
{
    /**
     * @param list<string> $offered offered settings, see Features::fromModuleValue()
     * @param bool         $zoom    text size enlarges the whole page (module option nax_font_zoom)
     */
    public static function build(array $offered, bool $zoom = false): string
    {
        $switches = json_encode(Features::switches($offered), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $key = json_encode(Features::STORAGE_KEY, JSON_THROW_ON_ERROR);

        $script = '(function(){try{'
            .'var s=JSON.parse(localStorage.getItem('.$key.')||"{}")||{},e=document.documentElement,f='.$switches.',i,n,b,d;'
            // Read aloud only where the browser can speak (the panel hides it elsewhere).
            .'for(i=0;i<f.length;i++){if(s[f[i]]===true&&(f[i]!=="speak"||window.speechSynthesis)){e.setAttribute("data-a11y-"+f[i],"")}}';

        if (\in_array(Features::FONT, $offered, true)) {
            $steps = json_encode(Features::FONT_STEPS, JSON_THROW_ON_ERROR);
            $min = Features::FONT_MIN;

            // Nearest step; anything at or below the smallest step is the normal size.
            $script .= 'n=parseInt(s.font,10);'
                ."if(n>$min){f=$steps;b=$min;d=1e9;"
                .'for(i=0;i<f.length;i++){if(Math.abs(f[i]-n)<d){d=Math.abs(f[i]-n);b=f[i]}}'
                ."if(b>$min){e.setAttribute(\"data-a11y-font\",String(b));"
                .($zoom
                    ? 'e.setAttribute("data-a11y-font-zoom","");e.style.setProperty("--nax-font-zoom",String(b/100))}}'
                    : 'e.style.fontSize="calc(var(--nax-font-base, 100%) * "+b/100+")"}}');
        }

        return $script.'}catch(x){}})();';
    }
}
