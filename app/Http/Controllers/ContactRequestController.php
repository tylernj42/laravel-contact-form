<?php

namespace App\Http\Controllers;

use App\ContactRequest;
use Illuminate\Http\Request;
use App\Mail\ContactRequestEmail;

class ContactRequestController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);

        \Mail::to('guy-smiley@example.com')->send(new ContactRequestEmail($request));

        $form_data = request(['name', 'email', 'phone', 'message']);
        if($form_data['phone'] === null){
            $form_data['phone'] = '';
        }
        ContactRequest::create($form_data);

        return redirect()->home();
    }
}
