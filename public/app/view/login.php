<section id="login-page" ng-controller="UsersController" class="rtl">
    <div class="container login-container">
        <form ng-submit="login()" bl-form-validator>
            <img src="images/logo.png" alt="logo" class="logo">

            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                <div class="message"></div>
            </div>

            <div class="form-group">
                <input class="form-control" name="email" type="text" placeholder="ایمیل" ng-model="loginInfo.email">
            </div>
            <div class="form-group">
                <input class="form-control" name="password" type="password" placeholder="رمز عبور" ng-model="loginInfo.password">
            </div>

            <p>حساب کاربری ندارید؟ <a ui-sref="register">ثبت نام کنید</a></p>

            <div class="login-btn"><button type="submit" class="btn btn-success">ورود</button></div>

        </form>
    </div>
    <div class="loader" ng-class="{ hidden: !loading }"><i class="fa fa-spinner fa-spin"></i></div>
</section>