# Silverstripe Short URLs

Create short URLs via Bit.ly, tagged with Google Analytics Campaign data.

[![CI](https://github.com/dynamic/silverstripe-shorturls/actions/workflows/ci.yml/badge.svg)](https://github.com/dynamic/silverstripe-shorturls/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/dynamic/silverstripe-shorturls/branch/master/graph/badge.svg)](https://codecov.io/gh/dynamic/silverstripe-shorturls)

[![Latest Stable Version](https://poser.pugx.org/dynamic/silverstripe-shorturls/v/stable)](https://packagist.org/packages/dynamic/silverstripe-shorturls)
[![Total Downloads](https://poser.pugx.org/dynamic/silverstripe-shorturls/downloads)](https://packagist.org/packages/dynamic/silverstripe-shorturls)
[![Latest Unstable Version](https://poser.pugx.org/dynamic/silverstripe-shorturls/v/unstable)](https://packagist.org/packages/dynamic/silverstripe-shorturls)
[![License](https://poser.pugx.org/dynamic/silverstripe-shorturls/license)](https://packagist.org/packages/dynamic/silverstripe-shorturls)

## Requirements

- Silverstripe ^6
- guzzlehttp/guzzle ^7.4

## Installation

`composer require dynamic/silverstripe-shorturls`

Create the file `app/myshorturlconfig.yml`:

	---
    Name: myshorturlconfig
    After: 'shorturlconfig'
    ---
    Dynamic\ShortURL\Model\ShortURL:
      bitly_token: 'your_token_here'

## Usage

Use the Short URLs model admin to create campaign links, which can be used to track incoming traffic via Google Analytics.

## Maintainers

*  [Dynamic](https://www.dynamicagency.com) (<dev@dynamicagency.com>)

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

- Create a new issue
- Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
  and screencasts can help here.
- Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
  Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
