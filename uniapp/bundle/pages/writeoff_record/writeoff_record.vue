<template>
	<view class="container">
		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 核销订单部件 -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">
			<view class="index-order-widget">
				<view class="pack-order-item"  v-for="(item, index) in orderList" :key="index">
					<view class="u-flex u-row-between u-padding-tb-20 u-border-bottom">
						<view class="u-flex u-font-28 u-color-lighter">
							<view class="activity" v-if="item.order_type===2">秒杀</view>
							<view class="activity" v-if="item.order_type===3">拼团</view>
							<view class="activity" v-if="item.order_type===4">砍价</view>
							<view>订单号: {{item.order_sn}}</view>
						</view>
						<view class="u-font-28 u-color-success">{{item.operate}}</view>
					</view>
					<view class="u-padding-tb-10">
						<view class="order-product-item" v-for="(product, i) in item.orderGoods" :key="i" 
							@click="$toPage('/bundle/pages/writeoff_detail/writeoff_detail?code='+item.pick_up_code)">
							<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="product.image" style="flex-shrink: 0;"></u-image>
							<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-24 u-flex-1">
								<view class="u-line-2 u-font-28">{{product.name}}</view>
								<view class="u-font-24 u-color-muted u-padding-tb-6">{{product.spec_value_str}}</view>
								<view class="u-flex u-row-between" style="width: 100%;">
									<view class="u-font-weight u-color-major"><text class="u-font-22">￥</text>{{product.sell_price}}</view>
									<view><text class="u-font-22">x</text>{{product.count}}</view>
								</view>
							</view>
						</view>
					</view>
					<view class="u-flex u-row-right u-padding-tb-20 u-font-24 u-border-bottom">
						<text class="u-color-muted">共{{item.orderGoods.length}}件商品，实付款: </text>
						<text class="u-font-28"><text class="u-font-22">￥</text>{{item.paid_amount}}</text>
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
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins";
	import MescrollBody from "@/components/mescroll-uni/mescroll-body"
	export default {
		mixins: [MescrollMixin],
		components: {MescrollBody},
		data() {
			return {
				// 首次加载
				isFirstLoading: false,
				// 订单列表数据
				orderList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 1,
					empty: {
						icon: '/static/empty/empty_trade.png',
						tip: '暂无订单'
					}
				}
			}
		},
		methods: {
			/**
			 * 上拉加载
			 */
			upCallback(page) {
				that.$u.api.apiStoreWriteOffRecord(aram = {
					type: this.tabIndex,
					page: pageNo
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					if (page.num == 1) this.orderList = []
					
					this.orderList = this.orderList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			}
		}
	}
</script>

<style lang="scss">
	// 订单部件样式
	.index-order-widget {
		.pack-order-item {
			margin: 20rpx;
			padding: 0 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.activity {
				padding: 0 10rpx;
				height: 38rpx;
				line-height: 38rpx;
				margin-right: 10rpx;
				color: #FF5058;
				font-size: 22rpx;
				border: 1rpx solid #FF5058;
				border-radius: 4rpx;
			}
			.order-product-item {
				display: flex;
				padding: 10rpx 0;
			}
			.order-button-group {
				display: flex;
				justify-content: flex-end;
				padding: 30rpx 0;
				.button-item {
					width: 85px;
					height: 60rpx;
					line-height: 60rpx;
					font-size: 24rpx;
					color: #616161;
					text-align: center;
					margin-right: 30rpx;
					border-radius: 40rpx;
					border: 1px #c7c7c7 solid;
					&:last-child { margin-right: 0; }
					&--main {color: #FF5058; border: 1px #FF5058 solid; }
				}
			}
		}
	}
</style>
