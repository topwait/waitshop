<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 头部筛选部件 -->
		<view class="index-header-widget">
			<view class="pack-search-widget">
				<view class="search-input" @click="$toPage('/pages/goods/search/search')">
					<u-icon name="search" size="28" color="#999"></u-icon>
					<text class="u-margin-lr-10 u-font-26 u-color-placeholder">{{keyword ? keyword : '请输入要搜索的商品名'}}</text>
				</view>
				<view class="u-margin-right-30">
					<u-icon v-if="layout == 'row'" custom-prefix="custom-icon" name="row" size="42" color="#999" @click="onSwitchLayout"></u-icon>
					<u-icon v-if="layout == 'col'" custom-prefix="custom-icon" name="col" size="42" color="#999" @click="onSwitchLayout"></u-icon>
				</view>
			</view>
			<view class="pack-filter-widget">
				<view class="filter-item" v-for="(item, index) in tabList" :key="index" @click="onSwitchTab(index)">
					<text :class="item.selected ? 'active' : ''">{{item.name}}</text>
					<image v-if="item.image" class="icon" :src="'/static/'+item.image[item.type]"></image>
				</view>
			</view>
		</view>
		
		<!-- 商品列表部件 -->
		<mescroll-body ref="mescrollRef" :top="186" @init="mescrollInit" 
			@up="upCallback" @down="downCallback" :up="upOption">	
			<wait-goods-list :layout="layout" :list="goodsList"></wait-goods-list>
		</mescroll-body>

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
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins"
	import MescrollBody from "@/components/mescroll-uni/mescroll-body"
	export default {
		mixins: [MescrollMixin],
		components: {MescrollBody},
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 布局方式[row, col]
				layout: 'row',
				// 选项卡下标
				tabIndex: 0,
				// 选项卡列表
				tabList: [
					{
						"name": "综合",
						"alias": "all",
						"type": 0,
						"selected": true
					},
					{
						"name": "销量",
						"alias": "sales",
						"type": 0,
						"selected": false,
						"image": ['ic_sort_horn.png', 'ic_sort_up.png', 'ic_sort_down.png']
					},
					{
						"name": "价格",
						"alias": "price",
						"type": 0,
						"selected": false,
						"image": ['ic_sort_horn.png', 'ic_sort_up.png', 'ic_sort_down.png']
					},
					{
						"name": "新品",
						"alias": "news",
						"type": 0,
						"selected": false,
					}
				],
				// 当前分类ID
				cid: 0,
				// 搜素关键词
				keyword: '',
				// 商品列表数据
				goodsList: [],
				// 上拉加载配置
				upOption: {
					noMoreSize: 1,
					page: {size: 20},
					empty: {
						icon: '/static/empty/empty_goods.png',
						tip: '暂无商品'
					}
				}
			}
		},
		onLoad(options) {
			this.cid = parseInt(options.cid || 0)
			this.keyword = options.keyword ? options.keyword : ''
		},
		methods: {
			/**
			 * 上拉加载
			 */
			upCallback(page) {
				let tab = this.tabList[this.tabIndex]
				this.$u.api.apiGoodsList({
					cid:     this.cid,
					filter:  tab.alias,
					sort:    tab.type === 0 || tab.type === 2 ? "desc" : "asc",
					keyword: this.keyword,
					page:    page.num
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total

					if (page.num == 1) this.goodsList = []
					this.goodsList = this.goodsList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},

			/**
			 * 筛选商品
			 */
			onSwitchTab(index) {
				let tab = this.tabList[index]
				this.tabIndex = index
				if (tab.image) {
					this.tabList.forEach(function(item, i) {
						if (i !== index) {
							item.type = 0
							item.selected = false
						}
					})
					let type = 0
					if (tab.type === 0) type = 2
					if (tab.type === 1) type = 2
					if (tab.type === 2) type = 1
					this.tabList[index].type = type
					this.tabList[index].selected = true
				} else {
					this.tabList.forEach(function(item, i) {
						item.type = 0
						item.selected = false
					})
					this.tabList[index].selected = true
				}

				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			},
			
			/**
			 * 切换布局
			 */
			onSwitchLayout() {
				let layout = this.layout === 'row' ? 'col' : 'row'
				this.layout = layout
			}
		}
	}
</script>

<style lang="scss">
	// 头部部件样式
	.index-header-widget {
		position: fixed;
		left: 0;
		right: 0;
		z-index: 9999;
		background: #FFFFFF;
		// 搜索
		.pack-search-widget {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 20rpx 0;
			.search-input {
				flex: 1;
				height: 70rpx;
				line-height: 70rpx;
				padding: 0 20rpx;
				margin: 0 20rpx;
				border-radius: 60rpx;
				background: #F4F4F4;
			}
		}
		// 筛选
		.pack-filter-widget {
			display: flex;
			align-items: center;
			justify-content: space-between;
			width: 100%;
			padding: 20rpx 0;
			font-size: 28rpx;
			text-align: center;
			border-top: 1px solid #EEEEEE;
			border-bottom: 1px solid #EEEEEE;
			.filter-item {flex: 1; max-width: 100%; font-size: 28rpx;}
			.filter-item .active {color: #e93323;}
			.filter-item .icon {width: 14rpx; height: 22rpx; margin-left: 12rpx;}
		}
	}
	
	// 商品部件样式
	// .index-goods-widget {
	// 	overflow: hidden;
	// 	position: absolute;
	// 	top: 186rpx;
	// 	left: 0;
	// 	right: 0;
	// 	bottom: 0;
	// 	overflow-y: auto;
	// }
</style>
