.. include:: ../../../Includes.txt

.. _functions-assignLanguage-judges-conditional:

=================
Conditional Judge
=================

The conditional judge makes his judgement based on the configured fact. Only if the prosecution is true, the verdict is
pronounced. Otherwise, the next judge tries to pass verdict.

.. _functions-assignLanguage-judges-conditional-configuration:

Configuration
=============

.. ### BEGIN~OF~TABLE ###

.. _functions-assignLanguage-judges-conditional-configuration-verdict:

Verdict
~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.judges.[0].verdict
   Data type
         string
   Default
         unset
   Description
         The name of the verdict to be enforced.

.. _functions-assignLanguage-judges-conditional-configuration-fact:

Fact
~~~~
.. container:: table-row

   Property
         config.tx_locate.judges.[0].fact
   Data type
         string
   Default
         unset
   Description
         The name of the fact to be checked.

.. _functions-assignLanguage-judges-conditional-configuration-prosecution:

Prosecution
~~~~~~~~~~~
.. container:: table-row

   Property
         config.tx_locate.judges.[0].prosecution
   Data type
         string
   Default
         unset
   Description
         The Prosecution. Only if this value is present in the fact, a verdict can be pronounced.

.. _functions-assignLanguage-judges-conditional-example:

Example
=======

.. code-block:: typoscript

   config.tx_locate.judges {
       100 = Leuchtfeuer\Locate\Judge\Condition
       100 {
           verdict = redirectToPageDE
           fact = browserAcceptLanguage
           prosecution = de
       }
   }
