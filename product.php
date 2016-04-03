<?php include "catalog.php"; ?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Каталог</title>
		<link rel="stylesheet" href="<?=PATH?>style.css">
	</head>
	<body>
		<a href="/goods_catalog/">Главная</a>
		<div class="wrapper">
			<div class="sidebar">
				<ul class="category">
					<?php echo $categories_menu; ?>
				</ul>
			</div>
			<div class="content">
				<p><?=$breadcrumbs;?></p>
				<br>
				<hr>
				<?php if($get_one_product): ?>
					<?php print_arr($get_one_product); ?>
				<?php else: ?>
					Такого товара нет	
				<?php endif; ?>	
			</div>
		</div>
		
		<script src="<?=PATH?>js/jquery-1.9.0.min.js"></script>
		<script src="<?=PATH?>js/jquery.accordion.js"></script>
		<script src="<?=PATH?>js/jquery.cookie.js"></script>
		<script>
			$(document).ready(function(){
				$(".category").dcAccordion();
			});
		</script>
	</body>
</html>