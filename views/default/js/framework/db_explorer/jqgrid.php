<?php if (FALSE) : ?>
	<script type='text/javascript'>
<?php endif; ?>

	elgg.provide('framework');
	elgg.provide('framework.db_explorer');

	framework.db_explorer.init = function() {

		$('.dbexplorer-grid')
				.each(function() {
					var id = $(this).attr('id');
					framework.db_explorer.jqGrid($('#' + id));
				})

		$('.dbexplorer-popup')
				.live('click', function(e) {
					e.preventDefault();

					var $popup = $('<div>').html($('<div>').addClass('elgg-ajax-loader'));

					$popup.dialog({
						title: elgg.echo('hj:db_explorer:loading'),
						width: 1300,
						height: 600,
						close: function() {
							$(this).dialog('destroy').remove()
						}
					});

					elgg.ajax($(this).data('href'), {
						success: function(output) {
							$popup.dialog({title: ''});
							$popup.html(output);
							var id = $popup.find('.dbexplorer-grid').eq(0).attr('id');
							framework.db_explorer.jqGrid($('#' + id));
						}
					})


				})

	}

	framework.db_explorer.jqGrid = function($grid) {

		var type = $grid.data('type'), guid = $grid.data('guid'), pagerId = $grid.data('pagerId');

		var colNames = ['guid'],
				colModel = [{name: 'e.guid', width: 50, searchrules: {integer: true}}],
		sortName = 'e.guid';

		switch (type) {

			case 'user' :
				colNames.push('username', 'name', 'email', 'admin', 'banned');
				colModel.push(
						{name: 'ue.username', width: 200},
				{name: 'ue.name', width: 200},
				{name: 'ue.email', width: 200},
				{name: 'ue.admin', width: 40},
				{name: 'ue.banned', width: 40}
				)
				break;

			case 'group' :
				colNames.push('name', 'description');
				colModel.push(
						{name: 'ge.name', width: 100},
				{name: 'ge.description', width: 300}
				)
				break;

			case 'object' :
				colNames.push('title', 'description');
				colModel.push(
						{name: 'oe.name', width: 100},
				{name: 'oe.description', width: 300}
				)
				break;

			case 'site' :
				colNames.push('name', 'description', 'url');
				colModel.push(
						{name: 'se.name', width: 100},
				{name: 'se.description', width: 300},
				{name: 'se.url', width: 100}
				)
				break;
		}

		colNames.push(
				'type',
				'subtype',
				'owner_guid',
				'site_guid',
				'container_guid',
				'access_id',
				'time_created',
				'time_updated',
				'last_action',
				'enabled'
				)

		colModel.push(
				{name: 'e.type', width: 90},
		{name: 'e.subtype', width: 90},
		{name: 'e.owner_guid', width: 50, searchrules: {integer: true}},
		{name: 'e.site_guid', width: 50, searchrules: {integer: true}},
		{name: 'e.container_guid', width: 50, searchrules: {integer: true}},
		{name: 'e.access_id', width: 90, searchrules: {integer: true}},
		{name: 'e.time_created', width: 120, search: false},
		{name: 'e.time_updated', width: 120, search: false},
		{name: 'e.last_action', width: 120, search: false},
		{name: 'e.enabled', width: 90}
		)

		$grid.jqGrid({
			url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/entities?' + $.param($grid.data())),
			datatype: 'json',
			mtype: 'GET',
			colNames: colNames,
			colModel: colModel,
			pager: '#' + pagerId,
			rowNum: 10,
			sortname: sortName,
			sortorder: 'asc',
			width: 1200,
			height: '100%',
			viewrecords: true,
			gridview: true,
			subGrid: true,
			subGridRowExpanded: framework.db_explorer.subGridRowExpanded,
			subGridRowColapsed: function(subgrid_id, row_id) {
				$('#' + subgrid_id).empty();
			}
		});

		if (!guid) {
			$grid.jqGrid('navGrid', '#' + pagerId, {edit: false, add: false, del: false});
		}
	}


	framework.db_explorer.subGridRowExpanded = function(subgrid_id, row_id) {

		var subgrid_table_id, subgrid_table_name, pager_id;
		var subgrid_tables = [];

		var row_data = $('#' + subgrid_id).closest('.dbexplorer-grid').jqGrid('getRowData', row_id);
		var row_data_type = row_data['e.type'];

		switch (row_data_type) {
			case 'user' :
				subgrid_tables.push('users_entity');
				subgrid_tables.push('access_collections_ownership');
				subgrid_tables.push('access_collections_membership');
				break;

			case 'group' :
				subgrid_tables.push('groups_entity');
				subgrid_tables.push('access_collections_ownership');
				break;

			case 'object' :
				subgrid_tables.push('objects_entity');
				break;

			case 'site' :
				subgrid_tables.push('sites_entity');
				break;
		}

		subgrid_tables.push(
				'metadata',
				'metadata_ownership',
				'private_settings',
				'annotations',
				'annotations_ownership',
				'entity_relationships'
				);

		var subgrid_accordion_id = subgrid_id + '_accordion';
		$('#' + subgrid_id).html($('<div>').attr('id', subgrid_accordion_id));

		$.each(subgrid_tables, function(key, subgrid_table_name) {
			subgrid_table_id = subgrid_id + subgrid_table_name;
			pager_id = 'pager-' + subgrid_table_id;
			$('#' + subgrid_accordion_id).append('<h3>' + elgg.echo('hj:db_explorer:tables:' + subgrid_table_name) + '</h3>');
			$('#' + subgrid_accordion_id).append('<div>\n\
							<table id="' + subgrid_table_id + '" class="scroll"></table>\n\
							<div id="' + pager_id + '" class="scroll"></div>\n\
							</div>');

			switch (subgrid_table_name) {

				case 'users_entity' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/users_entity?guid=' + row_id),
						datatype: 'json',
						colNames: ['guid', 'name', 'username', 'email', 'language', 'banned', 'admin', 'last_action', 'prev_last_action', 'last_login', 'prev_last_login'],
						colModel: [
							{name: 'ue.guid', width: 50},
							{name: 'ue.name', width: 100},
							{name: 'ue.username', width: 100},
							{name: 'ue.email', width: 150},
							{name: 'ue.language', width: 30},
							{name: 'ue.banned', width: 30},
							{name: 'ue.admin', width: 30},
							{name: 'ue.last_action', width: 120},
							{name: 'ue.prev_last_action', width: 120},
							{name: 'ue.last_login', width: 120},
							{name: 'ue.prev_last_login', width: 120},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'ue.guid',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'objects_entity' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/objects_entity?guid=' + row_id),
						datatype: 'json',
						colNames: ['guid', 'title', 'description'],
						colModel: [
							{name: 'oe.guid'},
							{name: 'oe.title'},
							{name: 'oe.description'},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'oe.guid',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'groups_entity' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/groups_entity?guid=' + row_id),
						datatype: 'json',
						colNames: ['guid', 'name', 'description'],
						colModel: [
							{name: 'ge.guid'},
							{name: 'ge.name'},
							{name: 'ge.description'},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'ge.guid',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'sites_entity' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/sites_entity?guid=' + row_id),
						datatype: 'json',
						colNames: ['guid', 'name', 'description', 'url'],
						colModel: [
							{name: 'se.guid'},
							{name: 'se.name'},
							{name: 'se.description'},
							{name: 'se.url'},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'se.guid',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'access_collections_ownership' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/access_collections_ownership?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'name', 'owner_guid', 'site_guid'],
						colModel: [
							{name: 'acl.id'},
							{name: 'acl.name'},
							{name: 'acl.owner_guid'},
							{name: 'acl.site_guid'},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'acl.id',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'access_collections_membership' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/access_collections_membership?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'name', 'owner_guid', 'site_guid'],
						colModel: [
							{name: 'acl.id'},
							{name: 'acl.name'},
							{name: 'acl.owner_guid'},
							{name: 'acl.site_guid'},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'acl.id',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'metadata' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/metadata?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'entity_guid', 'name_id', 'value_id', 'name_string', 'value_string', 'value_type', 'owner_guid', 'access_id', 'time_created', 'enabled'],
						colModel: [
							{name: 'md.id', width: 40},
							{name: 'md.entity_guid', width: 40, sortable: false},
							{name: 'md.name_id', width: 60},
							{name: 'md.value_id', width: 60},
							{name: 'msn.string', width: 200},
							{name: 'msv.string', width: 200},
							{name: 'md.value_type', width: 50},
							{name: 'md.owner_guid', width: 40},
							{name: 'md.access_id', width: 90},
							{name: 'md.time_created', width: 120},
							{name: 'md.enabled', width: 30},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'md.id',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'metadata_ownership' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/metadata_ownership?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'entity_guid', 'name_id', 'value_id', 'name_string', 'value_string', 'value_type', 'owner_guid', 'access_id', 'time_created', 'enabled'],
						colModel: [
							{name: 'md.id', width: 40},
							{name: 'md.entity_guid', width: 40, sortable: false},
							{name: 'md.name_id', width: 60},
							{name: 'md.value_id', width: 60},
							{name: 'msn.string', width: 200},
							{name: 'msv.string', width: 200},
							{name: 'md.value_type', width: 50},
							{name: 'md.owner_guid', width: 40},
							{name: 'md.access_id', width: 90},
							{name: 'md.time_created', width: 120},
							{name: 'md.enabled', width: 30},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'md.id',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'annotations' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/annotations?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'entity_guid', 'name_id', 'value_id', 'name_string', 'value_string', 'value_type', 'owner_guid', 'access_id', 'time_created', 'enabled'],
						colModel: [
							{name: 'md.id', width: 40},
							{name: 'md.entity_guid', width: 40, sortable: false},
							{name: 'md.name_id', width: 60},
							{name: 'md.value_id', width: 60},
							{name: 'msn.string', width: 200},
							{name: 'msv.string', width: 200},
							{name: 'md.value_type', width: 50},
							{name: 'md.owner_guid', width: 40},
							{name: 'md.access_id', width: 90},
							{name: 'md.time_created', width: 120},
							{name: 'md.enabled', width: 30},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'md.id',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'annotations_ownership' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/annotations_ownership?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'entity_guid', 'name_id', 'value_id', 'name_string', 'value_string', 'value_type', 'owner_guid', 'access_id', 'time_created', 'enabled'],
						colModel: [
							{name: 'md.id', width: 40},
							{name: 'md.entity_guid', width: 40, sortable: false},
							{name: 'md.name_id', width: 60},
							{name: 'md.value_id', width: 60},
							{name: 'msn.string', width: 200},
							{name: 'msv.string', width: 200},
							{name: 'md.value_type', width: 50},
							{name: 'md.owner_guid', width: 40},
							{name: 'md.access_id', width: 90},
							{name: 'md.time_created', width: 120},
							{name: 'md.enabled', width: 30},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'md.id',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'private_settings' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/private_settings?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'entity_guid', 'name', 'value'],
						colModel: [
							{name: 'ps.id'},
							{name: 'ps.entity_guid'},
							{name: 'ps.name'},
							{name: 'ps.value'},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'ps.id',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'entity_relationships' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/entity_relationships?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'guid_one', 'relationship', 'guid_two'],
						colModel: [
							{name: 'r.id'},
							{name: 'r.guid_one'},
							{name: 'r.relationship'},
							{name: 'r.guid_two'},
						],
						rowNum: 10,
						pager: pager_id,
						sortname: 'r.relationship',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;
			}

		})

		$('#' + subgrid_accordion_id).accordion({heightStyle: 'auto'});
	}

	framework.db_explorer.datePick = function(elem) {
		jQuery(elem).datepicker();
	}

	elgg.register_hook_handler('init', 'system', framework.db_explorer.init);

<?php if (FALSE) : ?></script><?php
endif;
?>
