<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Por onde anda?</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<div id="corpo">
		<?php
			$code = @$_REQUEST['code'];
		?>
		<h1>Por onde anda<?php echo $code ? ' ' . $code : ''?>?</h1>
		<hr />
		<form>
				<legend>Digite no campo abaixo o seu código de rastreamento dos correios. Ex.: ES751103462BR</legend>
				<p><input type="text" size="14" maxlength="13" name="code" value="<?php echo $code ? $code : 'ES751103462BR'?>" onblur="if(this.value == '') { this.value = 'ES751103462BR'; }" onfocus="if(this.value == 'ES751103462BR') { this.value = ''; }" />
				<button>Por onde anda?</button>
		</form>
		<hr />
		<?php
		if ($code):
		include_once 'verifica.php';
		$c = new Correio($code);
		if (!$c->erro):
		?>
		<h2>Situa&ccedil;&atilde;o atual: <?php echo $c->status ?></h2>
		<table>
			<tr>
				<td>Data</td>
				<td>Local</td>
				<td>A&ccedil;&atilde;o</td>
				<td>Detalhes</td>
			</tr>
			<?php foreach ($c->track as $l): ?>
				<tr>
					<td><?php echo $l->data ?></td>
					<td><?php echo $l->local ?></td>
					<td><?php echo $l->acao ?></td>
					<td><?php echo $l->detalhes ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php else: ?>
		<?php echo $c->erro_msg ?>
		<?php endif; endif;?>
		</div>
	</body>
</html>