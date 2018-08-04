<?php

/**
 * @file
 * Step definitions discussed in the first stage of the article.
 *
 * Demonstrates the use of annotations to relate Gherkin steps with PHP step
 * definitions.
 */

namespace StepDefs\StageOne;

use Behat\Behat\Context\Context;

/**
 * Step definition class.
 */
class Lamb implements Context {

  /**
   * @Given Mary has a little lamb
   */
  function hasLamb() {}

  /**
   * @When the lamb runs into the garden of the Selfish giant
   */
  function runsIntoTheGarden() {}

  /**
   * @Then Mary goes after the lamb
   */
  function followTheLamb() {}

  /**
   * @Then both gets chased out by the giant
   */
  function getsChased() {}
}
