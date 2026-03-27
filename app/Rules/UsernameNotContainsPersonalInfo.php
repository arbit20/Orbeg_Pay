<?php

declare(strict_types=1);

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class UsernameNotContainsPersonalInfo implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $username = mb_strtolower(trim((string) $value));

        if ($username === '') {
            return;
        }

        $this->checkName($username, $fail);
        $this->checkBirthDate($username, $fail);
    }

    private function checkName(string $username, Closure $fail): void
    {
        $name = mb_strtolower(trim($this->data['name'] ?? ''));

        if ($name === '') {
            return;
        }

        $parts = preg_split('/\s+/', $name);

        foreach ($parts as $part) {
            $normalized = $this->removeAccents($part);

            if (mb_strlen($normalized) >= 3 && str_contains($username, $normalized)) {
                $fail('El nombre de usuario no debe contener partes de tu nombre real.');

                return;
            }
        }
    }

    private function checkBirthDate(string $username, Closure $fail): void
    {
        $birthDate = $this->data['birth_date'] ?? null;

        if (! $birthDate) {
            return;
        }

        try {
            $date = Carbon::parse($birthDate);
        } catch (\Exception) {
            return;
        }

        $patterns = [
            $date->format('dmY'),
            $date->format('mdY'),
            $date->format('Ymd'),
            $date->format('dmy'),
            $date->format('mdy'),
            $date->format('ymd'),
            $date->format('dm'),
            $date->format('md'),
            $date->format('Y'),
            $date->format('d') . $date->format('m'),
        ];

        foreach (array_unique($patterns) as $pattern) {
            if (mb_strlen($pattern) >= 4 && str_contains($username, $pattern)) {
                $fail('El nombre de usuario no debe contener tu fecha de nacimiento.');

                return;
            }
        }
    }

    private function removeAccents(string $string): string
    {
        $map = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ñ' => 'n', 'ü' => 'u',
        ];

        return strtr($string, $map);
    }
}
