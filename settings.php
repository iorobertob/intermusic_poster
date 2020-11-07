<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 
/**
 * Adds admin settings for the plugin.
 *
 * @package     mod_mposter
 * @category    admin
 * @copyright   2020 Your Name <email@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();
 
if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/mposter/locallib.php');

    // Introductory explanation that all the settings are defaults for the add lesson form.
    $settings->add(new admin_setting_heading('mod_mposter/lessonintro', '', get_string('showinnavigation', 'mposter')));

    // Appearance settings.
    $settings->add(new admin_setting_heading('mod_mposter/appearance', get_string('manage'), 'mposter'));

    $settings->add(new admin_setting_configtext('mod_mposter/mediaheight', get_string('meta_label_1', 'mposter'),
            get_string('meta_label_2', 'mposter'), "480", PARAM_TEXT));
}