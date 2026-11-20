<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Team YD <dev@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Utility;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LocateUtility
{
    private const IPV4_MAPPED_PREFIX = "\0\0\0\0\0\0\0\0\0\0\xff\xff";

    private const WORD_FACTOR = 2 ** 32;

    /**
     * Check the IP in the geoip table and returns iso 2 code for the current remote address
     *
     * @param string|null $ip
     * @return bool|string
     * @throws Exception
     */
    public function getCountryIso2FromIP(?string $ip = null): bool|string
    {
        $ip = $this->getNumericIp($ip);
        if ($ip === false) {
            return false;
        }
        $tableName = $this->getTableNameForIp($ip);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        // Only the last range starting at or below the IP can contain it. Reading it backwards along idx_ip_from
        // touches a single row, while a condition on both ip_from and ip_to results in a (full) range scan.
        $range = $queryBuilder
            ->select('country_code', 'ip_to')
            ->from($tableName)
            ->where($queryBuilder->expr()->lte('ip_from', $queryBuilder->createNamedParameter($ip)))
            ->orderBy('ip_from', 'DESC')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if ($range === false || $this->compareNumericStrings((string)$range['ip_to'], $ip) < 0) {
            return false;
        }

        return $range['country_code'];
    }

    public function getNumericIp(?string $ip = null): string|bool
    {
        $ip = $ip ?? $this->getRemoteAddress();

        $binaryIp = inet_pton($ip);
        if ($binaryIp === false) {
            return false;
        }

        // IPv4-mapped IPv6 addresses (::ffff:192.0.2.1, e.g. from dual-stack sockets) are looked up as IPv4
        if (str_starts_with($binaryIp, self::IPV4_MAPPED_PREFIX)) {
            $binaryIp = substr($binaryIp, strlen(self::IPV4_MAPPED_PREFIX));
        }

        return $this->convertBinaryToDecimal($binaryIp);
    }

    /**
     * The client address as determined by TYPO3. Behind a reverse proxy, TYPO3 takes it from X-Forwarded-For once
     * $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'] and ['reverseProxyHeaderMultiValue'] are configured.
     * Reading the header directly would let every visitor choose the country to be located in.
     */
    protected function getRemoteAddress(): ?string
    {
        return (string)GeneralUtility::getIndpEnv('REMOTE_ADDR');
    }

    /**
     * Converts a binary IP address as returned by inet_pton() (4 bytes for IPv4, 16 bytes for IPv6) into its number,
     * written as a decimal string: 192.168.178.1 becomes '3232281089'. The IP tables store the same numbers.
     *
     * Why a string: IPv6 numbers reach 2^128 - 1, a number with 39 digits, far beyond PHP_INT_MAX (2^63 - 1). Floats
     * hold only about 16 significant digits, so neighbouring IPv6 addresses would end up as the same, wrong number and
     * be located in the wrong range. gmp or bcmath calculate exactly, but are optional PHP extensions. This conversion
     * is exact without them, and the tests cover the same code in every environment.
     *
     * How: the address is read as a number with base 2^32, one 32-bit word per digit, most significant word first.
     * Horner's method builds the number word by word: ((w1 * 2^32 + w2) * 2^32 + w3) * 2^32 + w4. Each multiplication
     * by 2^32 runs on the decimal digits of the intermediate result, from right to left with a carry, like long
     * multiplication on paper.
     *
     * Why 32-bit words instead of single bytes: an IPv4 address takes 1 round instead of 4, an IPv6 address 4 rounds
     * instead of 16. As the intermediate result grows by about 10 digits per round, this saves most of the work.
     *
     * Requires 64-bit PHP: only there unpack('N') returns the words unsigned, and the intermediate values in the loop
     * need up to 36 bits.
     */
    private function convertBinaryToDecimal(string $binary): string
    {
        $decimal = '0';

        // 'N*' splits the binary string into unsigned 32-bit integers in big-endian byte order, which is the order of
        // the bytes from inet_pton(). The length is always a multiple of 4, so no byte is left out.
        foreach (unpack('N*', $binary) as $word) {
            // Calculates $decimal * 2^32 + $word. Starting with the word as carry adds it on the way, as the carry
            // enters the last digit first.
            $carry = $word;
            $digits = '';

            for ($position = strlen($decimal) - 1; $position >= 0; $position--) {
                // The carry is always below 2^32: it starts as a word, and intdiv() below keeps it there, since
                // (9 * 2^32 + 2^32 - 1) / 10 < 2^32. So $value stays below 10 * 2^32 (about 4.3e10), an exact integer.
                $value = (int)$decimal[$position] * self::WORD_FACTOR + $carry;
                // The last decimal digit of $value is the new digit at this position ...
                $digits = ($value % 10) . $digits;
                // ... and the rest moves on to the next position to the left.
                $carry = intdiv($value, 10);
            }

            // What remains of the carry exceeds the existing digits and is prepended as a whole number, the way the
            // last carry of a long multiplication is written in front of the result. ltrim() removes the leading zero
            // of a carry of 0; for the address 0 (e.g. '::'), nothing would be left, so it falls back to '0'.
            $decimal = ltrim($carry . $digits, '0') ?: '0';
        }

        return $decimal;
    }

    public static function mainstreamValue(string &$value): void
    {
        $value = mb_strtolower(str_replace('-', '_', $value));
    }

    /**
     * Compares two non-negative integers given as decimal strings. IPv6 numbers exceed PHP_INT_MAX.
     */
    private function compareNumericStrings(string $a, string $b): int
    {
        $a = ltrim($a, '0');
        $b = ltrim($b, '0');

        return strlen($a) <=> strlen($b) ?: strcmp($a, $b) <=> 0;
    }

    protected function getTableNameForIp(string $ip): string
    {
        return strlen($ip) > 10 ? 'static_ip2country_v6' : 'static_ip2country_v4';
    }
}
