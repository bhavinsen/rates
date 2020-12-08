<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\Rates;

class ImportCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import currency rates from the http://www.cbr.ru/scripts/XML_daily.asp.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $url = 'http://www.cbr.ru/scripts/XML_daily.asp';
        $response = $client->request('GET', $url);
        $xml = (string)$response->getBody();
        $xmlObject = simplexml_load_string($xml);
        $json = json_encode($xmlObject);
        $currencies = json_decode($json, TRUE);
        $valutes = $currencies['Valute'];

        $total = 0;
        $valcursDate = $currencies['@attributes']['Date'];
        $importDate = Carbon::parse($valcursDate)->format('Y-m-d');

        foreach ($valutes as $valute) {
            $valuteId = $valute['@attributes']['ID'];
            $numCode = $valute['NumCode'];
            $charCode = $valute['CharCode'];
            $nominal = $valute['Nominal'];
            $name = $valute['Name'];
            $value = $valute['Value'];
            $price = floatval(str_replace(',', '.', str_replace('.', '', $value)));

            $rate = Rates::where([['char_code', '=', $charCode], ['valcurs_date', '=', $importDate]])->get()->first();
            if($rate) {
                continue;
            }

            $rate = Rates::create([
                'valute_id' => $valuteId,
                'num_code'  => $numCode,
                'char_code' => $charCode,
                'nominal' => $nominal,
                'name' => $name,
                'value' => $price,
                'valcurs_date' => Carbon::parse($valcursDate)->format('Y-m-d')
            ]);

            if ($rate) {
                $total += 1;
            }
        }

        $this->info('Total imported currencies ' . $total . '.');
    }
}
