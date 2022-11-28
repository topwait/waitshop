<template>
	<view class="diy-search" :style="{background: itemConfig.background}">
		
		<view class="search-input" 
			:class="itemConfig.searchStyle" 
			:style="{textAlign: itemConfig.textAlign, background: itemConfig.inputBgColor}"
			@click="onJump('/pages/goods/search/search')">
			
			<u-icon name="search" :color="itemConfig.textColor || ''" size="32"></u-icon>
			
			<text class="search-input-text" 
				:style="{color: itemConfig.textColor}">
				{{ itemConfig.placeholder }}
			</text>
				
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
	import { toPage } from '@/utils/tools.js'
	export default {
		props: {
			itemIndex: String,
			itemConfig: Object,
			itemData: Array
		},
		methods: {
			/**
			 * 跳转页面
			 */
			onJump(url) {
				toPage(url)
			}
		}
	}
</script>

<style lang="scss">
	.diy-search {
		font-size: 26rpx;
		padding: 20rpx 20rpx;
		background: #FFFFFF;
	}
	.search-input {
		height: 70rpx;
		line-height: 70rpx;
		color: #999;
		overflow: hidden;
		padding: 0 10px;
		background: #F4F4F4;
		.search-input-text {
			margin-left: 10rpx;
			font-size: 26rpx;
			color: #999;
		}
		&.radius { border-radius: 10rpx; }
		&.round { border-radius: 60rpx; }
	}
</style>
