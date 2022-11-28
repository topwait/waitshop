<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 售后商品部件 -->
		<view class="index-product-widget">
			<view class="u-font-30 u-font-weight u-padding-bottom-6">退款商品</view>
			<view class="product-item">
				<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="orderGoods.image" style="flex-shrink: 0;"></u-image>
				<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-24" >
					<view class="u-line-2 u-font-28">{{orderGoods.name}}</view>
					<view class="u-font-24 u-color-muted">{{orderGoods.spec_value_str}}</view>
					<view class="u-flex u-row-between" style="width: 100%;">
						<view class="u-font-weight u-color-major"><text class="u-font-xs">￥</text>{{orderGoods.actual_price}}</view>
						<view><text class="u-font-22">x</text>{{orderGoods.count}}</view>
					</view>
				</view>
			</view>
		</view>
		
		<!-- 售后方式部件 -->
		<view class="index-method-widget" v-if="refundType <= 0">
			<view class="u-font-30 u-font-weight" style="padding:20rpx;">选择服务类型</view>
			<view class="method-item u-border-bottom" @click="onSelectRefundType(1)">
				<view class="u-flex">
					<u-icon name="rmb-circle" size="46" color="#FF5058"></u-icon>
					<view class="u-margin-left-14">
						<view class="dt u-font-28 u-font-30 u-font-weight u-margin-bottom-10">我要退款（无需退货）</view>
						<view class="dd u-font-22 u-color-muted">没收到货，或与卖家协商同意不用退货只退款</view>
					</view>
				</view>
				<u-icon name="arrow-right" size="28" color="#999" style="padding:0 20rpx;"></u-icon>
			</view>
			<view class="method-item" @click="onSelectRefundType(2)">
				<view class="u-flex">
					<u-icon name="clock" size="46" color="#FF5058"></u-icon>
					<view class="u-margin-left-14">
						<view class="dt u-font-28 u-font-30 u-font-weight u-margin-bottom-10">我要退货退款</view>
						<view class="dd u-font-22 u-color-muted">已收到货，需退还收到的货物</view>
					</view>
				</view>
				<u-icon name="arrow-right" size="28" color="#999" style="padding:0 20rpx;"></u-icon>
			</view>
		</view>
		
		<!-- 售后原因部件 -->
		<view class="index-reason-widget" v-if="refundType > 0">
		<view class="u-font-30 u-font-weight" style="padding:20rpx;">退款信息</view>
			<u-cell-group :border="false">
				<u-cell-item title="退款原因" @click="onShowReason">
					<text v-if="reasonIndex === -1">请选择</text>
					<text v-else>{{reasonList[reasonIndex]}}</text>
				</u-cell-item>
				<u-cell-item title="退款金额" :arrow="false">
					<text class="u-color-major u-font-28 u-font-weight">￥{{orderGoods.actual_price}}</text>
				</u-cell-item>
				<u-cell-item title="退款说明" :arrow="false">
					<view class="u-margin-left-20 u-padding-20 u-radius-6" style="background-color: #f8f8f8;">
						<u-input :type="'textarea'" v-model="remark" 
							:border="false" height="130" 
							placeholder="补充描述,有助于商家更好处理售后" />
					</view>
				</u-cell-item>
				<u-cell-item title="上传凭证" :arrow="false">
					<view class="u-margin-left-10">
						<wait-uploader v-model="images"></wait-uploader>
					</view>
				</u-cell-item>	
			</u-cell-group>
		</view>
		
		<!-- 申请按钮部件 -->
		<view class="u-margin-top-40 u-padding-lr-20" v-if="refundType > 0">
			<u-button type="error" shape="circle" @click="onApply">申请退款</u-button>
		</view>
		
		<!-- 申请原因部件 -->
		<u-popup v-model="showReason" mode="bottom">
			<view class="u-flex u-row-center u-padding-tb-30">退款原因</view>
			<scroll-view style="height: 800rpx" :scroll-y="true">
				<view class="reason-item">
					<radio-group @change="onRadioChange">
						<label v-for="(item, index) in reasonList" :key="index" 
							class="u-flex u-row-between" style="padding: 24rpx 20rpx;">
							<view style="line-height: 46rpx;">{{item}}</view>
							<radio :value="index.toString()"></radio>
						</label>
					</radio-group>
				</view>
			</scroll-view>
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
				// 退款类型[1=仅退款，2=退款退货]
				refundType: 0,
				// 退款商品ID
				orderGoodsId: 0,
				// 退款商品信息
				orderGoods: {},
				// 显示选择原因
				showReason: false,
				// 售后原因下标
				reasonIndex: -1,
				// 售后原因列表
				reasonList: [],
				// 售后描述
				remark: '',
				// 售后图片
				images: []
			}
		},
		onLoad(options) {
			this.orderGoodsId = parseInt(options.id || 0)
			this.getAfterSaleGoods()
		},
		methods: {
			/**
			 * 获取售后商品
			 */
			getAfterSaleGoods() {
				const param = {id: this.orderGoodsId}
				this.$u.api.apiAfterSaleGoods(param).then(result => {
					if (result.code === 0) {
						this.orderGoods = result.data.goods
						this.reasonList = result.data.reason
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading afterSale error')
					}
				})
			},
			
			/**
			 * 发起售后申请
			 */
			onApply() {
				if (this.reasonIndex === -1) {
					return this.$showToast('请选择退款原因')
				}
				if (!this.remark) {
					return this.$showToast('请描述退款原因')
				}
				const param = {
					id: this.orderGoodsId,
					refund_type: this.refundType,
					reason: this.reasonList[this.reasonIndex],
					remark: this.remark,
					images: this.images[0] || ''
				}
		
				this.$u.api.apiAfterSaleApply(param).then(result => {
					if (result.code === 0) {
						this.$showSuccess(result.msg)
						uni.navigateTo({
							url: '/pages/order/after_sale_list/after_sale_list'
						})
					} else {
						this.$showToast(result.msg)
					}
				})
			},
			
			/**
			 * 显示原因选择
			 */
			onShowReason() {
				this.showReason = true
			},
			
			/**
			 * 选择售后原因
			 */
			onRadioChange(e) {
				this.reasonIndex = e.detail.value
				this.showReason = false
			},
			
			/**
			 * 选择退款类型
			 */
			onSelectRefundType(type) {
				this.refundType = type
			}
		}
	}
</script>

<style lang="scss">
	// 售后商品部件样式
	.index-product-widget {
		margin: 20rpx;
		padding: 10rpx 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.product-item {
			display: flex;
			padding: 10rpx 0;
		}
	}
	
	// 售后方式部件样式
	.index-method-widget {
		margin: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.method-item {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 20rpx;
		}
	}
	
	// 售后原因部件样式
	.index-reason-widget {
		margin: 20rpx;
		padding: 0 5rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
	}
</style>
