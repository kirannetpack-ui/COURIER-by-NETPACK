@extends('layouts.app')

@section('title', 'Edit Domestic Rate')
@section('page-title', 'Edit Domestic Rate')

@section('content')
<div class="mx-auto max-w-5xl">
    <form method="POST" action="{{ route('admin.domestic.rates.update', $rate->id) }}" class="rounded-xl bg-white shadow-sm">
        @csrf
        @method('PUT')
        <div class="border-b px-6 py-5">
            <h1 class="text-xl font-semibold text-gray-900">Edit domestic zone rate</h1>
            <p class="mt-1 text-sm text-gray-500">A conflicting weight or effective-date band is rejected before it can affect customer quotes.</p>
        </div>
        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
            @if($errors->any())
                <div class="md:col-span-2 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    <p class="font-semibold">Please correct the highlighted details.</p>
                    <ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div><label class="text-sm font-medium">Partner *</label><select name="partner_id" required class="mt-1 w-full rounded-lg border-gray-300"><option value="">Select partner</option>@foreach($partners as $partner)<option value="{{ $partner->id }}" @selected(old('partner_id', $rate->partner_id) == $partner->id)>{{ $partner->name }}</option>@endforeach</select></div>
            <div><label class="text-sm font-medium">Service *</label><select name="service_type" required class="mt-1 w-full rounded-lg border-gray-300">@foreach($serviceTypes as $type => $service)<option value="{{ $type }}" @selected(old('service_type', $rate->service_type) === $type)>{{ $service['name'] }}</option>@endforeach</select></div>
            <div><label class="text-sm font-medium">Origin zone *</label><select name="origin_zone_id" required class="mt-1 w-full rounded-lg border-gray-300"><option value="">Select zone</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected(old('origin_zone_id', $rate->origin_zone_id) == $zone->id)>{{ $zone->zone_name }} ({{ $zone->zone_code }})</option>@endforeach</select></div>
            <div><label class="text-sm font-medium">Destination zone *</label><select name="destination_zone_id" required class="mt-1 w-full rounded-lg border-gray-300"><option value="">Select zone</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected(old('destination_zone_id', $rate->destination_zone_id) == $zone->id)>{{ $zone->zone_name }} ({{ $zone->zone_code }})</option>@endforeach</select></div>
            <div><label class="text-sm font-medium">Weight from (kg) *</label><input type="number" step="0.01" min="0" name="weight_from" value="{{ old('weight_from', $rate->weight_from) }}" required class="mt-1 w-full rounded-lg border-gray-300"></div>
            <div><label class="text-sm font-medium">Weight to (kg) *</label><input type="number" step="0.01" min="0" name="weight_to" value="{{ old('weight_to', $rate->weight_to) }}" required class="mt-1 w-full rounded-lg border-gray-300"></div>
            <div><label class="text-sm font-medium">Base rate *</label><input type="number" step="0.01" min="0" name="base_rate" value="{{ old('base_rate', $rate->base_rate) }}" required class="mt-1 w-full rounded-lg border-gray-300"></div>
            <div><label class="text-sm font-medium">Per kg rate *</label><input type="number" step="0.01" min="0" name="per_kg_rate" value="{{ old('per_kg_rate', $rate->per_kg_rate) }}" required class="mt-1 w-full rounded-lg border-gray-300"></div>
            <div><label class="text-sm font-medium">Minimum rate</label><input type="number" step="0.01" min="0" name="minimum_rate" value="{{ old('minimum_rate', $rate->minimum_rate) }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
            <div><label class="text-sm font-medium">Currency *</label><input name="currency" maxlength="3" value="{{ old('currency', $rate->currency ?: 'NPR') }}" required class="mt-1 w-full rounded-lg border-gray-300 uppercase"></div>
            <div><label class="text-sm font-medium">Effective from *</label><input type="date" name="effective_from" value="{{ old('effective_from', optional($rate->effective_from)->format('Y-m-d')) }}" required class="mt-1 w-full rounded-lg border-gray-300"></div>
            <div><label class="text-sm font-medium">Effective to</label><input type="date" name="effective_to" value="{{ old('effective_to', optional($rate->effective_to)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-gray-300"></div>
            <div class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" id="active" @checked(old('is_active', $rate->is_active))><label for="active" class="text-sm font-medium">Active and available for quoting</label></div>
        </div>
        <div class="flex flex-wrap justify-end gap-3 border-t px-6 py-4"><a href="{{ route('admin.domestic.rates') }}" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700">Cancel</a><button class="rounded-lg bg-teal-600 px-5 py-2 text-sm font-semibold text-white hover:bg-teal-700">Save rate</button></div>
    </form>
</div>
@endsection
