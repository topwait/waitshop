<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 选项卡部件 -->
		<view class="u-absolute" style="top: 0; z-index: 10000; width: 100%;">
			<u-tabs
				:list="tabList" :is-scroll="false" :current="tabIndex"
				active-color="#FF5058" duration="0" @change="onSwitchTab"
			></u-tabs>
		</view>
		
		<!-- 日志流水部件 -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :down="downOption" :up="upOption">
			<view class="index-record-widget" style="margin-top: 100rpx;">
				<view class="record-item u-padding-30 u-border-bottom u-bg-white" v-for="(item, index) in logWalletList" :key="index">
					<view class="u-flex u-row-between">
						<view class="u-font-30 u-margin-bottom-10">{{ item.source_type }}</view>
						<view class="u-font-34 u-color-major" v-if="item.change_type==1">{{ item.change_amount }}</view>
						<view class="u-font-34" v-if="item.change_type==2">{{ item.change_amount }}</view>
					</view>
					<view class="u-font-24 u-color-muted">{{ item.create_time }}</view>
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
					{name: '收入', count: 0},
					{name: '支出', count: 0}
				],
				// 钱包日志流水数据
				logWalletList: [],
				// 下拉加载配置
				downOption: {
					use: true,
					auto: true
				},
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 1,
					empty: {
						icon: '/static/empty/empty_null.png',
						tip: '暂无数据'
					}
				},
			}
		},
		methods: {
			/**
			 * 获取钱包日志
			 */
			upCallback(page) {
				this.$u.api.apiUserAccount({
					type: this.tabIndex,
					page: page.num
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					if (page.num == 1) this.logWalletList = []
					
					this.logWalletList = this.logWalletList.concat(lists)
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
				this.logWalletList = []
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			}
		}
	}
</script>
