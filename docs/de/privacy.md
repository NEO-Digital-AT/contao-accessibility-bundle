---
titel: Datenschutz
kurzfassung: Was das Panel wo speichert, was Vorlesen an wen schickt, die Legasthenie-Schrift und was in die Datenschutzerklärung der Website gehört.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Datenschutz

## Was gespeichert wird

- Die Auswahl des Besuchers liegt im `localStorage` des Browsers unter dem
  Schlüssel `nax-settings`, etwa `{"contrast":true,"font":130}`. Sonst wird
  nichts gespeichert, auch nichts über den Besucher.
- Der Eintrag entsteht erst, wenn der Besucher eine Einstellung ändert, und
  verschwindet wieder, wenn alle aus sind (**Alles zurücksetzen**). Er bleibt
  bis dahin oder bis der Besucher die Browserdaten löscht.
- Keine Cookies, kein Tracking, keine Anfragen an den Server der Website oder
  an Dritte zum Speichern oder Anwenden der Auswahl.
- In der Datenbank der Website wird nichts gespeichert; die Erweiterung hat
  keine Tabelle für Besucher.

## Legasthenie-Schrift

Die Schrift OpenDyslexic gehört zur Erweiterung und wird vom eigenen Server
der Website geladen (`bundles/neoaccessibility/fonts/`), und nur, wenn ein
Besucher **Legasthenie-Schrift** einschaltet. Kein Schriftendienst ist
beteiligt.

## Vorlesen

**Vorlesen** nutzt die Sprachausgabe des Browsers (Web Speech API). Die
Erweiterung selbst schickt nichts irgendwohin: Sie übergibt den Text an den
Browser, der Browser liest ihn vor. Welche Stimme liest, entscheidet, ob der
Text das Gerät verlässt:

- Stimmen, die auf dem Gerät installiert sind (etwa die Systemstimmen von
  Windows, macOS und iOS, die Firefox und Safari nutzen), lesen den Text auf
  dem Gerät.
- Online-Stimmen (etwa die „Google“-Stimmen von Chrome am Computer und die
  „Online“-Stimmen von Microsoft Edge) schicken den vorgelesenen Text und
  damit die Anfrage des Besuchers an einen Dienst des Browser-Herstellers, der
  die Sprache zurückliefert. Das geschieht nach den Bedingungen des
  Herstellers; die Website erhält und steuert es nicht.

### Standard: nur Stimmen des Geräts

Ab Werk liest das Panel **nur mit Stimmen, die auf dem Gerät des Besuchers
installiert sind** (der Browser kennzeichnet sie so). Die Wahl der Stimme
überlässt es nie dem Browser:

- Es wartet, bis der Browser seine Stimmen gemeldet hat (manche Browser
  melden sie erst kurz nach dem Laden; das Panel wartet bis zu 2,5 Sekunden).
  Vorher wird nichts gesprochen, und nie ohne eine vom Panel gewählte Stimme —
  die Standardstimme des Browsers kann eine Online-Stimme sein.
- Hat das Gerät keine Stimme für die Sprache der Seite, sind **Vorlesen** und
  die Schnellauswahl **Vorlesen** ausgegraut, und der Grund steht daneben in
  der Sprache des Besuchers, etwa „Auf diesem Gerät ist keine Stimme für
  Deutsch installiert.“ Ein Text in einer anderen Sprache ohne Gerätestimme
  wird übersprungen.

Mit dieser Voreinstellung verlässt kein Text das Gerät, und Vorlesen braucht
über die gespeicherte Auswahl hinaus keine Erwähnung. Besucher sehen den
Hinweis „Nutzt eine Stimme, die auf Ihrem Gerät installiert ist. Der Text
bleibt auf Ihrem Gerät.“

### Online-Stimmen erlauben

Die Moduloption **Online-Stimmen des Browsers erlauben** (standardmäßig aus,
[Einrichtung](setup.md)) lässt Vorlesen auch Online-Stimmen nutzen, wo das
Gerät keine Stimme für die Sprache hat; Gerätestimmen haben weiter Vorrang.
Dann schickt der Browser den vorgelesenen Text an seinen Hersteller, und
**das muss in der Datenschutzerklärung stehen**. Das Modulformular zeigt bei
der Option einen Hinweis, solange sie an ist. Besucher sehen dann: „Nutzt die
Stimmen Ihres Browsers. Hat Ihr Gerät keine für die Sprache, schicken manche
Browser den Text zum Vorlesen an einen Online-Dienst ihres Herstellers.“
Geschickt wird der Text der Seite, den der Besucher hören will, keine Eingaben
des Besuchers.

## Was in die Datenschutzerklärung gehört

Ein Vorschlag zum Anpassen (keine Rechtsberatung):

> **Barrierefreiheits-Panel.** Diese Website bietet ein Panel mit Hilfen zur
> Darstellung. Ihre Auswahl wird nur in Ihrem Browser gespeichert (lokaler
> Speicher, Eintrag „nax-settings“) und nicht an uns übermittelt. Die Funktion
> „Vorlesen“ nutzt eine Stimme, die auf Ihrem Gerät installiert ist; der Text
> bleibt auf Ihrem Gerät.

Nur wenn **Online-Stimmen des Browsers erlauben** an ist, ergänzen:

> Hat Ihr Gerät keine Stimme für die Sprache, nutzt „Vorlesen“ eine
> Online-Stimme Ihres Browsers; Ihr Browser schickt den vorgelesenen Text dann
> an einen Dienst seines Herstellers (etwa Google oder Microsoft), um die
> Sprache zu erzeugen. Wir erhalten diesen Text nicht und haben auf diese
> Verarbeitung keinen Einfluss.

## Einwilligung

Das Speichern der Auswahl dient einer Funktion, die der Besucher ausdrücklich
wünscht; eine solche Speicherung braucht in der Regel keine Einwilligung
(Art. 5 Abs. 3 ePrivacy-Richtlinie, in Österreich § 165 Abs. 3 TKG 2021).
Vorlesen startet der Besucher in der eigenen Funktion seines Browsers;
standardmäßig bleibt der Text auf dem Gerät. Ob die Website mehr braucht, vor
allem bevor Online-Stimmen erlaubt werden, mit der oder dem
Datenschutzbeauftragten klären; das ist keine Rechtsberatung. Wer Vorlesen
nicht anbieten will, wählt es im Modul ab
([Einrichtung](setup.md)).
