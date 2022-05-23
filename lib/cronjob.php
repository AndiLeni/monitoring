<?php

class rex_monitoring_status_cronjob extends rex_cronjob
{

    public function execute(): bool
    {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('monitoring_domains'));
        $sql->select();


        foreach ($sql as $row) {
            $id = $row->getValue('id');
            $name = $row->getValue('name');
            $domain = $row->getValue('domain');

            try {
                $socket = rex_socket::factoryUrl($domain);

                $start_time = microtime(true);

                $response = $socket->doGet();

                $end_time = microtime(true);
                $response_time = $end_time - $start_time;

                $sql_update = rex_sql::factory();
                $sql_update->setTable(rex::getTable('monitoring_domains'));
                $sql_update->setWhere(['id' => $id]);
                $sql_update->setValue('status', $response->getStatusCode());
                $sql_update->setValue('response_time', $response_time);
                $sql_update->setValue('last_update', date('Y-m-d H:i:s'));
                $sql_update->update();

                if (!$response->isOk()) {
                    if (rex_config::get('monitoring', 'pushover_enabled') == 1) {
                        Pushover::sendMessage($name, $domain);
                    }

                    if (rex_config::get('monitoring', 'email_enabled') == 1) {
                        $mail = new rex_mailer();
                        $mail->Subject = "Website ${name} is down";
                        $mail->Body = "Website ${name} (${domain}) is down at " . date('d.m.Y H:i:s');
                        $mail->AddAddress(rex_config::get('monitoring', 'email_receiver'), 'Redaxo Monitoring');
                        $mail->Send();
                    }
                }
            } catch (rex_socket_exception $e) {
                rex_logger::logException($e);
                $this->setMessage('Could not update monitoring status. Please check logs.');
                return false;
            }
        }

        $this->setMessage('Monitoring status updated - ' . $sql->getRows() . ' domains checked');

        return true;
    }

    public function getTypeName(): string
    {
        return "Monitoring Status";
    }
}
