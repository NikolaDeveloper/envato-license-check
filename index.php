<?php

require_once 'config.php';
require_once 'functions.php';

$result = '';
$found = false;

if(isset($_POST['submit'])) {
	if(defined(PASS) && !empty(PASS) && $_POST['pwd'] != PASS) {
		$result = 'Password doesn\'t match.';
	}
	elseif(!isset($_POST['code']) || strlen($_POST['code']) < 10) {
		$result = 'Purchase code is missing or is not valid.';
	}
	else {
		$result = fetch_license_data($_POST['code']);
		if(!empty($result)) {
			$result = json_decode($result);
			
			if(isset($result->{'item'})) {
				
				//Fields from {item} we actually need.
				$allow = array('id', 'name', 'author_username', 'url');
				
				foreach($result->item as $k=>$v) {
					if(!in_array($k, $allow))
						unset($result->item->{$k});
				}
				
				$found = true;
			}
		}
	}
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>Envato License Validator</title>
<style>
body { font-family: 'Open Sans', sans-serif; }
pre {
	overflow: auto;
}
form { 
	max-width: 300px;
	background: #f8f8f8;
	border-radius: 10px;
	padding: 20px;
	margin: 100px auto 0 auto;
}
form input {
	margin-bottom: 10px;
	width: 100%;
	padding: 5px 7px;
	border: 3px solid #ddd;
	border-radius: 3px;
	box-sizing: border-box;
	
}
form button { 
	margin-bottom: 0;
	width: auto;
	display: inline-block;
	padding: 5px 7px;
	border: 3px solid #ddd;
	border-radius: 3px;
	box-sizing: border-box;
	background: #333;
	color: #fff;
	cursor: pointer;
}
.result { 
	background: #fff;
	padding: 10px;
	margin: 0 auto;
	margin-top: 20px;
	border: 1px solid #eee;
	font-size: 13px;
	max-width: 600px;
	
}
.supported { 
	text-align: center;
	margin: 0 auto 10px auto;
	width: 100%;
	padding: 10px;
	box-sizing: border-box;
	font-weight: bold;
	font-size: 18px;
}
.supported.yes { background: #d2eacc; color: #158419; }
.supported.no  { background: #eacccc; color: #841515; }
td, th {
	width: 100%;
	text-align: left;
}
table { table-layout: fixed; width: 100%; }
th { width: 120px } 
@media screen and (max-height: 600px) {
	form { 
		margin-top: 0;
	}
}
</style>
</head>
<body>
<form method="post">
<?php if(defined('PASS') && !empty(PASS)) : ?>
<input type="password" name="pwd" placeholder="Password" value="<?php echo isset($_REQUEST['pwd']) ? $_REQUEST['pwd'] : ''?>" autocomplete="off" /><br />
<?php endif; ?>
<input type="text" name="code" placeholder="Purchase Code" value="<?php echo isset($_REQUEST['code']) ? $_REQUEST['code'] : ''?>" /><br />
<button type="submit" name="submit">Validate</button>
</form>
<?php if(!empty($result)) : ?>
<div class="result">
	<?php if($found) :
	$supported = time() > strtotime($result->supported_until) ? false : true;
	 ?>
	
	<div class="supported <?php echo $supported ? 'yes' : 'no'?>"><?php echo $supported ? "Supported" : "Not Supported"?></div>
	
	<table>
	<thead>
	</thead>
	<tbody>
	<tr><th>Item</th><td><a href="<?php echo $result->item->url?>" target="_blank"><?php echo $result->item->name?> [#<?php echo $result->item->id?>]</a></td></tr>
	<tr><th>Buyer</th><td><a href="http://codecanyon.net/user/<?php echo $result->buyer?>" target="_blank"><?php echo $result->buyer?></a></td></tr>
	<tr><th>Sold At</th><td><?php echo date('d M Y. H:i:s', strtotime($result->sold_at))?></td></tr>
	<tr><th>Amount</th><td>$<?php echo $result->amount?></td></tr>
	<tr><th>License</th><td><?php echo $result->license?></td></tr>
	<tr><th>Support Amount</th><td>$<?php echo $result->support_amount?></td></tr>
	<tr><th>Supported Until</th><td><?php echo date('d M Y. H:i:s', strtotime($result->supported_until))?></td></tr>
	<tr><th>Times Purchased</th><td><?php echo $result->purchase_count?></td></tr>
	</tbody>
	</table>
	<?php else : ?>
	<em>Response:</em>
	<br />
	<?php var_dump($result); ?>
	<?php endif; ?>
</div>
<?php endif; ?>
</body>
</html>