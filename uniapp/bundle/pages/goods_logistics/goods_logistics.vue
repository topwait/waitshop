<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 商品部件 -->
		<view class="index-goods-widget" v-if="traces.order">
			<view class="image">
				<image class="img" :lazy-load="true" :src="traces.order.image"></image>
			</view>
			<view>
				<view class="u-font-30 u-font-weight u-margin-bottom-10">{{traces.order.tips}}</view>
				<view class="u-font-26 u-margin-bottom-10">物流公司：{{traces.order.express_name}}</view>
				<view class="u-font-26">快递单号：{{traces.order.waybill_no}}</view>
			</view>
		</view>
		
		<!-- 物流部件 -->
		<view class="index-logistics-widet" v-if="traces.addr">
			<u-time-line>
				<!-- 收货人信息 -->
				<u-time-line-item nodeTop="2">
					<template v-slot:node>
						<view class="u-node" style="background-color: #FF2C3C;">收</view>
					</template>
					<template v-slot:content>
						<view>
							<view class="u-order-title">{{traces.addr.contact}} {{traces.addr.mobile}}</view>
							<view class="u-order-desc">{{traces.addr.region}}{{traces.addr.address}}</view>
						</view>
					</template>
				</u-time-line-item>
				<!-- 交易完成 -->
				<u-time-line-item nodeTop="2">
					<template v-slot:node>
						<view class="u-node" style="background-color: #FF2C3C;">
							<u-icon name="pushpin-fill" size="24" color="#FFF"></u-icon>
						</view>
					</template>
					<template v-slot:content>
						<view>
							<view class="u-order-title">{{traces.finish.title}}</view>
							<view class="u-order-desc">{{traces.finish.tips}}</view>
							<view class="u-order-time">{{traces.finish.time}}</view>
						</view>
					</template>
				</u-time-line-item>
				<u-time-line-item nodeTop="2" v-for="(item, index) in traces.traces" :key="index">
					<template v-slot:content>
						<view>
							<view class="u-order-title" v-if="index===0">运输中</view>
							<view class="u-order-desc" v-if="item[2]">{{item[2]}}</view>
							<view class="u-order-time" v-if="item[1]">{{item[1]}}</view>
						</view>
					</template>
				</u-time-line-item>
				<!-- 已发货 -->
				<u-time-line-item nodeTop="2">
					<template v-slot:node>
						<view class="u-node">
							<u-icon name="bag" size="24" color="#FFF"></u-icon>
						</view>
					</template>
					<template v-slot:content>
						<view>
							<view class="u-order-title">{{traces.shipment.title}}</view>
							<view class="u-order-desc">{{traces.shipment.tips}}</view>
							<view class="u-order-time">{{traces.shipment.time}}</view>
						</view>
					</template>
				</u-time-line-item>
				<!-- 已支付 -->
				<u-time-line-item nodeTop="2">
					<template v-slot:node>
						<view class="u-node">
							<u-icon name="rmb-circle" size="24" color="#FFF"></u-icon>
						</view>
					</template>
					<template v-slot:content>
						<view>
							<view class="u-order-title">{{traces.pay.title}}</view>
							<view class="u-order-desc">{{traces.pay.tips}}</view>
							<view class="u-order-time">{{traces.pay.time}}</view>
						</view>
					</template>
				</u-time-line-item>
				<!-- 已下单 -->
				<u-time-line-item nodeTop="2">
					<template v-slot:node>
						<view class="u-node">
							<u-icon name="order" size="24" color="#FFF"></u-icon>
						</view>
					</template>
					<template v-slot:content>
						<view>
							<view class="u-order-title">{{traces.buy.title}}</view>
							<view class="u-order-desc">{{traces.buy.tips}}</view>
							<view class="u-order-time">{{traces.buy.time}}</view>
						</view>
					</template>
				</u-time-line-item>
			</u-time-line>
		</view>

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
				// 订单ID
				orderId: 0,
				// 物流轨迹信息
				traces: []
			}
		},
		onLoad(options) {
			this.orderId = parseInt(options.id)
			this.getOrderTraces()
		},
		methods: {
			getOrderTraces() {
				const param = {id: this.orderId}
				this.$u.api.apiOrderTraces(param).then(result => {
					if (result.code === 0) {
						this.traces = result.data
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
	// 商品部件样式
	.index-goods-widget {
		display: flex;
		align-items: center;
		margin: 20rpx 0;
		padding: 20rpx;
		background-color: #FFFFFF;
		.image {
			width: 160rpx;
			height: 160rpx;
			padding: 10rpx;
			margin-right: 10rpx;
			border: 1rpx dashed #eee;
			.img {
				width: 100%;
				height: 100%;
			}
		}
	}
	
	// 物流部件样式
	.index-logistics-widet {
		padding: 20rpx 40rpx;
		background-color: #FFFFFF;
		.u-node {
			width: 44rpx;
			height: 44rpx;
			color: #FFFFFF;
			border-radius: 100rpx;
			display: flex;
			justify-content: center;
			align-items: center;
			background: #ededed;
		}
		.u-order-title {
			color: #999;
			font-weight: bold;
			font-size: 30rpx;
		}
		.u-order-desc {
			color: rgb(150, 150, 150);
			font-size: 28rpx;
			margin-bottom: 6rpx;
		}
		.u-order-time {
			color: rgb(200, 200, 200);
			font-size: 26rpx;
		}
	}
	
</style>
