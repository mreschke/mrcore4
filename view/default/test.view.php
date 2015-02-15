<?php

######################### EZ SQL Tests
echo "<hr /><b>EZ SQL</b><hr />";

eval(Page::load_class('lib/ezSQL/shared/ez_sql_core.php', false));
eval(Page::load_class('lib/ezSQL/mysql/ez_sql_mysql.php', false));

$db = new ezSQL_mysql(Config::DB_USER, Config::DB_PASS, Config::DB_NAME, Config::DB_SERVER);

$user = $db->get_row("SELECT * FROM tbl_user WHERE user_id = 2");
#echo $user->user_id;
#$db->debug();
$db->vardump($user);
echo "<hr />";
echo $user->last_name;


$users = $db->get_results("SELECT * FROM tbl_user");
$db->vardump($users);
echo $users[0]->last_name;




echo "<hr /><b>Multi-Query Select<b><hr />";
################################## Staci multi query select test


class Proc {
	public $name;
	public $params;

	function __construct() {
		$this->Params = array();
	}
}

class Param {
	public $name;
	public $default;
	public $is_num;
	public $size;

	function __construct($name, $default, $is_num, $size) {
		$this->name = $name;
		$this->default = $default;
		$this->is_num = $is_num;
		$this->size = $size;
	}
}

$Procs = array();

//Test
$proc = new Proc;
	$proc->name = 'test1';
	$param = new Param;
		$param->name = 'dealerID';
		$param->default = null;
		$param->is_num = true;
		$param->size = null;
		$proc->Params[] = $param;
	$proc->Params[] = new Param('currDate', null, false, null);
	$Procs[] = $proc;

//Test2
$proc = new Proc;
	$proc->name = 'test2';
	$param = new Param;
		$param->name = 'dealerName';
		$param->default = 'bob';
		$param->is_num = false;
		$param->size = 12;
		$proc->Params[] = $param;
	$proc->Params[] = new Param('currDate', null, false, null);
	$Procs[] = $proc;


#var_dump($proc);

echo "Select Procedure";
echo "<select name='selProc' id='selProc'>";
foreach ($Procs as $proc) {
	echo "<option>".$proc->name."</option>";
}
echo "<input type='submit' name='btnLoad' value='Load Procedure' />";



//Event - Load Procedure
if (@$_POST['btnLoad']) {
	echo "<hr />";
	foreach ($Procs as $proc) {
		if ($_POST['selProc'] == $proc->name) {
			echo "<b>Procedure: ".$proc->name."</b><br />";
			if (count($proc->Params) > 0) {
				foreach ($proc->Params as $param) {
					echo $param->name.":";
					echo "<input type='text' name='txt_".$param->name."' maxlength='".$param->size."' value='".$param->default."' />";
					echo "<br />";
				}
			}
			echo "<input type='submit' name='btnRun' value='Run Procedure' />";
		}
	}
}

//Event - Run Procedure
if (@$_POST['btnRun']) {
	echo "<hr />";
	foreach ($Procs as $proc) {
		if ($_POST['selProc'] == $proc->name) {
			echo "<b>Running Procedure: ".$proc->name."</b><br />";
			echo "exec ".$proc->name." ";
			foreach ($proc->Params as $param) {
				echo "@".$param->name." = ".@$_POST['txt_'.$param->name];

			}

		}
	}
}