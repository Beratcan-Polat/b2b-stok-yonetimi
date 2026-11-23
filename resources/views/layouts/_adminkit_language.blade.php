<form action="{{ route('dil.degistir') }}" method="POST" class="m-0">
    @csrf
    <select name="locale" class="form-select tema-ust-kontrol" aria-label="{{ __('panel.language') }}" onchange="this.form.submit()">
        <option value="tr" lang="tr" @selected(app()->getLocale() === 'tr')>Türkçe</option>
        <option value="en" lang="en" @selected(app()->getLocale() === 'en')>English</option>
    </select>
</form>
