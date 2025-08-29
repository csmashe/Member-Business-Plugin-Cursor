# Post 116 Business Directory Plugin

A comprehensive WordPress plugin for managing and displaying member businesses for American Legion Post 116.

## Features

### Admin Features
- **Custom Post Type**: Manage businesses with full WordPress integration
- **Business Categories**: Hierarchical taxonomy for organizing businesses
- **Multiple Owners**: Add multiple business owners with individual contact information
- **Contact Management**: Business phone, email, and website
- **Address Management**: Full address support with city as required field
- **Ownership Flags**: Mark businesses as Veteran Owned, Sons Owned, or Auxiliary Owned
- **Additional Links**: Add social media and other external links
- **Services Offered**: List services provided by each business
- **Display Controls**: Show/hide businesses from public directory
- **Admin List Table**: Custom columns showing categories, owners, location, and ownership flags
- **Advanced Filtering**: Filter by category, ownership type, and display status
- **Settings Page**: Configure directory behavior and appearance

### Frontend Features
- **Gutenberg Block**: Easy-to-use block for adding directory to pages
- **Shortcode Support**: `[post116_directory]` with customizable attributes
- **AJAX Search**: Real-time search with autocomplete suggestions
- **Category Filtering**: Filter businesses by category
- **Ownership Filtering**: Filter by ownership flags
- **Responsive Design**: Mobile-friendly layout
- **Single Business Pages**: Detailed business information pages
- **Category Archive Pages**: Browse businesses by category
- **JSON-LD Schema**: Structured data for search engines

### Technical Features
- **REST API**: Custom endpoints for search and autocomplete
- **Performance Optimized**: Database indexes for fast searching
- **Security**: Proper sanitization, validation, and capability checks
- **Translation Ready**: Full internationalization support
- **Custom CSS**: Admin-configurable styling options

## Installation

1. Upload the plugin files to `/wp-content/plugins/post116-business-directory/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. The plugin will automatically create a "Business Directory" page
4. Configure settings under "Business Directory > Settings"

## Usage

### Adding Businesses

1. Go to "Business Directory > All Businesses" in the admin
2. Click "Add New Business"
3. Fill in the business information:
   - **Title**: Business name
   - **Content**: Detailed business description
   - **Featured Image**: Business logo
   - **Excerpt**: Short description for listings
4. Configure business details in the meta boxes:
   - **Business Owners**: Add multiple owners with contact info
   - **Contact Information**: Business phone, email, website
   - **Address**: Full address (city required)
   - **Ownership Flags**: Mark ownership type
   - **Additional Links**: Social media, directories, etc.
   - **Services Offered**: List of services (one per line)
   - **Display Options**: Show/hide from directory

### Displaying the Directory

#### Using the Gutenberg Block
1. Edit any page or post
2. Add the "Post 116 Directory" block
3. Configure block settings:
   - Show ownership flags
   - Businesses per page
   - Search placeholder text

#### Using Shortcodes
```
[post116_directory]
[post116_directory show_flags="true" per_page="10" placeholder="Search our businesses..."]
```

### Managing Categories

1. Go to "Business Directory > Categories"
2. Add new categories or edit existing ones
3. Categories are hierarchical - you can create parent/child relationships

### Settings Configuration

1. Go to "Business Directory > Settings"
2. Configure:
   - **Directory Page**: Select the page containing the directory
   - **Show Ownership Flags**: Enable/disable ownership badges
   - **Enable Map View**: Future feature toggle
   - **Businesses Per Page**: Number of businesses per page
   - **Custom CSS**: Add custom styling

## API Endpoints

The plugin provides REST API endpoints for integration:

- `GET /wp-json/p116/v1/search` - Search businesses
- `GET /wp-json/p116/v1/autocomplete` - Get autocomplete suggestions

### Search Parameters
- `search` - Search term
- `category` - Category slug
- `veteran_owned` - Boolean
- `sons_owned` - Boolean
- `auxiliary_owned` - Boolean
- `city` - City name
- `per_page` - Results per page
- `page` - Page number

### Autocomplete Parameters
- `search` - Search term (required)
- `type` - Type of suggestions (business, owner, category)
- `limit` - Number of suggestions

## Customization

### Templates

The plugin uses WordPress template hierarchy. You can override templates by copying them to your theme:

- `single-p116_business.php` - Single business page
- `taxonomy-p116_business_category.php` - Category archive page

### Styling

Add custom CSS through the settings page or in your theme's stylesheet. The plugin uses semantic CSS classes:

- `.post116-directory` - Main directory container
- `.post116-business-card` - Individual business cards
- `.post116-flag` - Ownership flags
- `.post116-category-tag` - Category tags

### Hooks and Filters

The plugin provides several hooks for customization:

```php
// Modify business query
add_filter('post116_bd_business_query_args', function($args) {
    // Modify query arguments
    return $args;
});

// Customize business data
add_filter('post116_bd_business_data', function($data, $post_id) {
    // Modify business data
    return $data;
}, 10, 2);
```

## Legal Disclaimer

The plugin includes a required legal disclaimer that appears on all directory pages:

> "American Legion Post 116 is not liable for or endorsing any listed businesses. Please independently verify their work quality, licenses, and insurance."

This disclaimer cannot be removed and is required for legal protection.

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Support

For support and feature requests, please contact American Legion Post 116.

## Changelog

### Version 1.0.0
- Initial release
- Custom post type and taxonomy
- Admin interface for business management
- Frontend directory with search and filtering
- Gutenberg block and shortcode support
- REST API endpoints
- JSON-LD schema markup
- Responsive design
- Translation ready

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed for American Legion Post 116, North Carolina.