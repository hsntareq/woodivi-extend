# WooDivi Extend Plugin

A professional WordPress plugin structure following OOP principles and PSR-4 autoloading standards.

## Project Structure

```
woo-divi-extended/
├── src/                          # PSR-4 compliant source files
│   ├── Plugin.php               # Main plugin class (Singleton)
│   ├── Loader.php               # Hook management class
│   ├── Admin/
│   │   └── AdminHooks.php       # Admin functionality
│   └── Frontend/
│       └── FrontendHooks.php    # Frontend functionality
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
├── languages/                    # Translation files
├── composer.json                # PSR-4 autoloader configuration
└── woodivi-extend.php       # Plugin entry point
```

## Key Features

### 1. **OOP Architecture**
- Singleton pattern for main Plugin class
- Separation of concerns (Admin, Frontend, Loader)
- Dependency injection via constructor
- Private properties and methods
- Type hints and documentation

### 2. **PSR-4 Autoloading**
- Namespace: `WooDiviExtended\`
- Automatic class loading via Composer
- No manual `require` statements needed
- Easy to extend with new classes

### 3. **Hook Management**
- Centralized hook registration via `Loader` class
- Actions and filters managed uniformly
- Clean, readable hook registration syntax
- Priority and argument handling

### 4. **Admin & Frontend Separation**
- Dedicated classes for admin and frontend functionality
- Conditional loading based on context
- Asset enqueuing with proper dependencies

## Installation & Setup

### 1. Install Composer Dependencies
```bash
cd /path/to/woodivi-extend
composer install
```

This generates the `vendor/autoload.php` file needed for PSR-4 autoloading.

### 2. Activate Plugin
- Go to WordPress Admin → Plugins
- Find "WooDivi Extend"
- Click Activate

## How to Extend

### Adding a New Admin Feature

Create a new file in `src/Admin/`:

```php
<?php
namespace WooDiviExtended\Admin;

use WooDiviExtended\Loader;

class SettingsPage {
    private $loader;

    public function __construct( Loader $loader ) {
        $this->loader = $loader;
    }

    public function register() {
        $this->loader->add_action( 'admin_init', $this, 'register_settings' );
    }

    public function register_settings() {
        // Your settings code here
    }
}
```

Then in `Plugin.php`, instantiate and register it:

```php
if ( is_admin() ) {
    $admin = new Admin\SettingsPage( $this->loader );
    $admin->register();
}
```

### Adding a New Hook

Use the Loader class:

```php
$this->loader->add_action( 'hook_name', $this, 'callback_method', 10, 1 );
$this->loader->add_filter( 'filter_name', $this, 'callback_method', 10, 2 );
```

## Constants

The plugin defines these constants in the main file:

```php
WOO_DIVI_EXTENDED_VERSION  // Plugin version
WOO_DIVI_EXTENDED_PATH     // Full path to plugin directory
WOO_DIVI_EXTENDED_URL      // Full URL to plugin directory
WOO_DIVI_EXTENDED_BASENAME // Plugin basename
```

## Best Practices Implemented

✅ WordPress security (`ABSPATH` check)
✅ Nonces for AJAX requests
✅ Escaping functions (`wp_kses_*`, `esc_html_*`)
✅ Proper hook priorities
✅ Conditional asset loading
✅ Internationalization support (`load_plugin_textdomain`)
✅ Rewrite rule flushing
✅ Proper hook documentation
✅ Access control (`manage_options` capability)
✅ Singleton pattern to prevent multiple instantiations

## Coding Standards

This plugin follows:
- **PSR-4**: Autoloading standard
- **WordPress Coding Standards**: For PHP formatting and practices
- **OOP Principles**: Inheritance, encapsulation, and abstraction

## License

GPL-2.0-or-later

See the LICENSE file for more information.
