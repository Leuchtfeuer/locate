<?php
declare(strict_types=1);
namespace Bitmotion\Locate\Tools;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @deprecated
 */
abstract class IP
{
    /**
     * Match IP number with list of numbers with wildcard
     * Dispatcher method for switching into specialised IPv4 and IPv6 methods.
     *
     * @param    string $baseIP is the current remote IP address for instance, typ. REMOTE_ADDR
     * @param    string $list is a comma-list of IP-addresses to match with. *-wildcard allowed instead of number, plus leaving out parts in the IP number is accepted as wildcard (eg. 192.168.*.* equals 192.168). If list is "*" no check is done and the function returns TRUE immediately. An empty list always returns FALSE.
     * @return    bool        True if an IP-mask from $list matches $baseIP
     * @deprecated
     */
    public static function compare(string $baseIP, string $list): bool
    {
        trigger_error('Calling IP::compare() is deprecated.', E_USER_DEPRECATED);

        $list = trim($list);
        if ($list === '') {
            return false;
        } elseif ($list === '*') {
            return true;
        }
        if (strpos($baseIP, ':') !== false && self::isValidIPv6($baseIP)) {
            return self::compareIPv6($baseIP, $list);
        }

        return self::compareIPv4($baseIP, $list);
    }

    /**
     * Validate a given IP address to the IPv6 address format.
     *
     * Example for possible format:  43FB::BB3F:A0A0:0 | ::1
     *
     * @param    string        IP address to be tested
     * @return    bool        True if $ip is of IPv6 format.
     * @deprecated
     */
    public static function isValidIPv6(string $ip): bool
    {
        trigger_error('Calling IP::isValidIPv6() is deprecated.', E_USER_DEPRECATED);

        $uppercaseIP = strtoupper($ip);

        $regex = '/^(';
        $regex .= '(([\dA-F]{1,4}:){7}[\dA-F]{1,4})|';
        $regex .= '(([\dA-F]{1,4}){1}::([\dA-F]{1,4}:){1,5}[\dA-F]{1,4})|';
        $regex .= '(([\dA-F]{1,4}:){2}:([\dA-F]{1,4}:){1,4}[\dA-F]{1,4})|';
        $regex .= '(([\dA-F]{1,4}:){3}:([\dA-F]{1,4}:){1,3}[\dA-F]{1,4})|';
        $regex .= '(([\dA-F]{1,4}:){4}:([\dA-F]{1,4}:){1,2}[\dA-F]{1,4})|';
        $regex .= '(([\dA-F]{1,4}:){5}:([\dA-F]{1,4}:){0,1}[\dA-F]{1,4})|';
        $regex .= '(::([\dA-F]{1,4}:){0,6}[\dA-F]{1,4})';
        $regex .= ')$/';

        return preg_match($regex, $uppercaseIP) ? true : false;
    }

    /**
     * Match IPv6 address with a list of IPv6 prefixes
     *
     * @param    string $baseIP is the current remote IP address for instance
     * @param    string $list is a comma-list of IPv6 prefixes, could also contain IPv4 addresses
     * @return    bool        True if an baseIP matches any prefix
     * @deprecated
     */
    public static function compareIPv6(string $baseIP, string $list): bool
    {
        trigger_error('Calling IP::compareIPv6() is deprecated.', E_USER_DEPRECATED);

        $success = false;    // Policy default: Deny connection
        $baseIP = self::normalizeIPv6($baseIP);

        $values = GeneralUtility::trimExplode(',', $list);
        foreach ($values as $test) {
            list($test, $mask) = explode('/', $test);
            if (self::isValidIPv6($test)) {
                $test = self::normalizeIPv6($test);
                if (intval($mask)) {
                    switch ($mask) {    // test on /48 /64
                        case '48':
                            $testBin = substr(self::IPv6Hex2Bin($test), 0, 48);
                            $baseIPBin = substr(self::IPv6Hex2Bin($baseIP), 0, 48);
                            $success = strcmp($testBin, $baseIPBin) == 0 ? true : false;
                            break;
                        case '64':
                            $testBin = substr(self::IPv6Hex2Bin($test), 0, 64);
                            $baseIPBin = substr(self::IPv6Hex2Bin($baseIP), 0, 64);
                            $success = strcmp($testBin, $baseIPBin) == 0 ? true : false;
                            break;
                        default:
                            $success = false;
                    }
                } else {
                    if (self::isValidIPv6($test)) {    // test on full ip address 128 bits
                        $testBin = self::IPv6Hex2Bin($test);
                        $baseIPBin = self::IPv6Hex2Bin($baseIP);
                        $success = strcmp($testBin, $baseIPBin) == 0 ? true : false;
                    }
                }
            }
            if ($success) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize an IPv6 address to full length
     * @deprecated
     */
    public static function normalizeIPv6(string $address): string
    {
        trigger_error('Calling IP::normalizeIPv6() is deprecated.', E_USER_DEPRECATED);

        $normalizedAddress = '';

        $chunks = explode('::', $address);    // Count 2 if if address has hidden zero blocks
        if (count($chunks) == 2) {
            $chunksLeft = explode(':', $chunks[0]);
            $chunksRight = explode(':', $chunks[1]);
            $left = count($chunksLeft);
            $right = count($chunksRight);

            // Special case: leading zero-only blocks count to 1, should be 0
            if ($left == 1 && strlen($chunksLeft[0]) == 0) {
                $left = 0;
            }

            $hiddenBlocks = 8 - ($left + $right);
            $hiddenPart = '';
            $h = 0;
            while ($h < $hiddenBlocks) {
                $hiddenPart .= '0000:';
                $h++;
            }

            if ($left == 0) {
                $stageOneAddress = $hiddenPart . $chunks[1];
            } else {
                $stageOneAddress = $chunks[0] . ':' . $hiddenPart . $chunks[1];
            }
        } else {
            $stageOneAddress = $address;
        }

        // Normalize the blocks:
        $blocks = explode(':', $stageOneAddress);
        $divCounter = 0;
        foreach ($blocks as $block) {
            $tmpBlock = '';
            $i = 0;
            $hiddenZeros = 4 - strlen($block);
            while ($i < $hiddenZeros) {
                $tmpBlock .= '0';
                $i++;
            }
            $normalizedAddress .= $tmpBlock . $block;
            if ($divCounter < 7) {
                $normalizedAddress .= ':';
                $divCounter++;
            }
        }

        return $normalizedAddress;
    }

    /**
     * Decode hex v6 IP
     *
     * @param    string $hex IPv6 in hex format
     * @deprecated
     */
    public static function IPv6Hex2Bin(string $hex): string
    {
        trigger_error('Calling IP::IPv6Hex2Bin() is deprecated.', E_USER_DEPRECATED);

        $bin = '';
        $hex = str_replace(':', '', $hex);    // Replace colon to nothing

        for ($i = 0; $i < strlen($hex); $i = $i + 2) {
            $bin .= chr(hexdec(substr($hex, $i, 2)));
        }

        return $bin;
    }

    /**
     * Match IPv4 number with list of numbers with wildcard
     *
     * @param    string $baseIP is the current remote IP address for instance, typ. REMOTE_ADDR
     * @param    string $list is a comma-list of IP-addresses to match with. *-wildcard allowed instead of number, plus leaving out parts in the IP number is accepted as wildcard (eg. 192.168.*.* equals 192.168)
     * @return    bool        True if an IP-mask from $list matches $baseIP
     * @deprecated
     */
    public static function compareIPv4(string $baseIP, string $list): bool
    {
        trigger_error('Calling IP::compareIPv4() is deprecated.', E_USER_DEPRECATED);

        $IPpartsReq = explode('.', $baseIP);
        if (count($IPpartsReq) == 4) {
            $values = GeneralUtility::trimExplode(',', $list);

            foreach ($values as $test) {
                list($test, $mask) = explode('/', $test);

                if (intval($mask)) {
                    // "192.168.3.0/24"
                    $lnet = ip2long($test);
                    $lip = ip2long($baseIP);
                    $binnet = str_pad(decbin($lnet), 32, '0', 'STR_PAD_LEFT');
                    $firstpart = substr($binnet, 0, $mask);
                    $binip = str_pad(decbin($lip), 32, '0', 'STR_PAD_LEFT');
                    $firstip = substr($binip, 0, $mask);
                    $yes = (strcmp($firstpart, $firstip) == 0);
                } else {
                    // "192.168.*.*"
                    $IPparts = explode('.', $test);
                    $yes = 1;
                    foreach ($IPparts as $index => $val) {
                        $val = trim($val);
                        if (strcmp($val, '*') && strcmp($IPpartsReq[$index], $val)) {
                            $yes = 0;
                        }
                    }
                }
                if ($yes) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validate a given IP address.
     *
     * Possible format are IPv4 and IPv6.
     *
     * @param    string        IP address to be tested
     * @return    bool        True if $ip is either of IPv4 or IPv6 format.
     * @deprecated
     */
    public static function isValid(string $ip): bool
    {
        trigger_error('Calling IP::isValid() is deprecated.', E_USER_DEPRECATED);

        if (strpos($ip, ':') === false) {
            return self::isValidIPv4($ip);
        }

        return self::isValidIPv6($ip);
    }

    /**
     * Validate a given IP address to the IPv4 address format.
     *
     * Example for possible format:  10.0.45.99
     *
     * @param    string        IP address to be tested
     * @return    bool        True if $ip is of IPv4 format.
     * @deprecated
     */
    public static function isValidIPv4(string $ip): bool
    {
        trigger_error('Calling IP::isValidIPv4() is deprecated.', E_USER_DEPRECATED);

        $parts = explode('.', $ip);
        if (count($parts) == 4 &&
            self::_testInt($parts[0]) && $parts[0] >= 1 && $parts[0] < 256 &&
            self::_testInt($parts[1]) && $parts[0] >= 0 && $parts[0] < 256 &&
            self::_testInt($parts[2]) && $parts[0] >= 0 && $parts[0] < 256 &&
            self::_testInt($parts[3]) && $parts[0] >= 0 && $parts[0] < 256
        ) {
            return true;
        }

        return false;
    }

    /**
     * Internal
     * Tests if the input is an integer.
     *
     * @param    mixed $var        Any input variable to test.
     * @return    bool        Returns true if string is an integer
     * @deprecated
     */
    protected static function _testInt($var): bool
    {
        trigger_error('Calling IP::_testInt() is deprecated.', E_USER_DEPRECATED);

        return !strcmp($var, intval($var));
    }

    /**
     * Match fully qualified domain name with list of strings with wildcard
     * @deprecated
     */
    public static function compareFQDN(string $baseIP, string $list): bool
    {
        trigger_error('Calling IP::compareFQDN() is deprecated.', E_USER_DEPRECATED);

        if (count(explode('.', $baseIP)) == 4) {
            $resolvedHostName = explode('.', gethostbyaddr($baseIP));
            $values = GeneralUtility::trimExplode(',', $list, 1);

            foreach ($values as $test) {
                $hostNameParts = explode('.', $test);
                $yes = 1;

                foreach ($hostNameParts as $index => $val) {
                    $val = trim($val);
                    if (strcmp($val, '*') && strcmp($resolvedHostName[$index], $val)) {
                        $yes = 0;
                    }
                }
                if ($yes) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @deprecated
     */
    public static function isLocal(string $ip): bool
    {
        trigger_error('Calling IP::isLocal() is deprecated.', E_USER_DEPRECATED);

        if (!self::isValidIPv4($ip)) {
            return false;
        }

        $ips1 = [];
        $ips2 = [];

        // get local ip prefix list
        // 1.0.0.0 - 10.255.255.255
        for ($i = 0; $i < 256; $i++) {
            $ips1[$i] = '1.' . $i;
        }

        // 172.16.0.0 - 172.31.255.255
        for ($i = 16; $i < 32; $i++) {
            $ips2[$i] = '172.' . $i;
        }

        // 192.168.0.0 - 192.168.255.255 & 127.0.0.1 (localhost)
        $ips3 = ['192.168', '127.0'];

        // array for all local ip prefix list
        $local_ips = array_merge($ips1, $ips2, $ips3);

        // get prefix
        $ip = explode('.', $ip);
        $ip = $ip[0] . '.' . $ip[1];

        if (in_array($ip, $local_ips)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the current remote address as long
     * @deprecated
     */
    public static function getUserIpAsLong(): int
    {
        trigger_error('Calling IP::getUserIpAsLong() is deprecated.', E_USER_DEPRECATED);

        return (int)sprintf('%u', ip2long(GeneralUtility::getIndpEnv('REMOTE_ADDR')));
    }

    /**
     * Returns the current remote address
     * @deprecated
     */
    public static function getUserIp(): string
    {
        trigger_error('Calling IP::getUserIp() is deprecated.', E_USER_DEPRECATED);

        return GeneralUtility::getIndpEnv('REMOTE_ADDR');
    }
}
