<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A 35 table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By 35 there is only one group (the '35' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = '35';
$active_record = TRUE;

$db['35']['hostname'] = 'localhost';
$db['35']['username'] = 'ci';
$db['35']['password'] = 'cicicici';
$db['35']['database'] = 'arhnet';
$db['35']['dbdriver'] = 'mysql';
$db['35']['dbprefix'] = '';
$db['35']['pconnect'] = TRUE;
$db['35']['db_debug'] = TRUE;
$db['35']['cache_on'] = FALSE;
$db['35']['cachedir'] = '';
$db['35']['char_set'] = 'cp1251';
$db['35']['dbcollat'] = 'cp1251_general_ci';
$db['35']['swap_pre'] = '';
$db['35']['autoinit'] = TRUE;
$db['35']['stricton'] = TRUE;

$db['12']['hostname'] = '192.168.1.2';
$db['12']['username'] = 'ci';
$db['12']['password'] = 'cicicici';
$db['12']['database'] = 'file_exchange';
$db['12']['dbdriver'] = 'mysql';
$db['12']['dbprefix'] = '';
$db['12']['pconnect'] = FALSE;
$db['12']['db_debug'] = TRUE;
$db['12']['cache_on'] = FALSE;
$db['12']['cachedir'] = '';
$db['12']['char_set'] = 'cp1251';
$db['12']['dbcollat'] = 'cp1251_general_ci';
$db['12']['swap_pre'] = '';
$db['12']['autoinit'] = TRUE;
$db['12']['stricton'] = FALSE;

$db['16']['hostname'] = '192.168.1.6';
$db['16']['username'] = 'korzhevdp';
$db['16']['password'] = 'rootpassword';
$db['16']['database'] = 'arhnet';
$db['16']['dbdriver'] = 'mysql';
$db['16']['dbprefix'] = '';
$db['16']['pconnect'] = TRUE;
$db['16']['db_debug'] = TRUE;
$db['16']['cache_on'] = FALSE;
$db['16']['cachedir'] = '';
$db['16']['char_set'] = 'cp1251';
$db['16']['dbcollat'] = 'cp1251_general_ci';
$db['16']['swap_pre'] = '';
$db['16']['autoinit'] = TRUE;
$db['16']['stricton'] = TRUE;


/* End of file database.php */
/* Location: ./application/config/database.php */