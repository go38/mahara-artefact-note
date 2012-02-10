<?php

/**
 *
 * @package    mahara
 * @subpackage artefact-note-note
 * @author     laurent.opprecht@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2011 University of Geneva http://www.unige.ch/
 *
 */
defined('INTERNAL') || die();

class PluginBlocktypeNote extends PluginBlocktype
{

    public static function get_title()
    {
        return self::get_string('title');
    }

    public static function get_description()
    {
        return self::get_string('description');
    }

    public static function get_categories()
    {
        return array('general');
    }

    /**
     * Optional method. If exists, allows this class to decide the title for 
     * all blockinstances of this type
     */
    public static function get_instance_title(BlockInstance $instance)
    {
        $configdata = $instance->get('configdata');

        if (!empty($configdata['artefactid']))
        {
            return $instance->get_artefact_instance($configdata['artefactid'])->get('title');
        }
        return '';
    }

    /**
     * Allows block types to override the instance's title.
     *
     * For example: My Views, My Groups, My Friends, Wall
     */
    public static function override_instance_title(BlockInstance $instance)
    {
        $configdata = $instance->get('configdata');

        if (!empty($configdata['artefactid']))
        {
            return $instance->get_artefact_instance($configdata['artefactid'])->get('title');
        }
        return '';
    }
    
    public static function render_instance(BlockInstance $instance, $editing=false)
    {
        $configdata = $instance->get('configdata');

        if (empty($configdata['artefactid']))
        {
            return;
        }

        $result = '';
        //require_once(get_config('docroot') . 'artefact/lib.php');
        $note = $instance->get_artefact_instance($configdata['artefactid']);
        $content = $note->get('description');

        $smarty = smarty_core();
        $configdata = $instance->get('configdata');
        $smarty->assign('content', $content);
        return $smarty->fetch('blocktype:note:content.tpl');
    }

    // Yes, we do have instance config. People are allowed to specify the title 
    // of the block, nothing else at this time. So in the next two methods we 
    // say yes and return no fields, so the title will be configurable.
    public static function has_instance_config()
    {
        return true;
    }

    public static function instance_config_form($instance)
    {
        global $USER;
        $configdata = $instance->get('configdata');
        $elements = array();
        $elements[] = self::artefactchooser_element((isset($configdata['artefactid'])) ? $configdata['artefactid'] : null);
        return $elements;
    }


    public static function artefactchooser_element($default=null) {
        return array(
            'name'  => 'artefactid',
            'type'  => 'artefactchooser',
            'title' => self::get_string('note'),
            'defaultvalue' => $default,
            'blocktype' => 'note',
            'limit'     => 10,
            'selectone' => true,
            'artefacttypes' => array('note'),
            //'template'  => 'artefact:note:artefactchooser-element.tpl',
        );
    }

    public static function default_copy_type()
    {
        return 'shallow';
    }

    public static function allowed_in_view(View $view)
    {
        return true;
    }

    protected static function get_string($text)
    {
        return get_string($text, 'blocktype.note/note');
    }

}
