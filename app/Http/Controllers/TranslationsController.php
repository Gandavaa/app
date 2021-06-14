<?php

namespace App\Http\Controllers;

use App\TestAPI;
use App\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TranslationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $translations = Translation::get();
        if ($request->ajax()) {
            // $data = Translation::all();
            $data = Translation::join('tests', 'translations.test_id', '=', 'tests.id')
                ->get(['translations.*', 'tests.label']);

            return DataTables::of($data)
                ->addIndexColumn()->editColumn('status', function ($data) {
                return ($data->MN !== null) ? '<span class="badge badge-success">Орчуулсан</span>' : '<span class="badge badge-warning">Орчуулаагүй</span>';
            })->addColumn('action', function ($row) {
                $btn = '
                    <ul class="list-group list-group-horizontal list-unstyled"><li class="pr-1">
                    <a href="' . route("translations.show", $row->id) . '"
                        data-toggle="tooltip"
                        data-id="' . $row->id . '" class="btn btn-secondary view btn-md">
                        <i class="cil-magnifying-glass"></i>
                        </a>
                    </li>
                    <li class="pr-1">
                        <a href="' . route("translations.edit", $row->id) . '"
                            data-toggle="tooltip"
                            data-id="' . $row->id . '"
                            data-original-title="Edit"
                            class="btn btn-primary btn-md" title="Засах">
                        <i class="cil-pencil"></i></a>
                    </li>
                    <li class="pr-1">
                        <form class="form-inline" action="' . route('translations.destroy', $row->id) . '" method="POST">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <button type="submit" class="btn btn-danger" title="Устгах" onclick="return confirm(\'Та энэ бичлэгийг үнэхээр устгах уу?\')"><i class="cil-trash"></i></button>
                        </form>
                    </li>
                </ul><input type="checkbox" id="' . $row->id . '"';

                return $btn;
            })
            // ->addColumn('name', function($row) {
            //     $options = '';
            //     $myArray = explode(',', $row->name);
            //     foreach ($myArray as $name) {
            //         $options .= $name;
            //     }
            //     $return = $options;
            //     return $return;
            // })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('layouts.translation.index', compact("translations"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validateInputs();
        // dd($data);
        $data = Translation::create($data);
        return redirect()->route('translations.index')->with('success', 'Асуултыг амжилттай бүртгэлээ!');
    }

    function new (Request $request) {

        $assessments = TestAPI::all(['id', 'label']);
        return view('layouts.translation.new', compact('assessments'));
    }

    public function saveTranslations(Request $request)
    {
        // dd($request->input());
        $en = $request->en;
        $mn = $request->mn;
        $test_id = $request->test_id;
        $input = $request->all();
        $condition = $input["en"];
        $id = $input["id"];
        $count = count($condition);
        // dd($count);
        for ($i = 0; $i < $count; $i++) {
            Translation::where('id', $id[$i])
                ->update([
                    // 'en' => $en[$i],
                    'mn' => $mn[$i],
                    'test_id' => $test_id,
                ]);

        }

        return redirect()->route('translations.add')->with('success', 'Монгол орчуулгыг амжилттай бүртгэлээ!');
    }

    public function add(Request $request)
    {

        $test_id = $request->test_id;
        // dd($test_id);
        $this->getJSON($test_id);
        // dd("inserted already");
        $data = Translation::where('MN', null)->limit(10)->get();
        return view('layouts.translation.addTranslations', compact('data'));
    }

    public function getJSON($test_id)
    {
        $isExist = Translation::where('test_id', '=', $test_id)->first();
        if ($isExist === null && $test_id !== null) {

            $data = array();
            $texts = array();
            $contents = Storage::get("assets/assessments/4329501.xml");
            // $decrypted= Crypt::decryptString($contents);
            $xml = xml_decode($contents);
            // get test factor results done
            // $data['general'] = [
            //     'test_id' => $xml["elements"]["test_tests"]["test_test"]["@attributes"]["id"],
            //     'score' => $xml["elements"]["test_tests"]["test_test"]["@attributes"]["score_calcule"],
            //     'label' => $xml["elements"]["test_tests"]["test_test"]["contenus"]["contenu"]["libelle"],
            //     'participant_name' => $xml["noyau_utilisateur_info"]["nom"] . " " . $xml["noyau_utilisateur_info"]["prenom"],
            // ];

            foreach ($xml['elements']['test_facteurs']['test_facteur'] as $value) {
                // $data['test_factors'][] =
                //     [
                //     'id' => $value["@attributes"]["id"],
                //     'score' => $value["@attributes"]["score_calcule"],
                //     'label' => $value["contenus"]["contenu"]["libelle"],
                // ];
                array_push($texts, $value["contenus"]["contenu"]["libelle"]);
            }

            foreach ($xml['elements']['test_groupe_facteurs']['test_groupe_facteur'] as $value) {
                // $data["test_group_factors"][] =
                //     [
                //     'id' => $value["@attributes"]["id"],
                //     'score' => $value["@attributes"]["score_calcule"],
                //     'label' => $value["contenus"]["contenu"]["libelle"],
                // ];
                array_push($texts, $value["contenus"]["contenu"]["libelle"]);
            }
            foreach ($xml['elements']['test_ref_adequation_profils']['test_ref_adequation_profil'] as $value) {
                // $data2["test_group_factors"][] =
                //     [
                //     'id' => $value["@attributes"]["id"],
                //     'label' => $value["contenus"]["contenu"]["libelle"],
                //     'description' => isset($value["contenus"]["contenu"]["description"]) ? $value["contenus"]["contenu"]["description"] : null,
                //     'description_long' => isset($value["contenus"]["contenu"]["libelle"]) ? $value["contenus"]["contenu"]["description_longue"] : null,
                // ];
                array_push($texts, isset($value["contenus"]["contenu"]["libelle"]) ? $value["contenus"]["contenu"]["libelle"] : null);
                array_push($texts, isset($value["contenus"]["contenu"]["description"]) ? $value["contenus"]["contenu"]["description"] : null);
                array_push($texts, isset($value["contenus"]["contenu"]["description_longue"]) ? $value["contenus"]["contenu"]["description_longue"] : null);
            }

            // $data['test_mini_tests'] = [
            //     'id' => $xml["elements"]["test_mini_tests"]["test_mini_test"]["@attributes"]["id"],
            //     'score' => $xml["elements"]["test_mini_tests"]["test_mini_test"]["@attributes"]["score_calcule"],
            // ];

            foreach ($xml['parties']['partie'] as $value) {
                // $data2[] =
                //     [
                //     'id' => $value["@attributes"]["id"],
                //     'label' => $value["contenus"]["contenu"]["libelle"],
                //     'title' => $value["contenus"]["contenu"]["titre"],
                //     'sub_title' => isset($value["contenus"]["contenu"]["sous_titre"]) ? $value["contenus"]["contenu"]["sous_titre"] : null,
                //     'comment' => isset($value["domaines"]["domaine"]["cibles_secondaires"]) ? $value["domaines"]["domaine"]["cibles_secondaires"] : null,
                // ];
                array_push($texts, $value["contenus"]["contenu"]["titre"], $value["contenus"]["contenu"]["libelle"],
                    isset($value["contenus"]["contenu"]["sous_titre"]) ? $value["contenus"]["contenu"]["sous_titre"] : null,
                    isset($value["domaines"]["domaine"]["contenus"]["contenu"]["libelle"]) ? $value["domaines"]["domaine"]["contenus"]["contenu"]["libelle"] : null);

                if (isset($value["domaines"])) {
                    foreach ($value["domaines"]["domaine"]["cibles_secondaires"]["cibles_secondaire"] as $value2) {
                        array_push($texts, isset($value2["contenus"]["contenu"]["commentaire_perso"]) ? $value2["contenus"]["contenu"]["commentaire_perso"] : null);
                    }
                }

            }

            array_map('strip_tags', $texts);
            $newArray = array_map(function ($v) {
                return trim(strip_tags($v));
            }, $texts);
            $xml = preg_replace("/\r|\n/", "", $newArray);
            $xml = array_filter($xml);
            $xml = array_unique($xml);

            foreach ($xml as $row) {
                $translation = new Translation();
                $translation->test_id = $test_id;
                $translation->EN = $row;
                $translation->save();
            };

        } else {
            // dd("existed");
            return "1";
        }
    }

    public function create()
    {
        $assessments = TestAPI::all(['id', 'label']);
        return view('layouts.translation.create', compact('assessments'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Translation $translation)
    {
        return view('layouts.translation.show', ['translation' => $translation]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Translation $translation)
    {
        return view('layouts.translation.edit', ['translation' => $translation]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Translation $translation)
    {
        $translation->update($this->validateInputs());
        return redirect()->route('translations.index')->with('success', 'Орчуулгыг амжилттай шинэчлэлээ!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $translation = Translation::findOrFail($id);
        $translation->delete();
        return back()->with('success', "Aмжилттай устгалаа!");
    }

    public function validateInputs()
    {
        return request()->validate([
            'test_id' => ['required'],
            'en' => ['required', ['string']],
            'mn' => ['required', ['string']],
        ]);
    }
}
