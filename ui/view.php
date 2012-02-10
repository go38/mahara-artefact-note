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

if (empty($note_id))
{
    return;
}

$data = get_record('artefact', 'id', $note_id);

$title = $data->title;
$description = $data->description;
$tags = $data->tags;
define('TITLE', $title);

$smarty = smarty();
$smarty->assign_by_ref('form', $form);
$smarty->assign('PAGEHEADING', $title);
$smarty->assign('description', $description);
$smarty->assign('tags', $tags);
$smarty->display('artefact:note:view.tpl');
