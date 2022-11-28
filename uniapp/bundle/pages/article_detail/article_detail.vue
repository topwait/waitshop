<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 文章详细部件 -->
		<view class="index-detail-widget">
			<view class="header u-border-bottom">
				<view class="u-padding-bottom-30">{{articleDetail.title}}</view>
				<view class="u-flex u-row-between">
					<view class="u-font-24 u-color-muted u-font-noweight">发布时间: {{articleDetail.create_time}}</view>
					<view class="u-font-24 u-color-muted u-font-noweight">{{articleDetail.views}}人浏览</view>
				</view>
			</view>
			<view class="content">
				<u-parse :html="articleDetail.content"></u-parse>
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
				// 文章ID
				articleId: 0,
				// 文章详细
				articleDetail: {}
			}
		},
		onLoad(options) {
			this.articleId = parseInt(options.id)
			this.getArticleDetail()
		},
		methods: {
			/**
			 * 获取文章
			 */
			getArticleDetail() {
				this.$u.api.apiArticleDetail({
					id: this.articleId,
				}).then(result => {
					if (result.code === 0) {
						this.articleDetail = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading article error')
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #FFFFFF;
	}
	
	// 文章详细部件样式
	.index-detail-widget {
		.header {
			font-size: 36rpx;
			font-weight: bold;
			color: #333333;
			padding: 30rpx;
		}
		.content {
			margin: 30rpx 20rpx;
		}
	}
</style>
