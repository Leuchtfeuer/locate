# locate

## Funktion

* „Weise sinnvolle Sprachversion beim ersten Zugriff zu!“
* „Erlaube manuelle Umschaltung und merke Dir diese!“

### Erster Zugriff

Ziel: „Richtige Version“ der Website zuweisen Version: „L=<Sprachen / Länder / Mischmasch>“

Entscheidungskriterien
* Browsersprache
* IP
* andere

Feinheit: Zugriff auf definierte Versions-Seite („/de/produkte“)
* beibehalten? -> Extension nur auf der Startseite einbinden
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

Judges
* AndCondition = Proceed to action if all facts are matched
* Fixed = Proceed anyway

## TypoScript

* Include TypoScript on pages where the redirect should take place

### Actions
```
config.tx_locate.actions {
    redirectToPageDE {
        20 = Bitmotion\Locate\Action\Redirect
        20.sys_language = 1
        20.cookieHandling = 1
        20.overrideCookie = 1
    }
    redirectToPageEN {
        20 = Bitmotion\Locate\Action\Redirect
        20.page = 42
        20.sys_language = 0
        20.cookieHandling = 1
        20.overrideCookie = 1
    }
    default {
        20 = Bitmotion\Locate\Action\Redirect
        20.page = 43
        20.sys_language = 0
        20.cookieHandling = 1
        20.overrideCookie = 1
    }
}
```
* cookieHandling = 0 -> bedeutet Prüfung bei jedem Zugriff! (ok, falls nicht auf jeder Seite eingebunden)

### Facts
```
config.tx_locate.facts {
    browserAccepted = Bitmotion\Locate\FactProvider\BrowserAcceptedLanguage
    countryByIP = Bitmotion\Locate\FactProvider\IP2Country
    env = Bitmotion\Locate\FactProvider\Environment
}
```
Facts = Klassen und symbolische Namen

Ausprägungen zu deren Verwendung in Judges:
* countryByIP.countrycode = DE
* countryByIP.IP2Dezimal = 1.2.3.4
* browserAccepted.lang = fr oder browserAccepted.locale = de_DE
* env.<Env.-Variable> = <value> z.B. env.HTTP_HOST = https://mysite.fr

### Judges
```
config.tx_locate.judges {
    20 = Bitmotion\Locate\Judge\Condition
    20.action = redirectToPageDE
    20.match = browserAccepted.lang = de

    999 = Bitmotion\Locate\Judge\Fixed
    999.action = default
}
```

### Sonstiges
```
config.tx_locate.cookieName = bm_locate
```
* Name of the cookie.
```
config.tx_locate.cookieLifetime = 30
```
* Default lifetime of cookie (in days).
```
config.tx_locate.httpResponseCode = HTTP/1.1 303 See Other
```
* HTTP response code for redirects.

## Cookies

```
Key: bm_locate (configurable)
Value: Language ID
```
* Wird beim ersten Zugriff gesetzt, falls im TS erlaubt
* Wird auch gesetzt, wenn &setLang=1 übergeben wird -> Dies ins Sprachmenü aufnehmen!
* Ist Cookie gesetzt, werden die Judges nicht ausgeführt