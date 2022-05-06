<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 搜索框部件 -->
		<view class="index-search-widget">
			<view class="search-input">
				<u-icon name="search" size="28" color="#999"></u-icon>
				<input class="search-input__text" type="text" 
					placeholder="请输入要搜索的商品名"
					v-model="keyword"  @blur="onInputBlur" :trim="true" />
			</view>
		</view>
		
		<!-- “热门”关键词组件 -->
		<view class="index-keywords-widget">
			<view class="u-flex u-row-between u-margin-bottom-30">
				<view class="u-font-28 u-font-weight u-color-normal">热门搜索</view>
				<view></view>
			</view>
			<view class="u-flex u-flex-wrap">
				<view class="keywords-main-item" 
					v-for="(item, index) in hotSearch" :key="index"
					@click="onChoiceSearch(item.keyword)"
				>{{item.keyword}}</view>
			</view>
		</view>
		
		<!-- “历史”关键词组件 -->
		<view class="index-keywords-widget" v-if="historyLists.length > 0">
			<view class="u-flex u-row-between u-margin-bottom-30">
				<view class="u-font-28 u-font-weight u-color-normal">历史搜索</view>
				<u-icon name="trash" size="34" color="#999" @click="onClearHistory()"></u-icon>
			</view>
			<view class="u-flex u-flex-wrap">
				<view class="keywords-main-item" 
					v-for="(item, index) in historyLists" :key="index"
					@click="onChoiceSearch(item.name)"
				>{{item.name}}</view>
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
	import {mapState} from 'vuex'
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 搜索关键词
				keyword: '',
				// 热门搜索数据
				hotSearch: []
			}
		},
		onLoad() {
			this.getHotSearch()
		},
		computed: {
			...mapState(['historyLists'])
		},
		methods: {
			/**
			 * 获取热门搜索
			 */
			getHotSearch() {
				this.$u.api.apiHotSearch().then(result => {
					if (result.code === 0) {
						this.hotSearch = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading hotSearch error')
					}
				})
			},
			
			/**
			 * 立即搜索
			 */
			onInputBlur(e) {
				const that = this
				const {value} = e.detail
				if (value !== '') {
					uni.navigateTo({
						url: "/pages/goods/list/list?cid=0&keyword="+value,
						success() {
							setTimeout(() => {
								that.$store.dispatch('setHistory', {name: value})
							}, 600)
						}
					})
				}
			},
			
			/**
			 * 选择搜索
			 */
			onChoiceSearch(keyword) {
				const that = this
				uni.navigateTo({
					url: "/pages/goods/list/list?cid=0&keyword="+keyword,
					success() {
						setTimeout(() => {
							that.$store.dispatch('setHistory', {name: keyword})
						}, 600)
					}
				})
			},
			
			/**
			 * 清空历史
			 */
			onClearHistory() {
				const that = this
				uni.showModal({
					title: '提示',
					content: '您确认要清空历史搜索吗?',
					cancelText: '再想想',
					confirmText: '我确定',
					confirmColor: '#FF5058',
					success: function() {
						that.$store.dispatch('setHistory', false)
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	// 搜索框部件样式
	.index-search-widget {
		padding: 20rpx;
		background: #FFFFFF;
		.search-input {
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
	}
	
	// 关键词部件
	.index-keywords-widget {
		padding: 20rpx;
		background-color: #FFFFFF;
		.keywords-main-item {
			display: flex;
			align-items: center;
			justify-content: center;
			min-width: 150rpx;
			font-size: 26rpx;
			color: #333333;
			margin-right: 20rpx;
			margin-bottom: 20rpx;
			padding: 10rpx 20rpx;
			box-sizing: border-box;
			border-radius: 100rpx;
			background-color: #F4F4F4;
		}
	}
</style>
