<?php
// 上传者（guest）账号列表；口令存储与管理员一致，均为加盐 bcrypt(bcrypt(SHA256(明文)))。
// 出厂不含任何可用示例账号（旧示例为遗留 SHA256，已无法登录），请在后台「上传者管理」添加。
$guestConfig = Array();
