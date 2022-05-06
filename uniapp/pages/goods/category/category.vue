<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 搜索部件 -->
		<view class="index-search-widget">
			<view class="search-input" @click="onJump('/pages/goods/search/search')">
				<u-icon name="search" size="28" color="#999"></u-icon>
				<text class="search-input__text">请输入要搜索的商品名</text>
			</view>
		</view>
		
		<!-- 分类部件 -->
		<view class="index-category-widget">
			<!-- “菜单”侧边栏 -->
			<view class="pack-sidebar" v-if="skinType.type != 10 || skinType.skin == 'destiny'">
				<view class="sidebar-item"
					v-for="(item, index) in topCate" :key="index"
					:class="currIdx===index ? 'active' : ''"
					@click="onSwitchCate(index, item.id)"
				>{{item.name}}</view>
			</view>
			<!-- “一级”商品模式 -->
			<view class="destiny-pack-main" v-if="skinType.skin == 'destiny'">
				<mescroll-uni ref="mescrollRef" @init="mescrollInit" :fixed="false" :height="$px2rpx(scrollHeight)" @down="downCallback" @up="upCallback" :down="downOption" :up="upOption">
					<view class="pack-banner" v-if="adList.length > 0">
						<u-swiper :list="adList" height="200"></u-swiper>
					</view>
					<view class="body">
						<view class="item" v-for="(item, index) in subCate" :key="index">
							<u-image class="image" width="200rpx" height="200rpx" 
								border-radius="12" :lazy-load="true" :src="item.image"
								@click="$toPage('/pages/goods/detail/detail?goods_id='+item.id)">
							</u-image>
							<view class="item-right-wrap" style="width:100%;">
								<view class="u-line-2 u-font-28 u-font-weight u-color-normal" 
									@click="$toPage('/pages/goods/detail/detail?goods_id='+item.id)">{{item.name}}</view>
								<view class="u-font-22 u-color-muted" @click="$toPage('/pages/goods/detail/detail?goods_id='+item.id)">原价 ¥{{item.market_price}}</view>
								<view class="u-flex u-row-between">
									<view class="u-font-weight u-color-major" @click="$toPage('/pages/goods/detail/detail?goods_id='+item.id)">
										<text class="u-font-26">￥</text>
										<text class="u-font-34">{{item.min_price}}</text>
									</view>
									<view class="addCart" @click="onPopupSku(item.id)">
										<view><u-icon name="plus" size="26" color="#FFFFFF"></u-icon></view>
										<view class="cartNum" v-if="item.cartNum > 0">{{item.cartNum}}</view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</mescroll-uni>
			</view>
			<!-- “一级”无图模式 -->
			<view class="simple-pack-main" v-if="skinType.skin == 'simple'">
				<view class="pack-banner" v-if="adList.length > 0">
					<u-swiper :list="adList" height="200"></u-swiper>
				</view>
				<view class="body" v-if="topCate.length > 0">
					<view class="item" 
						v-for="(item, index) in topCate" :key="index"
						@click="$toPage('/pages/goods/list/list?cid='+item.id)">
						{{item.name}}
					</view>
				</view>
				<wait-empty tips="暂无分类~" v-if="topCate.length <= 0"></wait-empty>
			</view>
			<!-- “一级”简约模式 -->
			<view class="ideal-pack-main" v-if="skinType.skin == 'ideal'">
				<view class="pack-banner" v-if="adList.length > 0">
					<u-swiper :list="adList" height="200"></u-swiper>
				</view>
				<view class="body" v-if="topCate.length > 0">
					<view class="item" v-for="(item, index) in topCate" :key="index"
						@click="$toPage('/pages/goods/list/list?cid='+item.id)">
						<u-image width="150rpx" height="150rpx" :lazy-load="true" :src="item.image"></u-image>
						<view class="name">{{item.name}}</view>
					</view>
				</view>
				<wait-empty tips="暂无分类~" v-if="topCate.length <= 0"></wait-empty>
			</view>
			
			<!-- “二级”简约模式 -->
			<view class="dream-pack-main" v-if="skinType.skin == 'dream'">
				<view class="pack-banner" v-if="adList.length > 0">
					<u-swiper :list="adList" height="200"></u-swiper>
				</view>
				<view class="body" v-if="subCate.length > 0">
					<view class="item" v-for="(item, index) in subCate" :key="index"
						@click="$toPage('/pages/goods/list/list?cid='+item.id)">
						<u-image width="150rpx" height="150rpx" :lazy-load="true" :src="item.image"></u-image>
						<view class="name">{{item.name}}</view>
					</view>
				</view>
				<wait-empty v-if="subCate.length <= 0"></wait-empty>
			</view>
			<!-- “二级”无图模式 -->
			<view class="clown-pack-main" v-if="skinType.skin == 'clown'">
				<view class="pack-banner" v-if="adList.length > 0">
					<u-swiper :list="adList.length" height="200"></u-swiper>
				</view>
				<view class="body" v-if="subCate.length > 0">
					<view class="item" v-for="(item, index) in subCate" :key="index"
						@click="$toPage('/pages/goods/list/list?cid='+item.id)">
						{{item.name}}
					</view>
				</view>
				<wait-empty tips="暂无分类~" v-if="subCate.length <= 0"></wait-empty>
			</view>
			
			<!-- “三级”简约模式 -->
			<view class="fantasy-pack-main" v-if="skinType.skin == 'fantasy'">
				<view class="pack-banner" v-if="adList.length > 0">
					<u-swiper :list="adList" height="200"></u-swiper>
				</view>
				<view class="pack-main-item" v-for="(item, index) in subCate" :key="index">
					<view class="header">{{item.name}}</view>
					<view class="body">
						<view class="item" v-for="(subItem, i) in item.children" :key="i" 
							@click="$toPage('/pages/goods/list/list?cid='+subItem.id)">
							<u-image width="150rpx" height="150rpx" :lazy-load="true" :src="subItem.image"></u-image>
							<view class="name">{{subItem.name}}</view>
						</view>
					</view>
				</view>
				<wait-empty tips="暂无分类~" v-if="subCate.length <= 0"></wait-empty>
			</view>
			<!-- “三级”无图模式 -->
			<view class="easy-pack-main" v-if="skinType.skin == 'easy'">
				<view class="pack-banner" v-if="adList.length > 0">
					<u-swiper :list="adList" height="200"></u-swiper>
				</view>
				<view class="pack-main-item" v-for="(item, index) in subCate" :key="index">
					<view class="header">{{item.name}}</view>
					<view class="body">
						<view class="item" v-for="(subItem, i) in item.children" :key="i"
							@click="$toPage('/pages/goods/list/list?cid='+subItem.id)">
							{{subItem.name}}
						</view>
					</view>
				</view>
				<wait-empty v-if="subCate.length <= 0"></wait-empty>
			</view>
		
			<!-- Sku组件 -->
			<wait-popup-sku :show="showSku" 
				:type="'all'"
				:goods="goodsDetail" 
				@buynow="onBuy" @addcart="onAddCart" 
				@close="showSku = false">
			</wait-popup-sku>
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
	import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'
	import {mapState, mapMutations} from 'vuex'
	import {toLogin} from '@/utils/tools'
	export default {
		mixins: [MescrollMixin],
		data() {
			return {
				// 首次加载
				isFirstLoading: true,	
				// 滚动高度
				scrollHeight: 0,
				// 轮播广告
				adList: [],
				// 分类页面风格
				skinType: {},
				// 所有分类数据
				allCate: [],
				// 顶级分类数据
				topCate: [],
				// 子级分类数据
				subCate: [],
				// 当前分类ID
				currCid: 0,
				// 当前分类下标
				currIdx: 0,
				// 下拉加载配置
				downOption: {
					use: true,
					auto: false
				},
				// 上拉加载配置
				upOption: {
					noMoreSize: 1,
					page: {size: 15},
					textNoMore: '没有更多了~',
					empty: {
						icon: '/static/empty/empty_goods.png',
						tip: '暂无商品'
					}
				},
				// 显示规格选择
				showSku: false,
				// 当前点击商品
				goodsDetail: {}
			}
		},
		onShow() {
			this.getAd()
			this.getCategorySkin()
			if (this.allCate.length <= 0) {
				this.getCategoryList()
			}
		},
		computed: {
			...mapState(['isLogin'])
		},
		mounted() {
			this.resize()
		},

		methods: {
			/**
			 * 滚动高度
			 */
			resize(){
				const that = this
				const query = uni.createSelectorQuery().in(this);
				setTimeout(() => {
					query.select('.index-category-widget').boundingClientRect((pos)=>{
						that.scrollHeight = pos.height - 1;	
					}).exec()
				}, 0)
			},

			/**
			 * 获取商品
			 */
			upCallback(page) {
				if (this.skinType.skin !== 'destiny') return	
				this.$u.api.apiCategoryGoods({
					cid: this.currCid,
					page: page.num
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					const currCid = result.data.current_cid
					
					if (this.currCid !== currCid && this.isFirstLoading === false) return
					if (page.num == 1) this.subCate = []
				
					this.subCate = this.subCate.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},

			/**
			 * 获取广告
			 */
			getAd() {
				const param = {position: 10}
				this.$u.api.apiAd(param).then(result => {
					if (result.code === 0) {
						this.adList = result.data
					} else {
						this.$showToast('loading ad error')
					}
				})
			},
			
			/**
			 * 获取风格
			 */
			getCategorySkin() {
				this.$u.api.apiCategorySkin().then(result => {
					if (result.code === 0) {
						this.skinType = result.data
					} else {
						this.$showToast('loading skin error')
					}
				})
			},
			
			/**
			 * 获取分类
			 */
			getCategoryList() {
				this.$u.api.apiCategoryList().then(result => {
					if (result.code === 0) {
						this.currCid = result.data.length ? result.data[0].id : 0
						this.allCate = result.data

						let topCate = []
						this.allCate.forEach(function(item){
							if (item.pid == 0) { topCate.push(item) }
						})
						this.topCate = topCate
						
						if (this.skinType.skin !== 'destiny') {
							this.filterSubCate(this.currCid)
							this.$nextTick(() => {
								this.isFirstLoading = false
							})
						}
					} else {
						this.$showToast('loading category error')
					}
				})
			},

			/**
			 * 过滤分类
			 */
			filterSubCate(cid) {
				const that = this
				try {
					that.allCate.forEach(function(item){
						if (item.id === cid) {
							that.subCate = item.children || []
							throw new Error("EndIterative")
						}
					})
				} catch (e) {
					if(e.message!="EndIterative") throw e
				}
			},
			
			/**
			 * 切换分类
			 */
			onSwitchCate(index, cid) {
				this.currIdx = index
				this.currCid = cid
				if (this.skinType.skin === 'destiny') {
					this.subCate = []
					this.mescroll.scrollTo(0, 0)
					this.mescroll.resetUpScroll()
					this.upCallback({num: 1})
				} else {
					this.filterSubCate(cid)
				}
			},
			
			/**
			 * 弹出规格
			 */
			onPopupSku(id) {
				if (!this.isLogin) return toLogin()
				const param = {id: id}
				this.$u.api.apiGoodsDetail(param).then(result => {
					this.goodsDetail = result.data
					this.showSku = true
				})
			},
			
			/**
			 * 加入购物车
			 */
			onAddCart(e) {
				if (!this.isLogin) return toLogin()
				let {id, goodsNum} = e.detail
				let param = {goods_id: this.goodsDetail.id, sku_id: id, num: goodsNum}
				this.$u.api.apiCartAdd(param).then((result) => {
					if (result.code === 0) {
						this.showSku = false
						this.getCategoryGoods()
						this.$showSuccess(result.msg)
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
				let products = [{goods_id: this.goodsDetail.id, sku_id: id, num: goodsNum}]
				uni.navigateTo({
					url: "/pages/order/purchase/purchase?data=" + encodeURIComponent((
						JSON.stringify({products})
					))
				})
			}
		}
	}
</script>

<style lang="scss">
	// “搜索”组件样式
	.index-search-widget {
		padding: 20rpx;
		background: #FFFFFF;
		.search-input {
			height: 70rpx;
			line-height: 70rpx;
			overflow: hidden;
			padding: 0 20rpx;
			border-radius: 60rpx;
			background: #F8F8F8;
			.search-input__text {
				margin: 0 10rpx;
				font-size: 26rpx;
				color: #999999;
			}
		}
	}

	// “分类”组件样式
	.index-category-widget {
		flex-shrink: 0;
		display: flex;
		position: absolute;
		left: 0;
		right: 0;
		bottom: 0;
		top: 110rpx;
		overflow: hidden;
		// “广告”轮播样式
		.pack-banner {
			margin: 0 20rpx;
			margin-top: 20rpx;
		}
		// “侧边”菜单样式
		.pack-sidebar {
			flex-shrink: 0;
			width: 180rpx;
			height: 100%;
			overflow: hidden;
			overflow-y: auto;
			box-sizing: border-box;
			background-color: #FFFFFF;
			.sidebar-item {position: relative; height: 100rpx; text-align: center; line-height: 100rpx; font-size: 28rpx; color: #000000;}
			.sidebar-item.active {color: #E93323; font-weight: bold; background-color: #FFFFFF;}
			.sidebar-item.active::before {content: ""; width: 16rpx; height: 36rpx; position: absolute; left: -10rpx; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); -webkit-border-radius: 20%; border-radius: 20%; background: #E93323 !important;}
		}
		// “一级”商品模式
		.destiny-pack-main {
			position: absolute;
			top: 0;
			left: 180rpx;
			right: 0;
			bottom: 0;
			box-sizing: border-box;
			.body { margin: 20rpx; margin-bottom: 0;}
			.body .item {&:last-child{margin-bottom: 0;}}
			.body .item .item-right-wrap {display: flex;flex-direction: column; justify-content: space-between; height: 200rpx; padding: 14rpx}
			.body .item {display: flex; margin-bottom: 20rpx; border-radius: 12rpx; background-color: #FFFFFF;}
			.body .item .image {flex-shrink: 0;}
			.body .item .addCart {position: relative; width: 42rpx; height: 42rpx; line-height: 42rpx; text-align: center; margin-right: 10rpx; border-radius: 50%; background-color: #FF2C3C;}
			.body .item .addCart .cartNum {position: absolute; top: -16rpx; right: -8rpx; line-height: 28rpx; font-size: 20rpx; color: #FFFFFF;border-radius: 50%; padding: 0 8rpx; background-color: #ffc705;}
		}
		// “一级”无图模式
		.simple-pack-main {
			width: 100%;
			overflow: hidden;
			overflow-y: auto;
			box-sizing: border-box;
			.body {display: flex; justify-content: space-between; flex-wrap: wrap; margin: 20rpx;}
			.body:after {content: ''; width: 30%;}
			.body .item {color: #333333; text-align: center; font-size: 24rpx; width: 30%; margin-bottom: 26rpx; padding: 20rpx 0; border-radius: 6rpx; background-color: #FFFFFF;}
		}
		// “一级”简约模式
		.ideal-pack-main {
			width: 100%;
			overflow: hidden;
			overflow-y: auto;
			box-sizing: border-box;
			.body {display: flex; flex-wrap: wrap; justify-content: space-between; margin: 20rpx;}
			.body:after {content: ''; width: 25%; flex: auto;}
			.body .item {display: flex; align-items: center; flex-direction: column; flex-shrink: 0; width: 25%; margin-bottom: 30rpx;}
			.body .item .name {font-size: 24rpx; color: #333333; padding-top: 10rpx;}
		}
		// “二级”梦想模式
		.dream-pack-main {
			width: 100%;
			overflow: hidden;
			overflow-y: auto;
			box-sizing: border-box;
			.body {display: flex; flex-wrap: wrap; margin: 20rpx; padding-top: 20rpx; border-radius: 12rpx; background-color: #FFFFFF;}
			.body .item {display: flex; align-items: center; flex-direction: column; flex-shrink: 0; width: 33.33%; margin-bottom: 30rpx;}
			.body .item .name {font-size: 24rpx; color: #333333; padding-top: 10rpx;}
		}
		// “二级”小丑模式
		.clown-pack-main {
			width: 100%;
			overflow: hidden;
			overflow-y: auto;
			box-sizing: border-box;
			.body {display: flex; justify-content: space-between; flex-wrap: wrap; margin: 20rpx;}
			.body:after {content: ''; width: 30%;}
			.body .item {color: #333333; text-align: center; font-size: 24rpx; width: 30%; margin-bottom: 26rpx; padding: 20rpx 0; border-radius: 6rpx; background-color: #FFFFFF;}
		}
		// “三级”玄幻模式
		.fantasy-pack-main {
			width: 100%;
			overflow: hidden;
			overflow-y: auto;
			box-sizing: border-box;
			.pack-main-item {
				margin: 20rpx;
				border-radius: 12rpx;
				background-color: #FFFFFF;
				.header { font-size: 26rpx; font-weight: bold; color: #333333; height: 80rpx; line-height: 80rpx; padding: 0 20rpx; border-radius: 12rpx; background: #FFFFFF;}
				.body {display: flex; flex-wrap: wrap;}
				.body .item {display: flex; align-items: center; flex-direction: column; flex-shrink: 0; width: 33.33%; margin-bottom: 30rpx;}
				.body .item .name {font-size: 24rpx; color: #333333; padding-top: 10rpx;}
			}
		}
		// “三级”简易模式
		.easy-pack-main {
			width: 100%;
			overflow: hidden;
			overflow-y: auto;
			box-sizing: border-box;
			.pack-main-item {
				margin: 20rpx;
				border-radius: 12rpx;
				.header { font-size: 26rpx; font-weight: bold; color: #333333; height: 80rpx; line-height: 80rpx; padding: 0 20rpx;}
				.body {display: flex; justify-content: space-between; flex-wrap: wrap;}
				.body:after {content: ''; width: 30%;}
				.body .item {color: #333333; text-align: center; font-size: 24rpx; width: 30%; margin-bottom: 26rpx; padding: 20rpx 0; border-radius: 6rpx; background-color: #FFFFFF;}
			}
		}
	}
</style>
