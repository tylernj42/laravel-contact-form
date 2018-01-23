@component('mail::layout')

@slot('header')
@component('mail::header', ['url' => config('app.url')])
Contact Form Message From {{ $form_data->name }}
@endcomponent
@endslot

**Name:** {{ $form_data->name }}

**Email:** {{ $form_data->email }}

@if($form_data->phone != '')
**Phone:** {{ $form_data->phone }}
@endif

**Message:**

{{ $form_data->message }}

@slot('footer')
@component('mail::footer')
Copyright &copy; {{ date('Y') }} Dealer Inspire.  All rights reserved.
@endcomponent
@endslot

@endcomponent