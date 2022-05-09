const install = (Vue, vm) => {
	vm.$u.api = {
		apiDiyPage:   (params = {}) => vm.$u.get("Diy/index", params),
		apiConfig:    (params = {}) => vm.$u.get("Index/config", params),
		apiHotSearch: (params = {}) => vm.$u.get("Index/hotSearch", params),
		
		// 广告推广接口
		apiAd: 		  (params = {}) => vm.$u.get("Index/ad", params),
		
		// 政策协议接口
		apiPolicy: 	  (params = {}) => vm.$u.get("Index/policy", params),
		
		// 订阅消息接口
		apiSubscribe: (params = {}) => vm.$u.get("Index/subscribe", params),
		
		// 联系客服接口
		apiCustomerService: (params = {}) => vm.$u.get("Index/customerService", params),
		
		// 分享相关接口
		apiSharePoster: (params = {}) => vm.$u.get("Share/poster", params),

		// 登录相关接口
		apiLoginConfig:	(params = {}) => vm.$u.get("Login/config", params),
		apiRegister:    (params = {}) => vm.$u.post("Login/register", params),
		apiLogin:       (params = {}) => vm.$u.post("Login/login", params),
		apiWxPhone:     (params = {}) => vm.$u.post("Login/getWxPhone", params),
		apiBindMobile:  (params = {}) => vm.$u.post("Login/bindMobile", params),
		apiSendSms:     (params = {}) => vm.$u.post("Sms/send", params),
		
		// 用户相关接口
		apiUserDesign:   (params = {}) => vm.$u.get("User/design", params),
		apiUserCenter:   (params = {}) => vm.$u.get("User/center", params),
		apiUserInfo:     (params = {}) => vm.$u.get("User/info", params),
		apiUserWallet:   (params = {}) => vm.$u.get("User/wallet", params),
		apiUserIntegral: (params = {}) => vm.$u.get("User/integral", params),
		apiUserAccount:  (params = {}) => vm.$u.get("User/account", params),
		apiUserSet:      (params = {}) => vm.$u.post("User/setUser", params),
		apiGradeList:    (params = {}) => vm.$u.get("User/grade", params),
		
		// 商品分类相关接口
		apiCategorySkin:  (params = {}) => vm.$u.get("GoodsCategory/skin"),
		apiCategoryList:  (params = {}) => vm.$u.get("GoodsCategory/lists"),
		apiCategoryGoods: (params = {}) => vm.$u.get("GoodsCategory/goods", params),
		
		// 商品相关接口
		apiGoodsList:   (params = {}) => vm.$u.get("Goods/lists", params),
		apiGoodsDetail: (params = {}) => vm.$u.get("Goods/detail", params),
		apiGoodsRecommend: (params = {}) => vm.$u.get("Goods/recommend", params),
		
		// 商品收藏相关接口
		apiGoodsCollectList: (params = {}) => vm.$u.get("GoodsCollect/lists", params),
		apiGoodsCollectAdd:  (params = {}) => vm.$u.post("GoodsCollect/add", params),
		apiGoodsCollectDel:  (params = {}) => vm.$u.post("GoodsCollect/del", params),
		
		// 评论相关接口
		apiCommentWhole: (params = {}) => vm.$u.get("GoodsComment/whole", params),
		apiCommentList:  (params = {}) => vm.$u.get("GoodsComment/lists", params),
		apiCommentGoods: (params = {}) => vm.$u.get("GoodsComment/goods", params),
		apiCommentAdd:   (params = {}) => vm.$u.post("GoodsComment/add", params),
		
		// 购物车相关接口
		apiCartList:   (params = {}) => vm.$u.get("Cart/lists", params),
		apiCartNum:    (params = {}) => vm.$u.get("Cart/num", params),
		apiCartAdd:    (params = {}) => vm.$u.post("Cart/add", params),
		apiCartDel:    (params = {}) => vm.$u.post("Cart/destroy", params),
		apiCartChoice: (params = {}) => vm.$u.post("Cart/choice", params),
		apiCartChange: (params = {}) => vm.$u.post("Cart/change", params),
		
		// 订单相关接口
		apiOrderList:    (params = {}) => vm.$u.get("Order/lists", params),
		apiOrderDetail:  (params = {}) => vm.$u.get("Order/detail", params),
		apiOrderTraces:  (params = {}) => vm.$u.get("Order/traces", params),
		apiOrderSettle:    (params = {}) => vm.$u.post("Order/settle", params),
		apiOrderBuy:     (params = {}) => vm.$u.post("Order/buy", params),
		apiOrderEnd:     (params = {}) => vm.$u.post("Order/end", params),
		apiOrderCancel:  (params = {}) => vm.$u.post("Order/cancel", params),
		apiOrderDelete:  (params = {}) => vm.$u.post("Order/del", params),
		apiOrderConfirm: (params = {}) => vm.$u.post("Order/confirm", params),
		
		// 售后相关接口
		apiAfterSaleList:    (params = {}) => vm.$u.get("AfterSale/lists", params),
		apiAfterSalelogs:    (params = {}) => vm.$u.get("AfterSale/logs", params),
		apiAfterSaleDetail:  (params = {}) => vm.$u.get("AfterSale/detail", params),
		apiAfterSaleGoods:   (params = {}) => vm.$u.get("AfterSale/goods", params),
		apiAfterSaleApply:   (params = {}) => vm.$u.post("AfterSale/apply", params),
		apiAfterSaleExpress: (params = {}) => vm.$u.post("AfterSale/express", params),
		
		// 支付相关接口
		apiPaymentWay: (params = {}) => vm.$u.get("Payment/way", params),
		apiPaymentPay: (params = {}) => vm.$u.post("Payment/prepay", params),
		
		// 地址相关接口
		apiAddressList:    (params = {}) => vm.$u.get("Address/Lists"),
		apiAddressDetail:  (params = {}) => vm.$u.get("Address/detail", params),
		apiAddressRegion:  (params = {}) => vm.$u.get("Address/region", params),
		apiAddressArea:    (params = {}) => vm.$u.get("Address/area", params),
		apiAddressAdd:     (params = {}) => vm.$u.post("Address/add", params),
		apiAddressEdit:    (params = {}) => vm.$u.post("Address/edit", params),
		apiAddressDel:     (params = {}) => vm.$u.post("Address/del", params),
		apiAddressDefault: (params = {}) => vm.$u.post("Address/setDefault", params),
		
		// 门店相关接口
		apiStoreList:   (params = {}) => vm.$u.get("Store/lists", params),
		apiStoreDetail: (params = {}) => vm.$u.get("Store/detail", params),
		apiStoreWriteOffIndex:  (params = {}) => vm.$u.get("Store/writeOffIndex", params),
		apiStoreWriteOffOrder:  (params = {}) => vm.$u.get("Store/writeOffOrder", params),
		apiStoreWriteOffDetail: (params = {}) => vm.$u.get("Store/writeOffDetail", params),
		apiStoreWriteOffRecord: (params = {}) => vm.$u.get("Store/writeOffRecord", params),
		apiStoreWriteOffSubmit: (params = {}) => vm.$u.post("Store/writeOffSubmit", params),
		
		// 文章相关接口
		apiArticleList:   (params = {}) => vm.$u.get("Article/lists", params),
		apiArticleDetail: (params = {}) => vm.$u.get("Article/detail", params),
		
		// 优惠券相关接口
		apiCouponList: (params = {}) => vm.$u.get("addons.Coupon/lists", params),
		apiCouponMy:   (params = {}) => vm.$u.get("addons.Coupon/myCoupon", params),
		apiCouponReceive: (params = {}) => vm.$u.post("addons.Coupon/receive", params),
		
		// 分销相关接口
		apiDistributionIndex:  (params = {}) => vm.$u.get("addons.Distribution/index", params),
		apiDistributionOrder:  (params = {}) => vm.$u.get("addons.Distribution/order", params),
		apiDistributionFans:   (params = {}) => vm.$u.get("addons.Distribution/fans", params),
		apiDistributionRecord: (params = {}) => vm.$u.get("addons.Distribution/record", params),
		apiDistributionCode:   (params = {}) => vm.$u.post("addons.Distribution/code", params),
		apiDistributionApply:  (params = {}) => vm.$u.post("addons.Distribution/apply", params),
		
		// 拼团相关接口
		apiTeamList:   (params = {}) => vm.$u.get("addons.Team/lists", params),
		apiTeamRecord: (params = {}) => vm.$u.get("addons.Team/record", params),
		apiTeamDetail: (params = {}) => vm.$u.get("addons.Team/detail", params),
		apiTeamSettle: (params = {}) => vm.$u.post("addons.Team/settle", params),
		apiTeamBuy:    (params = {}) => vm.$u.post("addons.Team/buy", params),
		
		// 秒杀相关接口
		apiSeckillList:   (params = {}) => vm.$u.get("addons.Seckill/lists", params),
		apiSeckillDetail: (params = {}) => vm.$u.get("addons.Seckill/detail", params),
		
		// 充值相关接口
		apiRechargeIndex:  (params = {}) => vm.$u.get("addons.Recharge/index", params),
		apiRechargeOrder:  (params = {}) => vm.$u.get("addons.Recharge/order", params),
		apiRechargeRecord: (params = {}) => vm.$u.get("addons.Recharge/record", params),
		apiRechargeBuy:    (params = {}) => vm.$u.post("addons.Recharge/buy", params),
		
		// 提现相关接口
		apiWithdrawConfig: (params = {}) => vm.$u.get("Withdraw/config", params),
		apiWithdrawRecord: (params = {}) => vm.$u.get("Withdraw/record", params),
		apiWithdrawDetail: (params = {}) => vm.$u.get("Withdraw/detail", params),
		apiWithdrawApply:  (params = {}) => vm.$u.post("Withdraw/apply", params),
		
		// 签到相关接口
		apiSignCalendar: (params = {}) => vm.$u.get("addons.Sign/calendar", params),
		apiSignConfirm:  (params = {}) => vm.$u.post("addons.Sign/sign", params),
		
		// 抽奖相关接口
		apiLotteryPrize:  (params = {}) => vm.$u.get("addons.Lottery/prize", params),
		apiLotteryRecord: (params = {}) => vm.$u.get("addons.Lottery/record", params),
		apiLotteryDraw:   (params = {}) => vm.$u.post("addons.Lottery/draw", params),
	}
}

export default {
	install
}
