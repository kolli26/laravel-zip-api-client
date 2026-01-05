<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $countyId = $request->get('county_id');
        $letter = $request->get('letter');
        $cities = [];

        try {
            // Fetch all counties for the dropdown
            $countiesResponse = Http::api()->get('counties');
            $counties = [];
            if ($countiesResponse->successful()) {
                $responseBody = json_decode($countiesResponse->body(), false);
                $counties = $responseBody->data ?? [];
            }

            // If county is selected, fetch the first letters
            $letters = [];
            if ($countyId) {
                $lettersResponse = Http::api()->get("counties/$countyId/abc");
                if ($lettersResponse->successful()) {
                    $responseBody = json_decode($lettersResponse->body(), false);
                    $letters = $responseBody->data ?? [];
                }

                // If letter is selected, fetch cities starting with that letter
                // If letter is 'all', fetch all cities for the county
                if ($letter === 'all') {
                    $citiesResponse = Http::api()->get("counties/$countyId/place-names");
                    if ($citiesResponse->successful()) {
                        $responseBody = json_decode($citiesResponse->body(), false);
                        $cities = $responseBody->data ?? [];
                    }
                } elseif ($letter) {
                    $citiesResponse = Http::api()->get("counties/$countyId/place-names/" . urlencode($letter));
                    if ($citiesResponse->successful()) {
                        $responseBody = json_decode($citiesResponse->body(), false);
                        $cities = $responseBody->data ?? [];
                    }
                }
            }

            return view('cities.index', [
                'counties' => $counties,
                'letters' => $letters,
                'cities' => $cities,
                'selectedCounty' => $countyId,
                'selectedLetter' => $letter,
                'isAuthenticated' => $this->isAuthenticated()
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('cities.index')
                ->with('error', "Nem sikerült betölteni a városokat: " . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $response = Http::api()->get("/zip-codes/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'A város nem található vagy hiba történt.';
                return redirect()
                    ->route('cities.index')
                    ->with('error', "Hiba: $message");
            }

            $city = $this->getCity($response);

            if (!$city) {
                return redirect()
                    ->route('cities.index')
                    ->with('error', "A város adatai nem érhetők el.");
            }

            return view('cities.show', ['entity' => $city]);

        } catch (\Exception $e) {
            return redirect()
                ->route('cities.index')
                ->with('error', "Nem sikerült betölteni a város adatait: " . $e->getMessage());
        }
    }

    public function create()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }

        try {
            $response = Http::api()->get('counties');
            $counties = [];
            if ($response->successful()) {
                $responseBody = json_decode($response->body(), false);
                $counties = $responseBody->data->counties ?? [];
            }

            return view('cities.create', ['counties' => $counties]);

        } catch (\Exception $e) {
            return redirect()
                ->route('cities.index')
                ->with('error', "Nem sikerült betölteni a megyéket: " . $e->getMessage());
        }
    }

    public function store(CityRequest $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/zip-codes', [
                    'county_id' => $request->get('county_id'),
                    'place_name' => $request->get('name'),
                    'zip_code' => $request->get('postal_code'),
                ]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a várost.';
                return redirect()
                    ->route('cities.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()
                ->route('cities.index')
                ->with('success', $request->get('name') . " város sikeresen létrehozva!");

        } catch (\Exception $e) {
            return redirect()
                ->route('cities.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }

        try {
            $cityResponse = Http::api()->get("/zip-codes/$id");
            $countiesResponse = Http::api()->get('counties');

            if ($cityResponse->failed()) {
                $message = $cityResponse->json('message') ?? 'A város nem található vagy hiba történt.';
                return redirect()
                    ->route('cities.index')
                    ->with('error', "Hiba: $message");
            }

            $city = $this->getCity($cityResponse);
            $counties = [];
            if ($countiesResponse->successful()) {
                $responseBody = json_decode($countiesResponse->body(), false);
                $countiesData = $responseBody->data ?? null;
                $counties = $countiesData->counties ?? [];
            }

            if (!$city) {
                return redirect()
                    ->route('cities.index')
                    ->with('error', "A város adatai nem érhetők el.");
            }

            return view('cities.edit', ['entity' => $city, 'counties' => $counties]);

        } catch (\Exception $e) {
            return redirect()
                ->route('cities.index')
                ->with('error', "Nem sikerült betölteni a város szerkesztő nézetét: " . $e->getMessage());
        }
    }

    public function update(CityRequest $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->put("/zip-codes/$id", [
                    'county_id' => $request->get('county_id'),
                    'place_name' => $request->get('name'),
                    'zip_code' => $request->get('postal_code'),
                ]);

            if ($response->successful()) {
                return redirect()
                    ->route('cities.index')
                    ->with('success', $request->get('name') . " város sikeresen frissítve!");
            }

            $errorMessage = $response->json('message') ?? 'Ismeretlen hiba történt.';
            return redirect()
                ->route('cities.index')
                ->with('error', "Hiba történt: $errorMessage");

        } catch (\Exception $e) {
            return redirect()
                ->route('cities.index')
                ->with('error', "Nem sikerült frissíteni: " . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("/zip-codes/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a várost.';
                return redirect()
                    ->route('cities.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()
                ->route('cities.index')
                ->with('success', "Város sikeresen törölve!");

        } catch (\Exception $e) {
            return redirect()
                ->route('cities.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $countyId = $request->get('county_id');
            if (!$countyId) {
                return redirect()->route('cities.index')->with('error', 'Válassz egy megyét az exportáláshoz.');
            }

            $response = Http::api()->get("counties/$countyId/place-names");

            if ($response->failed()) {
                return redirect()->route('cities.index')->with('error', 'Nem sikerült letölteni az adatokat.');
            }

            $cities = $this->getCities($response);

            $filename = 'cities_' . now()->format('Y_m_d_H_i_s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($cities) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Város', 'Megye', 'Irányítószám'], ';');
                
                foreach ($cities as $city) {
                    fputcsv($file, [
                        $city->id, 
                        $city->place_name ?? $city->name, 
                        $city->county->name ?? '', 
                        $city->zip_code ?? $city->postal_code
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->route('cities.index')->with('error', 'Hiba az exportálás során: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $countyId = $request->get('county_id');
            if (!$countyId) {
                return redirect()->route('cities.index')->with('error', 'Válassz egy megyét az exportáláshoz.');
            }

            $response = Http::api()->get("counties/$countyId/place-names");

            if ($response->failed()) {
                return redirect()->route('cities.index')->with('error', 'Nem sikerült letölteni az adatokat.');
            }

            $cities = $this->getCities($response);

            $pdf = Pdf::loadView('cities.pdf', ['entities' => $cities])
                ->setPaper('a4')
                ->setOption('margin-top', 20)
                ->setOption('margin-bottom', 20);

            return $pdf->download('cities_' . now()->format('Y_m_d_H_i_s') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('cities.index')->with('error', 'Hiba az exportálás során: ' . $e->getMessage());
        }
    }

    // Helper methods
    private function getCities($response)
    {
        $responseBody = json_decode($response->body(), false);
        $data = $responseBody->data ?? null;
        $results = $data->zip_codes ?? $data ?? [];

        return $results;
    }

    private function getCity($response)
    {
        $responseBody = json_decode($response->body(), false);
        $data = $responseBody->data ?? null;
        $result = $data->zip_code ?? $data ?? [];

        return $result;
    }
}
