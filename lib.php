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
defined('INTERNAL') || die();

class PluginArtefactNote extends PluginArtefact
{

    public static function get_artefact_types()
    {
        return array('note');
    }

    public static function get_block_types()
    {
        return array();
    }

    public static function get_plugin_name()
    {
        return 'note';
    }

    public static function menu_items()
    {
        return array(
            'content/note' => array(
                'path' => 'content/note',
                'url' => 'artefact/note/index.php',
                'title' => get_string('note', 'artefact.note'),
                'weight' => 100,
            )
        );
    }

    public static function get_event_subscriptions()
    {
        return array();
    }

    public static function get_activity_types()
    {
        return array();
    }

    public static function postinst($prevversion)
    {
        return true;
    }

    public static function view_export_extra_artefacts($viewids)
    {
        $artefacts = array();
        //@TODO:
        return $artefacts;
    }

    public static function artefact_export_extra_artefacts($artefactids)
    {
        $artefacts = array();
        //@TODO:
        return $artefacts;
    }
}

class ArtefactTypeNote extends ArtefactType
{

    public function __construct($id = 0, $data = null)
    {
        parent::__construct($id, $data);
        $owner = $this->get('owner');
        if (empty($owner))
        {
            global $USER;
            $this->set('owner', $USER->get('id'));
        }
    }

    public static function is_singular()
    {
        return false;
    }

    public static function get_icon($options=null)
    {
        global $THEME;
        return $THEME->get_url('images/thumb.gif', false, 'artefact/note');
    }

    public static function bulk_delete($artefactids)
    {
        if (empty($artefactids))
        {
            return;
        }

        $idstr = join(',', array_map('intval', $artefactids));

        db_begin();
        delete_records_select(self::TABLE_NAME, 'artefact IN (' . $idstr . ')');
        parent::bulk_delete($artefactids);
        db_commit();
    }

    public static function get_links($id)
    {
        return array(
            '_default' => get_config('wwwroot') . 'artefact/note/view.php?id=' . $id,
        );
    }

    public function can_have_attachments()
    {
        return false;
    }

    public function render_self()
    {
        return array('html' => $this->get('description'));
    }

    public function exportable()
    {
        return true;
    }

}