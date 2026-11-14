---
titel: Fehlersuche
kurzfassung: Häufige Probleme mit dem Barrierefreiheits-Panel als Anzeichen, Ursache und Lösung.
zielgruppe: anwender
sprache: de
stand: 2026-11-14
---

# Fehlersuche

| Anzeichen | Ursache | Lösung |
| --- | --- | --- |
| Die Schaltfläche erscheint nicht. | Das Modul steht nicht im Seitenlayout dieser Seite, oder JavaScript ist gesperrt oder fehlgeschlagen. | Das Modul ins Layout einbinden ([Einrichtung](setup.md)); in die Browserkonsole sehen. Das Panel erscheint erst, wenn sein Skript läuft. |
| Der Link `{{accessibility::link}}` springt nur an den Seitenanfang. | Das Modul steht nicht auf dieser Seite. | Es ins Seitenlayout einbinden. |
| Die Schaltfläche überdeckt ein Cookie-Banner oder eine andere Schaltfläche. | Gleiche Ecke, und eine der beiden gehört nicht zu den Schaltflächen am Bildschirmrand (ein überschriebenes Template ohne die `data-neo-dock`-Attribute oder eine ältere Erweiterung); oder ein Banner mit höherer Stapelreihenfolge. | Die Attribute übernehmen ([Anpassen](customising.md#schaltflächen-am-bildschirmrand-neo-basic)), eine andere Ecke wählen oder `--nax-z-index`, `--nax-offset-x`, `--nax-offset-y` ändern ([Anpassen](customising.md)). |
| Die Schaltfläche zur Barrierefreiheit fehlt; in ihrer Ecke sitzt eine Einstellungs-Schaltfläche mit Zahnrad. | Andere Schaltflächen teilen sich die Ecke, und das Seitenlayout ordnet sie als *Eine Einstellungs-Schaltfläche mit Menü* an (Standard): Ihr Menü führt zum Panel. | So gewollt. Eine andere Anordnung im Seitenlayout (**Schaltflächen am Bildschirmrand**) einstellen, eine andere Ecke hier ([Einrichtung](setup.md#zusammen-mit-anderen-schaltflächen-in-derselben-ecke)). |
| Mit zwei Schaltflächen steht die Schaltfläche zur Barrierefreiheit nicht in der Ecke, die unter **Position der Schaltfläche** gewählt ist. | Eine Schaltfläche unten weicht dem Cookie-Knopf und dem KI-Chat aus. | So gewollt; eine Position oben bleibt. |
| Die Auswahl wird vergessen. | Der Browser sperrt `localStorage` (privates Fenster, strenge Einstellungen). | In der Website nichts zu tun; die Auswahl gilt für die aktuelle Seite. |
| Eine Einstellung oder Schnellauswahl fehlt im Panel. | Sie ist im Modul nicht angehakt; eine Schnellauswahl, von deren Einstellungen keine angeboten wird, fällt weg — das Modulformular nennt sie in einem Hinweis unter **Schnellauswahl**. | Unter **Angebotene Einstellungen** oder **Schnellauswahl** anhaken. |
| **Vorlesen** und die Schnellauswahl „Vorlesen“ fehlen. | Die Einstellung ist im Modul nicht angehakt. | **Vorlesen** unter **Angebotene Einstellungen** anhaken. |
| **Vorlesen** ist ausgegraut: „Auf diesem Gerät ist keine Stimme für … installiert.“ | Das Gerät des Besuchers hat keine Stimme für die Sprache der Seite; standardmäßig werden nur Gerätestimmen genutzt. | Der Besucher installiert in den Spracheinstellungen des Geräts eine Stimme für die Sprache; oder die Website erlaubt die Online-Stimmen des Browsers (Datenschutzerklärung!). |
| **Vorlesen** ist ausgegraut: „Dieser Browser kann nicht vorlesen.“ | Der Browser hat keine Sprachausgabe (Web Speech API). | Einen anderen Browser nutzen. |
| Mit Vorlesen reagieren Link, Schaltfläche oder Häkchen beim ersten Klick nicht. | Vorlesen liest es zuerst vor; der Hinweis daneben sagt, dass der nächste Klick es benutzt. | So gewollt: noch einmal klicken oder mit Eingabe- bzw. Leertaste benutzen. Wird Vorlesen ausgeschaltet, wirkt jeder Klick sofort. |
| Mit Vorlesen reagiert ein Bedienelement der Website sofort, obwohl es zuerst vorgelesen werden sollte. | Die Website startet es beim Drücken der Maus oder des Fingers (`pointerdown`, `mousedown`, `touchstart`) statt beim Klick, oder es ist kein Link, keine Schaltfläche, keine Beschriftung und kein Element mit der Rolle eines Bedienelements. | Es auf den Klick reagieren lassen oder ihm das passende Element bzw. eine `role` geben (etwa `role="button"`). |
| Vorlesen bleibt stumm, der Text wird nicht markiert. | Die Seite lädt noch die Stimmen (bis zu 2,5 Sekunden), oder der Text ist in einer Sprache ohne Gerätestimme (er wird übersprungen). | Kurz warten; die Sprache des Textes mit `lang` auszeichnen; eine Stimme dafür installieren. |
| Vorlesen markiert den Text, bleibt aber stumm. | Das Gerät ist stumm geschaltet, oder die Stimme ist defekt. | Lautstärke und Spracheinstellungen des Geräts prüfen. |
| Vorlesen nutzt eine Stimme der falschen Sprache. | Die Seite oder der Seitenteil ist nicht mit ihrer Sprache ausgezeichnet. | Die Sprache der Seite setzen (Startpunkt), andere Sprachen mit `lang` auszeichnen. |
| Text in einer anderen Sprache wird mit der Stimme der Seite gelesen. | Der Teil ist nicht mit seiner Sprache ausgezeichnet. | Mit `lang="…"` auszeichnen (etwa im Texteditor). |
| Die Legasthenie-Schrift erscheint nicht. | Die Schriftdateien fehlen unter `bundles/neoaccessibility/fonts/`, oder eine Content Security Policy sperrt Schriften der Website. | `assets:install public` ausführen; `'self'` in `font-src` erlauben. |
| Mit **Lesbare Schrift** oder **Legasthenie-Schrift** wird ein Symbol zu Buchstaben oder einem Kästchen. | Die Symbolschrift der Website sitzt an einem Element, das das Panel nicht als Symbol erkennt. | Dem Element `data-nax-keep-font` geben ([Anpassen](customising.md)). |
| Der Text wird viel zu groß. | Die Website setzt eine eigene Grundschriftgröße. | `--nax-font-base` auf denselben Wert setzen. |
| Die Schriftgröße ändert an manchen Stellen nichts. | Dort stehen Größen in `px`. | Nur Größen in `rem`, `em` oder `%` wachsen mit. Die Gestaltung auf `rem` umstellen oder im Modul **Ganze Seite vergrößern** anhaken ([Einrichtung](setup.md)). |
| Die Schriftgröße ändert auf der Seite nichts, nur das Panel wächst. | Die Gestaltung der Website gibt alle Schriftgrößen in `px` an. | Im Modul **Ganze Seite vergrößern** anhaken oder die Gestaltung auf `rem` umstellen. |
| Ein Element sah bei 145 % oder 160 % anders aus. | Seit 1.0.0 hat die Schriftgröße die Stufen 115, 130 und 150; gespeicherte 145 und 160 werden zu 150. | Gegen `data-a11y-font="150"` gestalten. |
| Der hohe Kontrast sieht stellenweise falsch aus. | Der Modus ist allgemein: schwarze Schrift auf Weiß für alles. | Diese Stellen mit Regeln unter `html[data-a11y-contrast]` anpassen. |
| Ein Teil der Seite wird von Kontrast verringern, Farben abschwächen oder Graustufen nicht gefiltert. | Er liegt über dem Panel (`z-index` höher als `--nax-z-index` − 1). | Seinen `z-index` senken oder `--nax-z-index` erhöhen. |
| Scrollen wirkt mit einem Farbfilter langsamer. | Die Filterfläche wird bei jedem Scrollen neu gezeichnet; auf schwachen Geräten kostet das Zeit. | In der Website nichts zu ändern; der Besucher kann den Filter ausschalten. |
| **Bilder ausblenden** lässt Bilder stehen. | Es sind Hintergrundbilder des Designs (CSS), die bewusst bleiben. | Mit einer Regel unter `html[data-a11y-noimages]` ausblenden, wenn kein Text darauf steht. |
| **Bilder ausblenden** zeigt für ein Bild keine Beschreibung. | Das Bild hat keinen Alternativtext (Schmuckbild). | Inhaltsbildern in der Dateiverwaltung oder im Inhaltselement eine Beschreibung geben. |
| **Animationen anhalten** lässt etwas weiterlaufen. | Die Website bewegt es mit eigenen Zeitgebern (manche Bildwechsler, Skripte, die scrollen). | Im Skript der Website anhalten, solange `html[data-a11y-motion]` gesetzt ist. |
| Leselinie oder Lesemaske liegen hinter einem Element. | Das Element hat einen höheren `z-index` als das Panel. | Seinen `z-index` senken oder `--nax-z-index` erhöhen. |
| **Seitenstruktur** zeigt keine Bereiche. | Die Seite hat kein `header`, `nav`, `main`, `aside`, `footer` und keine benannten Abschnitte. | Diese Elemente im Layout nutzen; die Layoutbereiche von Contao sind schon `header`, `main` und `footer`. |
| Der große Mauszeiger erscheint nicht. | Eine Content Security Policy sperrt `data:`-Bilder. | `data:` in `img-src` erlauben. |
| Das Inline-Skript im `<head>` wird gesperrt. | Eine Content Security Policy vom Webserver oder einer anderen Erweiterung. | Mit der CSP von Contao wird es automatisch erlaubt; sonst seinen Hash (`'sha256-…'`) erlauben. |
| „Cookie-Einstellungen“ fehlt im Panel. | Der Knopf des Cookie-Hinweises (allein oder im Menü der Einstellungs-Schaltfläche) führt zu den Cookie-Einstellungen (dann entfällt der Eintrag absichtlich); oder das Cookie-Werkzeug der Website stellt `window.cookieConsent.open()` nicht bereit. | Diesen Knopf oder das Menü nutzen; oder ein Cookie-Werkzeug nutzen, das es bereitstellt. |
