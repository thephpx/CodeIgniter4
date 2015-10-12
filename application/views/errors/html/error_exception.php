<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">

	<title><?= htmlspecialchars($title, ENT_SUBSTITUTE, 'UTF-8') ?></title>
	<style type="text/css">
		<?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'debug.css')) ?>
	</style>

	<script type="text/javascript">
		<?= file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'debug.js') ?>
	</script>
</head>
<body onload="init()">

	<!-- Header -->
	<div class="header">
		<div class="container">
			<h1><?= htmlspecialchars($title, ENT_SUBSTITUTE, 'UTF-8'), ($exception->getCode() ? ' #'.$exception->getCode() : '') ?></h1>
			<p>
				<?= htmlspecialchars($exception->getMessage(), ENT_SUBSTITUTE) ?>
				<a href="https://www.google.com/search?q=<?= urlencode($title.' '.preg_replace('#\'.*\'|".*"#Us', '', $exception->getMessage())) ?>"
				   rel="noreferrer" target="_blank">search &rarr;</a>
			</p>
		</div>
	</div>

	<!-- Source -->
	<div class="container">
		<p><b><?= \CodeIgniter\Core\Exceptions::cleanPath($file, $line) ?></b> at line <b><?= $line ?></b></p>

		<?php if (is_file($file)) : ?>
			<div class="source">
				<?= self::highlightFile($file, $line, 15); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="container">

		<ul class="tabs" id="tabs">
			<li><a href="#backtrace">Backtrace</a></li>
				<li><a href="#server">Server</a></li>
				<li><a href="#request">Request</a></li>
				<li><a href="#response">Response</a></li>
				<li><a href="#files">Files</a></li>
				<li><a href="#memory">Memory</a></li>
			</li>
		</ul>

		<div class="tab-content">

			<!-- Backtrace -->
			<div class="content" id="backtrace">

				<ol class="trace">
				<?php foreach ($trace as $row) : ?>

					<li>
						<p>
							<!-- Trace info -->
							<?php if (isset($row['file']) && is_file($row['file'])) :?>
								<?= self::cleanPath($row['file']).' : '.$row['line'] ?>
							<?php else : ?>
								{PHP internal code} : <?= $row['line'] ?>
							<?php endif; ?>

							<!-- Class/Method -->
							<?php if (isset($row['class'])) : ?>
								&nbsp;&nbsp;&mdash;&nbsp;&nbsp;<?= $row['class'].$row['type'].$row['function'] ?>()
							<?php endif; ?>
						</p>

						<!-- Source? -->
						<?php if (isset($row['file']) && is_file($row['file']) &&  isset($row['class'])) : ?>
							<div class="source">
								<?= self::highlightFile($row['file'], $row['line']) ?>
							</div>
						<?php endif; ?>
					</li>

				<?php endforeach; ?>
				</ol>

			</div>

			<!-- Server -->
			<div class="content" id="server">
				<?php foreach (['_SERVER', '_SESSION', '_COOKIE'] as $var) : ?>
					<?php if (empty($GLOBALS[$var]) || ! is_array($GLOBALS[$var])) continue; ?>

					<h3>$<?= $var ?></h3>

					<table>
						<thead>
							<tr>
								<th>Key</th>
								<th>Value</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($GLOBALS[$var] as $key => $value) : ?>
							<tr>
								<td><?= htmlspecialchars($key, ENT_IGNORE, 'UTF-8') ?></td>
								<td>
									<?php if (!is_array($value) && ! is_object($value)) : ?>
										<?= htmlspecialchars($value, ENT_SUBSTITUTE, 'UTF-8') ?>
									<?php else: ?>
										<?= '<pre>'.print_r($value, true) ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

				<?php endforeach ?>
			</div>

			<!-- Request -->
			<div class="content" id="request">
				<?php $empty = true; ?>
				<?php foreach (['_GET', '_POST'] as $var) : ?>
					<?php if (empty($GLOBALS[$var]) || ! is_array($GLOBALS[$var])) continue; ?>

					<?php $empty = false; ?>

					<h3>$<?= $var ?></h3>

					<table style="width: 100%">
						<thead>
							<tr>
								<th>Key</th>
								<th>Value</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($GLOBALS[$var] as $key => $value) : ?>
							<tr>
								<td><?= htmlspecialchars($key, ENT_IGNORE, 'UTF-8') ?></td>
								<td>
									<?php if (!is_array($value) && ! is_object($value)) : ?>
										<?= htmlspecialchars($value, ENT_SUBSTITUTE, 'UTF-8') ?>
									<?php else: ?>
										<?= '<pre>'.print_r($value, true) ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

				<?php endforeach ?>

				<?php if ($empty) : ?>

					<div class="alert">
						No $_GET or $_POST Information to show.
					</div>

				<?php endif; ?>
			</div>

			<!-- Response -->
			<div class="content" id="response">
				<p>Response Code: <?= http_response_code() ?></p>

				<p>Headers:</p>
				<?php if (! empty(headers_list())) : ?>
					<ul>
					<?php foreach (headers_list() as $header) : ?>
						<li><?= htmlspecialchars($header, ENT_SUBSTITUTE, 'UTF-8') ?></li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<!-- Files -->
			<div class="content" id="files">
				<?php $files = get_included_files(); ?>

				<ol>
				<?php foreach ($files as $file) :?>
					<li><?= htmlspecialchars( self::cleanPath($file), ENT_SUBSTITUTE, 'UTF-8') ?></li>
				<?php endforeach ?>
				</ol>
			</div>

			<!-- Memory -->
			<div class="content" id="memory">

			</div>

		</div>  <!-- /tab-content -->

	</div> <!-- /container -->

</body>
</html>