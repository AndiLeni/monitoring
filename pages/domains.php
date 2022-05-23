<?php

$func = rex_request('func', 'string', '');
$table = 'monitoring_domains'; // rex_adressen - Prefix wird durch rex::getTable hinzugefügt

// Das Formular wird angezeigt, wenn der Request-Parameter func (get oder post) gleich "add" oder "edit" ist
if (in_array($func, ['add', 'edit'])) {
    // Formular-Objekt erstellen
    $form = rex_form::factory(rex::getTable($table), 'Domains', 'id=' . rex_request('id', 'int', 0), 'post', false);
    // Die ID muss immer mit übergeben werden, sonst funktioniert das Speichern nicht
    $form->addParam('id', rex_request('id', 'int', 0));

    // Textfeld name mit Label Nachname
    $field = $form->addTextField('name');
    $field->setLabel('Name');
    $field->getValidator()->add('notEmpty', 'Das Feld darf nicht leer sein.');
    $field->getValidator()->add('maxLength', 'Das Feld darf nicht länger als 255 Zeichen sein.', 255);

    // Textfeld vorname mit Label Vorname
    $field = $form->addTextField('domain');
    $field->setLabel('Domain');
    $field->getValidator()->add('notEmpty', 'Das Feld darf nicht leer sein.');
    $field->getValidator()->add('maxLength', 'Das Feld darf nicht länger als 255 Zeichen sein.', 255);

    // Formular auslesen
    $content = $form->get();

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', 'Domains', false);
    $fragment->setVar('body', $content, false);
    $content = $fragment->parse('core/page/section.php');
} else {
    // Listen-Objekt erstellen. 10 Datensätze pro Seite
    $list = rex_list::factory('SELECT id,name,domain,status,last_update,response_time FROM ' . rex::getTable($table), 100, $table, false);

    // Icon für die erste Spalte "+" = hinzufügen
    $th_icon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-action"></i></a>';
    // Edit-Icon
    $td_icon = '<i class="rex-icon fa-file-text-o"></i>';
    $list->addColumn($th_icon, $td_icon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($th_icon, ['func' => 'edit', 'id' => '###id###']);

    // die Spalte name wird sortierbar
    $list->setColumnSortable('id');
    $list->setColumnSortable('name');
    $list->setColumnSortable('domain');
    $list->setColumnSortable('status');
    $list->setColumnSortable('last_update');
    $list->setColumnSortable('response_time');

    $list->setColumnLabel('id', 'ID');
    $list->setColumnLabel('name', 'Name');
    $list->setColumnLabel('domain', 'Domain');
    $list->setColumnLabel('status', 'Status Code');
    $list->setColumnLabel('last_update', 'Letzter Check');
    $list->setColumnLabel('response_time', 'Response Time');

    $list->setColumnFormat('status', 'custom', function ($data) {
        return $data['value'] == 200 ? '<span class="label label-success">' . $data['value'] . '</span>' : '<span class="label label-danger">' . $data['value'] . '</span>';
    });

    $list->setColumnFormat('last_update', 'custom', function ($data) {
        return rex_formatter::intlDateTime($data['value'], IntlDateFormatter::MEDIUM);
    });

    $list->setColumnFormat('response_time', 'custom', function ($data) {
        $value = $data['value'];

        $thumb_down = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16">
        <path d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2.094 2.094 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.162 3.162 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.823 4.823 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591z"/>
        </svg>';

        $thumb_up = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
        <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
      </svg>';

        return $value < 0.5 ? '<code>' . $value . '</code>' . $thumb_up : '<code>' . $value . '</code>' . $thumb_down;
    });

    $list->setColumnFormat('domain', 'custom', function ($data) {
        return '<a href="' . $data['value'] . '" target="_blank">' . $data['value'] . '</a>';
    });

    // Liste auslesen
    $content = $list->get();
}


echo $content;
