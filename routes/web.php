<?php

use App\Models\Currency;
use App\Models\DefaultSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
    $currencies = Currency::where('hidden', 0)->orderBy('full_name', 'ASC')->get();

    $from = DefaultSetting::firstWhere('setting', 'from');
    $to = DefaultSetting::firstWhere('setting', 'to');

    $from = isset($from['currency_name']) ? $from['currency_name'] : null;
    $to = isset($to['currency_name']) ? $to['currency_name'] : null;

    $results = $request->session()->pull('results');
    $checkOldFrom = $request->old('from');
    $checkOldTo = $request->old('to');

    if (isset($checkOldFrom) && isset($checkOldTo)) {
        $from = null;
        $to = null;
    }

    return view('index', [
        'currencies' => $currencies,
        'from' => $from,
        'to' => $to,
        'results' => $results
    ]);
});

Route::post('/', function (Request $request) {
    $input = $request->input('input');

    $from = $request->input('from');
    $to = $request->input('to');

    $request->validate([
        "input" => 'numeric|min:1',
        "from" => "prohibited_if:to,$from",
    ]);

    $input = doubleval($input);
    $currentData = Currency::select('data', 'retrieved')->where('short_name', $from)->get();

    if (isset($currentData[0]['retrieved'])) {
        $limit = (new Carbon($currentData[0]['retrieved']))->addDay();
        $now = Carbon::now();

        if ($limit->greaterThan($now)) {
            $data = Json_decode($currentData[0]['data'], true);

            $result = $input * $data['rates'][$to];
            $results['result'] = number_format($result, 5) . " " . $to;
            $results['exchange_rate'] = "" . $data['rates'][$to];

            return back()->with("results", $results)->withInput();
        }
    }

    $url = "https://api.exchangeratesapi.io/latest?base=" . $from;
    $json = file_get_contents($url);

    $now = Carbon::now();
    DB::table('currencies')->where('short_name', $from)->update(array('data' => $json, 'retrieved' => $now));

    $data = json_decode($json, true);

    $result = $input * $data['rates'][$to];
    $results['result'] = number_format($result, 5) . " " . $to;
    $results['exchange_rate'] = "" . $data['rates'][$to];

    return back()->with("results", $results)->withInput();
});

Route::get('/settings', function () {
    $currencies = Currency::orderBy('full_name', 'ASC')->get();

    $from = DefaultSetting::firstWhere('setting', 'from');
    $to = DefaultSetting::firstWhere('setting', 'to');

    $from = isset($from['currency_name']) ? $from['currency_name'] : null;
    $to = isset($to['currency_name']) ? $to['currency_name'] : null;

    return view('settings', [
        'currencies' => $currencies,
        'from' => $from,
        'to' => $to
    ]);
});

Route::post('/settings', function (Request $request) {
    $from = $request->input('from');

    $to = $request->input('to');

    $checkedCurrencies = $request->input('currencies');

    $validator = Validator::make($request->all(), [
        'from' => [
            "prohibited_if:to,$from",
            Rule::exists('currencies', 'short_name', 'hidden')->where('short_name', $from)->where('hidden', 0),
        ],
        'to' => [
            Rule::exists('currencies', 'short_name', 'hidden')->where('short_name', $to)->where('hidden', 0),
        ],
        'currencies' => 'required',
    ], [
        'prohibited_if' => "The two currencies may not be the same",
        'exists' => "The selected currency is hidden",
        'required' => "At least one currency must be visible",
    ]);

    if ($validator->fails()) {
        return back()
                    ->withErrors($validator)
                    ->withInput();
    }

    $defaultSettingTable = (new DefaultSetting())->getTable();
    DB::table($defaultSettingTable)->where('setting', 'from')->update(array('currency_name' => $from));
    DB::table($defaultSettingTable)->where('setting', 'to')->update(array('currency_name' => $to));

    $currencyTable = (new Currency())->getTable();
    DB::table($currencyTable)->update(array('hidden' => 1));

    $ids = array_map('intval', $checkedCurrencies);

    DB::table($currencyTable)->whereIn('id', $ids)->update(array('hidden' => 0));

    return back();
});
