.. include:: ../Includes.txt

.. _developing:

==============
For Developers
==============

.. _developing-addFact:

Add a Fact
==========

This example will show you how to add a custom fact that looks up some environment variables.

.. rst-class:: bignums-xxl

1. Add your own FactProvider

   Your fact provider has to extend the abstract class :php:`Leuchtfeuer\Locate\FactProvider\AbstractFactProvider`. The value of
   the :php:`PROVIDER_NAME` constant has to match the TypoScript key you will use for your fact.

   .. code-block:: php

      class Environment extends AbstractFactProvider
      {
          const PROVIDER_NAME = 'environment';

          public function getBasename(): string
          {
              return self::PROVIDER_NAME;
          }

          public function process(): self
          {
              foreach (GeneralUtility::getIndpEnv('_ARRAY') as $key => $value) {
                  $this->facts[$this->getFactPropertyName($key)] = $value;
              }

              return $this;
          }

          public function isGuilty($prosecution): bool
          {
              $subject = array_keys($prosecution);
              $subject = array_shift($subject);
              $value = $prosecution[$subject];
              LocateUtility::mainstreamValue($subject);

              return ($this->getSubject()[$subject] ?? false) == $value;
          }
      }


2. Register fact via TypoScript:

   Next, you need to register this PHP class as a fact in TypoScript.

   .. code-block:: typoscript

      config.tx_locate.facts {
          environment = Leuchtfeuer\Locate\FactProvider\Environment
      }

3. Setup a Judge

   Now you are all set and use your fact in a judge. Here is an example that will redirect the user to the english page if the
   HTTP host matches `www.Leuchtfeuer.com`:

   .. code-block:: typoscript

      config.tx_locate.judges {
          10 = Leuchtfeuer\Locate\Judge\Condition
          100 {
              verdict = redirectToPageEN
              fact = environment
              prosecution {
                  HTTP_HOST = www.Leuchtfeuer.com
              }
          }
      }

.. _developing-addVerdict:

Add a Verdict
=============

This example will show you how to add a custom verdict that adds some data to the user session.

.. rst-class:: bignums-xxl

1. Add your own verdict class

   Your verdict has to extend the abstract class :php:`Leuchtfeuer\Locate\Verdict\AbstractVerdict`.

   .. code-block:: php

      class StoreSessionData extends AbstractVerdict
      {
          public function execute(): ?ResponseInterface
          {
              $sessionStore = new \Leuchtfeuer\Locate\Store\SessionStore('dummy');
              $sessionStore->set('foo', $this->configuration['foo']);

              return null;
          }
      }

2. Register the verdict in TypoScript

   Now you can register this verdict in your TypoScript setup and add a configuration key `foo` that contains the data that should
   be stored in the session.

   .. code-block:: typoscript

      config.tx_locate.verdicts {
          storeSessionData = Vendor\Extension\Verdict\StoreSessionData
          storeSessionData {
              foo = bar
          }
      }
