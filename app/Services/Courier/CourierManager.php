<?php

namespace App\Services\Courier;

use App\Models\CourierService;
use App\Services\Courier\Contracts\CourierInterface;
use InvalidArgumentException;

class CourierManager
{
    /** Map of courier code → implementation class. */
    private static array $drivers = [
        'internal' => InternalCourier::class,
        'leopards' => LeopardsCourier::class,
        'tcs'      => TcsCourier::class,
        'mnp'      => MnpCourier::class,
    ];

    /** Resolve a CourierInterface for the given CourierService model. */
    public static function make(CourierService $service): CourierInterface
    {
        $class = self::$drivers[$service->code] ?? null;

        if (!$class) {
            throw new InvalidArgumentException("No courier driver registered for code [{$service->code}].");
        }

        return new $class($service);
    }

    /** Resolve by courier code string (looks up DB record). */
    public static function forCode(string $code): CourierInterface
    {
        $service = CourierService::where('code', $code)->where('is_active', true)->firstOrFail();
        return self::make($service);
    }

    /** All active couriers as select options [code => name]. */
    public static function activeOptions(): array
    {
        return CourierService::where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    /** Register a custom courier driver at runtime. */
    public static function extend(string $code, string $class): void
    {
        self::$drivers[$code] = $class;
    }
}
