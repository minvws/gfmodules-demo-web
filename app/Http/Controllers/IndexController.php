<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DemoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

const DATA_DOMAINS = [
    'laboratory' => 'Laboratorium Uitslagen',
    'imaging' => 'Beeldvormende Diagnostiek',
    'medication' => 'Medicatiegegevens',
    'allergy' => 'Allergiegegevens',
    'diagnosis' => 'Diagnosegegevens',
    'treatment' => 'Behandelgegevens',
];

class IndexController extends Controller
{
    public const REGISTERED_TEST_BSN = '999990007';

    public function index(DemoService $demoService): View
    {
        $demoService->createNviListEntry(self::REGISTERED_TEST_BSN);

        return view('index', [
            'datadomains' => DATA_DOMAINS,
            'organisation_types' => [
                'ziekenhuis' => 'Ziekenhuis',
                'kliniek' => 'Kliniek',
                'praktijk' => 'Praktijk',
            ],
        ]);
    }

    public function locate(): View
    {
        // Go directly to the NVI locate page
        return view('locate', []);
    }

    public function step1(Request $request): View
    {
        // Validate the input
        $validated = $request->validate([
            'bsn' => 'required|string|max:9',
            'datadomain' => 'required|string',
            'organisation_type' => 'nullable|string',
        ]);

        session([
            'patient' => [
                'hashed_bsn' => $validated['bsn'],
                'datadomain' => $validated['datadomain'],
                'organisation_type' => $validated['organisation_type'] ?? null,
            ],
        ]);

        $patient = session('patient');

        return view('step_1', [
            'hashed_bsn' => $patient['hashed_bsn'],
            'data_domain' => DATA_DOMAINS[$patient['datadomain']],
            'organisation_type' => $patient['organisation_type'],
            'prs_input' => strtoupper(substr($patient['hashed_bsn'], 0, 10) . '...'),
            'scope' => 'NVI',
            'organisatie' => 'VWS',
        ]);
    }

    public function step2(DemoService $demoService): View
    {
        $patient = session('patient');

        $prs_input = $demoService->createPrsInput($patient['hashed_bsn']);
        $eval_output = $demoService->prsEvaluate($prs_input['blinded_input']);

        session([
            'eval_output' => $eval_output,
            'blind_factor' => $prs_input['blind_factor'],
        ]);

        return view('step_2', [
            'hashed_bsn' => $patient['hashed_bsn'],
            'data_domain' => $patient['datadomain'],
            'organisation_type' => $patient['organisation_type'],
            'scope' => 'NVI',
            'organisatie' => 'VWS',
            'eval_data' => substr(implode("\n", str_split($eval_output['jwe'], 20)), 0, 40),
        ]);
    }

    public function step3(): View
    {
        return view('step_3', []);
    }

    public function step4(DemoService $demoService): View
    {
        $data = $demoService->retrieveFromNVI(
            (string) session('eval_output')['jwe'],
            (string) session('blind_factor')
        );

        return view('step_4', [
            'organizations' => $data['entry'],
        ]);
    }
}
