<?php

namespace hypeJunction\DBExplorer;
?>

<div class="dbexplorer-form-row">
	<label>
		<?php
		echo elgg_view('input/checkbox', array(
			'default' => false,
			'value' => false,
			'class' => 'js-dbexplorer-toggle',
		));
		echo elgg_echo('db_explorer:toggle_all');
		?>
	</label>
</div>

<div class="elgg-col elgg-col-1of2">
	<fieldset>
		<legend><?php echo elgg_echo('db_explorer:batch:selected:user') ?></legend>
		<div class="dbexplorer-form-row">
			<label>
				<?php
				echo elgg_view('input/dropdown', array(
					'name' => 'user_action',
					'options_values' => array(
						'' => elgg_echo('db_explorer:batch:select:action'),
						'db_explorer/user/validate' => elgg_echo('db_explorer:batch:user:validate'),
						'db_explorer/user/ban' => elgg_echo('db_explorer:batch:user:ban'),
						'db_explorer/user/unban' => elgg_echo('db_explorer:batch:user:unban'),
						'db_explorer/user/disable' => elgg_echo('db_explorer:batch:user:disable'),
						'db_explorer/user/enable' => elgg_echo('db_explorer:batch:user:enable'),
						'db_explorer/user/delete' => elgg_echo('db_explorer:batch:user:delete'),
				)));
				?>
			</label>
		</div>
		<div class="dbexplorer-form-row">
			<label>
				<?php
				echo elgg_view('input/checkbox', array(
					'name' => 'notify_users',
					'value' => 1,
					'checked' => false,
					'default' => false
				));
				echo elgg_echo('db_explorer:batch:notify_users');
				?>
			</label>
		</div>
		<div class="dbexplorer-form-row">
			<label>
				<?php
				echo elgg_echo('db_explorer:batch:message');
				echo elgg_view('input/text', array(
					'name' => 'notify_users_message',
				));
				?>
			</label>
		</div>
		<div class="dbexplorer-form-row">
			<label>
				<?php
				echo elgg_echo('db_explorer:batch:approval_message');
				echo elgg_view('input/text', array(
					'name' => 'approval_message',
				));
				?>
			</label>
		</div>
	</fieldset>
</div>

<div class="elgg-col elgg-col-1of2">
	<fieldset>
		<legend><?php echo elgg_echo('db_explorer:batch:selected:content') ?></legend>
		<div class="dbexplorer-form-row">
			<label>
				<?php
				echo elgg_view('input/dropdown', array(
					'name' => 'content_action',
					'options_values' => array(
						'' => elgg_echo('db_explorer:batch:select:action'),
						'db_explorer/content/disable' => elgg_echo('db_explorer:batch:content:disable'),
						'db_explorer/content/enable' => elgg_echo('db_explorer:batch:content:enable'),
						'db_explorer/content/delete' => elgg_echo('db_explorer:batch:content:delete')
				)));
				?>
			</label>
		</div>
		<div class="dbexplorer-form-row">
			<label>
				<?php
				echo elgg_view('input/checkbox', array(
					'name' => 'notify_owners',
					'value' => 1,
					'checked' => false,
					'default' => false
				));
				echo elgg_echo('db_explorer:batch:notify_owners');
				?>
			</label>
		</div>
		<div class="dbexplorer-form-row">
			<label>
				<?php
				echo elgg_echo('db_explorer:batch:message');
				echo elgg_view('input/text', array(
					'name' => 'notify_owners_message',
				));
				?>
			</label>
		</div>
		<div class="dbexplorer-form-row">
			<label>
				<?php
				echo elgg_echo('db_explorer:batch:approval_message');
				echo elgg_view('input/text', array(
					'name' => 'content_approval_message',
				));
				?>
			</label>
		</div>
	</fieldset>
</div>

<div class="elgg-foot text-right">
	<?php
	echo elgg_view('input/submit', array(
		'value' => elgg_echo('save')
	));
	?>
</div>
