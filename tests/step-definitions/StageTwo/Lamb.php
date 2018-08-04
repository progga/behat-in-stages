<?php

/**
 * @file
 * Step definitions discussed in the second stage of the article.
 *
 * Demonstrates the use of the following in annotations:
 * - Alternative words.
 * - Parts of word.
 * - Regular expression.
 * - Argument transformation using @Transform methods.
 */

namespace StepDefs\StageTwo;

use Behat\Behat\Context\Context;

/**
 * Step definition class.
 */
class Lamb implements Context {

  /**
   * @Given Mary has :lambCount little lamb(s)
   */
  function hasLamb($lambCount) {}

  /**
   * @When one/some lamb(s) run(s) into the garden of the Selfish giant
   */
  function runsIntoTheGarden() {}

  /**
   * @Then Mary goes after the lamb(s)
   */
  function followTheLamb() {}

  /**
   * Here we use regular expression to capture arguments.
   *
   * @Then /^(both|all) gets chased out by the giant$/
   */
  function getsChased($who) {}

  /**
   * @Transform :lambCount
   */
  function toInt($lambCount) {

    if (ctype_digit($lambCount)) {
      return (int) $lambCount;
    } else if ($lambCount === "a") {
      return 1;
    } else {
      return 0;
    }
  }
}
