<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 收货地址部件 -->
		<view class="index-collect-widget">
			<view class="collect-header">
				<view class="method-item u-radius-tl-12" :class="deliveryType === 1 ? 'on' : ''" @click="onSwitchDeliveryType(1)">快递配送</view>
				<view class="method-item u-radius-tr-12" :class="deliveryType === 2 ? 'on' : ''" @click="onSwitchDeliveryType(2)">门店自提</view>
			</view>
			<view class="u-padding-26" v-if="deliveryType === 1" @click="onChoiceAddress">
				<block v-if="addressId">
					<view class="u-font-36 u-font-weight u-margin-bottom-20">{{addressData.region}} {{addressData.address}}</view>
					<view class="u-font-24 u-color-muted">
						<text class="u-margin-right-14">{{addressData.nickname}}</text>
						<text class="u-margin-right-14">{{addressData.mobile}}</text>
					</view>
				</block>
				<block v-else>
					<view class="u-flex u-row-between u-padding-tb-20 u-font-28" >
						<view class="u-flex">
							<u-icon name="map" size="48" color="#333"></u-icon>
							<view class="u-margin-left-20">请选择收货地址</view>
						</view>
						<u-icon name="arrow-right" size="28" color="#333"></u-icon>
					</view>
				</block>
			</view>
			<view class="u-padding-top-26 u-padding-lr-26 u-font-26" v-if="deliveryType === 2" @click="onChoiceShop">
				<block v-if="storeId">
					<view class="u-font-36 u-font-weight u-margin-bottom-10">{{storeData.name}}</view>
					<view class="u-font-28 u-color-muted u-margin-bottom-20">{{storeData.region}}{{storeData.address}}</view>
					<view class="u-font-28 u-color-main u-padding-bottom-30 u-border-bottom">
						<u-icon name="map" color="#FF5058" size="30"></u-icon>
						<text class="u-margin-left-10">距离您 {{storeData.distance}}</text>                                
						<text class="line"></text>
						<text style="color:#333333;">查看地图</text>
					</view>                                                                         
					<view class="form">                                                
						<u-form-item label="提货人" label-width="110" :border-bottom="false">        
							<u-input v-model="contact" placeholder="请输入姓名" />
						</u-form-item>
						<u-form-item label="手机号" label-width="110" :border-bottom="false">
							<u-input v-model="mobile" placeholder="请输入手机号" />
						</u-form-item>
					</view>
				</block>
				<block v-else>
					<view class="u-flex u-row-between u-padding-top-20 u-padding-bottom-46 u-font-28" >
						<view class="u-flex">
							<u-icon name="map" size="48" color="#333"></u-icon>
							<view class="u-margin-left-20">请选择自提门店</view>
						</view>
						<u-icon name="arrow-right" color="#333" size="28"></u-icon>
					</view>
				</block>
			</view>
		</view>
		
		<!-- 产品部件 -->
		<view class="index-product-widget">
			<view class="product-item" v-for="(item, index) in orderData.pStatusArray" :key="index">
				<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="item.image" style="flex-shrink: 0;"></u-image>
				<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-24" >
					<view class="u-line-2 u-font-28">
						<text class="people">{{item.people_num}}人团</text>
						<text>{{item.name}}</text>
					</view>
					<view class="u-font-24 u-color-muted">{{item.spec_value_str}}</view>
					<view class="u-flex u-row-between" style="width: 100%;">
						<wait-price :amount="item.sellPrice" fontColor="#FF2C3C" fontWeight="bold"></wait-price>
						<view><text class="u-font-22">x</text>{{item.count}}</view>
					</view>
				</view>
			</view>
		</view>
		
		<!-- 备注部件 -->
		<view class="index-remarks-widget">
			<view class="u-font-weight">订单备注</view>
			<view class="u-flex-1 u-margin-left-30">
				<u-input input-align="right" v-model="buyerRemarks" placeholder="请输入留言内容"  />
			</view>
		</view>
		
		<!-- 结算部件 -->
		<view class="index-settlement-widget">
			<view class="settlement-item">
				<view class="u-font-weight">商品金额</view>
				<view class="settlement-item__right" style="text-align:right;">
					<view class="product-total-price">
						<text class="u-margin-right-10 u-font-24 u-line-through u-color-muted" v-if="orderData.totalAmount > orderData.orderAmount">￥{{orderData.totalAmount}}</text>
						<wait-price :amount="orderData.orderAmount" fontWeight="bold" fontColor="#000" mainSize="32rpx"></wait-price>
					</view>
					<view class="u-font-22 u-color-muted u-margin-top-10">
						<text v-if="isUseIntegral">已使用积分抵扣 ￥{{orderData.integralAmount}}</text>
					</view>
				</view>
			</view>
			<view class="settlement-item">
				<view>配送费</view>
				<view class="u-font-28 u-font-weight">+￥{{orderData.freightAmount}}</view>
			</view>
			<view class="settlement-item" v-if="orderData.isCoupon">
				<view>优惠券</view>
				<view @click="onShowCoupon">
					<text class="u-font-26 u-font-weight u-color-lighter u-margin-right-10">
						<text v-if="usableCoupon.length <= 0">暂无优惠券可用</text>
						<text class="u-color-major" v-else>
							<text v-if="couponListId == 0">{{usableCoupon}}张可用</text>
							<text v-else>-￥{{orderData.discountAmount}}</text>
						</text>
					</text>
					<u-icon name="arrow-right" size="24" color="#666666"></u-icon>
				</view>
			</view>
			<view class="settlement-item" v-if="orderData.length > 0 && orderData.integralArray.isUsable">
				<view>
					<text>积分抵扣</text>
					<text class="u-font-28 u-color-muted u-margin-left-20">
						可用{{orderData.integralArray.useIntegral}}积分抵用{{orderData.integralArray.discountAmount}}元
					</text>
				</view>
				<checkbox @click="onUseIntegral"></checkbox>
			</view>
		</view>
		
		<!-- 支付方式部件 -->
		<view class="index-payment-widget">
			<view class="payment-header">支付方式</view>
			<view class="payment-item" v-for="(item, index) in payWayList" :key="index" @click="onSwitchPayWay(item.id)">
				<view class="u-flex">
					<u-image width="48rpx" height="48rpx" :src="item.icon"></u-image>
					<view class="u-margin-left-20">
						<view class="u-font-28 u-color-black">{{item.name}}</view>
						<view class="u-font-22 u-color-muted u-margin-top-10">{{item.tips}}</view>
					</view>
				</view>
				<u-icon name="checkbox-mark" size="32" color="#FF5058" v-if="payWayType === item.id"></u-icon>
			</view>
		</view>
		
		<!-- 订单提交部件 -->
		<view style="height: 120rpx;"></view>
		<view class="index-post-widget">
			<view>
				<text class="u-font-30 u-color-normal">合计应付款：</text>
				<text class="u-font-38 u-color-major">
					<text class="u-font-24">￥</text>
					<text>{{orderData.orderAmount}}</text>
				</text>
			</view>
			<view class="post-order-btn" @click="onSubmitOrder">提交订单</view>
		</view>
		
		<!-- 优惠券组件 -->
		<wait-popup-coupon :show="showCoupon" 
			:couponListId="couponListId"
			:usable="usableCoupon"
			:unusable="unusableCoupon"
			@close="showCoupon = false" 
			@change="onConfirmCoupon">
		</wait-popup-coupon>

	</view>
</template>

<script>
	// +----------------------------------------------------------------------
	// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
	// +----------------------------------------------------------------------
	// | 欢迎阅读学习程序代码
	// | gitee:   https://gitee.com/wafts/WaitShop
	// | github:  https://github.com/miniWorlds/waitshop
	// | 官方网站: https://www.waitshop.cn
	// +----------------------------------------------------------------------
	// | 禁止对本系统程序代码以任何目的、任何形式再次发布或出售
	// | WaitShop并不是自由软件,未经许可不能去掉WaitShop相关版权
	// | WaitShop系统产品必须购买商业授权后,方可去除前后端官方标识
	// | WaitShop团队版权所有并拥有最终解释权
	// +----------------------------------------------------------------------
	// | Author: WaitShop Team <2474369941@qq.com>
	// +----------------------------------------------------------------------
	import {wxpay} from '@/utils/payment'
	import {isClient, getLocation} from '@/utils/tools'
	export default {
		data() {
			return {
				isFirstLoading: true, // 首次加载
				
				teamId: 0,			//拼团活动ID
				foundId: 0,			//参团活动ID
				
				from: 'order',		//订单来源
				deliveryType: 1,    //配送类型[1=快递配送, 2=门店自提]
				
				addressId: 0,		//收货地址ID
				addressData: {},    //收货地址数据
				
				storeId: 0,			//门店ID
				storeData: {},		//自提门店数据
				contact: '',		//自提联系人名
				mobile: '',			//自提人联系电话
				
				payWayType: 2,		//支付方式[1=余额支付, 2=微信支付]
				payWayList: [],     //支付方式列表
				
				products: [],	    //要下单的产品
				orderData: [],		//订单数据信息
				buyerRemarks: '',   //买家备注
				
				couponListId: -1,	//优惠券ID[0=不使用,-1=最优]
				isUseIntegral: 0, 	//是否允许积分抵扣[0=否, 1=是]
				showCoupon: false,  //显示优惠券
				usableCoupon: [],   //可用优惠券
				unusableCoupon: [], //不可用优惠券
			}
		},
		onLoad(options) {
			const data = JSON.parse(decodeURIComponent(options.data))
			this.from = data.from || 'order'
			this.products = data.products
			this.teamId = parseInt(options.team_id)
			this.getOrderInfo()
			this.getPayment()
			
			uni.$on("selectAddress", (e) => {
				this.addressId = e.id;
				this.getOrderInfo()
			})
			
			uni.$on("selectStore", (e) => {
				this.storeId = e.id;
				this.getOrderInfo()
			})
		},
		onUnload() {
			uni.$off("selectAddress")
			uni.$off("selectStore")
		},
		methods: {
			/**
			 * 获取订单信息
			 */
			getOrderInfo() {
				const param = {
					team_id: this.teamId,
					address_id: this.addressId,
					store_id: this.storeId,
					coupon_list_id: this.couponListId,
					is_use_integral: this.isUseIntegral,
					products: this.products,
				}
				this.$u.api.apiTeamSettle(param).then(result => {
					if (result.code !== 0) {
						return this.$showToast('loading settle error')
					}
					// 收货地址信息
					if (result.data.address) {
						this.addressData = result.data.address
						this.addressId = result.data.address.id
					}
					// 自提门店数据
					if (result.data.store) {
						this.storeData = result.data.store
						this.storeId = result.data.store.id
					}
					// 优惠券信息
					this.couponListId = result.data.couponListId
					this.usableCoupon = result.data.coupon.usable || []
					this.unusableCoupon = result.data.coupon.unusable || []
					// 积分抵扣装填
					this.isUseIntegral = result.data.isUseIntegral || 0
					// 订单基础信息
					this.orderData = {
						"pass": result.data.pass,
						"status": result.data.status,
						"discountAmount": result.data.discountAmount,
						"freightAmount": result.data.freightAmount,
						"integralAmount": result.data.integralAmount,
						"orderAmount": result.data.orderAmount,
						"totalAmount": result.data.totalAmount,
						"totalCount": result.data.totalCount,
						"useIntegral": result.data.useIntegral,
						"integralArray": result.data.integralArray,
						"pStatusArray": result.data.pStatusArray
					}
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				})
			},
			
			/**
			 * 获取支付方式
			 */
			getPayment() {
				this.$u.api.apiPaymentWay().then(result => {
					if (result.code === 0) {
						this.payWayList = result.data
					} else {
						this.$showToast('loading payment error')
					}
				})
			},
			
			/**
			 * 选择地址
			 */
			onChoiceAddress() {
				uni.navigateTo({
					url: "/pages/address/list/list?type=1" 
				})
			},
			
			/**
			 * 选择门店
			 */
			onChoiceShop() {	
				uni.navigateTo({
					url: "/bundle/pages/store_list/store_list?type=1" 
				})
			},
			
			/**
			 * 选择配送方式
			 */
			onSwitchDeliveryType(type) {
				this.deliveryType = type
			},
			
			/**
			 * 选择支付方式
			 */
			onSwitchPayWay(type) {
				this.payWayType = type
			},
			
			/**
			 * 是否使用积分抵扣
			 */
			onUseIntegral() {
				this.isUseIntegral = !this.isUseIntegral
				this.getOrderInfo()
			},
			
			/**
			 * 显示优惠券选择
			 */
			onShowCoupon() {
				this.showCoupon = true
			},

			/**
			 * 确认优惠券
			 */
			onConfirmCoupon(e) {
				this.couponListId = e
				this.showCoupon = false
				this.getOrderInfo()
			},
			
			/**
			 * 订单提交
			 */
			onSubmitOrder() {
				// 处理下单商品
				let products = []
				this.orderData.pStatusArray.forEach(function(item) {
					products.push({
						"team_id": item.team_id,
						"goods_id": item.id,
						"sku_id": item.sku_id,
						"num": item.count
					})
				})
				// 处理下单参数
				const orderFrom = {
					client: isClient(),
					products: products,
					pay_way: this.payWayType,
					delivery_type: this.deliveryType,
					address_id: this.addressId,
					store_id: this.storeId,
					contact: this.contact,
					mobile: this.mobile,
					coupon_list_id: this.couponListId,
					is_use_integral: this.isUseIntegral,
					remarks: this.buyerRemarks,
					team_id: this.teamId
				}
				// 调用提交订单
				this.handleOrderSubmit(orderFrom)
			},
			
			/**
			 * 订单提交处理
			 */
			handleOrderSubmit(from) {
				uni.showLoading({title: '发起支付中', mask: true})
				setTimeout(() => {
					this.$u.api.apiTeamBuy(from).then(result => {
						if (result.code === 0) {
							this.orderId = result.data.order_id
							this.handleOrderPayment()
						} else {
							return this.$showError(result.msg)
						}
					}).finally(() => {
						uni.hideLoading()
					})
				}, 1300)
			},
			
			/**
			 * 订单支付处理
			 */
			handleOrderPayment() {
				this.$u.api.apiPaymentPay({
					from: this.from, 
					client: isClient(),
					order_id: this.orderId, 
					pay_way: this.payWayType
				}).then(({ code, msg, data }) => {
					switch(code) {
						case 1:
							this.$showToast(msg)
							break;
						case 2100: //余额支付
							uni.redirectTo({
								url: '/pages/order/pay_results/pay_results?id='+this.orderId
							})
							break;
						case 2200: //微信支付
							wxpay(data).then(res => {
								uni.redirectTo({
									url: '/pages/order/pay_results/pay_results?id='+this.orderId
								})
							})
							break;
						case 2300: //支付宝支付
							alipay(data).then(res => {
								uni.redirectTo({
									url: '/pages/order/pay_results/pay_results?id='+this.orderId
								})
							})
							break;
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	// 收货地址部件样式
	.index-collect-widget {
		margin: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.collect-header {
			display: flex;
			justify-content: space-between;
			height: 88rpx;
			.method-item {
				position: relative;
				display: flex;
				align-items: center;
				justify-content: center;
				width: 50%;
				background-color: #F8FCFD;
			}
			.on {
				font-size: 32rpx;
				font-weight: bold; 
				color: $-color-main; 
				background-color: #FFFFFF;
				&::after {
					content: "";
					position: absolute;
					z-index: 900;
					bottom: 8rpx;
					left: 115rpx;
					width: 140rpx;
					height: 16rpx;
					background-image: linear-gradient(to right,  #fbb6b9, #fdd1d3, #fcd8da, #FFFFFF);
				}
			}
		}
		.line {
			display: inline-block;
			width: 1px;
			height: 28rpx;
			margin: 0 16rpx;
			vertical-align: middle;
			background-color: #EEEEEE;
		}
	}
	
	// 产品部件样式
	.index-product-widget {
		margin: 20rpx;
		padding: 10rpx 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.product-item {
			display: flex;
			padding: 10rpx 0;
		}
		.people {
			display: inline-block;
			font-size: 22rpx;
			padding: 0 4rpx;
			color: #FF2C3C;
			border: 1px solid #FF2C3C;
			border-radius: 6rpx;
			margin-right: 10rpx;
		}
	}
	
	// 备注部件样式
	.index-remarks-widget {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin: 20rpx;
		padding: 10rpx 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
	}
	
	// 结算组件样式
	.index-settlement-widget {
		margin: 20rpx;
		padding: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.settlement-item {
			display: flex;
			justify-content: space-between;
			padding: 20rpx 0;
			.vip-icon {
				width: 26rpx;
				height: 26rpx;
				vertical-align: top;
				margin-top: 4rpx;
				margin-right: 8rpx;
			}
			.settlement-item__right {
				.product-discount-price {
					font-size: 24rpx;
					color: #999999;
					margin-top: 10rpx;
				}
			}
		}
	}
	
	// 支付部件样式
	.index-payment-widget {
		margin: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.payment-header {
			font-size: 28rpx;
			font-weight: bold;
			padding: 20rpx;
			color: #666666;
		}
		.payment-item {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 12px 0;
			margin: 0 20rpx;
			border-bottom: 1px solid rgba($color: #F2F2F2, $alpha: 0.6);
			&:first-child { padding-top: 10rpx; }
			&:last-child { border-bottom: none; }
		}
	}

	// 提交订单部件
	.index-post-widget {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 100;
		display: flex;
		align-items: center;
		justify-content: flex-end;
		height: 100rpx;
		padding: 0 20rpx;
		box-shadow: 0 0 8rpx #EEEEEE;
		background-color: #FFFFFF;
		.total-product-price {
			font-size: 30rpx;
			font-weight: bold;
			color: #333333;
		}
		.post-order-btn {
			margin-left: 30px;
			width: 100px;
			height: 76rpx;
			line-height: 76rpx;
			font-size: 26rpx;
			font-weight: bold;
			color: #FFFFFF;
			text-align: center;
			border-radius: 20px;
			background-image: linear-gradient(to right, #F95F2F, #FF2C3C);
		}
	}
</style>
