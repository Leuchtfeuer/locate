<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Store;

class SessionStore
{
    const SESSION_BASE_NAME = 'tx_locate_';

    protected $sessionBaseName;

    public function __construct(string $sessionBaseName = self::SESSION_BASE_NAME)
    {
        $this->sessionBaseName = $sessionBaseName === '' ? self::SESSION_BASE_NAME : $sessionBaseName;
    }

    public function get(string $key, $default = null)
    {
        $this->initSession();

        return $_SESSION[$this->getSessionKeyName($key)] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->initSession();
        $_SESSION[$this->getSessionKeyName($key)] = $value;
    }

    public function getSessionKeyName(string $key): string
    {
        return $this->sessionBaseName . $key;
    }

    public function delete(string $key): void
    {
        $this->initSession();
        unset($_SESSION[$this->getSessionKeyName($key)]);
    }

    private function initSession(): void
    {
        if (session_id() === '') {
            session_start();
        }
    }
}
