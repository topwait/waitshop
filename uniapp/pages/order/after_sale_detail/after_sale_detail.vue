<template>
	<view class="container">

		<!-- 售后状态[1=申请退款，2=商家拒绝，3=商品待退货，4=商家待收货，5=商家拒收货，6=等待退款，7=退款成功，8=撤销申请] -->
		
		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 售后流程部件 -->
		<view class="index-flow-widget">
			<view class="progress">
				<view class="u-flex u-margin-bottom-10">
					<u-icon v-if="detail.status === 7" name="checkmark-circle" color="#FF5058" size="36" style="margin-top:4rpx;"></u-icon>
					<u-icon v-if="detail.status === 8" name="close-circle" color="#FF5058" size="36" style="margin-top:4rpx;"></u-icon>
					<u-icon v-if="detail.status === 2" name="close-circle" color="#FF5058" size="36" style="margin-top:4rpx;"></u-icon>
					<u-icon v-if="detail.status!==7 && detail.status!==8 && detail.status!==2" name="clock" size="36" color="#FF5058" style="margin-top:4rpx;"></u-icon>
					<view class="u-margin-left-10 u-font-32 u-font-weight">{{detail.status_text}}</view>
				</view>
				<view class="u-font-24 u-color-muted">{{detail.update_time}}</view>
			</view>
		</view>

		<!-- 上传凭证 -->
		<view class="index-voucher-widget"  v-if="detail.status === 3">
			<view class="u-font-30 u-font-weight">上传凭证</view>
			<u-form :v-model="voucherForm" ref="uForm">
				<u-form-item label-width="130" label="快递公司"><u-input v-model="voucherForm.express_name" placeholder="请输入快递公司名"  /></u-form-item>
				<u-form-item label-width="130" label="快递单号"><u-input v-model="voucherForm.express_no" placeholder="请输入快递面单号" /></u-form-item>
				<u-form-item label-width="130" label="凭证图片">
					<wait-uploader tips="上传凭证" v-model="voucherForm.express_image"></wait-uploader>
				</u-form-item>
			</u-form>
			<view class="submit-btn" @click="$u.debounce(onVoucher, 500)">提交凭证</view>
		</view>

		<!-- 协商历史 -->
		<view class="u-bg-white u-margin-20  u-radius-12">
			<view v-if="detail.status === 7" class="u-flex u-row-between u-padding-24 u-border-bottom" >
				<view class="u-flex u-font-32 u-color-major">
					<u-icon name="rmb-circle" color="#FF5058" size="36" style="margin-top:4rpx;"></u-icon>
					<view class="u-margin-left-6">退款总金额</view>
				</view>
				<wait-price :amount="detail.refund_price" mainSize="34rpx" minorSize="25rpx"></wait-price>
			</view>
			<view v-if="detail.status === 3" class="u-padding-24 u-font-32 u-font-weight u-border-bottom">
				卖家已同意您的退货申请，请把货物包装好退回
			</view>
			<view v-if="detail.status === 4" class="u-padding-24 u-font-32 u-font-weight u-border-bottom">
				您的退回包裹正在运输途中，等待商家收货核验，如果疑问请联系商家！
			</view>
			<view class="u-flex u-row-between u-padding-24" 
				@click="$toPage('/pages/order/after_sale_logs/after_sale_logs?login=true&id='+afterSaleId)">
				<view class="u-font-32 u-font-weight">协商历史</view>
				<u-icon name="arrow-right" size="24" color="#999"></u-icon>
			</view>
		</view>

		<!-- 退款信息部件 -->
		<view class="index-order-widget">
			<view class="u-font-30 u-font-weight">退款信息</view>
			<view class="u-flex u-padding-tb-16" v-if="detail.orderGoods">
				<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="detail.orderGoods.image" style="flex-shrink: 0;"></u-image>
				<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-24" >
					<view class="u-line-2 u-font-28">{{detail.orderGoods.name}}</view>
					<view class="u-font-24 u-color-muted">{{detail.orderGoods.spec_value_str}}</view>
					<view class="u-flex u-row-between" style="width: 100%;">
						<view class="u-font-weight u-color-major"><text class="u-font-22">￥</text>{{detail.orderGoods.actual_price}}</view>
						<view><text class="u-font-22">x</text>{{detail.orderGoods.count}}</view>
					</view>
				</view>
			</view>
			<view class="order-item">
				<text class="dt">退款方式：</text>
				<text class="dd">{{detail.refund_type}}</text>
			</view>
			<view class="order-item">
				<text class="dt">退款原因：</text>
				<text class="dd">{{detail.refund_reason}}</text>
			</view>
			<view class="order-item">
				<text class="dt">退款金额：</text>
				<text class="dd">￥{{detail.refund_price}}</text>
			</view>
			<view class="order-item">
				<text class="dt">退款编号：</text>
				<text class="dd">{{detail.sn}}</text>
			</view>
			<view class="order-item">
				<text class="dt">申请时间：</text>
				<text class="dd">{{detail.create_time}}</text>
			</view>
		</view>
		
		<!-- 退回信息 -->
		<view class="index-order-widget" v-if="detail.status !== 3 && detail.express_no">
			<view class="u-font-30 u-font-weight">退回信息</view>
			<view class="order-item">
				<text class="dt">快递公司：</text>
				<text class="dd">{{detail.express_name}}</text>
			</view>
			<view class="order-item">
				<text class="dt">快递单号：</text>
				<text class="dd">{{detail.express_no}}</text>
			</view>
			<view class="order-item">
				<text class="dt">寄出时间：</text>
				<text class="dd">{{detail.express_time}}</text>
			</view>
			<view class="order-item u-margin-top-40">
				<text class="dt" style="height:100rpx;">凭证图片：</text>
				<view class="dd">
					<view style="width:100rpx; height:100rpx; border: 1px dashed #EEE;">
						<image :src="detail.express_image" style="width:100%; height:100%;"></image>
					</view>
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
				// 首次加载
				isFirstLoading: true,
				// 售后ID
				afterSaleId: 0,
				// 售后详细
				detail: [],
				// 凭证表单
				voucherForm: {
					id: 0,
					express_name: '',
					express_no: '',
					express_image: []
				}
			}
		},
		onLoad(options) {
			this.afterSaleId = parseInt(options.id)
			this.voucherForm.id = this.afterSaleId
			this.getAfterSaleDetail()
		},
		methods: {
			/**
			 * 获取售后详情
			 */
			getAfterSaleDetail() {
				const param = {id: this.afterSaleId}
				this.$u.api.apiAfterSaleDetail(param).then(result => {
					if (result.code === 0) {
						this.detail = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading afterSale error')
					}
				})
			},
			
			/**
			 * 上传退回凭证
			 */
			onVoucher() {
				const that = this
				if (!that.voucherForm.express_name)  return that.$showToast('请填写快递公司名')
				if (!that.voucherForm.express_no)    return that.$showToast('请填写快递面单号')
				if (!that.voucherForm.express_image) return that.$showToast('请上传快递面单图')
				uni.showModal({
				    title: '提示',
				    content: '您确定要上传凭证吗？',
					confirmColor: '#FF5058',
				    success: function (res) {
						if (!res.confirm) return false					
						that.$u.api.apiAfterSaleExpress(that.voucherForm).then(result => {
							if (result.code === 0) {
								that.getAfterSaleDetail()
								that.$showSuccess(result.msg)
							} else {
								that.$showToast(result.msg)
							}
						})
				    }
				})
			}
		}
	}
</script>

<style lang="scss">
	// 售后流程部件样式
	.index-flow-widget {
		position: relative;
		padding: 20rpx;
		background-color: $-color-main;
		.progress {
			padding: 30rpx 20rpx;
			border-radius: 16rpx;
			background-color: #FFFFFF;
		}
	}
	
	// 快递凭证部件样式
	.index-voucher-widget {
		margin: 20rpx;
		padding: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.submit-btn {
			margin: 20rpx;
			padding: 20rpx;
			margin-bottom: 0;
			color: #FFFFFF;
			text-align: center;
			border-radius: 60rpx;
			background-color: #FF5058;
		}
	}

	// 订单退款部件样式
	.index-order-widget {
		margin: 20rpx;
		padding: 20rpx 30rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.order-item {
			display: flex;
			align-items: center;
			font-size: 24rpx;
			color: #000000;
			height: 60rpx;
			line-height: 60rpx;		
			.dt {
				color: #999;
			}
		}
	}
</style>
