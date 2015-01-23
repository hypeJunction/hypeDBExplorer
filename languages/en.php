<?php

$english = array(

	'admin:developers:db_explorer' => 'Database Explorer',

	'db_explorer:objects' => 'Objects',
	'db_explorer:entity' => 'Entity',
	'db_explorer:metadata' => 'Metadata',

	'db_explorer:tables:users_entity' => 'User Entity',
	'db_explorer:tables:objects_entity' => 'Object Entity',
	'db_explorer:tables:groups_entity' => 'Group Entity',
	'db_explorer:tables:sites_entity' => 'Site Entity',

	'db_explorer:tables:owned_entities' => 'Owned Entities',
	'db_explorer:tables:contained_entities' => 'Contained Entities',
	'db_explorer:tables:river_items' => 'River Items',

	'db_explorer:tables:access_collections_ownership' => 'Access Collection Ownership',
	'db_explorer:tables:access_collections_membership' => 'Access Collection Membership',

	'db_explorer:tables:metadata' => 'Entity Metadata',
	'db_explorer:tables:metadata_ownership' => 'Owned Metadata',

	'db_explorer:tables:annotations' => 'Entity Annotations',
	'db_explorer:tables:annotations_ownership' => 'Owned Annotations',

	'db_explorer:tables:private_settings' => 'Private Settings',

	'db_explorer:tables:entity_relationships' => 'Entity Relationships',

	'db_explorer:loading' => 'Loading entity ...',
	'db_explorer:guid' => 'GUID: %s',

	'db_explorer:inspect' => 'Inspect',
	'db_explorer:url_sniffer_no_guid' => 'The provided URL (%s) could not be traced back to an existing entity',

	'db_explorer:error:nouser' => 'Error: user not found - %s',
	'db_explorer:error:nocontent' => 'Error: item not found - %s',
	'db_explorer:error:canedit' => 'Error: insufficient privileges - %s',
	'db_explorer:error:unknown' => 'Error: unknown - %s',
	'db_explorer:error:already_validated' => '%s accounts required no validation',
	'db_explorer:error:already_banned' => '%s users already banned',
	'db_explorer:error:notbanned' => '%s users are not banned',
	'db_explorer:error:already_disabled' => '%s users already disabled',
	'db_explorer:error:notdisabled' => '%s users not disabled',

	'db_explorer:success:validate' => '%s of %s user accounts were validated',
	'db_explorer:success:ban' => '%s of %s users were banned',
	'db_explorer:success:unban' => '%s of %s were unbanned',
	'db_explorer:success:disable' => '%s of %s user accounts were disabled',
	'db_explorer:success:enable' => '%s of %s user accounts were enabled',
	'db_explorer:success:delete' => '%s of %s user accounts were deleted',

	'db_explorer:success:content:disable' => '%s of %s items were disabled',
	'db_explorer:success:content:enable' => '%s of %s items were enabled',
	'db_explorer:success:content:delete' => '%s of %s items were deleted',

	'db_explorer:ban:email:subject' => 'Your account was suspended',
	'db_explorer:ban:email:head' => '%s suspended your account',
	'db_explorer:ban:email:note' => 'The following note was included for your information:',
	'db_explorer:ban:email:footer' => 'You can contact site administrator at %s',

	'db_explorer:unban:email:subject' => 'Your account is now active',
	'db_explorer:unban:email:head' => '%s lifted the suspension of your account',
	'db_explorer:unban:email:note' => 'The following note was included for your information:',
	'db_explorer:unban:email:footer' => 'You can contact %s at %s',

	'db_explorer:disable:email:subject' => 'Your account has been deactivated',
	'db_explorer:disable:email:head' => '%s has deactivated your account',
	'db_explorer:disable:email:note' => 'The following note was included for your information:',
	'db_explorer:disable:email:footer' => 'You can contact site administrator at %s',

	'db_explorer:enable:email:subject' => 'Your account has been activated',
	'db_explorer:enable:email:head' => '%s has activated your account',
	'db_explorer:enable:email:note' => 'The following note was included for your information:',
	'db_explorer:enable:email:footer' => 'You can contact %s at %s',

	'db_explorer:delete:email:subject' => 'Your account has been deleted',
	'db_explorer:delete:email:head' => '%s has deleted your account',
	'db_explorer:delete:email:note' => 'The following note was included for your information:',
	'db_explorer:delete:email:footer' => 'You can contact the site administrator at %s',

	'db_explorer:content:disable:email:subject' => 'Your item has been disabled',
	'db_explorer:content:disable:email:head' => '%s has disabled your items %s',
	'db_explorer:content:disable:email:note' => 'The following note was included for your information:',
	'db_explorer:content:disable:email:footer' => 'You can contact site administrator at %s',

	'db_explorer:content:enable:email:subject' => 'Your item has been enabled',
	'db_explorer:content:enable:email:head' => '%s has enabled your item %s',
	'db_explorer:content:enable:email:note' => 'The following note was included for your information:',
	'db_explorer:content:enable:email:footer' => 'You can contact %s at %s',

	'db_explorer:content:delete:email:subject' => 'Your items has been deleted',
	'db_explorer:content:delete:email:head' => '%s has deleted your items',
	'db_explorer:content:delete:email:note' => 'The following note was included for your information:',
	'db_explorer:content:delete:email:footer' => 'You can contact the site administrator at %s',

	'db_explorer:toggle_all' => 'Toggle all',

	'db_explorer:batch:select:action' => 'Do nothing',
	'db_explorer:batch:selected:user' => 'With selected users:',
	'db_explorer:batch:selected:content' => 'With selected content:',

	'db_explorer:batch:user:validate' => 'Validate',
	'db_explorer:batch:user:ban' => 'Ban user',
	'db_explorer:batch:user:unban' => 'Unban user',
	'db_explorer:batch:user:disable' => 'Disable user account',
	'db_explorer:batch:user:enable' => 'Enable user account',
	'db_explorer:batch:user:delete' => 'Delete user account and all user content',

	'db_explorer:batch:content:disable' => 'Disable',
	'db_explorer:batch:content:enable' => 'Enable',
	'db_explorer:batch:content:delete' => 'Delete',

	'db_explorer:batch:notify_users' => 'Notify users',
	'db_explorer:batch:notify_owners' => 'Notify owners',
	'db_explorer:batch:message' => 'Add a note to the notifications',
	'db_explorer:batch:approval_message' => 'Annotate this operation for internal use (add a reason, a note etc)',
);

add_translation('en', $english);
