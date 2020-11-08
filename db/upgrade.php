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
 * Upgrade steps for the Media Poster activity module.
 *
 * @package     mod_mposter
 * @category    upgrade
 * @copyright   Original Poster by 2015 David Mudrak <david@moodle.com>, modified by Roberto Becerra, 2020 <roberto.becerra@lmta.lt>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Performs the mposter upgrade steps.
 *
 * @param int $oldversion The version we are upgrading from
 * @return bool
 */
function xmldb_mposter_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2018120204) {

        // Define field author to be added to mposter.
        $table = new xmldb_table('mposter');
        $field = new xmldb_field('surtitle', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'name');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('author',   XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'surtitle');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('numbering', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'author');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('language', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'numbering');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Media Poster savepoint reached.
        upgrade_mod_savepoint(true, 2018120204, 'mposter');
    }

    if ($oldversion < 2018120205)
    {
        // Define field author to be added to mposter.
        $table = new xmldb_table('mposter');
        $field = new xmldb_field('rs_collection', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'course ');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Media Poster savepoint reached.
        upgrade_mod_savepoint(true, 2018120205, 'mposter');
    }

    if ($oldversion < 2018120207)
    {
        // Define field author to be added to mposter.
        $table = new xmldb_table('mposter');
        $field = new xmldb_field('autopopulate', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'language ');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Media Poster savepoint reached.
        upgrade_mod_savepoint(true, 2018120207, 'mposter');
    }
    if ($oldversion < 2018120208)
    {
        // Define field author to be added to mposter.
        $table = new xmldb_table('mposter');
        $field = new xmldb_field('rs_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'rs_collection ');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Media Poster savepoint reached.
        upgrade_mod_savepoint(true, 2018120208, 'mposter');
    }


    // 29 nov 2019
    if ($oldversion < 2019030505) {

        // Define field surtitle to be dropped from mposter.
        $table = new xmldb_table('mposter');
        $field_1 = new xmldb_field('surtitle');
        $field_2 = new xmldb_field('author');
        $field_3 = new xmldb_field('numbering');
        $field_4 = new xmldb_field('language');

        $field_meta1 = new xmldb_field('meta1', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'name');
        $field_meta2 = new xmldb_field('meta2', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta1');
        $field_meta3 = new xmldb_field('meta3', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta2');
        $field_meta4 = new xmldb_field('meta4', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta3');
        $field_meta5 = new xmldb_field('meta5', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta4');

        // Conditionally launch drop and add fields.
        if ($dbman->field_exists($table, $field_1)) {
            $dbman->drop_field($table, $field_1);
            $dbman->drop_field($table, $field_2);
            $dbman->drop_field($table, $field_3);
            $dbman->drop_field($table, $field_4);

            $dbman->add_field($table, $field_meta1);
            $dbman->add_field($table, $field_meta2);
            $dbman->add_field($table, $field_meta3);
            $dbman->add_field($table, $field_meta4);
            $dbman->add_field($table, $field_meta5);
        }

        // Media Poster savepoint reached.
        upgrade_mod_savepoint(true, 2019030505, 'mposter');
    }

    if ($oldversion < 2019030510) {

        // Define field surtitle to be dropped from mposter.
        $table = new xmldb_table('mposter');

        $field_meta2 = new xmldb_field('meta2', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta1');
        $field_meta3 = new xmldb_field('meta3', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta2');
        $field_meta4 = new xmldb_field('meta4', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta3');
        $field_meta5 = new xmldb_field('meta5', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta4');

        // Conditionally launch 
        if ($dbman->table_exists($table) && !$dbman->field_exists($table, $field_meta2)) {
            $dbman->add_field($table, $field_meta2);
            $dbman->add_field($table, $field_meta3);
            $dbman->add_field($table, $field_meta4);
            $dbman->add_field($table, $field_meta5);
        }

        // Media Poster savepoint reached.
        upgrade_mod_savepoint(true, 2019030510, 'mposter');
    }
    // \ 29 nov 2029

    if ($oldversion < 2019030511) {

        // Define field surtitle to be dropped from mposter.
        $table = new xmldb_table('mposter');
        $field_meta6 = new xmldb_field('meta6', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta5');

        // Conditionally launch 
        if ($dbman->table_exists($table)) {
            $dbman->add_field($table, $field_meta6);
        }

        // Media Poster savepoint reached.
        upgrade_mod_savepoint(true, 2019030511, 'mposter');
    }


    // 3 December 2019
    if ($oldversion < 2019030513) {

        // Define field surtitle to be dropped from mposter.
        $table = new xmldb_table('mposter');

        $field_meta1 = new xmldb_field('meta_value1', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta6');
        $field_meta2 = new xmldb_field('meta_value2', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta_value1');
        $field_meta3 = new xmldb_field('meta_value3', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta_value2');
        $field_meta4 = new xmldb_field('meta_value4', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta_value3');
        $field_meta5 = new xmldb_field('meta_value5', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta_value4');
        $field_meta6 = new xmldb_field('meta_value6', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta_value5');

        // Conditionally launch add fields
        if ($dbman->table_exists($table)) {

            $dbman->add_field($table, $field_meta1);
            $dbman->add_field($table, $field_meta2);
            $dbman->add_field($table, $field_meta3);
            $dbman->add_field($table, $field_meta4);
            $dbman->add_field($table, $field_meta5);
            $dbman->add_field($table, $field_meta6);
        }

        // Media Poster savepoint reached.
        upgrade_mod_savepoint(true, 2019030513, 'mposter');
    }
    // \ 3 December 2019


    // 5 Jan 2020
    if ($oldversion < 2019030523){
        // Define table and field to modify/add
        $table = new xmldb_table('mposter');
        $field_meta7 = new xmldb_field('meta_value7', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta_value6');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field_meta7)) {
            $dbman->add_field($table, $field_meta7);
        }
        upgrade_mod_savepoint(true, 2019030522, 'mposter');
    }

    if ($oldversion < 2019030524){
        // Define table and field to modify/add
        $table = new xmldb_table('mposter');
        $field_7 = new xmldb_field('meta7', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'meta6');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field_7)) {
            $dbman->add_field($table, $field_7);
        }
        upgrade_mod_savepoint(true, 2019030524, 'mposter');
    }

    if ($oldversion < 2019030525){
        // Define table and field to modify/add
        $table = new xmldb_table('mposter');
        $field_overwrite = new xmldb_field('overwrite', XMLDB_TYPE_CHAR, '8', null, XMLDB_NOTNULL, null, "0", 'name');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field_overwrite)) {
            $dbman->add_field($table, $field_overwrite);
        }
        upgrade_mod_savepoint(true, 2019030525, 'mposter');
    }
    // \ 5 :Jan 2020


    // 8 nov 2020
    if ($oldversion < 2019030535){
        // Define table and field to modify/add
        $table   = new xmldb_table('mposter');
        // new field to be created
        $field_revision = new xmldb_field('revision',     XMLDB_TYPE_CHAR, '16', null, "false", null, null, 'id');
        $field_tobe     = new xmldb_field('tobemigrated', XMLDB_TYPE_CHAR, '16', null, "false", null, null, 'revision');
        $field_display  = new xmldb_field('display',      XMLDB_TYPE_CHAR, '16', null, "false", null, "false", 'tobemigrated');

        // Conditionally launch add field author.
        if (!$dbman->field_exists($table, $field_revision)) {
            $dbman->add_field($table, $field_revision);
            $dbman->add_field($table, $field_tobe);
            $dbman->add_field($table, $field_display);
        }
        upgrade_mod_savepoint(true, 2019030535, 'mposter');
    }

    return true;
}
