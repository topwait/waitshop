<template>
	<view class="condition">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 门店信息部件 -->
		<view class="index-header-widget">
			<view class="index-shop-widget" v-if="detail.store">
				<u-image width="140rpx" height="140rpx" border-radius="12rpx" :src="detail.store.logo" style="flex-shrink: 0;"></u-image>
				<view class="u-margin-left-20">
					<view class="u-font-30">{{detail.store.name}}</view>
					<view class="u-font-26 u-margin-tb-12">电话：{{detail.store.mobile}}</view>
					<view class="u-font-26 u-margin-tb-12">地址：{{detail.store.region}}{{detail.store.address}}</view>
				</view>
			</view>
		</view>
		
		<!-- 门店功能部件 -->
		<view class="index-tools-widget">
			<view class="wrap-statistics-mod">
				<view class="statistics-item">
					<view class="u-font-28 u-color-normal">已核销数</view>
					<view class="number">{{detail.okOrderNum || 0}}</view>
				</view>
				<view class="statistics-item">
					<view class="u-font-28 u-color-normal">待核销数</view>
					<view class="number">{{detail.noOrderNum || 0}}</view>
				</view>
				<view class="statistics-item">
					<view class="u-font-28 u-color-normal">本月核销</view>
					<view class="number">{{detail.monthOrderNum || 0}}</view>
				</view>
			</view>
			<view class="wrap-function-mod">
				<view class="function-item" @click="$toPage('/bundle/pages/writeoff_order/writeoff_order')">
					<view class="u-flex">
						<u-icon name="order" size="38" color="#333"></u-icon>
						<view class="u-margin-left-14">订单核销</view>
					</view>
					<view class="">
						<u-icon name="arrow-right" size="28" color="#999"></u-icon>
					</view>
				</view>
				<view class="function-item" @click="onScan">
					<view class="u-flex">
						<u-icon name="scan" size="38" color="#333"></u-icon>
						<view class="u-margin-left-14">扫码核销</view>
					</view>
					<u-icon name="arrow-right" size="28" color="#999"></u-icon>
				</view>
				<view class="function-item" @click="onManualPopup">
					<view class="u-flex">
						<u-icon name="edit-pen" size="38" color="#333"></u-icon>
						<view class="u-margin-left-14">手动核销</view>
					</view>
					<u-icon name="arrow-right" size="28" color="#999"></u-icon>
				</view>
				<view class="function-item" @click="$toPage('/bundle/pages/writeoff_record/writeoff_record')">
					<view class="u-flex">
						<u-icon name="file-text" size="38" color="#333"></u-icon>
						<view class="u-margin-left-14">核销记录</view>
					</view>
					<u-icon name="arrow-right" size="28" color="#999"></u-icon>
				</view>
			</view>
		</view>
		
		<!-- 弹窗部件 -->
		<u-popup v-model="showPopup" mode="center" border-radius="12" :closeable="true">
			<view class="popup-form-widget">
				<view class="title">手动核销</view>
				<view class="form">
					<u-field v-model="code" label="核销码" placeholder="请输核销码"></u-field>
				</view>
				<view class="u-padding-tb-40">
					<u-button type="error" size="medium" 
						:custom-style="{width: '100%'}" 
						@click="onManualSubmit">确定
					</u-button>
				</view>
			</view>
		</u-popup>
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
				// 详情数据
				detail: [],
				// 手动核销码
				code: '',
				// 手动弹窗
				showPopup: false,
			}
		},
		onShow() {
			this.getWriteeoffIndex()
		},
		methods: {
			/**
			 * 获取核销信息
			 */
			getWriteeoffIndex() {
				this.$u.api.apiStoreWriteOffIndex().then(result => {
					if (result.code === 0) {
						this.detail = result.data	
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast(result.msg)
						setTimeout(() => {
							uni.navigateTo({
								url: '/pages/user/home/home'
							})
						}, 1000)
					}
				})
			},
			
			/**
			 * 扫码核销
			 */
			onScan() {
				//#ifdef H5
					return this.$showToast('扫码功能暂不支持H5端')
				//#endif
				const that = this
				uni.scanCode({
					scanType: ['barCode'],
					success: function (res) {
						toPage('/bundle/pages/writeoff_detail/writeoff_detail?code='+res.result)
					},
					fail: function(err) {
						that.$showToast(err)
					}
				})
			},
			
			qrcodeSucess(data) {
				console.log(data)
			},
			
			/**
			 * 手动核销弹窗
			 */
			onManualPopup() {
				this.showPopup = true
			},
			
			/**
			 * 手动核销确定
			 */
			onManualSubmit() {
				if (this.code === '' || !this.code) {
					return this.$showToast('请输入核销码')
				}
				toPage('/bundle/pages/writeoff_detail/writeoff_detail?code='+this.code)
				this.showPopup = false
				this.code = ''
			},

			/**
			 * 跳转页面
			 */
			onJump(url) {
				toPage(url)
			}
		}
	}
</script>

<style lang="scss">
	// 核销头部样式
	.index-header-widget {
		width: 100%;
		padding: 50rpx 0;
		padding-bottom: 100rpx;
		background-color: #FF5058;
		.index-shop-widget {
			display: flex;
			align-items: center;
			height: 200rpx;
			padding: 0 20rpx;
			margin: 0 20rpx;
			border-radius: 12rpx;
			background-color: #FFF;
		}
	}
	
	// 核销工具样式
	.index-tools-widget {
		margin: 0 20rpx;
		margin-top: -70rpx;
		border-radius: 12rpx;
		background-color: #FFF;
		.wrap-statistics-mod {
			display: flex;
			align-items: center;
			text-align: center;
			.statistics-item {
				width: 33.33%;
				margin: 46rpx 0;
				box-sizing: border-box;
				border-right: 1rpx solid #F2F2F2;
				:last-child {border-right: none;}
				.number {
					padding-top: 18rpx;
					font-size: 30rpx;
					color: #FF5058;
				}
			}
		}
		.wrap-function-mod {
			margin: 20rpx;
			.function-item {
				font-size: 30rpx;
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 36rpx 0;
				border-bottom: 1rpx solid #F2F2F2;
			}
		}
	}
	
	// 弹窗部件样式
	.popup-form-widget {
		width: 560rpx;
		padding: 0 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.title {
			text-align: center;
			font-size: 28rpx;
			padding: 22rpx;
		}
	}
</style>
