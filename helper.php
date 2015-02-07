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
     * Return info about supported methods in this Helper Plugin
     *
     * @return array of public methods
     */
    public function getMethods() {
        return array(
            array(
                'name'   => 'getThreads',
                'desc'   => 'returns pages with discussion sections, sorted by recent comments',
                'params' => array(
                    'namespace'         => 'string',
                    'number (optional)' => 'integer'
                ),
                'return' => array('pages' => 'array')
            ),
            array(
                // and more supported methods...
            )
        );
    }

}

// vim:ts=4:sw=4:et:
