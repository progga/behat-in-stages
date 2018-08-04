<?php

/**
 * @file
 * This file is executed by composer.  It loads environment variables from the
 * .env file.  When this file is absent, we assume the Drupal admin password
 * is already present in the BEHAT_DRUPAL_ADMIN_PASSWD environment variable.
 *
 * @see .env.dist
 */

if (file_exists('.env')) {
  $dotenv = new Dotenv\Dotenv(__DIR__);
  $dotenv->load();
}
