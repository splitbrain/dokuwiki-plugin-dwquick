<?php
/**
 * DokuWiki Plugin dwquick (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <andi@splitbrain.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class admin_plugin_dwquick extends DokuWiki_Admin_Plugin {

    /** @var helper_plugin_dwquick $hlp */
    protected $hlp = null;

    public function __construct() {
        $this->hlp = plugin_load('helper', 'dwquick');
    }


    /**
     * @return int sort number in admin menu
     */
    public function getMenuSort() {
        return 400;
    }

    /**
     * @return bool true if only access for superuser, false is for superusers and moderators
     */
    public function forAdminOnly() {
        return false;
    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle() {
        global $INPUT;
        $url = trim($INPUT->str('url'));
        if($url && checkSecurityToken()) {
            $handle = $this->hlp->store_url($url, $INPUT->str('handle'));
            msg(sprintf('<a href="http://doku.wiki/%s">http://doku.wiki/%s</a> created', $handle, $handle), 1);
        }
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html() {
        ptln('<h1>'.$this->getLang('menu').'</h1>');

        echo '<h2>Stored URL Shortcuts</h2>';
        $form = new Doku_Form(array('method' => 'POST'));
        $form->addHidden('page', 'dwquick');
        $form->startFieldset('Add new URL Shortcut');
        $form->addElement(form_makeTextField('handle', '', 'Handle (optional)', '', 'block'));
        $form->addElement(form_makeTextField('url', '', 'URL or Page ID', '', 'block'));
        $form->addElement(form_makeButton('submit', 'admin', 'Add'));
        $form->endFieldset();
        $form->printForm();

        $res = $this->hlp->sqlite->query('SELECT * FROM urls ORDER BY handle');
        $rows = $this->hlp->sqlite->res2arr($res);
        $this->hlp->sqlite->res_close($res);

        echo '<br />';
        echo '<table class="inline" style="width: 100%">';
        echo '<tr><th>Handle</th><th>URL or Page ID</th></tr>';
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>';
            echo '<a href="http://doku.wiki/'.$row['handle'].'">'.$row['handle'].'</a>';
            echo '</td>';
            echo '<td>';
            echo '<code>'.hsc($row['url']).'</code>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';

        echo '<h2>Most used 100 Shortcuts</h2>';
        $res = $this->hlp->sqlite->query('SELECT * FROM statistics ORDER BY calls DESC LIMIT 100');
        $rows = $this->hlp->sqlite->res2arr($res);
        $this->hlp->sqlite->res_close($res);

        echo '<br />';
        echo '<table class="inline" style="width: 100%">';
        echo '<tr><th>Count</th><th>Handle</th></tr>';
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>';
            echo $row['calls'];
            echo '</td>';
            echo '<td>';
            echo '<a href="http://doku.wiki/'.$row['handle'].'">'.$row['handle'].'</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}

// vim:ts=4:sw=4:et: