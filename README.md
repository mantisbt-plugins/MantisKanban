Mantis Kanban
=============

This is a fork of the original MantisKanban plugin which can be found here: https://github.com/thinksentient/MantisKanban
Since the plugin is not maintained and didn't work "out of the box" I've decided to fork, fix and extend it a bit.

Installation
------------

__Prerequisites__
* MantisBT 1.2 or higher
* [jQuery plugin](https://github.com/mantisbt-plugins/jquery) 1.6.2 or higher

Note: MantisBT 1.3 is currently [not supported](https://github.com/mantisbt-plugins/MantisKanban/issues/5)

__Setup__
*   Upload the "MantisKanban" folder to your Mantis "plugins" folder
*   Activate the plugin in your Mantis backend
*   edit "pages/kanban_page.php" and define at least your desired columns and "wip" limits. (see the "$columns" array)

Changelog
---------
__New features in version 1.2.1.:__
*   Show only used status in the workflow for each project.
*   Bold the summary for issue modified with 24 hours.
*   Show project name in each card for combined view for "All projects".
*   Using the default gravtar setting in config_inc.php.

__New features in version 1.2.:__
*   make use of the default states
*   filter directly in the kanban view
*   change user on the ticket
*   drag&drop of tickets
*   styled the readme

__New features in version 1.1.:__
*   fixed the base plugin, should run now "out of the box"
*   added AJAX support: you can now change the ticket status from the Kanban board via AJAX dropdown
*   made the tickets sortable by date or by priority
*   added alternative / optional view for "All projects" (separate row for each project)
*   added "work in progress" limits per column - if limit is reached, the column becomes red
*   made column titles multilingual
*   uses the most important filters from the general list view in Mantis (selected user etc.)

