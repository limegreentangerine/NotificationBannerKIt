# Notification Banner Kit

[![Code Standards](https://github.com/limegreentangerine/NotificationBannerKIt/actions/workflows/CodeStandards.yml/badge.svg)](https://github.com/limegreentangerine/NotificationBannerKIt/actions/workflows/CodeStandards.yml)
[![System Tests](https://github.com/limegreentangerine/NotificationBannerKIt/actions/workflows/SystemTests.yml/badge.svg)](https://github.com/limegreentangerine/NotificationBannerKIt/actions/workflows/SystemTests.yml)

A Concrete CMS package (handle `notification_banner_kit`) that shows a site-wide
notification **banner** and/or **popup**, managed from the dashboard.

## Features

- Two notification styles: a `banner` and a `popup` (the popup supports an
  image). One notification can be active per style at a time.
- Per-language content: a name, message, link text and link target (page) are
  stored for each site locale.
- Scheduling: optional start and end dates control when a notification shows.
- Display scope: show on `all` pages or the `home` page only.
- Dismissal: visitors can close a banner (configurable) or popup; the choice is
  remembered for 30 days in an `nb_closed_<id>` cookie. The cookie holds the
  notification's last-updated timestamp, so editing a notification shows it
  again to visitors who had dismissed it.
- Front end is a vanilla-JS Web Component (`<component-notification>`) with
  bundled CSS, which can be replaced with your own.

## Requirements

- Concrete CMS 9.5 or later.
- PHP 8.4 or later.
- The `class_kit` package (`limegreentangerine/class_kit`) installed first.

## Installation

1. Install the package via Composer (see the private registry notes under
   [Development](#development)), or place it in `packages/notification_banner_kit`.
2. In the Concrete dashboard go to **System & Settings > Extend Concrete**, and
   install **Notification Banner**.

Installation creates three database tables (`nbNotifications`,
`nbNotificationContents`, `nbNotification_NotificationContents`) and adds three
dashboard pages under **Notification Banner**.

## Usage

### 1. Create a notification

**Dashboard > Notification Banner > Notifications**

Add a notification and choose its style (banner or popup), display scope,
active flag and optional start/end dates. Enter the content for each site
locale. Notifications can be searched, edited and deleted from the same page.

### 2. Choose what is shown

**Dashboard > Notification Banner > Settings**

| Setting                  | Effect                                                               |
| ------------------------ | -------------------------------------------------------------------- |
| Activate                 | Master on/off switch for all notifications.                          |
| Use custom CSS           | Skips the bundled stylesheets so your theme can style the component. |
| Can dismiss              | Lets visitors close the banner (popups are always dismissable).      |
| Banner / popup selection | The notification currently shown for each style.                     |

Settings are stored in the package file config (`notification_banner.*`), not
the database.

### 3. What visitors see

On each page render the package checks that it is activated, skips dashboard,
login, register and two-factor pages, then for each style loads the selected
notification and renders it only if it is active, matches the style, is within
its schedule and matches its display scope. Output is wrapped in a
`<component-notification>` element (`js/notification-banner.js`), which
positions itself against `.ccm-page`.

## Project layout

| Path                                | Contents                                                    |
| ----------------------------------- | ----------------------------------------------------------- |
| `controller.php`                    | Package controller, autoloader, tables, dashboard pages.    |
| `src/Entity`                        | `Notification` and `NotificationContent` Doctrine entities. |
| `src/Events/NotificationBanner.php` | Front-end render hook (`on_before_render`).                 |
| `src/Search`                        | Search list, columns and results for the dashboard.         |
| `controllers/`, `single_pages/`     | Dashboard page controllers and views.                       |
| `elements/`                         | Banner/popup and dashboard partial templates.               |
| `css/`, `js/`                       | Front-end styles and the Web Component.                     |

## Development

### Requirements

- PHP 8.4 or later.
- Composer.
- Node.js and npm, used by the Composer post-install script and JavaScript
  formatting commands.
- Access to the private Composer registry at
  `https://packages.limegreentangerine.net` to install private dependencies.

The package requires `limegreentangerine/class_kit` and uses PHPUnit 11 for
development.

### Install

```bash
composer install
```

Composer runs `npm install` after installation. If you need to authenticate to
the private registry, configure Composer to send the Cloudflare Access service
token headers for `packages.limegreentangerine.net`:

- `CF-ACCESS-CLIENT-ID` -> Cloudflare Access service-token client ID.
- `CF-ACCESS-CLIENT-SECRET` -> matching client secret.

Keep these credentials private; do not commit them to the repository.

### Commands

Run these commands from the repository root:

| Command                                             | Purpose                                                                                                                        |
| --------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------ |
| `composer test`                                     | Run PHPUnit tests.                                                                                                             |
| `composer test-coverage`                            | Run PHPUnit and print a coverage report.                                                                                       |
| `composer typecheck`                                | Run PHPStan using `phpstan.neon`. Findings are displayed, but the command exits successfully even when PHPStan reports errors. |
| `composer format:check`                             | Check PHP and JavaScript formatting.                                                                                           |
| `composer format`                                   | Format PHP and JavaScript files.                                                                                               |
| `composer format:php:check` / `composer format:php` | Check or format PHP files with PHP-CS-Fixer.                                                                                   |
| `composer format:js:check` / `composer format:js`   | Check or format files with Prettier.                                                                                           |

PHPStan analyzes `src` and `tests` at level 4, with bootstrap configuration
from `phpstan-bootstrap.php`. If you add package directories, update the
`paths` in `phpstan.neon` and the directories in `.php-cs-fixer.dist.php` so
they are included in analysis and formatting.

### GitHub Actions

- **CodeStandards** runs formatting checks and PHPStan on pushes and pull
  requests.
- **SystemTests** runs PHPUnit on pull requests.

Both workflows install dependencies from the private Composer registry. Add
these **repository secrets** under **Settings > Secrets and variables >
Actions** so the workflows can authenticate:

- `CF_ACCESS_CLIENT_ID` -> Cloudflare Access service-token client ID.
- `CF_ACCESS_CLIENT_SECRET` -> matching client secret.
