<?php

namespace hypeJunction\DBExplorer;

$guids = get_input('user_guids');

if ($guids) {
	$count = count($guids);
	$error_nouser = $error_canedit = $error = $success = $enabled = 0;

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

		if ($user->isEnabled()) {
			$enabled++;
		} else {
			if ($user->enable()) {
				create_annotation($user->guid, 'disable', false, '', elgg_get_logged_in_user_guid(), ACCESS_PUBLIC);
				if (get_input('approval_message')) {
					create_annotation($user->guid, 'approval_message', get_input('approval_message'), '', elgg_get_logged_in_user_guid(), ACCESS_PUBLIC);
				}
				$subject = elgg_echo("db_explorer:enable:email:subject");
				if (get_input('notify_users', false)) {
					$body = elgg_view('framework/db_explorer/notifications/enable', array(
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

	$msg[] = elgg_echo('db_explorer:success:enable', array((int) $success, $count));
	if ($enabled > 0) {
		$msg[] = elgg_echo('db_explorer:error:notdisabled', array($enabled));
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