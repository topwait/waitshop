<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 秒杀活动部件 -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :down="{textColor: '#FFF'}" :up="upOption">	
			<!-- 背景图 -->
			<u-image  src="/bundle/static/seckill_bg.png" :lazy-load="true" width="100%" height="380rpx"></u-image>
			<!-- 活动列表 -->
			<view class="index-seckill-widget">
				<view class="seckill-item" v-for="(item, index) in seckillList" :key="index" 
					@click="$toPage('/bundle/pages/seckill_detail/seckill_detail?id='+item.id+'&goods_id='+item.goods_id)">
					<u-image width="180rpx" height="180rpx" border-radius="6rpx" style="flex-shrink: 0;" :src="item.image"></u-image>
					<view class="u-flex u-flex-1 u-flex-col u-row-between u-col-top u-margin-left-20">
						<view class="u-line-2 u-font-28 u-color-normal">{{item.name}}</view>
						<view class="u-flex" style="width:100%;">
							<view class="u-flex-1">
								<u-line-progress active-color="#FF5058" :percent="item.progress" height="24"></u-line-progress>
							</view>
							<text class="u-margin-left-20 u-font-22 u-color-muted" style="width:108rpx;">已抢{{item.sales_volume}}</text>
						</view>
						<view class="u-flex u-row-between" style="width:100%;">
							<view class="u-flex">
								<wait-price :amount="item.min_seckill_price" :mainSize="'32rpx'" :fontWeight="'bold'"></wait-price>
								<view class="u-margin-left-15">
									<wait-price :amount="item.cost_price" :lineThrough="true" :mainSize="'22rpx'"  :fontColor="'#999'"></wait-price>
								</view>
							</view>
							<view class="go-btn expect" v-if="!item.is_start">敬请期待</view>
							<block v-else>
								<view class="go-btn" v-if="item.total_stock > 0">立即抢购</view>
								<view class="go-btn not" v-if="item.total_stock <= 0">已售罄</view>
							</block>
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
				// 优惠券列表
				seckillList: [],
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 10,
					textColor: "#FFFFFF",
					empty: {
						icon: '',
						tip: '暂无秒杀'
					}
				}
			}
		},
		methods: {
			/**
			 * 获取秒杀活动
			 */
			upCallback(page) {
				this.$u.api.apiSeckillList({
					page: page.num,
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					if (page.num == 1) this.seckillList = []
					
					this.seckillList = this.seckillList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			}
		}
	}
</script>

<style lang="scss">
	// 容器样式
	.container {
		background-color: #FF2C3C;
		.mescroll-empty .empty-tip {
			color: #FFFFFF;
		}
	}

	// 秒杀部件样式
	.index-seckill-widget {
		width: 100%;
		.seckill-item {
			display: flex;
			margin: 20rpx;
			padding: 20rpx;
			border-radius: 12rpx;
			background-color: #FFFFFF;
			.go-btn {
				width: 160rpx;
				height: 50rpx;
				line-height: 52rpx;
				font-size: 24rpx;
				text-align: center;
				color: #FFFFFF;
				border-radius: 31px;
				background: #FF5058;
				&.expect {background-color: #4197F1;}
				&.not {background-color: #DDD;}
			}
		}
	}
</style>
