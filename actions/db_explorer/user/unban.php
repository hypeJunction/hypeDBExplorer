<?php

namespace hypeJunction\DBExplorer;

$guids = get_input('user_guids');

if ($guids) {
	$count = count($guids);
	$error_nouser = $error_canedit = $error = $success = $notbanned = 0;

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

		if ($user->guid == elgg_get_logged_in_user_guid()) {
			$error++;
			continue;
		}

		if (!$user->isBanned()) {
			$notbanned++;
		} else {
			if ($user->unban()) {
				$user->annotate('ban', false, ACCESS_PUBLIC, elgg_get_logged_in_user_guid());
				if (get_input('approval_message')) {
					$user->annotate('approval_message', get_input('approval_message'), ACCESS_PUBLIC, elgg_get_logged_in_user_guid());
				}
				$subject = elgg_echo("db_explorer:unban:email:subject");
				if (get_input('notify_users', false)) {
					$body = elgg_view('framework/db_explorer/notifications/unban', array(
						'entity' => $user,
						'setter' => elgg_get_logged_in_user_entity(),
						'note' => get_input('notify_users_message')
					));

					try {
						elgg_send_email(elgg_get_site_entity()->email, $user->email, $subject, $body);
					} catch (Exception $e) {
						elgg_register_error_message($e->getMessage());
					}
				}
				$success++;
			} else {
				$error++;
			}
		}
	}

	$msg[] = elgg_echo('db_explorer:success:ban', array((int) $success, $count));
	if ($notbanned > 0) {
		$msg[] = elgg_echo('db_explorer:error:notbanned', array($notbanned));
	}
	if ($error_nouser > 0) {
		$msg[] = elgg_echo('db_explorer:error:nouser', array($error_nouser));
	}
	if ($error_canedit > 0) {
		$msg[] = elgg_echo('db_explorer:error:canedit', array($error_canedit));
	}
	if ($error > 0) {
		$msg[] = elgg_echo('db_explorer:error:unknown', array($error));
	}

	elgg_register_success_message(implode('<br />', $msg));
}