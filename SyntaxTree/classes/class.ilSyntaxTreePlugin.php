<?php
include_once("./Modules/TestQuestionPool/classes/class.ilQuestionsPlugin.php");

/**
* Abstract parent class for all event hook plugin classes.
*
* @author Ferdinand Kuhl <fkuhl@uni-bielefeld.de>
* @version $Id$
*
*/
class ilSyntaxTreePlugin extends ilQuestionsPlugin
{
	/**
	* Get Plugin Name. Must be same as in class name il<Name>Plugin
	* and must correspond to plugins subdirectory name.
	*
	* @return	string	Plugin Name
	*/
	final function getPluginName()
	{
		return "SyntaxTree";
	}
	
	/**
	* Object initialization. Can be overwritten by plugin class
	* (and should be made private final)
	*/
	protected function init()
	{
		// nothing to do
		
	}

	function getQuestionType() {
		return "SyntaxTree";
	}
	
	function getQuestionTypeTranslation() {
		return $this->txt("syntaxtree_question");
	}
	
	/**
	 * Get PHPSyntaxTree-Source and library directory
	 * @return string PHPSyntaxTree-Directory
	 */
	final function getSyntaxTreeSrcDirectory() {
		return $this->getDirectory() . "/phpsyntaxtree_src";
	}
	
	/**
	 * Get PHPSyntaxTree-Image-Path 
	 * @return string PHPSyntaxTree Image Path
	 */
	final function getSyntaxTreeImage() {
		return $this->getDirectory() . "/phpsyntaxtree_obj/stgraph.png";
	}
	
	 /** Get PHPSyntaxTree-SVG-Path 
	 * @return string PHPSyntaxTree SVG Path
	 */
	final function getSyntaxTreeSVG() {
		return $this->getDirectory() . "/phpsyntaxtree_obj/stgraph.svg";
	}
}