<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountyRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountyController extends Controller
{
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        try {
            $url = $needle ? "counties?needle=" . urlencode($needle) : "counties";

            $response = Http::api()->get($url);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Ismeretlen hiba történt.';
                return redirect()
                    ->route('counties.index')
                    ->with('error', "Hiba történt a lekérdezés során: $message");
            }

            $counties = $this->getCounties($response);

            return view('counties.index', ['entities' => $counties, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()
                ->route('counties.index')
                ->with('error', "Nem sikerült betölteni a megyéket: " . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $response = Http::api()->get("/counties/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'A megye nem található vagy hiba történt.';
                return redirect()
                    ->route('counties.index')
                    ->with('error', "Hiba: $message");
            }
            $county = $this->getCounty($response);

            if (!$county) {
                return redirect()
                    ->route('counties.index')
                    ->with('error', "A megye adatai nem érhetők el.");
            }

            return view('counties.show', ['entity' => $county]);

        } catch (\Exception $e) {
            return redirect()
                ->route('counties.index')
                ->with('error', "Nem sikerült betölteni a megye adatait: " . $e->getMessage());
        }
    }

    public function create()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }
        return view('counties.create');
    }

    public function store(CountyRequest $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }

        $name = $request->get('name');

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/counties', ['name' => $name]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a megyét.';
                return redirect()
                    ->route('counties.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()
                ->route('counties.index')
                ->with('success', "$name megye sikeresen létrehozva!");

        } catch (\Exception $e) {
            return redirect()
                ->route('counties.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }

        try {
            $response = Http::api()->get("/counties/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'A megye nem található vagy hiba történt.';
                return redirect()
                    ->route('counties.index')
                    ->with('error', "Hiba: $message");
            }

            $county = $this->getCounty($response);

            if (!$county) {
                return redirect()
                    ->route('counties.index')
                    ->with('error', "A megye adatai nem érhetők el.");
            }

            return view('counties.edit', ['entity' => $county]);

        } catch (\Exception $e) {
            return redirect()
                ->route('counties.index')
                ->with('error', "Nem sikerült betölteni a megye szerkesztő nézetét: " . $e->getMessage());
        }
    }

    public function update(CountyRequest $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Bejelentkezés szükséges.');
        }

        $name = $request->get('name');

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->put("/counties/$id", ['name' => $name]);

            if ($response->successful()) {
                return redirect()
                    ->route('counties.index')
                    ->with('success', "$name megye sikeresen frissítve!");
            }

            $errorMessage = $response->json('message') ?? 'Ismeretlen hiba történt.';
            return redirect()
                ->route('counties.index')
                ->with('error', "Hiba történt: $errorMessage");

        } catch (\Exception $e) {
            return redirect()
                ->route('counties.index')
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
                ->delete("/counties/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a megyét.';
                return redirect()
                    ->route('counties.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()
                ->route('counties.index')
                ->with('success', "Megye sikeresen törölve!");

        } catch (\Exception $e) {
            return redirect()
                ->route('counties.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $response = Http::api()->get('counties');

            if ($response->failed()) {
                return redirect()->route('counties.index')->with('error', 'Nem sikerült letölteni az adatokat.');
            }

            $counties = $this->getCounties($response);

            $filename = 'counties_' . now()->format('Y_m_d_H_i_s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($counties) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Megye'], ';');
                
                foreach ($counties as $county) {
                    fputcsv($file, [$county->id, $county->name], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->route('counties.index')->with('error', 'Hiba az exportálás során: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $response = Http::api()->get('counties');

            if ($response->failed()) {
                return redirect()->route('counties.index')->with('error', 'Nem sikerült letölteni az adatokat.');
            }

            $counties = $this->getCounties($response);

            $pdf = Pdf::loadView('counties.pdf', ['entities' => $counties])
                ->setPaper('a4')
                ->setOption('margin-top', 20)
                ->setOption('margin-bottom', 20);

            return $pdf->download('counties_' . now()->format('Y_m_d_H_i_s') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('counties.index')->with('error', 'Hiba az exportálás során: ' . $e->getMessage());
        }
    }

    // Helper methods
    private function getCounties($response)
    {
        $responseBody = json_decode($response->body(), false);
        $data = $responseBody->data ?? null;
        $results = $data ?? [];

        return $results;
    }

    private function getCounty($response)
    {
        $responseBody = json_decode($response->body(), false);
        $data = $responseBody->data ?? null;
        $result = $data ?? [];

        return $result;
    }
}
