.. include:: ../../../Includes.txt

.. _functions-assignLanguage-judges-fixed:

===========
Fixed Judge
===========

The fixed judge always speaks a firmly defined judgment. It should have the lowest priority of all judges since it should only be
called if no other judge comes to a verdict.

.. _functions-assignLanguage-judges-fixed-example:

Example
=======

.. code-block:: typoscript

   config.tx_locate.judges {

       # other judges should be called prior to the fixed judge

       999999 = Leuchtfeuer\Locate\Judge\Fixed
       999999.verdict = redirectToPageEN
   }
