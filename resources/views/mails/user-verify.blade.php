<x-mail::message>
# Introduction

Clik button to verify email addres

<x-mail::button :url="$verifyCode" color="success">
Vertify mail
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
