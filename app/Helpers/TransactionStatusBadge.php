<?php

namespace App\Helpers;

/**
 * Retorna as classes Tailwind para o badge de status de transação (audit log).
 * Centralizado aqui para evitar avisos de cssConflict no tailwindcss-intellisense,
 * já que apenas uma variante é aplicada por vez.
 */
class TransactionStatusBadge
{
    public static function classes(string $status): string
    {
        return match ($status) {
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'cancelled', 'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
