<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login with AUMS</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <style type="text/css">
        html {
            position: relative;
            min-height: 100%;
        }
        body {
            /* Margin bottom by footer height */
            margin-bottom: 60px;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60px;
            padding-top: 17px;
            text-align: center;
            background-color: #fbfbfb;
        }
    </style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container" style="margin-top: 20px;">
    <div class="row">
        <div class="col-sm-12 hidden-xs" style="height: 100px;">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 col-sm-push-2">
            <div class="panel panel-default" style="height: 225px;">
                <div class="panel-heading">Login with <strong>AUMS</strong></div>
                <div class="panel-body">
                    Once you authorize, <strong><?=$this->data['app_name']?></strong> can:
                    <ul class="list-unstyled" style="line-height: 2; margin-top: 10px;">
                        <li><span class="fa fa-check text-success"></span> Get your personal info</li>
                        <li><span class="fa fa-check text-success"></span> Get your current department & semester</li>
                        <li><span class="fa fa-check text-success"></span> Get your AUMS profile picture</li>
                    </ul>
                    <span class="text-muted">No worries. We are as secure as the AUMS website.<br></span>
                </div>
            </div>
            </div>
        <div class="col-sm-4 col-sm-push-2">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form id="loginForm" method="POST" action="">
                        <div class="form-group <?=(($this->data['error']) ? 'has-error' : '')?>">
                            <label for="username" class="control-label">Roll No</label>
                            <input type="text" class="form-control" id="username" name="username" value="" required title="Please enter you username" placeholder="CB.EN.U4XYZ12029">
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group <?=(($this->data['error']) ? 'has-error' : '')?>">
                            <label for="password" class="control-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="" required placeholder="AUMS login password" title="Please enter your password">
                            <span class="help-block"></span>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <p class="text-muted">Designed & developed by <a href="https://github.com/niranjan94" target="_blank">@niranjan94</a>.</p>
    </div>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>