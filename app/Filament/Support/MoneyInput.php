<?php

namespace App\Filament\Support;

use Filament\Forms\Components\TextInput;

class MoneyInput
{
    /**
     * Buat TextInput dengan format ribuan (titik) tanpa desimal.
     * Value yang dikirim ke server adalah integer murni.
     */
    public static function make(string $name): TextInput
    {
        return TextInput::make($name)
            ->prefix('Rp')
            ->inputMode('numeric')
            ->rules([
                function () {
                    return function (string $attribute, $value, \Closure $fail) {
                        $val = str_replace('.', '', (string) $value);
                        if (!ctype_digit($val) && $val !== '') {
                            $fail('Kolom ini harus berupa angka.');
                        } elseif ((int)$val < 0) {
                            $fail('Kolom ini tidak boleh kurang dari 0.');
                        }
                    };
                },
            ])
            ->extraInputAttributes([
                'inputmode' => 'numeric',
                'oninput'   => "var v=this.value.replace(/[^0-9]/g,''); this.value=v===''?'':v.replace(/\\B(?=(\\d{3})+(?!\\d))/g,'.');",
                'onblur'    => "var v=this.value.replace(/[^0-9]/g,''); this.value=v===''?'':v.replace(/\\B(?=(\\d{3})+(?!\\d))/g,'.');",
                'onfocus'   => "this.value=this.value.replace(/\\./g,'');",
            ])
            ->dehydrateStateUsing(fn ($state) => (int) str_replace('.', '', $state ?? '0'));
    }
}
