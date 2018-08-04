<?php

/**
 * @file
 * Trait for setting up, logging into and out of a Drupal site.
 */

namespace StepDefs\TraitStage;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * To use this trait, first call the setDrupalDetails() method.
 */
trait DrupalService {

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
  function setDrupalDetails($username, $passwd=NULL, $db_src_path, $db_dst_path) {

    $this->admin_username = $username;
    $this->admin_passwd   = $passwd ? : getenv('BEHAT_DRUPAL_ADMIN_PASSWD');
    $this->db_src_path    = $db_src_path;
    $this->db_dst_path    = $db_dst_path;
  }

  /**
   * Submits Drupal's login form.
   */
  function login() {

    $this->visitPath('/user/login');

    $loginForm = $this->getSession()->getPage()->findById('user-login-form');
    if (is_null($loginForm)) {
      throw new ExpectationException('Cannot find the login form.', $this->getSession()->getDriver());
    }

    $usernameField = $loginForm->findById('edit-name');
    $passwdField   = $loginForm->findById('edit-pass');

    if (is_null($usernameField) or is_null($passwdField)) {
      throw new ExpectationException('Cannot find the authentication fields.', $this->getSession()->getDriver());
    }

    $usernameField->setValue($this->admin_username);
    $passwdField->setValue($this->admin_passwd);
    $loginForm->submit();

    $this->assertSession()->elementNotExists('css', '.messages--error');
  }

  /**
   * @BeforeScenario
   */
  function setupSite() {

    // Remove all existing sqlite files for our site.
    $existingDbfilePattern = "{$this->db_dst_path}*";
    array_map('unlink', glob($existingDbfilePattern));

    copy($this->db_src_path, $this->db_dst_path);
  }

  /**
   * @AfterScenario
   */
  function logout() {

    $this->visitPath('/user/logout');
  }
}
