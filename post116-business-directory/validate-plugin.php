<?php
/**
 * Plugin Validation Script
 * 
 * This script validates the plugin structure and files.
 */

echo "Post 116 Business Directory Plugin Validation\n";
echo "============================================\n\n";

// Check required files
$required_files = array(
    'post116-business-directory.php',
    'includes/class-post-type.php',
    'includes/class-taxonomy.php',
    'includes/class-meta-fields.php',
    'includes/class-admin.php',
    'includes/class-rest-api.php',
    'includes/class-gutenberg-block.php',
    'includes/class-template-loader.php',
    'includes/class-settings.php',
    'includes/class-capabilities.php',
    'includes/class-schema.php',
    'public/js/public.js',
    'public/js/admin.js',
    'public/css/public.css',
    'public/css/admin.css',
    'templates/single-p116_business.php',
    'templates/taxonomy-p116_business_category.php',
    'languages/post116-business-directory.pot',
    'readme.md'
);

$missing_files = array();
$existing_files = array();

foreach ($required_files as $file) {
    if (file_exists($file)) {
        $existing_files[] = $file;
        echo "✅ $file\n";
    } else {
        $missing_files[] = $file;
        echo "❌ $file (MISSING)\n";
    }
}

echo "\n";
echo "File Summary:\n";
echo "=============\n";
echo "Total required files: " . count($required_files) . "\n";
echo "Existing files: " . count($existing_files) . "\n";
echo "Missing files: " . count($missing_files) . "\n";

if (count($missing_files) === 0) {
    echo "\n🎉 All required files are present!\n";
} else {
    echo "\n⚠️  Missing files:\n";
    foreach ($missing_files as $file) {
        echo "   - $file\n";
    }
}

// Check file sizes
echo "\nFile Sizes:\n";
echo "===========\n";
foreach ($existing_files as $file) {
    $size = filesize($file);
    $size_kb = round($size / 1024, 2);
    echo "$file: {$size_kb} KB\n";
}

// Check main plugin file
echo "\nMain Plugin File Check:\n";
echo "======================\n";
if (file_exists('post116-business-directory.php')) {
    $content = file_get_contents('post116-business-directory.php');
    
    $checks = array(
        'Plugin Name' => strpos($content, 'Plugin Name:') !== false,
        'Plugin URI' => strpos($content, 'Plugin URI:') !== false,
        'Description' => strpos($content, 'Description:') !== false,
        'Version' => strpos($content, 'Version:') !== false,
        'Author' => strpos($content, 'Author:') !== false,
        'Text Domain' => strpos($content, 'Text Domain:') !== false,
        'class Post116_Business_Directory' => strpos($content, 'class Post116_Business_Directory') !== false,
        'get_instance()' => strpos($content, 'get_instance()') !== false,
    );
    
    foreach ($checks as $check => $result) {
        echo ($result ? "✅" : "❌") . " $check\n";
    }
}

echo "\nValidation Complete!\n";