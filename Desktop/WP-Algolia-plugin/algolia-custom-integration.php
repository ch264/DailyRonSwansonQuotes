<?php

/**
 * Plugin Name:     Algolia Custom Integration
 * Description:     Add Algolia Search feature
 * Text Domain:     algolia-custom-integration
 * Version:         1.0.0
 *
 * @package         Algolia_Custom_Integration
 */

// Your code starts here.


//require_once __DIR__ . '/vendor/autoload.php';
// If you're using Composer, require the Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

global $algolia;

$algolia = \Algolia\AlgoliaSearch\SearchClient::create("<Your Algolia App ID>", "<Your Algolia Admin Key>");








// require_once __DIR__ . '/wp-cli.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/algolia-custom-integration/wp-cli.php';
