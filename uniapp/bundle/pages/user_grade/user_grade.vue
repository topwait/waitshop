<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 头部部件 -->
		<view class="index-header-widget">
			<view class="u-bg-white" style="height:300rpx;"></view>
		</view>
		
		<!-- 卡片部件 -->
		<view class="index-swiper-widget">
			<swiper class="swiper" 
				style="height:320rpx" 
				previous-margin="60rpx" 
				next-margin="50rpx" 
				display-multiple-items="1"
				:current="currentIndex" 
				@change="bindChange"
				>
				<swiper-item v-for="(item, index) in gradeList" :key="index">
					<view class="grade-card-item" :style="'background-image: url('+item.background+');'">
						<view class="card-left">
							<view class="u-flex u-margin-bottom-40">
								<view style="width:100rpx; height:100rpx; border-radius:50%;">
									<image class="u-equal-bfb u-radius-circular" :src="item.userInfo.avatar" ></image>
								</view>
								<view class="u-margin-left-16">
									<view class="u-font-32 u-font-weight u-margin-bottom-10">{{item.userInfo.nickname}}</view>
									<view class="u-font-24" style="color:#696969;">
										商城购物可享<text class="u-margin-lr-10 u-font-30" style="font-style:italic;">{{item.equity}}</text>折
									</view>
								</view>
							</view>
							<view class="u-margin-bottom-6">
								<u-icon name="lock-opened-fill" size="32" color="#000"></u-icon>
								<text class="u-margin-left-14 u-font-30 u-font-weight u-color-black" v-if="!item.unlock">暂未解锁该等级</text>
								<text class="u-margin-left-14 u-font-30 u-font-weight u-color-black" v-else>当前等级已解锁</text>
							</view>
							<view style="width:330rpx;">
								<u-line-progress :percent="item.progress" :round="true" :height="10" active-color="#2f3334" inactive-color="#a4a7ac">
									<view slot="default"></view >
								</u-line-progress>
							</view>
							<view class="u-margin-top-16 u-font-26">
								当前 <text class="u-font-34 u-margin-lr-10">{{item.userInfo.growth_value}}</text> 点, 
								需达到 {{item.upgrade.total_growth_value}} 点解锁
							</view>
						</view>
						<view class="reach" v-if="!item.unlock">未解锁</view>
						<view class="reach" v-else>已解锁</view>
					</view>
				</swiper-item>
			</swiper>
		</view>
		
		<!-- 权益部件 -->
		<view class="index-privilege-widget">
			<wait-title skin="speckle" text="等级特权" textColor="c2926a" topPadding="0" bottomPadding="0"></wait-title>
			<view class="main u-flex u-row-between">
				<view class="item u-flex u-flex-col">
					<u-image width="90rpx" height="90rpx" shape="circle" src="/bundle/static/privilege_1.png"></u-image>
					<text class="u-margin-top-14 u-font-26 u-color-lighter">购物折扣</text>
				</view>
				<view class="item u-flex u-flex-col">
					<u-image width="90rpx" height="90rpx" shape="circle" src="/bundle/static/privilege_2.png"></u-image>
					<text class="u-margin-top-14 u-font-26 u-color-lighter">专属徽章</text>
				</view>
				<view class="item u-flex u-flex-col">
					<u-image width="90rpx" height="90rpx" shape="circle" src="/bundle/static/privilege_3.png"></u-image>
					<text class="u-margin-top-14 u-font-26 u-color-lighter">经验积累</text>
				</view>
				<view class="item u-flex u-flex-col">
					<u-image width="90rpx" height="90rpx" shape="circle" src="/bundle/static/privilege_4.png"></u-image>
					<text class="u-margin-top-14 u-font-26 u-color-lighter">尊享客服</text>
				</view>
			</view>
		</view>
		
		<!-- 任务部件 -->
		<view class="index-task-widget">
			<view class="task-item u-border-bottom">
				<view class="u-font-36 u-font-weight">我的任务</view>
			</view>
			<view class="task-item u-border-bottom">
				<view class="u-flex">
					<view class="icon">
						<u-icon name="calendar" size="40" color="#cba485"></u-icon>
					</view>
					<view class="u-margin-left-20">
						<view class="u-font-32 u-font-weight">完成签到</view>
						<view class="u-flex u-font-22 u-color-muted u-margin-top-10">每日签到可获得经验值</view>
					</view>
				</view>
				<view class="button" @click="$toPage('/bundle/pages/user_sign/user_sign')">去签到</view>
			</view>
			<view class="task-item u-border-bottom">
				<view class="u-flex">
					<view class="icon">
						<u-icon name="gift" size="40" color="#cba485"></u-icon>
					</view>
					<view class="u-margin-left-20">
						<view class="u-font-32 u-font-weight">购买商品</view>
						<view class="u-flex u-font-22 u-color-muted u-margin-top-10">购买商品可获得对应经验值</view>
					</view>
				</view>
				<view class="button" @click="$toPage('/pages/goods/list/list')">去购买</view>
			</view>
			<view class="task-item u-border-bottom">
				<view class="u-flex">
					<view class="icon">
						<u-icon name="zhuanfa" size="40" color="#cba485"></u-icon>
					</view>
					<view class="u-margin-left-20">
						<view class="u-font-32 u-font-weight">邀请好友</view>
						<view class="u-flex u-font-22 u-color-muted u-margin-top-10">邀请好友注册商城可获得经验值</view>
					</view>
				</view>
				<view class="button" @click="$toPage('/bundle/pages/distribution_index/distribution_index?login=true')">去邀请</view>
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
				// 当前等级
				currentIndex: 0,
				// 会员等级
				gradeList: []
			}
		},
		onShow() {
			this.getGradeList()
		},
		methods: {
			/**
			 * 绑定切换等级
			 */
			bindChange(e) {
				let {current} = e.detail
				this.currentIndex = current
			},
			
			/**
			 * 获取等级列表
			 */
			getGradeList() {
				this.$u.api.apiGradeList().then(result => {
					if (result.code === 0) {
						this.gradeList = result.data
						result.data.forEach((item, index) => {
							if (item.id === item.userInfo['grade_id']) {
								this.currentIndex = index
							}
						})
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading data exception')
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	// 头部部件样式
	.index-header-widget {
		position: relative;
		width: 100%;
		height: 230rpx;
		z-index: 100;
		overflow: hidden;
		&::after {
			width: 140%;
			height: 230rpx;
			position: absolute;
			left: -20%;
			top: 0;
			z-index: 100;
			content: '';
			border-radius: 0 0 50% 50%;
			background-color: #313131;
		}
	}
	
	// 卡片部件样式
	.index-swiper-widget {
		position: absolute;
		top: 20rpx;
		z-index: 2000;
		width: 100%;
		.grade-card-item {
			height: 320rpx;
			width: 600rpx;
			position: relative;
			background-size: 100% 100%;
			.card-left {
				padding: 26rpx;
				padding-right: 0;
			}
			.reach {
				position: absolute;
				top: 30rpx;
				right: 0;
				font-size: 24rpx;
				color: #FFFFFF;
				padding: 8rpx 26rpx;
				border-top-left-radius: 30rpx;
				border-bottom-left-radius: 30rpx;
				background-color: #4c4c4c;
			}
		}
	}

	// 权益部件样式
	.index-privilege-widget {
		width: 100%;
		padding: 160rpx 0 20rpx 0;
		background-color: #FFFFFF;
		.main {margin-top: 40rpx; margin-bottom:20rpx;}
		.main .item {width: 25%;}
	}
	
	// 任务部件样式	
	.index-task-widget {
		color: #5c5c5c;
		padding: 0 34rpx;
		margin: 20rpx 0;
		background-color: #FFFFFF;
		.task-item {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 34rpx 0;
			.icon {
				display: flex;
				align-items: center;
				justify-content: center;
				width: 80rpx;
				height: 80rpx;
				background-color: #fff6f1;
			}
		}
		.button {
			font-size: 28rpx;
			color: #FFFFFF;
			text-align: center;
			width: 160rpx;
			height: 60rpx;
			line-height: 60rpx;
			border-radius: 60rpx;
			background-color: #c2926a;
		}
	}

</style>
