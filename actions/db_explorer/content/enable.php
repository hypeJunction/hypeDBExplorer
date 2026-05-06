<?php

namespace hypeJunction\DBExplorer;

$guids = get_input('content_guids');

if ($guids) {
	$count = count($guids);
	$error_noentity = 0;
	$error_canedit = 0;
	$error = 0;
	$success = 0;
	$enabled = 0;

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

		if ($entity->isEnabled()) {
			$enabled++;
		} else {
			if ($entity->enable()) {
				$entity->annotate('disable', false, ACCESS_PUBLIC, elgg_get_logged_in_user_guid());
				if (get_input('content_approval_message')) {
					$entity->annotate('approval_message', get_input('content_approval_message'), ACCESS_PUBLIC, elgg_get_logged_in_user_guid());
				}

				$subject = elgg_echo('db_explorer:content:enable:email:subject');
				if (get_input('notify_owners', false)) {
					$body = elgg_view('framework/db_explorer/notifications/contentenable', [
						'entity' => $entity,
						'setter' => elgg_get_logged_in_user_entity(),
						'note' => get_input('notify_owners_message')
					]);

					notify_user($entity->owner_guid, elgg_get_logged_in_user_guid(), $subject, $body);
				}

				$success++;
			} else {
				$error++;
			}
		}
	}
}

$msg[] = elgg_echo('db_explorer:success:content:enable', [(int) $success, $count]);
if ($enabled > 0) {
	$msg[] = elgg_echo('db_explorer:error:content:notdisabled', [$enabled]);
}

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
