<?php

namespace App\Livewire;

use Livewire\Component;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class LmStudioToggle extends Component
{
    public bool $isRunning = false;

    public function mount()
    {
        $this->checkStatus();
    }

    public function checkStatus()
    {
        $process = new Process(['lms', 'status']);
        $process->run();
        $output = strtolower($process->getOutput());

        $this->isRunning = str_contains($output, 'running');
    }

    public function toggleServer()
    {
        $command = $this->isRunning ? 'stop' : 'start';
        $path = 'C:\\Users\\M Abdul Aziz\\AppData\\Roaming\\npm\\lms.cmd'; // ganti sesuai hasil 'where lms'

        $process = new Process([$path, $command]);
        $process->run();

        Log::info('LMStudio Toggle Command', [
            'command' => $command,
            'output' => $process->getOutput(),
            'error' => $process->getErrorOutput(),
            'success' => $process->isSuccessful(),
        ]);

        if ($process->isSuccessful()) {
            $this->isRunning = !$this->isRunning;
        } else {
            session()->flash('error', $process->getErrorOutput());
        }
    }

    public function render()
    {
        return view('livewire.lm-studio-toggle');
    }
}
