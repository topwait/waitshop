<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 选项卡部件 -->
		<view class="index-tabs-widget">
			<u-tabs
				:list="tabList" :is-scroll="false" :current="tabIndex"
				active-color="#FF5058" duration="0" @change="onSwitchTab"
			></u-tabs>
		</view>

		<!-- 订单部件 -->
		<view class="index-order-widget">
			<mescroll-body :top="80" ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">
				<view class="pack-order-item" v-for="(item, index) in orderList" :key="index">
					<view class="u-flex u-row-between u-padding-tb-20 u-border-bottom">
						<view class="u-flex u-font-28 u-color-lighter">
							<view class="activity" v-if="item.order_type===2">秒杀</view>
							<view class="activity" v-if="item.order_type===3">拼团</view>
							<view class="activity" v-if="item.order_type===4">砍价</view>
							<view>订单号: {{item.order_sn}}</view>
						</view>
						<view class="u-font-28" :class="item.order_status !== 4 ? 'u-color-major' : 'u-color-lighter'">
							<u-count-down v-if="tabIndex === 1 && item.extend.surplus_close_time > 0"
								font-size="28" separator-color="#FF5058" 
								color="#FF5058" @end="onDownTimeEnd(item.id)"
								:data-id="item.id"
								:timestamp="item.extend.surplus_close_time">
							</u-count-down>
							<text v-if="tabIndex === 1">, </text>
							{{item.extend.order_status}}
						</view>
					</view>
					<view class="u-padding-tb-10">
						<view class="order-product-item" v-for="(product, i) in item.orderGoods" :key="i" @click="$toPage('/pages/order/detail/detail?id='+item.id)">
							<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="product.image" style="flex-shrink: 0;"></u-image>
							<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-24 u-flex-1">
								<view class="u-line-2 u-font-28">{{product.name}}</view>
								<view class="u-font-24 u-color-muted u-padding-tb-6">{{product.spec_value_str}}</view>
								<view class="u-flex u-row-between" style="width: 100%;">
									<view class="u-font-weight u-color-major"><text class="u-font-22">￥</text>{{product.sell_price}}</view>
									<view><text class="u-font-22">x</text>{{product.count}}</view>
								</view>
							</view>
						</view>
					</view>
					<view class="u-flex u-row-right u-padding-tb-20 u-font-24 u-border-bottom">
						<text class="u-color-muted">共{{item.orderGoods.length}}件商品，实付款: </text>
						<text class="u-font-28"><text class="u-font-22">￥</text>{{item.paid_amount}}</text>
					</view>
					<view class="order-button-group" v-if="item.order_status !== 1">
						<view class="button-item" v-if="item.order_status === 3 || item.order_status === 4" @click="onDelete(item.id)">删除订单</view>
						<view class="button-item" v-if="item.order_status === 0 || item.extend.is_allow_cancel" @click="onCancel(item.id)">取消订单</view>
						<view class="button-item button-item--main" v-if="item.order_status === 0" @tap="onShowPayment(item.id)">立即支付</view>
						<view class="button-item button-item--main" 
							v-if="item.order_status === 2 || item.order_status === 3" 
							@click="$toPage('/bundle/pages/goods_logistics/goods_logistics?id='+item.id)">
							查询物流
						</view>
						<view class="button-item button-item--main"
							v-if="item.order_status === 2" 
							@click="onConfirm(item.id)">
							确认收货
						</view>
					</view>
					<view class="order-button-group" v-if="item.order_status == 1 && item.delivery_type===2">
						<view class="button-item" v-if="item.extend.is_allow_cancel" @click="onCancel(item.id)">取消订单</view>
						<view class="button-item button-item--main" 
							v-if="item.order_status === 1" 
							@click="$toPage('/pages/order/detail/detail?id='+item.id)">
							查看提货码
						</view>
					</view>
				</view>
			</mescroll-body>
		</view>
		
		<!-- 支付选择组件 -->
		<wait-popup-payment
			:show="showPayment"
			:orderId="payOrderId"
			@close="onClosePayment"
			@pay="onPayment"
		></wait-popup-payment>

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
				isFirstLoading: true,
				// 当前选项卡下标
				tabIndex: 0,
				// 选项卡数据列表
				tabList: [
					{name: '全部', count: 0},
					{name: '待付款', count: 0}, 
					{name: '待发货', count: 0}, 
					{name: '待收货', count: 0},
					{name: '已完成', count: 0},
				],
				// 订单列表数据
				orderList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 1,
					empty: {
						icon: '/static/empty/empty_trade.png',
						tip: '暂无订单'
					}
				},
				// 显示支付窗
				showPayment: false,
				// 待支付的订单
				payOrderId: 0
			}
		},
		onLoad(options) {
			this.tabIndex = parseInt(options.tab)
		},
		methods: {
			/**
			 * 上拉加载
			 */
			upCallback(page) {
				this.$u.api.apiOrderList({
					type: this.tabIndex,
					page: page.num
				}).then(result => {
					const tabs    = result.data.tabs
					const lists   = result.data.data
					const total	  = result.data.total
					
					if (page.num == 1) {
						this.orderList = []
						if (this.tabIndex === 1 && lists.length > 0 && lists['automaticCancelDuration'] > 0) {
							lists.forEach(item => {
								if (item['extend']['surplus_close_time'] <= 0) {
									this.onDownTimeEnd(item['id'])
								}
							})
						}
					}

					this.tabList   = tabs
					this.orderList = this.orderList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
					
				}).catch(() => {
					this.mescroll.endErr()
				})
			},
			
			/**
			 * 删除订单
			 */
			onDelete(id) {
				const that = this
				uni.showModal({
				    title: '提示',
				    content: '您确定要删除订单吗？',
					confirmColor: '#FF5058',
				    success: function (res) {
						if (!res.confirm) return false
						const param = {id: id}
						that.$u.api.apiOrderDelete(param).then(result => {
							if (result.code === 0) {
								that.$showSuccess(result.msg)
								that.upCallback({num: 1})
							} else {
								that.$showToast(result.msg)
							}
						})
				    }
				})
			},
			
			/**
			 * 取消订单
			 */
			onCancel(id) {
				const that = this
				uni.showModal({
				    title: '提示',
				    content: '您确定要取消订单吗？',
					confirmColor: '#FF5058',
				    success: function (res) {
						if (!res.confirm) return false
						const param = {id: id}
						that.$u.api.apiOrderCancel(param).then(result => {
							if (result.code === 0) {
								that.$showSuccess(result.msg)
								that.upCallback({num: 1})
							} else {
								that.$showToast(result.msg)
							}
						})
				    }
				})
			},
			
			/**
			 * 确认收货
			 */
			onConfirm(id) {
				const that = this
				uni.showModal({
				    title: '提示',
				    content: '您确定要确认收货吗？',
					confirmColor: '#FF5058',
				    success: function (res) {
						if (!res.confirm) return false
						const param = {id: id}
						that.$u.api.apiOrderConfirm(param).then(result => {
							if (result.code === 0) {
								that.$showSuccess(result.msg)
								that.upCallback({num: 1})
							} else {
								that.$showToast(result.msg)
							}
						})
				    }
				})
			},
			
			/**
			 * 关闭超时订单
			 */
			onDownTimeEnd(orderId) {
				this.$u.api.apiOrderEnd({
					id: orderId,
				}).then(result => {
					if (result.code === 0) {
						this.upCallback({num: 1})
					}
				})
			},
			
			/**
			 * 切换选项卡
			 */
			onSwitchTab(index) {
				this.tabIndex = index
				this.orderList = []
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			},
			
			/**
			 * 弹出支付窗口
			 */
			onShowPayment(id) {
				this.payOrderId = id
				this.showPayment = true
			},
			
			/**
			 * 关闭支付弹窗
			 */
			onClosePayment() {
				this.showPayment = false
			},
			
			/**
			 * 去支付
			 */
			onPayment(e) {
				const that = this
				const {payWayType, orderId} = e
				that.showPayment = false
				uni.showLoading({title: '处理中'});
				setTimeout(function(){
					uni.hideLoading();
					that.$u.api.apiPaymentPay({
						from: "order", 
						client: 6,
						pay_way: payWayType,
						order_id: orderId
					}).then(result => {
						console.log(result)
						switch(result.code) {
							case 1:
								that.$showToast(result.msg)
								break;
							case 2100: //余额支付
								uni.redirectTo({
									url: '/pages/order/pay_results/pay_results?id='+that.orderId
								})
								break;
							case 2200: //微信支付
								wxpay(data).then(res => {
									uni.redirectTo({
										url: '/pages/order/pay_results/pay_results?id='+that.orderId
									})
								})
								break;
							case 2300: //支付宝支付
								// alipay(data).then(res => {})
								break;
						}
					})
				}, 2000)
			}
		}
	}
</script>

<style lang="scss">
	// 选项卡部件样式
	.index-tabs-widget {
		position: fixed;
		top: 0;
		z-index: 2000;
		width: 100%;
	}

	
	// 订单部件样式
	.index-order-widget {
		.pack-order-item {
			margin: 20rpx;
			padding: 0 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			&:last-child{margin-bottom: 0 !important;}
			.activity {
				padding: 0 10rpx;
				height: 38rpx;
				line-height: 38rpx;
				margin-right: 10rpx;
				color: #FF5058;
				font-size: 22rpx;
				border: 1rpx solid #FF5058;
				border-radius: 4rpx;
			}
			.order-product-item {
				display: flex;
				padding: 10rpx 0;
			}
			.order-button-group {
				display: flex;
				justify-content: flex-end;
				padding: 30rpx 0;
				.button-item {
					width: 85px;
					height: 60rpx;
					line-height: 60rpx;
					font-size: 24rpx;
					color: #616161;
					text-align: center;
					margin-right: 30rpx;
					border-radius: 40rpx;
					border: 1px #c7c7c7 solid;
					&:last-child { margin-right: 0; }
					&--main {color: #FF5058; border: 1px #FF5058 solid; }
				}
			}
		}
	}
</style>
