const { request } = require('../../../utils/request');
const { getUserInfo } = require('../../../utils/storage');

Page({
  data: {
    goods: null,
    quantity: 1
  },

  onLoad(options) {
    this.goodsId = options.id;
    this.fetchDetail();
  },

  fetchDetail() {
    request({
      url: 'goods_detail.php',
      data: { id: this.goodsId }
    }).then((res) => {
      this.setData({ goods: res.data.goods });
    });
  },

  addToCart() {
    const goods = this.data.goods;
    if (!goods) {
      return;
    }

    const userInfo = getUserInfo();
    if (!userInfo || !userInfo.id) {
      wx.showToast({ title: '请先登录', icon: 'none' });
      return;
    }

    request({
      url: 'cart_save.php',
      method: 'POST',
      data: {
        user_id: userInfo.id,
        goods_id: goods.id,
        quantity: this.data.quantity,
        checked: 1
      }
    }).then(() => {
      wx.showToast({ title: '已加入购物车' });
    });
  },

  createOrder() {
    const goods = this.data.goods;
    if (!goods) {
      return;
    }

    wx.navigateTo({
      url: `/pages/orders/list/list?create=1&id=${goods.id}&quantity=${this.data.quantity}`
    });
  }
});