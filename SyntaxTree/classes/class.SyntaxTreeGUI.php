<?php
/*
 +----------------------------------------------------------------------------+
 | ILIAS open source                                                          |
 +----------------------------------------------------------------------------+
 | Copyright (c) 1998-2001 ILIAS open source, University of Cologne           |
 |                                                                            |
 | This program is free software; you can redistribute it and/or              |
 | modify it under the terms of the GNU General Public License                |
 | as published by the Free Software Foundation; either version 2             |
 | of the License, or (at your option) any later version.                     |
 |                                                                            |
 | This program is distributed in the hope that it will be useful,            |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of             |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the              |
 | GNU General Public License for more details.                               |
 |                                                                            |
 | You should have received a copy of the GNU General Public License          |
 | along with this program; if not, write to the Free Software                |
 | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA. |
 +----------------------------------------------------------------------------+
 */

include_once "./Modules/TestQuestionPool/classes/class.assQuestionGUI.php";
include_once "./Modules/Test/classes/inc.AssessmentConstants.php";

/**
 * SyntaxTree question GUI representation
 *
 * The SyntaxTreeGUI class encapsulates the GUI representation
 * for multiple choice questions.
 *
 * @author		Helmut Schottmüller <helmut.schottmueller@mac.com>
 * @author		Ferdinand Kuhl <fkuhl@coolys.de>
 * @version	    $Id$
 * @ingroup ModulesTestQuestionPool
 * @ilCtrl_IsCalledBy SyntaxTreeGUI: ilObjQuestionPoolGUI
 */
class SyntaxTreeGUI extends assQuestionGUI
{

	protected $cssDir;
	protected $ownDir;
	/**
	 * SyntaxTreeGUI constructor
	 *
	 * The constructor takes possible arguments an creates an instance of the SyntaxTreeGUI object.
	 *
	 * @param integer $id The database id of a text subset question object
	 * @access public
	 * @ilCtrl_IsCalledBy SyntaxTreeGUI: ilObjQuestionPoolGUI
	 */
	function __construct(	$id = -1)
	{
		$this->ownDir = "./Customizing/global/plugins/Modules/TestQuestionPool/Questions/SyntaxTree";
		$this->cssDir = "./Customizing/global/plugins/Modules/TestQuestionPool/Questions/SyntaxTree/css";
		parent::__construct();
		include_once "class.SyntaxTree.php";
		$this->object = new SyntaxTree();
		if ($id >= 0)
		{
			$this->object->loadFromDb($id);
		}
	}

	function getCommand($cmd)
	{
		return $cmd;
	}

	/**
	 * Creates an output of the edit form for the question
	 *
	 * Creates an output of the edit form for the question
	 *
	 * @access public
	 */
	function editQuestion()
	{
		$this->tpl->addJavascript("./Services/JavaScript/js/Basic.js");
		$this->tpl->addCss($this->cssDir . "/default.css");
		$this->tpl->addJavascript($this->ownDir . "/js/bracketCount.js");
		$this->tpl->addJavascript($this->ownDir . "/js/url_encode.js");
		$javascript = "<script type=\"text/javascript\">ilAddOnLoad(initialSelect);\n".
			"function initialSelect() {\n%s\n}</script>";
		// single response
		$this->getQuestionTemplate();

		$this->tpl->addBlockFile("QUESTION_DATA", "question_data", "tpl.il_as_qpl_syntaxtree.html", $this->ownDir);
		// output of existing single response answers
		for ($i = 0; $i < $this->object->getAnswerCount(); $i++)
		{
			$this->tpl->setCurrentBlock("answers");
			$answer = $this->object->getAnswer($i);
			$this->tpl->setVariable("VALUE_ANSWER_COUNTER", $answer->getOrder() + 1);
			$this->tpl->setVariable("ANSWER_ORDER", $answer->getOrder());
			$this->tpl->setVariable("VALUE_ANSWER", ilUtil::prepareFormOutput($answer->getAnswertext()));
			$this->tpl->setVariable("VALUE_POINTS", ilUtil::prepareFormOutput($answer->getPoints()));
			$this->tpl->setVariable("TEXT_PHRASE_HEADER",$this->lng->txt("qpl_qst_syntaxtree_phrase_header"));
			$this->tpl->setVariable("TEXT_OPEN_BRACKETS", $this->lng->txt("qpl_qst_syntaxtree_open_brackets"));
			$this->tpl->setVariable("TEXT_CLOSED_BRACKETS", $this->lng->txt("qpl_qst_syntaxtree_closed_brackets"));
			/* SyntaxTreeAddon */
			$img = "<img src=\"".sprintf($this->ownDir."/phpsyntaxtree_obj/stgraph.png?syntaxTreeData=%s\" id=\"syntaxTreeGraph\" alt=\"\">",urlencode($answer->getAnswertext()));
			$this->tpl->setVariable("GRAPH",  sprintf( "%s", $img ));

			$this->tpl->parseCurrentBlock();
		}
		if ($this->object->getAnswerCount() > 0)
		{
			$this->tpl->setCurrentBlock("answersheading");
			$this->tpl->setVariable("TEXT_ANSWER_TEXT", $this->lng->txt("answer_text"));
			$this->tpl->setVariable("TEXT_POINTS", $this->lng->txt("points"));
			$this->tpl->parseCurrentBlock();
		}

		$internallinks = array(
			"lm" => $this->lng->txt("obj_lm"),
			"st" => $this->lng->txt("obj_st"),
			"pg" => $this->lng->txt("obj_pg"),
			"glo" => $this->lng->txt("glossary_term")
		);
		foreach ($internallinks as $key => $value)
		{
			$this->tpl->setCurrentBlock("internallink");
			$this->tpl->setVariable("TYPE_INTERNAL_LINK", $key);
			$this->tpl->setVariable("TEXT_INTERNAL_LINK", $value);
			$this->tpl->parseCurrentBlock();
		}

		$this->tpl->setCurrentBlock("HeadContent");
		if ($this->object->getAnswerCount() == 0)
		{
			$this->tpl->setVariable("CONTENT_BLOCK", sprintf($javascript, "document.frm_syntaxtree.title.focus();"));
		}
		else
		{
			switch ($this->ctrl->getCmd())
			{
				case "add":
					$this->tpl->setVariable("CONTENT_BLOCK", sprintf($javascript, "document.frm_syntaxtree.answer_".($this->object->getAnswerCount() - $_POST["nrOfAnswers"]).".focus(); document.getElementById('answer_".($this->object->getAnswerCount() - $_POST["nrOfAnswers"])."').scrollIntoView(\"true\");"));
					break;
				case "":
					if ($this->object->getAnswerCount() == 0)
					{
						$this->tpl->setVariable("CONTENT_BLOCK", sprintf($javascript, "document.frm_syntaxtree.title.focus();"));
					}
					else
					{
						$this->tpl->setVariable("CONTENT_BLOCK", sprintf($javascript, "document.frm_syntaxtree.answer_".($this->object->getAnswerCount() - 1).".focus(); document.getElementById('answer_".($this->object->getAnswerCount() - 1)."').scrollIntoView(\"true\");"));
					}
					break;
				default:
					$this->tpl->setVariable("CONTENT_BLOCK", sprintf($javascript, "document.frm_syntaxtree.title.focus();"));
					break;
			}
		}
		$this->tpl->parseCurrentBlock();

		// Add text rating options
		$text_options = array(
		array("ci", $this->lng->txt("cloze_textgap_case_insensitive")),
		array("cs", $this->lng->txt("cloze_textgap_case_sensitive")),
		array("l1", sprintf($this->lng->txt("cloze_textgap_levenshtein_of"), "1")),
		array("l2", sprintf($this->lng->txt("cloze_textgap_levenshtein_of"), "2")),
		array("l3", sprintf($this->lng->txt("cloze_textgap_levenshtein_of"), "3")),
		array("l4", sprintf($this->lng->txt("cloze_textgap_levenshtein_of"), "4")),
		array("l5", sprintf($this->lng->txt("cloze_textgap_levenshtein_of"), "5"))
		);
		$text_rating = $this->object->getTextRating();
		foreach ($text_options as $text_option)
		{
			$this->tpl->setCurrentBlock("text_rating");
			$this->tpl->setVariable("RATING_VALUE", $text_option[0]);
			$this->tpl->setVariable("RATING_TEXT", $text_option[1]);
			if (strcmp($text_rating, $text_option[0]) == 0)
			{
				$this->tpl->setVariable("SELECTED_RATING_VALUE", " selected=\"selected\"");
			}
			$this->tpl->parseCurrentBlock();
		}

		if ($this->object->getAnswerCount() > 0)
		{
			$this->tpl->setCurrentBlock("selectall");
			$this->tpl->setVariable("SELECT_ALL", $this->lng->txt("select_all"));
			$this->tpl->parseCurrentBlock();
			$this->tpl->setCurrentBlock("existinganswers");
			$this->tpl->setVariable("DELETE", $this->lng->txt("delete"));
			$this->tpl->setVariable("MOVE", $this->lng->txt("move"));
			$this->tpl->setVariable("ARROW", "<img src=\"" . ilUtil::getImagePath("arrow_downright.gif") . "\" alt=\"".$this->lng->txt("arrow_downright")."\">");

			$this->tpl->parseCurrentBlock();
		}

		for ($i = 1; $i < 10; $i++)
		{
			$this->tpl->setCurrentBlock("numbers");
			$this->tpl->setVariable("VALUE_NUMBER", $i);
			if ($i == 1)
			{
				$this->tpl->setVariable("TEXT_NUMBER", $i . " " . $this->lng->txt("answer"));
			}
			else
			{
				$this->tpl->setVariable("TEXT_NUMBER", $i . " " . $this->lng->txt("answers"));
			}
			$this->tpl->parseCurrentBlock();
		}

		$this->tpl->setCurrentBlock("question_data");
		$this->tpl->setVariable("SYNTAXTREE_ID", $this->object->getId());
		$this->tpl->setVariable("VALUE_SYNTAXTREE_TITLE", ilUtil::prepareFormOutput($this->object->getTitle()));
		$this->tpl->setVariable("VALUE_SYNTAXTREE_COMMENT", ilUtil::prepareFormOutput($this->object->getComment()));
		$this->tpl->setVariable("VALUE_SYNTAXTREE_AUTHOR", ilUtil::prepareFormOutput($this->object->getAuthor()));
		$this->tpl->setVariable("VALUE_CORRECTANSWERS", $this->object->getCorrectAnswers());
		$this->tpl->setVariable("VALUE_POINTS", $this->object->getMaximumPoints());
		$questiontext = $this->object->getQuestion();
		$this->tpl->setVariable("VALUE_QUESTION", ilUtil::prepareFormOutput($this->object->prepareTextareaOutput($questiontext)));
		$this->tpl->setVariable("VALUE_ADD_ANSWER", $this->lng->txt("add"));
		$this->tpl->setVariable("TEXT_TITLE", $this->lng->txt("title"));
		$this->tpl->setVariable("TEXT_AUTHOR", $this->lng->txt("author"));
		$this->tpl->setVariable("TEXT_COMMENT", $this->lng->txt("description"));
		$this->tpl->setVariable("TEXT_QUESTION", $this->lng->txt("question"));
		$this->tpl->setVariable("TEXT_SOLUTION_HINT", $this->lng->txt("solution_hint"));
		$this->tpl->setVariable("TEXT_RATING", $this->lng->txt("text_rating"));
		$this->tpl->setVariable("TEXT_POINTS", $this->lng->txt("maximum_points"));
		$this->tpl->setVariable("TEXT_CORRECTANSWERS", $this->lng->txt("nr_of_correct_answers"));

		// estimated working time
		$est_working_time = $this->object->getEstimatedWorkingTime();
		$this->tpl->setVariable("TEXT_WORKING_TIME", $this->lng->txt("working_time"));
		$this->tpl->setVariable("TIME_FORMAT", $this->lng->txt("time_format"));
		$this->tpl->setVariable("VALUE_WORKING_TIME", ilUtil::makeTimeSelect("Estimated", false, $est_working_time[h], $est_working_time[m], $est_working_time[s]));

		$this->tpl->setVariable("SAVE",$this->lng->txt("save"));
		$this->tpl->setVariable("SAVE_EDIT", $this->lng->txt("save_edit"));
		$this->tpl->setVariable("CANCEL",$this->lng->txt("cancel"));
		$this->tpl->setVariable("TXT_REQUIRED_FLD", $this->lng->txt("required_field"));
		$this->ctrl->setParameter($this, "sel_question_types", "SyntaxTree");
		$this->tpl->setVariable("ACTION_SYNTAXTREE_TEST", $this->ctrl->getFormAction($this));
		$this->tpl->setVariable("TEXT_QUESTION_TYPE", $this->lng->txt("qpl_qst_syntaxtree_syntaxtree_question"));
		//$this->outOtherQuestionData();

		$this->tpl->parseCurrentBlock();
		include_once "./Services/RTE/classes/class.ilRTE.php";
		$rtestring = ilRTE::_getRTEClassname();
		include_once "./Services/RTE/classes/class.$rtestring.php";
		$rte = new $rtestring();
		$rte->addPlugin("latex");
		$rte->addButton("latex"); $rte->addButton("pastelatex");
		include_once "./classes/class.ilObject.php";
		$obj_id = $_GET["q_id"];
		$obj_type = ilObject::_lookupType($_GET["ref_id"], TRUE);
		$rte->addRTESupport($obj_id, $obj_type, "assessment");

		$this->tpl->setCurrentBlock("adm_content");
		//$this->tpl->setVariable("BODY_ATTRIBUTES", " onload=\"initialSelect();\"");
		$this->tpl->parseCurrentBlock();
	}

	/**
	 * add an answer(s)
	 */
	function deleteAnswer()
	{
		$this->writePostData();
		$answers = $_POST["chb_answers"];
		if (is_array($answers))
		{
			arsort($answers);
			foreach ($answers as $answer)
			{
				$this->object->deleteAnswer($answer);
			}
		}
		$this->editQuestion();
	}

	/**
	 * add an answer
	 */
	function add()
	{
		//$this->setObjectData();
		$this->writePostData();

		for ($i = 0; $i < $_POST["nrOfAnswers"]; $i++)
		{
			$this->object->addAnswer(
				"",
			1,
			count($this->object->answers)
			);
		}

		$this->editQuestion();
	}

	function save()
	{
		$unfilled_answer = false;
		foreach ($_POST as $key => $value)
		{
			if (preg_match("/answer_(\d+)/", $key, $matches))
			{
				if (!$value)
				{
					$unfilled_answer = true;
				}
			}
		}
		if ($unfilled_answer)
		{
			ilUtil::sendInfo($this->lng->txt("qpl_answertext_fields_not_filled"));
			$this->writePostData();
			$this->editQuestion();
		}
		else
		{
			parent::save();
		}
	}

	function saveEdit()
	{
		$unfilled_answer = false;
		foreach ($_POST as $key => $value)
		{
			if (preg_match("/answer_(\d+)/", $key, $matches))
			{
				if (!$value)
				{
					$unfilled_answer = true;
				}
			}
		}
		if ($unfilled_answer)
		{
			ilUtil::sendInfo($this->lng->txt("qpl_answertext_fields_not_filled"));
			$this->writePostData();
			$this->editQuestion();
		}
		else
		{
			parent::saveEdit();
		}
	}

	/**
	 * check input fields
	 */
	function checkInput()
	{
		$cmd = $this->ctrl->getCmd();

		if ((!$_POST["title"]) or (!$_POST["author"]) or (!$_POST["question"]) or (!$_POST["correctanswers"]))
		{
			return false;
		}
		return true;
	}

	/**
	 * Evaluates a posted edit form and writes the form data in the question object
	 *
	 * Evaluates a posted edit form and writes the form data in the question object
	 *
	 * @return integer A positive value, if one of the required fields wasn't set, else 0
	 * @access private
	 */
	function writePostData()
	{
		$result = 0;
		if (!$this->checkInput())
		{
			$result = 1;
		}

		if (($result) and (strcmp($this->ctrl->getCmd(), "add") == 0))
		{
			// You cannot add answers before you enter the required data
			ilUtil::sendInfo($this->lng->txt("fill_out_all_required_fields_add_answer"));
		}

		$this->object->setTitle(ilUtil::stripSlashes($_POST["title"]));
		$this->object->setAuthor(ilUtil::stripSlashes($_POST["author"]));
		$this->object->setComment(ilUtil::stripSlashes($_POST["comment"]));
		include_once "./Services/AdvancedEditing/classes/class.ilObjAdvancedEditing.php";
		$questiontext = ilUtil::stripSlashes($_POST["question"], false, ilObjAdvancedEditing::_getUsedHTMLTagsAsString("assessment"));
		$this->object->setQuestion($questiontext);
		$this->object->setSuggestedSolution($_POST["solution_hint"], 0);
		$this->object->setCorrectAnswers($_POST["correctanswers"]);
		$this->object->setTextRating($_POST["text_rating"]);
		$this->object->setEstimatedWorkingTime(
			$_POST["Estimated"]["h"],
			$_POST["Estimated"]["m"],
			$_POST["Estimated"]["s"]
		);

		//$saved = $this->writeOtherPostData($result);

		// Delete all existing answers and create new answers from the form data
		$this->object->flushAnswers();

		// Add all answers from the form into the object
		foreach ($_POST as $key => $value)
		{
			if (preg_match("/answer_(\d+)/", $key, $matches))
			{
				$this->object->addAnswer(
				ilUtil::stripSlashes($_POST["$key"]),
				ilUtil::stripSlashes($_POST["points_".$matches[1]]),
				ilUtil::stripSlashes($matches[1])
				);
			}
		}

		// Set the question id from a hidden form parameter
		if ($_POST["syntaxtree_id"] > 0)
		{
			$this->object->setId($_POST["syntaxtree_id"]);
		}

		$maximum_points = $this->object->getMaximumPoints();
		if (($maximum_points <= 0) && (count($this->object->answers) > 0))
		{
			$result = 1;
			$this->setErrorMessage($this->lng->txt("enter_enough_positive_points"));
		}
		$this->object->setPoints($maximum_points);

		if ($saved)
		{
			// If the question was saved automatically before an upload, we have to make
			// sure, that the state after the upload is saved. Otherwise the user could be
			// irritated, if he presses cancel, because he only has the question state before
			// the upload process.
			$this->object->saveToDb();
			$this->ctrl->setParameter($this, "q_id", $this->object->getId());
		}

		return $result;
	}

	function outQuestionForTest($formaction, $active_id, $pass = NULL, $is_postponed = FALSE, $use_post_solutions = FALSE)
	{
		$test_output = $this->getTestOutput($active_id, $pass, $is_postponed, $use_post_solutions);
		$this->tpl->setVariable("QUESTION_OUTPUT", $test_output);
		$this->tpl->setVariable("FORMACTION", $formaction);
	}

	function getSolutionOutput($active_id, $pass = NULL, $graphicalOutput = FALSE, $result_output = FALSE, $show_question_only = TRUE, $show_feedback = FALSE, $show_correct_solution = FALSE)
	{
		// get the solution of the user for the active pass or from the last pass if allowed
		$solutions = array();
		if (($active_id > 0) && (!$show_correct_solution))
		{
			$solutions =& $this->object->getSolutionValues($active_id, $pass);
		}
		else
		{
			$rank = array();
			foreach ($this->object->answers as $answer)
			{
				if ($answer->getPoints() > 0)
				{
					if (!is_array($rank[$answer->getPoints()]))
					{
						$rank[$answer->getPoints()] = array();
					}
					array_push($rank[$answer->getPoints()], $answer->getAnswertext());
				}
			}
			krsort($rank, SORT_NUMERIC);
			foreach ($rank as $index => $bestsolutions)
			{
				array_push($solutions, array("value1" => join(",", $bestsolutions), "points" => $index));
			}
		}

		// generate the question output
		include_once "./classes/class.ilTemplate.php";
		$template = new ilTemplate("tpl.il_as_qpl_syntaxtree_output_solution.html", TRUE, TRUE, $this->ownDir);
		$solutiontemplate = new ilTemplate("tpl.il_as_tst_solution_output.html", TRUE, TRUE, "Modules/TestQuestionPool");
		$available_answers =& $this->object->getAvailableAnswers();
		for ($i = 0; $i < $this->object->getCorrectAnswers(); $i++)
		{
			if ((!$test_id) && (strcmp($solutions[$i]["value1"], "") == 0))
			{
			}
			else
			{
				if (($active_id > 0) && (!$show_correct_solution))
				{
					if ($graphicalOutput)
					{
						// output of ok/not ok icons for user entered solutions
						$index = $this->object->isAnswerCorrect($available_answers, $solutions[$i]["value1"]);
						$correct = FALSE;
						if ($index !== FALSE)
						{
							unset($available_answers[$index]);
							$correct = TRUE;
						}
						if ($correct)
						{
							$template->setCurrentBlock("icon_ok");
							$template->setVariable("ICON_OK", ilUtil::getImagePath("icon_ok.gif"));
							$template->setVariable("TEXT_OK", $this->lng->txt("answer_is_right"));
							$template->parseCurrentBlock();
						}
						else
						{
							$template->setCurrentBlock("icon_ok");
							$template->setVariable("ICON_NOT_OK", ilUtil::getImagePath("icon_not_ok.gif"));
							$template->setVariable("TEXT_NOT_OK", $this->lng->txt("answer_is_wrong"));
							$template->parseCurrentBlock();
						}
					}
				}
				$template->setCurrentBlock("syntaxtree_row");
				$template->setVariable("SOLUTION", $solutions[$i]["value1"]);
				$template->setVariable("COUNTER", $i+1);
				if ($result_output)
				{
					$points = $solutions[$i]["points"];
					$resulttext = ($points == 1) ? "(%s " . $this->lng->txt("point") . ")" : "(%s " . $this->lng->txt("points") . ")";
					$template->setVariable("RESULT_OUTPUT", sprintf($resulttext, $points));
				}
				$template->parseCurrentBlock();
			}
		}
		$questiontext = $this->object->getQuestion();
		$template->setVariable("QUESTIONTEXT", $this->object->prepareTextareaOutput($questiontext, TRUE));
		$questionoutput = $template->get();
		$feedback = ($show_feedback) ? $this->getAnswerFeedbackOutput($active_id, $pass) : "";
		if (strlen($feedback)) $solutiontemplate->setVariable("FEEDBACK",  $feedback);
		$solutiontemplate->setVariable("SOLUTION_OUTPUT", $questionoutput);

		$solutionoutput = $solutiontemplate->get();
		if (!$show_question_only)
		{
			// get page object output
			$solutionoutput = $this->getILIASPage($solutionoutput);
		}
		return $solutionoutput;
	}

	function getPreview($show_question_only = FALSE)
	{
		// generate the question output
		include_once "./classes/class.ilTemplate.php";
		$template = new ilTemplate("tpl.il_as_qpl_syntaxtree_output.html", TRUE, TRUE, $this->ownDir);

		$width = $this->object->getMaxTextboxWidth();
		for ($i = 0; $i < $this->object->getCorrectAnswers(); $i++)
		{
			$template->setCurrentBlock("syntaxtree_row");
			$template->setVariable("COUNTER", $i+1);
			$template->setVariable("TEXTFIELD_ID", sprintf("%02d", $i+1));
			$template->setVariable("TEXTFIELD_SIZE", $width);
			$template->setVariable("TEXT_PHRASE_HEADER",$this->lng->txt("qpl_qst_syntaxtree_phrase_header"));
			$template->setVariable("TEXT_OPEN_BRACKETS", $this->lng->txt("qpl_qst_syntaxtree_open_brackets"));
			$template->setVariable("TEXT_CLOSED_BRACKETS", $this->lng->txt("qpl_qst_syntaxtree_closed_brackets"));
			$template->setVariable("TEXT_YOUR_GRAPH", $this->lng->txt("qpl_qst_syntaxtree_your_graph"));
			$template->setVariable("GRAPHPATH", $this->ownDir . "/phpsyntaxtree_obj/stgraph.png");
			$template->parseCurrentBlock();
		}
		$questiontext = $this->object->getQuestion();
		$template->setVariable("QUESTIONTEXT", $this->object->prepareTextareaOutput($questiontext, TRUE));
		$this->tpl->addCss($this->cssDir . "/default.css");
		$this->tpl->addJavascript($this->ownDir . "/js/bracketCount.js");
		$this->tpl->addJavascript($this->ownDir . "/js/url_encode.js");
		$questionoutput = $template->get();
		if (!$show_question_only)
		{
			// get page object output
			$questionoutput = $this->getILIASPage($questionoutput);
		}
		return $questionoutput;
	}

	function getTestOutput($active_id, $pass = NULL, $is_postponed = FALSE, $use_post_solutions = FALSE)
	{
		// get page object output
		$pageoutput = $this->outQuestionPage("", $is_postponed, $active_id);

		// get the solution of the user for the active pass or from the last pass if allowed
		$user_solution = "";
		if ($active_id)
		{
			$solutions = NULL;
			include_once "./Modules/Test/classes/class.ilObjTest.php";
			if (!ilObjTest::_getUsePreviousAnswers($active_id, true))
			{
				if (is_null($pass)) $pass = ilObjTest::_getPass($active_id);
			}
			$solutions =& $this->object->getSolutionValues($active_id, $pass);
		}

		// generate the question output
		include_once "./classes/class.ilTemplate.php";
		$template = new ilTemplate("tpl.il_as_qpl_syntaxtree_output.html", TRUE, TRUE, $this->ownDir);
		$this->tpl->addCss($this->cssDir . "/default.css");
		$this->tpl->addJavascript($this->ownDir . "/js/bracketCount.js");
		$this->tpl->addJavascript($this->ownDir . "/js/url_encode.js");
		$width = $this->object->getMaxTextboxWidth();
		for ($i = 0; $i < $this->object->getCorrectAnswers(); $i++)
		{
			$template->setCurrentBlock("syntaxtree_row");
			foreach ($solutions as $idx => $solution_value)
			{
				if ($idx == $i)
				{
					$template->setVariable("TEXTFIELD_VALUE", $solution_value["value1"]);
				}
			}
			$template->setVariable("COUNTER", $i+1);
			$template->setVariable("TEXTFIELD_ID", sprintf("%02d", $i+1));
			$template->setVariable("TEXTFIELD_SIZE", $width);
			$template->setVariable("GRAPHPATH", $this->ownDir . "/phpsyntaxtree_obj/stgraph.png");
			$template->setVariable("TEXT_PHRASE_HEADER",$this->lng->txt("qpl_qst_syntaxtree_phrase_header"));
			$template->setVariable("TEXT_OPEN_BRACKETS", $this->lng->txt("qpl_qst_syntaxtree_open_brackets"));
			$template->setVariable("TEXT_CLOSED_BRACKETS", $this->lng->txt("qpl_qst_syntaxtree_closed_brackets"));
			$template->setVariable("TEXT_YOUR_GRAPH", $this->lng->txt("qpl_qst_syntaxtree_your_graph"));
			$template->parseCurrentBlock();
		}
		$questiontext = $this->object->getQuestion();
		$template->setVariable("QUESTIONTEXT", $this->object->prepareTextareaOutput($questiontext, TRUE));
		$questionoutput = $template->get();
		$pageoutput = $this->outQuestionPage("", $is_postponed, $active_id,  $questionoutput);
		return $pageoutput;
	}

#	function addSuggestedSolution()
#	{
#		$_SESSION["subquestion_index"] = 0;
#		if ($_POST["cmd"]["addSuggestedSolution"])
#		{
#			if ($this->writePostData())
#			{
#				ilUtil::sendInfo($this->getErrorMessage());
#				$this->editQuestion();
#				return;
#			}
#			if (!$this->checkInput())
#			{
#				ilUtil::sendInfo($this->lng->txt("fill_out_all_required_fields_add_answer"));
#				$this->editQuestion();
#				return;
#			}
#		}
#		$this->object->saveToDb();
#		$this->ctrl->setParameter($this, "q_id", $this->object->getId());
#		$this->tpl->setVariable("HEADER", $this->object->getTitle());
#		$this->getQuestionTemplate();
#		parent::addSuggestedSolution();
#	}

	/**
	 * Saves the feedback for a single choice question
	 *
	 * Saves the feedback for a single choice question
	 *
	 * @access public
	 *
	function saveFeedback()
	{
		include_once "./Services/AdvancedEditing/classes/class.ilObjAdvancedEditing.php";
		$this->object->saveFeedbackGeneric(0, ilUtil::stripSlashes($_POST["feedback_incomplete"], false, ilObjAdvancedEditing::_getUsedHTMLTagsAsString("assessment")));
		$this->object->saveFeedbackGeneric(1, ilUtil::stripSlashes($_POST["feedback_complete"], false, ilObjAdvancedEditing::_getUsedHTMLTagsAsString("assessment")));
		$this->object->cleanupMediaObjectUsage();
		parent::saveFeedback();
    }*/

	/**
	* Saves the feedback for a single choice question
	*
	* @access public
	*/
	function saveFeedback()
	{
		include_once "./Services/AdvancedEditing/classes/class.ilObjAdvancedEditing.php";
		$errors = $this->feedback(true);
		$this->object->saveFeedbackGeneric(0, $_POST["feedback_incomplete"]);
		$this->object->saveFeedbackGeneric(1, $_POST["feedback_complete"]);
		foreach ($this->object->answers as $index => $answer)
		{
			$this->object->saveFeedbackSyntaxTree($index, $_POST["feedback_answer_$index"]);
		}
		$this->object->cleanupMediaObjectUsage();
		parent::saveFeedback();
	}
    
    /**
	* Creates the output of the feedback page for a single choice question
	*
	* @access public
	*/
	function feedback($checkonly = false)
	{
		$save = (strcmp($this->ctrl->getCmd(), "saveFeedback") == 0) ? TRUE : FALSE;
		include_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle($this->lng->txt('feedback_answers'));
		$form->setTableWidth("100%");
		$form->setId("feedback");

		$complete = new ilTextAreaInputGUI($this->lng->txt("feedback_complete_solution"), "feedback_complete");
		$complete->setValue($this->object->prepareTextareaOutput($this->object->getFeedbackGeneric(1)));
		$complete->setRequired(falsse);
		$complete->setRows(10);
		$complete->setCols(80);
		$complete->setUseRte(true);
		include_once "./Services/AdvancedEditing/classes/class.ilObjAdvancedEditing.php";
		$complete->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
		$complete->addPlugin("latex");
		$complete->addButton("latex");
		$complete->addButton("pastelatex");
		$complete->setRTESupport($this->object->getId(), "qpl", "assessment");
		$form->addItem($complete);

		$incomplete = new ilTextAreaInputGUI($this->lng->txt("feedback_incomplete_solution"), "feedback_incomplete");
		$incomplete->setValue($this->object->prepareTextareaOutput($this->object->getFeedbackGeneric(0)));
		$incomplete->setRequired(false);
		$incomplete->setRows(10);
		$incomplete->setCols(80);
		$incomplete->setUseRte(true);
		include_once "./Services/AdvancedEditing/classes/class.ilObjAdvancedEditing.php";
		$incomplete->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
		$incomplete->addPlugin("latex");
		$incomplete->addButton("latex");
		$incomplete->addButton("pastelatex");
		$incomplete->setRTESupport($this->object->getId(), "qpl", "assessment");
		$form->addItem($incomplete);

		if (!$this->getSelfAssessmentEditingMode())
		{
			foreach ($this->object->answers as $index => $answer)
			{
				$answerobj = new ilTextAreaInputGUI($this->object->prepareTextareaOutput($answer->getAnswertext(), true), "feedback_answer_$index");
				$answerobj->setValue($this->object->prepareTextareaOutput($this->object->getFeedbackSyntaxTree($index)));
				$answerobj->setRequired(false);
				$answerobj->setRows(10);
				$answerobj->setCols(80);
				$answerobj->setUseRte(true);
				include_once "./Services/AdvancedEditing/classes/class.ilObjAdvancedEditing.php";
				$answerobj->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
				$answerobj->addPlugin("latex");
				$answerobj->addButton("latex");
				$answerobj->addButton("pastelatex");
				$answerobj->setRTESupport($this->object->getId(), "qpl", "assessment");
				$form->addItem($answerobj);
			}
		}

		$form->addCommandButton("saveFeedback", $this->lng->txt("save"));
		if ($save)
		{
			$form->setValuesByPost();
			$errors = !$form->checkInput();
			$form->setValuesByPost(); // again, because checkInput now performs the whole stripSlashes handling and we need this if we don't want to have duplication of backslashes
		}
		if (!$checkonly) $this->tpl->setVariable("ADM_CONTENT", $form->getHTML());
		return $errors;
	}

	/**
	 * Creates the output of the feedback page for a single choice question
	 *
	 * Creates the output of the feedback page for a single choice question
	 *
	 * @access public
	 *
	function feedback()
	{
		$this->tpl->addBlockFile("ADM_CONTENT", "feedback", "tpl.il_as_qpl_syntaxtree_feedback.html", "Modules/TestQuestionPool");
		$this->tpl->setVariable("FEEDBACK_TEXT", $this->lng->txt("feedback"));
		$this->tpl->setVariable("FEEDBACK_COMPLETE", $this->lng->txt("feedback_complete_solution"));
		$this->tpl->setVariable("VALUE_FEEDBACK_COMPLETE", ilUtil::prepareFormOutput($this->object->prepareTextareaOutput($this->object->getFeedbackGeneric(1)), FALSE));
		$this->tpl->setVariable("FEEDBACK_INCOMPLETE", $this->lng->txt("feedback_incomplete_solution"));
		$this->tpl->setVariable("VALUE_FEEDBACK_INCOMPLETE", ilUtil::prepareFormOutput($this->object->prepareTextareaOutput($this->object->getFeedbackGeneric(0)), FALSE));
		$this->tpl->setVariable("SAVE", $this->lng->txt("save"));
		$this->tpl->setVariable("FORMACTION", $this->ctrl->getFormAction($this));

		include_once "./Services/RTE/classes/class.ilRTE.php";
		$rtestring = ilRTE::_getRTEClassname();
		include_once "./Services/RTE/classes/class.$rtestring.php";
		$rte = new $rtestring();
		$rte->addPlugin("latex");
		$rte->addButton("latex"); $rte->addButton("pastelatex");
		include_once "./classes/class.ilObject.php";
		$obj_id = $_GET["q_id"];
		$obj_type = ilObject::_lookupType($_GET["ref_id"], TRUE);
		$rte->addRTESupport($obj_id, $obj_type, "assessment");
    }*/

	/*
	 * Sets the ILIAS tabs for this question type
	 *
	 * Sets the ILIAS tabs for this question type
	 *
	 * @access public
	 */
	function setQuestionTabs()
	{
		global $rbacsystem, $ilTabs;

		$this->ctrl->setParameterByClass("ilpageobjectgui", "q_id", $_GET["q_id"]);
		include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
		$q_type = $this->object->getQuestionType();

		if (strlen($q_type))
		{
			$classname = $q_type . "GUI";
			$this->ctrl->setParameterByClass(strtolower($classname), "sel_question_types", $q_type);
			$this->ctrl->setParameterByClass(strtolower($classname), "q_id", $_GET["q_id"]);
		}

		if ($_GET["q_id"])
		{
			if ($rbacsystem->checkAccess('write', $_GET["ref_id"]))
			{
				// edit page
				$ilTabs->addTarget("edit_content",
				$this->ctrl->getLinkTargetByClass("ilPageObjectGUI", "edit"),
				array("edit", "insert", "exec_pg"),
					"", "", $force_active);
			}

			// edit page
			$ilTabs->addTarget("preview",
			$this->ctrl->getLinkTargetByClass("ilPageObjectGUI", "preview"),
			array("preview"),
				"ilPageObjectGUI", "", $force_active);
		}

		$force_active = false;
		if ($rbacsystem->checkAccess('write', $_GET["ref_id"]))
		{
			$url = "";
			if ($classname) $url = $this->ctrl->getLinkTargetByClass($classname, "editQuestion");
			// edit question properties
			$ilTabs->addTarget("edit_properties",
			$url,
			array("editQuestion", "save", "cancel", "addSuggestedSolution",
					"cancelExplorer", "linkChilds", "removeSuggestedSolution", "add", "deleteAnswer", 
					"saveEdit"),
			$classname, "", $force_active);
		}

		if ($_GET["q_id"])
		{
			$ilTabs->addTarget("feedback",
			$this->ctrl->getLinkTargetByClass($classname, "feedback"),
			array("feedback", "saveFeedback"),
			$classname, "");
		}
		
		if ($_GET["q_id"])
		{
			$ilTabs->addTarget("solution_hint",
				$this->ctrl->getLinkTargetByClass($classname, "suggestedsolution"),
				array("suggestedsolution", "saveSuggestedSolution", "outSolutionExplorer", "cancel", 
				"addSuggestedSolution","cancelExplorer", "linkChilds", "removeSuggestedSolution"
				),
				$classname, 
				""
			);
		}
		
		// Assessment of questions sub menu entry
		if ($_GET["q_id"])
		{
			$ilTabs->addTarget("statistics",
			$this->ctrl->getLinkTargetByClass($classname, "assessment"),
			array("assessment"),
			$classname, "");
		}

		if (($_GET["calling_test"] > 0) || ($_GET["test_ref_id"] > 0))
		{
			$ref_id = $_GET["calling_test"];
			if (strlen($ref_id) == 0) $ref_id = $_GET["test_ref_id"];
			$ilTabs->setBackTarget($this->lng->txt("backtocallingtest"), "ilias.php?baseClass=ilObjTestGUI&cmd=questions&ref_id=$ref_id");
		}
		else
		{
			$ilTabs->setBackTarget($this->lng->txt("qpl"), $this->ctrl->getLinkTargetByClass("ilobjquestionpoolgui", "questions"));
		}
	}

	function draw() {
		$this->writePostData();
		$this->editQuestion();
	}
}
?>
