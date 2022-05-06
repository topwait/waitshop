<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 记录列表部件 -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit"@down="downCallback" @up="upCallback" :up="upOption">
			<view class="index-record-widget u-margin-tb-20 u-bg-white">
				<view class="record-item u-padding-30 u-border-bottom" 
					v-for="(item, index) in recordList" :key="index" 
					@click="$toPage('/bundle/pages/withdraw_result/withdraw_result?login=true&id='+item.id)">
					<view class="u-flex u-row-between">
						<view class="u-font-30 u-margin-bottom-10">{{item.type}}</view>
						<view class="u-font-34 u-color-major u-font-weight">+{{item.apply_money}}</view>
					</view>
					<view class="u-flex u-row-between">
						<view class="u-font-24 u-color-muted">{{item.create_time}}</view>
						<view class="u-font-24" style="color: #2979FF;" v-if="item.status===1">{{item.state}}</view>
						<view class="u-font-24" style="color: #999999;" v-if="item.status===2">{{item.state}}</view>
						<view class="u-font-24" style="color: #18B566;" v-if="item.status===3">{{item.state}}</view>
						<view class="u-font-24" style="color: #999999;" v-if="item.status===4">{{item.state}}</view>
					</view>
					<view class="u-margin-top-10 u-font-24 u-color-major" v-if="item.tips">审核说明：{{item.tips}}</view>
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
				isFirstLoading: false,
				// 记录数据列表
				recordList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 10,
					empty: {
						icon: '/static/empty/empty_null.png',
						tip: '暂无记录'
					}
				}
			}
		},
		methods: {
			/**
			 * 获取提现记录
			 */
			upCallback(page) {
				this.$u.api.apiWithdrawRecord({
					page: page.num,
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					if (page.num == 1) this.recordList = []
					
					this.recordList = this.recordList.concat(lists)
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
