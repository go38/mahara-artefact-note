<?php

defined('INTERNAL') || die();

/**
 * 
 */
class LeapImportNote extends LeapImportArtefactPlugin
{
    const STRATEGY_IMPORT_AS_NOTE = 1;

    /**
     * Runs as the importer is starting up, giving the plugin a chance to do
     * some initialisation.
     *
     * @param PluginImportLeap $importer The importer
     */
    public static function setup(PluginImportLeap $importer)
    {
        
    }

    /**
     * Given an entry, should return a list of the possible ways that it could 
     * be imported by this plugin.
     *
     * The return result is in the form:
     * array(
     *     array(
     *         strategy => [strategy:int],
     *         score    => [score:int],
     *         other_required_entries => array(
     *             [entryid:string],
     *             [entryid:string],
     *             ...
     *         ),
     *     ),
     *     [...],
     * )
     *
     * This can be described as a list of strategies. Each strategy has a 
     * unique (to this class) identifier ([strategy:int]), a score, and a list 
     * of IDs of other entries required to implement this strategy.
     *
     * The strategy is just an identifier for the internal use of the class, to 
     * distinguish between strategies. Most implementors should define class 
     * constants for them, e.g.:
     *
     *     const STRATEGY_IMPORT_AS_FILE = 1;
     *     const STRATEGY_IMPORT_AS_FOLDER = 1;
     *
     * The score represents how well this strategy applies to this entry. 100 
     * is considered an extremely high score (use this for 'I think this is a 
     * perfect match' type strategies).
     *
     * The other required entries is a list of entries this strategy will 
     * require to be implemented. It's a list of entry IDs - a.k.a the contents 
     * of the <id> element of an <entry>.
     *
     * The return result is a list of strategies, which means that you can 
     * provide more than one if you think you have two possible matches. This 
     * method should return everything that is _possible_, even if it's not the 
     * best match, as the user may choose the less obvious method of importing 
     * for some reason.
     *
     * @param SimpleXMLElement $entry    The entry to find import strategies for
     * @param PluginImportLeap $importer The importer
     * @return array A list of strategies that could be used to import this entry
     */
    public static function get_import_strategies_for_entry(SimpleXMLElement $entry, PluginImportLeap $importer)
    {
        $strategies = array();
        if (PluginImportLeap::is_rdf_type($entry, $importer, 'selection') &&
                PluginImportLeap::is_correct_category_scheme($entry, $importer, 'selection_type', 'Note'))
        {
            $strategies[] = array(
                'strategy' => self::STRATEGY_IMPORT_AS_NOTE,
                'score' => 100,
            );
        }
        return $strategies;
    }

    /**
     * Converts an entry into the appropriate artefacts using the given 
     * strategy.
     *
     * The strategy will be one of ones this plugin previously said would be
     * possible for this entry. This method may throw an ImportException if it 
     * is not.
     *
     * This method is quite tied to get_import_strategies_for_entry: if that 
     * method exports a certain strategy (with a certain list of other required 
     * entries), then if that strategy is chosen, this method will be invoked 
     * with that strategy and that list of other required entries. HOWEVER, you 
     * cannot assume that both method calls will happen in the same request - a 
     * UI may be presented to the user to make them choose strategies in 
     * between these steps, for example. So don't store state between them!
     *
     * Regarding other entries: based on the previous statement, this class 
     * said it required them to import this entry, so they should be necessary 
     * to complete the import of the entry. Alternatively, perhaps you 
     * recognise that importing them makes no sense when you import this entry. 
     * But be aware that your class is denying these entries to other classes 
     * if you do this!
     *
     * This method should return a list of entry ID => (list of artefact IDs):
     *
     * array(
     *     [entryid:string] => array([artefactid:int], [artefactid:int], ...),
     *     [entryid:string] => array([artefactid:int], [artefactid:int], ...),
     *     ...
     * )
     *
     * This list informs the importer of how each entry was converted into 
     * artefact(s). Often, an entry will be converted into just one artefact, 
     * but there's no reason why it might not be convereted into more.
     *
     * This information is used by setup_relationships() hooks to work out how 
     * entries were converted to artefacts, so for example, files can be 
     * attached to blog posts even though the files and blog posts were 
     * imported by different plugins.
     *
     * @param SimpleXMLElement $entry    The entry to import
     * @param PluginImportLeap $importer The importer
     * @param int $strategy              The strategy to use (should be a class 
     *                                   constant on your class, see the documentation
     *                                   of get_import_strategies_for_entry for more
     *                                   information)
     * @param array $otherentries        A list of entry IDs that this class 
     *                                   previously said were required to import 
     *                                   the entry
     * @throws ImportException If the strategy is unrecognised
     * @return array A list describing what artefacts were created by the 
     *               import of each entry
     */
    public static function import_using_strategy(SimpleXMLElement $entry, PluginImportLeap $importer, $strategy, array $otherentries)
    {
        $result = array();
        switch ($strategy)
        {
            case self::STRATEGY_IMPORT_AS_NOTE:
                $note = new ArtefactTypeNote();
                $note->set('title', (string) $entry->title);
                $note->set('description', PluginImportLeap::get_entry_content($entry, $importer));
                $note->set('owner', $importer->get('usr'));
                if ($published = strtotime((string) $entry->published))
                {
                    $note->set('ctime', $published);
                }
                if ($updated = strtotime((string) $entry->updated))
                {
                    $note->set('mtime', $updated);
                }
                $note->set('tags', PluginImportLeap::get_entry_tags($entry));
                $note->commit();
                $result[(string) $entry->id] = array($note->get('id'));
                break;
            default:
                throw new ImportException($importer, 'Unknown strategy chosen for importing entry');
        }
        return $result;
    }

    /**
     * Gives plugins a chance to import author data
     *
     * This gets passed the entry ID for the entry that represents the person
     * who is being imported, should there be such an entry. This method can
     * then dig through it to create artefacts. Contrast this with exporting
     * persondata in the plugin's export implementation. A plugin might export
     * a persondata field there, and then look for it again here.
     *
     * @param PluginImportLeap $importer The importer
     * @param string $persondataid       The entry ID for the persondata entry.
     *                                   May be empty if no such entry was
     *                                   found in the import.
     */
    public static function import_author_data(PluginImportLeap $importer, $persondataid)
    {
        
    }

    /**
     * Gives plugins a chance to construct relationships between the newly 
     * created artefacts.
     *
     * This hook is optional. If implemented, plugins get access to the entries 
     * they imported, and the strategy they used to import them. It is 
     * guaranteed that all other plugins have created the artefacts they wanted 
     * to create, and implementors of this hook can use 
     * $importer->get_artefactids_imported_by_entryid to get access to the 
     * artefacts they need.
     *
     * This method has no return value.
     *
     * @param SimpleXMLElement $entry    The entry previously imported
     * @param PluginImportLeap $importer The importer
     * @param int $strategy              The strategy to use (should be a class 
     *                                   constant on your class, see the documentation
     *                                   of get_import_strategies_for_entry for more
     *                                   information)
     * @param array $otherentries     A list of entry IDs that this class 
     *                                previously said were required to import 
     *                                the entry
     * @throws ImportException If the strategy is unrecognised
     */
    public static function setup_relationships(SimpleXMLElement $entry, PluginImportLeap $importer, $strategy, array $otherentries)
    {
        
    }

    /**
     * Gives plugins a chance to construct relationships between the newly
     * created artefacts and newly created views.
     */
    public static function setup_view_relationships(SimpleXMLElement $entry, PluginImportLeap $importer, $strategy, array $otherentries)
    {
        
    }

    /**
     * Runs after the importer has finished, to allow the plugin to perform any
     * cleanup operations.
     *
     * @param PluginImportLeap $importer The importer
     */
    public static function cleanup(PluginImportLeap $importer)
    {
        
    }

}
