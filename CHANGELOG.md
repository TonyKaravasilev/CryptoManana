CHANGELOG
=========

v0.8.0, 2020-01-26
------------------
- Added the ability to generate asymmetric key pairs for RSA/DSA at the TokenGenerator class;
- Added a the standardized RSA asymmetric encryption algorithm objects for securing data;
- Added unit tests for the new RSA asymmetric encryption algorithm object realizations;
- Reused the DataEncryptionInterface for both symmetric and asymmetric encryption algorithms;
- Updated the low-level CLI script for checking of the PHP requirements for the framework;
- Updated the Composer JSON schema with new keywords and information;
- Generated an online DOI number via Zenodo and CERN integration;
- Fixed a few simple typos at existing PHPDoc comments;
- Purged the image caches for the GitHub repository;
- Release is signed with a GitHub GPG signature.

v0.7.0, 2020-01-21
------------------
- Added a few standardized symmetric encryption algorithm objects for securing data;
- Added unit tests for the new symmetric encryption algorithm object realizations;
- Added a SymmetricCipherFactory for easier block cipher algorithm object instancing;
- Added a HashAlgorithmFactory for easier hash algorithm object instancing;
- Added a unit test for the SymmetricCipherFactory object testing;
- Added a unit test for the HashAlgorithmFactory object testing;
- Component improvements for encodings that use more than 8 bits per character (UTF-16/UTF-32);
- Updated the low-level CLI script for checking of the PHP requirements for the framework;
- Updated the Composer JSON schema with new keywords and information;
- Generated an online DOI number via Zenodo and CERN integration;
- Updated the configuration of Travis CI and of Scrutinizer CI;
- Fixed a few simple typos at existing PHPDoc comments;
- Purged the image caches for the GitHub repository;
- Release is signed with a GitHub GPG signature.

v0.6.0, 2020-01-15
------------------
- Added a few standardized password-based derivation algorithm (PBKDF) objects for digest generation;
- Added unit tests for all newly added password-based derivation algorithm object realizations;
- Added a PBKDF2-SHA-3 native realization for older versions of PHP or ext-hash;
- Added a unit test for the NativePbkdf2Sha3 polyfill object realization;
- Updated the PHP 5.x/7.x backward compatible snippet, autoloaded via Composer;
- Updated the low-level CLI script for checking of the PHP requirements for the framework;
- Added a digest verification feature for all keyed and password-based derivation objects;
- Added repetitive hashing capabilities for unkeyed, keyed and HKDF hash algorithm objects;
- Improved the performance of the HMAC-SHA-3 native realization by reducing function calls;
- Fixed the wrong algorithm realization for the HkdfWhirlpool class;
- Updated the Composer JSON schema with new keywords and information;
- Generated an online DOI number via Zenodo and CERN integration;
- Fixed a few simple typos at existing PHPDoc comments;
- Purged the image caches for the GitHub repository;
- Release is signed with a GitHub GPG signature.

v0.5.0, 2019-12-18
------------------
- Added a few standardized key material derivation algorithm (HKDF) objects for digest generation;
- Added unit tests for all newly added key material derivation algorithm object realizations;
- Added a HKDF-SHA-3 native realization for older versions of PHP or ext-hash;
- Added a unit test for the NativeHkdfSha3 polyfill object realization;
- Improved some of the existing unit tests for hash algorithms objects;
- Fixed an OpCache and performance problem for the StringBuilder class;
- Switched the framework testing to PHP 7.4 as the main build version;
- Fixed compatibility issues with PHP 7.4 and PHP 8.0 features;
- Updated the configuration of Travis CI and of Scrutinizer CI;
- Generated an online DOI number via Zenodo and CERN integration;
- Fixed a few simple typos at existing PHPDoc comments;
- Purged the image caches for the GitHub repository;
- Release is signed with a GitHub GPG signature.

v0.4.0, 2019-12-14
------------------
- Added salting capabilities when hashing files for all digest algorithms and updated the unit tests;
- Added a few standardized keyed hash algorithm (HMAC) objects for digest generation;
- Added unit tests for all newly added keyed hash algorithm object realizations;
- Added a HMAC-SHA-3 native realization for older versions of PHP or ext-hash;
- Added a unit test for the NativeHmacSha3 polyfill object realization;
- Improved the code reuse in some classes that define the same methods;
- Generated an online DOI number via Zenodo and CERN integration;
- Fixed a few simple typos at existing PHPDoc comments;
- Purged the image caches for the GitHub repository;
- Release is signed with a GitHub GPG signature.

v0.3.0, 2019-12-11
------------------
- Changed the creation location of all temporary files related to unit testing;
- Added an abstraction for the framework’s representation of hash algorithms;
- Added a few standardized unkeyed hash algorithm objects for digest generation;
- Added unit tests for all newly added hash algorithm object realizations;
- Added a SHA-3 native realization for older versions of PHP or ext-hash;
- Added a unit test for the NativeSha3 polyfill object realization;
- Generated an online DOI number via Zenodo and CERN integration;
- Updated the configuration of Travis CI and of Scrutinizer CI;
- Updated the project's description at the README.md file;
- Fixed a few simple typos at existing PHPDoc comments;
- Purged the image caches for the GitHub repository;
- Release is signed with a GitHub GPG signature.

v0.2.4, 2019-06-23
------------------
- Updated the configuration of Travis CI and of Scrutinizer CI;
- Configured testing with and without Zend OPcache for CI checks.

v0.2.3, 2019-06-22
------------------
- Fixed compatibility issues with PHP 7.4 and PHP 8.0 features;
- Updated the configuration of Travis CI to use the valid build names.

v0.2.2, 2019-04-25
------------------

- Improved the performance of the unit test for the QuasiRandom object;
- Fixed the wrong timestamp year information at all changelog records;
- Updated the Composer JSON schema with new keywords and information;
- Fixed a few simple typos at existing PHPDoc comments.

v0.2.1, 2019-04-14
------------------

- Updated all PHPDoc comments to follow the correct structure of the PSR-5 standard;
- Updated the attribute list for Git when exporting or downloading the project;
- Updated the CLI check script for the framework requirements scanning;
- Updated the DOI badge to point to the overall concept reference;
- Updated the citation information at the README.md file;
- Reviewed and profiled all the framework objects.

v0.2.0, 2019-04-02
------------------

- Added an abstract dependency injection container for randomness services;
- Added a data shuffler object that can inject any randomness generator service;
- Added an element picker object that can inject any randomness generator service;
- Added a token generator object that can inject any randomness generator service;
- Added unit tests for the DataShuffler, the ElementPicker and the TokenGenerator objects;
- Updated the polyfill/compatibility logic for disabling via constant definition;
- Updated the README.md information about the project installation and usage;
- Generated an online DOI number via Zenodo and CERN integration;
- Updated the configuration of Travis CI and of Scrutinizer CI;
- Purged the image caches for the GitHub repository;
- Release is signed with a GitHub GPG signature.

v0.1.10, 2019-03-31
------------------

- Updated the installation information at the README.md file;
- Created a documentation website for the project's usage;
- Updated the XML configuration for PHP Documentor;
- Added more badges to the README.md file;
- Created a staging branch for tests.

v0.1.9, 2019-03-30
------------------
- Added a RandomnessFactory for easier data generator instancing;
- Added a unit test for the RandomnessFactory object.

v0.1.8, 2019-03-30
------------------

- Added four new framework exceptions for easier error handling and usage;
- Added unit tests for all the new framework exception objects;
- Added the new exceptions to the ExceptionFactory object;
- Updated the unit test for the ExceptionFactory class.

v0.1.7, 2019-03-29
------------------

- Updated the PHP 5.x/7.x backward compatible snippet, autoloaded via Composer;
- Added a low-level CLI check of the PHP requirements for CryptoManana;
- Updated the configuration of Travis CI to use the new CLI script.

v0.1.6, 2019-03-29
------------------

- Improved the unit test abstraction and added temporary file manipulation methods;
- Updated the configuration of Travis CI to fetch with Git depth of 5 commits;
- Updated the testing settings for PHPUnit, CodeCoverage and PHP Documentor;
- Added a few new development Composer script aliases.

v0.1.5, 2019-03-29
------------------

- Added an interface for the StringBuilder Singleton to enforce the substitution principle;
- Improved the seeding capabilities of the PseudoRandom and QuasiRandom objects.

v0.1.4, 2019-03-28
------------------

- Reduced the complexity of the AbstractGenerator class via traits;
- Improved the code reuse in some of the longest methods.

v0.1.3, 2019-03-25
------------------

- Updated the coverage export formats for PHPUnit and CodeCoverage;
- Updated the configuration of Travis CI and of Scrutinizer CI.

v0.1.2, 2019-03-24
------------------

- Renamed all interfaces to follow the PSR Naming Convention standards.

v0.1.1, 2019-03-24
------------------

- Added the ignoring of Docker related build and image files;
- Updated some of the badges at the README.md file.

v0.1.0, 2019-03-23
------------------

- Added three randomness generator objects for quasi, pseudo and cryptography secure sources.
- Added unit tests for the QuasiRandom, the PseudoRandom and the CryptoRandom objects;
- Added and configured Travis integration, unit testing and coverage reporting;
- Added and configured Scrutinizer integration and automated code reviews;
- Updated the README.md information about the project installation;
- Generated an online DOI number via Zenodo and CERN integration;
- Added badges to the project description and updated Packagist;
- Updated the Composer JSON schema and suggestions information;
- Purged the image caches for the GitHub repository;
- Release is signed with a GitHub GPG signature.

v0.0.20, 2019-03-20
------------------

- Updated the Composer JSON schema and renamed the package for Packagist usage;
- Registered the first online DOI number via Zenodo and CERN integration;
- Registered the package at Packagist, the Composer main repository;
- Added badges to the README.md file's description.

v0.0.19, 2019-03-19
------------------

- Added an abstraction for the framework’s internal exceptions;
- Added some framework exceptions for easier error handling;
- Added unit tests for all the framework exception objects;
- Added an ExceptionFactory for easier error instancing;
- Added a unit test for the ExceptionFactory class.

v0.0.18, 2019-03-19
------------------

- Rearranged the namespacing and the folder hierarchy for abstractions;
- Added a core StringBuilder class for Unicode string manipulations;
- Added a unit test for the StringBuilder core class;
- Removed the firstly added dummy unit test.

v0.0.17, 2019-03-19
------------------

- Updated the testing settings for PHPUnit, CodeCoverage and PHP Documentor;
- Updated some of the PHPDoc comments at development files.

v0.0.16, 2019-03-09
------------------

- Added a core class abstraction for the Factory design pattern.

v0.0.15, 2019-03-03
------------------

- Added a core class abstraction for the Singleton design pattern.

v0.0.14, 2019-03-02
------------------

- Updated the Composer JSON schema and renamed for easier future Packagist loading;
- Moved the development and testing functions implementation to a seconds file;
- Fixed a semantic language typo in the README.md file's description section.

v0.0.13, 2019-03-02
------------------

- Updated the testing settings for PHPUnit and CodeCoverage;
- Added a few new development Composer script aliases.

v0.0.12, 2019-02-17
------------------

- Updated the project's description at the README.md file.

v0.0.11, 2019-02-17
------------------

- Added a PSR-4 autoloader for the project's loading without Composer.

v0.0.10, 2019-02-17
------------------

- Added a backward compatibility snippet for running under PHP 5.x versions.

v0.0.9, 2019-02-16
------------------

- Updated the XML configuration for PHPUnit and CodeCoverage testing.

v0.0.8, 2019-02-16
------------------

- Added a Composer branch alias for latest master development version.

v0.0.7, 2019-02-16
------------------

- Added XML configuration for PHP Code Sniffer and PHP Documentor binaries.

v0.0.6, 2019-02-10
------------------

- Updated the Composer JSON schema keywords to match with the GitHub topics.

v0.0.5, 2019-02-09
------------------

- Added PHPUnit and CodeCoverage integration to project;
- Created new Composer script aliases for development purposes;
- Created internal class hierarchy for tests and some debug functions.

v0.0.4, 2019-02-09
------------------

- Added a Makefile and an attribute list for Git when exporting.

v0.0.3, 2019-02-03
------------------

- Added a project logo and updated the README.md file.

v0.0.2, 2019-02-03
------------------

- Updated the composer.json suggested packages and extensions;
- Added a few new development Composer script aliases.

v0.0.1, 2019-01-27
------------------

- Repository creation and setting up the initial project structure.
