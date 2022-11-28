<template>
	<view>
		<view class="index-empty-widget" :style="bgColor ? 'background-color:'+bgColor : ''">
			<image class="img-null" :src="icon"></image>
			<view class="muted">{{tips}}</view>
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
			tips: {
				type: String,
				default: '暂无数据~'
			},
			icon: {
				type: String,
				default: '/static/empty/empty_null.png'
			},
			bgColor: {
				type: String,
				default: ''
			}
		}
	}
</script>

<style lang="scss">
	.index-empty-widget {
		text-align: center;
		padding: 80rpx 0 50rpx;
		.img-null {
			width: 300rpx;
			height: 300rpx;
		}
		.muted {
			font-size: 24rpx;
			color: #999999;
			padding: 20rpx 0;
		}
	}
</style>
