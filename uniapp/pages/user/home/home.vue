<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 头部组件 -->
		<view class="index-header-widget">
			<view class="user-avatar">
				<image class="avatar" :lazy-load="true" :src="isLogin ? userInfo.avatar : design.avatar"></image>
			</view>
			<view class="user-login" v-if="!isLogin" @click="$toPage('/pages/login/login')">点击登录</view>
			<view class="user-info" v-else>
				<view class="username">{{userInfo.nickname}}</view>
				<view class="level">{{userInfo.gradeName}}</view>
			</view>
			<image class="user-set" src="/static/ic_set.png" @click="$toPage('/pages/user/info/info?login=true')"></image>
		</view>
		
		<view class="index-vip-widget" @click="$toPage('/bundle/pages/user_grade/user_grade')">
			<!-- <view class="u-flex u-row-between" style="width: 100%; height: 78rpx;">
				<view class="u-flex">
					<image src="/static/vip.png" style="width: 40rpx; height: 40rpx; vertical-align: middle;"></image>
					<text class="u-font-26 u-font-weight u-margin-left-10" style="color:#f8d5a8;">开通会员SVIP</text>
				</view>
				<view>
					<text>会员可享受多项权益</text>
					<u-icon name="arrow-right" size="26" color="#999"></u-icon>
				</view>
			</view> -->

			<view class="u-flex u-row-between" style="width:100%; height:78rpx;">
				<view class="u-flex">
					<image src="/static/ic_vip.png" style="width: 40rpx; height: 40rpx; vertical-align: middle;"></image>
					<text class="u-font-26 u-font-weight u-margin-left-10" style="color:#f8d5a8;">{{isLogin ? userInfo.gradeName : '请先登录'}}</text>
				</view>
				<view v-if="isLogin">
					<text v-if="userInfo.lackGrowthValue !== -1">距离升级还差{{userInfo.lackGrowthValue}}</text>
					<text v-else>当前已是最高级会员</text>
					<!-- #ifndef APP-PLUS -->
					<u-icon name="arrow-right" size="26" color="#999999"></u-icon>
					<!-- #endif -->
				</view>
			</view>
		</view>
		
		<!-- 余额组件 -->
		<view class="index-balance-widget">
			<view class="balance-item" @click="$toPage('/bundle/pages/user_wallet/user_wallet?login=true')">
				<view class="text">我的余额</view>
				<view class="number">{{userInfo.money || 0}}</view>
			</view>
			<view class="balance-item" @click="$toPage('/bundle/pages/user_integral/user_integral?login=true')">
				<view class="text">积分</view>
				<view class="number">{{userInfo.integral || 0}}</view>
			</view>
			<view class="balance-item" @click="$toPage('/bundle/pages/coupon_my/coupon_my?login=true')">
				<view class="text">优惠券</view>
				<view class="number">{{userInfo.coupon_num || 0}}</view>
			</view>
		</view>
		
		<!-- 订单组件 -->
		<view class="index-order-widget">
			<view class="order-header">
				<text class="order-header__title">我的订单</text>
				<text class="order-header__all" @click="$toPage('/pages/order/list/list?login=true&tab=0')">查看全部订单</text>
			</view>
			<view class="order-navbar">
				<view class="order-navbar-item" 
					v-for="(item, index) in design.order" :key="index"
					@click="$toPage(item.path)">
					<image class="navbar-item__icons" :src="item.image"></image>
					<text class="navbar-item__text">{{item.name}}</text>
				</view>
			</view>
		</view>
		
		<!-- 工具组件 -->
		<view class="index-tools-widget">
			<view class="tools-header">常用工具</view>
			<view class="tools-apply">
				<button class="tools-apply-item" hover-class="none"
					v-for="(item, index) in design.me" :key="index"
					@click="$toPage(item.link_url, item.link_type)">
					
					<image class="apply-item__icon" :src="item.image"></image>
					<text class="apply-item__text">{{item.name}}</text>
				</button>
			</view>
		</view>

		<view style="height:20rpx;"></view>
		
		<wait-copyright v-if="!isFirstLoading"></wait-copyright>

	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex'	
	import {toLogin, toPage, isWeixin} from '@/utils/tools'
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 是否是微信端
				isWeixin: false,
				// 设计数据
				design: {},
				// 用户信息
				userInfo: {}
			}
		},
		computed: {
			...mapState(['isLogin'])
		},
		onShow() {
			// #ifndef MP-WEIXIN || APP-PLUS
				this.isWeixin = isWeixin()
			//#endif
			this.getUserDesign()
			if (this.isLogin) {
				this.getUserCenter()
			}
		},
		methods: {
			/**
			 * 获取装修设计
			 */
			async getUserDesign() {
				await this.$u.api.apiUserDesign().then(result => {
					if (result.code === 0) {
						this.design = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast(result.msg)
					}
				})
			},
			
			/**
			 * 获取个人中心信息
			 */
			getUserCenter() {
				this.$u.api.apiUserCenter().then(result => {
					this.userInfo = result.data
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				})
			},
			
			/**
			 * 跳转页面
			 */
			onJump(url, type=1) {
				switch (type) {
					case 1: 
						toPage(url)
						break
					case 2:
						if (url.indexOf('http://') === 0 || url.indexOf('https://') === 0) {
							uni.navigateTo({
								url: "/pages/webview/webview?url=" + url
							})
						} else {
							toPage(url)
						}
						break
				}
			}
		}
	}
</script>

<style lang="scss">
	// 头部组件样式
	.index-header-widget {
		position: relative;
		height: 300rpx;
		background-color: #FF5058;
		background-repeat: no-repeat;
		background-position: center right;
		background-size: auto 100%;
		background-image: url('data:image/png;base64,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');
		.user-avatar {
			position: absolute;
			top: 40rpx;
			left: 60rpx;
			width: 120rpx;
			height: 120rpx;
			.avatar {
				width: 100%;
				height: 100%;
				vertical-align: middle;
				border-radius: 50%;
			}
		}
		.user-login {
			position: absolute;
			top: 68rpx;
			left: 200rpx;
			width: 170rpx;
			text-align: center;
			font-size: 28rpx;
			color: #FFFFFF;
			padding: 8rpx;
			border-radius: 50rpx;
			border: 2rpx solid #FFFFFF;
		}
		.user-info {
			position: absolute;
			top: 48rpx;
			left: 200rpx;
			.username {
				color: #FFFFFF;
				font-size: 28rpx;
			}
			.level {
				color: #FFFFFF;
				font-size: 24rpx;
				text-align: center;
				height: 40rpx;
				line-height: 40rpx;
				padding: 0 16rpx;
				margin-top: 10rpx;
				border-radius: 50rpx;
				background-color: #000000;
				.level-icon {
					width: 24rpx;
					height: 24rpx;
					vertical-align: top;
					margin-right: 10rpx;
					margin-top: 10rpx;
				}
			}
		}
		.user-set {
			position: absolute;
			right: 50rpx;
			top: 40rpx;
			width: 50rpx;
			height: 50rpx;
			z-index: 1000;
		}
	}
	
	// 会员组件样式
	.index-vip-widget {
		display: flex;
		justify-content: space-between;
		position: absolute;
		top: 200rpx;
		left: 20rpx;
		right: 20rpx;
		height: 150rpx;
		font-size: 26rpx;
		color: #bbb;
		padding: 0 30rpx 0 30rpx;
		border-radius: 16rpx 16rpx 0 0;
		background-color: #343a48;
	}
	
	// 余额组件样式
	.index-balance-widget {
		position: absolute;
		top: 280rpx;
		left: 24rpx;
		right: 24rpx;
		display: flex;
		justify-content: space-between;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.balance-item {
			width: 33.33%;
			margin: 30rpx 0;
			text-align: center;
			box-sizing: border-box;
			border-right: 1px solid #F2F2F2;
			&:last-child { border-right: none; }
			.text { font-size: 26rpx; color: #999; margin-bottom: 10rpx; }
			.number { color: #000; font-size: 32rpx; }
		}
	}
	
	// 订单组件样式
	.index-order-widget {
		margin: 0 20rpx;
		margin-top: 138rpx;
		margin-bottom: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.order-header { 
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 30rpx 10px;
			border-bottom: 1px solid #f8f8f8;
			.order-header__title { font-size: 28rpx; color: #000; }
			.order-header__all { font-size: 24rpx; color: #8c8c8c; }
		}
		.order-navbar {
			display: flex;
			justify-content: space-between;
			.order-navbar-item {
				display: flex;
				flex-direction: column;
				align-items: center;
				width: 20%;
				padding: 20rpx 0;
				text-align: center;
				.navbar-item__icons { width: 60rpx; height: 60rpx; padding: 10rpx 0; }
				.navbar-item__text { font-size: 24rpx; color: #868686; }
			}
		}
	}

	// 工具组件样式
	.index-tools-widget {
		margin: 0 20rpx;
		margin-top: 24rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.tools-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 30rpx 10px;
			border-bottom: 1px solid #f8f8f8;
			.order-header__title { font-size: 28rpx; color: #000; }
		}
		.tools-apply {
			display: flex;
			align-items: center;
			flex-wrap: wrap;
			padding: 26rpx 0 0;
			.tools-apply-item {
				width: 25%;
				display: flex;
				flex-direction: column;
				justify-content: center;
				text-align: center;
				margin-bottom: 26rpx;
				.apply-item__icon {
					margin: 0 auto;
					width: 60rpx;
					height: 60rpx;
				}
				.apply-item__text {
					font-size: 24rpx;
					margin-top: 4rpx;
					height: 60rpx;
					line-height: 60rpx;
				}
			}
		}
	}
</style>
