<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 优惠券部件 -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">
			<view class="index-coupon-widget">
				<view class="coupon-item" :class="item.is_receive===1?'failure':''" v-for="(item, index) in couponList" :key="index">
					<view class="mod-header-region u-flex">
						<view class="coupon-item__left">
							<block v-if="item.type === 10">
								<wait-price amount="50" :fontColor="item.is_receive===1?'#FFF':'#fe574e'" fontWeight="bold" mainSize="58rpx" minorSize="30rpx"></wait-price>
							</block>
							<block v-if="item.type === 20">
								<view class="u-font-weight" :style="{color:item.is_receive===1?'#FFF':'#fe574e'}">
									<text style="font-size:58rpx;">{{item.discount}}</text>
									<text class="u-margin-left-4">折</text>
								</view>
							</block>
							<view class="u-font-24 u-margin-top-16" :style="{color:item.is_receive===1?'#FFF':'#c36968'}">{{item.use_condition}}</view>
						</view>
						<view class="coupon-item__right">
							<view class="u-font-30 u-line-2" :style="{color:item.is_receive===1?'#FFF':'#514545'}">{{item.name}}</view>
							<view class="condition">
								<text class="text u-line-2">{{item.use_goods_type}}</text>
							</view>
							<view class="failure-time">
								{{item.use_expire_time}}
							</view>
						</view>
					</view>
					<view class="mod-footer-region u-flex u-row-between">
						<view class="u-flex-1">
							<u-collapse 
								hover-class="none"
								:arrow-color="item.is_receive===0?'#333':'#FFF'"
								:head-style="{color:item.is_receive===0?'#333':'#FFF', fontSize:'28rpx', justifyContent: 'flex-start'}"
								:body-style="{color:item.is_receive===0?'#999':'#FFF', fontSize:'22rpx', lineHeight:'32rpx'}">
								<u-collapse-item title="查看详情" >
									{{item.explain}}
								</u-collapse-item>
							</u-collapse>
						</view>
						<view class="u-margin-left-30">
							<view class="btn" v-if="!item.is_receive" @click="onReceive(item.id, index)">领取</view>
							<view class="btn" v-else>已领取</view>
						</view>
					</view>
				</view>
			</view>
		</mescroll-body>
	
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
	import MescrollMixin from "@/components/mescroll-uni/mescroll-mixins"
	import MescrollBody from "@/components/mescroll-uni/mescroll-body"
	export default {
		mixins: [MescrollMixin],
		components: {MescrollBody},
		data() {
			return {
				// 首次加载
				isFirstLoading: false,
				// 优惠券列表
				couponList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 10,
					empty: {
						icon: '/static/empty/empty_coupon.png',
						tip: '暂无优惠券'
					}
				}
			}
		},
		methods: {
			/**
			 * 获取优惠券
			 */
			upCallback(page) {
				this.$u.api.apiCouponList({
					page: page.num,
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					
					if (page.num == 1) this.couponList = []
					this.couponList = this.couponList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},
			
			/**
			 * 领取优惠券
			 */
			onReceive(id, index) {
				const param = {id: id}
				this.$u.api.apiCouponReceive(param).then(result => {
					if (result.code === 0) {
						this.couponList[index].is_receive = 1
						return this.$showSuccess(result.msg)
					}
					this.$showToast(result.msg)
				})
			}
		}
	}
</script>

<style lang="scss">

	page {
		background-color: #FFFFFF;
		
	}

	// 优惠券组件样式
	.index-coupon-widget {
		.coupon-item {
			margin: 20rpx;
			border-radius: 12rpx;
			background-color: #fef4f3;
			&.failure {
				background-color: #d2d2d2;
				.condition .text {
					color: #FFFFFF !important;
					border-color: #FFFFFF !important;
				}
				.failure-time {
					color: #FFFFFF !important;
				}
				.mod-footer-region {
					color: #FFFFFF;
					.btn {
						color: #CFCFCF;
						background-color: #FFFFFF !important;
					}
				}
			}
			.mod-header-region {
				padding: 24rpx 0;
				margin: 0 20rpx;
				min-height: 180rpx;
				border-bottom: 1px dashed #E5E5E5;
				.coupon-item__left {
					display: flex;
					align-items: center;
					flex-direction: column;
					margin-right: 10rpx;
					width: 170rpx;
					color: #fe574e;
				}
				.coupon-item__right {
					height: 100%;
					display:flex;
					flex: 1;
					flex-flow: column;
					justify-content: space-between;
					.condition {
						margin: 20rpx 0;
						.text {
							display: inline-block;
							font-size: 22rpx;
							color: #ED5758;
							line-height: 20rpx;
							padding: 4rpx;
							padding-bottom: 2rpx;
							border-radius: 6rpx;
							border: 1rpx solid #c0676b;
						}
					}
					.failure-time {
						font-size: 24rpx;
						line-height: 20rpx;
						color: #766d6e;
					}
				}
			}
			.mod-footer-region {
				position: relative;
				padding: 20rpx 0;
				margin: 0 20rpx;
				font-size: 28rpx;
				&::before {
					position: absolute;
					top: -15rpx;
					left: -35rpx;
					content: "";
					width: 30rpx;
					height: 30rpx;
					border-radius: 50%;
					background-color: #FFFFFF;
				}
				&::after {
					position: absolute;
					top: -15rpx;
					right: -35rpx;
					content: "";
					width: 30rpx;
					height: 30rpx;
					border-radius: 50%;
					background-color: #FFFFFF;
				}
				.btn {
					line-height: 52rpx;
					height: 52rpx;
					width: 120rpx;
					color: #FFFFFF;
					font-size: 24rpx;
					text-align: center;
					text-align: center;
					box-sizing: border-box;
					border-radius: 50rpx;
					background-color: #fc5835;
					&.plain {
						color: #ccc;
						background-color: #fff;
						border: 1px solid currentColor;
					}
				}
			}
		}
	}

</style>
