# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://git.d3data.de/D3Public/linkmobility-php-client/compare/2.1.0...rel_2.x)

## [2.1.0](https://git.d3data.de/D3Public/linkmobility-php-client/compare/2.0.3...2.1.0) - 2022-12-26
### Added
- installable in PHP > 8.0
- debug logger to log all comunications in debug mode (default Guzzle client only)
- retry middleware to request again in defined error cases  (default Guzzle client only)

### Fixed
- missing getRecipientsList() in RecipientsListInterface

### Deprecated
- unused client argument in recipient list class

### Removed 
- unused ApiException class

## [2.0.3](https://git.d3data.de/D3Public/linkmobility-php-client/compare/2.0.2...2.0.3) - 2022-12-26
### Changed
- allow Guzzle v7.3 for more backward compatibility

## [2.0.2](https://git.d3data.de/D3Public/linkmobility-php-client/compare/2.0.1...2.0.2) - 2022-07-28
### Changed
- add support note
- adjust readme

## [2.0.1](https://git.d3data.de/D3Public/linkmobility-php-client/compare/2.0.0...2.0.1) - 2022-07-28
### Added
- phpstan code checks

### Changed
- improve changelog
- improve code quality

### Fixed
- wrong return type of LoggerHandler::getInstance

## [2.0.0](https://git.d3data.de/D3Public/linkmobility-php-client/compare/1.3.0...2.0.0) - 2022-07-19
### Changed
- adjust to PHP >= 7.3 and current dependency packages

## [1.3.1](https://git.d3data.de/D3Public/linkmobility-php-client/compare/1.3.0...1.3.1) - 2022-07-28
### Changed
- improve code quality

### Fixed
- wrong return type of LoggerHandler::getInstance

## [1.3.0](https://git.d3data.de/D3Public/linkmobility-php-client/compare/1.2.1...1.3.0) - 2022-07-18
### Added
- tests added

### Changed
- tests use generated example phone numbers
- move recipient checks from list to recipient itself

## [1.2.1](https://git.d3data.de/D3Public/linkmobility-php-client/compare/1.2.0...1.2.1) - 2022-07-15
### Changed
- extend log messages
- sanitize special phone number format before request

## [1.2.0](https://git.d3data.de/D3Public/linkmobility-php-client/compare/1.1.0...1.2.0) - 2022-07-14
### Added
- collect exception messages in a class
- collect URI parts in a class

### Changed
- make sender number optional
- assign sender address type only if sender is set
- extract logger handler from client

## [1.1.0](https://git.d3data.de/D3Public/linkmobility-php-client/compare/1.0.0...1.1.0) - 2022-07-13
### Added
- make installable in PHP 8

### Removed
- remove unused dependency

## [1.0.0](https://git.d3data.de/D3Public/linkmobility-php-client/releases/tag/1.0.0) - 2022-07-13
### Added
- initial implementation
  - SMS requests (text or binary)
  - SMS responses
  - recipient managing
