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
 * Student note management.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videonotes;

/**
 * Handles ownership, categories and teacher sharing.
 */
class note_manager {
    /** @var string[] */
    public const CATEGORIES = ['important', 'question', 'review', 'example'];

    /**
     * Saves a note owned by the current user.
     *
     * @param stdClass $activity Activity record.
     * @param int $userid User id.
     * @param int $noteid Existing id or zero.
     * @param float $timecode Video position.
     * @param string $category Category.
     * @param string $text Note text.
     * @param bool $shared Share with teacher.
     * @return stdClass
     */
    public function save(\stdClass $activity, int $userid, int $noteid, float $timecode,
                         string    $category, string $text, bool $shared): \stdClass {
        global $DB;

        if (!in_array($category, self::CATEGORIES, true)) {
            $category = 'important';
        }
        $text = trim($text);
        if ($text === '') {
            throw new \moodle_exception('emptynote', 'videonotes');
        }
        $now = time();
        if ($noteid) {
            $record = $DB->get_record('videonotes_notes', [
                'id' => $noteid,
                'videonotesid' => $activity->id,
                'userid' => $userid,
            ], '*', MUST_EXIST);
        } else {
            $record = (object)[
                'videonotesid' => $activity->id,
                'userid' => $userid,
                'timecreated' => $now,
            ];
        }
        $record->timecode = max(0.0, $timecode);
        $record->category = $category;
        $record->note = $text;
        $record->shared = (int)((int)$activity->notemode === 1 && $shared);
        $record->timemodified = $now;
        if (empty($record->id)) {
            $record->id = $DB->insert_record('videonotes_notes', $record);
        } else {
            $DB->update_record('videonotes_notes', $record);
        }
        return $record;
    }

    /**
     * Deletes a note if it belongs to the user.
     *
     * @param int $activityid Activity id.
     * @param int $userid User id.
     * @param int $noteid Note id.
     * @return bool
     */
    public function delete(int $activityid, int $userid, int $noteid): bool {
        global $DB;
        return $DB->delete_records('videonotes_notes', [
            'id' => $noteid,
            'videonotesid' => $activityid,
            'userid' => $userid,
        ]);
    }

    /**
     * Returns a user's notes ordered by video position.
     *
     * @param int $activityid Activity id.
     * @param int $userid User id.
     * @return array
     */
    public function get_user_notes(int $activityid, int $userid): array {
        global $DB;
        return array_values($DB->get_records('videonotes_notes', [
            'videonotesid' => $activityid,
            'userid' => $userid,
        ], 'timecode ASC, id ASC'));
    }

    /**
     * Formats seconds as H:MM:SS or MM:SS.
     *
     * @param float $seconds Seconds.
     * @return string
     */
    public static function format_time(float $seconds): string {
        $seconds = max(0, (int)round($seconds));
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;
        return $hours > 0 ? sprintf('%d:%02d:%02d', $hours, $minutes, $secs) : sprintf('%02d:%02d', $minutes, $secs);
    }
}
