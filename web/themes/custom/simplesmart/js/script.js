Drupal.behaviors.myBehavior = {
  attach: function (context, settings) {
    once('myBehavior', 'html').forEach(function () {
      var currentUser = drupalSettings.simplesmart.currentUser;

      console.log("ユーザー名：", currentUser.username);
      console.log("役割一覧：", currentUser.roles);
      console.log("タイムゾーン：", currentUser.timezone);
    })
  }
}
