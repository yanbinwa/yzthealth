<?php 
$index = array(
       array(
			'name' => '我的账户',
			'id'=>'distribut',
			'sub' => array(
					array(
							'name'=>'个人信息修改',
							'file'=>'distribution/bupdate'
					)
			)
		),
       
	    array(
				'name' => '商家管理',
				'id'=>'sjgl',
				'sub' => array(
				        array(
								'name'=>'添加商户',
								'file'=>'distribution/mysjadduser'
						),
						array(
								'name'=>'商户列表',
								'file'=>'distribution/mysjuser'
						)
				)
		),
		array(
				'name' => '理疗师管理',
				'id'=>'sjgl',
				'sub' => array(
				        array(
								'name'=>'添加理疗师',
								'file'=>'distribution/addtherapist'
						),
						array(
								'name'=>'理疗师列表',
								'file'=>'distribution/therapist'
						)
				)
		),
		array(
			'name' => '业绩汇总',
			'id'=>'myyj',
			'sub' => array(
			   	array(
					'name'=>'业绩汇总',
					'file'=>'distribution/monthcount'
				),
			)
		)
		
		
		
);