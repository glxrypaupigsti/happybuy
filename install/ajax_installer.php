<?php

#error_reporting(0);

/*
 * Copyright (C) 2014 koodo@qq.com.
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

switch ($_POST['a']) {
    // 数据库连接检查
    case 'db_valid' : {
            if (mysql_connect($_POST['f-dbaddress'], $_POST['f-dbusername'], $_POST['f-dbpassword'])) {
                echo 1;
            } else {
                echo 0;
            }
        }
        break;
    // 数据库安装
    case 'db_install': {
            $db = mysql_connect($_POST['f-dbaddress'], $_POST['f-dbusername'], $_POST['f-dbpassword']);
            mysql_query("drop database if exists " . $_POST['f-dbname'] . ";");
            $db_found = mysql_query("CREATE DATABASE IF NOT EXISTS " . $_POST['f-dbname'] . " DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;") !== false;
            if ($db_found) {
                // 数据库处理完成，导入数据
                mysql_select_db($_POST['f-dbname']);
                mysql_query("SET NAMES 'utf8mb4';");
                // 使用事务进行导入
                mysql_query("START TRANSACTION;");
                $sqls = loadWshopSql();
                foreach ($sqls as $index => $sql) {
                    $sql = trim($sql);
                    if (!empty($sql)) {
                        if (mysql_query($sql) === false) {
                            // 遇到错误回滚
                            mysql_query("ROLLBACK");
                            mysql_query("END");
                            die(0);
                        }
                    }
                }
                mysql_query("COMMIT;");
                mysql_query("END");
                echo 1;
            } else {
                echo -1;
            }
        }
        break;
    case 'config_install': {

            $configCont = file_get_contents(dirname(__FILE__) . '/../config/config_sample.php');

            $configCont = str_replace('__APPID__', $_POST['f-appid'], $configCont);
            $configCont = str_replace('__APPSECRET__', $_POST['f-appsecret'], $configCont);
            $configCont = str_replace('__TOKEN__', $_POST['f-token'], $configCont);
            $configCont = str_replace('__PARTNER__', $_POST['f-partner'], $configCont);
            $configCont = str_replace('__PARTNERKEY__', $_POST['f-partnerkey'], $configCont);
            $configCont = str_replace('__DBNAME__', $_POST['f-dbname'], $configCont);
            $configCont = str_replace('__DBHOST__', $_POST['f-dbaddress'], $configCont);
            $configCont = str_replace('__DBUSER__', $_POST['f-dbusername'], $configCont);
            $configCont = str_replace('__DBPASS__', $_POST['f-dbpassword'], $configCont);
            $configCont = str_replace('__DOCROOT__', $_POST['f-docroot'], $configCont);
            $configCont = str_replace('__DOMAIN__', urldecode($_POST['f-domain']), $configCont);
            $configCont = str_replace('__SHOPNAME__', urldecode($_POST['f-shopname']), $configCont);
            
            touch(dirname(__FILE__) . '/install.lock');

            file_put_contents('../config/config.php', $configCont);

            // 创建admin账户
            include '../config/sys_config.php';
            $db = mysql_connect($_POST['f-dbaddress'], $_POST['f-dbusername'], $_POST['f-dbpassword']);
            mysql_query("SET NAMES 'utf8mb4';");
            mysql_select_db($_POST['f-dbname']);
            $pwd = hash('sha384', $_POST['f-adminpassword'] . $config->admin_salt . hash('md4', $config->admin_salt2[intval($_POST['f-adminname'])]));
            mysql_query("INSERT INTO `admin` (admin_account,admin_password,admin_permission,admin_auth) VALUES ('" . $_POST['f-adminname'] . "','$pwd',0,'stat,orde,prod,gmes,user,comp,sett');");

            echo 1;
        }
}

// 对navicat导出的sql文件进行数组拆分
function loadWshopSql() {
    $lines = file("iwshop.sql");
    $sqlstr = "";
    foreach ($lines as $line) {
        if ($line != "") {
            if (!($line[0] == "#" || $line[0] . $line[1] == "--")) {
                $sqlstr.=$line;
            }
        }
    }
    $sqls = explode(";", $sqlstr);
    return $sqls;
}
