<?php

/**
 * @file
 * Step definitions discussed in the final stage of the article.
 *
 * Demonstrates the use of services in step definitions classes.
 */

namespace StepDefs\ServiceStage;

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
   * @var StepDefs\ServiceStage\DrupalService
   */
  private $drupal = NULL;

  /**
   * Get ready to deal with Drupal.
   *
   * @param DrupalService $drupal
   */
  function __construct(DrupalService $drupal) {

    $this->drupal = $drupal;
  }

  /**
   * @Given I am logged in as an admin user.
   */
  function login() {

    $this->drupal->login($this);
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
  function setupSite(BeforeScenarioScope $unusedScope) {

    $this->drupal->setupSite();
  }

  /**
   * @AfterScenario
   */
  function logout() {

    $this->drupal->logout($this);
  }

  /**
   * @Transform :title
   */
  function toTitleCase($title) {

    return ucfirst($title);
  }
}
