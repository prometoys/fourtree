<#1>
<?php
if($ilDB->tableExists('il_qpl_qst_st_quest'))
        $ilDB->dropTable('il_qpl_qst_st_quest');

if(!$ilDB->tableExists('il_qpl_qst_st_quest'))
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
        $ilDB->createTable('il_qpl_qst_st_quest', $fields);
        $ilDB->addIndex('il_qpl_qst_st_quest', array('question_fi'), 'i1');
}

if($ilDB->tableExists('il_qpl_qst_st_answer'))
        $ilDB->dropTable('il_qpl_qst_st_answer');

if(!$ilDB->tableExists('il_qpl_qst_st_answer'))
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
        $ilDB->createTable('il_qpl_qst_st_answer', $fields);
        $ilDB->addPrimaryKey('il_qpl_qst_st_answer',array('answer_id'));
        $ilDB->addIndex('il_qpl_qst_st_answer', array('question_fi'), 'i1');
        $ilDB->createSequence('il_qpl_qst_st_answer');
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
if($ilDB->tableExists('il_qpl_qst_st_feedb'))
        $ilDB->dropTable('il_qpl_qst_st_feedb');

if(!$ilDB->tableExists('il_qpl_qst_st_feedb'))
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
        $ilDB->createTable('il_qpl_qst_st_feedb', $fields);
        $ilDB->addPrimaryKey('il_qpl_qst_st_feedb',array('feedback_id'));
        $ilDB->addIndex('il_qpl_qst_st_feedb', array('question_fi'), 'i1');
        $ilDB->createSequence('il_qpl_qst_st_feedb');
}
?>
