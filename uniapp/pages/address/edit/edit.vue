<template>
	<view class="container">
		<!-- 首次加载部件 -->
		<wait-loading v-if="isFirstLoading"></wait-loading>
		
		<!-- 地址表单部件 -->
		<view class="u-margin-tb-20 u-padding-lr-30 u-bg-white">
			<u-form :model="form" ref="uForm" :error-type="['toast', 'border-bottom']">
				<u-form-item label="收货人" label-width="140" prop="nickname">
					<u-input v-model="form.nickname" placeholder="请填写收货人姓名" />
				</u-form-item>
				<u-form-item label="手机号码" label-width="140" prop="mobile">
					<u-input v-model="form.mobile" placeholder="请填写收货人手机号" />
				</u-form-item>
				<u-form-item label="所在地区" label-width="140" prop="region">
					<view class="u-color-muted" @click="onShowRegion" v-if="!form.region">请选择省、市、区</view>
					<view v-else @click="onShowRegion">{{form.region}}</view>
					<u-select v-model="showRegion" mode="mutil-column-auto" :list="regionList" @confirm="onRegionChange"></u-select>
				</u-form-item>
				<u-form-item label="详细地址" label-width="140" prop="address">
					<u-input :type="'textarea'" v-model="form.address" :height="76"  placeholder="填写小区、街道、门牌号等信息" />
				</u-form-item>
				<u-form-item label="设为默认地址" label-width="180" prop="is_default">
					<u-switch slot="right" size="40" inactive-color="#e3e2e2" active-color="#FF5058"
						v-model="form.is_default ? true : false"
						@change="onSwitchDefault"
					></u-switch>
				</u-form-item>	
			</u-form>
		</view>
		
		<!-- 完成按钮部件 -->
		<view class="u-margin-lr-20 u-margin-top-50">
			<u-button size="default" type="error" shape="circle" @click="$u.debounce(onSubmit, 200)">完成</u-button>
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
	import area from '@/utils/area'
	export default {
		data() {
			return {
				// 首次加载
				isFirstLoading: true, 
				// 全国地区数据
				regionList: [],
				// 显示地区选择
				showRegion: false,
				// 地址表单数据
				form: {
					id: 0,
					nickname: '',
					mobile: '',
					region: '',
					address: '',
					is_default: 0,
					province_id: 0,
					city_id: 0,
					district_id: 0
				},
				// 地址表单验证
				rules: {
					nickname: [
						{
							required: true,
							message: '请输入收货人名称',
							trigger: ['blur', 'change']
						}
					],
					mobile: [
						{
							required: true, 
							message: '请输入手机号',
							trigger: ['change', 'blur'],
						},
						{
							validator: (rule, value, callback) => {
								return this.$u.test.mobile(value);
							},
							message: '手机号码不正确',
							trigger: ['blur', 'change'],
						}
					],
					region: [
						{
							required: true,
							message: '请选择收货地区',
							trigger: ['blur', 'change']
						}
					],
					address: [
						{
							required: true,
							message: '请输入详细的收货地址',
							trigger: ['blur', 'change']
						}
					]
				}

			}
		},
		onLoad(options) {
			this.form.id = parseInt(options.id || 0)
			if (this.form.id) {
				this.getAddress()
				uni.setNavigationBarTitle({title: '编辑地址'})
			} else {
				uni.setNavigationBarTitle({title: '添加地址'})
				this.getWxAddress()
				this.$nextTick(() => {
					this.isFirstLoading = false
				})
			}

			this.$nextTick(() => {
				this.regionList = area
			})
		},
		onReady() {
			this.$refs.uForm.setRules(this.rules);
		},
		onUnload() {
			uni.removeStorageSync('wxAddress');
		},
		methods: {
			/**
			 * 获取地址详细
			 */
			getAddress() {
				this.$u.api.apiAddressDetail({
					id: this.form.id,
				}).then(result => {
					if (result.code === 0) {
						this.form = result.data
						this.$nextTick(() => {
							this.isFirstLoading = false
						})
					} else {
						this.$showToast('loading data error')
					}
				})
			},
			
			/**
			 * 获取微信地址
			 */
			getWxAddress() {
				let wxAddress = uni.getStorageSync('wxAddress')
				if (!wxAddress) return;
				wxAddress = JSON.parse(wxAddress)
				let {
					userName,
					telNumber,
					provinceName,
					cityName,
					detailInfo
				} = wxAddress;
				let district = wxAddress.countryName || wxAddress.countyName
				this.$u.api.apiAddressRegion({
					province: provinceName, 
					city: cityName, 
					district: district
				}).then(result => {
					if (result.data.province) {
						this.form.region = `${provinceName} ${cityName} ${district}`;
						this.form.nickname = userName;
						this.form.mobile = telNumber
						this.form.address = detailInfo
						this.form.province_id = result.data.province;
						this.form.city_id = result.data.city;
						this.form.district_id = result.data.district;
					}
				})
			},
			
			/**
			 * 显示选择地区
			 */
			onShowRegion() {
				this.showRegion = !this.showRegion
			},
			
			/**
			 * 选择所在地区
			 */
			onRegionChange(region) {
				this.form.province_id = region[0].value;
				this.form.city_id = region[1].value;
				this.form.district_id = region[2].value;
				this.form.region = region[0].label + " " + region[1].label + " " + region[2].label
			},
			
			/**
			 * 切换默认状态
			 */
			onSwitchDefault() {
				let status = this.form.is_default ? 0 : 1; 
				this.form.is_default = status
			},
			
			/**
			 * 提交地址数据
			 */
			onSubmit() {
				const that = this
				this.$refs.uForm.validate(valid => {
					if (valid) {
						if (!that.form.id) {
							that.$u.api.apiAddressAdd(that.form).then((result) => {
								if (result.code === 0) {
									that.$showSuccess(result.msg)
									setTimeout(function() {
										uni.navigateBack({delta: 1})
									}, 800)
								} else {
									that.$showToast(result.msg)
								}
							})
						} else {
							that.$u.api.apiAddressEdit(that.form).then((result) => {
								if (result.code === 0) {
									that.$showSuccess(result.msg)
									setTimeout(function() {
										uni.navigateBack({delta: 1})
									}, 800)
								} else {
									that.$showToast(result.msg)
								}
							})
						}
					}
				})
			}
		}
	}
</script>