<?php

namespace hypeJunction\DBExplorer;

$guids = get_input('content_guids');

if ($guids) {
	$count = count($guids);
	$error_noentity = $error_canedit = $error = $success = 0;

	foreach ($guids as $guid) {
		$entity = get_entity($guid);
		if (!$entity instanceof \ElggObject && !$entity instanceof \ElggGroup) {
			$error_noentity++;
			continue;
		}

		if (!$entity->canEdit()) {
			$error_canedit++;
			continue;
		}
		
		$subject = elgg_echo("db_explorer:content:delete:email:subject");
		$body = elgg_view('framework/db_explorer/notifications/contentdelete', [
			'entity' => $entity,
			'setter' => elgg_get_logged_in_user_entity(),
			'note' => get_input('notify_owners_message')
		]);

		if ($entity->delete(true)) {
			if (get_input('notify_owners', false)) {
				notify_user($entity->owner_guid, elgg_get_logged_in_user_guid(), $subject, $body);
			}
			$success++;
		} else {
			$error++;
		}
	}
}

$msg[] = elgg_echo('db_explorer:success:content:delete', [(int) $success, $count]);
if ($error_noentity > 0) {
	$msg[] = elgg_echo('db_explorer:error:noentity', [$error_noentity]);
}
if ($error_canedit > 0) {
	$msg[] = elgg_echo('db_explorer:error:canedit', [$error_canedit]);
}
if ($error > 0) {
	$msg[] = elgg_echo('db_explorer:error:unknown', [$error]);
}

elgg_register_success_message(implode('<br />', $msg));

