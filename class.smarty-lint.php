<?

/**
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package smarty-lint
 * @link http://code.google.com/p/smarty-lint/
 * @author Jon Ursenbach <lorderunion@gmail.com>
 * @version 1.0
 */

require dirname(__FILE__) . "/smarty/Smarty.class.php";

ob_start();

$_SERVER["LINT_ERRORS"] = array();
$_SERVER["LINT_FILE"] = "";
$_SERVER["LINT_RESULT"] = "";

set_error_handler("check_errors");
register_shutdown_function("show_errors");

class smarty_lint extends Smarty
{
	function __construct ( )
	{
		$this->caching = 0;
		$this->template_dir = dirname(__FILE__) . "/smarty/templates";
		$this->compile_dir = dirname(__FILE__) . "/smarty/templates_c";
		$this->cache_dir = dirname(__FILE__) . "/smarty/cache";
		$this->config_dir = dirname(__FILE__) . "/smarty/configs";
	}

	/**
	 * Attempt to compile the file we want to run a lint check on.
	 *
	 * @param string $resource_name
	 */
	function check ( $resource_name )
	{
		$_SERVER["LINT_FILE"] = $resource_name;
		parent::fetch($resource_name, null, null, false);
		$this->remove_compiled_tpl($resource_name);
	}

	/**
	 * Remove all compiled templates for the file we're linting. We must do
	 * this, because Smarty does not conform to its own $this->caching = 0
	 * regulation (at least per v2.6.19).
	 *
	 * @param string $resource_name
	 */
	function remove_compiled_tpl ( $resource_name )
	{
		$res = glob($this->compile_dir . "/*" . $resource_name . "*");
		if (is_array($res) && count($res) > 0)
			foreach ($res as $data)
				if (file_exists($data))
					unlink($data);
	}
}

/**
 * Custom error handler so we can parse out errors triggered
 * within the Smarty compiling classes. 
 *
 * @param const $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * 
 * @return bool
 */
function check_errors ( $errno, $errstr, $errfile, $errline )
{
	switch ($errno)
	{
		case E_USER_ERROR:
			$_SERVER["LINT_ERRORS"][] = array(
				"errno" => $errno,
				"errstr" => $errstr,
				"errfile" => $errfile,
				"errline" => $errline
			);
		break;
	}

  	return true;
}

/**
 * Display any errors that occured while compiling a template.
 *
 */
function show_errors ( )
{
	ob_end_clean();

	if (is_array($_SERVER["LINT_ERRORS"]) && count($_SERVER["LINT_ERRORS"]) > 0)
	{
		foreach ($_SERVER["LINT_ERRORS"] as $err)
		{
			$_SERVER["LINT_RESULT"] .= $err["errfile"] . ":" . $err["errline"] . "\n";
			$_SERVER["LINT_RESULT"] .= "\t" . $err["errstr"] . "\n";
		}
	}
	else
		$_SERVER["LINT_RESULT"] .= "No errors present in " . $_SERVER["LINT_FILE"] . ".\n";
		
	echo $_SERVER["LINT_RESULT"];
}

?>