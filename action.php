<?php
/**
 * DokuWiki Plugin dwquick (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <andi@splitbrain.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_dwquick extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {

       $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_action_act_preprocess');
   
    }

    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */

    public function handle_action_act_preprocess(Doku_Event &$event, $param) {
        $act = act_clean($event->data);
        if($act != 'dwquick') return;
        $event->preventDefault();
        $event->stopPropagation();

        global $INPUT;

        /** @var helper_plugin_dwquick $hlp */
        $hlp = plugin_load('helper', 'dwquick');


        $hlp->match($INPUT->str('go'));
        die();


    }

}

// vim:ts=4:sw=4:et:
