<?php
namespace Snippets;

class Config {
	//Default MSSQL Connection Info (used only if MSSQL helper instantiation is parameterless)
    const MSSQL_DB_NAME = 'Ebis_Prod';
    const MSSQL_DB_SERVER = 'dyna-sql6';
    const MSSQL_DB_PORT = 1433;
    const MSSQL_DB_USER = 'dyna';
    const MSSQL_DB_PASS = 'dyna';

    //Default MYSQL Connection Info (used only if MYSQL helper instantiation is parameterless)
    const MYSQL_DB_NAME = 'mrcore4';
    const MYSQL_DB_SERVER = 'localhost';
    const MYSQL_DB_PORT = 3306;
    const MYSQL_DB_USER = 'root';
    const MYSQL_DB_PASS = 'a;sldkfjqwer';
}