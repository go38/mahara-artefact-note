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

define('TITLE', get_string('deletenote', 'artefact.note'));

$form = pieform(array(
    'name' => 'deletenote',
    'autofocus' => false,
    'method' => 'post',
    'renderer' => 'div',
    'elements' => array(
        'submit' => array(
            'type' => 'submitcancel',
            'value' => array(get_string('yes'), get_string('no')),
            'goto' => get_config('wwwroot') . 'artefact/note/index.php',
        )
    ),
        ));

$smarty = smarty();
$smarty->assign('PAGEHEADING', TITLE);
$smarty->assign('form', $form);
$smarty->assign('confirm', get_string('deletenoteconfirm', 'artefact.note'));
$smarty->display('artefact:note:delete.tpl');

function deletenote_submit(Pieform $form, $values)
{
    $id = param_integer('id');
    $artefact = new ArtefactTypeNote($id);
    $artefact->delete();
    redirect('/artefact/note/index.php');
}
