<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 选项卡部件 -->
		<view class="index-tabs-widget">
			<u-tabs
				:list="tabList" :is-scroll="false" :current="tabIndex"
				active-color="#FF5058" duration="0" @change="onSwitchTab"
			></u-tabs>
		</view>

		<!-- 分销订单部件 -->
		<mescroll-body :top="80" ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">
			<view class="index-dis-widget">
				<view class="dis-item" v-for="(item, index) in orderList" :key="index">
					<view class="header u-flex u-row-between u-border-bottom">
						<view class="u-font-26" style="color:#555;">订单编号: {{ item.order_sn}}</view>
						<view class="u-font-26 status-stay" v-if="item.status === 1">{{ item.str_status }}</view>
						<view class="u-font-26 status-success" v-if="item.status === 2">{{ item.str_status }}</view>
						<view class="u-font-26 status-invalid" v-if="item.status === 3">{{ item.str_status }}</view>
					</view>
					<view class="u-flex u-padding-20 u-border-bottom">
						<u-image style="flex-shrink:0;" :src="item.image" width="180rpx" height="180rpx" border-radius="12rpx"></u-image>
						<view class="u-margin-left-14">
							<view class="u-font-26 u-line-2" style="color:#101010;">{{ item.name }}</view>
							<view class="u-flex u-row-between u-padding-tb-10">
								<view><text class="u-color-muted u-padding-right-10">数量</text>{{ item.count }}</view>
								<view><text class="u-color-muted u-padding-right-10">付款金额</text>￥{{ item.reality_amount }}</view>
							</view>
							<view>
								<text class="u-color-muted u-padding-right-10">预估收益</text>
								<text class="u-color-major u-font-26">￥{{ item.earnings_money }}</text>
							</view>
						</view>
					</view>
					<view class="footer u-font-26 u-color-muted">{{ item.create_time }}</view>
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
				// 当前选项卡下标
				tabIndex: 0,
				// 选项卡数据列表
				tabList: [
					{name: '全部', count: 0},
					{name: '待返佣', count: 0}, 
					{name: '已结算', count: 0}, 
					{name: '已失效', count: 0}
				],
				// 分销订单列表
				orderList: [],
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
		onLoad(options) {
			this.tabIndex = parseInt(options.tab || 0)
		},
		methods: {
			/**
			 * 获取分销订单
			 */
			upCallback(page) {
				this.$u.api.apiDistributionOrder({
					status: this.tabIndex,
					page: page.num
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
			},
			
			/**
			 * 切换选项卡
			 */
			onSwitchTab(index) {
				this.tabIndex = index
				this.orderList = []
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			}
		}
	}
</script>

<style lang="scss">
	// 选项卡部件样式
	.index-tabs-widget {
		position: fixed;
		top: 0;
		z-index: 2000;
		width: 100%;
	}
	
	// 分销订单部件样式
	.index-dis-widget {
		.dis-item {
			margin: 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.header, .footer {
				padding: 24rpx 20rpx;
			}
			.status-stay { color: #F95F2F; }
			.status-success { color: #07CE1B; }
			.status-invalid { color: #999999; }
		}
	}
</style>
