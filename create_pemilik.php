<?php
$u = App\Models\User::where('email', 'pemilik@nukumasoes.com')->first() ?? new App\Models\User();
$u->name = 'Pemilik Nukuma';
$u->email = 'pemilik@nukumasoes.com';
$u->password = Illuminate\Support\Facades\Hash::make('rahasia123');
$u->role = 'pemilik';
$u->save();
echo "Berhasil membuat user Pemilik!";
