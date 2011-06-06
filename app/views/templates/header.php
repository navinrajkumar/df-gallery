<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo (isset($title)) ? $title : 'DfGallery 2.0'; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link   type="text/css" href="<?php echo base_url() ?>/app/views/templates/ui/index.css" rel="stylesheet"  media="screen"/>
<script type="text/javascript" src="<?php echo base_url() ?>/app/views/templates/ui/scripts/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>/app/views/templates/ui/scripts/script.js"></script>

</head>

<body id="body">


<div class="wrapper">
    <div class="main">
	    <div class="header">
	            <a href="<?php echo base_url().'admin/' ?>"><span class="logo"></span></a>
	            <div class="title">DfGallery <?php echo $version ?></div>
		</div>
        <div class="content_wrapper">