<?php
	if (getenv('ENV') == 'docker_dev' ) {
		class MySQL{
			var $HOST = 'mysql55';
			var $USERNAME = "root";
			var $PASSWORD = "";
			var $DB = "satavenu_kz";
		} 
	} elseif ($_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
		class MySQL{
			var $HOST = 'localhost';
			var $USERNAME = "root";
			var $PASSWORD = "";
			var $DB = "satavenu_kz";
		} 
	} else {
		class MySQL{
			var $HOST="xxxxxxxx";
			var $USERNAME="xxxxxxxxxxx";
			var $PASSWORD="xxxxxxxxxxxxx";
			var $DB="xxxxxxxxxxxxxxx";
		}
	}

	$MySQL=new MySQL();

	error_reporting(E_ALL);
	//error_reporting(null);

	date_default_timezone_set('Asia/Almaty');
