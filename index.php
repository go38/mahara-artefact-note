<?php

/**
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author Laurent Opprecht
 */

require(dirname(__FILE__) . '/ui/init.php');
define('MENUITEM', 'content/note');
define('SECTION_PAGE', 'index');

define('TITLE', get_string('note', 'artefact.note'));

$user_id = $USER->get('id');
$limit = param_integer('limit', 16);
$offset = param_integer('offset', 0);

$notes = get_records_select_array('artefact', "owner=$user_id AND artefacttype='note'", null, 'ctime DESC', '*', $offset, $limit);
$count = get_record_sql("SELECT count(*) as count FROM artefact WHERE owner=$user_id AND artefacttype='note'");
$count = $count->count;

$pagination = build_pagination(array(
    'url' => get_config('wwwroot') . 'artefact/note/index.php',
    'count' => $count,
    'limit' => $limit,
    'offset' => $offset,
    'resultcounttextsingular' => get_string('note', 'artefact.note'),
    'resultcounttextplural' => get_string('notes', 'artefact.note'),
));


$smarty = smarty();
$smarty->assign('PAGEHEADING', hsc(get_string('note', 'artefact.note')));
$smarty->assign('notes', $notes);
$smarty->assign('pagination', $pagination['html']);
$smarty->display('artefact:note:index.tpl');