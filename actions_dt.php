<div class="dropdown">
	<button class="btn btn-primary btn--icon-text" data-toggle="dropdown">
		<i class="zmdi zmdi-menu"></i>
		Menu
	</button>

	<?php if (sizeof($actions) > 0): ?>

		<div class="dropdown-menu">
			<?php foreach ($actions as $action): ?>
				<?php if (has_permission_view($action['permission'])): ?>
					<?php if (array_key_exists("formatter", $action)): ?>
						<a href="<?= $action['url'] ?>"
						   class="dropdown-item"><?= $action['formatter']($action['value']) ?></a>
					<?php else: ?>
						<a href="<?= $action['url'] ?>" class="dropdown-item"><?= $action['text'] ?></a>
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>
</div>
