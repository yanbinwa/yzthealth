<?php 
$index = array(
		array(
				'name' => '基本设置',
				'id'=>'wdgj',
				'sub' => array(
						array(
								'name'=>'账号信息',
								'file'=>'userCenter/myAccount'
						),
						array(
								'name'=>'修改密码',
								'file'=>'userCenter/updatePwd'
						)
				)
		),
		array(
			'name' => '权限设置',
			'id'=>'qxsz',
			'sub' => array(
				array(
					'name'=>'角色管理',
					'file'=>'auth/role'
				),
				array(
					'name'=>'账号管理',
					'file'=>'auth/user'
				)
			)
		),
		array(
				'name' => '代理商管理',
				'id'=>'distribution',
				'sub' => array(
				        array(
								'name'=>'添加代理商',
								'file'=>'distribution/adduser'
						),
						array(
								'name'=>'代理商列表',
								'file'=>'distribution/list'
						)

				)
		),
		array(
				'name' => '商家管理',
				'id'=>'sjgl',
				'sub' => array(
				        array(
								'name'=>'添加商户',
								'file'=>'sjgl/adduser'
						),
						array(
								'name'=>'商户列表',
								'file'=>'sjgl/user'
						)/*,
						array(
								'name'=>'商户申请',
								'file'=>'sjgl/user-sh'
						)*/
				)
		),
		array(
				'name' => '理疗师管理',
				'id'=>'sjgl',
				'sub' => array(
				        array(
								'name'=>'添加理疗师',
								'file'=>'sjgl/addtherapist'
						),
						array(
								'name'=>'理疗师列表',
								'file'=>'sjgl/therapist'
						)/*,
						array(
								'name'=>'理疗师申请',
								'file'=>'sjgl/therapist-sh'
						)*/
				)
		),
		array(
				'name' => '申请合作管理',
				'id'=>'apply',
				'sub' => array(
						array(
								'name'=>'审核列表',
								'file'=>'apply/list'
						)
				)
		),		
		array(
				'name' => '会员管理',
				'id'=>'member',
				'sub' => array(
				       
				        array(
								'name'=>'会员列表',
								'file'=>'member/list'
						),
						array(
								'name'=>'会员实名认证',
								'file'=>'member/authen'
						),
						array(
								'name'=>'银行卡信息修改',
								'file'=>'member/listbank'
						),
				)
		),
		
		array(
				'name' => '商品管理',
				'id'=>'spgl',
				'sub' => array(
				       /*array(
								'name'=>'商品审核',
								'file'=>'shop2t/sproductsh'
						),*/
				       array(
								'name'=>'理疗审核',
								'file'=>'shop2t/llsproductsh'
						),
				       array(
								'name'=>'添加理疗服务',
								'file'=>'shop2t/llsproductadd'
						)
				)
		),
		array(
				'name' => '订单管理',
				'id'=>'ddgl',
				'sub' => array(
						array(
								'name'=>'理疗订单',
								'file'=>'shop2t/orders'
						)
				)
		),
		
		array(
				'name' => '评论管理',
				'id'=>'ddgl',
				'sub' => array(
						array(
								'name'=>'理疗评论列表',
								'file'=>'shop2t/llpcommadmin'
						)
				)
		),		
		array(
				'name' => '平台设置',
				'id'=>'sjgl',
				'sub' => array(
			        /*array(
							'name'=>'平台分类',
							'file'=>'shop2t/stype'
					),
					array(
							'name'=>'品牌分类',
							'file'=>'shop2t/brand'
					),*/
					array(
							'name'=>'理疗设置',
							'file'=>'web/weixinset'
						),
					array(
						'name'=>'关于我们',
						'file'=>'distribution/aboutus'
					),
					/*array(
						'name'=>'商城设置',
						'file'=>'sjgl/setkeyword'
					),*/
					array(
						'name'=>'优惠券',
						'file'=>'yhj/index'
					),
					array(
						'name'=>'分销佣金率设置',
						'file'=>'web/brokerage'
					),
					array(
						'name'=>'加盟费用设置',
						'file'=>'web/apply_fee'
					),
					array(
						'name'=>'代理商提成设置',
						'file'=>'web/dls_set'
					)
						
				)
		),
		array(
			'name' => '财务中心',
			'id'=>'cwgl',
			'sub' => array(
				array(
						'name'=>'用户提现',
						'file'=>'cwgl/memberWithdrawals'
				),
				array(
						'name'=>'商家提现',
						'file'=>'cwgl/supplierWithdrawals'
				),
				array(
						'name'=>'代理商每月业绩汇总',
						'file'=>'cwgl/dlsmonthcount'
				)
			)
		),
		array(
			'name' => '官网后台',
			'id'=>'web',
			'sub' => array(
				array(
						'name'=>'文章管理',
						'file'=>'web/art'
				),
				
				array(
						'name'=>'商家管理',
						'file'=>'web/shop'
				),
				array(
						'name'=>'商品管理',
						'file'=>'web/good'
				),
				
				array(
						'name'=>'集团概况',
						'file'=>'web/about'
				),
				array(
						'name'=>'企业介绍',
						'file'=>'web/jieshao'
				),
				array(
						'name'=>'企业文化',
						'file'=>'web/wenhua'
				),
				array(
						'name'=>'健康理念',
						'file'=>'web/linian'
				),
				array(
						'name'=>'联系我们',
						'file'=>'web/content'
				),
				
				array(
						'name'=>'系统设置',
						'file'=>'web/set'
				),
			)
		),

	
);