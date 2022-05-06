<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 搜索框部件 -->
		<view class="index-search-widget">
			<view class="search-input">
				<u-icon name="search" size="28" color="#999"></u-icon>
				<input class="search-input__text" type="text" 
					placeholder="请输入会员昵称/手机号"
					v-model="keyword" :trim="true" 
					@confirm="onSearch" />
			</view>
		</view>
		
		<!-- 选项卡部件 -->
		<view class="index-tabs-widget">
			<u-tabs
				:list="tabList" :is-scroll="false" :current="tabIndex"
				active-color="#FF5058" duration="0" @change="onSwitchTab"
			></u-tabs>
		</view>

		<!-- 筛选部件 -->
		<view class="index-filter-widget">
			<view class="filter-item" v-for="(item, index) in filterList" :key="index" 
				@click="onSwitchSort(index)">
				<text :class="item.selected ? 'active' : ''">{{item.name}}</text>
				<image v-if="item.image" class="icon" :src="'/static/'+item.image[item.type]"></image>
			</view>
		</view>
		
		<!-- 推广粉丝部件 -->
		<mescroll-body :top="270" ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">
			<view class="index-fans-widget u-margin-tb-20 u-padding-lr-20 u-bg-white">
				<view class="fans-item u-flex u-row-between u-padding-tb-20 u-border-bottom" 
					v-for="(item, index) in fansList" :key="index">
					<view class="u-flex">
						<u-image :src="item.avatar" width="100rpx" height="100rpx" border-radius="50%"></u-image>
						<view class="u-margin-left-14">
							<view class="u-font-32 u-margin-bottom-20">{{item.nickname}}</view>
							<view class="u-color-lighter">
								<text class="u-margin-right-46">{{item.mobile}}</text>
								<!-- <text>{{item.create_time}}</text> -->
							</view>
						</view>
					</view>
					<view>
						<view>{{item.fans_num || 0}} 人</view>
						<view>{{item.distribution_order_num || 0}} 单</view>
						<view>{{item.distribution_order_money || 0}} 元</view>
					</view>
				</view>
			</view>
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
				// 搜索关键词
				keyword: '',
				// 当前选项卡下标
				tabIndex: 0,
				// 选项卡数据列表
				tabList: [
					{name: '全部粉丝', count: 0, alias: "all"},
					{name: '一级粉丝', count: 0, alias: "first"}, 
					{name: '二级粉丝', count: 0, alias: "second"}
				],
				// 当前筛选列表下标
				filterIndex: 0,
				// 筛选列表
				filterList: [
					{
						"name": "团队排序",
						"alias": "all",
						"type": 1,
						"selected": true,
						"image": ['ic_sort_horn.png', 'ic_sort_up.png', 'ic_sort_down.png']
					},
					{
						"name": "金额排序",
						"alias": "sales",
						"type": 0,
						"selected": false,
						"image": ['ic_sort_horn.png', 'ic_sort_up.png', 'ic_sort_down.png']
					},
					{
						"name": "订单排序",
						"alias": "price",
						"type": 0,
						"selected": false,
						"image": ['ic_sort_horn.png', 'ic_sort_up.png', 'ic_sort_down.png']
					}
				],
				// 粉丝列表
				fansList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 1,
					empty: {
						icon: '/static/empty/empty_null.png',
						tip: '暂无数据'
					}
				}
			}
		},
		methods: {
			/**
			 * 获取粉丝数据
			 */
			upCallback(page) {
				let filter = this.filterList[this.filterIndex]
				this.$u.api.apiDistributionFans({
					type: this.tabList[this.tabIndex].alias,
					filter: filter.alias,
					sort: filter.type ? "desc" : "asc",
					keyword: this.keyword,
					page: page.num
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					if (page.num == 1) this.fansList = []

					this.fansList = this.fansList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},
			
			/**
			 * 确认搜索
			 */
			onSearch() {
				this.fansList = []
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			},
			
			/**
			 * 切换选项
			 */
			onSwitchTab(index) {
				this.tabIndex = index
				this.fansList = []
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			},
			
			/**
			 * 筛选排序
			 */
			onSwitchSort(index) {
				let tab = this.filterList[index]
				this.filterIndex = index
				if (tab.image) {
					this.filterList.forEach(function(item, i) {
						if (i !== index) {
							item.type = 0
							item.selected = false
						}
					})
					let type = 0
					if (tab.type === 0) type = 2
					if (tab.type === 1) type = 2
					if (tab.type === 2) type = 1
					this.filterList[index].type = type
					this.filterList[index].selected = true
				} else {
					this.filterList.forEach(function(item, i) {
						item.type = 0
						item.selected = false
					})
					this.filterList[index].selected = true
				}
			
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			}
		}
	}
</script>

<style lang="scss">
	// 搜索框部件样式
	.index-search-widget {
		position: fixed;
		z-index: 2000;
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 20rpx;
		background: #FFFFFF;
		.search-input {
			flex: 1;
			display: flex;
			padding: 0 20rpx;
			border-radius: 60rpx;
			background: #F4F4F4;
			.search-input__text {
				flex: 1;
				height: 70rpx;
				line-height: 70rpx;
				margin: 0 10rpx;
				font-size: 26rpx;
				color: #999;
				padding-left: 10rpx;
				background-color: #F4F4F4;
			}
		}
		.confirm-search {
			margin-left: 20rpx;
			font-size: 28rpx;
			color: #333;
		}
	}
	
	// 选项卡部件样式
	.index-tabs-widget {
		position: fixed;
		top: 110rpx;
		width: 100%;
		z-index: 2000;
	}
	
	// 筛选部件样式
	.index-filter-widget {
		position: fixed;
		top: 190rpx;
		z-index: 2000;
		display: flex;
		align-items: center;
		justify-content: space-between;
		width: 100%;
		padding: 20rpx 0;
		font-size: 28rpx;
		text-align: center;
		border-top: 1rpx solid rgba($color: #F2F2F2, $alpha: 0.6);
		background-color: #FFFFFF;
		.filter-item {flex: 1; max-width: 100%; font-size: 28rpx;}
		.filter-item .active {color: #e93323;}
		.filter-item .icon {width: 14rpx; height: 22rpx; margin-left: 12rpx;}
	}
</style>
