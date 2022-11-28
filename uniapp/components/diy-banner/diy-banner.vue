<template>
	<view class="diy-banner">
	
		<view class="diy-swiper">
			<image class="bgImage"
				v-if="itemConfig.type === 'card'" 
				:src="itemConfig.backdrop"
				:style="{visibility:itemConfig.backdrop ? 'block' : 'hidden'}"
			></image>
			
			<u-swiper :class="itemConfig.type === 'card' ? 'swiper' : ''" 
				:list="itemData"
				:mode="itemConfig.mode || 'round'"
				:effect3d="itemConfig.effect3d == 1 ? true : false"
				:interval="itemConfig.interval"
				:autoplay="autoplay"
				:duration="duration"
				:circular="circular"
				:bg-color="'0'"
				name="imgUrl"
				height="300"
				@click="onBanner"
			></u-swiper>
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
	import {toPage} from '@/utils/tools.js'
	export default {
		props: {
			itemIndex: String,
			itemConfig: Object,
			itemData: Array
		},
		data() {
			return {
				autoplay: true,  // 是否自动切换
				duration: 800, 	 // 滑动动画时长
				circular: true,	 // 到尾重头开始
			}
		},
		methods: {
			/**
			 * 跳转轮播
			 */
			onBanner(index) {
				let linkUrl = this.itemData[index].linkUrl
				if (linkUrl) {
					toPage(linkUrl)
				}
			}
		}
	}
</script>

<style lang="scss">
	// 轮播背景样式
	.diy-banner {
		position: relative;
		.bgImage { width: 100%; height: 300rpx; }
	}
	
	/**
	 * 轮播容器样式
	 */
	.diy-swiper {
		position: relative;
		height: 303rpx;
		.swiper {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			padding: 0 20rpx;
		}
	}
</style>
