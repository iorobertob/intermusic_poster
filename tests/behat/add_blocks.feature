@mod @mod_mediaposter
Feature: Adding blocks to the mediaposter page
  In order to have some contents displayed at a mediaposter page
  As a teacher
  I need to add blocks to the regions provided by the mediaposter view page

  Scenario: Add multiple blocks to the mediaposter page
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
    And I log in as "teacher1"
    And I am on "Course 001" course homepage
    And I turn editing mode on
    And I add a "Media Poster" to section "1" and I fill the form with:
      | Name                              | Media Poster 003                  |
      | Description                       | This is a test mediaposter 003.  |
    And I follow "Media Poster 003"
    And I add the "HTML" mediaposter block
    And I configure the "(new HTML block)" block
    And I set the field "config_title" to "Created in mediaposter context"
    And I set the field "Content" to "This is first HTML block displayed at a mediaposter page"
    And I set the field "Region" to "mod_mediaposter-pre"
    And I press "Save changes"
    And I am on "Course 001" course homepage
    And I add the "HTML" block
    And I configure the "(new HTML block)" block
    And I set the field "config_title" to "Created in course context"
    And I set the field "Content" to "This is second HTML block displayed at a mediaposter page"
    And I set the field "Display on page types" to "Any page"
    And I press "Save changes"
    And I follow "Media Poster 003"
    And I configure the "Created in course context" block
    And I set the field "Display on page types" to "Media Poster module main page"
    And I set the field "Region" to "mod_mediaposter-pre"
    And I press "Save changes"
    And I add the "People" mediaposter block
    And I configure the "People" block
    And I set the field "Region" to "mod_mediaposter-post"
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I am on "Course 001" course homepage
    Then I should not see "This is second HTML block displayed at a mediaposter page"
    And I follow "Media Poster 003"
    And I should see "This is first HTML block displayed at a mediaposter page" in the "#mod_mediaposter-content-region-pre" "css_element"
    And I should see "This is second HTML block displayed at a mediaposter page" in the "#mod_mediaposter-content-region-pre" "css_element"
    And I should see "People" in the "#mod_mediaposter-content-region-post" "css_element"
