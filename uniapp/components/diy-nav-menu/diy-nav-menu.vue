<template>
	<view class="diy-nav-menu">
		<view class="nav-menu-list"
			:class="itemConfig.rowsNum === '4'?'line-row-4':(itemConfig.rowsNum ==='3'?'line-row-3':'line-row-5')"
			:style="{
				background:itemConfig.background,
				borderRadius:itemConfig.borderRadius+'px',
				marginLeft:itemConfig.marginLeft+'px',
				marginRight:itemConfig.marginLeft+'px',
				marginTop:itemConfig.marginTop+'px',
			}">
			
			<view class="nav-menu-item" v-for="(item, index) in itemData" :key="index" @click="onJump(item.linkUrl)">
				<image class="nav-menu-item__img" :src="item.imgUrl" :lazy-load="true" mode="widthFix"></image>
				<view class="nav-menu-item__text" :style="{'color': item.color}">{{item.text}}</view>
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
	import { toPage } from '../../utils/tools.js'
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
	.diy-nav-menu {
		.nav-menu-list {
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			text-align: center;
			box-sizing: border-box;
			padding: 0 5px;
			&.line-row-3 .nav-menu-item { width: 33.33333333%;}
			&.line-row-4 .nav-menu-item { width: 25%;}
			&.line-row-5 .nav-menu-item { width: 20%;}
			.nav-menu-item {
				width: 20%;
				margin: 10px 0;
				.nav-menu-item__img {
					width: 35px;
					height: 35px;
					margin-bottom: 4px;
				}
				.nav-menu-item__text {
					font-size: 12px;
					color: #050505;
				}
			}
		}
	}
</style>
