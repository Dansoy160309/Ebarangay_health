<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardCard extends Component
{
    public $title;
    public $count;
    public $link;
    public $linkText;

    /**
     * Create a new component instance.
     */
    public function __construct($title, $count = '', $link = '', $linkText = '')
    {
        $this->title = $title;
        $this->count = $count;
        $this->link = $link;
        $this->linkText = $linkText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.dashboard-card');
    }
}
