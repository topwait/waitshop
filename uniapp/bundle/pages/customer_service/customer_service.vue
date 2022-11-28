<template>
	<view class="container">
		
		<!-- 联系客服部件 -->
		<view class="index-service-widget">
			<u-image class="u-margin-top-60" width="390rpx" height="390rpx" :src="detail.image"></u-image>
			<view class="u-font-32 u-font-weight u-margin-top-20">扫码添加客服</view>
			<view class="saveQrCode">保存二维码</view>
			<view class="info">
				<view class="line">服务时间：{{detail.business_hours}}</view>
				<view class="line">服务电话：{{detail.phone}}</view>
				<view class="line">服务Q Q：{{detail.qq}}</view>
			</view>
		</view>
		<view class="u-font-24 u-color-white u-margin-top-50 u-text-center">无法添加或疑难问题请联系人工客服电话</view>
	
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
				// 首次加载
				isFirstLoading: true,
				// 客服信息
				detail: []
			}
		},
		onShow() {
			this.getCustomerService()
		},
		methods: {
			getCustomerService() {
				this.$u.api.apiCustomerService().then(result => {
					if (result.code === 0) {
						this.detail = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast(result.msg)
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background: linear-gradient(to bottom, #ffb86a, #fb7a4b, #f94e35, #f73327, #f61b1c);
	}
	
	// 联系客服部件样式
	.index-service-widget {
		display: flex;
		flex-direction: column;
		align-items: center;
		margin: 0 60rpx;
		margin-top: 200rpx;
		height: 900rpx;
		border-radius: 12rpx;
		background-color: #FFF;
		.saveQrCode {
			color: #FFF;
			font-size: 32rpx;
			font-weight: bold;
			text-align: center;
			width: 460rpx;
			height: 88rpx;
			line-height: 88rpx;
			margin-top: 80rpx;
			border-radius: 60rpx;
			background: linear-gradient(to bottom, #FFA200, #FF5E44);
		}
		.info {
			margin-top: 40rpx;
			.line {
				color: #333;
				line-height: 46rpx;
				font-size: 26rpx;
				font-weight: 400;
			}
		}
	}
</style>
