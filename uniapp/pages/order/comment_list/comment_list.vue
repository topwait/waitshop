<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>

		<!-- 选项卡部件 -->
		<u-tabs class="index-tabs-widget"
			:list="tabList" :is-scroll="false" :current="tabIndex"
			active-color="#FF5058" duration="0" @change="onSwitcTab"
		></u-tabs>

		<!-- 待评价产品部件 -->
		<view class="index-stay-widget" v-if="tabIndex === 0">
			<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">	
				<view class="stay-item" v-for="(item, index) in commentList" :key="index">
					<view class="u-flex" @click="$toPage('/pages/goods/detail/detail?id='+item.goods_id)">
						<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="item.image" style="flex-shrink: 0;"></u-image>
						<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-20">
							<view class="u-line-2 u-font-28">{{item.name}}</view>
							<view class="u-font-24 u-color-muted">{{item.spec_value_str}}</view>
							<view class="u-flex u-row-between" style="width: 100%;">
								<view class="u-font-weight u-color-major"><text class="u-font-22">￥</text>{{item.actual_price}}</view>
								<view><text class="u-font-22">x</text>{{item.count}}</view>
							</view>
						</view>
					</view>
					<view class="button-group">
						<view class="button-item button-item--main" 
							@click="$toPage('/pages/order/comment_push/comment_push?id='+item.id)">评价商品</view>
					</view>
				</view>
			</mescroll-body>
		</view>

		<!-- 已评价产品部件 -->
		<view class="index-evaluate-widget" v-if="tabIndex === 1">
			<mescroll-body ref="mescrollRef" @init="mescrollInit" height="100%" @down="downCallback" @up="upCallback" :up="upOption">
				<view class="evaluate-item" v-for="(item, index) in commentList" :key="index">
					<view class="u-font-xs u-color-muted u-margin-bottom-20 u-margin-lr-8">
						<text class="u-margin-right-10">{{item.create_time}}</text>
						<text>套餐类型：{{item.orderGoods.spec_value_str}}</text>
					</view>
					<view class="u-font-28 u-margin-bottom-20 u-margin-lr-8">{{item.content}}</view>
					<view class="u-margin-bottom-20" v-if="item.images.length > 0">
						<u-row gutter="16">
							<u-col span="4" v-for="(img, i) in item.image" :key="i">
								<u-image width="100%" height="200rpx" :lazy-load="true" :src="img"></u-image>
							</u-col>
						</u-row>
					</view>
					<view class="u-flex u-margin-lr-8 u-padding-6 u-radius-6" style="background-color: #f8f7fc;" 
						@click="$toPage('/pages/goods/detail/detail?id='+item.goods_id)">
						<u-image width="160rpx" height="160rpx" :lazy-load="true" :src="item.orderGoods.image" style="flex-shrink: 0;"></u-image>
						<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-20 u-padding-tb-10 u-padding-right-10" style="height: 160rpx;">
							<view class="u-line-2 u-font-28">{{item.orderGoods.name}}</view>
							<view class="u-font-30">￥{{item.orderGoods.actual_price}}</view>	
						</view>
					</view>
					<view class="merchant-reply" v-if="item.reply">{{item.reply}}</view>
				</view>
			</mescroll-body>
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
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins"
	import MescrollBody from "@/components/mescroll-uni/mescroll-body"
	export default {
		mixins: [MescrollMixin],
		components: {MescrollBody},
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				loading: true,
				// 选项卡下标
				tabIndex: 0,
				// 选项卡列表
				tabList: [
					{name: '待评价'}, 
					{name: '已评价'},
				],
				// 待评论列表
				commentList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 8,
					empty: {
						icon: '/static/empty/empty_comment.png',
						tip: '暂无评论'
					}
				}
			}
		},
		methods: {
			/**
			 * 获取我的评论
			 */
			upCallback(page) {
				this.$u.api.apiCommentList({
					type: this.tabIndex,
					page: page.num
				}).then(result => {
					const lists   = result.data.data
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
			onSwitcTab(index) {
				this.tabIndex = index
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			}
		}
	}
</script>

<style lang="scss">
	// 待评价部件样式
	.index-stay-widget {
		position: absolute;
		width: 100%;
		top: 80rpx;
		bottom: 0;
		.stay-item {
			margin: 20rpx;
			padding: 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.button-group {
				width: 100%;
				display: flex;
				justify-content: flex-end;
				margin-top: 20rpx;
				.button-item {
					width: 85px;
					height: 54rpx;
					line-height: 54rpx;
					font-size: 24rpx;
					color: #616161;
					text-align: center;
					margin-right: 30rpx;
					border-radius: 40rpx;
					border: 1px #CCCCCC solid;
					&:last-child { margin-right: 0; }
					&--main {color: $-color-main; border: 1px $-color-main solid; }
				}
			}
		}
	}
	
	// 已评价产品部件样式
	.index-evaluate-widget {
		position: absolute;
		width: 100%;
		top: 80rpx;
		bottom: 0;
		.evaluate-item {
			margin: 20rpx;
			padding: 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.merchant-reply {
				position: relative;
				margin: 0 8rpx;
				margin-top: 40rpx;
				padding: 20rpx;
				color: #6a6a6a;
				font-size: 28rpx;
				border-radius: 6rpx;
				background-color: #f8f8f8;
				&::after {
					position: absolute;
					top: -18rpx;
					left: 50rpx;
					content: "";
					width: 36rpx;
					height: 36rpx;
					transform:rotate(45deg);
					background-color: #f8f8f8;
				}
			}
		}
	}
</style>
