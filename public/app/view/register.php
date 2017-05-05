<section id="login-page" ng-controller="UsersController" class="rtl">
	<div class="container login-container">
		<form ng-submit="register()" bl-form-validator>

			<img src="images/logo.png" alt="logo" class="logo">

			<div class="alert alert-danger alert-dismissible fade in" role="alert">
		    	<div class="message"></div>
		    </div>

            <div class="form-group">
                <input class="form-control" name="name" type="text" placeholder="نام" ng-model="registerInfo.name">
            </div>
			<div class="form-group">
				<input class="form-control" name="email" type="text" placeholder="ایمیل" ng-model="registerInfo.email">
			</div>
			<div class="form-group">
				<input class="form-control" name="password" type="password" placeholder="رمز عبور" ng-model="registerInfo.password">
			</div>
			<div class="form-group">
				<input class="form-control" name="password_confirmation" type="password" placeholder="تکرار رمز عبور" ng-model="registerInfo.password_confirmation">
			</div>

			<p>حساب کاربری دارید؟ <a ui-sref="home">از این جا وارد حساب شوید.</a></p>
			
			<div class="login-btn">
				<button type="submit" class="btn btn-success">ثبت نام</button>
			</div>
		</form>
	</div>
	<div class="loader" ng-class="{ hidden: !loading }"><i class="fa fa-spinner fa-spin"></i></div>
</section>