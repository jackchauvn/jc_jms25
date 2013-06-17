<?php

/*-------------------------------------------------------------------------------
# com_jms - JMS Membership Sites
# -------------------------------------------------------------------------------
# author    			Infoweblink
# copyright 			Copyright (C) 2011 Infoweblink. All Rights Reserved.
# @license 				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlamadesimple.com/
# Technical Support:  	http://www.joomlamadesimple.com/forums
---------------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class iwl_ihub_crm {
	
	/**
	 * Constructor functions, init some parameter
	 * @param object $config
	 */
	function __construct() {
		// Do nothing	
	}
	
	function autoresponder($plan, $user) {
		?>
                    <form name="ibalance dbs - 6 month BD program" action="<?php echo $plan->ihub_crm_url; ?>/modules/Webforms/capture.php" method="post" accept-charset="utf-8">
                        <input type="hidden" name="publicid" value="32fb37a77ca7407c2d9ad742b852da8b"></input>
                        <input type="text" value="<?php echo $plan->firstname; ?>" name="firstname"></input>
                        <?php echo $plan->custom_code; ?>
			<script type="text/javascript">
				setTimeout('document.ibalance dbs - 6 month BD program.submit()', 1000);
			</script>
                    </form>
		<?php
	}
}