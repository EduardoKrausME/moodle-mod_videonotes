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
 * External progress update API.
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
use mod_videonotes\progress_manager;

/**
 * AJAX endpoint for validated watched ranges.
 */
class update_progress extends external_api {

    /**
     * Method execute_parameters.
     *
     * @return external_function_parameters Return value.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id'),
            'segmentstart' => new external_value(PARAM_FLOAT, 'Watched segment start'),
            'segmentend' => new external_value(PARAM_FLOAT, 'Watched segment end'),
            'currentposition' => new external_value(PARAM_FLOAT, 'Current player position'),
            'duration' => new external_value(PARAM_FLOAT, 'Video duration'),
            'watchtime' => new external_value(PARAM_FLOAT, 'Elapsed real playback time'),
        ]);
    }

    /**
     * Stores watched progress.
     *
     * @param int $cmid Course module id.
     * @param float $segmentstart Segment start.
     * @param float $segmentend Segment end.
     * @param float $currentposition Current position.
     * @param float $duration Duration.
     * @param float $watchtime Real playback time.
     * @return array
     */
    public static function execute(int $cmid, float $segmentstart, float $segmentend,
                                   float $currentposition, float $duration, float $watchtime): array {
        global $DB, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'segmentstart' => $segmentstart,
            'segmentend' => $segmentend,
            'currentposition' => $currentposition,
            'duration' => $duration,
            'watchtime' => $watchtime,
        ]);
        $cm = get_coursemodule_from_id('videonotes', $params['cmid'], 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/videonotes:view', $context);
        $activity = $DB->get_record('videonotes', ['id' => $cm->instance], '*', MUST_EXIST);

        $progress = (new progress_manager())->update(
            (int)$activity->id,
            (int)$USER->id,
            (float)$params['segmentstart'],
            (float)$params['segmentend'],
            (float)$params['currentposition'],
            (float)$params['duration'],
            (float)$params['watchtime']
        );

        $course = get_course($cm->course);
        $completion = new \completion_info($course);
        if ($completion->is_enabled($cm)) {
            $completion->update_state($cm, COMPLETION_UNKNOWN, $USER->id);
        }

        return [
            'percent' => (float)$progress->percent,
            'lastposition' => (float)$progress->lastposition,
            'uniquewatched' => (float)$progress->uniquewatched,
            'totalwatchtime' => (float)$progress->totalwatchtime,
            'segments' => (string)$progress->watchedsegments,
        ];
    }

    /**
     * Method execute_returns.
     *
     * @return external_single_structure Return value.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'percent' => new external_value(PARAM_FLOAT, 'Watched unique percentage'),
            'lastposition' => new external_value(PARAM_FLOAT, 'Last player position'),
            'uniquewatched' => new external_value(PARAM_FLOAT, 'Unique watched seconds'),
            'totalwatchtime' => new external_value(PARAM_FLOAT, 'Total playback seconds'),
            'segments' => new external_value(PARAM_RAW, 'Merged watched segments JSON'),
        ]);
    }
}
