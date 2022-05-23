<template>
	<view class="container">

		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>

		<!-- 门店列表组件 -->
		<mescroll-body ref="mescrollRef" @init="mescrollInit" @down="downCallback" @up="upCallback" :down="downOption" :up="upOption">
			<!-- 地图 -->
			<map style="width: 100%; height: 600rpx;" :latitude="latitude" :longitude="longitude" :markers="covers"></map>
			<!-- 门店 -->
			<view class="index-stroe-widget">
				<view class="stroe-item u-flex" v-for="(item, index) in storeList" :key="index" :data-id="item.id" @click="onSelect">
					<u-image :src="item.logo" width="145rpx" height="145rpx" border-radius="12rpx"></u-image>
					<view class="u-margin-left-20">
						<view class="u-margin-bottom-10">
							<text class="u-font-30 u-font-weight">{{item.name}}</text>
							<text class="u-font-22 u-color-muted u-margin-left-10">{{item.distance}}</text>
						</view>
						<view class="u-margin-bottom-10">
							<u-icon name="clock-fill" size="28" color="#666"></u-icon>
							<text class="u-font-26 u-color-lighter u-margin-left-10">{{item.business_hours}}</text>
						</view>
						<view>
							<u-icon name="map-fill" color="#666" size="28"></u-icon>
							<text class="u-font-26 u-color-lighter u-margin-left-10">{{item.region}}{{item.address}}</text>
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
	import {toPage} from '@/utils/tools'
	export default {
		mixins: [MescrollMixin],
		components: {MescrollBody},
		data() {
			return {
				// 首次加载
				isFirstLoading: false,
				// 0=不可选, 1=可选
				isSelect: 0,
				// 经度坐标
				longitude: 0,
				// 维度坐标
				latitude: 0,
				// 地图标记
				covers: [],
				// 门店列表
				storeList: [],
				// 下拉加载配置
				downOption: {
					use: true,
					auto: true
				},
				// 上拉加载配置
				upOption: {
					page: {size: 20},
					noMoreSize: 10,
					empty: {                          
						icon: '/static/empty/empty_null.png',
						tip: '暂无门店'
					}
				},
			}
		},
		onLoad(options) {
			this.isSelect  = options.type
			this.longitude = options.longitude
			this.latitude  = options.latitude
			this.covers = [
				{
					longitude: this.longitude,
					latitude: this.latitude,
				}, {
					longitude: this.longitude,
					latitude: this.latitude,
				}
			]
		},
		methods: {
			/**
			 * 上拉加载
			 */
			upCallback(page) {
				this.$u.api.apiStoreList({
					page: page.num,
					longitude: this.longitude,
					latitude: this.latitude
				}).then(result => {
					const lists   = result.data.data
					const total	  = result.data.total
					if (page.num == 1) this.storeList = []
					
					this.storeList = this.storeList.concat(lists)
					this.mescroll.endBySize(lists.length, total)
					this.$nextTick(() => {
						this.isFirstLoading = false
					})
				}).catch(() => {
					this.mescroll.endErr()
				})
			},

			/**
			 * 选择门店
			 */
			onSelect(e) {
				let { id } = e.currentTarget.dataset;
				if (this.isSelect) {
					uni.$emit('selectStore', {id: id})
					uni.navigateBack()
				} else {
					toPage("/bundle/pages/store_detail/store_detail?id="+id)
				}
			}
		}
	}
</script>

<style lang="scss">
	.mescroll-empty {
		padding: 0 !important;
	}
	
	// 门店列表部件样式
	.index-stroe-widget {
		.stroe-item {
			margin: 20rpx;
			padding: 30rpx;
			border-radius: 14rpx;
			background-color: #FFFFFF;
		}
	}
</style>
