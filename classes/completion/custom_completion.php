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
 * Custom completion rules.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videonotes\completion;

use core_completion\activity_custom_completion;

/**
 * Completion by watched percentage and minimum note count.
 */
class custom_completion extends activity_custom_completion {
    /**
     * Returns completion state for a rule.
     *
     * @param string $rule Rule name.
     * @return int
     */
    public function get_state(string $rule): int {
        global $DB;

        $this->validate_rule($rule);
        $activity = $DB->get_record('videonotes', ['id' => $this->cm->instance], '*', MUST_EXIST);

        if ($rule === 'completionpercent') {
            if ((int)$activity->completionpercent <= 0) {
                return COMPLETION_COMPLETE;
            }
            $progress = $DB->get_record('videonotes_progress', [
                'videonotesid' => $activity->id,
                'userid' => $this->userid,
            ]);
            return $progress && (float)$progress->percent >= (float)$activity->completionpercent
                ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
        }

        if ((int)$activity->completionnotes <= 0) {
            return COMPLETION_COMPLETE;
        }
        $count = $DB->count_records('videonotes_notes', [
            'videonotesid' => $activity->id,
            'userid' => $this->userid,
        ]);
        return $count >= (int)$activity->completionnotes ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Defines supported custom rules.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return ['completionpercent', 'completionnotes'];
    }

    /**
     * Returns descriptions for enabled rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        global $DB;

        $activity = $DB->get_record('videonotes', ['id' => $this->cm->instance], '*', MUST_EXIST);
        $descriptions = [];
        if ((int)$activity->completionpercent > 0) {
            $descriptions['completionpercent'] = get_string(
                'completiondetail:percent', 'videonotes', (int)$activity->completionpercent
            );
        }
        if ((int)$activity->completionnotes > 0) {
            $descriptions['completionnotes'] = get_string(
                'completiondetail:notes', 'videonotes', (int)$activity->completionnotes
            );
        }
        return $descriptions;
    }

    /**
     * Returns display order for completion rules.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return ['completionview', 'completionpercent', 'completionnotes'];
    }
}
