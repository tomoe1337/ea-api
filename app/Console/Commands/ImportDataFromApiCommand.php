<?php

namespace App\Console\Commands;

use App\Components\ImportDataClient;
use App\Models\Income;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportDataFromApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:allDataFromApi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all date from test Laravel api';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $http = new ImportDataClient();
        $params = [
            'key' => config('api.key'),
            'dateFrom' => '2025-01-01',//Не было указано за какой отрезок времени необходмо получить данные,был взят новый год
            'dateTo' => date('Y-m-d'),
            'page' => 1,
            'limit' => 500,

        ];
        try {
            DB::beginTransaction();


            dump('Starting importing orders...');
            $this->importData($http, $params, 'orders',new Order);
            dump('Orders imported successfully!');
            DB::commit();
            DB::beginTransaction();

            dump('Starting importing incomes...');
            $this->importData($http, $params, 'incomes', new Income);
            dump('Incomes imported successfully!');

            dump('Starting importing sales...');
            $this->importData($http, $params,'sales', new Sale);
            dump('Sales imported successfully!');

            dump('Starting importing stocks...');
            unset($params['dateTo']);
            $params['dateFrom'] = date('Y-m-d');
            $this->importData($http, $params, 'stocks', new Stock);
            dump('Stock imported successfully!');

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
        return dump('All data successfully imported!');
    }

    public function importData($http, $params, $method,$model)
    {
        do {
            DB::rollBack();
            try {
                $response = $http->client->request('GET', 'api/' . $method, ['query' => $params]);
                $data = json_decode($response->getBody()->getContents())->data;

                foreach ($data as $item) {
                    $model->Create(
                        [
                            'data' => $item,
                        ]
                    );
                }

                dump('page ' . $params['page']);
                $params['page']++;

            } catch (\Exception $ex) {
                if ($ex->getResponse()->getStatusCode() === 429) {
                    $delay = intval($ex->getResponse()->getHeader('Retry-After')[0]);
                    dump('continue importing, waiting please...');
                    sleep($delay);
                    continue;
                } else {
                    return $ex;
                }

            }
        } while ($data == true);

    }


}
