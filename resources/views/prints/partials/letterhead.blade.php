@php
    $logoDataUri = null;

    if (! empty($letterhead?->logo)) {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        if ($disk->exists($letterhead->logo)) {
            $logoAbsolutePath = $disk->path($letterhead->logo);
            $mime = mime_content_type($logoAbsolutePath) ?: 'image/png';
            $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoAbsolutePath));
        }
    }
@endphp

<table class="letterhead-table">
    <tr>
        <td class="logo-cell">
            @if ($logoDataUri)
                <img src="{{ $logoDataUri }}" alt="Logo {{ $letterhead->organization_name }}" class="logo-image">
            @endif
        </td>
        <td class="identity-cell">
            <div class="organization-name">{{ strtoupper($letterhead->organization_name ?? config('app.name')) }}</div>
            @if (! empty($letterhead->organization_tagline))
                <div class="organization-tagline">{{ $letterhead->organization_tagline }}</div>
            @endif
            <div class="contact-line">
                @if ($letterhead->address)
                    <span>{{ $letterhead->address }}</span>
                @endif
            </div>
            <div class="contact-line">
                @if ($letterhead->phone)
                    <span>Telp: {{ $letterhead->phone }}</span>
                @endif
                @if ($letterhead->email)
                    <span>&nbsp;•&nbsp;Email: {{ $letterhead->email }}</span>
                @endif
                @if ($letterhead->website)
                    <span>&nbsp;•&nbsp;Web: {{ $letterhead->website }}</span>
                @endif
            </div>
        </td>
    </tr>
</table>
<hr class="letterhead-divider">
