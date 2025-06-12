@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Bond And Partners')
<img src="/images/logo.png" class="logo" alt="Bond And Partners">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
