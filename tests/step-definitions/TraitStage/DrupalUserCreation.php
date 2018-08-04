<?php

/**
 * @file
 * Step definitions discussed in the sixth stage of the article.
 *
 * Demonstrates packaging up common functionality in PHP Traits and then using
 * them multiple step definition classes.
 */

namespace StepDefs\TraitStage;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Mink\Exception\ExpectationException;

/**
 * Step definition class.
 */
class DrupalUserCreation extends RawMinkContext {

  use DrupalService;

  /**
   * @var string User details.
   */
  private $username = '';
  private $email    = '';

  /**
   * Initialize parameters.
   *
   * Parameters can come from the behat config file or environment variables.
   */
  function __construct($username, $passwd=NULL, $db_src_path, $db_dst_path) {

    $this->setDrupalDetails(...func_get_args());
  }

  /**
   * @Given I am logged in as an admin user who can create users
   */
  function loginToSite() {

    $this->login();
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
}
