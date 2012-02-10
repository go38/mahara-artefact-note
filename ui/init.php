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
define('INTERNAL', 1);
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'note');

require(dirname(__FILE__) . '/../../../init.php');
safe_require('artefact', 'note');