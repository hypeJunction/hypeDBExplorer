<?php

namespace hypeJunction\DBExplorer;

$guids = get_input('user_guids');

if ($guids) {
	$count = count($guids);
	$error_nouser = $error_canedit = $error = $success = $validated = 0;

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

		if (elgg_get_user_validation_status($user->guid)) {
			$validated++;
		} else {
			if (elgg_set_user_validation_status($user->guid, true, 'manual')) {
				create_annotation($user->guid, 'validate', true, '', elgg_get_logged_in_user_guid(), ACCESS_PUBLIC);
				$success++;
			} else {
				$error++;
			}
		}
	}

	$msg[] = elgg_echo('db_explorer:success:validate', array((int) $success, $count));
	if ($validated > 0) {
		$msg[] = elgg_echo('db_explorer:error:already_validated', array($validated));
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