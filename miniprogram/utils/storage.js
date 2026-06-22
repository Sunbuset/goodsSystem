function setUserInfo(userInfo) {
  wx.setStorageSync('userInfo', userInfo);

  const app = getApp();
  if (app && app.globalData) {
    app.globalData.userInfo = userInfo;
  }
}

function clearUserInfo() {
  wx.removeStorageSync('userInfo');

  const app = getApp();
  if (app && app.globalData) {
    app.globalData.userInfo = null;
  }
}

function getUserInfo() {
  return wx.getStorageSync('userInfo') || null;
}

function setCart(cartList) {
  wx.setStorageSync('cartList', cartList);
}

function getCart() {
  return wx.getStorageSync('cartList') || [];
}

module.exports = {
  setUserInfo,
  clearUserInfo,
  getUserInfo,
  setCart,
  getCart
};