{{--
    INSTRUCCIONES PARA CAMBIAR EL LOGO:
    1. Guarda tu imagen en: public/images/logo.png (o .jpg, .svg)
    2. Actualiza la ruta en src="{{ asset('images/logo.png') }}"
    3. Ajusta width y height según tus necesidades
--}}

@php
    // Puedes cambiar la ruta del logo aquí
    $logoPath = 'images/logo.png';
    // O usar configuración desde .env agregando: LOGO_PATH=images/tu-logo.png
    // $logoPath = config('app.logo_path', 'images/logo.png');
@endphp

<img
    src="{{ asset($logoPath) }}"
    alt="Logo Sistema Horarios FICCT"
    {{ $attributes->merge(['class' => 'object-contain']) }}
    onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%2250%22 font-size=%2220%22>FICCT</text></svg>';"
/>
