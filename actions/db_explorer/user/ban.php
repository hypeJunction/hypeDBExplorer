<?php

namespace hypeJunction\DBExplorer;

use Exception;

$guids = get_input('user_guids');

if ($guids) {
	$count = count($guids);
	$error_nouser = $error_canedit = $error = $success = $banned = 0;

	foreach ($guids as $guid) {
		$user = get_entity($guid);
		if (!elgg_instanceof($user, 'user')) {
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

		if ($user->isBanned()) {
			$banned++;
		} else {
			if ($user->ban(get_input('approval_message', 'admin decision'))) {
				create_annotation($user->guid, 'ban', get_input('approval_message', true), '', elgg_get_logged_in_user_guid(), ACCESS_PUBLIC);
				$subject = elgg_echo("db_explorer:ban:email:subject");
				if (get_input('notify_users', false)) {
					$body = elgg_view('framework/db_explorer/notifications/ban', array(
						'entity' => $user,
						'setter' => elgg_get_logged_in_user_entity(),
						'note' => get_input('notify_users_message')
					));

					try {
						elgg_send_email(elgg_get_site_entity()->email, $user->email, $subject, $body);
					} catch (Exception $e) {
						register_error($e->getMessage());
					}
				}
				$success++;
			} else {
				$error++;
			}
		}
	}

	$msg[] = elgg_echo('db_explorer:success:ban', array((int) $success, $count));
	if ($banned > 0) {
		$msg[] = elgg_echo('db_explorer:error:already_banned', array($banned));
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

	system_message(implode('<br />', $msg));
}