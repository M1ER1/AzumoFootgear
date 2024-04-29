<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous" />

    <link rel="stylesheet" href="res/css/style.css" />

    <title>Login</title>
</head>

<body>


<!-- Login -->
<div class="login-page bg-secondary">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="bg-white shadow rounded">
                    <div class="row">
                        <div class="col-md-7 pe-0">
                            <div class="form-left h-100 py-5 px-5">
                                <form action="process/process-login.php" id="login-form" method="POST" class="row g-4">
                                    <div class="col-12">

                                    <label for="user">Username</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                                        <input type="text" class="form-control" placeholder="max_mustermann" name="username" required>
                                    </div>
                            </div>

                            <div class="col-12">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                                    <input type="password" class="form-control" placeholder="********" name="password" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="submit" target="index.php" class="btn btn-secondary px-4 float-end mt-4">submit</button>
                            </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-5 ps-0 d-none d-md-block">
                        <div class="form-right h-100 bg-dark text-white text-center pt-5">
                            <i class="bi bi-emoji-laughing"></i>
                            <h2 class="fs-1">Welcome back!</h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Registration Link -->
            <p class="text-end text-dark mt-3">New here? <a id="account" href="register.php">Sign up here</a>!</p>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
</script>
</body>

</html>
