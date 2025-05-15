<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CitySelector extends Component
{
    /**
     * The selected city.
     *
     * @var string|null
     */
    public $selectedCity;

    /**
     * The selected postal code.
     *
     * @var string|null
     */
    public $selectedPostalCode;

    /**
     * Whether the component has validation errors.
     *
     * @var bool
     */
    public $hasError;

    /**
     * Create a new component instance.
     *
     * @param  string|null  $selectedCity
     * @param  string|null  $selectedPostalCode
     * @param  bool  $hasError
     * @return void
     */
    public function __construct($selectedCity = null, $selectedPostalCode = null, $hasError = false)
    {
        $this->selectedCity = $selectedCity;
        $this->selectedPostalCode = $selectedPostalCode;
        $this->hasError = $hasError;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.city-selector');
    }
}
