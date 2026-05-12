# Setup Instructions

## Step 1: Install Composer Dependencies

Navigate to the plugin directory and install dependencies:

```bash
cd /Users/hasan/Local\ Sites/wootest/app/public/wp-content/plugins/woodivi-extend
composer install
```

This will create:
- `vendor/autoload.php` - The PSR-4 autoloader
- `vendor/` - Composer dependencies directory

The `vendor/` directory is excluded from git via `.gitignore`.

## Step 2: Activate the Plugin

1. Go to your WordPress Dashboard
2. Navigate to **Plugins**
4. Find **WooDivi Extend**
4. Click **Activate**

## Step 3: Verify Installation

- Check that the plugin appears in the Plugins list
- Navigate to the admin panel to see the new menu item: **WooDivi Extend**
- Check your browser console to verify frontend scripts loaded (should see `WooDivi Extend Frontend loaded`)

## Next Steps

### Apex27 Divi 5 Listings Module

1. Open **WooDivi Extend -> Apex27 Settings**.
2. Set the following values:
    - **API Base URL**: `https://api.apex27.co.uk`
    - **API Key**: your key from `https://ragdon.apex27.co.uk/admin/api-keys`
    - **Listings Endpoint**: `/listings`
    - **Sales Endpoint**: `/offers`
    - **API Key Header**: `x-api-key`
    - **Authorization Scheme**: `None`
3. In Divi 5 builder, add **Apex27 Listings** module.
4. Set **Items to Display** to:
    - `Listings`, `Sales`, or `Listings and Sales` for normal mode
    - `Custom Endpoint` if you want one module instance to call a specific endpoint
5. Optionally set **Static Query Params** in query-string format, e.g. `branch=ragdon&department=sales`.

The module merges static query params with search-form query params from the URL.
For listings output, keep `includeImages=1` in Static Query Params unless you intentionally want text-only cards.

### Development

1. **Add new features**: Create new classes in `src/` following the same namespace pattern
2. **Register hooks**: Use the `Loader` class to add actions and filters
3. **Add assets**: Place CSS/JS in `assets/css/` and `assets/js/`

### Example: Adding a New Admin Settings Class

```php
<?php
namespace WooDiviExtended\Admin;

use WooDiviExtended\Loader;

class Settings {
    private $loader;

    public function __construct( Loader $loader ) {
        $this->loader = $loader;
    }

    public function register() {
        $this->loader->add_action( 'admin_init', $this, 'register_settings' );
    }

    public function register_settings() {
        // Add your settings code here
    }
}
```

Then instantiate in `Plugin.php`:

```php
$settings = new Admin\Settings( $this->loader );
$settings->register();
```

### Debugging

Enable WordPress debugging in `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Check logs in `wp-content/debug.log`

### Translation Support

1. Use `__( 'Text', 'woodivi-extend' )` for translatable strings
2. Generate `.pot` file using WP-CLI or similar tools
3. Place `.po` files in `languages/` directory

## Composer Autoloading

The plugin uses PSR-4 autoloading configured in `composer.json`:

```json
"autoload": {
  "psr-4": {
    "WooDiviExtended\\": "src/"
  }
}
```

This means:
- Any file in `src/Admin/MyClass.php` is automatically available as `\WooDiviExtended\Admin\MyClass`
- No manual `require` or `include` statements needed
- File names must match class names (case-sensitive)

## File Organization Reference

```
src/
├── Plugin.php              ← Main singleton class
├── Loader.php              ← Hook management
├── Admin/
│   ├── AdminHooks.php      ← Main admin hooks
│   ├── SettingsPage.php    ← Example: Settings functionality
│   └── MetaBoxes.php       ← Example: Custom meta boxes
├── Frontend/
│   ├── FrontendHooks.php   ← Main frontend hooks
│   └── Widgets.php         ← Example: Custom widgets
├── Utils/
│   └── Helpers.php         ← Helper functions/utilities
└── Api/
    └── Endpoints.php       ← REST API endpoints
```

Use this as a reference for organizing your plugin classes!
