<template>
	<view class="diy-notice" :style="{
		marginLeft: itemConfig.marginAlign+'px',
		marginRight: itemConfig.marginAlign+'px',
		marginTop: itemConfig.marginTop+'px',
		borderRadius: itemConfig.borderRadius+'px',
		background: itemConfig.background }">
		
		<view class="notice__icon">
			<image class="icon" :src="itemConfig.icon"></image>
		</view>
		
		<view class="notice__split"></view>
		<view class="notice__text">
			<u-notice-bar mode="vertical" 
				:list="list" @click="onJump"
				:volume-icon="false" :bg-color="itemConfig.background || '#FFF'" :color="itemConfig.textColor || '#000'"
				:duration="3500" :more-icon="true" padding="10rpx 0">
			</u-notice-bar>
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
	import { toPage } from '@/utils/tools.js'
	export default {
		props: {
			itemIndex: String,
			itemConfig: Object,
			itemData: Array
		},
		data() {
			return {
				list: []
			}
		},
		watch: {
			itemData: {
				immediate: true,
				handler() {
					let data = []
					this.itemData.forEach(item => {
						data.push(item.title)
					})
					this.list = data
				}
			}
		},
		methods: {
			/**
			 * 跳转页面
			 */
			onJump(index) {
				let linkUrl = this.itemData[index].linkUrl
				if (linkUrl) {
					toPage(linkUrl)
				}
			}
		}
	}
</script>

<style lang="scss">
	.diy-notice {
		display: flex; 
		align-items: center; 
		height: 76rpx; 
		padding: 0 20rpx;
		.notice__icon {
			flex-shrink: 0;
			width: 128rpx;
			height: 34rpx;
			.icon {
				width: 100%;
				height: 100%;
			}
		}
		.notice__split { 
			width: 2rpx; 
			height: 26rpx; 
			background-color: #E4E4E4; 
			margin: 0 22rpx;
			margin-right: 6rpx;
		}
		.notice__text {
			flex: 1;
			overflow: hidden;
		}
	}
</style>
