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
 * Activity configuration form.
 *
 * @package   mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_videonotes\player_config;

global $CFG;
require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Main activity form.
 */
class mod_videonotes_mod_form extends moodleform_mod {
    /**
     * Defines the form.
     *
     * @return void
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('videonotesname', 'videonotes'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $this->standard_intro_elements();

        $mform->addElement('html', '<h3>' . get_string('videosettings', 'videonotes') . '</h3>');
        $mform->addElement('select', 'videosource', get_string('videosource', 'videonotes'), [
            0 => get_string('sourceupload', 'videonotes'),
            1 => get_string('sourceurl', 'videonotes'),
            2 => get_string('sourceyoutube', 'videonotes'),
            3 => get_string('sourcevimeo', 'videonotes'),
        ]);
        $mform->setDefault('videosource', 1);

        $mform->addElement('filemanager', 'videofile', get_string('videofile', 'videonotes'), null, [
            'subdirs' => 0,
            'accepted_types' => ['video'],
        ]);
        $mform->hideIf('videofile', 'videosource', 'neq', 0);

        $mform->addElement('url', 'videourl', get_string('videourl', 'videonotes'), ['size' => 80], ['usefilepicker' => false]);
        $mform->setType('videourl', PARAM_URL);
        $mform->hideIf('videourl', 'videosource', 'eq', 0);

        $mform->addElement('select', 'resumeplayback', get_string('resumeplayback', 'videonotes'), [
            0 => get_string('resumefromstart', 'videonotes'),
            1 => get_string('resumeautomatic', 'videonotes'),
            2 => get_string('resumeask', 'videonotes'),
        ]);
        $mform->setDefault('resumeplayback', 1);

        $mform->addElement('selectyesno', 'allowseek', get_string('allowseek', 'videonotes'));
        $mform->setDefault('allowseek', 1);

        $mform->addElement('html', '<h3>' . get_string('notesettings', 'videonotes') . '</h3>');
        $mform->addElement('select', 'notemode', get_string('notemode', 'videonotes'), [
            0 => get_string('notemodeprivate', 'videonotes'),
            1 => get_string('notemodeshareoptional', 'videonotes'),
        ]);
        $mform->setDefault('notemode', 0);
        $mform->addHelpButton('notemode', 'notemode', 'videonotes');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Adds custom completion rule form elements.
     *
     * @return array
     */
    public function add_completion_rules(): array {
        $mform = $this->_form;

        $mform->addElement('text', 'completionpercent', get_string('completionpercent', 'videonotes'), ['size' => 5]);
        $mform->setType('completionpercent', PARAM_INT);
        $mform->setDefault('completionpercent', 0);
        $mform->addHelpButton('completionpercent', 'completionpercent', 'videonotes');

        $mform->addElement('text', 'completionnotes', get_string('completionnotes', 'videonotes'), ['size' => 5]);
        $mform->setType('completionnotes', PARAM_INT);
        $mform->setDefault('completionnotes', 0);
        $mform->addHelpButton('completionnotes', 'completionnotes', 'videonotes');

        return ['completionpercent', 'completionnotes'];
    }

    /**
     * Validates custom completion configuration.
     *
     * @param array $data Submitted values.
     * @return bool
     */
    public function completion_rule_enabled($data): bool {
        return !empty($data['completionpercent']) || !empty($data['completionnotes']);
    }

    /**
     * Validates form data.
     *
     * @param array $data Submitted values.
     * @param array $files Submitted files.
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if ((int)$data['videosource'] !== 0 && empty($data['videourl'])) {
            $errors['videourl'] = get_string('required');
        }
        if (!empty($data['completionpercent']) && ((int)$data['completionpercent'] < 1 || (int)$data['completionpercent'] > 100)) {
            $errors['completionpercent'] = get_string('completionpercenterror', 'videonotes');
        }
        if (!empty($data['completionnotes']) && (int)$data['completionnotes'] < 1) {
            $errors['completionnotes'] = get_string('completionnoteserror', 'videonotes');
        }
        if ((int)$data['videosource'] === 2 && !empty($data['videourl']) &&
            player_config::youtube_id((string)$data['videourl']) === '') {
            $errors['videourl'] = get_string('invalidyoutubeurl', 'videonotes');
        }
        if ((int)$data['videosource'] === 3 && !empty($data['videourl']) &&
            (!preg_match('~vimeo\.com~i', $data['videourl']) || !preg_match('~/[0-9]+(?:$|[?/])~', $data['videourl']))) {
            $errors['videourl'] = get_string('invalidvimeourl', 'videonotes');
        }
        foreach (['videofile'] as $field) {
            $draftid = (int)($data[$field] ?? 0);
            if ($draftid > 0) {
                $draftinfo = file_get_draft_area_info($draftid);
                if ((int)$draftinfo['filecount'] > 1) {
                    $errors[$field] = get_string('errormaxfiles', 'videonotes');
                }
            }
        }
        return $errors;
    }

    /**
     * Prepares the uploaded file draft area.
     *
     * @param array $defaultvalues Default values.
     * @return void
     */
    public function data_preprocessing(&$defaultvalues): void {
        parent::data_preprocessing($defaultvalues);
        if (empty($this->current->instance)) {
            return;
        }
        $context = context_module::instance($this->current->coursemodule);
        $draftid = file_get_submitted_draft_itemid('videofile');
        file_prepare_draft_area($draftid, $context->id, 'mod_videonotes', 'video', 0, [
            'subdirs' => 0,
            'maxfiles' => 1,
        ]);
        $defaultvalues['videofile'] = $draftid;
    }
}
