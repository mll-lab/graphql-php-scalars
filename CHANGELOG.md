# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## 4.1.1

### Fixed

- Move `ext-json` to `require` section in `composer.json`

## 4.1.0

### Changed

- Improve error message when values can not be coerced into strings

## 4.0.0

### Added

- Support PHP 8

### Changed

- Rename `Mixed` class to `MixedScalar` because `mixed` is a reserved name in PHP 8.
  The GraphQL name of the scalar is still `Mixed` so the schema does not change.

## 3.1.0

### Added

- Support `webonyx/graphql-php@^14.0.0`

## 3.0.2

### Changed

- Move util functions to class for better autoloading

## 3.0.1

### Fixed

- Export only minimally needed files in distribution package

## 3.0.0

### Changed

- Bump dependencies of various packages

### Removed

- Remove support for PHP 7.1
