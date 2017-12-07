<?php 
$index = array(
       array(
				'name' => '我的账户',
				'id'=>'wdzh',
				'sub' => array(
						array(
								'name'=>'修改密码',
								'file'=>'business/updatePwd'
						)
				)
		),
		array(
				'name' => '理疗服务管理',
				'id'=>'lgc',
				'sub' => array(
						array(
								'name'=>'店铺设置',
								'file'=>'shop2t/myshop'
						),
						/*array(
								'name'=>'标签设置',
								'file'=>'shop2t/stag'
						),*/
						/*array(
								'name'=>'商品类别',
								'file'=>'shop2t/stype'
						),*/
						/*array(
								'name'=>'商品规格',
								'file'=>'shop2t/sprop'
						),
						array(
								'name'=>'商品分组',
								'file'=>'shop2t/sgroup'
						),
						array(
								'name'=>'商品管理',
								'file'=>'shop2t/sproduct'
						),*/
						array(
								'name'=>'理疗管理',
								'file'=>'shop2t/llproduct'
						)

			    )
		),
		array(
			'name' => '订单管理',
			'id'=>'orders',
			'sub' => array(
			   	array(
					'name'=>'订单管理',
					'file'=>'bussinessOrders/orders'
				),
			)
		),
		array(
				'name' => '财务中心',
				'id'=>'cwcenter',
				'sub' => array(
				   	array(
								'name'=>'我的提现明细',
								'file'=>'bussinessOrders/mytxlist'
					),
					array(
								'name'=>'绑定提现信息',
								'file'=>'bussinessOrders/setbank'
					),
					array(
								'name'=>'商户提现',
								'file'=>'bussinessOrders/withdrawals'
					)
				)
		)
);