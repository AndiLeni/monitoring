<?php

$sql = rex_sql::factory();
$sql->setQuery('SELECT * FROM ' . rex::getTable('monitoring_domains') . ' ORDER BY status DESC, name ASC');

echo "<div class='row'>";

foreach ($sql as $row) {

    $name = $row->getValue('name');
    $domain = $row->getValue('domain');
    $panel_type = $row->getValue('status') == 200 ? "panel-success" : "panel-danger";
    $status_text = $row->getValue('status') == 200 ? "OK" : "DOWN";
    $status = $row->getValue('status');
    $last_update = rex_formatter::intlDateTime($row->getValue('last_update'), IntlDateFormatter::MEDIUM);
    $response_time = $row->getValue('response_time');

    echo <<<DATA
    <div class="col-12 col-md-3">
        <div class="panel $panel_type">
        <div class="panel-heading"><a style="color:#fff" href="$domain" target="_blank">$name</a></div>
            <div class="panel-body">
                <p>Last checked: $last_update</p>
                <p style="margin:0">Response time: <code>$response_time</code></p>
            </div>
        </div>
    </div>
    DATA;
}

echo "</div>";
