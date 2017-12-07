<?php
	$set = new Model('web_set');
	$set->find();
	$id = Request::get(1);
	
	$d = new Model('web_danye');
	$d->find(array('id'=>$id));
	// dump($d);
?>