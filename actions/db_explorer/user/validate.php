<?php

namespace hypeJunction\DBExplorer;

$guids = get_input('user_guids');

if ($guids) {
	$count = count($guids);
	$error_nouser = $error_canedit = $error = $success = $validated = 0;

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

		if ($user->isValidated()) {
			$validated++;
		} else {
			if ($user->setValidationStatus(true, 'manual')) {
				$user->annotate('validate', true, ACCESS_PUBLIC, elgg_get_logged_in_user_guid());
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

	elgg_register_success_message(implode('<br />', $msg));
}