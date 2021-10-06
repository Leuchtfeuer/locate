Language Utilities for TYPO3
============================

[![Latest Stable Version](https://poser.pugx.org/leuchtfeuer/locate/v/stable)](https://packagist.org/packages/leuchtfeuer/locate)
[![Build Status](https://github.com/Leuchtfeuer/locate/workflows/Continous%20Integration/badge.svg)](https://github.com/Leuchtfeuer/locate/actions)
[![Total Downloads](https://poser.pugx.org/leuchtfeuer/locate/downloads)](https://packagist.org/leuchtfeuer/locate)
[![Latest Unstable Version](https://poser.pugx.org/leuchtfeuer/locate/v/unstable)](https://packagist.org/leuchtfeuer/locate)
[![Code Climate](https://codeclimate.com/github/Leuchtfeuer/locate/badges/gpa.svg)](https://codeclimate.com/github/Leuchtfeuer/locate)
[![codecov](https://codecov.io/gh/Leuchtfeuer/locate/branch/master/graph/badge.svg?token=0GcE422Ms1)](https://codecov.io/gh/Leuchtfeuer/locate)
[![License](https://poser.pugx.org/leuchtfeuer/locate/license)](https://packagist.org/packages/leuchtfeuer/locate)

This TYPO3 extension provides some functions to **assign a suitable language** version of your website to the website user or to 
**deny access to configurable pages** in configurable countries (geo blocking).

The full documentation for the latest releases can be found [here](https://docs.typo3.org/p/leuchtfeuer/locate/master/en-us/).

## Requirements

We are currently supporting following TYPO3 versions:<br><br>

| Extension Version | TYPO3 v11 Support | TYPO3 v10 Support | TYPO3 v9 Support | TYPO3 v8 Support |
| :-: | :-: | :-: | :-: | :-: |
| 11.x              | x                 | x                | -                | -                |
| 10.x              | -                 | x                | x                | -                |
| 9.x               | -                 | -                | x                | x
| 8.x               | -                 | -                | -                | x

### IPv6 Support

For an accurate IPv6 support, your PHP needs to support either `gmp` or `bcmath`. It also has to be compiled  without the 
`--disable-ipv6` option. The determination of IP addresses is also possible without these packages, but it is less precise.

## Contributing

You can contribute by making a **pull request** to the master branch of this repository. Or just send us some **beers**...

---
This site or product includes IP2Location LITE data available from [https://lite.ip2location.com/](https://lite.ip2location.com/).

