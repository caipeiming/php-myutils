<?php
header("Content-type: text/html; charset=utf-8");

include 'IDValidator.php';
$v = com\jdk5\blog\IDValidator\IDValidator::getInstance();

//生成一个18位身份证号
$id = $v->makeID();
//获取身份证信息
$info = $v->getInfo($id);
var_dump($info);
//生成一个15位身份证号
$id = $v->makeID(true);
$info = $v->getInfo($id);
var_dump($info);

//验证身份证号是否正确
var_dump($v->isValid("123456789012345678"));