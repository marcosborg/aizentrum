<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormData;
use App\Http\Controllers\Traits\Iftech;
use Illuminate\Support\Facades\Notification;
use \App\Models\FormField;

class FormsController extends Controller
{

    use Iftech;

    public function index($form_id)
    {
        $form = Form::where('id', $form_id)->with('form_fields')->first();

        return view('website.form', compact('form'));
    }

    public function formSend(Request $request)
    {

        $all = $request->all();

        $fields = [];
        $form_id = null;

        foreach ($all as $key => $value) {
            if ($key == 'form_id') {
                $form_id = $value;
            }
        }

        foreach ($all as $key => $value) {
            if ($key != 'form_id') {
                $form_field = FormField::where([
                    'form_id' => $form_id,
                    'name' => $key,
                ])->first();
                if ($request->hasFile($key)) {
                    if ($request->file($key)->isValid()) {
                        $path = $request->file($key)->store('uploads', 'public');
                        $fields[] = [
                            'name' => $key,
                            'value' => url('/') . '/storage/' . $path,
                            'label' => $form_field->label,
                            'type' => $form_field->type,
                            'required' => $form_field->required
                        ];
                    }
                } else {
                    $fields[] = [
                        'name' => $key,
                        'value' => $value,
                        'label' => $form_field->label,
                        'type' => $form_field->type,
                        'required' => $form_field->required
                    ];
                }
            }
        }

        $form_data = new FormData;
        $form_data->form_id = $form_id;
        $form_data->data = json_encode($fields);
        $form_data->save();

        $data = json_encode($form_data);

        $form_data->load('form.project');
        $form_data->data = json_decode($form_data->data);

        Notification::route('mail', env('COMERCIAL_EMAIL'))
            ->notify(new \App\Notifications\FormSubmit($form_data));

        //SEND BY API

        $access_token = $this->login();

        $send_form = $this->sendForm($access_token, $data);

        return $send_form;

    }

    public function all($project_id)
    {
        $forms = Form::where('project_id', $project_id)->get()->load('project');

        return view('website.all', compact('forms'));
    }
}
