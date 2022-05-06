<template>
	<view class="diy-image-magic">
		
		<!-- 矩阵风格 -->
		<view v-if="itemConfig.layout > -1" class="magic-style-matrix" :class="'avg-sm-'+ itemConfig.layout"
			:style="{
				background: itemConfig.background,
				marginTop: itemConfig.marginTop+'px',
				marginLeft: itemConfig.marginLeft+'px',
				marginRight: itemConfig.marginLeft+'px',
				padding: itemConfig.padding+'px',
				borderRadius: itemConfig.borderRadius+'px'
			}">
			<view class="magic-item" v-for="(item, index) in itemData" :key="index"
				@click="onJump(item.linkUrl)"
				:style="{padding: itemConfig.interval+'px'}">
				<image class="image" :lazy-load="true" :src="item.imgUrl" mode="widthFix"></image>
			</view>
		</view>
		
		<!-- 橱窗风格 -->
		<view v-else class="magic-style-window" :style="{ 
				background: itemConfig.background,
				marginTop: itemConfig.marginTop+'px',
				marginLeft: itemConfig.marginLeft+'px',
				marginRight: itemConfig.marginLeft+'px',
				padding: itemConfig.padding+'px',
				borderRadius: itemConfig.borderRadius+'px'
			}"
		>
			<view class="magic-window-wrap">
				<view class="index-region-left" :style="{paddingRight: itemConfig.interval+'px'}">
					<image class="image" :lazy-load="true" :src="itemData[0].imgUrl" @click="onJump(itemData[0].linkUrl)"></image>
				</view>
				<view class="index-region-right">
					<view v-if="itemData.length >= 2" class="modular_top" 
						:style="{paddingBottom: itemConfig.interval+'px'}">
						<image class="image" :lazy-load="true" :src="itemData[1].imgUrl" @click="onJump(itemData[1].linkUrl)"></image>
					</view>
					<view class="modular_bottom">
						<view v-if="itemData.length >= 3" class="left"
							:style="{paddingRight: itemConfig.interval+'px'}">
							<image class="image" :lazy-load="true" :src="itemData[2].imgUrl" @click="onJump(itemData[2].linkUrl)"></image>
						</view>
						<view v-if="itemData.length >= 4" class="right">
							<image class="image" :lazy-load="true" :src="itemData[3].imgUrl" @click="onJump(itemData[3].linkUrl)"></image>
						</view>
					</view>
				</view>
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
	// 矩阵风格
	.magic-style-matrix {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		.image { display: block; width: 100%; }
		&.avg-sm-2 { .magic-item { width: 50%; } }
		&.avg-sm-3 { .magic-item { width: 33.33333333%; } }
		&.avg-sm-4 { .magic-item { width: 25%; } }
	}
	
	// 橱窗风格
	.magic-style-window {
		.magic-window-wrap {
			position: relative;
			height: 0;
			width: 100%;
			margin: 0;
			padding-bottom: 50%;
			box-sizing: border-box;
			.image {
				width: 100%;
				height: 100%;
			}
			.index-region-left {
				position: absolute;
				top: 0;
				left: 0;
				width: 50%;
				height: 100%;
				box-sizing: border-box;
			}
			.index-region-right {
				position: absolute;
				width: 50%;
				top: 0;
				left: 50%;
				height: 100%;
				box-sizing: border-box;
				.modular_top {
					position: absolute;
					top: 0;
					left: 0;
					width: 100%;
					height: 50%;	
					box-sizing: border-box;
				}
				.modular_bottom {
					position: absolute;
					top: 50%;
					left: 0;
					width: 100%;
					height: 50%;
					box-sizing: border-box;
					.left {
						position: absolute;
						top: 0;
						left: 0;
						width: 50%;
						height: 100%;
						box-sizing: border-box;
					}
					.right {
						position: absolute;
						top: 0;
						left: 50%;
						width: 50%;
						height: 100%;
						box-sizing: border-box;
					}
				}
			}
		}
	}
</style>
