<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de-DE">
<head>
	<title><?=$globvar['title']?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="inc/style.css" type="text/css" media="screen" />
</head>
<body>
<div id="container">
	<div><h1><a href="./"><?=$globvar['title']?></a> <small><?=$globvar['2ndtitle']?></small></h1></div>
	<div id="navi">
		<ul>
			<li><span><a href="./">Start</a></span></li>
			<?php if($globvar['allowreg'] == true && islogged() == false) { ?>
			<li><span><a href="./?reg">Reg</a></span></li>
			<?php } ?>
			<?php if($globvar['allowlogin'] == true && islogged() == false) { ?>
			<li><span><a href="./?login">Login</a></span></li>
			<?php } ?>
			<?php if(islogged()) { ?>
			<li><span><a href="./?logout">Logout</a></span></li>
			<li><span><a href="./?profile"><?php echo $_COOKIE['img_username']; ?></a></span></li>
			<?php } ?>
		</ul>
	</div>
