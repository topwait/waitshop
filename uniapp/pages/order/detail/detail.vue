<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 头部部件 -->
		<view class="index-header-widget">
			<view class="pack-order-status">
				<view class="u-font-40 u-font-weight u-color-white u-margin-bottom-10">
					<text v-if="orderDetail.order_status === 0">等待买家付款</text>
					<text v-if="orderDetail.order_status === 1">
						<block v-if="orderDetail.order_type !== 3">
							{{orderDetail.delivery_type===1 ? '待发货' : '待取货'}}
						</block>
						<block v-else>
							<block v-if="orderDetail.team_found_status === 1">正在拼团中</block>
							<block v-if="orderDetail.team_found_status === 2">
								{{orderDetail.delivery_type===1 ? '拼团成功，待发货' : '拼团成功，待取货'}}
							</block>
						</block>
					</text>
					<text v-if="orderDetail.order_status === 2">卖家已发货</text>
					<text v-if="orderDetail.order_status === 3">交易完成</text>
					<text v-if="orderDetail.order_status === 4">交易关闭</text>
				</view>
				<view class="u-font-28 u-color-white">
					<view v-if="orderDetail.order_status === 0 && orderDetail.extend.order_close_type">
						请在 <u-count-down class="u-margin-lr-8"
							bg-color="#FF5058" color="#FFFFFF" separator-color="#FFFFFF"
							:timestamp="orderDetail.extend.order_close_time"
							:show-days="false" font-size="24" @end="downTimeEnd"
						></u-count-down> 内支付，逾期订单讲自动取消
					</view>
					<view v-if="orderDetail.order_status === 1">
						<block v-if="orderDetail.team_found_status === 1">您的商品正在拼团中,继续加油吧！</block>
						<block v-else>
							{{orderDetail.delivery_type===1 ? '您的商品正在打包中，请耐心等待~' : '请在营业时间到指定门店取货'}}
						</block>
					</view>
					<view v-if="orderDetail.order_status === 2">{{orderDetail.extend.order_finish_time}}</view>
					<view v-if="orderDetail.order_status === 3">{{orderDetail.delivery_type===1 ? '商品已签收，期待您的再次购买' : '商品已成功取件，订单完成'}}</view>
					<view v-if="orderDetail.order_status === 4">未能在约定时间内付款，订单已被关闭</view>
				</view>
			</view>
			<view class="pack-collect-address" v-if="orderDetail.delivery_type===1">
				<u-icon name="car" size="48" color="#999"></u-icon>
				<view class="u-flex u-flex-col u-col-top u-margin-left-20">
					<view class="u-font-28 u-font-weight u-margin-bottom-6">{{orderDetail.address_snap.contact}} {{orderDetail.address_snap.mobile}}</view>
					<view class="u-font-24 u-line-2">{{orderDetail.address_snap.region}}{{orderDetail.address_snap.address}}</view>
				</view>
			</view>
			<view class="pack-collect-address" v-if="orderDetail.delivery_type===2">
				<u-icon name="car" size="48" color="#999"></u-icon>
				<view class="u-flex u-flex-col u-col-top u-margin-left-20">
					<view class="u-font-28u-font-weight u-margin-bottom-6">{{orderDetail.address_snap.store_name}}</view>
					<view class="u-font-24 u-line-2 u-margin-bottom-6">{{orderDetail.address_snap.region}}{{orderDetail.address_snap.address}}</view>
					<view class="u-font-22 u-line-2 u-color-muted">营业时间：{{orderDetail.address_snap.business_hours}}</view>
				</view>
			</view>
		</view>
		
		<!-- 占位高度 -->
		<view style="height: 80rpx;"></view>
		
		<!-- 提货码 -->
		<view class="index-take-widget" v-if="orderDetail.delivery_type===2">
			<view class="qr">
				<view class="">请凭二维码取货</view>
				<view class="image">
					<uqrcode ref="uQRCode" :text="orderDetail.pick_up_code" :size="110" />
				</view>
				<view class="code">提货码：{{orderDetail.pick_up_code}}</view>
			</view>
			<view class="u-margin-lr-20">
				<view class="u-padding-tb-24 u-flex u-row-between" style="border-bottom: 1rpx dashed #eee;">
					<text>提货人</text>
					<text>{{orderDetail.address_snap.contact}}</text>
				</view>
				<view class="u-padding-tb-24 u-flex u-row-between">
					<text>联系方式</text>
					<text>{{orderDetail.address_snap.mobile}}<</text>
				</view>
			</view>
		</view>
		
		<!-- 产品部件 -->
		<view class="index-product-widget">
			<view class="product-item" v-for="(item, index) in orderDetail.orderGoods" :key="index" @click="onJump('/pages/goods/detail/detail?goods_id='+item.goods_id)">
				<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="item.image" style="flex-shrink: 0;"></u-image>
				<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-24" >
					<view class="u-line-2 u-font-28">{{item.name}}</view>
					<view class="u-font-24 u-color-muted u-padding-tb-6">{{item.spec_value_str}}</view>
					<view class="u-flex u-row-between" style="width: 100%;">
						<view class="u-font-weight u-color-major"><text class="u-font-22">￥</text>{{item.sell_price}}</view>
						<view><text class="u-font-22">x</text>{{item.count}}</view>
					</view>
					<view class="u-flex u-row-right u-margin-top-14" style="width: 100%;" v-if="orderDetail.pay_status==1">
						<view class="button-item" @click.stop="onRefund(item.id, 0)" v-if="item.refund_status===0 || item.refund_status == 2">申请售后</view>
						<view class="button-item" @click.stop="onRefund(item.id, item.after_sale_id)" v-if="item.refund_status===1">售后中</view>
						<view class="button-item" @click.stop="onRefund(item.id, item.after_sale_id)" v-if="item.refund_status===3">成功退款</view>
					</view>
				</view>
			</view>
		</view>
		
		<!-- 金额部件 -->
		<view class="index-amount-widget">
			<view class="amount-item">
				<text class="dt">商品金额</text>
				<text class="dd">￥{{orderDetail.total_amount}}</text>
			</view>
			<view class="amount-item">
				<text class="dt">运费</text>
				<text class="dd">+￥{{orderDetail.freight_amount}}</text>
			</view>
			<view class="amount-item">
				<text class="dt">优惠券抵扣：</text>
				<text class="dd"><text class="u-color-major">-￥{{orderDetail.discount_amount}}</text></text>
			</view>
			<view class="amount-item">
				<text class="dt"></text>
				<text class="dd">实付金额：<text class="u-color-major">￥{{orderDetail.paid_amount}}</text></text>
			</view>
		</view>
		
		<!-- 留言部件 -->
		<view class="index-remarks-widget">
			<text class="dt">买家留言：</text>
			<text class="dd">{{orderDetail.buyers_remarks || '无'}}</text>
		</view>
		
		<!-- 订单部件 -->
		<view class="index-order-widget" v-if="orderDetail.extend">
			<view class="order-item">
				<text class="dt">订单编号：</text>
				<text class="dd">{{orderDetail.order_sn}}</text>
			</view>
			<view class="order-item">
				<text class="dt">订单状态：</text>
				<text class="dd">{{orderDetail.extend.order_status}}</text>
			</view>
			<view class="order-item">
				<text class="dt">订单类型：</text>
				<text class="dd">{{orderDetail.extend.order_type}}</text>
			</view>
			<view class="order-item">
				<text class="dt">支付方式：</text>
				<text class="dd">{{orderDetail.extend.pay_way}}</text>
			</view>
			<view class="order-item">
				<text class="dt">下单时间：</text>
				<text class="dd">{{orderDetail.create_time}}</text>
			</view>
			<view class="order-item" v-if="orderDetail.pay_time">
				<text class="dt">付款时间：</text>
				<text class="dd">{{orderDetail.pay_time}}</text>
			</view>
			<view class="order-item" v-if="orderDetail.delivery_time">
				<text class="dt">发货时间：</text>
				<text class="dd">{{orderDetail.delivery_time}}</text>
			</view>
			<view class="order-item" v-if="orderDetail.cancel_time">
				<text class="dt">取消时间：</text>
				<text class="dd">{{orderDetail.cancel_time}}</text>
			</view>
			<view class="order-item" v-if="orderDetail.confirm_time">
				<text class="dt">完成时间：</text>
				<text class="dd">{{orderDetail.confirm_time}}</text>
			</view>
		</view>
		
		<!-- 页脚部件 -->
		<view style="height:100rpx;"></view>
		<view class="index-footer-widget">
			<view class="u-font-28 u-font-weight u-margin-right-30">
				<text>实付金额：</text>
				<text style="color:#FF5058;">
					<text class="u-font-24">¥</text>
					<text class="u-font-32">{{orderDetail.paid_amount}}</text>
				</text>
			</view>
			<view class="button-item button-item--normal" @click="onLogistics" 
				v-if="orderDetail.delivery_type===1 && (orderDetail.order_status===2 || orderDetail.order_status===3)">查看物流</view>
			<view class="button-item button-item--normal" @click="onCancel" 
				v-if="orderDetail.order_status===0 || orderDetail.is_allow_cancel">取消订单</view>
			<view class="button-item button-item--pay" @click="onShowPayment" 
				v-if="!orderDetail.pay_status && orderDetail.order_status===0">立即支付</view>
		</view>
		
		<!-- 支付选择组件 -->
		<wait-popup-payment
			:show="showPayment"
			:orderId="orderId"
			@close="onClosePaymentFun"
			@pay="onPaymentFun"
		></wait-popup-payment>

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
	import {toPage} from '@/utils/tools'
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 订单ID
				orderId: 0,
				// 订单详细
				orderDetail: [],
				// 是否显示支付选择
				showPayment: false
			}
		},
		onLoad(options) {
			this.orderId = parseInt(options.id)
			this.getOrderDetail()
		},
		methods: {
			/**
			 * 获取订单详细
			 */
			getOrderDetail() {
				const param = {id: this.orderId}
				this.$u.api.apiOrderDetail(param).then(result => {
					if (result.code === 0) {
					this.orderDetail = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading orderDetail error')
					}
				})
			},
			
			/**
			 * 关闭超时订单
			 */
			downTimeEnd() {
				if (this.orderDetail.order_status !==  0) return true
				const param = {id: this.orderDetail.id}
				this.$u.api.apiOrderEnd(param).then(result => {
					if (result.code === 0) {
						this.getOrderDetail()
					}
				})
			},
			
			/**
			 * 取消订单
			 */
			onCancel() {
				const that = this
				uni.showModal({
				    title: '提示',
				    content: '您确定要取消吗？',
					cancelText: '再想一想',
					confirmText: '我确定',
					confirmColor: '#FF5058',
				    success: function (res) {
						if (!res.confirm) return false
						const param = {id: that.orderDetail.id}
						that.$u.api.apiOrderCancel(param).then(result => {
							if (result.code === 0) {
								that.$showSuccess(result.msg)
								that.getOrderDetail()
							} else {
								that.$showToast(result.msg)
							}
						})
				    }
				})
			},
				
			/**
			 * 查看物流
			 */
			onLogistics() {
				toPage('/bundle/pages/goods_logistics/goods_logistics?login=true&id='+this.orderDetail.id)
			},
			
			/**
			 * 申请退款
			 */
			onRefund(id, afterId) {
				if (afterId === 0) {
					if (!this.orderDetail.extend.is_allow_afterSale) {
						return this.$showToast('订单已超出售后时间')
					}
					toPage('/pages/order/after_sale_apply/after_sale_apply?login=true&id='+id)
				} else {
					toPage('/pages/order/after_sale_detail/after_sale_detail?login=true&id='+afterId)
				}
			},
			
			/**
			 * 弹出支付窗口
			 */
			onShowPayment() {
				this.showPayment = true
			},
			
			/**
			 * 关闭支付弹窗
			 */
			onClosePayment() {
				this.showPayment = false
			},
				
			/**
			 * 去支付
			 */
			onPayment(e) {
				const that = this
				const {payWayType, orderId} = e
				that.showPayment = false
				uni.showLoading({title: '处理中'});
				setTimeout(function(){
					uni.hideLoading();
					that.$u.api.apiPaymentPay({
						from: "order", 
						client: 6,
						pay_way: payWayType,
						order_id: orderId
					}).then(({ code, msg, data }) => {
						switch(code) {
							case 1:
								that.$showToast(msg)
								break;
							case 2100: //余额支付
								uni.navigateTo({
									url: '/pages/order/pay_results/pay_results?id='+that.orderId
								})
								break;
							case 2200: //微信支付
								wxpay(data).then(res => {
									uni.navigateTo({
										url: '/pages/order/pay_results/pay_results?id='+that.orderId
									})
								})
								break;
							case 2300: //支付宝支付
								// alipay(data).then(res => {})
								break;
						}
					})
				}, 2000)
			},
			
			/**
			 * 跳转页面
			 */
			onJump(url) {
				uni.navigateTo({
					url: url
				})
			}
			
		}
		
	}
</script>

<style lang="scss">
	// 头部部件样式
	.index-header-widget {
		position: relative;
		height: 200rpx;
		background-color: $-color-main;
		.pack-order-status {
			position: absolute;
			top: 20rpx;
			left: 30rpx;
		}
		.pack-collect-address {
			display: flex;
			align-items: center;
			position: absolute;
			left: 20rpx;
			right: 20rpx;
			bottom: -80rpx;
			height: 140rpx;
			padding: 0 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
		}
	}
	
	// 自提部件样式
	.index-take-widget {
		margin: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.qr {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 20rpx 0;
			font-size: 24rpx;
			color: #333;
			.code {
				padding: 10rpx 30rpx;
				border-radius: 50rpx;
				background-color: #eee;
			}
			.image {
				padding: 10rpx;
				margin: 10rpx 0;
				border: 1rpx solid #eee;
				border-radius: 12rpx;
			}
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
		.button-item {
			height: 48rpx;
			line-height: 49rpx;
			font-size: 24rpx;
			color: #616161;
			text-align: center;
			margin-right: 16rpx;
			padding: 0 30rpx;
			border-radius: 40rpx;
			border: 1px #c7c7c7 solid;
			&:last-child { margin-right: 0; }
		}
	}
	
	// 金额部件样式
	.index-amount-widget {
		margin: 20rpx;
		padding: 20rpx 30rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.amount-item {
			display: flex;
			justify-content: space-between;
			font-size: 28rpx;
			color: #000000;
			height: 60rpx;
			line-height: 60rpx;				
		}
	}
	
	// 订单部件样式
	.index-order-widget {
		margin: 20rpx;
		padding: 20rpx 30rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.order-item {
			font-size: 28rpx;
			color: #000000;
			height: 60rpx;
			line-height: 60rpx;				
		}
	}
	
	// 留言部件样式
	.index-remarks-widget {
		font-size: 28rpx;
		color: #000000;
		margin: 20rpx;
		padding: 20rpx 30rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
	}

	// 页脚部件样式
	.index-footer-widget {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		display: flex;
		align-items: center;
		justify-content: flex-end;
		height: 100rpx;
		padding: 0 30rpx;
		box-sizing: initial;
		box-shadow: 0 0 8rpx #EEEEEE;
		background-color: #FFFFFF;
		.button-item {
			height: 60rpx;
			line-height: 56rpx;
			font-size: 24rpx;
			color: #616161;
			text-align: center;
			margin-right: 16rpx;
			padding: 0 30rpx;
			border-radius: 40rpx;
			border: 1px #c7c7c7 solid;
			&:last-child { margin-right: 0; }
			&--pay {
				color: #FFFFFF;
				border-color: $-color-main;
				background-color: $-color-main;
			}
		}
	}
</style>
