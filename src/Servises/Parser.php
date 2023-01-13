<?php

namespace App\Servises;

use App\Interfaces\DatabaseInterface;
use DOMDocument;

class Parser
{
    public static function handle(DatabaseInterface $databaseInterface)
    {
        $databaseInterface->connection();
        $html = file_get_contents("https://www.cbr.ru/currency_base/daily/");
        $html = substr($html, strpos($html, '<tr>'));
        $html = substr($html, 0, strrpos($html, '</tr>'));
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $lines = $dom->getElementsByTagName("tr");
        $sql = "INSERT INTO exchange_rates (code, name, rate) VALUES ";
        foreach ($lines as $keyLine => $line) {
            if ($keyLine === 0) continue;
            $columns = $line->getElementsByTagName("td");
            $values = [];
            foreach ($columns as $keyColumn => $column) {
                switch ($keyColumn):
                    case 1:
                        $values["code"] = $column->nodeValue;
                        break;
                    case 2:
                        $values["rate"] = (float)$column->nodeValue;
                        break;
                    case 3:
                        $values["name"] = $column->nodeValue;
                        break;
                    case 4:
                        $values["rate"] = 1 / ((float)$column->nodeValue / $values["rate"]);
                        break;
                endswitch;
            }
            if ($keyLine === 1) $sql .= "('{$values['code']}', '{$values['name']}', '{$values['rate']}')";
            else $sql .= ",('{$values['code']}', '{$values['name']}', '{$values['rate']}')";
        }
        $databaseInterface->query("TRUNCATE TABLE exchange_rates");
        $databaseInterface->query($sql);
    }
}
