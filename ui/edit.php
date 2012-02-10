<?php

/**
 *
 * @package    mahara
 * @subpackage artefact-note
 * @author     laurent.opprecht@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2011 University of Geneva http://www.unige.ch/
 *
 */
require_once dirname(__FILE__) . '/init.php';

define('MENUITEM', 'content/note');
define('SECTION_PAGE', 'note');

$note_id = param_integer('id', 0);
$user_id = $USER->get('id');

if ($note_id)
{
    $data = get_record('artefact', 'id', $note_id);
    if ($data->owner != $user_id)
    {
        throw new AccessDeniedException(get_string('notallowed', 'artefact.note'));
    }
    
    $obj = new ArtefactTypeNote($note_id);
    $title = $data->title;
    $description = $data->description;
    $tags = $obj->get('tags');
    $pagetitle = get_string('editnote', 'artefact.note');
    define('TITLE', $pagetitle);
}
else
{
    $title = '';
    $description = '';
    $tags = '';
    $pagetitle = get_string('newnote', 'artefact.note');
    define('TITLE', $pagetitle);
}


$form = pieform(array(
    'name' => 'editnote',
    'method' => 'post',
    'jsform' => false,
    'newiframeonsubmit' => true,    
    'plugintype' => 'artefact',
    'pluginname' => 'note',
    'configdirs' => array(get_config('libroot') . 'form/', get_config('docroot') . 'artefact/file/form/'),
    'elements' => array(
        'note_id' => array(
            'type' => 'hidden',
            'value' => $note_id,
        ),
        'title' => array(
            'type' => 'text',
            'title' => get_string('title', 'artefact.note'),
            'rules' => array(
                'required' => true
            ),
            'defaultvalue' => $title,
        ),
        'description' => array(
            'type' => 'wysiwyg',
            'rows' => 20,
            'cols' => 70,
            'title' => get_string('content', 'artefact.note'),
            //'description' => get_string('description', 'artefact.note'),
            'rules' => array(
                'maxlength' => 65536,
                'required' => true
            ),
            'defaultvalue' => $description,
        ),
        'tags' => array(
            'defaultvalue' => $tags,
            'type' => 'tags',
            'title' => get_string('tags'),
            'description' => get_string('tagsdesc'),
            'help' => true,
        ),
        'submitpost' => array(
            'type' => 'submitcancel',
            'value' => array(get_string('save', 'artefact.note'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/note/index.php',
        )
    )
        ));

$smarty = smarty();
$smarty->assign_by_ref('form', $form);
$smarty->assign('PAGEHEADING', $pagetitle);
$smarty->display('artefact:note:edit.tpl');

/**
 * This function get called to cancel the form submission. 
 */
function editnote_cancel_submit()
{
    redirect(get_config('wwwroot') . 'artefact/note/index.php');
}

function editnote_submit(Pieform $form, $values)
{
    global $USER, $SESSION;

    db_begin();
    $note = new ArtefactTypeNote($values['note_id']);
    $note->set('title', $values['title']);
    $note->set('description', $values['description']);
    $note->set('tags', $values['tags']);
    $note->commit();
    $id = $note->get('id');

    $result = array(
        'error' => false,
        'message' => get_string('saved', 'artefact.note'),
        'goto' => get_config('wwwroot') . 'artefact/note/index.php',
    );
    if ($form->submitted_by_js())
    {
        $SESSION->add_ok_msg($result['message']);
        $form->json_reply(PIEFORM_OK, $result, false);
    }
    $form->reply(PIEFORM_OK, $result);
}