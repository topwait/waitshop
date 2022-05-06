<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 收藏部件 -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :down="downOption" :up="upOption">
			<view class="index-collect-wdiget">
				<view class="collect-item" v-for="(item, index) in collectList" :key="index" 
					@click="$toPage('/pages/goods/detail/detail?id='+item.id)">
					<u-image width="160rpx" height="160rpx" :lazy-load="true" :src="item.image"></u-image>
					<view class="product-info">
						<view class="u-font-md u-color-normal u-line-2">{{item.name}}</view>
						<view class="u-flex u-row-between">
							<view class="u-font-lg u-font-weight u-color-major">
								<text class="u-font-xs">￥</text>
								<text>{{item.min_price}}</text>
							</view>
							<view class="button-item" @tap.stop="onCollectDel(item.id)">删除</view>
						</view>
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
				// 收藏列表数据
				collectList: [],
				// 下拉加载配置
				downOption: {
					use: true,
					auto: true
				},
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 10,
					empty: {
						icon: '/static/empty/empty_collect.png',
						tip: '暂无收藏'
					}
				},
			}
		},
		methods: {
			/**
			 * 上拉加载
			 */
			upCallback(page) {
				this.$u.api.apiGoodsCollectList({
					page: page.num
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					if (page.num == 1) this.collectList = []
					
					this.collectList = this.collectList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},
			
			/**
			 * 删除收藏
			 */
			onCollectDel(id) {
				const that = this
				uni.showModal({
				    title: '温馨提示',
				    content: '您去确定要删除吗？',
					cancelText: '再想一想',
					confirmText: '狠心删除',
					confirmColor: '#FF5058',
				    success: function (res) {
				        if (!res.confirm) return
						const param = {id: id}
						that.$u.api.apiGoodsCollectDel(param).then(result => {
							if (result.code === 0) {
								that.$showSuccess(result.msg)
								that.upCallback({
									num: 1
								})
							} else {
								that.$showToast(result.msg)
							}
						})
				    }
				})
			}
		}
	}
</script>

<style lang="scss">
	// 收藏部件样式
	.index-collect-wdiget {
		.collect-item {
			display: flex;
			padding: 20rpx;
			border-bottom: 1rpx solid #EEE;
			background-color: #FFFFFF;
			.product-info {
				display: flex;
				flex-direction: column;
				justify-content: space-between;
				padding: 6rpx 0;
				padding-left: 20rpx;
				.button-item {
					font-size: 24rpx;
					color: $-color-muted;
					padding: 8rpx 40rpx;
					border-radius: 50rpx;
					border: 1rpx solid $-color-muted;
				}
			}
		}
	}
</style>
