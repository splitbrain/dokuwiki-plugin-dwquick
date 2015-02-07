<?php
/**
 * DokuWiki Plugin dwquick (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <andi@splitbrain.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_dwquick extends DokuWiki_Plugin {
    /**
     * maximum levenshtein distance for fuzzy search
     */
    const FUZZY = 1;

    /**
     * @var array namespaces to search (in order of importance)
     */
    private $namespaces = array('', 'faq:', 'plugin:', 'devel:', 'config:', 'template:', 'install:');

    /** @var helper_plugin_sqlite */
    private $sqlite = null;

    /**
     * initialize the DB
     */
    public function __construct() {
        // load the helper plugin
        $this->sqlite = plugin_load('helper', 'sqlite');
        // initialize the database connection
        $this->sqlite->init('dwquick', __DIR__.'/db/');
    }

    /**
     * Find a matching page
     *
     * @param string $search
     */
    public function match($search) {
        $id = cleanID($search);
        $this->inc_counter($id);

        $result = '';
        if(!$id) $result = 'https://www.dokuwiki.org';
        if(!$result) $result = $this->match_db($id);
        if(!$result) $result = $this->match_page($id);
        if(!$result) $result = 'http://search.dokuwiki.org/'.urlencode($search);

        if(!preg_match('/^https?:\/\//', $result)) $result = 'https://www.dokuwiki.org/'.$result;

        echo $result;
        // FIXME send_redirect($result);
    }

    /**
     * Increase the call counter for statistics
     *
     * @param $id
     */
    protected function inc_counter($id) {
        $this->sqlite->query("INSERT OR IGNORE INTO statistics (handle, calls) VALUES (?, 0)", $id);
        $this->sqlite->query("UPDATE statistics SET calls = calls+1 WHERE handle = ?", $id);
    }

    /**
     * Tries to find a matching entry in the database
     *
     * @param $id
     * @return bool|string
     */
    protected function match_db($id) {
        $res = $this->sqlite->query('SELECT url FROM urls WHERE handle = ?', $id);
        $result = $this->sqlite->res2single($res);
        $this->sqlite->res_close($res);
        return $result;
    }

    /**
     * Tries to find a matching page
     *
     * @param $id
     * @return bool|string either the best matching page or false if none found
     */
    protected function match_page($id) {
        $Indexer = idx_get_indexer();
        $pages   = $Indexer->getPages();

        $results = array();
        foreach($pages as $page) {
            foreach($this->namespaces as $score => $ns) {
                if($page == $ns.$id) {
                    // exact match
                    $results[$page] = (double) $score * 2;
                } else if(substr($id, -1) != 's' && $page == $ns.$id.'s') {
                    // plural match
                    $results[$page] = (double) $score * 2 + 1;
                } else if(substr($id, -1) == 's' && $page == $ns.substr($id, 0, -1)) {
                    // singular match
                    $results[$page] = (double) $score * 2 + 1;
                } else {
                    $lvnst = levenshtein($page, $ns.$id);
                    if($lvnst <= self::FUZZY) {
                        // fuzzy match
                        $results[$page] = (double) $score * 2 + 1 + $lvnst / 10;
                    }
                }
            }
        }
        asort($results); // lowest score is best

        // check that the found page exists
        foreach(array_keys($results) as $result) {
            if(page_exists($result)) return $result;
        }

        return false;
    }

}

// vim:ts=4:sw=4:et:
