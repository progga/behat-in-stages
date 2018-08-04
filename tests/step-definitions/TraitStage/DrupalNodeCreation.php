<?php

/**
 * @file
 * Step definitions discussed in the sixth stage of the article.
 *
 * Demonstrates packaging up common functionality in PHP Traits and then using
 * them multiple step definition classes.
 */

namespace StepDefs\TraitStage;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Mink\Exception\ExpectationException;

/**
 * Step definition class.
 */
class DrupalNodeCreation extends RawMinkContext {

  use DrupalService;

  /**
   * @var string Page content.
   */
  private $pageTitle = '';
  private $pageBody  = '';

  /**
   * Initialize parameters.
   *
   * Parameters can come from the behat config file or environment variables.
   */
  function __construct($username, $passwd=NULL, $db_src_path, $db_dst_path) {

    $this->setDrupalDetails(...func_get_args());
  }

  /**
   * @Given I am logged in as an admin user.
   */
  function loginToSite() {

    $this->login();
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
   * @Transform :title
   */
  function toTitleCase($title) {

    return ucfirst($title);
  }
}
