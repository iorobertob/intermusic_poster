<?php
// This file is part of Moodle - http://moodle.org/
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
 * Provides the backup_mediaposter_activity_task class.
 *
 * @package     mod_mediaposter
 * @category    backup
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/mediaposter/backup/moodle2/backup_mediaposter_stepslib.php');

/**
 * Defines the settings and steps to perform a backup of the mediaposter.
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_mediaposter_activity_task extends backup_activity_task {

    /**
     * Defines activity specific settings to be added to the common ones.
     *
     * The mediaposter module does not define own settings.
     *
     * @see self::define_settings() for the example how to define own settings
     */
    protected function define_my_settings() {
    }

    /**
     * Defines activity specific steps for this task
     *
     * This method is called from {@link self::build()}.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_mediaposter_activity_structure_step('mediaposter_structure', 'mediaposter.xml'));
    }

    /**
     * Encodes URLs to the activity instance's scripts into a site-independent form
     *
     * The current instance of the activity may be referenced from other places in
     * the course by URLs like http://my.moodle.site/mod/mediaposter/view.php?id=42
     * Obvisouly, such URLs are not valid any more once the course is restored elsewhere.
     * For this reason the backup file does not store the original URLs but encodes them
     * into a transportable form. During the restore, the reverse process is applied and
     * the encoded URLs are replaced with the new ones valid for the target site.
     *
     * @see backup_xml_transformer class that actually runs the transformation
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of mediaposters.
        $search = '/('.$base.'\/mod\/mediaposter\/index.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@POSTERINDEX*$2@$', $content);

        // Link to a mediaposter view by cmid.
        $search = '/('.$base.'\/mod\/mediaposter\/view.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@POSTERVIEWBYID*$2@$', $content);

        return $content;
    }
}
