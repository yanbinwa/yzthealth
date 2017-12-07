<?php
	$ddid = Request::get(1);
	$dd = new Model('z02_order');
	$dd->find($ddid);
	
	$result = file_get_contents("http://www.kuaidi100.com/query?type=".$dd->kdtype."&postid=".$dd->kd_no."");
	$result = json_decode($result);
	
