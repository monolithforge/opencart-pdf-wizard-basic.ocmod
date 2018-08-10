<?php

/**
 * @category   MonolithForge/OpenCart/PdfWizard
 * @package    monolithforge-opencart-pdf-wizard
 * @author     Original Author <support@monolithforge.com>
 * @copyright  2017-2018 Monolith Forge, LLC
 * @license    https://www.monolithforge.com/license/pdf-wizard-basic-license.txt
 * @version    3-6-beta2
 */

static $registry = null;

// Error Handler
function error_handler_for_pdf_wizard($errno, $errstr, $errfile, $errline) {
	global $registry;
	
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$errors = "Notice";
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$errors = "Warning";
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$errors = "Fatal Error";
			break;
		default:
			$errors = "Unknown";
			break;
	}
	
	$config = $registry->get('config');
	$url = $registry->get('url');
	$request = $registry->get('request');
	$session = $registry->get('session');
	$log = $registry->get('log');
	
	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	if (($errors=='Warning') || ($errors=='Unknown')) {
		return true;
	}

	$dir = version_compare(VERSION,'3.0','>=') ? 'extension' : 'tool';
	if (($errors != "Fatal Error") && isset($request->get['route']) && ($request->get['route']!="$dir/pdf_wizard/download"))  {
		if ($config->get('config_error_display')) {
			echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
		}
	} else {
		$session->data['pdf_wizard_error'] = array( 'errstr'=>$errstr, 'errno'=>$errno, 'errfile'=>$errfile, 'errline'=>$errline );
		$token = version_compare(VERSION,'3.0','>=') ? $request->get['user_token'] : $request->get['token'];
		$link = $url->link( "$dir/pdf_wizard", version_compare(VERSION,'3.0','>=') ? 'user_token='.$token : 'token='.$token, true );
		header('Status: ' . 302);
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $link));
		exit();
	}

	return true;
}


function fatal_error_shutdown_handler_for_pdf_wizard()
{
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		// fatal error
		error_handler_for_pdf_wizard(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	}
}


class ModelToolPdfWizard extends Model {

	private $error = array();
	protected $null_array = array();
	protected $use_table_seo_url = false;


	public function __construct( $registry ) {
		parent::__construct( $registry );
		$this->use_table_seo_url = version_compare(VERSION,'3.0','>=') ? true : false;
	}

}


if (version_compare(VERSION,'3.0','>=')) {
	class ModelExtensionPdfWizard extends ModelToolPdfWizard {
	}
}

?>