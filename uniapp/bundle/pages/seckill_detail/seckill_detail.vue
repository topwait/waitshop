<template>
	<view  class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 轮播部件 -->
		<view class="index-swiper-widget">
			<swiper class="swiper"
				:autoplay="autoplay" 
				:duration="duration" 
				:circular="circular" 
				:indicator-dots="indicatorDots"  
				:interval="interval"
				@change="changeSwiperCurrent"
				@click="onPreviewImg">
				<swiper-item v-for="(item, index) in goodsDetail.banner" :key="index">
					<image class="swiper-slide-images" :src="item"></image>
				</swiper-item>
			</swiper>
			<view class="instructions">
				<text>{{ currentSwiperIndex || 0 }}</text>
				<text>/{{ bannerSwiperNumber || 0 }}</text>
			</view>
		</view>
		
		<!-- 金额部件 -->
		<view class="index-amount-widget">
			<view class="amount-bg">
				<img class="u-equal-bfb" src="/bundle/static/seckill_detail_bg.png">
				<view class="price u-flex">
					<view class="u-font-30 u-color-white u-margin-right-6">秒杀价</view>
					<wait-price :amount="goodsChecked.seckill_price || goodsDetail.min_seckill_price" fontColor="#FFF" fontWeight="bold" mainSize="46rpx" minorSize="28rpx"></wait-price>
					<view class="u-margin-left-10">
						<wait-price :amount="goodsChecked.sell_price" fontColor="#FFF" :lineThrough="true"></wait-price>
					</view>
				</view>
			</view>
			<view class="count-down">
				<view class="u-font-24 u-color-major u-margin-top-10">{{goodsDetail.surplus_start_time ? '距离开始还剩' : '距离结束还剩'}}</view>
				<view class="u-margin-top-10">
					<u-count-down :timestamp="goodsDetail.surplus_start_time ? goodsDetail.surplus_start_time : goodsDetail.surplus_end_time" 
						color="#FFF" separator-color="#FF2C3C" bg-color="#FF2C3C"></u-count-down>
				</view>
			</view>
		</view>
		
		<!-- 产品组件 -->
		<view class="index-product-widget">
			<view class="u-flex u-margin-bottom-20">
				<view class="wrap-product__name">{{goodsDetail.name}}</view>
				<image src="/static/icon/ic_share.png" 
					style="width:132rpx; height:60rpx; flex-shrink:0; margin-top: -16rpx;"
					@click="onShare"></image>
			</view>
			<view class="wrap_product__meta">
				<text>库存: 999</text>
				<text>销量: 230</text>
				<text>浏览量: 230</text>
			</view>
		</view>
		
		<!-- 规格部件 -->
		<view class="index-sku-widget" @click="onPopupSku()">
			<view class="u-font-28 u-padding-lr-20 u-color-lighter">已选</view>
			<view class="u-flex-1 u-line-1 u-padding-lr-10">
				<view class="u-font-28 u-color-normal">{{ goodsChecked.spec_value_str || "默认" }}</view>
			</view>
			<u-icon name="arrow-right" size="28" color="#999999" style="padding:0 20rpx;"></u-icon>
		</view>
		
		<!-- 评论部件 -->
		<view class="index-comment-widget" v-if="goodsDetail.comment">
			<view class="u-flex u-row-between u-padding-tb-20 u-border-bottom">
				<view>商品评价({{goodsDetail.comment.count || 0}})</view>
				<view>
					<text class="u-margin-right-10 u-color-lighter">查看全部</text>
					<u-icon name="arrow-right" size="28" color="#999999"></u-icon>
				</view>
			</view>
			<view>
				<view class="comment-item" v-for="(item, index) in goodsDetail.comment.list" :key="index">
					<view class="u-flex">
						<u-image width="60rpx" height="60rpx" border-radius="50%" :lazy-load="true" :src="item.image"></u-image>
						<text class="u-font-26 u-margin-left-10">{{ item.nickname }}</text>
					</view>
					<view class="u-margin-top-10">
						{{ item.content }}
					</view>
				</view>
			</view>
		</view>
		
		<!-- 详细部件 -->
		<view class="index-detail-widget" v-if="goodsDetail.content">
			<view class="detail-title">商品详情</view>
			<u-parse :html="goodsDetail.content"></u-parse>
		</view>
		
		<!-- 购买部件 -->
		<view style="height: 100rpx;"></view>
		<view class="index-footer-widget">
			<view class="icon-group">
				<navigator class="icon-item" hover-class="none" url="/pages/contact_offical">
					<u-icon name="kefu-ermai" size="42" color="#666"></u-icon>
					<text>客服</text>
				</navigator>
				<view class="icon-item" @click="onCollect()">
					<u-icon name="star" size="42" color="#666" v-if="!goodsDetail.is_collect"></u-icon>
					<u-icon name="star-fill" size="42" color="red" v-else></u-icon>
					<text>收藏</text>
				</view>
				<view class="icon-item">
					<u-icon name="shopping-cart" size="42" color="#666"></u-icon>
					<text>购物车</text>
					<text class="cart-num">{{cartNum || 0}}</text>
				</view>
			</view>
			<view class="button-group">
				<view class="button-item button-item--expect" v-if="goodsDetail.surplus_start_time">敬请期待</view>
				<block v-else>
					<view class="button-item button-item--buy" v-if="goodsDetail.is_end===0 && goodsDetail.total_stock >0" @click="onPopupSku(0)">立即开枪</view>
					<view class="button-item button-item--end" v-if="goodsDetail.is_end===0 && goodsDetail.total_stock<=0">已售罄</view>
					<view class="button-item button-item--end" v-if="goodsDetail.is_end===1">抢购已结束</view>
				</block>
			</view>
		</view>
		
		<!-- Sku组件 -->
		<wait-popup-sku :show="showSku" :goods="goodsDetail" 
			@change="onChoiceSku" @close="showSku = false"
			@buynow="onBuy" @addcart="onAddCart">
		</wait-popup-sku>
		
		<!-- 分享组件 -->
		<wait-popup-share shareType="seckill" :show="showShare" 
			:tid="seckillId"  @close="showShare = false">
		</wait-popup-share>
		
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
	import {mapState, mapMutations} from 'vuex'
	import {toLogin} from '@/utils/tools'
	export default {
		data() {
			return {
				autoplay: true, 	   // 是否自动切换
				duration: 800, 		   // 滑动动画时长
				circular: true,		   // 到尾重头开始
				interval: 3000, 	   // 自动切换时间间隔
				indicatorDots: false,  // 是否显示面板指示点
				currentSwiperIndex: 1, // 轮播图指针
				bannerSwiperNumber: 0, // 轮播图总数量

				// 首次加载
				isFirstLoading: true,
				// 秒杀ID
				seckillId: null,
				// 商品ID
				goodsId: null,
				// 商品详细数据
				goodsDetail: {},
				// 选中的商品规格
				goodsChecked: {}, 
				// 是否显示SKU窗口
				showSku: false,
				// 是否显示分享窗口
				showShare: false,

				// 购物车数量
				cartNum: 0
			}
		},
		onLoad(options) {
			this.seckillId = parseInt(options.id)
			this.goodsId = parseInt(options.goods_id)
			this.getCartNum()
			this.getSeckillDetail()
		},
		computed: {
			...mapState(['isLogin'])
		},
		methods: {
			// 改变轮播图指针
			changeSwiperCurrent(e) {
			    this.currentSwiperIndex = e.detail.current + 1
			},

			/**
			 * 获取秒杀商品
			 */
			getSeckillDetail() {
				const param = {id: this.seckillId}
				this.$u.api.apiSeckillDetail(param).then(result => {
					if (result.code === 0) {
						let data = result.data
						this.goodsDetail = data
						this.bannerSwiperNumber = data.banner.length
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading seckill error')
					}
				})
			},
			
			/**
			 * 获取购物车数量
			 */
			getCartNum() {
				this.$u.api.apiCartNum().then(result => {
					if (result.code === 0) {
						this.cartNum = result.data.count
					} else {
						this.$showToast('loading seckill error')
					}
				})
			},
			
			/**
			 * 收藏商品
			 */
			onCollect() {
				if (!this.isLogin) return toLogin()
				const param = {goods_id: this.goodsId, status: this.goodsDetail.is_collect ? 0 : 1}
				this.$u.api.apiGoodsCollectAdd(param).then(result => {
					if (result.code === 0) {
						this.goodsDetail.is_collect = !this.goodsDetail.is_collect
						this.$showToast(result.msg)
					} else {
						this.$showToast(result.msg)
					}
				})
			},
			
			/**
			 * 弹出分享窗口
			 */
			onShare() {
				if (!this.isLogin) return toLogin()
				this.showShare = true
			},

			/**
			 * 弹出SKU
			 */
			onPopupSku() {
				if (!this.isLogin) return toLogin()
				this.showSku = true
			},
			
			/**
			 * 选择SKU
			 */
			onChoiceSku(value) {
				this.goodsChecked = value.checked
			},

			/**
			 * 去下单
			 */
			onBuy(e) {
				if (!this.isLogin) return toLogin()
				let {id, goodsNum} = e.detail
				let products = [{
					goods_id: this.goodsId, 
					sku_id: id, 
					num: goodsNum
				}]
				this.$u.api.apiOrderSettle({'products':products, 'seckill_id': this.seckillId}).then(result => {
					if (result.code !== 0) {
						return this.$showToast(result.msg)
					}
					uni.navigateTo({
						url: "/pages/order/purchase/purchase?seckill_id="+this.seckillId
						+"&data=" + encodeURIComponent((JSON.stringify({products})))
					})
				})
			},
			
			/**
			 * 预览图片
			 */
			onPreviewImg() {
				uni.previewImage({
					current: this.currentSwiperIndex - 1,
					urls: this.goodsDetail.banner,
					indicator: 'number',
					loop: true
				})
			}
		}
	}
</script>

<style lang="scss">
	// 轮播部件
	.index-swiper-widget {
		.swiper {
			height: 750rpx;
			max-height: 375px;
			.swiper-slide-images {
				width: 100%;
				height: 100%;
				margin: 0 auto;
				display: block;
			}
		}
		.instructions {
			position: absolute;
			right: 30rpx;
			margin-top: -70rpx;
			padding: 0 8px;
			font-size: 28rpx;
			color: #FFFFFF;
			border-radius: 50rpx;
			background: rgba(0, 0, 0, 0.3);
		}
	}
	
	// 金额部件
	.index-amount-widget {
		display: flex;
		align-items: center;
		height: 100rpx;
		.amount-bg {
			position: relative;
			width:510rpx;
			height:100%;
			z-index: 100;
			.price {
				position: absolute;
				top: 26rpx;
				left: 30rpx;
				z-index: 1000;
			}
		}
		.count-down {
			flex: 1;
			text-align: center;
			height: 100%;
			background: #ffd4d8;
		}
	}
	
	// 产品部件样式
	.index-product-widget {
		padding: 20rpx 0;
		margin-bottom: 20rpx;
		background-color: #FFFFFF;
		.wrap-product__name {
			padding: 0 20rpx;
			font-size: 32rpx;
			font-weight: bold;
			color: #000000;
			display: -webkit-box;
			-webkit-box-orient: vertical; 
			-webkit-line-clamp: 2; 
			overflow: hidden;
		}
		.wrap_product__meta {
			display: flex;
			justify-content: space-between;
			padding: 0 20rpx;
			font-size: 24rpx;
			color: #A7A7A7;
		}
	}
	
	// 规格部件样式
	.index-sku-widget {
		display: flex;
		align-items: center;
		justify-content: space-between;
		width: 100%;
		height: 100rpx;
		margin: 20rpx 0;
		background-color: #FFFFFF;
	}
	
	// 评论部件样式
	.index-comment-widget {
		margin: 20rpx 0;
		padding: 0 20rpx;
		background-color: #FFFFFF;
		.comment-item {
			font-size: 28rpx;
			color: #333333;
			padding: 20rpx 0;
			border-bottom: 1rpx solid rgba(#eee, 0.5);
		}
	}
		
	// 商品详细部件样式
	.index-detail-widget {
		margin-bottom: 20rpx;
		background-color: #FFFFFF;
		.detail-title {
			font-size: 30rpx;
			padding: 30rpx 20rpx;
		}
	}
	
	// 页脚部件样式
	.index-footer-widget {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		height: 100rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		bottom: calc(constant(safe-area-inset-bottom));
		bottom: calc(env(safe-area-inset-bottom)); 
		box-shadow: 0 0 8rpx #EEEEEE;
		background-color: #FFFFFF;
		.icon-group {
			display: flex;
			justify-content: space-around;
			font-size: 22rpx;
			color: #666666;
			.icon-item {
				position: relative;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				width: 100rpx;
				height: 100rpx;
				.cart-num {
					position: absolute;
					top: 5rpx;
					right: 20rpx;
					font-size: 22rpx;
					color: #FFFFFF;
					padding: 0 8rpx;
					border-radius: 50%;
					background-color: #E93323;
				}
			}
		}
		.button-group {
			display: flex;
			width: 420rpx;
			margin: 0 10rpx;
			.button-item {
				flex: 1; 
				width: 50px;
				line-height: 74rpx;
				font-size: 26rpx;
				font-weight: bold;
				color: #FFFFFF;
				text-align: center;
				margin: 0 6rpx;
				border-radius: 40rpx !important;
				&--expect {background-color: #4197F1;}
				&--cart {background-color: #ffa630;}
				&--buy {background-color: #ff2c3c;}
				&--end {background-color: #dad6d6;}
			}
		}
	}
</style>
