<!DOCTYPE html>
<html>
<head>
	<title id="title">YDKList</title>
	<link rel="icon" 
      href="favicon.ico">

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="	sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="	sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<style type="text/css">
		h1, h2, h3, p {
			text-align: center;
			margin: 10px auto;
		}

		textarea {
			resize: none;
			height: 200px;
			width: 100%;
			padding: 0;
			box-sizing: border-box;
			display: block;
		}

		#ydk_file {
			display: none;
		}

		textarea, .ydk_file, input[type=submit], td, th {
			-moz-box-shadow: inset 0 -1px 1px rgba(0,0,0,0.5);
			-webkit-box-shadow: inset 0 -1px 1px rgba(0,0,0,0.5);
			box-shadow: inset 0 -1px 1px rgba(0,0,0,0.5);
		}

		.vcenter {
			display: inline-block;
			vertical-align: bottom;
			float: none;
		}

		.ydk_file, input[type=submit] {
			background: #DDD;
			border: 0;
			display: block;
			width: 100%;
			max-width: 500px;
			height: 200px;
			margin: auto;
			font-size: 2em;
			text-align: center;
		}

		input[type=submit] {
			line-height: 200px;
		}

		input[type=submit]:disabled, input[type=submit]:disabled:hover {
			opacity: 0.75;
			cursor: not-allowed;
		}

		.ydk_file > span {
			display: block;
			position: relative;
    		top: 50%;
    		transform: translateY(-50%);
		}

		.ydk_file:hover, input[type=submit]:hover {
			cursor: pointer;
			background: #CCC;
		}

		#loading {
			display: none;
		}

		.table-responsive {
			overflow-x: auto;
		}

		@-moz-document url-prefix() {
			fieldset { display: table-cell; }
		}

		.table > tbody > tr > td, .table > tbody > tr > th {
     		vertical-align: middle;
		}

		td > a > img {
			height: 250px;
		}

		td, th {
			text-align: center;
		}

		td {
			background-color: #DDD;
		}

		th {
			background-color: #CCC;
		}
	</style>
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<h1>YDKList by <a href="/" target="_blank">Eiji Uema</a></h1>
			<p>YDKList is a simple PHP application that translates YGOPro's .ydk files to human readable card lists containing data provided by <a target="_blank" href="http://www.yugiohprices.com">yugiohprices.com</a> and <a target="_blank" href="http://yugioh.wikia.com">yugioh.wikia.com</a> API's.</p>
			<h2 style="color: red;">YDKList is currently not working due to a limitation on the current server.</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<form method="post">
				<div class="row">
					<div class="col-xs-12 col-md-4 vcenter">
						<h2>Select your ydk file</h2>
						<label id="ydk_file_label" class="ydk_file" for="ydk_file">
							<span>Click here to select a file</span>
							<input id="ydk_file" type="file" accept=".ydk">
						</label>
					</div><!--
					--><div class="col-xs-12 col-md-4 vcenter">
						<h2>or copy and paste your ydk file content below</h2>
						<textarea id="ydk"></textarea>
					</div><!--
					--><div class="col-xs-12 col-md-4 vcenter">
						<h2>Click the send button to search</h2>
						<input id="submit" type="submit" value="Send">
					</div>
				</div>
			</form>
		</div>
	</div>
	<div id="loading">
		<h3>
			<img src="img/loading.gif">
		</h3>
	</div>
	<div id="result"></div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		$(document).ajaxStart(function() {
			$("#loading").show();
			i = 1;
			dots = ['', '.', '..', '...'];
			$('#title').text('YDKList - Loading');
			title = setInterval(function() {
				$('#title').text('YDKList - Loading' + dots[i]);
				i++;
				if(i == 4)
				{
					i = 0;
				}
			}, 1000);
			$("#submit").attr('disabled', 'disabled');
			t0 = performance.now();
		});

		$(document).ajaxStop(function() {
			$("#loading").hide();
			clearInterval(title);
			t1 = performance.now();
			$('#time').html(Math.round(t1 - t0)/1000);
			$('#title').text('YDKList - Done');
			$("#submit").removeAttr('disabled');
		});


		$("#ydk_file").change(function(e)
		{
			var $i = $('#ydk_file'),
			input = $i[0];

			if(input.files && input.files[0]) {
				file = input.files[0];
				fr = new FileReader();
				fr.onload = function () {
					$('#ydk').val(fr.result);
				};
				fr.readAsText(file);
			}

			$('#ydk_file').wrap('<form>').closest('form').get(0).reset();
			$('#ydk_file').unwrap();
			e.stopPropagation();
			e.preventDefault();
		});

		$("form").submit(function(e) {
			$("#result").html('');
			$.post("",
				{ydk: $('#ydk').val()},
				function(data) {
  				$("#result").html(data);
  				errorchecker();
			});
			return false;
		});

		function errorchecker() {
			$('img').on('error', function(){
				$(this).attr('src', 'img/card_back.jpg');
			});
		}
		
	});
</script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>