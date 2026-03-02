<?php

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Store;

class SessionStore
{
    public const string SESSION_BASE_NAME = 'tx_locate_';

    protected string $sessionBaseName;

    public function __construct(string $sessionBaseName = self::SESSION_BASE_NAME)
    {
        $this->sessionBaseName = $sessionBaseName === '' ? self::SESSION_BASE_NAME : $sessionBaseName;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->initSession();

        return $_SESSION[$this->getSessionKeyName($key)] ?? $default;
    }

    public function set(string $key, mixed $value): void
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
        if (session_status() !== \PHP_SESSION_ACTIVE) {
            $sessionId = $_COOKIE[session_name()] ?? null;
            if ($sessionId !== null && !preg_match('/^[a-zA-Z0-9,-]{22,250}$/', $sessionId)) {
                // The session ID in the header is invalid, create a new one
                setcookie(session_name(), '', time() - 3600, '/');
                unset($_COOKIE[session_name()]);
            }
            session_start();
        }
    }
}
