<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>

		<!-- 积分部件 -->
		<view class="index-integral-widget">
			<view class="property">
				<view class="u-font-24 u-margin-bottom-16">当前积分</view>
				<view style="font-size: 70rpx;">{{detail.surplusIntegral || 0}}</view>
			</view>
			<view class="statistics">
				<view>
					<view class="u-font-26">累计积分</view>
					<view class="u-font-22 u-padding-top-10">{{detail.totalGetIntegral || 0}}</view>
				</view>
				<view>
					<view class="u-font-26">累计消费</view>
					<view class="u-font-22 u-padding-top-10">{{detail.totalUseIntegral || 0}}</view>
				</view>
				<view>
					<view class="u-font-26">今日获得</view>
					<view class="u-font-22 u-padding-top-10">{{detail.todayIntegral || 0}}</view>
				</view>
			</view>
		</view>
		
		<!-- 积分明细 -->
		<view class="index-record-widget">
			<view class="header">
				<view class="u-font-40 u-font-weight">积分明细</view>
				<view class="u-font-28 u-color-muted">
					<text>收入</text> / 
					<text>支出</text>
				</view>
			</view>
			<mescroll-body ref="mescrollRef" height="none" @init="mescrollInit" @down="downCallback" @up="upCallback" :down="{use: false}" :up="upOption">
				<view class="u-flex u-row-between u-padding-tb-16 u-border-bottom" v-for="(item, index) in logIntegral" :key="index">
					<view>
						<view class="u-font-32 u-color-normal u-padding-bottom-10">{{item.remarks}}</view>
						<view class="u-font-24 u-color-muted u-margin-tb-10">{{item.create_time}}</view>
					</view>
					<view class="u-font-30 u-font-weight">{{item.change_type===1?'+':'-'}}{{item.change_amount}}</view>
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
				// 积分数据信息
				detail: {
					surplusIntegral: 0,
					totalGetIntegral: 0,
					totalUseIntegral: 0,
					todayIntegral: 0
				},
				// 积分日志列表
				logIntegral: [],
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
			 * 获取用户积分
			 */
			upCallback(page) {
				this.$u.api.apiUserIntegral({
					page: page.num,
				}).then(result => {
					const lists = result.data.data
					const total = result.data.total
					this.detail.surplusIntegral  = result.data.surplusIntegral
					this.detail.totalGetIntegral = result.data.totalGetIntegral
					this.detail.totalUseIntegral = result.data.totalUseIntegral
					this.detail.todayIntegral    = result.data.todayIntegral
					if (page.num == 1) this.logIntegral = []

					this.logIntegral = this.logIntegral.concat(lists)
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
	// 积分部件样式
	.index-integral-widget {
		position: relative;
		width: 100%;
		height: 300rpx;
		background: linear-gradient(to right, #FF5058, #fc5e65);
		.property {
			color: #FFFFFF;
			margin-left: 30rpx;
			padding-top: 40rpx;
		}
		.statistics {
			display: flex;
			justify-content: space-between;
			color: #FFFFFF;
			margin: 0 30rpx;
			margin-top: 20rpx;
		}
	}
	
	// 积分记录样式
	.index-record-widget {
		position: absolute;
		top: 270rpx;
		bottom: 0;
		width: 100%;
		padding: 0 30rpx;
		color: #32446b;
		border-top-left-radius: 40rpx;
		border-top-right-radius: 40rpx;
		background-color: #FFFFFF;
		.header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 40rpx 0;
		}
	}

</style>
