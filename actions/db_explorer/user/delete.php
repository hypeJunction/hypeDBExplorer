<?php

namespace hypeJunction\DBExplorer;

use Exception;

$guids = get_input('user_guids');

if ($guids) {
	$count = count($guids);
	$error_nouser = $error_canedit = $error = $success = 0;

	foreach ($guids as $guid) {
		$user = get_entity($guid);
		if (!$user instanceof \ElggUser) {
			$error_nouser++;
			continue;
		}

		if (!$user->canEdit()) {
			$error_canedit++;
			continue;
		}

		if ($user->guid == elgg_get_logged_in_user_guid() || elgg_is_admin_user($user->guid)) {
			$error++;
			continue;
		}

		$email = $user->email;
		$subject = elgg_echo("db_explorer:delete:email:subject");
		$body = elgg_view('framework/db_explorer/notifications/delete', [
			'entity' => $user,
			'setter' => elgg_get_logged_in_user_entity(),
			'note' => get_input('notify_users_message')
		]);
		if ($user->delete(true)) {
			if (get_input('notify_users', false)) {
				try {
					elgg_send_email(elgg_get_site_entity()->email, $email, $subject, $body);
				} catch (Exception $e) {
					elgg_register_error_message($e->getMessage());
				}
			}
			$success++;
		} else {
			$error++;
		}
	}

	$msg[] = elgg_echo('db_explorer:success:delete', [(int) $success, $count]);
	if ($error_nouser > 0) {
		$msg[] = elgg_echo('db_explorer:error:nouser', [$error_nouser]);
	}
	if ($error_canedit > 0) {
		$msg[] = elgg_echo('db_explorer:error:canedit', [$error_canedit]);
	}
	if ($error > 0) {
		$msg[] = elgg_echo('db_explorer:error:unknown', [$error]);
	}

	elgg_register_success_message(implode('<br />', $msg));
}