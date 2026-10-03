# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

`make it` runs fix, stan, test and normalize.
Run a single test with `vendor/bin/phpunit --filter RegexTest`.

## Compatibility

CI tests PHP 8.0 to 8.5 with `prefer-lowest` and `prefer-stable`.
Write PHP 8.0 syntax and code against the lowest version of each dependency range in `composer.json`.

## Architecture

Every scalar extends `GraphQL\Type\Definition\ScalarType` directly or through a base class: `StringScalar`, `Regex`, `DateScalar`, `IntRange`.
Each implements three entry points with distinct exceptions:

- `serialize` throws `InvariantViolation` — invalid output is a server bug.
- `parseValue` and `parseLiteral` throw `GraphQL\Error\Error` — invalid input is a client error.

Shared coercion helpers live in `Utils`.
Use native `DateTimeImmutable`, not the `Safe` variant.

## Changes

A new or changed scalar gets a section in `README.md` and an entry under `## Unreleased` in `CHANGELOG.md`.
