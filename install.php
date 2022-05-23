<?php

rex_sql_table::get(rex::getTable('monitoring_domains'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('domain', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('status', 'int'))
    ->ensureColumn(new rex_sql_column('response_time', 'float'))
    ->ensureColumn(new rex_sql_column('last_update', 'datetime'))
    ->ensure();
