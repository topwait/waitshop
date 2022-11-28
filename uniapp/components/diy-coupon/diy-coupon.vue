<template>
	<view class="diy-coupon" :style="{
			background: itemConfig.background,
			padding: itemConfig.paddingTop + 'px '+' 10px',
			marginLeft: itemConfig.marginLeft+'px',
			marginRight: itemConfig.marginLeft+'px',
			marginTop: itemConfig.marginTop+'px',
			borderRadius: itemConfig.borderRadius+'px'
		}">
		<view class="coupon-item" v-for="(item, index) in itemData" :key="index" 
			@click="onReceive(item.id, index, item.is_receive)"
			:style="{background: item.is_receive ? itemConfig.receiveColor : itemConfig.normalColor}">
			<view class="left">
				<wait-price :amount="item.min_price" fontWeight="bold" fontColor="#FFF" mainSize="48rpx" minorSize="24rpx" v-if="item.type===10"></wait-price>
				<view v-if="item.type===20">
					<text class="u-font-weight" style="font-size:48rpx;">{{item.discount}}</text>
					<text class="u-font-weight u-font-24 u-margin-left-2">折</text>
				</view>
				<text class="condition">满100元可用</text>
			</view>
			<view class="right" v-if="item.is_receive===0">
				<view>立</view>
				<view>即</view>
				<view>领</view>
				<view>取</view>
			</view>
			<view class="right" v-if="item.is_receive===1">
				<view>已</view>
				<view>领</view>
				<view>取</view>
			</view>
			<view class="mark after" :style="{background: itemConfig.background}"></view>
			<view class="mark before" :style="{background: itemConfig.background}"></view>
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
		props: {
			itemIndex: String,
			itemConfig: Object,
			itemData: Array
		},
		methods: {
			/**
			 * 领取优惠券
			 */
			onReceive(id, index, isReceive) {
				if (isReceive) return false
				const param = {'id': id}
				this.$u.api.apiCouponReceive(param).then(result => {
					if (result.code === 0) {
						// todo 修改领取状态		
					}
					return this.$showToast(result.msg)
				})
			}
		}
	}
</script>

<style lang="scss">
	.diy-coupon {
		display: flex; 
		align-items: center;
		.coupon-item {
			position: relative;
			display: flex; 
			position: relative; 
			width: 304rpx; 
			height: 152rpx; 
			color: #FFF; 
			border-radius: 10rpx; 
			margin-right: 20rpx;
			.left {
				z-index: 2000;
				width: 240rpx; 
				height: 152rpx; 
				display: flex; 
				flex-direction: column; 
				align-items: center; 
				justify-content: center;
			}
			.right {
				flex: 1; 
				display: flex; 
				align-items: center; 
				justify-content: center; 
				flex-direction: column; 
				font-size: 24rpx; 
				border-left: 1px dashed #fff;
			}
			.mark {
				width: 20rpx; 
				height: 20rpx; 
				border-radius: 50%; 
				background: #f8f8f8;
			}
			.after {
				position: absolute; 
				right: 54rpx; 
				top: -10rpx; 
			}
			.before {
				position: absolute; 
				right: 54rpx; 
				bottom: -10rpx; 
			}
		}
	}
</style>
