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
 * AJAX external functions for Video Notes.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_videonotes_update_progress' => [
        'classname' => 'mod_videonotes\\external\\update_progress',
        'methodname' => 'execute',
        'description' => 'Stores server-authoritative watched video progress.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/videonotes:view',
    ],
    'mod_videonotes_save_note' => [
        'classname' => 'mod_videonotes\\external\\save_note',
        'methodname' => 'execute',
        'description' => 'Creates or updates a timestamped student note.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/videonotes:view',
    ],
    'mod_videonotes_delete_note' => [
        'classname' => 'mod_videonotes\\external\\delete_note',
        'methodname' => 'execute',
        'description' => 'Deletes a student note.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/videonotes:view',
    ],
];
