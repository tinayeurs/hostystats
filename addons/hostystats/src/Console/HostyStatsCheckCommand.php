<?php

namespace App\Addons\HostyStats\Console;

use App\Addons\HostyStats\Models\Monitor;
use App\Addons\HostyStats\Models\Check;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class HostyStatsCheckCommand extends Command
{
    
    protected $signature = 'hostystats:check {--limit=200} {--force}';
    protected $description = 'Run checks for HostyStats monitors';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');
        $now = Carbon::now();

        $monitors = Monitor::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        foreach ($monitors as $m) {

            
            if (!$force) {
                $interval = max(10, (int)($m->interval_sec ?? 60));
                if ($m->last_checked_at) {
                    
                    $nextDue = $m->last_checked_at->copy()->addSeconds($interval);
                    if ($now->lt($nextDue)) {
                        continue;
                    }
                }
            }

            
            $lockTtlSeconds = 120;
            $locked = Monitor::query()
                ->whereKey($m->id)
                ->where(function ($q) use ($lockTtlSeconds) {
                    $q->whereNull('checking_at')
                      ->orWhere('checking_at', '<', now()->subSeconds($lockTtlSeconds));
                })
                ->update(['checking_at' => $now]);

            if ($locked !== 1) {
                
                continue;
            }

            
            $m->refresh();

            try {
                
                [$status, $ms, $httpCode, $error] = $this->runSingle($m);

                
                Check::create([
                    'monitor_id' => $m->id,
                    'status' => $status,
                    'response_time_ms' => $ms,
                    'http_code' => $httpCode,
                    'error' => $error,
                    'checked_at' => $now,
                ]);

                
                $m->update([
                    'last_status' => $status,
                    'last_response_time_ms' => $ms,
                    'last_http_code' => $httpCode,
                    'last_error' => $error,
                    'last_checked_at' => $now,
                    'checking_at' => null,
                ]);

                $this->line("#{$m->id} {$m->name} => {$status}" . ($ms ? " ({$ms}ms)" : ""));
            } catch (\Throwable $e) {
                
                $m->update([
                    'last_status' => 'down',
                    'last_error' => $e->getMessage(),
                    'last_checked_at' => $now,
                    'checking_at' => null,
                ]);

                $this->error("#{$m->id} {$m->name} => error: {$e->getMessage()}");
            } finally {
                
                try {
                    Monitor::whereKey($m->id)->update(['checking_at' => null]);
                } catch (\Throwable $e) {
                }
            }
        }

        return self::SUCCESS;
    }

    private function runSingle(Monitor $m): array
    {
        
        if (!empty($m->forced_status)) {
            return [$m->forced_status, null, null, null];
        }

        return match ($m->type) {
            'http' => $this->checkHttp($m),
            'tcp'  => $this->checkTcp($m),
            'ping' => $this->checkPing($m),
            default => ['down', null, null, "Unknown type {$m->type}"],
        };
    }

    private function checkHttp(Monitor $m): array
    {
        $timeoutSeconds = max(1, (int) ceil(($m->timeout_ms ?? 3000) / 1000));
        $start = microtime(true);

        try {
            $resp = Http::timeout($timeoutSeconds)
                ->withoutVerifying()
                ->get($m->target);

            $ms = (int) round((microtime(true) - $start) * 1000);
            $code = $resp->status();

            $expected = $m->expected_http_code;
            $ok = $expected ? ($code === (int) $expected) : ($code >= 200 && $code < 400);

            if (!$ok) {
                return ['down', $ms, $code, "HTTP unexpected code: {$code}"];
            }

            if ($ms > (int) ($m->degraded_threshold_ms ?? 800)) {
                return ['degraded', $ms, $code, null];
            }

            return ['ok', $ms, $code, null];
        } catch (\Throwable $e) {
            $ms = (int) round((microtime(true) - $start) * 1000);
            return ['down', $ms ?: null, null, $e->getMessage()];
        }
    }

    private function checkTcp(Monitor $m): array
    {
        if (!str_contains($m->target, ':')) {
            return ['down', null, null, 'TCP target must be host:port'];
        }

        [$host, $port] = explode(':', $m->target, 2);
        $port = (int) $port;

        $timeoutSeconds = max(1, (int) ceil(($m->timeout_ms ?? 3000) / 1000));
        $start = microtime(true);

        try {
            $errno = 0; $errstr = '';
            $fp = @fsockopen($host, $port, $errno, $errstr, $timeoutSeconds);
            $ms = (int) round((microtime(true) - $start) * 1000);

            if (!$fp) {
                return ['down', $ms ?: null, null, $errstr ?: "TCP connect failed ({$errno})"];
            }

            fclose($fp);

            if ($ms > (int) ($m->degraded_threshold_ms ?? 800)) {
                return ['degraded', $ms, null, null];
            }

            return ['ok', $ms, null, null];
        } catch (\Throwable $e) {
            $ms = (int) round((microtime(true) - $start) * 1000);
            return ['down', $ms ?: null, null, $e->getMessage()];
        }
    }

    private function checkPing(Monitor $m): array
    {
        $timeoutSeconds = max(1, (int) ceil(($m->timeout_ms ?? 3000) / 1000));
        $start = microtime(true);

        try {
            $process = new Process(['ping', '-c', '1', '-W', (string)$timeoutSeconds, $m->target]);
            $process->setTimeout($timeoutSeconds + 1);
            $process->run();

            $ms = (int) round((microtime(true) - $start) * 1000);

            if (!$process->isSuccessful()) {
                $err = trim($process->getErrorOutput() ?: $process->getOutput());
                return ['down', $ms ?: null, null, $err ?: 'Ping failed'];
            }

            if ($ms > (int) ($m->degraded_threshold_ms ?? 800)) {
                return ['degraded', $ms, null, null];
            }

            return ['ok', $ms, null, null];
        } catch (\Throwable $e) {
            $ms = (int) round((microtime(true) - $start) * 1000);
            return ['down', $ms ?: null, null, $e->getMessage()];
        }
    }
}
