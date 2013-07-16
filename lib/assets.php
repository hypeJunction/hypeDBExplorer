<?php

elgg_register_js('jquery.jqgrid.js', '/mod/hypeDBExplorer/vendors/jqgrid/js/jquery.jqGrid.min.js');
$locale = get_language();
elgg_register_js('jquery.jqgrid.locale.js', "/mod/hypeDBExplorer/vendors/jqgrid/js/i18n/grid.locale-$locale.js");

elgg_register_simplecache_view('js/framework/db_explorer/jqgrid');
elgg_register_js('dbexplorer.jqgrid.js', elgg_get_simplecache_url('js', 'framework/db_explorer/jqgrid'));


elgg_register_css('jquery.ui.css', '/mod/hypeDBExplorer/vendors/jquery.ui/css/jquery-ui.custom.css');
elgg_register_css('jquery.jqgrid.css', '/mod/hypeDBExplorer/vendors/jqgrid/css/ui.jqgrid.css');

elgg_register_simplecache_view('css/framework/db_explorer/jqgrid');
elgg_register_css('dbexplorer.jqgrid.css', elgg_get_simplecache_url('css', 'framework/db_explorer/jqgrid'));
