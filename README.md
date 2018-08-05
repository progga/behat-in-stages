# Behat in stages
If you are after examples of a certain Behat functionality (e.g. tabular data in test code), this repo could be useful.

This repo serves as an accessory to an [article I have written](https://demo.codesetter.com/behat-in-stages) about [Behat](https://en.wikipedia.org/wiki/Behat_(computer_science)).  That article is a brain dump of my Behat knowledge.

## Topics
The code in this repo tries to demonstrate as many ways you can write Behat test code as possible.  In my article, I have introduced various Behat concepts in seven stages.  The test suites in this repo corresponds with those stages.  Following topics are covered:

Test suite   | Topics
------------|----------------------------------------------------------
Stage 1     | How to connect Gherkin steps with PHP step definitions.
Stage 2     | <ul><li>Extracting arguments from steps.</li><li>Use of optional words and regular expressions.</li><li>Use of **@Transform** methods to transform arguments.</li></ul>
Stage 3,4,5 | <ul><li>Hook methods.</li><li>Use of multiline strings as arguments.</li><li>Use of tabular data in Gherkin steps and their use as arguments in PHP step definitions.</li><li>Scenario outline.</li><li>Testing a web application using Mink and Goutte.</li></ul>
Stage 6     | Multiple features in a single test suite.

The code in stage 6 suffers from code repetition.  I took this opportunity to demonstrate the use of **Behat profiles**.  I have created three profiles which override stage 6 from the default profile in three different ways all of which avoid code repetition:

Test suite     | Topics
--------------|----------------------------------------------------------
Trait stage   | We use PHP traits to avoid code repetition.
Service stage | We create a service class that contains the common functionality and rely on Behat's built-in service container to pass it to our PHP class constructors.
Service autowire stage | Same as the service stage, but we rely on [service autowiring](https://symfony.com/doc/current/service_container/autowiring.html).

If you are after Behat examples of a certain type, the previous two tables should guide you to the right test suite.

## Installation
### Behat project
```
$ git clone https://github.com/progga/behat-in-stages.git
$ cd behat-in-stages/
$ cp .env.dist .env  # Optional step. See Configuration notes below.
$ composer install
```

### Test target (a fresh Drupal site)
```
$ composer create-project --stability dev --no-interaction --no-dev drupal-composer/drupal-project behat-test-target
$ cd behat-test-target/
$ ./vendor/bin/drush --yes site-install --db-url=sqlite:///path/to/behat-test.sqlite --account-pass=RANDOM-PASSWD
$ cp /path/to/behat-test.sqlite /path/to/behat-in-stages/tests/test-db/behat-test.sqlite
$ ./vendor/bin/drush runserver
```

## Configuration
- The admin password for the Drupal site must go into the *behat-in-stages/.env* file.  Alternatively, you can drop the *.env* file and provide it through the *BEHAT_DRUPAL_ADMIN_PASSWD* environment variable.
- The provided behat.yml file uses **/dev/shm/** to store the Drupal site's password.  This only works in GNU/Linux where /dev/shm is a memory backed file system with quicker access.  On other Operating systems, use a different path and update behat.yml accordingly.


## Test execution
```
$ cd behat-in-stages/
$ ./vendor/bin/behat --format=progress
$ ./vendor/bin/behat --format=progress --profile=dry-with-trait
$ ./vendor/bin/behat --format=progress --profile=dry-with-services
$ ./vendor/bin/behat --format=progress --profile=dry-with-autowired-services
$ ./vendor/bin/behat --format=progress --suite=stage-two --stop-on-failure # Test only stage-two
```

## Software versions used
- Behat: 3.4
- PHP: 5.6 or 7.x
- PHP extensions: curl, gd, pdo-sqlite, sqlite3
- composer: 1.6+
- SQLite: 3.x

## Licence
[Simplified BSD licence](https://en.wikipedia.org/wiki/BSD_licenses#2-clause).
