# Post 116 Business Directory Plugin - Installation Guide

## Quick Installation

1. **Upload the plugin** to your WordPress site's `/wp-content/plugins/` directory
2. **Activate the plugin** through the WordPress admin under Plugins
3. **Configure settings** at Business Directory > Settings
4. **Start adding businesses** at Business Directory > All Businesses

## Detailed Installation Steps

### Step 1: Upload Plugin Files

Upload the entire `post116-business-directory` folder to your WordPress installation's `/wp-content/plugins/` directory.

### Step 2: Activate the Plugin

1. Log into your WordPress admin dashboard
2. Navigate to **Plugins > Installed Plugins**
3. Find "Post 116 Business Directory" in the list
4. Click **Activate**

### Step 3: Initial Setup

Upon activation, the plugin will automatically:
- Register the custom post type and taxonomy
- Set up user capabilities
- Create a "Business Directory" page
- Add database indexes for performance
- Set default options

### Step 4: Configure Settings

1. Go to **Business Directory > Settings**
2. Configure the following:
   - **Directory Page**: Select the page containing the directory
   - **Show Ownership Flags**: Enable/disable ownership badges
   - **Businesses Per Page**: Number of businesses per page (default: 20)
   - **Custom CSS**: Add any custom styling

### Step 5: Create Business Categories

1. Go to **Business Directory > Categories**
2. Add categories such as:
   - Automotive Services
   - Construction & Contracting
   - Food & Beverage
   - Healthcare Services
   - Professional Services

### Step 6: Add Your First Business

1. Go to **Business Directory > All Businesses**
2. Click **Add New Business**
3. Fill in the business information:
   - **Title**: Business name
   - **Content**: Detailed description
   - **Featured Image**: Business logo
   - **Categories**: Select appropriate categories
4. Configure the meta boxes:
   - **Business Owners**: Add owner information
   - **Contact Information**: Phone, email, website
   - **Address**: Full address (city required)
   - **Ownership Flags**: Mark ownership type
   - **Services Offered**: List services
   - **Display Options**: Show in directory

### Step 7: Display the Directory

#### Option A: Using the Gutenberg Block
1. Edit the "Business Directory" page (or any page)
2. Add the "Post 116 Directory" block
3. Configure block settings as needed

#### Option B: Using Shortcodes
Add this shortcode to any page or post:
```
[post116_directory]
```

With custom attributes:
```
[post116_directory show_flags="true" per_page="10" placeholder="Search our businesses..."]
```

## Testing the Installation

### Run the Test Script

1. Navigate to `/wp-content/plugins/post116-business-directory/test-plugin.php` in your browser
2. Add `?run_tests=1` to the URL
3. The script will verify:
   - Post type registration
   - Taxonomy registration
   - User capabilities
   - Sample data creation
   - REST API endpoints

### Create Sample Data

1. Navigate to `/wp-content/plugins/post116-business-directory/install.php` in your browser
2. Add `?install=1&create_sample_data=1` to the URL
3. This will create sample businesses and categories for testing

## Verification Checklist

- [ ] Plugin activates without errors
- [ ] "Business Directory" menu appears in admin
- [ ] Custom post type "Businesses" is created
- [ ] Custom taxonomy "Categories" is created
- [ ] Directory page is created and accessible
- [ ] Settings page is accessible
- [ ] Can add/edit/delete businesses
- [ ] Can add/edit/delete categories
- [ ] Frontend directory displays correctly
- [ ] Search functionality works
- [ ] Single business pages work
- [ ] Category archive pages work
- [ ] JSON-LD schema is present on single pages

## Troubleshooting

### Common Issues

**Plugin won't activate:**
- Check file permissions
- Ensure all files are uploaded correctly
- Check for PHP errors in error logs

**Directory page not found:**
- Go to Settings > Permalinks and click "Save Changes"
- Check if the page was created during activation

**Search not working:**
- Verify REST API is enabled
- Check browser console for JavaScript errors
- Ensure AJAX is working on your site

**Styling issues:**
- Check if your theme has conflicting CSS
- Add custom CSS through the settings page
- Verify CSS files are loading correctly

### Getting Help

If you encounter issues:
1. Check the WordPress error logs
2. Verify all files are present and have correct permissions
3. Test with a default WordPress theme
4. Contact American Legion Post 116 for support

## Next Steps

After successful installation:

1. **Add your businesses** with complete information
2. **Organize by categories** for better user experience
3. **Customize the styling** to match your site's design
4. **Test all functionality** including search and filtering
5. **Train your staff** on how to manage the directory
6. **Promote the directory** to your members and community

## Support

For technical support or feature requests, please contact American Legion Post 116.

---

**Plugin Version:** 1.0.0  
**Last Updated:** 2024  
**Compatible with:** WordPress 5.0+