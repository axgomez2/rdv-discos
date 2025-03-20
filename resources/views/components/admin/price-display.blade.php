@props(['selectedRelease'])

@php
    $exchangeRate = 5.8;
    $lowestPriceUSD = $selectedRelease['lowest_price'] ?? 0;
    $medianPriceUSD = $selectedRelease['median_price'] ?? 0;
    $highestPriceUSD = $selectedRelease['highest_price'] ?? 0;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    @if($lowestPriceUSD > 0)
        <div>
            <h5 class="text-sm font-medium text-gray-500">Preço mais baixo</h5>
            <p class="text-base text-gray-900">
                US$ {{ number_format($lowestPriceUSD, 2, ',', '.') }}
                <span class="text-xs text-gray-500">(R$ {{ number_format($lowestPriceUSD * $exchangeRate, 2, ',', '.') }})</span>
            </p>
        </div>
        <div>
            <h5 class="text-sm font-medium text-gray-500">Preço médio</h5>
            <p class="text-base text-gray-900">
                US$ {{ number_format($medianPriceUSD, 2, ',', '.') }}
                <span class="text-xs text-gray-500">(R$ {{ number_format($medianPriceUSD * $exchangeRate, 2, ',', '.') }})</span>
            </p>
        </div>
        <div>
            <h5 class="text-sm font-medium text-gray-500">Preço mais alto</h5>
            <p class="text-base text-gray-900">
                US$ {{ number_format($highestPriceUSD, 2, ',', '.') }}
                <span class="text-xs text-gray-500">(R$ {{ number_format($highestPriceUSD * $exchangeRate, 2, ',', '.') }})</span>
            </p>
        </div>
    @endif
</div>
