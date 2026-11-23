<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function catalogTheme(): string
    {
        $kayitliTema = static::query()->where('key', 'catalog_theme')->value('value');
        $temalar = config('themes.options', []);
        $adaylar = [
            $kayitliTema,
            config('themes.default', 'katalog'),
            'katalog',
        ];

        foreach ($adaylar as $tema) {
            if (! is_string($tema) || ! array_key_exists($tema, $temalar)) {
                continue;
            }

            if (View::exists($tema.'.index')) {
                return $tema;
            }
        }

        return 'katalog';
    }


}
