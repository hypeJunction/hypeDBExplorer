define(['jquery', 'elgg', 'jqgrid', 'jqgrid.locale'], function ($, jqgrid) {

	elgg.db_explorer = {};

	/**
	 * Bind events on system init
	 * @returns {void}
	 */
	elgg.db_explorer.init = function () {

		if (elgg.config.db_explorer) {
			return;
		}

		// Initial jqgrid
		$('.dbexplorer-grid').each(function () {
			var id = $(this).attr('id');
			elgg.db_explorer.jqGrid($('#' + id));
		});

		// Links that trigger popups
		$('.dbexplorer-popup').live('click', function (e) {
			e.preventDefault();

			var $popup = $('<div>').html($('<div>').addClass('elgg-ajax-loader'));

			$popup.dialog({
				title: elgg.echo('db_explorer:loading'),
				width: 1300,
				height: 600,
				close: function () {
					$(this).dialog('destroy').remove()
				}
			});

			elgg.ajax($(this).data('href'), {
				success: function (output) {
					$popup.dialog({title: ''});
					$popup.html(output);
					var id = $popup.find('.dbexplorer-grid').eq(0).attr('id');
					elgg.db_explorer.jqGrid($('#' + id));
				}
			});
		});

		// Highlight
		$('td[role="gridcell"]:has([data-guid])').live('mouseenter', function (e) {
			var guid = $(this).find('[data-guid]').eq(0).data('guid');
			$('[data-guid="' + guid + '"]').closest('td[role="gridcell"]').addClass('highlighted');
		}).live('mouseleave', function (e) {
			$('td[role="gridcell"]').removeClass('highlighted');
		});

		elgg.config.db_explorer = true;
	};

	/**
	 * Build jqGrid tables
	 * @param {object} $grid DOM element to attach the grid to
	 * @returns {void}
	 */
	elgg.db_explorer.jqGrid = function ($grid) {

		var type = $grid.data('type'),
				guid = $grid.data('guid'),
				pagerId = $grid.data('pagerId'),
				colNames = ['guid'],
				colModel = [{
						name: 'e.guid',
						width: 90,
						searchrules: {
							integer: true
						},
						searchoptions:{sopt:['eq','ne','le','lt','gt','ge']}
					}],
				sortName = 'e.guid';

		switch (type) {

			case 'user' :
				colNames.push('username', 'name', 'email', 'admin', 'banned');
				colModel.push(
						{name: 'ue.username', width: 200, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}},
				{name: 'ue.name', width: 200, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}},
				{name: 'ue.email', width: 200, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}},
				{name: 'ue.admin', width: 40, searchoptions:{sopt:['eq','ne']}},
				{name: 'ue.banned', width: 40, searchoptions:{sopt:['eq','ne']}}
				);
				break;

			case 'group' :
				colNames.push('name', 'description');
				colModel.push(
						{name: 'ge.name', width: 100, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}},
				{name: 'ge.description', width: 300, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}}
				);
				break;

			case 'object' :
				colNames.push('title', 'description');
				colModel.push(
						{name: 'oe.title', width: 100, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}},
				{name: 'oe.description', width: 300, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}}
				);
				break;

			case 'site' :
				colNames.push('name', 'description', 'url');
				colModel.push(
						{name: 'se.name', width: 100, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}},
				{name: 'se.description', width: 300, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}},
				{name: 'se.url', width: 100, searchoptions:{sopt:['eq','bw','bn','cn','nc','ew','en']}}
				);
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
				);

		colModel.push(
				{name: 'e.type', width: 90, searchoptions:{sopt:['eq','ne']}},
		{name: 'e.subtype', width: 90, searchoptions:{sopt:['eq','ne']}},
		{name: 'e.owner_guid', width: 100, searchrules: {integer: true}, searchoptions:{sopt:['eq','ne','le','lt','gt','ge']}},
		{name: 'e.site_guid', width: 50, searchrules: {integer: true}, searchoptions:{sopt:['eq','ne','le','lt','gt','ge']}},
		{name: 'e.container_guid', width: 100, searchrules: {integer: true}, searchoptions:{sopt:['eq','ne','le','lt','gt','ge']}},
		{name: 'e.access_id', width: 90, searchrules: {integer: true}, searchoptions:{sopt:['eq','ne','le','lt','gt','ge']}},
		{name: 'e.time_created', width: 80, search: false},
		{name: 'e.time_updated', width: 80, search: false},
		{name: 'e.last_action', width: 80, search: false},
		{name: 'e.enabled', width: 90}
		);

		$grid.jqGrid({
			url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/entities?' + $.param($grid.data())),
			datatype: 'json',
			mtype: 'GET',
			colNames: colNames,
			colModel: colModel,
			pager: '#' + pagerId,
			rowNum: 10,
			rowList: [10, 25, 50, 100],
			sortname: sortName,
			sortorder: 'asc',
			width: 1200,
			height: '100%',
			viewrecords: true,
			gridview: true,
			subGrid: true,
			subGridRowExpanded: elgg.db_explorer.subGridRowExpanded,
			subGridRowColapsed: function (subgrid_id, row_id) {
				$('#' + subgrid_id).empty();
			}
		});

		if (!guid) {
			$grid.jqGrid('navGrid', '#' + pagerId, {edit: false, add: false, del: false});
			$grid.jqGrid('filterToolbar', {searchOperators : true});
		}
	};


	elgg.db_explorer.subGridRowExpanded = function (subgrid_id, row_id) {

		var subgrid_table_id,
				pager_id,
				subgrid_tables = [];

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
				'owned_entities',
				'contained_entities',
				'river_items',
				'metadata',
				'metadata_ownership',
				'private_settings',
				'annotations',
				'annotations_ownership',
				'entity_relationships'
				);

		var subgrid_accordion_id = subgrid_id + '_accordion';
		$('#' + subgrid_id).html($('<div>').attr('id', subgrid_accordion_id));

		$.each(subgrid_tables, function (key, subgrid_table_name) {
			subgrid_table_id = subgrid_id + subgrid_table_name;
			pager_id = 'pager-' + subgrid_table_id;
			$('#' + subgrid_accordion_id).append('<h3>' + elgg.echo('db_explorer:tables:' + subgrid_table_name) + '</h3>');
			$('#' + subgrid_accordion_id).append('<div>' +
					'<table id="' + subgrid_table_id + '" class="scroll"></table>' +
					'<div id="' + pager_id + '" class="scroll"></div>' +
					'</div>');

			switch (subgrid_table_name) {

				case 'users_entity' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/users_entity?guid=' + row_id),
						datatype: 'json',
						colNames: ['guid', 'name', 'username', 'email', 'language', 'banned', 'admin', 'last_action', 'prev_last_action', 'last_login', 'prev_last_login'],
						colModel: [
							{name: 'ue.guid', width: 90},
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
						rowList: [10, 25, 50, 100],
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
						rowList: [10, 25, 50, 100],
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
						rowList: [10, 25, 50, 100],
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
						rowList: [10, 25, 50, 100],
						pager: pager_id,
						sortname: 'se.guid',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'owned_entities' :
				case 'contained_entities' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/' + subgrid_table_name + '?guid=' + row_id),
						datatype: 'json',
						colNames: ['guid',
							'type',
							'subtype',
							'owner_guid',
							'site_guid',
							'container_guid',
							'access_id',
							'time_created',
							'time_updated',
							'last_action',
							'enabled'],
						colModel: [
							{name: 'e.guid', width: 90},
							{name: 'e.type', width: 90},
							{name: 'e.subtype', width: 90},
							{name: 'e.owner_guid', width: 90},
							{name: 'e.site_guid', width: 50},
							{name: 'e.container_guid', width: 90},
							{name: 'e.access_id', width: 90},
							{name: 'e.time_created', width: 80},
							{name: 'e.time_updated', width: 80},
							{name: 'e.last_action', width: 80},
							{name: 'e.enabled', width: 90},
						],
						rowNum: 10,
						rowList: [10, 25, 50, 100],
						pager: pager_id,
						sortname: 'e.guid',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'river_items' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/river_items?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'type', 'subtype', 'action_type', 'subject_guid', 'object_guid', 'annotation_id', 'access_id', 'view', 'posted'],
						colModel: [
							{name: 'r.id', width: 50},
							{name: 'r.type', width: 90},
							{name: 'r.subtype', width: 90},
							{name: 'r.action_type', width: 90},
							{name: 'r.subject_guid', width: 90},
							{name: 'r.object_guid', width: 90},
							{name: 'r.annotation_id', width: 30},
							{name: 'r.access_id', width: 90},
							{name: 'r.view', width: 120},
							{name: 'r.posted', width: 90},
						],
						rowNum: 10,
						rowList: [10, 25, 50, 100],
						pager: pager_id,
						sortname: 'r.id',
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
						rowList: [10, 25, 50, 100],
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
						rowList: [10, 25, 50, 100],
						pager: pager_id,
						sortname: 'acl.id',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;

				case 'metadata' :
				case 'metadata_ownership' :
				case 'annotations' :
				case 'annotations_ownership' :
					jQuery('#' + subgrid_table_id).jqGrid({
						url: elgg.security.addToken(elgg.get_site_url() + 'action/db_explorer/' + subgrid_table_name + '?guid=' + row_id),
						datatype: 'json',
						colNames: ['id', 'entity_guid', 'name_id', 'value_id', 'name_string', 'value_string', 'value_type', 'owner_guid', 'access_id', 'time_created', 'enabled'],
						colModel: [
							{name: 'md.id', width: 40},
							{name: 'md.entity_guid', width: 90, sortable: false},
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
						rowList: [10, 25, 50, 100],
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
						rowList: [10, 25, 50, 100],
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
						rowList: [10, 25, 50, 100],
						pager: pager_id,
						sortname: 'r.relationship',
						sortorder: 'asc',
						width: '100%',
						height: '100%'
					});
					break;
			}
		});

		$('#' + subgrid_accordion_id).accordion({heightStyle: 'auto'});
	}

	elgg.db_explorer.datePick = function (elem) {
		jQuery(elem).datepicker();
	};

	elgg.db_explorer.init();
});

