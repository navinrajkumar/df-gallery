<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
/*
| -------------------------------------------------------------------
|  Filters configuration
| -------------------------------------------------------------------
|
| Note: The filters will be applied in the order that they are defined
|
| Example configuration:
|
| $filter['auth'] = array('exclude', array('login/*', 'about/*'));
| $filter['cache'] = array('include', array('login/index', 'about/*', 'register/form,rules,privacy'));
|
*//*
$filter ['bootstrap'] = array (
		'exclude', 
		array ( 
		), 
		array ( 
		) 
);
*/
$filter ['init'] = array (
		'exclude', 
		array ( 
		), 
		array ( 
		) 
);
$filter ['install'] = array (
		'exclude', 
		array (
				'admin/setup/*', 
				'admin/login/*', 
				'/api/*' 
		), 
		array ( 
		) 
);
$filter ['auth'] = array (
		'exclude', 
		array (
				'admin/setup/*', 
				'admin/login/*',
				'admin/resetpassword/*', 
				'/api/*' 
		), 
		array ( 
		) 
);
$filter ['upgrade'] = array (
		'exclude', 
		array (
				'admin/setup/*', 
				'admin/login/*', 
				'admin/resetpassword', 
				'admin/upgrade/*', 
				'/api/*' 
		), 
		array ( 
		) 
);
?>