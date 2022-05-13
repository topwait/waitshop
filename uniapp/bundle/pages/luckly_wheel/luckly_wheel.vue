<template>
	<view class="condition">

		<!-- 背景图部件 -->
		<image class="background" src="/bundle/static/lottery/lottery_bg.png"></image>
		
		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 抽奖部件 -->
		<view class="index-lottery-widget">
			<view class="u-flex u-row-right" 
				@click="$toPage('/bundle/pages/luckly_record/luckly_record?login=true')">
				<view class="whell-prize">我的奖品</view>
			</view>
			
			<view class="wheel-container">
				<view class="wheel-notice">
					<image class="icon" src="/bundle/static/ic_notice.png"></image>
					<view class="message">
						<swiper autoplay vertical style="height:33rpx">
							<swiper-item v-for="(item, index) in records" :key="index" class="row">
								<text class="u-font-22 marquee_text" style="color: #F9EDDD;">{{item.tips}}</text>
							</swiper-item>
						</swiper>
					</view>
				</view>
				<view class="wheel-lottery">
					<view v-for="(item, index) in lotteryData" :key="index"
						:class="'lottery-item ' + (item.id==0 ? 'draw' : '') + (activeIndex == index ? 'active' : '')"
						@tap="start(item.id)">
						<image class="image" :src="item.image"></image>
						<view class="text" v-if="item.id!=0">{{item.name}}</view>
						<view class="text" v-else>剩余{{surplus}}次</view>
					</view>
				</view>
				<view class="wheel-rule">
					<view class="u-font-30" style="color:#FCD7D2; margin-bottom: 26rpx;">活动规则</view>
					<text class="u-font-sm" style="color:#FCD7D2;">{{config.rules}}</text>
				</view>
			</view>
		</view>
		
		<!-- 中奖弹窗部件 -->
		<u-popup v-model="showResult" mode="center" class="transparent">
		    <view class="win-result-popup">
				<view class="result-container">
					<view class="result-reward">
						<text style="color:#F95F2F; font-size:62rpx; text-align:center;">{{resultTips}}</text>
					</view>
					<view class="result-btn" @click="onClose">点击收下</view>
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
	const LOTTERY_ORDER = [0, 1, 2, 5, 8, 7, 6, 3]
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				
				// 抽奖结果弹窗
				showResult: false,
				
				// 是否在抽奖中
				isLottery: false,
				// 位移总次数
				currentIndex: 0,
				// 中奖索引
				luckyOrder: -1,
				// 转盘旋转速度
				speed: 200,
				// 当前活跃的坐标
				activeIndex: -1,
				// 最少转动多少圈
				circleTimes: 30,
				
				// 抽奖配置参数
				config: {},
				// 剩余抽奖次数
				surplus: 0,
				// 最近抽奖信息
				records: [],
				// 抽奖奖品列表
				lotteryData: [],
				// 抽奖结果
				resultTips: ""
			}
		},
		onShow() {
			this.getLotteryPrize()
		},
		methods: {
			/**
			 * 获取抽奖奖品
			 */
			getLotteryPrize() {
				this.$u.api.apiLotteryPrize().then(result => {
					if (result.code === 0) {
						this.config = result.data.config
						this.records = result.data.record
						this.surplus = result.data.surplus
						this.lotteryData = result.data.prize
						
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast("loading data exception")
					}
				})
			},
			
			/**
			 * 关闭弹窗
			 */
			onClose() {
				this.showResult = false
			},
			
			/**
			 * 开始抽奖
			 */
			start(type) {
				if (!this.config.is_open) {
					this.$showToast('抽奖活动已结束')
				}
				if (this.surplus <= 0) {
					this.$showToast('今日次数已用完')
				}
				if (type === 0 && !this.isLottery) {
					this.$u.api.apiLotteryDraw().then(result => {
						if (result.code === 0) {
							let index = this.lotteryData.findIndex(item => {
								return item.id == result.data.id
							})
							switch (index) {
								case 0: index = 0; break;
								case 1: index = 1; break;
								case 2: index = 2; break;
								case 3: index = 7; break;
								case 5: index = 3; break;
								case 6: index = 6; break;
								case 7: index = 5; break;
								case 8: index = 4; break;
								default: index = -1; break;
							}
							this.resultTips = result.data.tips;
							this.luckyOrder = index
							this.isLottery = true
							this.startLottery();
						} else {
							this.$showToast(result.msg)
						}
					})
				}
			},
			
			/**
			 * 重置抽奖
			 */
			reset() {
				this.isLottery = false
				this.currentIndex = 0
				this.speed = 100
				this.luckyOrder = -1
			},
			
			/**
			 * 停止回调
			 */
			stopCallback(luckyIndex) {
				this.surplus -= 1
				this.showResult = true
			},
			
			/**
			 * 抽奖逻辑
			 */
			startLottery() {
			    let {currentIndex} = this;
			    let activeIndex = LOTTERY_ORDER[currentIndex % LOTTERY_ORDER.length]
				const currentOrder = currentIndex % 8
		
			    this.activeIndex = activeIndex
			    this.currentIndex = ++currentIndex
				if (currentIndex > this.circleTimes + 8 && this.luckyOrder == currentOrder) {
			        if (this.lotteryTimer) {
			            clearTimeout(this.lotteryTimer)
			        }
			        
			        setTimeout(() => {
			            this.stopCallback(LOTTERY_ORDER[this.luckyOrder]);
			            setTimeout(() => {
							this.reset()
							this.activeIndex = -1
			            }, 1500);
			        }, 500)
			    } else {
			        if (currentIndex < this.circleTimes) {
			            this.speed -= 10
			        } else if (currentIndex > this.circleTimes + 8 && this.luckyOrder == currentOrder + 1) {
			            this.speed += 80
			        } else {
			            this.speed += 20
			        }
			        
			        if (this.speed < 50) {
						this.speed = 50
			        }
			        
			        this.lotteryTimer = setTimeout(this.startLottery.bind(this), this.speed);
			    }
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #ff2c3c;
		.background {
			width: 100%;
			height: 1108rpx;
			vertical-align: middle;
		}
	}
	
	// 抽奖样式
	.index-lottery-widget {
	    margin-top: -405px;
	    position: relative;
		.wheel-container {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			margin: 0 15rpx;
			margin-top: 10px;
			border-radius: 15px;
			padding: 13px 7px 18px;
			border: 6px solid #fe6847;
			background-color: #ed3720;
		}
		.wheel-notice {
			display: flex;
			align-items: center;
			width: 586rpx;
			padding: 13rpx 28rpx;
			margin-bottom: 20rpx;
			border-radius: 100rpx;
			border: 2rpx solid #EDB17D;
			background-color: #D30C16;
			.icon {
				width: 34rpx;
				height: 34rpx;
			}
			.message {
				margin-left: 20rpx;
				width: 500rpx;
				height: 33rpx;
			}
		}
		.wheel-rule {
			width: 640rpx;
			margin-top: 12px;
			padding: 10px 8px 10px 14px;
			border-radius: 10px;
			background-color: #d30c16;
		}
	}
	
	// 弹窗样式
	.win-result-popup {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		width: 545rpx;
		height: 520rpx;
		.result-container {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: space-between;
			width: 545rpx;
			height: 514rpx;
			background-size: 100% 100%;
			background-image: url("../../static/lottery/prize_bg.png");
		}
		.result-reward {
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 0px 118rpx;
			margin-top: 90rpx;
		}
		.result-btn {
			display: flex;
			align-items: center;
			justify-content: center;
			margin-bottom: 20rpx;
			width: 320rpx;
			height: 70rpx;
			color: #7B3200;
		    border-radius: 30px;
			background: linear-gradient(180deg, #FEF0B0 0%, #FFA92E 100%);
		}
	}
	
	// 我的奖品
	.whell-prize {
		font-size: 30rpx;
		font-weight: bold;
		color: #7d350c;
		padding: 10px;
		box-shadow: 0 3px 0 #f95f2f;
		border-radius: 50px 0px 0px 50px;
		background-color: #fef0b5;
	}
	
	// 转盘样式
	.wheel-lottery {
		display: flex;
		align-items: center;
		flex-wrap: wrap;
		justify-content: space-between;
		width: 640rpx;
		height: 640rpx;
		padding: 35rpx 40rpx;
		background-size: 100% 100%;
		background-image: url("../../static/lottery/lottery_light.png");
		.lottery-item {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			width: 180rpx;
			height: 180rpx;
			background-size: 100% 100%;
			background-image: url("../../static/lottery/lottery_block.png");
			&.draw {background-image: url("../../static/lottery/lottery_button.png");}
			&.active {opacity: 0.7;}
			.image {width: 80rpx; height: 80rpx;}
			.text {font-weight: bold; color: #743C3C; font-size: 22rpx; margin-top: 20rpx;}
		}
	}
	
</style>
