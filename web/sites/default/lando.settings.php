<?php

// DATABASE
$databases['default']['default'] = [
  'database' => $_ENV['MYSQL_DATABASE'],
  'driver' => 'mysql',
  'host' => $_ENV['MYSQL_HOSTNAME'],
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'password' => $_ENV['MYSQL_PASSWORD'],
  'port' => $_ENV['MYSQL_PORT'],
  'prefix' => '',
  'username' => $_ENV['MYSQL_USER'],
  'init_commands' => [
    'isolation_level' => 'SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED',
  ],
];

// SETTINGS
$settings['container_yamls'][] = $app_root . '/sites/local.services.yml';
$settings['hash_salt'] = 'bAnw6uQjUI91y9v-LZFDtwNop67-4HU0bBPBrUA2gydc6i60pUNoi_bYZgXLCk_rQA57920urw';
$settings['config_sync_directory'] = $_ENV['DRUPAL_CONFIG_SYNC'];
// $settings['file_public_path'] = './sites/default/files';
$settings['file_private_path'] = $app_root . '/../private';
$settings['update_free_access'] = FALSE;
$settings['rebuild_access'] = FALSE;
$settings['trusted_host_patterns'] = [
  '^localhost$',
  '^dropi-test\.lndo\.site$',
];
$settings['config_exclude_modules'] = [];

// CONFIGURATION
$config['system.site']['name'] = 'Dropi Test [LOCAL]';
$config['simple_sitemap.settings']['base_url'] = 'https://dropi-test.lndo.site';
$config['advagg.settings']['cache_level'] = 0;
