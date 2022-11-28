<template>
	<view class="container">

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
				@change="changeSwiperCurrent">
				<swiper-item v-for="(item, index) in goodsDetail.banner" :key="index" @tap="onPreviewImg(index)">
					<view class="swiper-slide-images" v-if="item.type === 'video'">
						<video id="myVideo" class="u-equal-bfb"
							:src="item.url"
							:muted="false" 
							:show-center-play-btn="false" 
							:show-progress="true" 
							:enable-progress-gesture="false" 
							:show-mute-btn="false"
							:controls="showControls"
							:show-fullscreen-btn="showControls" 
							:show-play-btn="showControls" 
							@click="play" 
							@ended="playEnd"
							@fullscreenchange="fullscreenchange"
						></video>
						<image v-show="!startPlay" @tap.stop="play" class="ic_play" src="/static/ic_so_play.png"></image>
					</view>
					<image v-else class="swiper-slide-images" :src="item.url"></image>
				</swiper-item>
			</swiper>
			<view class="instructions">
				<text>{{ (currentSwiperIndex || 0) + 1 }}</text>
				<text>/{{ bannerSwiperNumber || 0 }}</text>
			</view>
		</view>

		<!-- 产品组件 -->
		<view class="index-product-widget">
			<view class="wrap-product__amount">
				<view class="u-flex">
					<wait-price :amount="goodsChecked.sell_price || goodsDetail.min_price" :mainSize="'40rpx'" :fontWeight="'bold'" class="u-margin-right-16"></wait-price>
					<wait-price :amount="goodsChecked.market_price || goodsDetail.market_price" :mainSize="'26rpx'" :fontColor="'#999'" :lineThrough="true"></wait-price>
				</view>
				<image src="/static/ic_share.png" style="width:132rpx; height:60rpx;" @click="onShare"></image>
			</view>
			<view class="wrap-product__name">{{ goodsDetail.name }}</view>
			<view class="wrap_product__meta">
				<text>库存: {{ goodsChecked.stock || 0 }}件</text>
				<text>销量: {{ goodsDetail.sales_volume || 0}}件</text>
				<text>浏览量: {{ goodsDetail.click_count || 0}}次</text>
			</view>
		</view>

		<!-- 规格部件 -->
		<view class="index-sku-widget" @click="onPopupSku('all')">
			<view class="u-font-md u-padding-lr-20 u-color-lighter">已选</view>
			<view class="u-flex-1 u-line-1 u-padding-lr-10">
				<view class="u-font-md u-color-normal">{{ goodsChecked.spec_value_str || "默认" }}</view>
			</view>
			<u-icon name="arrow-right" size="28" color="#999999" style="padding:0 20rpx;"></u-icon>
		</view>

		<!-- 评论部件 -->
		<view class="index-comment-widget" v-if="goodsDetail.comment && goodsDetail.comment.count > 0">
			<view class="u-flex u-row-between u-padding-tb-20 u-border-bottom">
				<view>商品评价({{ goodsDetail.comment.count || 0}})</view>
				<view>
					<text class="u-margin-right-10 u-color-lighter" 
						@click="$toPage('/pages/goods/comment/comment?id='+goodsDetail.id)">查看全部</text>
					<u-icon name="arrow-right" size="28" color="#999999"></u-icon>
				</view>
			</view>
			<view>
				<view class="comment-item" v-for="(item, index) in goodsDetail.comment.list" :key="index">
					<view class="u-flex">
						<u-image width="60rpx" height="60rpx" border-radius="50%" :lazy-load="true" :src="item.avatar"></u-image>
						<text class="u-font-sm u-margin-left-10">{{ item.nickname }}</text>
					</view>
					<view class="u-margin-top-10">
						{{ item.content }}
					</view>
				</view>
			</view>
		</view>

		<!-- 商品详细部件 -->
		<view class="index-detail-widget" v-if="goodsDetail.content">
			<view class="detail-title">商品详情</view>
			<u-parse :html="goodsDetail.content"></u-parse>
		</view>

		<!-- 页脚购买部件 -->
		<view style="height: 100rpx;"></view>
		<view class="index-footer-widget">
			<view class="icon-group">
				<navigator class="icon-item" hover-class="none" 
					@click="$toPage('/bundle/pages/customer_service/customer_service')">
					<u-icon name="kefu-ermai" size="42" color="#666"></u-icon>
					<text>客服</text>
				</navigator>
				<view class="icon-item" @click="onCollect">
					<u-icon name="star" size="42" color="#666" v-if="!goodsDetail.is_collect"></u-icon>
					<u-icon name="star-fill" size="42" color="red" v-else></u-icon>
					<text>收藏</text>
				</view>
				<view class="icon-item" @click="$toPage('/pages/cart/cart?type=tab')">
					<u-icon name="shopping-cart" size="42" color="#666" ></u-icon>
					<text>购物车</text>
					<text class="cart-num">{{cartNum}}</text>
				</view>
			</view>
			<view class="button-group">
				<view class="button-item button-item--cart" @click="onPopupSku('cart')">加入购物车</view>
				<view class="button-item button-item--buy" @click="onPopupSku('buy')">立即购买</view>
			</view>
		</view>

		<!-- Sku组件 -->
		<wait-popup-sku :show="showSku" :type="typeSku" :goods="goodsDetail" 
			@change="onChoiceSku" @close="showSku = false"
			@buynow="onBuy" @addcart="onAddCart">
		</wait-popup-sku>

		<!-- 分享组件 -->
		<wait-popup-share shareType="goods" :show="showShare" 
			:tid="goodsDetail.id"  @close="showShare = false">
		</wait-popup-share>

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
	import {mapState, mapMutation} from 'vuex'
	import {toLogin} from '@/utils/tools'
	export default {
		data() {
			return {
				startPlay: false,	  // 视频播放
				showControls: false,  // 视频控制器
				
				autoplay: true, 	   // 是否自动切换
				duration: 800, 		   // 滑动动画时长
				circular: true,		   // 到尾重头开始
				interval: 5000, 	   // 自动切换时间间隔
				indicatorDots: false,  // 是否显示面板指示点
				currentSwiperIndex: 0, // 轮播图指针
				bannerSwiperNumber: 0, // 轮播图总数量
				
				// 首次加载
				isFirstLoading: true,
				// 当前商品ID
				goodsId: null,
				// 商品详细数据
				goodsDetail: {},
				// 选中的商品规格
				goodsChecked: {}, 
				// 是否显示SKU窗口
				showSku: false,
				// SKU打开类型: all=全部, buy=下单,cart=加入购物车
				typeSku: 'buy',
				// 购物车产品数
				cartNum: 0,
				// 是否显示分享窗口
				showShare: false,
			}
		},
		onLoad(options) {
			this.goodsId = parseInt(options.goods_id || 1)
			this.getGoodsDetail()
			this.getCartNum()
		},
		onReady: function (res) {
			this.videoContext = uni.createVideoContext('myVideo')
		},
		computed: {
			...mapState(['isLogin'])
		},
		methods: {
			/**
			 * 改变轮播图指针
			 */
			changeSwiperCurrent(e) {
			    this.currentSwiperIndex = e.detail.current
				this.autoplay = true
				this.startPlay = false
				this.videoContext.stop()
			},
			
			/**
			 * 获取商品详细
			 */
			getGoodsDetail() {
				const param = {id: this.goodsId}
				this.$u.api.apiGoodsDetail(param).then(result => {
					if (result.code === 0) {
						let data = result.data
						let banner = []
						if (data.video) { 
							banner.push({type: 'video', url: data.video}) 
							this.autoplay = false
						}
						data.banner.forEach(item => { banner.push({type: 'image', url: item}) })
						data.banner = banner
						this.goodsDetail = data
						this.bannerSwiperNumber = banner.length
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading goods error')
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
						this.$showToast('loading cartNum error')
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
			 * 弹出分享
			 */
			onShare() {
				if (!this.isLogin) return toLogin()
				this.showShare = true
			},
			
			/**
			 * 弹出SKU
			 */
			onPopupSku(type) {
				if (!this.isLogin) return toLogin()
				this.typeSku = type
				this.showSku = true
			},
			
			/**
			 * 选择SKU
			 */
			onChoiceSku(value) {
				this.goodsChecked = value.checked
			},

			/**
			 * 加入购物车
			 */
			onAddCart(e) {
				if (!this.isLogin) return toLogin()
				let {id, goodsNum} = e.detail
				let param = {goods_id: this.goodsId, sku_id: id, num: goodsNum}
				this.$u.api.apiCartAdd(param).then((result) => {
					if (result.code === 0) {
						this.showSku = false
						this.getCartNum()
						setTimeout(() => {
							this.$showSuccess(result.msg)
						}, 500)
					} else {
						this.$showToast(result.msg)
					}
				})
			},
			
			/**
			 * 去下单
			 */
			onBuy(e) {
				if (!this.isLogin) return toLogin()
				let {id, goodsNum} = e.detail
				let products = [{goods_id: this.goodsId, sku_id: id, num: goodsNum}]
				
				const param = {products: products}
				this.$u.api.apiOrderSettle(param).then(result => {
					if (!result.data.pass || !result.data.status) {
						let errorTips = ''
						result.data.pStatusArray.forEach((item) => {
							if (item.errorTips !== false) {
								errorTips = item.errorTips
							}
						})
						return this.$showToast(errorTips)
					} else {
						const url = '/pages/order/purchase/purchase?data='
						uni.navigateTo({url: url +  encodeURIComponent((JSON.stringify({products}))) })
					}
				})
				this.showSku = false
			},

			/**
			 * 预览图片
			 */
			onPreviewImg(index) {
				let images = []
				let banner =  this.goodsDetail.banner
				banner.forEach(item => { if (item.type !== 'video'){images.push(item.url)} })
				if (index === 0 && banner[index].type == "video") {
					return false
				}

				uni.previewImage({
					current: index,
					urls: images,
					indicator: 'default'
				})
			},
			
			/**
			 * 开始播放视频
			 */
			play(e) {
				if (!this.startPlay) {
					this.autoplay = false
					this.startPlay = true
					this.videoContext.play()
				} else {
					this.startPlay = false
					this.videoContext.stop()
				}
			},
			
			/**
			 * 视频播放结束
			 */
			playEnd() {
				this.autoplay  = true
				this.startPlay = false
				this.videoContext.stop()
			},
			
			/**
			 * 当视频进入和退出全屏时触发
			 */
			fullscreenchange(e) {
				const {fullScreen} = e.detail
				!fullScreen && this.videoContext.stop()
				this.showControls = fullScreen ? true : false
				this.startPlay = fullScreen ? false : true
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
				position: relative;
				width: 100%;
				height: 100%;
				margin: 0 auto;
				display: block;
				.ic_play {
					width: 90rpx;
					height: 90rpx;
					position: absolute;
					top: 50%;
					left: 50%;
					z-index: 999;
					transform: translate(-50%, -50%);
				}
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
	
	// 产品部件样式
	.index-product-widget {
		padding: 20rpx 0;
		margin-bottom: 20rpx;
		background-color: #FFFFFF;
		.wrap-product__amount {
			display: flex;
			justify-content: space-between;
			height: 60rpx;
			line-height: 60rpx;
			padding-left: 20rpx;
		}
		.wrap-product__name {
			flex: 1;
			margin: 16rpx 20rpx;
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
				width: 50px;
				line-height: 80rpx;
				font-size: 26rpx;
				font-weight: bold;
				color: #FFFFFF;
				text-align: center;
				&.alone {border-radius: 40rpx !important;}
				&--cart {flex: 1; border-top-left-radius: 40rpx; border-bottom-left-radius: 40rpx; background-color: #ffa630;}
				&--buy {flex: 1; border-top-right-radius: 40rpx; border-bottom-right-radius: 40rpx; background-color: #ff2c3c;}
			}
		}
	}
</style>
