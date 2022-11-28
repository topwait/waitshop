<template>
	<u-popup v-model="showPop" mode="center" :closeable="true" width="80%" border-radius="14" @close="onClose" :safe-area-inset-bottom="true">
		<view class="index-payment-widget">
			<!-- 弹窗标题 -->
			<view class="u-font-28 u-font-weight u-text-center">支付订单</view>
			<!-- 订单信息 -->
			<view class="u-text-center u-margin-top-20">
				<view class="u-font-24 u-color-lighter" v-if="orderType !== 'recharge'">
					<text class="u-margin-right-10">支付剩余时间 </text>
					<u-count-down 
						:timestamp="orderCloseTime" 
						:show-days="false" font-size="24" color="666666"
						@end="onDownTimeEnd">
					</u-count-down>
				</view>
				<view class="u-font-weight u-padding-top-10">
					<text class="u-font-32">￥</text>
					<text class="u-font-38">{{orderDetail.paid_amount}}</text>
				</view>
			</view>
			<!-- 支付方式 -->
			<view class="payment">
				<view class="payment-item" v-for="(item, index) in payWayList" :key="index" 
					@click="onSwitchPayWay(item.id)">
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
			<!-- 确认按钮 -->
			<view class="u-margin-top-26">
				<u-button type="error" @click="onPayment">确认支付</u-button>
			</view>
		</view>
	</u-popup>
</template>

<script>
	// +----------------------------------------------------------------------
	// | WaitShop开源电商系统 [ 助力中小企业快速落地 ]
	// +----------------------------------------------------------------------
	// | 欢迎阅读学习程序代码
	// | gitee:   https://gitee.com/wafts/WaitShop
	// | github:  https://github.com/topwait/waitshop
	// | 官方网站: https://www.waitshop.cn
	// +----------------------------------------------------------------------
	// | 禁止对本系统程序代码以任何目的、任何形式再次发布或出售
	// | WaitShop并不是自由软件,未经许可不能去掉WaitShop相关版权
	// | WaitShop系统产品必须购买商业授权后,方可去除前后端官方标识
	// | WaitShop团队版权所有并拥有最终解释权
	// +----------------------------------------------------------------------
	// | Author: WaitShop Team <2474369941@qq.com>
	// +----------------------------------------------------------------------
	export default {
		props: {
			// 是否显示弹窗
			show: {
				type: Boolean,
				default: () => { return false }
			},
			// 订单ID
			orderId: {
				type: Number,
				default: () => { return 0 }
			},
			// 订单类型: order=普通订单, recharge=充值订单
			orderType: {
				type: String,
				default: () => { return 'order' }
			}
		},
		data() {
			return {
				// 是否弹窗
				showPop: false,
				// 支付方式[1=余额支付, 2=微信支付]
				payWayType: 2,	
				// 支付方式
				payWayList: [],
				// 订单数据
				orderDetail: {},
				// 订单超时[false=未超时, true=超时]
				isOvertime: false,
				// 订单关闭时间
				orderCloseTime: 0
			};
		},
		watch: {
			show(val) {
				this.showPop = val
				if (val) {
					this.getPayWay()
					this.getOrderDetail()
				}
			}
		},
		onShow() {
			this.getPayWay()
			this.getOrderDetail()
		},
		methods: {
			/**
			 * 获取订单
			 */
			getOrderDetail() {
				const param = {id: this.orderId}
				if (this.orderType === 'recharge') {
					this.$u.api.apiRechargeOrder(param).then(result => {
						this.orderDetail = result.data
						// if (this.orderDetail.extend.order_close_time <= 0) {
						// 	this.isOvertime = true
						// }
					})
				} else {
					this.$u.api.apiOrderDetail(param).then(result => {
						this.orderDetail = result.data
						this.orderCloseTime = this.orderDetail.extend.order_close_time
					})
				}
			},
			
			/**
			 * 获取支付方式
			 */
			getPayWay() {
				this.$u.api.apiPaymentWay().then(result => {
					if (this.orderType === 'recharge') {
						let data = []
						result.data.forEach(item => {
							if (item.alias !== 'wallet') {
								data.push(item)
							}
						})
						this.payWayList = data
					} else {
						this.payWayList = result.data
					}
				})
			},
			
			/**
			 * 选择支付方式
			 */
			onSwitchPayWay(type) {
				this.payWayType = type
			},
			
			/**
			 * 关闭弹窗
			 */
			onClose() {
				this.$emit('close')
			},
			
			/**
			 * 去支付
			 */
			onPayment() {
				if (this.isOvertime) {
					return this.$showToast('订单已超时,请重新下单!')
				}
				this.$emit('pay', {
					payWayType: this.payWayType, 
					orderId: this.orderId,
				})
			},
			
			/**
			 * 关闭超时订单
			 */
			onDownTimeEnd() {
				this.isOvertime = true
			}
		}
	}
</script>

<style lang="scss">
	.index-payment-widget {
		padding: 26rpx 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.payment {
			margin: 20rpx 0;
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
			}
		}
	}
</style>
