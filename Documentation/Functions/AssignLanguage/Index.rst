.. include:: ../../Includes.txt

.. _functions-assignLanguage:

======================
Assign Website Version
======================

.. note::

   Please note that this function is disabled by default. :ref:`Learn how to enable it <admin-enablingExtension>`.

.. _functions-assignLanguage-howThisExtensionWorks:

How This Extension Works
========================

Assigning a website version to the user works like a trial: the website user is accused of being guilty of one of the
:ref:`existing charges <functions-assignLanguage-howThisExtensionWorks-facts>`. A
:ref:`judge <functions-assignLanguage-howThisExtensionWorks-judges>` then decides which
:ref:`verdict <functions-assignLanguage-howThisExtensionWorks-verdicts>` should be pronounced and executes it.

.. _functions-assignLanguage-howThisExtensionWorks-facts:

Facts
-----

A fact is a property that applies to the website user. This extension comes up with two different facts build in by default. The
extension can be extended with more facts.

.. _functions-assignLanguage-howThisExtensionWorks-facts-browserAcceptLanguage:

Browser Accept Language
~~~~~~~~~~~~~~~~~~~~~~~

This fact reads the languages supported by the user's browser. Technically, it processes the `HTTP_ACCEPT_LANGUAGE` header that
is sent along with the user's request. This fact is available for judges under the name :typoscript:`browserAcceptLanguage`.

.. _functions-assignLanguage-howThisExtensionWorks-countryByIPAddress:

Country by IP Address
~~~~~~~~~~~~~~~~~~~~~

This fact reads the user's IP address and compares it with an internal database to determine from which country the request
originates. The IP is not cached or used for any other purpose. This fact is available for judges under the name
:typoscript:`countryByIP`.

.. _functions-assignLanguage-howThisExtensionWorks-judges:

Judges
------

Judges check whether a configured fact applies to the current web page request and make a judgment if it does. It also
prioritizes the judgments if multiple Facts apply to the query. The prioritization is based on the occurrence of the first
hit. A few examples:

.. rst-class:: bignums

1. The browser of the user accepts multiple languages

   In this case, the priority of the judges is based on the priority of the browser language. So if a browser supports the
   languages German, French and English (:code:`accept-language: de-DE,fr,en;q=0.9,de;q=0.8,en-US;q=0.7`) and there is the
   following TypoScript configuration, then the redirectToPageDE verdict is enforced, although the redirection to the English
   language version is higher prioritized (lower key).

   .. code-block:: typoscript

      config.tx_locate.judges {
          100 = Leuchtfeuer\Locate\Judge\Condition
          100 {
              verdict = redirectToPageEN
              fact = browserAcceptLanguage
              prosecution = en
          }

          200 = Leuchtfeuer\Locate\Judge\Condition
          200 {
              verdict = redirectToPageAT
              fact = browserAcceptLanguage
              prosecution = de-at
          }

          300 = Leuchtfeuer\Locate\Judge\Condition
          300 {
              verdict = redirectToPageDE
              fact = browserAcceptLanguage
              prosecution = de
          }
      }

2. The browser of the user accepts multiple languages (with region)

   Like the first example, the browser supports multiple languages again, but we changed the ordering in the settings of the web
   browser so that the header looks like this: :code:`de-DE,fr,en-US;q=0.9,de,en;q=0.8`. We also changed the TypoScript so that
   American-English browsers should be directed to a separate page. In this case, the order of delivered verdicts would be:
   `redirectToPageUS`, `redirectToPageDE`, `redirectToPageEN`. Therefore, verdict `redirectToPageUS` is enforced.

   .. code-block:: typoscript

      config.tx_locate.judges {
          100 = Leuchtfeuer\Locate\Judge\Condition
          100 {
              verdict = redirectToPageUS
              fact = browserAcceptLanguage
              prosecution = en-us
          }

          150 = Leuchtfeuer\Locate\Judge\Condition
          150 {
              verdict = redirectToPageEN
              fact = browserAcceptLanguage
              prosecution = en
          }

          200 = Leuchtfeuer\Locate\Judge\Condition
          200 {
              verdict = redirectToPageAT
              fact = browserAcceptLanguage
              prosecution = de-at
          }

          300 = Leuchtfeuer\Locate\Judge\Condition
          300 {
              verdict = redirectToPageDE
              fact = browserAcceptLanguage
              prosecution = de
          }
      }

3. A user from mainland China accesses the page

   In this case, a user from mainland China is supposed to access the site and should be redirected to a special page. All other
   users who operate their browser in Chinese, but do not access the page from mainland China, should not be redirected. The
   `accept-language` header looks like this: :code:`zh-chs,de,en-US;q=0.9,zh-sg,en;q=0.8,zh;q=0.7`.

   .. code-block:: typoscript

      config.tx_locate.judges {
          100 = Leuchtfeuer\Locate\Judge\Condition
          100 {
              verdict = redirectToPageCNMainland
              fact = countryByIP
              prosecution = cn
          }

          200 = Leuchtfeuer\Locate\Judge\Condition
          200 {
              verdict = redirectToPageDE
              fact = browserAcceptLanguage
              prosecution = de
          }

          300 = Leuchtfeuer\Locate\Judge\Condition
          300 {
              verdict = redirectToPageCN
              fact = browserAcceptLanguage
              prosecution = zh-chs
          }
      }


.. note::
   You can use the `countryByIP` fact, you can use the dash character (:code:`-`) to match all IP addresses that cannot be looked
   up in the database or cannot be assigned to any country.

.. _functions-assignLanguage-howThisExtensionWorks-verdicts:

Verdicts
--------

A verdict can be a redirect to a specific page / language version of the website, or simply a mechanism that stores data of
the user in a session. Each verdict has additional, specific settings.

.. _functions-assignLanguage-configuration:

Configuration
=============

The basic configuration of locate consists of only five different options, which are shown below.

Root Configuration
------------------

.. ### BEGIN~OF~TABLE ###

.. _functions-assignLanguage-configuration-dryRun:

Dry Run
~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.dryRun
   Data type
         integer
   Default
         0
   Description
         This will prevent the verdict to be called.

.. _functions-assignLanguage-configuration-excludeBots:

Exclude Bots
~~~~~~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.excludeBots
   Data type
         integer
   Default
         1
   Description
         Whether bots should be excluded from the behavior of the extension. This option only takes effect if the
         :ref:`corresponding Composer package <admin-additionalPackages-detectCrawler>` has been installed.

.. _functions-assignLanguage-configuration-verdicts:

Verdicts
~~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.verdicts
   Data type
         array
   Default
         unset
   Description
         This collection contains the configuration of the verdicts. Each verdict can provide its own configuration. This
         extension provides one judgment by default, the :ref:`redirect verdict <functions-assignLanguage-verdicts-redirect>`.

.. _functions-assignLanguage-configuration-facts:

Facts
~~~~~
.. container:: table-row

   Property
         config.tx_locate.facts
   Data type
         array
   Default
         .. code-block:: typoscript
            {
                browserAcceptLanguage = Leuchtfeuer\Locate\FactProvider\BrowserAcceptedLanguage
                countryByIP = Leuchtfeuer\Locate\FactProvider\IP2Country
            }
   Description
         This array contains the facts. The key is the name of the fact used in the judges section and the value is the php class
         that should take care about the trial.

.. _functions-assignLanguage-configuration-judges:

Judges
~~~~~~
.. container:: table-row

   Property
         config.tx_locate.judges
   Data type
         array
   Default
         unset
   Description
         This array contains the judges. Each judge may have specific configuration depending on the type of judge. This
         extension provides two different types of judges by default: The
         :ref:`conditional judge <functions-assignLanguage-judges-conditional>` and the
         :ref:`fixed judge <functions-assignLanguage-judges-fixed>`.


.. toctree::
    :maxdepth: 3
    :hidden:

    Examples/Index
    Judges/Conditional
    Judges/Fixed
    Verdicts/Redirect
