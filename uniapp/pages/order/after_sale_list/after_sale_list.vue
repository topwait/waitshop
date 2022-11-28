<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 选项卡组件 -->
		<u-tabs class="index-tabs-widget"
			:list="tabList" :is-scroll="false" :current="tabIndex"
			active-color="#FF5058" duration="0" @change="onSwitchTab"
		></u-tabs>
		
		<!-- 退款列表部件 -->
		<view class="index-refund-widget">
			<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">	
				<view class="refund-item" v-for="(item, index) in afterSaleList" :key="index">
					<view class="u-flex u-row-between u-padding-tb-20 u-border-bottom">
						<view class="u-font-24 u-color-lighter">服务单号：{{item.sn}}</view>
						<view class="u-font-24 u-color-major" v-if="item.status != 8">{{item.status_text}}</view>
						<view class="u-font-24 u-color-muted" v-if="item.status==8">已关闭</view> 
					</view> 
					<view class="u-flex u-padding-tb-20" @click="$toPage('/pages/order/after_sale_detail/after_sale_detail?id='+item.id)">
						<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="item.image" style="flex-shrink: 0;"></u-image>
						<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-24">
							<view class="u-line-2 u-font-md">{{item.name}}</view>
							<view class="u-font-24 u-color-muted u-padding-tb-6">{{item.spec_value_str}}</view>
							<view class="u-font-28">退款：<text class="u-font-xs">￥</text>{{item.refund_price}}</view>
						</view>
					</view>
					<view class="tips">申请状态：{{item.status_text}}</view>
					<view class="button-group" v-if="item.status !== 2">
						<view class="button-item" v-if="item.status === 1" @click="onRevoke(item.id)">撤销申请</view>
						<!-- <view class="button-item" v-if="item.status === 3">重新申请</view> -->
					</view>
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
				// 选项卡下标
				tabIndex: 0,
				// 选项卡列表
				tabList: [
					{name: '全部'}, 
					{name: '处理中'}, 
					{name: '已处理'}
				],
				// 售后列表数据
				afterSaleList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 1,
					empty: {
						icon: '/static/empty/empty_sale.png',
						tip: '暂无售后'
					}
				}	
			}
		},
		methods: {
			/**
			 * 获取售后列表
			 */
			upCallback(page) {
				this.$u.api.apiAfterSaleList({
					type: this.tabIndex,
					page: page.num
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					if (page.num == 1) this.afterSaleList = []
					
					this.afterSaleList = this.afterSaleList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},
			
			/**
			 * 撤销申请
			 */
			onRevoke(id) {
				const param = {id: id}
				this.$u.api.apiAfterSaleRevoke(param).then(result => {
					if (result.code === 0) {
						this.$showSuccess(result.msg)
						this.upCallback({num: 1})
					} else {
						this.$showToast(result.msg)
					}
				})
			},
			
			/**
			 * 切换选项卡
			 */
			onSwitchTab(index) {
				this.tabIndex = index
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			}
		}
	}
</script>

<style lang="scss">
	// 退款列表部件样式
	.index-refund-widget {
		position: absolute;
		width: 100%;
		top: 80rpx;
		bottom: 0;
		.refund-item {
			margin: 20rpx;
			padding: 0 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.tips {
				padding: 22rpx;
				border-radius: 12rpx;
				background: #f6f6f6;
			}
			.button-group {
				display: flex;
				justify-content: flex-end;
				padding: 20rpx 0;
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
				}
			}
		}
	}
</style>
