<?php

namespace hypeJunction\DBExplorer;

$guids = get_input('content_guids');

if ($guids) {
	$count = count($guids);
	$error_noentity = 0;
	$error_canedit = 0;
	$error = 0;
	$success = 0;
	$disabled = 0;

	foreach ($guids as $guid) {
		$entity = $guid ? get_entity((int) $guid) : null;
		if (!$entity instanceof \ElggObject && !$entity instanceof \ElggGroup) {
			$error_noentity++;
			continue;
		}

		if (!$entity->canEdit()) {
			$error_canedit++;
			continue;
		}

		if (!$entity->isEnabled()) {
			$disabled++;
		} else {
			if ($entity->disable(get_input('content_approval_message', 'admin decision'))) {
				$entity->annotate('disable', get_input('content_approval_message', true), ACCESS_PUBLIC, elgg_get_logged_in_user_guid());
				$subject = elgg_echo('db_explorer:content:disable:email:subject');
				if (get_input('notify_owners', false)) {
					$body = elgg_view('framework/db_explorer/notifications/contentdisable', [
						'entity' => $entity,
						'setter' => elgg_get_logged_in_user_entity(),
						'note' => get_input('notify_owners_message')
					]);

					$owner = $entity->owner_guid ? get_entity((int) $entity->owner_guid) : null;
					if ($owner instanceof \ElggUser) {
						elgg_notify_user($owner, 'db_explorer:content:disable', $entity, [
							'subject' => $subject,
							'body' => $body,
							'summary' => $subject,
						], elgg_get_logged_in_user_entity());
					}
				}

				$success++;
			} else {
				$error++;
			}
		}
	}
}

$msg[] = elgg_echo('db_explorer:success:content:disable', [(int) $success, $count]);
if ($disabled > 0) {
	$msg[] = elgg_echo('db_ex plorer:error:content:already_disabled', [$disabled]);
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
