@extends('layouts.default')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-semibold mb-4">Bestelling plaatsen</h1>
        <p class="text-gray-500 mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quasi fuga officia ducimus
            eum, animi iusto sequi vel nulla quam debitis perferendis dolorum esse voluptas et rerum officiis. Velit, earum
            maiores.</p>

        <p class="text-lg font-semibold">Afleveradres</p>
        <form action="{{ route('orders.store') }}" method="post" class="flex flex-col gap-4 pt-4">
            @csrf
            <div class="flex flex-col">
                <label class="text-gray-500" for="voornaam">Voornaam: *</label>
                <input name="voornaam" value="{{ old('voornaam') }}" type="text"
                    class="bg-white border border-gray-500 px-4 py-2">
                @error('firstname')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex flex-col">
                <label class="text-gray-500" for="achternaam">Achternaam: *</label>
                <input name="naam" value="{{ old('naam') }}" type="text"
                    class="bg-white border border-gray-500 px-4 py-2">
                @error('lastname')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div class="grid grid-cols-5 gap-4">
                <div class="flex flex-col col-span-4">
                    <label class="text-gray-500" for="straat">Straat: *</label>
                    <input name="straat" value="{{ old('straat') }}" type="text"
                        class="bg-white border border-gray-500 px-4 py-2">
                    @error('street')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col">
                    <label class="text-gray-500" for="huisnummer">Huisnummer: *</label>
                    <input name="huisnummer" value="{{ old('huisnummer') }}" type="text"
                        class="bg-white border border-gray-500 px-4 py-2">
                    @error('housenumber')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="grid grid-cols-5 gap-4">
                <div class="flex flex-col">
                    <label class="text-gray-500" for="postcode">Postcode: *</label>
                    <input name="postcode" value="{{ old('postcode') }}" type="text"
                        class="bg-white border border-gray-500 px-4 py-2">
                    @error('zipcode')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col col-span-4">
                    <label class="text-gray-500" for="woonplaats">Woonplaats: *</label>
                    <input name="woonplaats" value="{{ old('woonplaats') }}" type="text"
                        class="bg-white border border-gray-500 px-4 py-2">
                    @error('city')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div>
                <button type="submit"
                    class="mt-4 block hover:bg-orange-600 bg-orange-500 uppercase text-center font-semibold text-lg cursor-pointer text-white px-4 py-2 w-full">
                    Bestelling plaatsen
                </button>
            </div>
        </form>
    </div>
@endsection
