<?php

namespace App\Http\Controllers;

use App\Requests\QuotesRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class QuotesController extends Controller
{
    public function showForm()
    {
        $companySymbols = $this->getCompanySymbols();

        return view('form')->with('companySymbols', $companySymbols);
    }

    public function showQuotes(QuotesRequest $request)
    {
        $historicQuotes = $this->getQuotes($request->symbol, $request->from, $request->to);
        $companyName = $this->getCompanyName($request->symbol);

        $emailBody = 'From ' . Carbon::parse($request->from)->format('Y-m-d') . ' to ' . Carbon::parse($request->to)->format('Y-m-d');

        Mail::send('mail', ['emailBody' => $emailBody], function($message) use ($request, $companyName) {
            $message->to($request->email)->subject($companyName);
        });

        return view('quotes')->with(['historicQuotes' => $historicQuotes, 'companyName' => $companyName]);
    }

    protected function getCompanySymbols()
    {
        $response = Http::get(
            'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json'
        )->body();

        return json_decode($response, 1);
    }

    protected function getQuotes($companySymbol, $startDate, $endDate)
    {
        $response = Http::withHeaders(['X-RapidAPI-Host' => 'yh-finance.p.rapidapi.com',
            'X-RapidAPI-Key' => 'c7736c5a58msh38cf0bd3ca149a9p17e451jsn06afa1054c2c'])
            ->get('https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data', ['symbol' => $companySymbol])
            ->body();

        $quotes = json_decode($response, 1)['prices'];

        usort($quotes, function ($a, $b)
            {
                return strcmp($a["date"], $b["date"]);
            }
        );

        foreach ($quotes as $index=>$quote) {
            if ((Carbon::parse($quote["date"]))->lt(Carbon::parse($startDate)) || (Carbon::parse($quote["date"]))->gt(Carbon::parse($endDate))) {
                unset($quotes[$index]);
            } else {
                $quotes[$index]['date'] = Carbon::parse($quote["date"])->format('Y-m-d');
            }
        }

        return $quotes;
    }

    protected function getCompanyName($symbol)
    {
        $companySymbols = $this->getCompanySymbols();
        foreach ($companySymbols as $companySymbol) {
            if ($companySymbol['Symbol'] === $symbol) {
                $companyName = $companySymbol['Company Name'];
            }
        }

        return $companyName;
    }
}
