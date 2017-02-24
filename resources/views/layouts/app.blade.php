<!DOCTYPE html>
<html>
<head>
	<title>App Name - @yield('title')</title>

	<!-- Bootstrap core CSS -->
	    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

	    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	    <link href="http://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

	    <!-- Custom styles for this template -->
	    <link href="http://getbootstrap.com/examples/signin/signin.css" rel="stylesheet">

	    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
	    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
	    <script src="http://getbootstrap.com/assets/js/ie-emulation-modes-warning.js"></script>

	    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
</head>
<body>
{{--
@section('sidebar')
	<p>This is the master sidebar</p>
@show
--}}

<div class="container">
	@yield('content')
</div>
</body>
</html>