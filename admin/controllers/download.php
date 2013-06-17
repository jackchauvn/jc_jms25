<?php

/**
 * @version     2.0.2
 * @package		Joomla
 * @subpackage	Joomla Membership Sites
 * @author		Infoweblink
 * @authorEmail	support@infoweblink.com 
 * @home page	http://joomlasubscriptionsites.com/ 
 * @copyright	Copyright (C) 2011. Infoweblink. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * This component manages Subscriptions for members to access to Joomla Resource
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Plan controller class.
 */
class JmsControllerDownload extends JControllerForm
{

    function __construct()
    {
        $this->view_list = 'downloads';
        parent::__construct();
    }

    function download_file()
    {

        require_once(JPATH_COMPONENT."/helpers/class.chip_download.php");

        $download_path = "../";
        $file = $_REQUEST['link'];
        
        $args = array(
            'download_path' => $download_path,
            'file' => $file,
            'extension_check' => TRUE,
            'referrer_check' => FALSE,
            'referrer' => NULL,
        );
        $download = new chip_download($args);

        /*
          |-----------------
          | Pre Download Hook
          |------------------
         */

        $download_hook = $download->get_download_hook();
//$download->chip_print($download_hook);
//exit;
          
        /*
          |-----------------
          | Download
          |------------------
         */

        if ($download_hook['download'] == TRUE)
        {

            /* You can write your logic before proceeding to download */

            /* Let's download file */
            $download->get_download();
            
        }
    }

}