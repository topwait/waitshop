<template>
	<u-popup v-model="showPop" mode="bottom" border-radius="14" :closeable="true" @close="onClose" :safe-area-inset-bottom="true">
		<view class="popup-sku-widget">
			<view class="popup-sku__info">
				<view class="sku-product-image">
					<image class="image" :lazy-load="true" 
						@tap="previewImage(goodsChecked.image || goods.image)"
						:src="goodsChecked.image || goods.image"></image>
				</view>
				<view class="sku-product-price">
					<text class="u-font-24">¥</text>
					<text>{{ goodsChecked.sell_price || goods.min_price }}</text>
				</view>
			</view>
			<scroll-view class="popup-sku__list" scroll-y="true">
				<view class="mod-spec-item" v-for="(specItem, index) in goodsSpec" :key="index">
					<view class="keys">{{ specItem.name }}</view>
					<view class="values">
						<view class="item"
							v-for="(specValue, i) in specItem.specValue" :key="i"
							@tap="onChoseSpecItem(specItem.id, specValue.id)"
							:class="specValue.checked ? 'active' : ''">
							{{ specValue.value }}
						</view>
					</view>
				</view>
			</scroll-view>
			<view class="popup-sku__number">
				<view class="purchase">
					<text>购买数量</text>
					<text class="stock">库存{{ goodsChecked.stock || 0}}件</text>
				</view>
				<u-number-box v-model="goodsNum" :min="1" :max="goodsChecked.stock || 1"></u-number-box>
			</view>
			<view class="popup-sku__footer">
				<block v-if="type !== 'all'">
					<view class="button add-buy-btn" @tap="onClick('buynow')"  v-if="type === 'buy'">确定</view>
					<view class="button add-buy-btn" @tap="onClick('addcart')" v-if="type === 'cart'">确定</view>
				</block>
				<block v-else>
					<view class="button-item button-item--cart" @tap="onClick('addcart')">加入购物车</view>
					<view class="button-item button-item--buy" @tap="onClick('buynow')">立即购买</view>
				</block>
			</view>
		</view>
	</u-popup>
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
				goodsSpec: [],	    // 商品规格
				goodsChecked: {},   // 选中商品
				showPop: false,	    // 是否弹窗
				goodsNum: 1,		// 购买数量
			};
		},
		props: {
			type: {
				type: String,
				default: 'all'
			},
			show: {
				type: Boolean
			},
			goods: {
				type: Object
			},
		},
		watch: {
			goods(value) {
				let goodsSpec = value.spec || [];
				let goodsSku = value.sku || [];
				goodsSpec.forEach(item => {
					if (item.specValue) {
						item.specValue.forEach((specValueitem, specValueindex) => {
							if (specValueindex == 0) {
								specValueitem.checked = 1;
							} else {
								specValueitem.checked = 0;
							}
						});
					}
				});
				this.goodsSpec = goodsSpec
				this.goodsChecked = goodsSku.length ? goodsSku[0] : {}
			},
			goodsSpec(value) {
				const { sku } = this.goods;
				let keyArr = [];

				value.forEach(item => {
					if (item.specValue) {
						item.specValue.forEach(specValueItem => {
							if (specValueItem.checked) {
								keyArr.push(specValueItem.id);
							}
						});
					}
				});
				if (!keyArr.length) return;
				let key = keyArr.join(',');
				let index = sku.findIndex(item => {
					return item.spec_value_ids == key;
				});
				if (index == -1) return;
				this.goodsChecked = sku[index]
				this.$emit('change', {
					checked: sku[index]
				});
			},
			show(val) {
				this.showPop = val
			}
		},
		methods: {
			/**
			 * 关闭弹窗
			 */
			onClose() {
				this.$emit('close')
			},
			
			/**
			 * 选择规格
			 */
			onClick(type) {
				let {goodsChecked, goodsNum} = this;
				goodsChecked.goodsNum = goodsNum
				this.$emit(type, {
					detail: goodsChecked
				})
			},
			
			/**
			 * 选择规格
			 */
			onChoseSpecItem(spec_id, spec_value_id) {
				this.goods.spec.forEach(item => {
					if (item.specValue && item.id == spec_id) {
						item.specValue.forEach(specValueitem => {
							specValueitem.checked = 0;
							if (specValueitem.id == spec_value_id) {
								specValueitem.checked = 1;
							}
						});
					}
				});
				this.goodsSpec = [...this.goodsSpec]
			},
			
			/**
			 * 预览当前图片
			 */
			previewImage(current) {
				uni.previewImage({
					current,
					urls: [current]
				})
			}
		}
	}
</script>

<style lang="scss">
	.popup-sku-widget {
		position: relative;
		overflow: hidden;
		.popup-sku__info {
			display: flex;
			padding: 20rpx 0;
			margin: 0 20rpx;
			border-bottom: 1px #f2f2f2 solid;
			.sku-product-image {
				width: 150rpx;
				height: 150rpx;
				.image {
					width: 100%;
					height: 100%;
					border-radius: 12rpx;
				}
			}
			.sku-product-price {
				font-size: 40rpx;
				color: #FF5058;
				margin-left: 20rpx;
			}
		}
		.popup-sku__list {
			width: auto;
			padding: 20rpx 0;
			margin: 0 20rpx;
			max-height: 680rpx;
			overflow: hidden;
			box-sizing: border-box;
			border-bottom: 1px solid #EEEEEE;
			.mod-spec-item {
				margin-top: 20rpx;
				&:first-child { margin-top: 0; }
				.keys {
					font-size: 26rpx;
					color: #000000;
					margin-bottom: 20rpx;
				}
				.values {
					display: flex;
					align-items: center;
					flex-wrap: wrap;
					.item {
						display: flex;
						align-items: center;
						padding: 0 20rpx;
						line-height: 56rpx;
						height: 60rpx;
						margin-right: 40rpx;
						margin-bottom: 20rpx;
						font-size: 24rpx;
						color: #000000;
						border-radius: 8rpx;
						border: 1rpx #F6F5F6 solid;
						background-color: #F6F5F6;
						display: -webkit-box;
						-webkit-box-orient: vertical;
						-webkit-line-clamp: 1;
						overflow: hidden;
						&.disabled { color: #A8A6AC !important; }
						&.active { 
							color: #FF4F2A; 
							border: 1rpx #ff4f2a solid;
							background-color: #fff9fd;
						}
					} 
				}
			}
		}
		.popup-sku__number {
			display: flex;
			justify-content: space-between;
			padding: 10px;
			.purchase {
				font-size: 14px;
				color: #333333;
				.stock {
					margin-left: 10px;
					font-size: 12px;
					color: #999;
				}
			}
		}
		.popup-sku__footer {
			display: flex;
			justify-content: center;
			padding: 10px;
			.button {
				flex: 1;
				height: 80rpx;
				line-height: 80rpx;
				font-size: 30rpx;
				color: #FFFFFF;
				text-align: center;
				border-radius: 40rpx;
				background-color: #ff2c3c;
			}
			.button-item {
				line-height: 80rpx;
				font-size: 26rpx;
				font-weight: bold;
				color: #FFFFFF;
				text-align: center;
				&.alone {border-radius: 40rpx !important;}
				&--cart {flex: 1; border-top-left-radius: 40rpx; border-bottom-left-radius: 40rpx; background-color: #ffa630;}
				&--buy {flex: 1; border-top-right-radius: 40rpx; border-bottom-right-radius: 40rpx; background-color: #ff2c3c;}
			}
		}
	}
</style>

