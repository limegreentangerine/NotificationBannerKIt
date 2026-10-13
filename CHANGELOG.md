# Changelog

Notable changes to this project are documented here.

## 0.0.3

### Changed

- Settings (on/off, custom CSS, dismissable banner, and the notification chosen
  for each style) are now stored in a new `nbSettings` database table, through a
  `Settings` entity, instead of the package file config. The chosen banner and
  popup are foreign keys to `nbNotifications` and are cleared automatically if
  that notification is deleted.
- Upgrading copies any existing file-config settings into `nbSettings`. The
  file config is left in place and is no longer read. **Run the package upgrade
  after updating**: until then the front end shows nothing, as the table does
  not exist yet.

### Fixed

- Saving the Settings page now validates the form's CSRF token. Previously a
  forged cross-site request could change the settings for a logged-in admin.

## 0.0.2

### Added

- An edited notification is shown again to visitors who had dismissed it. The
  dismissal cookie now stores the notification's last-updated timestamp and is
  only honoured while it matches. Saving a notification in the dashboard updates
  that timestamp.

### Changed

- Dismissal is remembered for 30 days (previously 1 day), and the cookie is
  marked `Secure` on HTTPS. Visitors holding an old `nb_closed_<id>=1` cookie
  see the notification once more.
- The banner and popup front-end templates moved from `elements/dashboard/` to
  `elements/`, and dashboard controllers moved to `controllers/single_page/`.
- Deleting a notification resets the active banner/popup setting that pointed
  at it, and switches the feature off when none remain.
- Added padding to the banner content.
- Template output is escaped consistently.

### Fixed

- Removed N+1 queries from the Notifications and Settings dashboard pages.
  Notifications and their contents are loaded together, the one-banner/one-popup
  check uses a count instead of loading records, and popup images are fetched in
  one query when saving or editing. Page query counts no longer grow with the
  number of notifications or locales.
- Front-end element tests included the templates from their old location.

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
