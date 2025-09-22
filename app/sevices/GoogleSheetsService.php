<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = '1DcneJQUGCp1NHXGI7LUlgAd53aBnOULY7w6xqT02dSk'; // Pastikan ID ini benar
        $credentialsPath = storage_path('app/credentials/bot-telegram-final-12fde28b0b6f.json'); // Pastikan nama file ini benar

        $this->client = new Client();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->addScope(Sheets::SPREADSHEETS);
        $this->client->setAccessType('offline');

        $this->service = new Sheets($this->client);
    }

    private function generateMaterialId($worksheet)
    {
        $range = $worksheet . '!A2:A';
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $all_ids = $response->getValues() ?? [];

        $today_str = date('dmy');
        $prefix = "MAT-{$today_str}-";
        $max_num = 0;

        foreach ($all_ids as $row) {
            $pid = $row[0] ?? '';
            if (strpos($pid, $prefix) === 0) {
                $num_part = str_replace($prefix, '', $pid);
                if (is_numeric($num_part) && (int)$num_part > $max_num) {
                    $max_num = (int)$num_part;
                }
            }
        }
        $new_num = $max_num + 1;
        return "{$prefix}{$new_num}";
    }
    
    public function appendMaterial(array $data)
    {
        $worksheetName = 'Data Material';
        $newId = $this->generateMaterialId($worksheetName);

        $newRow = [
            $newId,
            $data['id_project_posjar'],
            $data['lokasi_jalan'],
            $data['no'],
            $data['jenis_material'],
            $data['uraian_pekerjaan'],
            $data['satuan'],
            $data['volume'],
            date('d-m-Y H:i:s'),
        ];

        $body = new ValueRange(['values' => [$newRow]]);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $range = $worksheetName;

        return $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }
}