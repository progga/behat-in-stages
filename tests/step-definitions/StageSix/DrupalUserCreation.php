<?php

/**
 * @file
 * Step definitions discussed in the sixth stage of the article.
 *
 * Demonstrates implementing step definitions for multiple features.
 */

namespace StepDefs\StageSix;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Mink\Exception\ExpectationException;

/**
 * Step definition class.
 */
class DrupalUserCreation extends RawMinkContext {

  /**
   * @var string User details.
   */
  private $username = '';
  private $email    = '';

  /**
   * @var string Authentication details.
   */
  private $admin_username = '';
  private $admin_passwd = '';

  private $db_src_path = '';
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
   * @Given I am logged in as an admin user who can create users
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
   * @Given the desired :username and :email of a user
   */
  function prepare($username, $email) {

    $this->username = $username;
    $this->email    = $email;
  }

  /**
   * @When I fill in the form to create that user
   */
  function createUser() {

    $this->visitPath('/admin/people/create');

    $userRegForm = $this->getSession()->getPage()->findById('user-register-form');
    if (is_null($userRegForm)) {
      throw new ExpectationException('Cannot find the user registration form.', $this->getSession()->getDriver());
    }

    $usernameField = $userRegForm->findById('edit-name');
    $emailField    = $userRegForm->findById('edit-mail');
    $passwdField   = $userRegForm->findById('edit-pass-pass1');
    $passwdConfField = $userRegForm->findById('edit-pass-pass2');
    if (is_null($usernameField) or is_null($emailField) or is_null($passwdField) or is_null($passwdConfField)) {
      throw new ExpectationException('Cannot find the user details entry fields.', $this->getSession()->getDriver());
    }

    $usernameField->setValue($this->username);
    $emailField->setValue($this->email);

    $randomPasswd = bin2hex(random_bytes(5));
    $passwdField->setValue($randomPasswd);
    $passwdConfField->setValue($randomPasswd);

    $userRegForm->findById('edit-submit')->click();
  }

  /**
   * @Then the user :username appears in the users list
   */
  function checkUserList($username) {

    $this->assertSession()->elementNotExists('css', '.messages--error');

    $this->visitPath('/admin/people');

    $this->assertSession()->elementTextContains('css', '.username', $username);
  }

  /**
   * Setup the Drupal database file.
   *
   * @BeforeScenario
   */
  function setupSite(BeforeScenarioScope $scope) {

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
