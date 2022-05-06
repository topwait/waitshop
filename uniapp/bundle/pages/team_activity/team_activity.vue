<template>
	<view class="container">
		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 拼团活动部件 -->
		<u-image src="/bundle/static/team_bg.png" :lazy-load="true" width="100%" height="380rpx"></u-image>
		<view class="index-team-widget">
			<view class="team-item" v-for="(item, index) in teamList" :key="index" 
				@click="$toPage('/bundle/pages/team_detail/team_detail?id='+item.id+'&goods_id='+item.goods_id)">
				<u-image :src="item.image" width="180rpx" height="180rpx" border-radius="6rpx"></u-image>
				<view class="goods-info">
					<view class="u-font-28 u-line-2 u-color-normal">{{item.name}}</view>
					<view class="u-flex u-margin-top-16">
						<view class="group">
							<view class="group-icon">
								<u-image src="/bundle/static/team_people.png" width="30rpx" height="30rpx"></u-image>
							</view>
							<view class="u-color-major u-margin-lr-10 u-font-xs">{{item.people_num}}人团</view>
						</view>
						<view class="u-font-24 u-color-lighter u-margin-left-20">已拼{{item.team_volume}}件</view>
					</view>
					<view class="u-flex u-row-between u-margin-top-16">
						<view class="u-flex">
							<wait-price :amount="item.min_team_price" mainSize="34rpx" minorSize="26rpx" class="u-margin-right-10"></wait-price>
							<wait-price :amount="item.max_price" mainSize="24rpx" :lineThrough="true" fontColor="#999"></wait-price>
						</view>
						<view class="go-team">去拼团</view>
					</view>
				</view>
			</view>
		</view>
		
		<view class="u-text-center u-color-white" style="margin-top: 200rpx;" v-if="teamList.length <= 0">暂无拼团</view>

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
				// 拼团列表数据
				teamList: [],
				// 当前页码
				pageNo: 0,
				// 是否是最后一页
				isLastPage: false
			}
		},
		onShow() {
			this.getTeamList()
		},
		/**
		 * 上拉刷新
		 */
		onReachBottom() {
			if (!this.isLastPage) {
				this.getTeamList(this.pageNo + 1)
			}
		},
		methods: {
			/**
			 * 获取拼团活动
			 */
			getTeamList(pageNo=1) {
				const param = {page: pageNo}
				this.$u.api.apiTeamList(param).then(result => {
					if (result.code === 0) {
						const res = result.data
						this.teamList = mergeNewData(res.data, this.teamList, pageNo)
						this.pageNo = res.current_page
						if (res.last_page == res.current_page) {
							this.isLastPage = true
						}
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					}  else {
						this.$showToast('loading team error')
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	// 容器样式
	.container {
		min-height: 100vh;
		background-color: #FF2C3C;
	}
	
	// 拼团列表部件样式
	.index-team-widget {
		.team-item {
			display: flex;
			margin: 20rpx;
			padding: 30rpx 20rpx;
			border-radius: 10rpx;
			background-color: #FFFFFF;
			.goods-info {
				width: 100%;
				margin-left: 20rpx;
				.group {
					display: flex;
					align-items: center;
					border: 1px solid #FF2C3C;
					border-radius: 4rpx;
					.group-icon {
						padding: 2rpx 6rpx;
						border-radius: 0 4rpr 4rpx 0;
						background: linear-gradient(90deg, rgba(249, 95, 47, 1) 0%, rgba(255, 44, 60, 1) 100%);
					}
				}
				.go-team {
					font-size: 28rpx;
					color: #FFFFFF;
					width: 154rpx;
					height: 62rpx;
					line-height: 62rpx;
					text-align: center;
					border-radius: 31rpx;
					background: linear-gradient(#f95f2f 0%, #ff2c3c 100%);
				}
			}
		}
	}
</style>
