<template>
	<view class="condition">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 订单信息部件 -->
		<view class="index-writeoff-widget">
			<view class="wrap-goods-mode">
				<view class="goods-item" v-for="(item, index) in detail.orderGoods" :key="index"
					@click="$toPage('/pages/goods/detail/detail?id='+item.goods_id)">
					<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="item.image" style="flex-shrink: 0;"></u-image>
					<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-24" >
						<view class="u-line-2 u-font-28">{{item.name}}</view>
						<view class="u-font-24 u-color-muted">{{item.spec_value_str}}</view>
						<view class="u-flex u-row-between" style="width: 100%;">
							<view class="u-font-weight u-color-major"><text class="u-font-22">￥</text>{{item.sell_price}}</view>
							<view><text class="u-font-22">x</text>{{item.count}}</view>
						</view>
					</view>
				</view>
			</view>
			<view class="wrap-dl-widget" v-if="detail.address_snap">
				<view class="dd">
					<view class="">客户姓名</view>
					<view class="">{{detail.address_snap.contact}}</view>
				</view>
				<view class="dd">
					<view>客户手机</view>
					<view>{{detail.address_snap.mobile}}</view>
				</view>
				<view class="dd">
					<view>核销状态</view>
					<view class="u-font-weight u-color-success" v-if="detail.pick_up_status">已核销</view>
					<view class="u-font-weight u-color-danger" v-else>未核销</view>
				</view>
				<view class="dd" v-if="detail.pick_up_status">
					<view>核销人员</view>
					<view class="u-font-weight u-color-success">{{detail.operate}}</view>
				</view>
				<view class="dd" v-if="detail.pick_up_status">
					<view>核销时间</view>
					<view>{{detail.verify_time}}</view>
				</view>
				<view class="dd">
					<view>订单状态</view>
					<view v-if="!detail.is_after_sale">{{detail.order_status}}</view>
					<view class="u-font-weight u-color-danger" v-else>售后中</view>
				</view>
				<view class="dd">
					<view>订单类型</view>
					<view>{{detail.order_type}}</view>
				</view>
				<view class="dd">
					<view>购买份数</view>
					<view>{{detail.total_num}}</view>
				</view>
				<view class="dd">
					<view>合计金额</view>
					<view>￥{{detail.total_amount}}</view>
				</view>
				<view class="dd">
					<view>实付金额</view>
					<view class="u-color-major">￥{{detail.paid_amount}}</view>
				</view>
			</view>
		</view>
		
		<!-- 按钮部件 -->
		<view class="btn" v-if="!detail.pick_up_status" @click="onSubmit">确认核销</view>
		<view class="btn ok" v-else>已核销</view>
		
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
				// 核销码
				code: 0,
				// 订单详情
				detail: []
			}
		},
		onLoad(options) {
			this.code = options.code
			this.getWriteoffDetail()
		},
		methods: {
			/**
			 * 获取核销信息
			 */
			getWriteoffDetail() {
				const param = {code: this.code}
				this.$u.api.apiStoreWriteOffDetail(param).then(result => {
					if (result.code === 0) {
						this.detail = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast(result.msg)
						setTimeout(function() {
							uni.navigateBack()
						}, 1000)
					}
				})
			},
			
			/**
			 * 提交核销
			 */
			onSubmit() {
				const that = this
				uni.showModal({
				    title: '提示',
				    content: '您确定要核销订单吗？',
					confirmColor: '#FF5058',
				    success: function (res) {
						if (!res.confirm) return false
						console.log(that.detail)
						const param = {id: that.detail.id}
						that.$u.api.apiStoreWriteOffSubmit(param).then(result => {
							if (result.code === 0) {
								that.$showSuccess(result.msg)
								that.getWriteoffDetail()
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
	// 核销详情样式
	.index-writeoff-widget {
		padding: 20rpx 30rpx;
		margin: 20rpx 0;
		background-color: #FFF;
		.wrap-goods-mode {
			.goods-item {
				display: flex;
				padding: 10rpx 0;
			}
		}
		.wrap-dl-widget {
			margin-top: 40rpx;
			.dd {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 26rpx 0;
				font-size: 30rpx;
				color: #555;
			}
		}
	}
	
	// 按钮样式
	.btn {
		margin: 30rpx;
		text-align: center;
		font-weight: bold;
		color: #FFF;
		padding: 30rpx 0;
		border-radius: 12rpx;
		background-color: $-color-main;
		&.ok {
			background-color: #CCC;
		}
	}
</style>
