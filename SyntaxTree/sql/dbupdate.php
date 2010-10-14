<#1>
<?
if($ilDB->tableExists('il_qpl_qst_syntaxtree_question'))
        $ilDB->dropTable('il_qpl_qst_syntaxtree_question');

if(!$ilDB->tableExists('il_qpl_qst_syntaxtree_question'))
{
        $fields = array (
                'question_fi' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                ),
                'textgap_rating'   => array
                (
                        'type' => 'text',
                        'notnull' => false,
                        'length' => 2,
                        'default' => null
                ),
                'correctanswers' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                )
        );
        $ilDB->createTable('il_qpl_qst_syntaxtree_question', $fields);
        $ilDB->addIndex('il_qpl_qst_syntaxtree_question', array('question_fi'), 'i1');
}

if($ilDB->tableExists('il_qpl_qst_syntaxtree_answer'))
        $ilDB->dropTable('il_qpl_qst_syntaxtree_answer');

if(!$ilDB->tableExists('il_qpl_qst_syntaxtree_answer'))
{
        $fields = array (
                'answer_id' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                ),
                'question_fi' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                ),
                'answertext'   => array
                (
                        'type' => 'text',
                        'notnull' => false,
                        'length' => 4000,
                        'default' => null
                ),
                'points' => array
                (
                        'type' => 'float',
                        'notnull' => true,
                        'default' => 0
                ),
                'aorder' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                ),
                'lastchange' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0 
                ), 
        );
        $ilDB->createTable('il_qpl_qst_syntaxtree_answer', $fields);
        $ilDB->addPrimaryKey('il_qpl_qst_syntaxtree_answer',array('answer_id'));
        $ilDB->addIndex('il_qpl_qst_syntaxtree_answer', array('question_fi'), 'i1');
        $ilDB->createSequence('il_qpl_qst_syntaxtree_answer');
}
?>
<#2>
<?php
$res = $ilDB->queryF("SELECT * FROM qpl_qst_type WHERE type_tag = %s",
        array('text'),
        array('SyntaxTree')
);
if ($res->numRows() == 0)
{
        $res = $ilDB->query("SELECT MAX(question_type_id) maxid FROM qpl_qst_type");
        $data = $ilDB->fetchAssoc($res);
        $max = $data["maxid"] + 1;

        $affectedRows = $ilDB->manipulateF("INSERT INTO qpl_qst_type (question_type_id, type_tag, plugin) VALUES (%s, %s, %s)", 
                array("integer", "text", "integer"),
                array($max, 'SyntaxTree', 1)
        );
}
?>
<#3>
<?php
if($ilDB->tableExists('il_qpl_qst_syntaxtree_feedback'))
        $ilDB->dropTable('il_qpl_qst_syntaxtree_feedback');

if(!$ilDB->tableExists('il_qpl_qst_syntaxtree_feedback'))
{
        $fields = array (
                'feedback_id' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                ),
                'question_fi' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                ),
                'answer' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                ),
                'feedback'   => array
                (
                        'type' => 'text',
                        'notnull' => false,
                        'length' => 4000,
                        'default' => null
                ),
                'tstamp' => array
                (
                        'type' => 'integer',
                        'length'  => 4,
                        'notnull' => true,
                        'default' => 0
                ),
        );
        $ilDB->createTable('il_qpl_qst_syntaxtree_feedback', $fields);
        $ilDB->addPrimaryKey('il_qpl_qst_syntaxtree_feedback',array('feedback_id'));
        $ilDB->addIndex('il_qpl_qst_syntaxtree_feedback', array('question_fi'), 'i1');
        $ilDB->createSequence('il_qpl_qst_syntaxtree_feedback');
}
?>