# locate

## Funktion

* „Weise sinnvolle Sprachversion beim ersten Zugriff zu!“
* „Erlaube manuelle Umschaltung und merke Dir diese!“

### Erster Zugriff

Ziel: „Richtige Version“ der Website zuweisen Version: „L=<Sprachen / Länder / Mischmasch>“

Entscheidungskriterien
* Browsersprache
* IP
* HTML5 Geo Location (nicht implementiert)
* andere

Feinheit: Zugriff auf definierte Versions-Seite („/de/produkte“)
* beibehalten? -> Extension nur auf <home> einbinden
* übersteuern? -> Extension überall einbinden

Sprachwechsel... wohin?
* auf die angeforderte Seite
* auf eine definierte Ausgangsseite

## Konzept

„Judges trigger actions based on facts“

Actions
* cookieHandling = Use Cookie to persist language
* overrideCookie = Allow explicit change of Cookie value (&setLang=1)
* page | url = Target Page ID or URL (if none given, redirect to self)
* sys_language = Target Language ID

Facts
* BrowserAcceptedLanguage (Lang, Locale)
* Environment (UserAgent, ...)
* IP2Country (Country Code based on Source IP of Request, ...)
* Constants (any TS constants)

Judges
* AndCondition = Proceed to action if all facts are matched
* Fixed = Proceed anyway

## TypoScript

### Actions
```
actions {
    redirectToPageDE {
        20 = \Bitmotion\Locate\Action\Redirect
        20.sys_language = 1
        20.cookieHandling = 1
    }
    redirectToPageEN {
        20 = \Bitmotion\Locate\Action\Redirect
        20.page = 42
        20.sys_language = 0
        20.cookieHandling = 1
    }
    default {
        20 = \Bitmotion\Locate\Action\Redirect
        20.page = 43
        20.sys_language = 0
        20.cookieHandling = 1
    }
}
```
* cookieHandling = 0 -> bedeutet Prüfung bei jedem Zugriff! (ok, falls nicht auf jeder Seite eingebunden)

### Facts
```
facts {
    # de, DE, de_DE; en, GB, en_GB
    env = \Bitmotion\Locate\FactProvider\Environment
    # DE, UK, ...
    countryByIP = \Bitmotion\Locate\FactProvider\IP2Country
    browserAccepted = \Bitmotion\Locate\FactProvider\BrowserAcceptedLanguage
    constants  = \Bitmotion\Locate\FactProvider\Constants
}
```
Facts = Klassen und symbolische Namen

Ausprägungen zu deren Verwendung in Judges:
* countryByIP.countrycode = DE
* countryByIP.IP2Dezimal = 1.2.3.4
* constants.<meinkey> = DACH
* browserAccepted.lang = fr browserAccepted.locale = de_DE
* env.<Env.-Variable> = <value> z.B. env.HTTP_HOST = https://mysite.fr

### Judges
```
judges {
    20 = \Bitmotion\Locate\Judge\AndCondition
    20.action = redirect_fr
    20.matches (
        countryByIP.countryCode = CH
        browserAccepted.lang = fr
    )
    999 = \Bitmotion\Locate\Judge\Fixed
    999.action = default
}
```

### Sonstiges
```
plugin.tx_locate_pi1.debug
```
* Set to 1 toshow additional information (on the screen), i.e. what was the data of incoming request
```
plugin.tx_locate_pi1.dryRun
```
* Set to 1 if you want debug only
```
page.1 < plugin.tx_locate_pi1
```
* Include userfunc at the beginning of the page

## Cookies

```
Key: bm_locate
Value: Language ID
```
* Wird beim ersten Zugriff gesetzt, falls im TS erlaubt
* Wird auch gesetzt, wenn &setLang=1 übergeben wird -> Dies ins Sprachmenü aufnehmen!
* Ist Cookie gesetzt, werden die Judges nicht ausgeführt