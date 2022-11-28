<template>
	<view class="container">
		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 余额部件 -->
		<view class="index-balance-widget">
			<view class="balance-type">余额（CNY）</view>
			<view class="balance-price">{{wallet.money}}</view>
			<view class="balance-total">
				<view class="balance-total__item">
					<view class="text">累计充值(元)</view>
					<view class="price">{{wallet.total_recharge_amount}}</view>
				</view>
				<view class="balance-total__item">
					<view class="text">累计消费(元）</view>
					<view class="price">{{wallet.total_order_amount}}</view>
				</view>
			</view>
		</view>
		
		<!-- 助手部件 -->
		<view class="index-helper-widget">
			<view class="helper-item" @click="$toPage('/bundle/pages/recharge_index/recharge_index?login=true')">
				<image class="icon" src="/bundle/static/wallet/recharge.png"></image>
				<view class="text">充值余额</view>
			</view>
			<view class="helper-item" @click="$toPage('/bundle/pages/recharge_record/recharge_record?login=true')">
				<image class="icon" src="/bundle/static/wallet/recharge_log.png"></image>
				<view class="text">充值记录</view>
			</view>
			<view class="helper-item" @click="$toPage('/bundle/pages/user_integral/user_integral?login=true')">
				<image class="icon" src="/bundle/static/wallet/integral_log.png"></image>
				<view class="text">积分明细</view>
			</view>
			<view class="helper-item" @click="$toPage('/bundle/pages/user_account/user_account?login=true')">
				<image class="icon" src="/bundle/static/wallet/account_log.png"></image>
				<view class="text">账户明细</view>
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
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 钱包数据
				wallet: {}
			}
		},
		onShow() {
			this.getUserWallet()
		},
		methods: {
			/**
			 * 获取用户钱包
			 */
			getUserWallet() {
				this.$u.api.apiUserWallet().then(result => {
					if (result.code === 0) {
						this.wallet = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading wallet error')
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	// 余额部件
	.index-balance-widget {
		margin: 20rpx;
		height: 320rpx;
		padding: 30rpx;
		border-radius: 20rpx;
		box-shadow: 0 0 5rpx #EEEEEE;
		background: linear-gradient(180deg, #FF2C3C 0%, #FF316A 100%);
		.balance-type {
			font-size: 24rpx;
			color: #FFE4E4;
		}
		.balance-price {
			color: #FFFFFF;
			font-size: 76rpx;
			padding-top: 20rpx;
		}
		.balance-total {
			padding-top: 26rpx;
			display: flex;
			justify-content: space-between;
			.balance-total__item {
				width: 50%;
				text-align: left;
				font-size: 24rpx;
				color: #FFE4E4;
				.price {
					margin-top: 6rpx;
					font-size: 38rpx;
					color: #FFFFFF;
				}
			}
		}
	}

	// 助手部件
	.index-helper-widget {
		display: flex;
		flex-wrap: wrap;
		margin: 20rpx;
		padding: 20rpx;
		border-radius: 20rpx;
		box-shadow: 0 0 5rpx #EEEEEE;
		background-color: #FFFFFF;
		.helper-item {
			display: flex;
			align-items: center;
			flex-direction: column;
			width: 25%;
			padding: 20rpx;
			margin-bottom: 18rpx;
			box-sizing: border-box;
			.icon {
				width: 60rpx;
				height: 60rpx;
			}
			.text {
				font-size: 24rpx;
				color: #666666;
				margin-top: 10rpx;
			}
		}
	}
</style>
