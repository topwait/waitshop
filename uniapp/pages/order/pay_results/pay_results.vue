<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 支付结果部件 -->
		<view class="index-result-widget">
			<view class="u-border-bottom u-padding-top-36">
				<view class="icon-box">
					<view class="icon u-flex u-row-center" :class="payResult.order_status !== 1 ? 'icon--fail' : 'icon--success'">
						<u-icon name="close" size="48" color="#FFFFFF" v-if="payResult.order_status !== 1"></u-icon>
						<u-icon name="checkbox-mark" size="48" color="#FFFFFF" v-else></u-icon>
					</view>
				</view>
				<view class="u-padding-tb-30 u-text-center u-font-md u-font-weight" v-if="payResult.order_status !== 1">订单支付失败</view>
				<view class="u-padding-tb-30 u-text-center u-font-md u-font-weight" else>订单已支付成功</view>
			</view>
			<view class="u-padding-tb-20 u-border-bottom">
				<view class="u-flex u-row-between u-padding-tb-10">
					<view>订单编号</view>
					<view class="u-color-muted">{{payResult.order_sn}}</view>
				</view>
				<view class="u-flex u-row-between u-padding-tb-10">
					<view>下单时间</view>
					<view class="u-color-muted">{{payResult.create_time}}</view>
				</view>
				<view class="u-flex u-row-between u-padding-tb-10" v-if="payResult.pay_time">
					<view>支付时间</view>
					<view class="u-color-muted">{{payResult.pay_time}}</view>
				</view>
				<view class="u-flex u-row-between u-padding-tb-10">
					<view>支付方式</view>
					<view class="u-color-muted">{{payResult.extend.pay_way}}</view>
				</view>
				<view class="u-flex u-row-between u-padding-tb-10">
					<view>支付金额</view>
					<view class="u-color-muted">{{payResult.paid_amount}}</view>
				</view>
			</view>
			<view class="u-padding-top-30 u-padding-bottom-10">
				<view>
					<view class="u-margin-bottom-20">
						<u-button type="error" shape="circle"@click="goOrder">查看订单</u-button>
					</view>

					<u-button type="error" shape="circle" plain @click="goHome"
						:custom-style="{backgroundColor: '#FFFFFF !important'}">
						返回首页
					</u-button>
				</view>
			</view>
		</view>

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
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true, 
				// 订单ID
				orderId: 0,
				// 支付结果
				payResult: {}
			}
		},
		onLoad(options) {
			this.orderId = parseInt(options.id)
			this.getOrderResult()
		},
		methods: {
			/**
			 * 获取订单支付结果
			 */
			getOrderResult() {
				const param = {id: this.orderId}
				this.$u.api.apiOrderDetail(param).then(result => {
					if (result.code === 0) {
						this.payResult = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading payResult error')
					}
				})
			},
			
			/**
			 * 到订单页
			 */
			goOrder() {
				uni.redirectTo({
					url: '/pages/order/detail/detail?id='+this.payResult.id
				})
			},
			
			/**
			 * 返回首页
			 */
			goHome() {
				uni.redirectTo({
					url: '/pages/index/index',
				})
			}
		}
	}
</script>

<style lang="scss">
	.index-result-widget {
		position: relative;
		margin: 30rpx;
		margin-top: 100rpx;
		padding: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.icon-box {
			position: absolute;
			top: -74rpx;
			left: 42%;
			justify-content: center;
			width: 120rpx;
			height: 120rpx;
			border-radius: 50%;
			padding: 6rpx;
			background-color: #F2F2F2;
			.icon {
				width: 100%;
				height: 100%;
				border-radius: 50%;
				&--fail { background-color: red; }
				&--success { background-color: #19BE6B; }
			}
		}
	}
</style>
