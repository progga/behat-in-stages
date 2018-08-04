Feature: Manage node
  Add a page node.

  Background:
    Given I am logged in as an admin user.

  Scenario: Create a page.
    Given this piece of content
      | title | Foo bar |
      | body  | Baz qux |
    When I fill in the form to create a page node
    Then I end up at page 1 with title "Foo bar"

  Scenario: Create another page node.
    Given this piece of content with title "blah blah" and the following body copy:
      """
      This is the
      Body of the
      Blah blah page.
      """
    When I fill in the form to create a page node
    Then I end up at page 1 with title "Blah blah"
    And feel very very happy :)
