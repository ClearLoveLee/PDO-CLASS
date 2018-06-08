<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2018/6/8
 * Time: 下午12:05
 */
include "database.php";

$db = new DB('127.0.0.1', 'mysql_basic', 'root', '***');

//插入(返回id)
//$new_id = $db->add('players', [
//    'fname' => 'lee',
//    'lname' => 'ming',
//    'email' => 'qaz@qq.com'
//]);

//删除
//$db->delete('players', [
//    'fname' => ['like', 'we%'],
//    'id' => ['>', 117]
//]);

//更新
//$db->update('players', ['lname' => 'bo', 'fname' => 'wei'], [
//    'email' => ['like', 'asd@%'],
//    'id' => ['>', 100]
//]);

//查询
print_r($db->select('players', ['id','fname', 'lname'], ['id' => ['>', 106]]));