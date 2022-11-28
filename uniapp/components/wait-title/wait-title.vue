<template>
	<view>
		<view v-if="skin === 'line'" class="style-line-widget" :style="{paddingTop: topPadding, paddingBottom: bottomPadding}">
			<view class="line left-spot" :style="{borderColor: lineColor}"></view>
			<view class="text" :style="{color: textColor}">{{text}}</view>
			<view class="line right-spot" :style="{borderColor: lineColor}"></view>
		</view>
		
		<!-- 斑点风格 -->
		<view v-if="skin === 'speckle'" class="style-speckle-widget" :style="{paddingTop: topPadding, paddingBottom: bottomPadding}">
			<view class="ic left">
				<view class="spot spot-one" :style="{backgroundColor: spotOneColor}"></view>
				<view class="spot spot-tow" :style="{backgroundColor: spotTowColor}"></view>
			</view>
			<view class="text" :style="{color: textColor}">{{text}}</view>
			<view class="ic right">
				<view class="spot spot-one" :style="{backgroundColor: spotOneColor}"></view>
				<view class="spot spot-tow" :style="{backgroundColor: spotTowColor}"></view>
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
		props: {
			// 皮肤类型: [line,speckle]
			skin: {
				type: String,
				default: 'line'
			},
			// 文本内容
			text: {
				type: String,
				default: '猜你喜欢'
			},
			// 文本颜色
			textColor: {
				type: String,
				default: '#333'
			},
			// 线条颜色
			lineColor: {
				type: String,
				default: '#999'
			},
			// 上边内边距
			topPadding: {
				type: String,
				default: '20rpx'
			},
			// 下内边距
			bottomPadding: {
				type: String,
				default: '20rpx'
			},
			// 第一点颜色
			spotOneColor: {
				type: String,
				default: '#bf916d'
			},
			// 第二点颜色
			spotTowColor: {
				type: String,
				default: '#edd8c5'
			}
		}
	}
</script>

<style lang="scss">
	// 线条风格标题
	.style-line-widget {
		position: relative;
		display: flex;
		align-items: center;
		justify-content: center;
		.text {
			color: #333;
			padding: 0 24rpx;
		}
		.line {
			position: relative;
			width: 150rpx;
			border-bottom: 1rpx solid #999;
			&::after {
				position: absolute;
				top: -4rpx;
				content: "";
				width: 12rpx;
				height: 12rpx;
				border-radius: 50%;
				background-color: #333;
			}
		}
		.left-spot {&::after {right: 0;}}
		.right-spot {&::after {left: 0;}}
	}
	
	// 斑点风格标题
	.style-speckle-widget {
		display: flex;
		justify-content: center;
		.text {
			color: #c2926a;
			font-size: 40rpx;
			font-weight: bold;
		}
		.ic {
			width: 36rpx;
			position: relative;
			display: flex;
			margin-top: 4rpx;
			.spot {
				width: 10rpx;
				height: 30rpx;
				transform:rotate(-45deg);
				border-radius: 20rpx;
				background-color: #bf916d;
				&.spot-one {
					position: absolute;
					top: 16rpx;
					left: 0;
				}
				&.spot-tow {
					position: absolute;
					top: -6rpx;
					background-color: #edd8c5;
				}
			}
			&.right {
				margin-left: 24rpx;
				.spot {transform:rotate(45deg);}
			}
		}
	}
</style>
