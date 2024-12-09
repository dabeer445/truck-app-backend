<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Counter extends Component
{
    public $count = 1;
    
    public function mount()
    {
        Log::info('Counter mounted', ['id' => $this->getId()]);
    }
 
    public function increment()
    {
        Log::info('Increment called', [
            'id' => $this->getId(),
            'old_count' => $this->count,
            'new_count' => $this->count + 1
        ]);
        
        $this->count++;
    }

    public function updated($property)
    {
        Log::info('Property updated', ['property' => $property]);
    }

    public function render()
    {
        Log::info('Counter rendering', ['count' => $this->count]);
        return view('livewire.counter');
    }
}