.. include:: ../../../Includes.txt

.. _functions-assignLanguage-examples:

=======
Example
=======

This is a complete example that redirects the user according to the following criteria in the following order:

#. Users from mainland China should be redirected to a specific URL.
#. Users using American English as browser language should be redirected to the US language version of the current page.
#. Users using German as browser language should be redirected to the German language version of the current page.
#. Users using French as browser language should be redirected to the French language version of another page.
#. All other users should be redirected to the English language version of the current page.

.. code-block:: typoscript

   config.tx_locate = 1
   config.tx_locate {
       excludeBots = 1
       sessionHandling = 1
       overrideSessionValue = 1
       # Simulate your IP address for countryByIP fact provider (for test purposes only), e.g. 109.10.163.98 is a french IP address
       simulateIp =

       verdicts {
           redirectToMainlandChina = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToMainlandChina.url = https://www.example.cn

           redirectToUS = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToUS.sys_language = 12

           redirectToDE = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToDE.sys_language = 29

           redirectToPageFR = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToPageFR {
               sys_language = 19
               page = 89
           }

           redirectToEN = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToEN.sys_language = 0
       }

       judges {
           # Users from mainland China should be redirected to a specific URL.
           100 = Leuchtfeuer\Locate\Judge\Condition
           100 {
               verdict = redirectToMainlandChina
               fact = countryByIP
               prosecution = cn
           }

           # Users with the American English browser language should be redirected to a specific language version of the current page.
           200 = Leuchtfeuer\Locate\Judge\Condition
           200 {
               verdict = redirectToUS
               fact = browserAcceptLanguage
               prosecution = en-us
           }

           # Users with the browser language German shall be redirected to the German language version of the current page.
           300 = Leuchtfeuer\Locate\Judge\Condition
           300 {
               verdict = redirectToDE
               fact = browserAcceptLanguage
               prosecution = de
           }

           # Users with the French browser language should be redirected to the French language version of another page.
           300 = Leuchtfeuer\Locate\Judge\Condition
           300 {
               verdict = redirectToPageFR
               fact = browserAcceptLanguage
               prosecution = fr
           }

           # All other users should be redirected to the English language version of the current page.
           999999 = Leuchtfeuer\Locate\Judge\Fixed
           999999.verdict = redirectToEN
       }
   }

.. tip::

   The full example is available at `GitHub <https://github.com/Leuchtfeuer/locate/blob/master/Configuration/TypoScript/setup-switch_language.txt>`__.
