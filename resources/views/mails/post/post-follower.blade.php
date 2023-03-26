<x-mail::message>
# Introduction

This mail sent to yuo because you followed th post title {{ $post->title }}. Commnt by : {{ $commetBy->name }}

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
