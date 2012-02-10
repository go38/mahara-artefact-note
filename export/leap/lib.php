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

class LeapExportElementNote extends LeapExportElement
{

    public function get_leap_type()
    {
        return 'selection';
    }

    public function get_categories()
    {
        return array(
            array(
                'scheme' => 'selection_type',
                'term' => 'Note',
            )
        );
    }

    public function get_content_type()
    {
        return 'html';
    }

}
