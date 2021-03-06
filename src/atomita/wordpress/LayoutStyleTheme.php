<?php

namespace atomita\wordpress;

/**
 * LayoutStyleTheme
 * 
 * @author atomita
 */
class LayoutStyleTheme
{
	protected $layout	   = 'default';
	protected $varname	   = 'contents';
	protected $layouts_dir = 'layout';
	protected $priority;
	
	/**
	 * @param	$layouts_dir	string	layouts directory name
	 * @param	$priority	int	priority of wordpress filter
	 */
	function __construct($layouts_dir = 'layout', $priority = PHP_INT_MAX)
	{
		$this->layouts_dir = $layouts_dir;
		$this->priority    = $priority;
	}
	
	/**
	 * set layout-name and get
	 */
	function layout($name = '')
	{
		if (!empty($name)){
			$this->layout = $name;
		}
		return $this->layout;
	}
	
	/**
	 * set contents-varname and get
	 */
	function varname($name = '')
	{
		if (!empty($name)){
			$this->varname = $name;
		}
		return $this->varname;
	}

	/**
	 * set wordpress filter
	 */
	function initialize()
	{
		add_filter('template_include', array($this, 'apply'), $this->priority);
	}

	/**
	 * remove wordpress filter
	 */
	function uninitialize()
	{
		remove_filter('template_include', array($this, 'apply'), $this->priority);
	}

	/**
	 * call from "template_include" filter
	 */
	function apply($template) {
		global $wp, $wp_query, $wp_the_query;
		
		ob_start();
		include $template;
		${$this->varname} = ob_get_clean();
		
		$layouts = array();
		if ('default' != $this->layout) {
			$layouts[] = $this->layout;
		}
		$layouts[]= 'default';

		$paths = array();
		foreach($layouts as $layout) {
			$paths[] =  $this->layouts_dir . '/' . $layout . '.php';
		}
		if ($path = locate_template($paths, false)) {
			include $path;
		}
		else {
			include rtrim(__FILE__, '.php') . DIRECTORY_SEPARATOR . 'default.php';
		}
		return false;
	}

}
