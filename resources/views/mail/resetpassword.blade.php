@component('mail::message')
# Reset Password

@component('mail::button', ['url' => config('app.url').'/reset/password?access_token='. $data->access_token])
Redirect Link
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
