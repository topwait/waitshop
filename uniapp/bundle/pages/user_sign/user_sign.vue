<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>

		<!-- 头部部件 -->
		<view class="index-header-widget">
			<view class="user u-flex">
				<view class="avatar">
					<image class="u-equal-bfb u-radius-circular" :lazy-load="true" :src="infoData.avatar"></image>
				</view>
				<view class="u-margin-left-20 u-color-white">
					<view class="u-font-30 u-font-weight">{{infoData.nickname}}</view>
					<view class="integral u-font-22 u-margin-tb-14">积分: {{infoData.integral}}</view>
				</view>
			</view>
		</view>
		
		<!-- 签到部件 -->
		<view class="index-sign-widget">
			<view class="datetime u-flex u-margin-bottom-20">
				<view class="day u-font-weight" style="font-size:120rpx;">{{dateData.month}}</view>
				<view class="u-margin-left-14">
					<view class="week u-font-32 u-font-weight" style="color:#8b8a98;">{{dateData.week}}</view>
					<view class="month u-font-28 u-color-muted u-margin-top-6">{{dateData.year}}</view>
				</view>
			</view>
			<view class="calendar u-flex u-flex-wrap">
				<view class="item" v-for="(item, index) in calendarData" :key="index">
					<view class="u-margin-bottom-10 u-color-lighter u-font-26">{{item.days}}天</view>
					<view class="circle u-flex u-relative" :class="item.status ? 'active-circle' : ''">
						<view class="num u-font-22 u-color-lighter">
							<u-icon name="checkbox-mark" size="34" color="#FFF" v-if="item.status"></u-icon>
							<view class="num u-font-22 u-color-lighter" v-else>+{{item.integral}}</view>
						</view>
					</view>
				</view>
			</view>
			<view class="button forbid" v-if="!infoData.is_open">活动已结束</view>
			<block v-else>
				<view class="button" v-if="!infoData.is_today_sign" @click="$u.debounce(onSign, 500)">立即签到</view>
				<view class="button forbid" v-if="infoData.is_today_sign">今日已签到</view>
			</block>
		</view>
		
		<!-- 连续签到部件 -->
		<view class="index-days-widget">
			<view class="u-font-30 u-color-lighter">已连续签到</view>
			<view class="main u-flex">
				<view class="item" v-for="(item, index) in daysArr" :key="index">{{item}}</view>
				<view class="day">天</view>
			</view>
			<view class="explain u-color-muted">
				{{infoData.explain}}
			</view>
		</view>
		
		<!-- 签到弹窗部件 -->
		<u-popup v-model="showPop" mode="center" class="transparent">
			<view class="index-popup-container">
				<view class="popup-header">+{{resultData.integral > 0 ? resultData.integral : resultData.growth}}</view>
				<view class="u-flex-col u-row-center u-col-center">
					<view class="reward-value">
						<image style="width:28rpx; height:30rpx; margin-right:8rpx" src="../../static/ic_integral.png"></image>
						<text v-if="resultData.integral > 0 && resultData.growth > 0">{{resultData.integral}}积分 + {{resultData.growth}}成长值</text>
						<block v-else>
							<text v-if="resultData.integral">{{resultData.integral}}积分</text>
							<text v-if="resultData.growth">{{resultData.growth}}成长值</text>
						</block>
					</view>
					<view style="margin-top:90rpx;">
						<view class="md" style="line-height: 36rpx">
							您已连续签到 <text class="u-color-major u-font-40">{{resultData.days}}</text>天
						</view>
					</view>
					<view class="popup-btn" @tap="onClose">确定</view>
				</view> 
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
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 连续签到
				daysArr: [],
				// 基本数据
				infoData: {},
				// 当前日期
				dateData: {},
				// 日历数据
				calendarData: [],
				// 签到弹窗
				showPop: false,
				// 签到结果
				resultData: {}
			}
		},
		onShow() {
			this.getSignCalendar()
		},
		methods: {
			/**
			 * 获取签到日历
			 */
			getSignCalendar() {
				this.$u.api.apiSignCalendar().then(result => {
					if (result.code === 0) {
						this.calendarData = result.data.calendar
						this.infoData = result.data.info
						this.dateData = result.data.date
						
						// 连续签到天数转数组格式
						let daysArr = []	
						let daysStr = this.infoData.even_sign_day.toString()
						for (let i=0; i < daysStr.length; i++) {
							daysArr.push( daysStr.charAt(i) )
						}
						if (daysArr.length < 4) {
							for (let i=0; i <= (5-daysArr.length); i++) {
								daysArr.unshift('0')
							}
						}
						this.daysArr = daysArr
						
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading data error')
					}
				})
			},
			
			/**
			 * 关闭弹窗
			 */
			onClose() {
				this.showPop = false
			},
			
			/**
			 * 确认签到
			 */
			onSign() {
				if (this.infoData.is_today_sign) {
					return this.$showToast('今天已签到,明天再来吧！')
				}
				this.$u.api.apiSignConfirm().then(result => {
					if (result.code === 0) {
						this.resultData = result.data
						this.showPop = true
						this.getSignCalendar()
					} else {
						this.$showError(result.msg)
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
		z-index: 1000;
		height: 260rpx;
		background-color: #FF5058;
		.user {
			position: absolute;
			top: 30rpx;
			left: 30rpx;
			.avatar {
				width: 110rpx;
				height: 110rpx;
			}
			.integral {
				padding: 2rpx 10rpx;
				border-radius: 4rpx;
				background-color: #fc9103;
			}
		}
	}
	
	// 签到部件样式
	.index-sign-widget {
		position: absolute;
		top: 160rpx;
		left: 30rpx;
		right: 30rpx;
		z-index: 2000;
		padding: 30rpx;
		padding-bottom: 20rpx;
		border-radius: 20rpx;
		background-color: #FFFFFF;
		.calendar {
			.item {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
			    width: 14.2%;
			    margin-bottom: 20rpx;
			}
			.item .num {
			    width: 68rpx;
			    height: 68rpx;
			    line-height: 58rpx;
			    border-radius: 50%;
			    display: flex;
			    align-items: center;
			    justify-content: center;
			    background-color: #f2f2f2;
			}
			.item .circle::before {
				position: absolute;
				right: 68rpx;
			    content: "";
			    width: 34rpx;
			    height: 6rpx;
			    background-color: #f2f2f2;
			}
			.item:nth-of-type(7n+1) .circle::before {
			    content: "";
			    background-color: rgba(0, 0, 0, 0);
			}
			.item .active-circle::before {
				position: absolute;
				right: 68rpx;
			    content: "";
			    width: 34rpx;
			    height: 4rpx;
			    background-color: #FFBD40;
			}
			.item .active-circle .num {
				display: flex;
				align-items: center;
				justify-content: center;
				z-index: 3000;
				width: 68rpx;
				height: 68rpx;
				line-height: 58rpx;
				border-radius: 50%;
				background-color: #FF9900;
			}
		}
		.button {
			margin: 20rpx 120rpx;
			text-align: center;
			color: #FFFFFF;
			height: 80rpx;
			line-height: 80rpx;
			border-radius: 60rpx;
			background-color: #FF5058;
			&.forbid {
				background-color: #CCCCCC;
			}
		}
	}
	
	// 连续签到部件样式
	.index-days-widget {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		margin: 0 30rpx;
		margin-top: 370rpx;
		padding: 40rpx 30rpx;
		border-radius: 20rpx;
		background-color: #FFFFFF;
		.main {
			margin: 50rpx 0;
			.item {
				text-align: center;
				font-size: 70rpx;
				color: #FFFFFF;
				width: 80rpx;
				height: 110rpx;
				line-height: 110rpx;
				margin: 0 10rpx;
				border-radius: 10rpx;
				background-color: #FF5058;
			}
			.day {
				color: #000;
				font-size: 30rpx;
				margin-top: 70rpx;
			}
		}
	}
	
	// 签到弹窗样式
	.index-popup-container {
		position: relative;
		height: 626rpx;
		width: 560rpx;
		background-size: 100%;
		background-repeat: no-repeat;
		background-image: url(../../static/sing_popup.png);
		.popup-header {
			display: flex;
			align-items: center;
			justify-content: center;
			color: #ff2c3c;
			font-size: 46rpx;
			line-height: 36rpx;
			font-weight: bold;
			padding-top: 90rpx;
			padding-bottom: 150rpx;
		}
		.popup-btn {
			color: #FFFFFF;
			padding: 16rpx 190rpx;
			margin-top: 28rpx;
			border-radius: 60rpx;
			background-color: #ff2c3c;
		}
		.reward-value {
			display: flex;
			align-items: center;
			margin-top: 20rpx;
			color: #fff;
			font-size: 26rpx;
			padding: 16rpx 22rpx 16rpx 42rpx;
			background: linear-gradient(82deg, #fa5132, #ec3c22 49%, #fa5332);
		}
	}
</style>
