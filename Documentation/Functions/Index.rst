.. include:: ../Includes.txt

.. _functions:

=========
Functions
=========

.. _functions-assignWebsiteVersionToUser:

Assign Website Version to User
==============================

You can redirect a user to a specific page or page translation based on certain criteria (browser language, IP address, ...).
There is also the possibility to save this decision, so that the criteria are not evaluated on every page request.

.. _functions-restrictAccessToPages:

Restrict Access to Pages
========================

You can block pages (and page translations) for access from certain countries. Your server will respond with an 451 HTTP status
code when the page is not available in your country.


.. toctree::
    :maxdepth: 3
    :hidden:

    AssignLanguage/Index
    GeoBlocking/Index
