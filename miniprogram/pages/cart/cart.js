const { request } = require('../../utils/request');
const { getUserInfo } = require('../../utils/storage');

Page({
  data: {
    cartList: [],
    selectAll: false,
    totalPrice: 0,
    userId: 0
  },

  onShow() {
    const userInfo = getUserInfo();
    if (!userInfo || !userInfo.id) {
      this.setData({ cartList: [], selectAll: false, totalPrice: 0, userId: 0 });
      wx.showToast({ title: '请先登录', icon: 'none' });
      return;
    }

    this.setData({ userId: userInfo.id });
    this.refreshCart();
  },

  refreshCart() {
    request({
      url: 'cart_list.php',
      data: { user_id: this.data.userId }
    }).then((res) => {
      const cartList = (res.data.list || []).map((item) => ({
        ...item,
        checked: item.checked !== false
      }));
      const selectAll = cartList.length > 0 ? cartList.every((item) => item.checked !== false) : false;
      this.setData({ cartList, selectAll });
      this.calcTotal();
    });
  },

  calcTotal() {
    const totalPrice = this.data.cartList.reduce((sum, item) => {
      if (item.checked === false) {
        return sum;
      }
      return sum + Number(item.price) * Number(item.quantity);
    }, 0);
    this.setData({ totalPrice: totalPrice.toFixed(2) });
  },

  toggleItem(e) {
    const index = e.currentTarget.dataset.index;
    const cartList = this.data.cartList.slice();
    const current = cartList[index];
    current.checked = !current.checked;
    request({
      url: 'cart_update.php',
      method: 'POST',
      data: {
        user_id: this.data.userId,
        cart_id: current.id,
        checked: current.checked ? 1 : 0
      }
    }).then(() => {
      this.setData({ cartList, selectAll: cartList.every((item) => item.checked !== false) });
      this.calcTotal();
    });
  },

  toggleAll() {
    const selectAll = !this.data.selectAll;
    const cartList = this.data.cartList.map((item) => ({ ...item, checked: selectAll }));
    Promise.all(cartList.map((item) => request({
      url: 'cart_update.php',
      method: 'POST',
      data: {
        user_id: this.data.userId,
        cart_id: item.id,
        checked: selectAll ? 1 : 0
      }
    }))).then(() => {
      this.setData({ cartList, selectAll });
      this.calcTotal();
    });
  },

  changeQuantity(e) {
    const { index, type } = e.currentTarget.dataset;
    const cartList = this.data.cartList.slice();
    const current = cartList[index];
    const quantity = type === 'add' ? current.quantity + 1 : Math.max(1, current.quantity - 1);
    request({
      url: 'cart_update.php',
      method: 'POST',
      data: {
        user_id: this.data.userId,
        cart_id: current.id,
        quantity
      }
    }).then(() => {
      current.quantity = quantity;
      this.setData({ cartList });
      this.calcTotal();
    });
  },

  removeItem(e) {
    const index = e.currentTarget.dataset.index;
    wx.showModal({
      title: '确认删除',
      content: '确定要从购物车移除该商品吗？',
      success: (res) => {
        if (!res.confirm) {
          return;
        }
        const cartList = this.data.cartList.slice();
        const current = cartList[index];
        request({
          url: 'cart_delete.php',
          method: 'POST',
          data: {
            user_id: this.data.userId,
            cart_id: current.id
          }
        }).then(() => {
          cartList.splice(index, 1);
          this.setData({ cartList, selectAll: cartList.every((item) => item.checked !== false) });
          this.calcTotal();
        });
      }
    });
  },

  submitOrder() {
    const selectedGoods = this.data.cartList.filter((item) => item.checked !== false).map((item) => ({
      id: item.goods_id,
      quantity: item.quantity
    }));
    if (selectedGoods.length === 0) {
      wx.showToast({ title: '请选择商品', icon: 'none' });
      return;
    }

    request({
      url: 'order_create.php',
      method: 'POST',
      data: { user_id: this.data.userId, goodsList: selectedGoods }
    }).then(() => {
      wx.showToast({ title: '下单成功' });
      this.refreshCart();
    });
  }
});