<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

abstract class BaseCommand extends Command
{
    protected function resolveOption(string $option, string $question, bool $secret = false): string
    {
        $value = $this->option($option);

        if ($value) {
            return $value;
        }

        return $secret ? $this->secret($question) : $this->ask($question);
    }

    protected function validateOrFail(array $data, array $rules): bool
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return false;
        }

        return true;
    }
}
