<template>
	<view class="container">

		<!-- 协商协议记录部 -->
		<view class="index-logs-widget">
			<view class="logs-item" v-for="(item, index) in logsData" :key="index">
				<view class="u-flex">
					<u-image width="80rpx" height="80rpx" :src="item.user.avatar"></u-image>
					<view class="u-margin-left-20">
						<view class="u-font-26 u-margin-bottom-10">{{item.user.nickname}}</view>
						<view class="u-font-22 u-color-muted">{{item.create_time}}</view>
					</view>
				</view>
				<view class="u-margin-top-20">
					<view class="dd">【标题】{{item.operate}}</view>
					<view class="dd">【内容】{{item.content}}</view>
					<view class="dd" v-if="item.remarks">【说明】{{item.remarks}}</view>
				</view>
			</view>
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
	export default {
		data() {
			return {
				// 售后ID
				afterSaleId: 0,
				// 记录列表
				logsData: []
			}
		},
		onLoad(options) {
			this.afterSaleId = parseInt(options.id)
			this.getLogsList()
		},
		methods: {
			getLogsList() {
				const param = {id: this.afterSaleId}
				this.$u.api.apiAfterSalelogs(param).then(result => {
					if (result.code === 0) {
						this.logsData = result.data
					} else {
						this.$showToast(result.msg)
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	// 协商协议记录部件样式
	.index-logs-widget {
		margin-top: 20rpx;
		.logs-item {
			padding: 20rpx;
			margin-bottom: 20rpx;
			background-color: #FFFFFF;
			.dd {
				padding: 4rpx 0;
				font-size: 26rpx;
				color: #333333;
			}
		}
	}
</style>
