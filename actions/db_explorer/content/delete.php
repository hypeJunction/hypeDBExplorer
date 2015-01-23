<?php

namespace hypeJunction\DBExplorer;

$guids = get_input('content_guids');

if ($guids) {
	$count = count($guids);
	$error_noentity = $error_canedit = $error = $success = 0;

	foreach ($guids as $guid) {
		$entity = get_entity($guid);
		if (!elgg_instanceof($entity, 'object') && !elgg_instanceof($entity, 'group')) {
			$error_noentity++;
			continue;
		}

		if (!$entity->canEdit()) {
			$error_canedit++;
			continue;
		}
		
		$subject = elgg_echo("db_explorer:content:delete:email:subject");
		$body = elgg_view('framework/db_explorer/notifications/contentdelete', array(
			'entity' => $entity,
			'setter' => elgg_get_logged_in_user_entity(),
			'note' => get_input('notify_owners_message')
		));

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

$msg[] = elgg_echo('db_explorer:success:content:delete', array((int) $success, $count));
if ($error_noentity > 0) {
	$msg[] = elgg_echo('db_explorer:error:noentity', array($error_noentity));
}
if ($error_canedit > 0) {
	$msg[] = elgg_echo('db_explorer:error:canedit', array($error_canedit));
}
if ($error > 0) {
	$msg[] = elgg_echo('db_explorer:error:unknown', array($error));
}

system_message(implode('<br />', $msg));

