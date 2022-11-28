<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 评论商品部件 -->
		<view class="index-product-widget u-flex">
			<u-image width="160rpx" height="160rpx" :lazy-load="true" border-radius="12" :src="orderGoods.image" style="flex-shrink: 0;"></u-image>
			<view class="u-flex u-row-between u-col-top u-flex-col u-padding-left-20">
				<view class="u-line-2 u-font-28">{{orderGoods.name}}</view>
				<view class="u-font-24 u-color-muted u-padding-tb-6">{{orderGoods.spec_value_str}}</view>
				<view class="u-flex u-row-between" style="width: 100%;">
					<view class="u-font-weight"><text class="u-font-22">￥</text>{{orderGoods.actual_price}}</view>
					<view><text class="u-font-22">x</text>{{orderGoods.count}}</view>
				</view>
			</view>
		</view>
		
		<!-- 评星打分部件 -->
		<view class="index-rate-widget">
			<view class="u-margin-lr-20">
				<view class="rate-item">
					<text class="u-margin-right-20">描述相符</text>
					<u-rate :count="5" v-model="goodsComment" size="38"></u-rate>
				</view>
				<view class="rate-item">
					<text class="u-margin-right-20">服务态度</text>
					<u-rate :count="5" v-model="serviceComment" size="38"></u-rate>
				</view>
				<view class="rate-item">
					<text class="u-margin-right-20">配送服务</text>
					<u-rate :count="5" v-model="expressComment" size="38"></u-rate>
				</view>
			</view>
			<view class="textarea">
				<u-input type="textarea" height="180" v-model="content" placeholder="宝贝收到还满意吗，说说你的使用心得。分享给想买的他们吧！！" />
			</view>
			<view class="u-margin-lr-10">
				<wait-uploader dir="comment" :maxUpload="3" :showProgress="true" v-model="images"></wait-uploader>
			</view>
		</view>
		
		<!-- 评论提交部件 -->
		<view class="u-margin-20 u-margin-top-50">
			<u-button type="error" shape="circle" @click="onSubmit">立即评论</u-button>
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
				// 评论订单商品ID
				orderGoodsId: 0,
				// 订单商品数据
				orderGoods: {},
				// 商品描述评分
				goodsComment: 0,
				// 服务描述评分
				serviceComment: 0,
				// 物流描述评分
				expressComment: 0,
				// 评论的内容
				content: '',
				// 评论的图片
				images: []
			}
		},
		onLoad(options) {
			this.orderGoodsId = parseInt(options.id)
			this.getCommentGodos()
		},
		methods: {
			/**
			 * 获取评论商品
			 */
			getCommentGodos() {
				const param = {id: this.orderGoodsId}
				this.$u.api.apiCommentGoods(param).then(result => {
					if (result.code === 0) {
						this.orderGoods = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					}
				})
			},
			
			/**
			 * 提交评论
			 */
			onSubmit() {
				if (!this.goodsComment) return this.$showToast('请给描述相符打分')
				if (!this.serviceComment) return this.$showToast('请给服务态度打分')
				if (!this.expressComment) return this.$showToast('请给物流服务打分')
				const param = {
					order_goods_id: this.orderGoodsId,
					goods_comment: this.goodsComment,
					service_comment: this.serviceComment,
					express_comment: this.expressComment,
					content: this.content,
					images: this.images
				}
				this.$u.api.apiCommentAdd(param).then(result => {
					if (result.code === 0) {
						this.$showSuccess(result.msg)
						uni.navigateTo({
							url: '/pages/order/comment_list/comment_list'
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
	// 评论商品部件样式
	.index-product-widget {
		margin: 20rpx;
		padding: 20rpx;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.rate-item {
			font-size: 30rpx;
			margin-bottom: 16rpx;
			&:last-child { margin-bottom: 0; }
		}
	}
		
	// 评星打分部件样式
	.index-rate-widget {
		margin: 20rpx;
		padding: 20rpx 0;
		border-radius: 12rpx;
		background-color: #FFFFFF;
		.rate-item {
			font-size: 30rpx;
			margin-bottom: 22rpx;
			&:last-child { margin-bottom: 0; }
		}
		.textarea {
			border-radius: 12rpx;
			background-color: #F8F8F8; 
			padding: 10rpx 20rpx; 
			margin: 20rpx;
			margin-top: 30rpx;
		}
	}
</style>
