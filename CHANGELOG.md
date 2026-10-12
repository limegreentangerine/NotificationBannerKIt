# Changelog

Notable changes to this project are documented here.

## 0.0.1

### Added

- Site-wide notification **banner** and **popup**, rendered on every front-end
  page (admin, login, register and two-factor pages are skipped).
- Notification entities with per-language content (name, message, link text,
  link page and popup image), saved for each site locale.
- Scheduling: optional start and end dates, plus an active flag.
- Display scope: show a notification on all pages or on the home page only.
- Dashboard pages under **Notification Banner**:
    - **Notifications**: searchable list with create, edit and delete.
    - **Settings**: global on/off switch, choice of the active banner and popup,
      dismissable-banner option, and an option to use custom CSS instead of the
      bundled styles.
- `<component-notification>` web component with dismissal remembered in a
  cookie, plus bundled banner and popup stylesheets.
- PHPUnit tests for front-end elements and content sanitising.

### Changed

- Removed the "LGT" branding: the description now reads "Notification Banner"
  and the CSS classes are renamed `lgt-notification` → `nb-notification` and
  `close-lgt-notification` → `close-nb-notification`. Sites with custom CSS
  targeting the old class names must update it.
- Replaced global class aliases (`Page`, `View`, `Package`, `Localization`,
  `URL`, `File`) with namespaced imports, and read package config through the
  package controller, so PHPStan passes with no errors.

## 0.0.0

### Added

- Separate GitHub Actions workflows for code standards and system tests.

### Changed

- Code standards checks run on pushes and pull requests; system tests run on
  pull requests.
- Updated Composer dependencies and development tooling for the package
  template.

## 2026-09-25

### Added

- PHPStan static analysis configuration and a Composer command for type checks.
- Cloudflare Access authentication for installing dependencies from the private
  Composer registry.

### Changed

- Updated the template to use the published `limegreentangerine/class_kit`
  Composer package.
- Refined the GitHub Actions workflows and development dependencies.

## 2026-09-15

### Added

- Initial Concrete CMS Composer package template, including Composer
  autoloading, PHPUnit test setup, and a GitHub Actions test workflow.
