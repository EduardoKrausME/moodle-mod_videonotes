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
 * Player configuration factory.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videonotes;

/**
 * Builds safe client-side video configuration.
 */
class player_config {
    /**
     * Builds player data.
     *
     * @param stdClass $activity Activity record.
     * @param context_module $context Module context.
     * @return array
     */
    public static function build(\stdClass $activity, \context_module $context): array {
        $source = (int)$activity->videosource;
        $config = ['type' => $source, 'url' => '', 'videoid' => ''];

        if ($source === 0) {
            $files = get_file_storage()->get_area_files(
                $context->id, 'mod_videonotes', 'video', 0, 'itemid, filepath, filename', false
            );
            if ($files) {
                $file = reset($files);
                $config['url'] = \moodle_url::make_pluginfile_url(
                    $context->id,
                    'mod_videonotes',
                    'video',
                    0,
                    $file->get_filepath(),
                    $file->get_filename()
                )->out(false);
            }
        } else if ($source === 1) {
            $config['url'] = clean_param((string)$activity->videourl, PARAM_URL);
        } else if ($source === 2) {
            $config['videoid'] = self::youtube_id((string)$activity->videourl);
        } else if ($source === 3) {
            $config['url'] = clean_param((string)$activity->videourl, PARAM_URL);
        }
        return $config;
    }

    /**
     * Extracts a YouTube video id from common URL formats.
     *
     * @param string $url YouTube URL.
     * @return string
     */
    public static function youtube_id(string $url): string {
        $patterns = [
            '~youtu\\.be/([A-Za-z0-9_-]{6,})~i',
            '~[?&]v=([A-Za-z0-9_-]{6,})~i',
            '~/(?:embed|shorts)/([A-Za-z0-9_-]{6,})~i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $match)) {
                return $match[1];
            }
        }
        return '';
    }
}
