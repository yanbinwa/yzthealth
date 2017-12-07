<?php
/*
 Uploadify
 Copyright (c) 2012 Reactive Apps, Ronnie Garcia
 Released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
 */

// Define a destination
$targetFolder = '/uploads'; // Relative to the root


if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	$exts = $fileParts['extension'];
	//$subpath = '/upload/'.date('Y/m/d').'/'.md5(time().$_FILES['Filedata']['name']).'.'.$exts;

	$subpath='/ups/'.date('Y/m',time()).'/'.md5(time().$_FILES['Filedata']['name']).'.'.$exts;


	$targetFile = YYUC_FRAME_PATH.YYUC_PUB.$subpath;
	File::creat_dir_with_filepath($targetFile);
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png','mp4','mp3'); // File extensions

	if (in_array($exts,$fileTypes)) {
		//move_uploaded_file($tempFile,$targetFile);
		//HttpClient::quickUpload('http://s.weixinguanjia.cn/upupup.html', array('f'=>$tempFile), array('subp'=>$subpath,'up'=>'pu'));
        //Response::write('http://s.weixinguanjia.cn'.$subpath);
		
		$aloss = new ALIOSS();
		
		$basname = md5(time().$_FILES['Filedata']['name']).'.'.$exts;
		$aloss->upload_file_by_file('ups/wtp/'.Session::get('wid').'/'.$basname, $tempFile);
		$rurl = Conf::$alioss_url.'ups/wtp/'.Session::get('wid').'/'.$basname;
        Response::write($rurl);

		
	} else {
		Response::write(0);
	}
}
?>