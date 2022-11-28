<template>
	<view class="container">
		
		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 选项卡部件 -->
		<view class="index-tabs-wdiget">
			<view class="tabs-item" 
				:class="index === tabIndex ? 'active' : ''" 
				v-for="(item, index) in tabList" :key="index"
				@click="onSwitchTab(index)">
				{{item.name}}({{item.count}})
			</view>
		</view>
		
		<!-- 评论列表部件 -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">
			<view class="index-comment-widget">
				<view class="comment-item" v-for="(item, index) in commentList" :key="index">
					<view class="u-flex">
						<u-image width="90rpx" height="90rpx" border-radius="50%" :lazy-load="true" :src="item.avatar"></u-image>
						<view class="u-margin-left-10">
							<view class="u-flex u-margin-left-10">
								<view class="u-font-weight u-font-32">{{item.nickname}}</view>
								<view class="u-margin-left-10">
									<u-rate :count="5" :current="item.goods_comment" :disabled="true"></u-rate>
								</view>
							</view>
							<view class="u-font-24 u-color-lighter u-margin-top-10">
								{{item.create_time}}
							</view>
						</view>
					</view>
					<view class="u-font-28 u-color-muted u-margin-top-20">
						已购商品：{{item.spec_value_str}}
					</view>
					<view class="u-margin-top-10">
						{{item.content || '默认评价'}}
					</view>
					<view class="reply u-margin-top-10" v-if="item.reply">
						商家回复：{{item.reply}}
					</view>
					<view class="u-margin-top-20" v-if="item.images.lenght > 0">
						<view class="img" v-for="(img, i) in item.images" :key="i">
							<image :src="img" alt=""></image>
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
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins"
	import MescrollBody from "@/components/mescroll-uni/mescroll-body"
	export default {
		mixins: [MescrollMixin],
		components: {MescrollBody},
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 商品ID
				goodsId: 0,
				// 当前选项卡下标
				tabIndex: 0,
				// 选项卡数据列表
				tabList: [
					{name: '全部', count: 0},
					{name: '晒图', count: 0}, 
					{name: '好评', count: 0}
				],
				// 评论列表
				commentList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 10,
					empty: {
						icon: '/static/empty/empty_null.png',
						tip: '暂无评论'
					}
				}
			}
		},
		onLoad(options) {
			this.goodsId = parseInt(options.id || 0)
			this.upCallback({
				num: 1
			})
		},
		methods: {
			/**
			 * 获取商品评论列表
			 */
			upCallback(page) {
				this.$u.api.apiCommentWhole({
					id: this.goodsId,
					tab: this.tabIndex,
					page: page.num
				}).then(result => {
					const tabs    = result.data.data.tabs
					const lists   = result.data.data.list
					const total	  = result.data.total
					
					if (page.num == 1) this.commentList = []
					
					this.commentList = this.commentList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},

			/**
			 * 切换选项卡
			 */
			onSwitchTab(index) {
				this.tabIndex = index
				this.commentList = []
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			}
		}
	}
</script>

<style lang="scss">
	// 选项卡样式
	.index-tabs-wdiget {
		display: flex;
		align-items: center;
		.tabs-item {
			font-weight: bold;
			margin: 20rpx;
			padding: 20rpx;
			border-radius: 12rpx;
			border: 1rpx solid #FFF;
			background-color: #FFF;
			&.active {
				color: #FF5058;
				border: 1rpx solid #FF5058;
				background-color: #faeced;
			}
		}
	}
	
	// 评论列表样式
	.index-comment-widget {
		margin: 20rpx 0;
		margin-top: 0;
		.comment-item {
			padding: 20rpx;
			background-color: #FFF;
			.reply {
				padding: 20rpx;
				color: #6a6a6a;
				font-size: 28rpx;
				border-radius: 6rpx;
				background-color: #f8f8f8;
			}
		}
	}
</style>
