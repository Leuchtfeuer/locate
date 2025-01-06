.. include:: ../../../Includes.txt

.. _functions-assignLanguage-verdicts-redirect:

================
Redirect Verdict
================

The redirect verdict redirects the user to a specific page in a specific language. So that the check does not have to be repeated
for each page access, the result can be cached in the user's session.

.. _functions-assignLanguage-verdicts-redirect-configuration:

Configuration
=============

.. tip::

   The configuration for :ref:`sessionHandling <>`, `overrideSessionValue` and `overrideQueryParameter` can also be made directly as a root configuration:

.. ### BEGIN~OF~TABLE ###

.. _functions-assignLanguage-verdicts-redirect-configuration-languageId:

Language ID
~~~~~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.verdicts.[name].sys_language
   Data type
         integer
   Default
         unset
   Description
         The ID of the language the user should be redirected to.

.. _functions-assignLanguage-verdicts-redirect-configuration-pageId:

Page ID
~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.verdicts.[name].page
   Data type
         integer
   Default
         unset
   Description
         The ID of the page the user should be redirected to. If unset, the user will stay on the current page or will be
         redirected to an other language version of the current page.

.. _functions-assignLanguage-verdicts-redirect-configuration-url:

URL
~~~
.. container:: table-row

   Property
         config.tx_locate.verdicts.[name].url
   Data type
         string
   Default
         unset
   Description
         This option only applies if no information has been entered for both
         :ref:`sys_language <functions-assignLanguage-verdicts-redirect-configuration-languageId> and
         :ref:`page <functions-assignLanguage-verdicts-redirect-configuration-pageId>`. The user is then redirected to this
         configured URL.

.. _functions-assignLanguage-verdicts-redirect-configuration-sessionHandling:

Session Handling
~~~~~~~~~~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.verdicts.[name].sessionHandling
   Data type
         integer
   Default
         :code:`0`
   Description
         If this option is enabled, the verdict will be saved in a session and will not be evaluated again.

.. _functions-assignLanguage-verdicts-redirect-configuration-overrideSessionValue:

Override Session Value
~~~~~~~~~~~~~~~~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.verdicts.[name].overrideSessionValue
   Data type
         integer
   Default
         :code:`0`
   Description
         If this option is enabled, it is possible to overwrite the verdict stored in the session. For this, a URL query parameter
         is mandatory.

.. _functions-assignLanguage-verdicts-redirect-configuration-overrideQueryParameter:

Override Query Parameter
~~~~~~~~~~~~~~~~~~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.verdicts.[name].overrideQueryParameter
   Data type
         string
   Default
         :code:`setLang`
   Description
         If session overwriting is enabled and this parameter is present in the URL, the session data will be overwritten with
         the current request. Thus, it is possible for a user who was originally directed to the English language version to be
         directed to the German language version of the page e.g. by clicking in the language menu. The language menu must then
         generate all links with the query parameter attached (:code:`/de/?setLang`). The value of the parameter does not matter.

.. _functions-assignLanguage-verdicts-redirect-configuration-allowFallback:

Allow Fallback
~~~~~~~~~~~~~~~~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.verdicts.[name].allowFallback
   Data type
         integer
   Default
         :code:`0`
   Description
         If the option is enabled, redirection to a non-localized page is allowed. In this case, the page is accessed under the
         corresponding language URL, even if it does not exist. The displayed content corresponds to the defined fallback page
         of your site configuration.

.. _functions-assignLanguage-verdicts-redirect-example:

Example
=======
.. code-block:: typoscript

   config.tx_locate {
       sessionHandling = 1
       overrideSessionValue = 1

       verdicts {
           # Redirect the user to the default language version of the current page
           redirectToPageEN = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToPageEN {
               sys_language = 0
           }

           # Redirect the user to the default language version of page 29
           redirectToPageUS = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToPageUS {
               sys_language = 0
               page = 29
           }

           # Redirect the user to the default language version of page 29 and disable session handling
           redirectToPageUSNC = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToPageUSNC {
               sys_language = 0
               page = 29
               sessionHandling = 0
           }

           # Redirect the user to a specific URL
           redirectToPageXX = Leuchtfeuer\Locate\Verdict\Redirect
           redirectToPageXX {
               url = https://www.Leuchtfeuer.com
           }
       }
   }
