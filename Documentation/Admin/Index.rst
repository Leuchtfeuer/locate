.. include:: ../Includes.txt

.. _admin::

==================
For Administrators
==================

.. _admin-installation:

Installation
============

There are several ways to require and install this extension. We recommend to get this extension via
`composer <https://getcomposer.org/>`__.

.. _admin-installation-composer:

Via Composer
------------

If your TYPO3 instance is running in composer mode, you can simply require the extension by running:

.. code-block:: bash

   composer req leuchtfeuer/locate

.. _admin-installation-extensionManager:

Via Extension Manager
---------------------

Open the extension manager module of your TYPO3 instance and select "Get Extensions" in the select menu above the upload
button. There you can search for `locate` and simply install the extension. Please make sure you are using the latest
version of the extension by updating the extension list before installing the locate extension.

.. _admin-installation-zipFile:

Via ZIP File
------------

You need to download the locate extension from the `TYPO3 Extension Repository <https://extensions.typo3.org/extension/locate/>`__
and upload the zip file to the extension manager of your TYPO3 instance and activate the extension afterwards.

.. important::

   Please make sure to include all TypoScript files.

.. important::

   For an more accurate IPv6 support, your PHP needs to support either :code:`gmp` or :code:`bcmath`. It also has to be compiled
   without the :code:`--disable-ipv6` option. The determination of IP addresses is also possible without these packages, but it
   is less precise.

.. _admin-additionalPackages:

Additional Packages
===================

.. _admin-additionalPackages-staticInfoTables:

Static Info Tables
------------------

If you want to use the geo blocking feature for your pages, you need to to install the
`static info tables <https://extensions.typo3.org/extension/static_info_tables/>`__ extension as well. It is enough to install
just the basic version. Additional country-specific versions are not required by this extension. If you are using a composer setup
you can execute following command:

.. code-block:: bash

   composer req sjbr/static-info-tables

.. _admin-additionalPackages-detectCrawler:

Detect Crawler
--------------

If you want to exclude bots such as the Google search bot from being assigned specific website versions, you must require the
composer package `jaybizzle/crawler-detect <https://github.com/JayBizzle/Crawler-Detect>`__. This feature is only available in
composer setups. If your TYPO3 is not running in composer mode, you have to provide this functions on your own.

.. code-block:: bash

   composer req jaybizzle/crawler-detect

.. _admin-updatingIPDatabase:

Updating the IP Database
========================

We try to update the supplied IP database every quarter. For this update we provide a new patchlevel release of this extension.
After this new version has been installed, you can update your local database via the Extension Manager module in your TYPO3
backend as shown below.

.. figure:: ../Images/update-ip-database.png
   :alt: Update the provided IP database
   :class: with-shadow

   You can update your local IP tables via the Extension Manager module.

.. _admin-enablingExtension:

Enabling this Extension
=======================

If you want to activate the :ref:`language assignment <functions-assignLanguage>`, you have to add the following TypoScript
line after you have installed locate and included the TypoScript. This function is disabled by default.

.. code-block:: typoscript

   config.tx_locate = 1

If you do not want to activate the language assignment on every page, you can simply put the activation into a condition.

.. code-block:: typoscript

   [page["uid"] == 1]
       config.tx_locate = 1
   [end]
