<template>
	<view>
		<!-- “列”商品组件 -->
		<view class="index-row-widget" v-if="layout == 'row'">
			<view class="product-item" v-for="(item, index) in list" :key="index" @click="onJump('/pages/goods/detail/detail?goods_id='+item.id)">
				<view style="width: 100%; height: 300rpx;">
					<image class="u-equal-bfb u-radius-tl-14 u-radius-tr-14" :lazy-load="true" :src="item.image"></image>
				</view>
				<view class="u-padding-lr-10">
					<view class="u-margin-tb-10 u-line-2 u-font-26">{{item.name}}</view>
					<view class="u-font-34 u-font-weight u-color-major">
						<text class="u-font-24">￥</text>
						<text>{{item.min_price}}</text>
					</view>
					<view class="u-flex u-row-between u-margin-tb-10 u-font-24 u-color-muted">
						<view class="u-line-through">￥{{item.market_price}}</view>
						<view>已售{{item.sales_volume}}件</view>
					</view>
				</view>
			</view>
		</view>
		
		<!-- “行”商品组件 -->
		<view class="index-col-widget" v-if="layout == 'col'">
			<view class="product-item" v-for="(item, index) in list" :key="index"  @click="onJump('/pages/goods/detail/detail?goods_id='+item.id)">
				<view style="width: 180rpx; height: 180rpx; flex-shrink: 0;">
					<image class="u-equal-bfb u-radius-tl-14 u-radius-14" :lazy-load="true" :src="item.image"></image>
				</view>
				<view class="info">
					<view class="u-line-2 u-font-30">{{item.name}}</view>
					<view class="u-margin-top-10 u-font-34 u-font-weight u-color-major">
						<text class="u-font-24">￥</text>
						<text>{{item.min_price}}</text>
					</view>
					<view class="u-flex u-row-between u-margin-tb-10 u-font-24 u-color-muted">
						<view class="u-line-through">￥{{item.market_price}}</view>
						<view>已售{{item.sales_volume}}件</view>
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
			layout: {
				type: String,
				default: 'row'
			},
			list: {
				type: Array,
				default: []
			}
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
	// “列”商品组件样式
	.index-row-widget {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		padding: 0 20rpx;
		.product-item {
			width: 49%;
			margin-top: 20rpx;
			border-radius: 14rpx;
			background-color: #FFFFFF;
		}
	}
	
	// “行”商品组件样式
	.index-col-widget {
		background-color: #FFFFFF;
		.product-item {
			display: flex;
			margin: 0 20rpx;
			padding: 20rpx 0;
			border-bottom: 1px solid #f6f6f6;
			&:last-child { border-bottom: 0; }
			.info {
				display: flex;
				flex-direction: column;
				justify-content: space-between;
				flex: 1;
				padding: 0 20rpx;
			}
		}
	}
</style>
