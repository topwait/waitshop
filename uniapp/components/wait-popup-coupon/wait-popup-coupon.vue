<template>
	<u-popup v-model="showPop" mode="bottom" border-radius="14" :closeable="true" @close="onClose" :safe-area-inset-bottom="true" height="80%">
		<view class="coupon-tabs">
			<view class="tabs-item" :class="tabIndex===0 ? 'on': ''" @click="onSwitchTab(0)">可用优惠券 ({{usableCoupon.length || 0}})</view>
			<view class="tabs-item" :class="tabIndex===1 ? 'on': ''" @click="onSwitchTab(1)">不可用优惠券 ({{unusableCoupon.length || 0}})</view>
		</view>
		<view class="coupon-main">
			<view class="coupon-item u-flex" :class="tabIndex===1 ? 'invalid' : ''" 
				v-for="(item, index) in couponList" :key="index" 
				@click="onSelectCoupon(item.coupon_list_id)">
				
				<view class="coupon-item__left u-flex u-color-white">
					<view class="u-font-weight u-margin-bottom-10">
						<block v-if="item.type === 10">
							<text class="u-font-24">￥</text>
							<text style="font-size: 48rpx;">{{item.reduce_price}}</text>
						</block>
						<block v-else>
							<text style="font-size: 48rpx;">{{item.discount}}</text>
							<text>折</text>
						</block>
					</view>
					<view class="u-font-22 u-text-center">{{item.use_condition}}</view>
				</view>
				
				<view class="coupon-item__right">
					<view class="u-flex u-col-top u-flex-col u-row-between" style="height: 100%;">
						<view class="u-font-weight u-font-32" :class="tabIndex===0 ? 'u-color-normal' : 'u-color-muted'">{{item.name}}</view>
						<view class="u-font-22" :class="tabIndex===0 ? 'u-color-lighter' : 'u-color-muted'">{{item.expiry_tips}}</view>
						<view class="u-font-22" :class="tabIndex===0 ? 'u-color-lighter' : 'u-color-muted'">{{item.use_goods_type}}</view>
					</view>
					<checkbox v-if="tabIndex == 0" 
						:checked="selectId == item.coupon_list_id" 
						class="u-margin-right-20"
					></checkbox>
				</view>
			</view>
			
			<wait-empty v-if="couponList.length <= 0"></wait-empty>
		</view>
		
		<view class="coupon-footer">
			<u-button type="error" 
				shape="circle" size="medium" 
				:custom-style="{width: '100%;'}"
				@click="onConfirmCoupon">确认
			</u-button>
		</view>
		
	</u-popup>
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
		props: {
			show: {
				type: Boolean,
				default: () => { return false }
			},
			usable: {
				type: Array,
				default: () => []
			},
			unusable: {
				type: Array,
				default: () => []
			},
			couponListId: [Number, String]
		},
		data() {
			return {
				// 是否显示
				showPop: false,
				// 选项卡下标
				tabIndex: 0,
				// 选择的优惠券ID
				selectId: 0,
				// 优惠券列表
				couponList: [],
				// 可用优惠列表
				usableCoupon: [],
				// 不可用优惠券列表
				unusableCoupon: []
			}
		},
		watch: {
			show: {
				handler(val) {
					this.showPop = val
				},
				immediate: true,
			},
			usable: {
				immediate: true,
				handler(val) {
					this.couponList = val
					this.usableCoupon = val
				}
			},
			unusable: {
				immediate: true,
				handler(val) {
					this.unusableCoupon = val
				}
			},
			couponListId: {
				immediate: true,
				handler(val) {
					this.selectId = val
				}
			}
		},
		methods: {
			/**
			 * 关闭弹窗
			 */
			onClose() {
				this.$emit('close')
			},
			
			/**
			 * 切换选卡
			 */
			onSwitchTab(index) {
				this.tabIndex = index
				if (this.tabIndex === 0) {
					this.couponList = this.usableCoupon
				} else {
					this.couponList = this.unusableCoupon
				}
			},
			
			/**
			 * 选择优惠券
			 */
			onSelectCoupon(cl_id) {
				if (this.tabIndex == 1) return
				this.selectId = cl_id == this.selectId ? 0 : cl_id
			},
			
			/**
			 * 确认优惠券
			 */
			onConfirmCoupon() {
				this.$emit("change", this.selectId)
			}
		}
	}
</script>

<style lang="scss">
	// 优惠券选项卡
	.coupon-tabs {
		display: flex;
		justify-content: space-between;
		height: 106rpx;
		padding: 26rpx 130rpx;
		padding-bottom: 40rpx;
		font-size: 28rpx;
		color: #999;
		border-bottom: 1px solid rgba(#EEEEEE, 0.5);
		.tabs-item {
			position: relative;
			&.on {color: $-color-major; }
			&.on::before {
				content: "";
				position: absolute;
				bottom: -20rpx;
				width: 80rpx;
				height: 8rpx;
				margin-left: 50rpx;
				border-radius: 20rpx;
				background-color: #FF5058;
			}
		}
	}
	// 优惠券列表
	.coupon-main {
		position: absolute;
		top: 106rpx;
		bottom: 110rpx;
		width: 100%;
		overflow: hidden;
		overflow-y: auto;
		background: #f6f6f6;
		.coupon-item {
			position: relative;
			height: 160rpx;
			background-image: url('/static/coupon_bg.png');
			background-size: 100% 100%;
			margin: 20rpx;
			&.invalid {
				background-image: url('/static/coupon_bg_grey.png');
			}
			&__left {
				flex-direction: column;
				align-items: center;
				justify-content: center;
				margin-right: 20rpx;
				width: 200rpx;
			}
			&__right {
				height: 100%;
				flex: 1;
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 20rpx 0;
			}
			.receive  {
				position: absolute;
				right: 30rpx;
				top: 0rpx;
				width: 99rpx;
				height: 77rpx;
			}
			.btn {
				line-height: 52rpx;
				height: 52rpx;
				position: absolute;
				right: 20rpx;
				bottom: 20rpx;
				width: 120rpx;
				font-size: 24rpx;
				text-align: center;
				padding: 0;
				text-align: center;
				box-sizing: border-box;
				border-radius: 50rpx;
				background-color: red;
				&.plain {
					background-color: #fff;
					color: #ccc;
					border: 1px solid currentColor;
				}
			}
		}
	}
	// 优惠券确定按钮
	.coupon-footer {
		position: absolute;
		bottom: 0;
		width: 100%;
		padding: 20rpx 20rpx;
		background-color: #FFFFFF;
	}
</style>
