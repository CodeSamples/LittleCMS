<?php

	include_once('includes/header-top-bar.php');	

?>
<div class="centerText" style="margin-bottom: 7em;">
	<span class="homeItemsTitle">Selección de área</span>
	<br />
	<span class="homeItemsContainer">
		<?php foreach ($response->allowedAreas as $key => $value): ?>
		<span><a href="<?php echo $key; ?>"><?php echo $value; ?></a></span>
		<?php endforeach; ?>
	</span>
</div>
<script type="text/javascript">
	$('.homeItemsContainer > span > a').button();
</script>