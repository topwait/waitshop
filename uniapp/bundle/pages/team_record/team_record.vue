<template>
	<view class="container">

		<!-- 选项卡部件 -->
		<view class="index-tabs-widget">
			<u-tabs
				:list="tabList" :is-scroll="false" :current="tabIndex"
				active-color="#FF5058" duration="0" @change="onSwitchTab"
			></u-tabs>
		</view>
		
		<!-- 参团订单部件 -->
		<mescroll-body :top="80" ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :down="downOption" :up="upOption">
			<view class="index-teamOrder-widget">
				<view class="teamOrder-item" v-for="(item, index) in orderList" :key="index" 
					@click="$toPage('/pages/order/detail/detail?id='+item.order_id+'&login=true')">
					<view class="header u-flex u-row-between u-border-bottom">
						<view class="u-font-26 u-color-normal">{{item.create_time}}</view>
						<view class="u-font-26 u-color-major" v-if="item.status===1">拼团中</view>
						<view class="u-font-26 u-color-major" v-if="item.status===2">拼团成功</view>
						<view class="u-font-26 u-color-major" v-if="item.status===3">拼团失败</view>
					</view>
					<view class="main u-flex">
						<u-image :src="item.image" width="180rpx" height="180rpx" border-radius="6rpx" style="flex-shrink: 0;"></u-image>
						<view class="goods-info">
							<view>
								<text class="team-people">{{item.people}}人团</text>
								<text class="u-font-28 u-color-normal">{{item.name}}</text>
							</view>
							<view class="u-margin-top-10 u-font-24 u-color-muted">IPhone6，32G</view>
							<view class="u-margin-top-10 u-flex u-row-between">
								<view class="u-color-major">
									<text class="u-font-26">￥</text>
									<text class="u-font-34">{{item.total_price}}</text>
								</view>
								<view class="u-font-28 u-color-normal">x{{item.count}}</view>
							</view>
						</view>
					</view>
					<view class="footer u-flex u-row-right">
						<text class="u-font-30 u-color-muted">共1件商品，实付款：</text>
						<text class="u-font-34 u-color-normal">￥{{item.actual_price}}</text>
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
	import {isWeixin} from '@/utils/tools'
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
					{name: '拼团中', count: 0}, 
					{name: '拼团成功', count: 0}, 
					{name: '拼团失败', count: 0}
				],
				// 拼团订单列表
				orderList: [],
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
						icon: '/static/empty/empty_null.png',
						tip: '暂无数据'
					}
				}
			}
		},
		methods: {
			/**
			 * 获取拼团订单
			 */
			upCallback(page) {
				this.$u.api.apiTeamRecord({
					status: this.tabIndex != 0 ? this.tabIndex : "",
					page: page.num
				}).then(result => {
					const lists = result.data.data
					const total	= result.data.total
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
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			}
		}
	}
</script>

<style lang="scss">
	// 选项卡样式
	.index-tabs-widget {
		position: fixed;
		top: 0;
		z-index: 2000;
		width: 100%;
	}
	
	// 拼团订单列表部件样式
	.index-teamOrder-widget {
		.teamOrder-item {
			margin: 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.header, .main {
				padding: 26rpx 0;
				margin: 0 20rpx;
			}
			.footer {
				padding: 20rpx 0;
				margin: 0 20rpx;
			}
			.goods-info {
				margin-left: 20rpx;
				.team-people {
					font-size: 22rpx;
					color: #ff2c3c;
					text-align: center;
					display: inline-block;
					padding: 2rpx 10rpx;
					white-space:nowrap;
					border-radius: 6rpx;
					background: #FFF;
					border: 1px solid #ff2c3c;
					margin-right: 10rpx;
				}
			}
		}
	}
</style>
