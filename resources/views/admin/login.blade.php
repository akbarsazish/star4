<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ثبت نام</title>
    <link rel="stylesheet" href="{{ url('/resources/assets/css/mainAdmin.css')}}">
    <meta name="viewport" content="width =device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#FFE1C4">
    <script src="{{url('/resources/assets/js/sweetalert.min.js')}}"></script>
    <link rel="icon" type="image/png" href="{{ url('/resources/assets/images/part.png')}}">
</head>
<body style="background-color:#bbcbda;">
    <section class="account-box">
        <div class="register login" style="background: linear-gradient(#85baef, #116bc7, #2659a9); margin-top:200px;">

            <div class="headline" style="color:rgb(0, 0, 0);text-align:center;">ورود به CRM</div>
                <div class="content">
                    <form action="{{('/loginUser')}}" method="post">
                        @csrf
                        <label for="mobtel" style="color:white">ایمیل یا شماره موبایل</label>
                        <input name="userName" type="text" placeholder="نام کاربری" required>
                        <label for="pwd" style="color:white">کلمه عبور</label>
                        <input name="password" type="password"  placeholder="کلمه عبور" required>
                        <button type="submit" style="background-color:rgb(0, 0, 0)"><i class="fa fa-unlock"></i> ورود به CRM </button>
                        @if(isset($loginError))
                            @if($loginError=="نام کاربری و یا رمز ورود اشتباه است")
                                <script>
                                    swal({
                                        title: "خطا!",
                                        text: "نام کاربری و یا رمز ورود اشتباه است",
                                        icon: "warning",
                                        button: "تایید!",
                                    });
                                </script>
                            @else
                                @php
                                    unset($loginError);
                                @endphp
                            @endif
                        @endif
                    </form>
                </div>
           </div>
    </section>
    <script>
    </script>
</body>
</html>
