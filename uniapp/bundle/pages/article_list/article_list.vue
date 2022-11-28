<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 选项卡部件 -->
		<u-tabs class="index-tabs-widget"
			:list="tabList" :is-scroll="true" :current="tabIndex"
			active-color="#FF5058" duration="0" @change="onSwitchTab"
		></u-tabs>

		<!-- 文章列表部件 -->
		<mescroll-body :top="100" ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :up="upOption">
			<view class="index-article-widget">
				<view class="article-item" v-for="(item, index) in articleList" :key="index"
					@click="$toPage('/bundle/pages/article_detail/article_detail?id='+item.id)">
					<view class="u-flex u-row-between">
						<view class="u-margin-right-20">
							<view class="title u-line-2">{{item.title}}</view>
							<view class="intro u-line-2">{{item.intro}}</view>
						</view>
						<u-image :lazy-load="true" width="240rpx" height="180rpx" :src="item.image" style="flex-shrink: 0;"></u-image>
					</view>
					<view class="u-flex u-row-between u-margin-top-18">
						<view class="u-font-24 u-color-muted">发布时间: {{item.create_time}}</view>
						<view class="u-font-24 u-color-muted">{{item.views}}人浏览</view>
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
				// 当前选项卡下标
				tabIndex: 0,
				// 选项卡数据列表
				tabList: [
					{id:0, name: '全部'}
				],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 1,
					empty: {
						icon: '/static/empty/empty_null.png',
						tip: '暂无资讯'
					}
				},
				// 文章列表数据
				articleList: []
			}
		},
		methods: {
			/**
			 * 获取文章
			 */
			upCallback(page) {
				this.$u.api.apiArticleList({
					cid: this.tabList[this.tabIndex].id || 0,
					page: page.num
				}).then(result => {
					const tabs    = result.data.tabs
					const lists   = result.data.data
					const total	  = result.data.total
					
					if (page.num == 1) this.articleList = []
					this.tabList     = tabs
					this.articleList = this.articleList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},
			
			/**
			 * 切换选项卡
			 */
			onSwitchTab(index) {
				this.tabIndex = index
				this.mescroll.scrollTo(0, 0)
				this.mescroll.resetUpScroll()
				this.upCallback({num: 1})
			}
		}
	}
</script>

<style lang="scss">
	// 文章选项卡部件样式
	.index-tabs-widget {
		position: fixed;
		top: 0;
		z-index: 1000;
		width: 100%;
	}
	
	// 文章列表部件样式
	.index-article-widget {
		.article-item {
			padding: 20rpx;
			background-color: #FFFFFF;
			border-bottom: 1rpx dashed #F2F2F2;
			&:last-child {
				border-bottom: 0;
			}
			.title {
				font-size: 32rpx;
				font-weight: bold;
				color: #333333;
				margin-bottom: 20rpx;
			}
			.intro {
				font-size: 26rpx;
				color: #666666;
			}
		}
	}
</style>
