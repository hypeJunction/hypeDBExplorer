hypeDBExplorer
==============

Database explorer tool for Elgg.
hypeDBExplorer collects Elgg entity information spread across multiple tables, and presents it in a human-readable format.

## Screenshots ##

![alt text](https://raw.github.com/hypeJunction/hypeDBExplorer/master/screenshots/db_explorer_entities.png "Database Explorer")
![alt text](https://raw.github.com/hypeJunction/hypeDBExplorer/master/screenshots/db_explorer_subgrids.png "Database Explorer")

## Dependencies ##

Install with composer:
```json
{
	"require": {
		"hypejunction/hypedbexplorer": "3.*"
	}
}

or run:
```sh
	composer install
```

Install bower dependencies:
```sh
	bower install
```

## Features ##

* jqGrid integration with real-time pagination and search
* Multi-column filtering
* Bulk user management (validate, ban, unban, disable, enable, delete)
* Bulk content management (disable, enable, delete)
* elgg_entities table joined with object, user, group, site attributes
* elgg_metadata table joined with metastring values
* Subgrid: owned entities
* Subgrid: contained entities
* Subgrid: river items
* Subgrid: entity attributes table
* Subgrid: access collections ownership and membership
* Subgrid: entity metadata and metadata ownership
* Subgrid: entity annotations and annotations ownership
* Subgrid: private settings
* Subgrid: entity relationships
* Popup grid view on any entity guid
* Multiple popups with highlighting


## Credits / Acknolwedgements ##

* Uses the URL sniffer class by Steve Clay (ufcoe) - https://github.com/mrclay
