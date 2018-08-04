Feature: Create user

  Background: I have to have the right privileges to create users.
    Given I am logged in as an admin user who can create users

  Scenario Outline: Add a new user
    Given the desired "<Username>" and "<Email>" of a user
    When I fill in the form to create that user
    Then the user "<Username>" appears in the users list

    Examples:
      | Username | Email           |
      | Foo      | foo@example.net |
      | Bar      | bar@example.net |
      | Baz      | baz@example.net |
