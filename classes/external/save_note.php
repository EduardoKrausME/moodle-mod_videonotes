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
 * External note save API.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videonotes\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use mod_videonotes\note_manager;

/**
 * Creates or updates a note belonging to the authenticated user.
 */
class save_note extends external_api {

    /**
     * Method execute_parameters.
     *
     * @return external_function_parameters Return value.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id'),
            'noteid' => new external_value(PARAM_INT, 'Existing note id'),
            'timecode' => new external_value(PARAM_FLOAT, 'Video timestamp'),
            'category' => new external_value(PARAM_ALPHANUMEXT, 'Note category'),
            'note' => new external_value(PARAM_RAW, 'Note text'),
            'shared' => new external_value(PARAM_BOOL, 'Share with teachers'),
        ]);
    }

    /**
     * Saves a note.
     *
     * @param int $cmid Course module id.
     * @param int $noteid Note id.
     * @param float $timecode Timestamp.
     * @param string $category Category.
     * @param string $note Note text.
     * @param bool $shared Share flag.
     * @return array
     */
    public static function execute(int    $cmid, int $noteid, float $timecode, string $category,
                                   string $note, bool $shared): array {
        global $DB, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'noteid' => $noteid,
            'timecode' => $timecode,
            'category' => $category,
            'note' => $note,
            'shared' => $shared,
        ]);
        $cm = get_coursemodule_from_id('videonotes', $params['cmid'], 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/videonotes:view', $context);
        $activity = $DB->get_record('videonotes', ['id' => $cm->instance], '*', MUST_EXIST);

        $record = (new note_manager())->save(
            $activity,
            (int)$USER->id,
            (int)$params['noteid'],
            (float)$params['timecode'],
            (string)$params['category'],
            (string)$params['note'],
            (bool)$params['shared']
        );

        $course = get_course($cm->course);
        $completion = new \completion_info($course);
        if ($completion->is_enabled($cm)) {
            $completion->update_state($cm, COMPLETION_UNKNOWN, $USER->id);
        }

        return [
            'id' => (int)$record->id,
            'timecode' => (float)$record->timecode,
            'timestring' => note_manager::format_time((float)$record->timecode),
            'category' => (string)$record->category,
            'categorylabel' => get_string('category:' . $record->category, 'videonotes'),
            'note' => (string)$record->note,
            'shared' => (bool)$record->shared,
            'notecount' => $DB->count_records('videonotes_notes', [
                'videonotesid' => $activity->id,
                'userid' => $USER->id,
            ]),
        ];
    }

    /**
     * Method execute_returns.
     *
     * @return external_single_structure Return value.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Note id'),
            'timecode' => new external_value(PARAM_FLOAT, 'Timestamp'),
            'timestring' => new external_value(PARAM_TEXT, 'Formatted timestamp'),
            'category' => new external_value(PARAM_ALPHANUMEXT, 'Category'),
            'categorylabel' => new external_value(PARAM_TEXT, 'Category label'),
            'note' => new external_value(PARAM_RAW, 'Note text'),
            'shared' => new external_value(PARAM_BOOL, 'Share flag'),
            'notecount' => new external_value(PARAM_INT, 'Current note count'),
        ]);
    }
}
