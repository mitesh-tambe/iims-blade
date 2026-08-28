<?php

namespace App\View\Components\Charts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BarChart extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $id,
        public string $title = 'Overview',
        public ?string $description = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.charts.bar-chart');
    }
}
