<template>
	<view class="diy-teamGoods" :style="{
			marginLeft: itemConfig.marginLeft+'px',
			marginRight: itemConfig.marginLeft+'px',
			marginTop: itemConfig.marginTop+'px',
			borderRadius: itemConfig.borderRadius+'px',
			background: itemConfig.background
		}">
		<view class="title-wrapper">
			<view class="u-flex u-row-between">
				<u-image width="36rpx" height="36rpx" :src="itemConfig.icon" v-if="itemConfig.icon"></u-image>
				<text class="u-margin-left-10 u-font-32 u-font-weight">拼团活动</text>
			</view>
			<view class="u-font-24 u-color-muted" @click="onJump('/bundle/pages/team_activity/team_activity')">
				<text>更多</text>
				<u-icon name="arrow-right" size="24" color="#999"></u-icon>
			</view>
		</view>
		<view class="list-wrapper">
			<view class="item" v-for="(item, index) in itemData" :key="index"
				@click="onJump('/bundle/pages/team_detail/team_detail?id='+item.id+'&goods_id='+item.goods_id)">
				<view class="img-box">
					<u-image width="100%" height="100%" border-radius="12rpx" :src="item.image"></u-image>
					<view class="join" v-if="itemConfig.show.teamJoin">{{item.total_join_number}}人参团</view>
				</view>
				<view class="u-line-2 u-margin-tb-10">{{item.name}}</view>
				<view class="u-flex u-row-between u-margin-bottom-4" v-if="itemConfig.show.teamPrice || itemConfig.show.teamGoBtn">
					<wait-price :amount="item.min_team_price" v-if="itemConfig.show.teamPrice"></wait-price>
					<view class="go-team-btn" v-if="itemConfig.show.teamGoBtn">去拼团</view>
				</view>
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
	import { toPage } from '@/utils/tools.js'
	export default {
		props: {
			itemIndex: String,
			itemConfig: Object,
			itemData: Array
		},
		methods: {
			/**
			 * 跳转页面
			 */
			onJump(url) {
				toPage(url)
			}
		}
	}
</script>

<style lang="scss">
	.diy-teamGoods {
		.title-wrapper {
			display: flex;
			align-content: center; 
			justify-content: space-between;
			padding: 26rpx;
		}
		.list-wrapper {
			display: flex;
			flex-wrap: nowrap;
			overflow: hidden;
			overflow-x: auto;
			padding: 0 20rpx 20rpx; 
			.item {
				flex-shrink: 0; 
				width: 31.7%; 
				margin-right: 16rpx;
				border-radius: 12rpx; 
				background: #FFFFFF; 
				.img-box {
					position: relative; 
					width: 100%; 
					height: 220rpx;
					.join {
						position: absolute; 
						left: 6px; 
						top: 6px; 
						width: 70px; 
						height: 16px; 
						line-height: 16px; 
						text-align: center; 
						background: rgba(0,0,0,.1); 
						border-radius: 8px; 
						color: #FFFFFF; 
						font-size: 12px;
					}
				}
				.go-team-btn {
					font-size: 22rpx; 
					color: #FFFFFF; 
					line-height: 24rpx; 
					padding: 8rpx 16rpx; 
					border-radius: 60rpx; 
					background: #f44;
				}
			}
		}
	}
</style>
