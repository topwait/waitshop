<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>

		<!-- 钱包余额部件 -->
		<view class="index-wallet-widget">
			<view class="balance">
				<view class="u-font-28 u-margin-bottom-14">账户可用余额</view>
				<view class="u-font-weight">
					<text class="u-font-24">￥</text>
					<text style="font-size: 48rpx;">{{balance || 0}}</text>
				</view>
			</view>
			<view class="record" @click="$toPage('/bundle/pages/recharge_record/recharge_record?login=true')">
				<text>充值记录</text>
				<u-icon name="arrow-right" size="28" color="#FFF"></u-icon>
			</view>
		</view>
		
		<!-- 自定义充值金额部件 -->
		<view class="index-custom-widget u-flex">
			<view class="u-font-30 u-font-weight">自定金额</view>
			<view class="u-flex-1 u-margin-left-20">
				<u-input type="number" placeholder="0.00" :border="true" v-model="rechargeMoney" />
			</view>
		</view>
		
		<!-- 余额充值套餐部件 -->
		<view class="index-package-widget" v-if="package.length > 0">
			<view class="u-font-30 u-font-weight u-margin-bottom-20">充值套餐</view>
			<view class="u-flex u-row-between" style="flex-wrap: wrap;">
				<view class="package-item" v-for="(item, index) in package" :key="index" @tap="onPackageBuy(item.id, item.money)">
					<view class="u-font-40 u-font-weight u-color-major">{{item.money}}元</view>
					<view class="u-font-24 u-color-major u-margin-top-10">充{{item.money}}赠送{{item.give_money}}元</view>
				</view>
			</view>
		</view>
		
		<!-- 提交充值按钮 -->
		<view class="recharge-btn" @tap="onBuy">立即充值</view>
		
		<!-- 支付选择组件 -->
		<wait-popup-payment
			:show="showPayment"
			:orderId="payOrderId"
			:orderType="'recharge'"
			@close="onClosePayment"
			@pay="onPayment"
		></wait-popup-payment>
		
		<u-popup v-model="showResultPop" mode="center" border-radius="14" :closeable="true" @close="onResutlClose" :safe-area-inset-bottom="true">
			<view class="index-recharge-result">
				<view class="icon">
					<u-icon name="checkbox-mark" size="48" color="#fff"></u-icon>
				</view>
				<view class="text">充值成功</view>
				<view class="btn" @click="onResutlClose">好的，谢谢</view>
			</view> 
		</u-popup>

	</view>
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
	import {wxpay} from '@/utils/payment'
	import {isClient} from '@/utils/tools'
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,

				// 剩余余额
				balance: 0,
				// 充值配置
				config: [],
				// 充值套餐
				package: [],

				// 自定义充值金额
				rechargeMoney: 0,

				// 显示支付窗
				showPayment: false,
				// 待支付的订单
				payOrderId: 0,
				
				// 结果弹窗
				showResultPop: false 
			}
		},
		onLoad() {
			this.getRechargeIndex()
		},
		methods: {
			/**
			 * 获取充值信息
			 */
			getRechargeIndex() {
				this.$u.api.apiRechargeIndex().then(result => {
					if (result.code === 0) {
						this.balance = result.data.balance
						this.config = result.data.config
						this.package = result.data.package
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading recharge error')
					}
				})
			},
			
			/**
			 * 套餐充值下单
			 */
			onPackageBuy(id, money) {
				const param = {money: money, client: 6, package_id:id}
				this.$u.api.apiRechargeBuy(param).then(result => {
					if (result.code === 0) {
						this.payOrderId = parseInt(result.data.order_id)
						this.showPayment = true
					} else {
						return this.$showToast("订单创建失败")
					}
				})
			},
			
			/**
			 * 定义充值下单
			 */
			onBuy() {
				if (this.config.min_recharge && this.rechargeMoney < this.config.min_recharge) {
					return this.$showToast("充值金额不能少于"+this.config.min_recharge+"元")
				}
				if (this.config.max_recharge && this.rechargeMoney < this.config.max_recharge) {
					return this.$showToast("充值金额不能大于"+this.config.min_recharge+"元")
				}
				if (this.rechargeMoney <= 0 || this.rechargeMoney < 0.01) {
					return this.$showToast("充值金额不能少于0.01元")
				}
				const param = {money: this.rechargeMoney, client: 6, package_id:0}
				this.$u.api.apiRechargeBuy(param).then(result => {
					if (result.code === 0) {
						this.payOrderId = parseInt(result.data.order_id)
						this.showPayment = true
					} else {
						return this.$showToast("订单创建失败")
					}
				})
			},
			
			/**
			 * 关闭支付弹窗
			 */
			onClosePayment() {
				this.showPayment = false
			},
			
			// 关闭结果弹窗
			onResutlClose() {
				this.showResultPop = false
			},
			
			/**
			 * 去充值支付
			 */
			onPayment(e) {
				const {payWayType, orderId} = e
				const that = this
				this.showPayment = false
				uni.showLoading({title: '处理中'});
				setTimeout(function(){
					uni.hideLoading();
					that.$u.api.apiPaymentPay({
						from: "recharge", 
						client: isClient(),
						pay_way: payWayType,
						order_id: orderId 
					}).then(({ code, msg, data }) => {
						switch(code) {
							case 1:
								that.$showToast(msg)
								break;
							case 2200: //微信支付
								wxpay(data).then(res => {
									if (res.errMsg === 'requestPayment:ok') {
										that.getRechargeIndex()
										that.showResultPop = true
									}
								})
								break;
							case 2300: //支付宝支付
								alipay(data).then(res => {
									if (res.errMsg === 'requestPayment:ok') {
										that.getRechargeIndex()
										that.showResultPop = true
									}
								})
								break;
						}
					})
				}, 1500)
			}
		}
	}
</script>

<style lang="scss">
	// 钱包余额样式
	.index-wallet-widget {
		position: relative;
		margin: 20rpx;
		height: 200rpx;
		color: #FFFFFF;
		border-radius: 20rpx;
		box-shadow: 0 0 5rpx #EEEEEE;
		background: linear-gradient(180deg, #FF2C3C 0%, #FF316A 100%);
		.balance {
			position: absolute;
			left: 40rpx;
			top: 50rpx;
		}
		.record {
			position: absolute;
			right: 40rpx;
			top: 84rpx;
			font-size: 22rpx;
			
		}
	}
	
	// 自定义充值金额样式
	.index-custom-widget {
		margin: 30rpx 20rpx;
		padding: 20rpx;
		border-radius: 20rpx;
		background-color: #FFFFFF;
	}
	
	// 充值套餐样式
	.index-package-widget {
		margin: 20rpx;
		padding: 20rpx;
		border-radius: 20rpx;
		background-color: #FFFFFF;
		.package-item {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			width: 48%;
			height: 150rpx;
			margin: 10rpx 0;
			border-radius: 10rpx;
			background: #FFFFFF;
			border: 1px solid #ff2c3c;
		}
	}
	
	// 充值结果样式
	.index-recharge-result {
		display: flex;
		align-items: center;
		flex-direction: column;
		width: 500rpx;
		padding: 30rpx 0;
		.icon {
			margin-top: 30rpx;
			display: flex;
			align-items: center;
			justify-content: center;
			width: 100rpx;
			height: 100rpx;
			border-radius: 50%;
			background-color: #18B566;
		}
		.text {
			font-size: 32rpx;
			font-weight: bold;
			color: #000;
			margin: 10rpx 0 20px 0;
		}
		.btn {
			color: #fff;
			padding: 14rpx 140rpx;
			border-radius: 50rpx;
			background-color: #FF2C3C;
		}
	}
	
	// 充值按钮样式
	.recharge-btn {
		margin: 40rpx 20rpx;
		color: #FFFFFF;
		font-size: 28rpx;
		text-align: center;
		padding: 24rpx;
		border-radius: 50rpx;
		background-color: #FF2C3C;
	}
</style>
