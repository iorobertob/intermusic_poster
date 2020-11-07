@mod @mod_mposter
Feature: Adding mposter activity
  In order to display blocks not only on the course page side bars
  As a teacher
  I need to add mposter activity module to a course

  Background:
    Given the following "users" exist:
      | username    | firstname | lastname  | email                |
      | teacher1    | Teacher   | 1         | teacher1@example.com |
      | student1    | Student   | 1         | student1@example.com |
    And the following "courses" exist:
      | fullname    | shortname | category  |
      | Course 001  | C1        | 0         |
    And the following "course enrolments" exist:
      | user        | course    | role              |
      | teacher1    | C1        | editingteacher    |
      | student1    | C1        | student           |

  Scenario: Add empty mposter to the course with description displayed
    Given I log in as "teacher1"
    And I am on "Course 001" course homepage
    And I turn editing mode on
    And I add a "Media Poster" to section "1" and I fill the form with:
      | Name                              | Media Poster 001                  |
      | Description                       | This is a test mposter 001.  |
      | Display description on view page  | 1                           |
    And I log out
    When I log in as "student1"
    And I am on "Course 001" course homepage
    And I follow "Media Poster 001"
    Then I should see "This is a test mposter 001."

  Scenario: Add empty mposter to the course with description not displayed
    Given I log in as "teacher1"
    And I am on "Course 001" course homepage
    And I turn editing mode on
    And I add a "Media Poster" to section "1" and I fill the form with:
      | Name                              | Media Poster 002                  |
      | Description                       | This is a test mposter 002.  |
      | Display description on view page  | 0                           |
    When I follow "Media Poster 002"
    Then I should not see "This is a test mposter 002."
