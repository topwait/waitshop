<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 提现结果部件 -->
		<view class="index-result-widget">
			<view class="status">
				<view class="icon wait" v-if="withdrawData.status===1"><u-icon name="question" size="60" color="#FFF"></u-icon></view>
				<view class="icon wait" v-if="withdrawData.status===2"><u-icon name="info" size="60" color="#FFF"></u-icon></view>
				<view class="icon success" v-if="withdrawData.status===3"><u-icon name="checkbox-mark" size="60" color="#FFF"></u-icon></view>
				<view class="icon fail" v-if="withdrawData.status===4"><u-icon name="close" size="60" color="#FFF"></u-icon></view>
				<view class="u-font-weight u-font-36">{{withdrawData.state}}</view>
				
				<view class="u-margin-top-16">
					<wait-price :amount="withdrawData.apply_money" mainSize="48rpx" minorSize="28rpx"></wait-price>
				</view>
			</view>
			<u-cell-group :border="false">
				<u-cell-item :arrow="false" :border-bottom="false" title="流水编号" value="203404304304034"></u-cell-item>
				<u-cell-item :arrow="false" :border-bottom="false" title="申请时间" value="20211-10-20 15:28:35"></u-cell-item>
				<u-cell-item :arrow="false" :border-bottom="false" title="提现方式" value="钱包余额"></u-cell-item>
				<u-cell-item :arrow="false" :border-bottom="false" title="手续费用" value="0.00"></u-cell-item>
				<u-cell-item :arrow="false" :border-bottom="false" title="实际到账" value="0.00"></u-cell-item>
			</u-cell-group>
		</view>
		<view class="u-text-center u-font-22 u-color-muted">* 审核成功后约72小时内到账，请留意账户明细</view>

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
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true,
				// 提现ID
				withdrawId: 0,
				// 提现数据
				withdrawData: {}
			}
		},
		onLoad(options) {
			this.withdrawId = parseInt(options.id || 0)
			this.getWithdrawDetail()
		},
		methods: {
			getWithdrawDetail() {
				const param = {id: this.withdrawId}
				this.$u.api.apiWithdrawDetail(param).then(result => {
					if (result.code === 0) {
						this.withdrawData = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading withdraw error')
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	// 提现结果部件样式
	.index-result-widget {
		margin: 20rpx;
		padding: 20rpx;
		border-radius: 16rpx;
		background-color: #FFFFFF;
		.u-cell {padding: 10rpx 32rpx;}
		.status {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			margin: 30rpx 0;
			.icon {
				width: 120rpx;
				height: 120rpx;
				line-height: 140rpx;
				text-align: center;
				border-radius: 50%;
				margin-bottom: 30rpx;
				&.success {background-color: #18B566;}
				&.fail{background-color: #FA3534;}
				&.wait{background-color: #2979FF;}
			}
		}
	}
</style>
