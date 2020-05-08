<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $settings = \Utilities::getSettings();

        return view('settings.show', compact('settings'));
    }

    public function edit()
    {
        $settings = \Utilities::getSettings();

        return view('settings.edit', compact('settings'));
    }

    public function save(Request $request)
    {
        // dd($request->all());
        // Get All Inputs Except '_Token' to loop through and save
        $settings = $request->except('_token');

        // Update All Settings
        foreach ($settings as $key => $value) {
            if ($key == 'gym_logo') {
                \Utilities::uploadFile($request, '', $key, 'gym_logo', \constPaths::GymLogo); // Upload File
                $value = $key.'.jpg'; // Image Name For DB
            }

            if($value == NULL) $value = '';
            Setting::where('key', '=', $key)->update(['value' => $value]);
        }

        flash()->success('Setting was successfully updated');

        return redirect('settings/edit');
    }
}
