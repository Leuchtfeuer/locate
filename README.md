# locate

## Function

* "Assign useful language on first page access."
* "Allow manual switching and remember the value."

### First Access

Goal: Assign "correct language version" of the website: "L=<Languages / Country / Mishmash>""

Decision criteria
* Browser language
* IP
* other

Fineness: Access to defined version page ("/en/products")
* keep? -> Include extension only on the start page
* override? -> Integrate extension everywhere

Change of language... to where?
* to the requested page
* to a specific page

## Concept

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
* cookieHandling = 0 -> means check at every access! (ok, if not included on every page)

### Facts
```
config.tx_locate.facts {
    browserAccepted = Bitmotion\Locate\FactProvider\BrowserAcceptedLanguage
    countryByIP = Bitmotion\Locate\FactProvider\IP2Country
    env = Bitmotion\Locate\FactProvider\Environment
}
```
Facts = Classes and symbolic names

Characteristics for their use in judges:
* countryByIP.countryCode = DE
* countryByIP.IP2Dezimal = 1.2.3.4
* browserAccepted.lang = fr or browserAccepted.locale = de_DE
* env.<Env.-Variable> = <value> e.g. env.HTTP_HOST = https://mysite.fr

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

### Miscellaneous
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
* Set at first page call, if allowed in the TS
* Is also set if &setLang=1 is passed -> Add this parameter to your language menu!
* If a cookie is set, the judges are no longer executed
