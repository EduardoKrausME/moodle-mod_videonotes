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
 * Video progress calculations.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videonotes;

/**
 * Maintains watched ranges and calculated percentages.
 */
class progress_manager {
    /**
     * Records a watched segment.
     *
     * @param int $activityid Activity id.
     * @param int $userid User id.
     * @param float $start Segment start.
     * @param float $end Segment end.
     * @param float $position Current position.
     * @param float $duration Duration.
     * @param float $watchtime Real playback time since previous update.
     * @return stdClass
     */
    public function update(int $activityid, int $userid, float $start, float $end, float $position,
                           float $duration, float $watchtime): \stdClass {
        global $DB;

        $now = time();
        $record = $DB->get_record('videonotes_progress', ['videonotesid' => $activityid, 'userid' => $userid]);
        if (!$record) {
            $record = (object)[
                'videonotesid' => $activityid,
                'userid' => $userid,
                'duration' => 0,
                'lastposition' => 0,
                'uniquewatched' => 0,
                'totalwatchtime' => 0,
                'percent' => 0,
                'watchedsegments' => '[]',
                'timecreated' => $now,
                'timemodified' => $now,
            ];
        }

        $duration = max(0.0, min($duration, 86400.0));
        $position = max(0.0, $duration > 0 ? min($position, $duration) : $position);
        $watchtime = max(0.0, min($watchtime, 30.0));
        $segments = json_decode((string)$record->watchedsegments, true);
        if (!is_array($segments)) {
            $segments = [];
        }

        if ($duration > 0 && $end > $start && ($end - $start) <= 30.0) {
            $start = max(0.0, min($start, $duration));
            $end = max($start, min($end, $duration));
            if ($end > $start) {
                $segments[] = [$start, $end];
                $segments = $this->merge_segments($segments);
            }
        }

        $unique = 0.0;
        foreach ($segments as $segment) {
            $unique += max(0.0, (float)$segment[1] - (float)$segment[0]);
        }
        $percent = $duration > 0 ? min(100.0, ($unique / $duration) * 100.0) : 0.0;

        $record->duration = max((float)$record->duration, $duration);
        $record->lastposition = $position;
        $record->uniquewatched = $unique;
        $record->totalwatchtime = max(0.0, (float)$record->totalwatchtime + $watchtime);
        $record->percent = $percent;
        $record->watchedsegments = json_encode($segments);
        $record->timemodified = $now;

        if (empty($record->id)) {
            $record->id = $DB->insert_record('videonotes_progress', $record);
        } else {
            $DB->update_record('videonotes_progress', $record);
        }
        return $record;
    }

    /**
     * Merges overlapping ranges.
     *
     * @param array $segments Watched ranges.
     * @return array
     */
    public function merge_segments(array $segments): array {
        $clean = [];
        foreach ($segments as $segment) {
            if (!is_array($segment) || count($segment) < 2) {
                continue;
            }
            $start = max(0.0, (float)$segment[0]);
            $end = max($start, (float)$segment[1]);
            if ($end > $start) {
                $clean[] = [$start, $end];
            }
        }
        usort($clean, static fn(array $a, array $b): int => $a[0] <=> $b[0]);
        $merged = [];
        foreach ($clean as $segment) {
            if (!$merged || $segment[0] > $merged[count($merged) - 1][1] + 0.75) {
                $merged[] = $segment;
                continue;
            }
            $last = count($merged) - 1;
            $merged[$last][1] = max($merged[$last][1], $segment[1]);
        }
        return $merged;
    }
}
