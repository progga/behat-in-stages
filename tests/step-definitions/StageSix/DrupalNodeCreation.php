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
class DrupalNodeCreation extends RawMinkContext {

  /**
   * @var string Page content.
   */
  private $pageTitle = '';
  private $pageBody  = '';

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
   * @Given I am logged in as an admin user.
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
   * @Given this piece of content
   */
  function prepare(TableNode $table) {

    $map = $table->getRowsHash();
    $this->pageTitle = $map['title'];
    $this->pageBody  = $map['body'];
  }

  /**
   * @Given this piece of content with title :title and the following body copy:
   *
   * @see self::toTitleCase()
   */
  function prepareAnother($title, PyStringNode $body) {

    $this->pageTitle = $title;
    $this->pageBody  = $body;
  }

  /**
   * @When I fill in the form to create a page node
   */
  function createPage() {

    $this->visitPath('/node/add/page');

    $contentEntryForm = $this->getSession()->getPage()->findById('node-page-form');
    if (is_null($contentEntryForm)) {
      throw new ExpectationException('Cannot find the content entry form.', $this->getSession()->getDriver());
    }

    $titleField = $contentEntryForm->findById('edit-title-0-value');
    $bodyField  = $contentEntryForm->findById('edit-body-0-value');
    if (is_null($titleField) or is_null($bodyField)) {
      throw new ExpectationException('Cannot find the content entry fields.', $this->getSession()->getDriver());
    }

    $titleField->setValue($this->pageTitle);
    $bodyField->setValue($this->pageBody);
    $contentEntryForm->submit();
  }

  /**
   * @Then I end up at page 1 with title :expectedPageTitle
   */
  function checkNewPage($expectedPageTitle) {

    $this->assertSession()->elementNotExists('css', '.messages--error');

    $pageTitle = $this->getSession()->getPage()->find('css', 'h1')->getText();
    if ($pageTitle !== $expectedPageTitle) {
      throw new ExpectationException('Unexpected page title: ' . $pageTitle, $this->getSession()->getDriver());
    }
  }

  /**
   * @Then feel very very happy :)
   */
  function feelHappy() {}

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

  /**
   * @Transform :title
   */
  function toTitleCase($title) {

    return ucfirst($title);
  }
}
