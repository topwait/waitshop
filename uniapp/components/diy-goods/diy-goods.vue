<template>
	<view class="diy-goods" :style="{
		background: itemConfig.background,
		marginLeft: itemConfig.marginLeft+'px',
		marginRight: itemConfig.marginLeft+'px',
		marginTop: itemConfig.marginTop+'px'}">
		
		<view class="goods-list" :class="['display__' + itemConfig.display, 'column__' + itemConfig.column]">
			<view class="goods-item" v-for="(goods, index) in itemData" 
				:key="index" @click="onJump('/pages/goods/detail/detail?goods_id='+goods.id)"
				:style="{borderRadius: itemConfig.borderRadius+'px'}">
				<!-- 单列商品 -->
				<block v-if="itemConfig.column == 1">
					<view class="goods-item_left" :style="{width: '240rpx', height: '240rpx'}">
						<image class="image" :lazy-load="true" :src="goods.image" :style="{
							borderTopLeftRadius:itemConfig.borderRadius+'px', 
							borderBottomLeftRadius:itemConfig.borderRadius+'px'}">
					</view>
					<view class="goods-item_right">
						<!-- 商品名称 -->
						<view v-if="itemConfig.show.goodsName" class="goods-item_title">
							<text>{{goods.name}}</text>
						</view>
					
						<view class="desc-sales">
							<text class="span">已有{{goods.sales_volume}}人购买</text>
						</view>
		
						<!-- 商品价格 -->
						<view class="desc_footer">
							<text v-if="itemConfig.show.goodsPrice"
								  class="span sale_price">¥{{goods.min_price}}</text>
							<text v-if="itemConfig.show.linePrice"
								  class="span market_price">¥{{goods.market_price}}</text>
						</view>
					
					</view>
				</block>
				
				<!-- 两列三列 -->
				<block v-else>
					<view class="goods_image" :style="{
							borderTopLeftRadius: itemConfig.borderRadius+'px',
							borderTopRightRadius: itemConfig.borderRadius+'px'
						}">
						<image class="image" :src="goods.image">
					</view>
					<view class="goods-item_desc">
						<view v-if="itemConfig.show.goodsName" class="goods_name">
							{{goods.name}}
						</view>
						<view class="detail-price">
							<text v-if="itemConfig.show.goodsPrice"
								  class="span sale_price">¥{{goods.min_price}}</text>
							<text v-if="itemConfig.show.linePrice"
								  class="span market_price">¥{{goods.market_price}}</text>
							<text v-if="itemConfig.show.goodsSales"
								  class="span sales_volume">{{goods.sales_volume}}人付款</text>
						</view>
					</view>
				</block>
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
	import {toPage} from '@/utils/tools.js'
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
	.diy-goods {
		.goods-list { box-sizing: border-box; }
		.goods-list.display__slide { overflow-x: hidden; overflow-x: auto; white-space: nowrap; }
		.goods-list.display__slide::-webkit-scrollbar { display: none; }
		
		.goods-list.column__1 .goods-item { display: flex; width: 100%; margin-bottom: 16rpx; box-sizing: border-box; background: #FFFFFF; }
		.goods-list.column__1 .goods-item:last-child { margin-bottom: 0; }
		.goods-list.column__1 .goods-item_left { display: flex; align-items: center; flex-shrink: 0; width: 240rpx; height: 240rpx; margin-right: 20rpx; background: #FFFFFF; }
		.goods-list.column__1 .goods-item_left .image { display: block; width: 100%; height: 100%; }
		.goods-list.column__1 .goods-item_right { position: relative; display: flex; flex-direction: column; justify-content: center; }
		.goods-list.column__1 .goods-item_right .goods-item_title { margin-bottom: 20rpx; font-size: 28rpx; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-height: 1.3; color: #333; }
		.goods-list.column__1 .goods-item_right	.desc-sales .span { display: inline-block; padding: 1px 20rpx; margin-bottom: 20rpx; font-size: 24rpx; color: #F79C0C; border-radius: 38px; background: #fff5e6;}
		.goods-list.column__1 .desc_footer .span { margin-right: 16rpx; }
		.goods-list.column__1 .desc_footer .sale_price { font-size: 15px; color: #f03c3c; }
		.goods-list.column__1 .desc_footer .market_price { font-size: 24rpx; color: #999; text-decoration:line-through; }
	
		.goods-list.display__slide.column__2 { display: flex; flex-wrap: nowrap; justify-content: space-between; }
		.goods-list.display__slide.column__2 .goods-item { flex-shrink: 0; padding-bottom: 0; }
		.goods-list.column__2 { display: flex; flex-wrap: wrap; justify-content: space-between; }
		.goods-list.column__2 .goods-item { width: 49%; margin-bottom: 12rpx; background: #FFFFFF; }
		.goods-list.column__2 .goods-item:nth-last-child(1) { margin-bottom: 0; }
		.goods-list.column__2 .goods-item:nth-last-child(2) { margin-bottom: 0; }
		
		.goods-list.display__slide.column__3 { flex-wrap: nowrap; }
		.goods-list.display__slide.column__3 .goods-item { flex-shrink: 0; padding-bottom: 0; margin-bottom: 0; width: 33.33333%; margin-right: 10rpx; }
		.goods-list.column__3 { display: flex; flex-wrap: wrap; justify-content: space-between; }
		.goods-list.column__3 .goods-item { width: 32%; margin-bottom: 12rpx; background: #FFFFFF; }
		.goods-list.column__3 .goods-item:nth-last-child(1) { margin-bottom: 0; }
		.goods-list.column__3 .goods-item:nth-last-child(2) { margin-bottom: 0; }
		.goods-list.column__3 .goods-item:nth-last-child(3) { margin-bottom: 0; }
		
		.goods-list .goods-item { box-sizing: border-box; }
		.goods-list .goods-item:nth-last-of-type(-n+2) { padding-bottom: 0; }
		.goods-list .goods-item .goods_image { position: relative; width: 100%; height: 0; padding-bottom: 100%; overflow: hidden; background: #FFFFFF;}
		.goods-list .goods-item .goods_image:after { content: ''; display: block; margin-top: 100%;  }
		.goods-list .goods-item .goods_image .image { position: absolute; width: 100%; height: 100%; top: 0; left: 0; object-fit: cover; }
		.goods-list .goods-item .goods-item_desc { padding: 10rpx; overflow: hidden; }
		.goods-list .goods-item .goods-item_desc .span { margin-right: 10rpx; }
		.goods-list .goods-item .goods-item_desc .goods_name { margin-bottom: 4rpx; overflow: hidden; white-space: initial; font-size: 26rpx; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; color: #333; }
		.goods-list .goods-item .goods-item_desc .sale_price { font-size: 30rpx; padding-right: 6rpx; color: $-color-major; }
		.goods-list .goods-item .goods-item_desc .market_price { padding-right: 6rpx; font-size: 24rpx; color: #999; text-decoration: line-through; }
		.goods-list .goods-item .sales_volume { font-size: 24rpx; color: #999; }
	}
</style>
