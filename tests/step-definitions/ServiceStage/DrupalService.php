<?php

namespace StepDefs\ServiceStage;

use Behat\MinkExtension\Context\RawMinkContext;

class DrupalService {

  /**
   * @var string Authentication details.
   */
  private $admin_username = '';
  private $admin_passwd = '';

  /**
   * @var string Location of test database file.
   */
  private $db_src_path = '';

  /**
   * @var string This is where Drupal expects its database file.
   */
  private $db_dst_path = '';

  /**
   * Initialize parameters.
   *
   * Parameters can come from the behat config file or environment variables.
   */
  function __construct($username, $passwd=NULL, $db_src_path, $db_dst_path) {

    $this->admin_username = $username;
    $this->admin_passwd   = $passwd ? : getenv('BEHAT_DRUPAL_ADMIN_PASSWD');
    $this->db_src_path    = $db_src_path;
    $this->db_dst_path    = $db_dst_path;
  }

  /**
   * Fills in Drupal's login form.
   */
  function login(RawMinkContext $context) {

    $context->visitPath('/user/login');

    $loginForm = $context->getSession()->getPage()->findById('user-login-form');
    if (is_null($loginForm)) {
      throw new ExpectationException('Cannot find the login form.', $context->getSession()->getDriver());
    }

    $usernameField = $loginForm->findById('edit-name');
    $passwdField   = $loginForm->findById('edit-pass');

    if (is_null($usernameField) or is_null($passwdField)) {
      throw new ExpectationException('Cannot find the authentication fields.', $context->getSession()->getDriver());
    }

    $usernameField->setValue($this->admin_username);
    $passwdField->setValue($this->admin_passwd);
    $loginForm->submit();

    $context->assertSession()->elementNotExists('css', '.messages--error');
  }

  /**
   * Sets up Drupal's SQLite database file.
   */
  function setupSite() {

    // Remove all existing sqlite files for our site.
    $existingDbfilePattern = "{$this->db_dst_path}*";
    array_map('unlink', glob($existingDbfilePattern));

    copy($this->db_src_path, $this->db_dst_path);
  }

  /**
   * Logs out of the site.
   */
  function logout($context) {

    $context->visitPath('/user/logout');
  }
}
